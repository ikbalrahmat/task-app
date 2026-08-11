<?php

namespace App\Console\Commands;

use App\Models\HariLibur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncHariLibur extends Command
{
    protected $signature   = 'libur:sync {tahun? : Tahun yang akan di-sync (default: tahun berjalan)}';
    protected $description = 'Sync hari libur nasional Indonesia dari API date.nager.at';

    public function handle(): int
    {
        $tahun = (int) ($this->argument('tahun') ?? date('Y'));

        $this->info("Fetching hari libur nasional Indonesia untuk tahun {$tahun}...");

        try {
            $response = Http::timeout(15)
                ->get("https://date.nager.at/api/v3/PublicHolidays/{$tahun}/ID");

            if ($response->failed()) {
                $this->error("Gagal mengambil data dari API. Status: " . $response->status());
                return self::FAILURE;
            }

            $data    = $response->json();
            $synced  = 0;
            $skipped = 0;

            foreach ($data as $item) {
                $tanggal   = $item['date'];
                $namaLibur = $item['localName'] ?? $item['name'] ?? 'Hari Libur Nasional';

                // Upsert berdasarkan tanggal + sumber nasional
                $existing = HariLibur::where('tanggal', $tanggal)
                                     ->where('sumber', 'nasional')
                                     ->first();

                if ($existing) {
                    $existing->update([
                        'nama_libur' => $namaLibur,
                        'tahun'      => $tahun,
                    ]);
                    $skipped++;
                } else {
                    HariLibur::create([
                        'tanggal'    => $tanggal,
                        'nama_libur' => $namaLibur,
                        'tahun'      => $tahun,
                        'sumber'     => 'nasional',
                    ]);
                    $synced++;
                }
            }

            $this->info("✅ Selesai! {$synced} data baru disimpan, {$skipped} data diperbarui.");
            $this->table(
                ['Tanggal', 'Nama Libur'],
                collect($data)->map(fn($i) => [$i['date'], $i['localName'] ?? $i['name']])->toArray()
            );

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return self::FAILURE;
        }
    }
}
