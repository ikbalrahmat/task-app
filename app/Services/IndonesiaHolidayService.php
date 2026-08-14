<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * IndonesiaHolidayService
 *
 * Fetch hari libur nasional + cuti bersama Indonesia
 * dari API publik api-harilibur.vercel.app
 *
 * - Data di-cache per bulan selama 30 hari
 * - Kalau API down, fallback ke data hardcode
 */
class IndonesiaHolidayService
{
    protected string $baseUrl = 'https://api-harilibur.vercel.app/api';

    /**
     * Kembalikan semua hari libur untuk satu tahun.
     *
     * Format kembalian:
     * [
     *   'Y-m-d' => [
     *     'name'       => 'Nama Hari Libur',
     *     'is_national' => true/false  (false = cuti bersama)
     *   ]
     * ]
     */
    public function getHolidays(int $year): array
    {
        $cacheKey = "holidays_indonesia_{$year}";

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($year) {
            return $this->fetchFromApi($year);
        });
    }

    /**
     * Fetch semua bulan untuk satu tahun dari API.
     */
    protected function fetchFromApi(int $year): array
    {
        $result = [];

        try {
            // Fetch semua bulan sekaligus (bulan 1-12)
            for ($month = 1; $month <= 12; $month++) {
                $response = Http::timeout(10)
                    ->get($this->baseUrl, [
                        'month' => $month,
                        'year'  => $year,
                    ]);

                if ($response->successful()) {
                    $items = $response->json();
                    if (is_array($items)) {
                        foreach ($items as $item) {
                            if (!empty($item['holiday_date'])) {
                                $result[$item['holiday_date']] = [
                                    'name'        => $item['holiday_name'] ?? 'Hari Libur',
                                    'is_national' => (bool) ($item['is_national_holiday'] ?? true),
                                ];
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning("IndonesiaHolidayService: API fetch gagal untuk tahun {$year}. " . $e->getMessage());

            // Fallback ke data hardcode
            return $this->fallbackHolidays($year);
        }

        // Kalau API tidak mengembalikan data sama sekali, fallback
        if (empty($result)) {
            return $this->fallbackHolidays($year);
        }

        ksort($result);
        return $result;
    }

    /**
     * Data hardcode sebagai fallback kalau API tidak tersedia.
     */
    protected function fallbackHolidays(int $year): array
    {
        $fixed = [
            "$year-01-01" => ['name' => 'Tahun Baru Masehi',                    'is_national' => true],
            "$year-05-01" => ['name' => 'Hari Buruh Internasional',              'is_national' => true],
            "$year-06-01" => ['name' => 'Hari Lahir Pancasila',                  'is_national' => true],
            "$year-08-17" => ['name' => 'Hari Kemerdekaan Republik Indonesia',   'is_national' => true],
            "$year-12-25" => ['name' => 'Hari Raya Natal',                       'is_national' => true],
        ];

        $variable = [
            2025 => [
                '2025-01-27' => ['name' => 'Isra Mikraj Nabi Muhammad SAW',           'is_national' => true],
                '2025-01-29' => ['name' => 'Tahun Baru Imlek 2576',                    'is_national' => true],
                '2025-03-28' => ['name' => 'Hari Raya Nyepi (Tahun Baru Saka 1947)',   'is_national' => true],
                '2025-03-30' => ['name' => 'Hari Raya Idul Fitri 1446 H',              'is_national' => true],
                '2025-03-31' => ['name' => 'Hari Raya Idul Fitri 1446 H (Hari Kedua)','is_national' => true],
                '2025-04-18' => ['name' => 'Jumat Agung',                              'is_national' => true],
                '2025-05-12' => ['name' => 'Hari Raya Waisak 2569',                    'is_national' => true],
                '2025-05-29' => ['name' => 'Kenaikan Isa Almasih',                     'is_national' => true],
                '2025-06-07' => ['name' => 'Hari Raya Idul Adha 1446 H',               'is_national' => true],
                '2025-06-27' => ['name' => 'Tahun Baru Islam 1447 H',                  'is_national' => true],
                '2025-09-05' => ['name' => 'Maulid Nabi Muhammad SAW',                 'is_national' => true],
            ],
            2026 => [
                '2026-01-17' => ['name' => 'Tahun Baru Imlek 2577',                    'is_national' => true],
                '2026-02-16' => ['name' => 'Isra Mikraj Nabi Muhammad SAW',            'is_national' => true],
                '2026-03-04' => ['name' => 'Hari Raya Nyepi (Tahun Baru Saka 1948)',   'is_national' => true],
                '2026-03-20' => ['name' => 'Hari Raya Idul Fitri 1447 H',              'is_national' => true],
                '2026-03-21' => ['name' => 'Hari Raya Idul Fitri 1447 H (Hari Kedua)','is_national' => true],
                '2026-04-03' => ['name' => 'Jumat Agung',                              'is_national' => true],
                '2026-05-14' => ['name' => 'Kenaikan Isa Almasih',                     'is_national' => true],
                '2026-05-26' => ['name' => 'Hari Raya Waisak 2570',                    'is_national' => true],
                '2026-05-27' => ['name' => 'Hari Raya Idul Adha 1447 H',               'is_national' => true],
                '2026-06-16' => ['name' => 'Tahun Baru Islam 1448 H',                  'is_national' => true],
                '2026-08-25' => ['name' => 'Maulid Nabi Muhammad SAW',                 'is_national' => true],
            ],
        ];

        $result = $fixed + ($variable[$year] ?? []);
        ksort($result);
        return $result;
    }

    /**
     * Paksa refresh cache (misal dipanggil dari artisan atau admin).
     */
    public function refreshCache(int $year): array
    {
        Cache::forget("holidays_indonesia_{$year}");
        return $this->getHolidays($year);
    }
}
