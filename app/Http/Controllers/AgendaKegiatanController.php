<?php

namespace App\Http\Controllers;

use App\Models\AgendaKegiatan;
use App\Models\HariLibur;
use App\Models\Project;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgendaKegiatanController extends Controller
{
    /**
     * Tampilkan halaman Agenda Tahunan (Gantt + Kalender)
     */
    public function index(Request $request)
    {
        $tahun = (int) $request->query('tahun', date('Y'));

        // Data agenda untuk tahun ini
        $agendas = AgendaKegiatan::where('tahun', $tahun)
            ->orderBy('tanggal_mulai')
            ->orderBy('id')
            ->get();

        // Hari libur untuk kalender
        $hariLibur = HariLibur::forYear($tahun)->get()->map(fn($h) => [
            'tanggal'    => $h->tanggal->format('Y-m-d'),
            'nama_libur' => $h->nama_libur,
            'sumber'     => $h->sumber,
        ]);

        // Bangun array hari per bulan untuk header Gantt
        $bulanData = $this->buildBulanData($tahun);

        return view('agenda.index', compact('agendas', 'tahun', 'hariLibur', 'bulanData'));
    }

    /**
     * Simpan kegiatan baru (tanpa tanggal dulu)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'kategori'      => 'nullable|string|max:100',
            'keterangan'    => 'nullable|string',
            'tahun'         => 'required|integer|min:2020|max:2099',
        ]);

        $data['created_by']    = auth()->id();
        $data['unit_kerja_id'] = auth()->user()->isSuperAdmin()
            ? null
            : auth()->user()->unit_kerja_id;

        $agenda = AgendaKegiatan::create($data);

        ActivityLogger::log('agenda.created', 'Membuat agenda kegiatan: ' . $agenda->nama_kegiatan);

        return response()->json([
            'success' => true,
            'agenda'  => $agenda->fresh(),
            'message' => 'Kegiatan berhasil ditambahkan.',
        ]);
    }

    /**
     * Update nama/kategori/keterangan (tanpa tanggal)
     */
    public function update(Request $request, AgendaKegiatan $agenda)
    {
        $data = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'kategori'      => 'nullable|string|max:100',
            'keterangan'    => 'nullable|string',
        ]);

        $agenda->update($data);

        // Sync nama ke project terkait jika ada
        if ($agenda->project_id) {
            Project::withoutGlobalScopes()
                ->where('id', $agenda->project_id)
                ->update(['name' => $data['nama_kegiatan']]);
        }

        ActivityLogger::log('agenda.updated', 'Memperbarui agenda: ' . $agenda->nama_kegiatan);

        return response()->json(['success' => true, 'agenda' => $agenda->fresh()]);
    }

    /**
     * AJAX: Simpan mapping tanggal dan buat/update Project
     */
    public function mapping(Request $request, AgendaKegiatan $agenda)
    {
        $data = $request->validate([
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        DB::transaction(function () use ($agenda, $data) {
            // 1. Update tanggal di agenda
            $agenda->update($data);

            // 2. Buat atau update Project terkait
            $user = auth()->user();
            $projectData = [
                'name'          => $agenda->nama_kegiatan,
                'year'          => (int) date('Y', strtotime($data['tanggal_mulai'])),
                'start_date'    => $data['tanggal_mulai'],
                'end_date'      => $data['tanggal_selesai'],
                'description'   => '[Agenda Tahunan] ' . ($agenda->keterangan ?? ''),
                'created_by'    => auth()->id(),
                'unit_kerja_id' => $user->isSuperAdmin() ? null : $user->unit_kerja_id,
                'status'        => 'Belum Mulai',
            ];

            if ($agenda->project_id) {
                // Update project yang sudah ada
                Project::withoutGlobalScopes()
                    ->where('id', $agenda->project_id)
                    ->update($projectData);
            } else {
                // Buat project baru
                $project = Project::withoutGlobalScopes()->create($projectData);
                $agenda->update(['project_id' => $project->id]);
            }
        });

        ActivityLogger::log('agenda.mapped', "Mapping tanggal agenda '{$agenda->nama_kegiatan}': {$data['tanggal_mulai']} s/d {$data['tanggal_selesai']}");

        return response()->json([
            'success' => true,
            'agenda'  => $agenda->fresh(),
            'message' => 'Tanggal berhasil disimpan dan Program dibuat.',
        ]);
    }

    /**
     * Reset mapping (hapus tanggal, detach project)
     */
    public function resetMapping(AgendaKegiatan $agenda)
    {
        $namaKegiatan = $agenda->nama_kegiatan;

        // Soft-delete project terkait jika ada
        if ($agenda->project_id) {
            Project::withoutGlobalScopes()
                ->where('id', $agenda->project_id)
                ->delete();
        }

        $agenda->update([
            'tanggal_mulai'   => null,
            'tanggal_selesai' => null,
            'project_id'      => null,
        ]);

        ActivityLogger::log('agenda.reset', "Reset mapping agenda: {$namaKegiatan}");

        return response()->json(['success' => true, 'message' => 'Mapping tanggal berhasil direset.']);
    }

    /**
     * Hapus kegiatan beserta project terkait
     */
    public function destroy(AgendaKegiatan $agenda)
    {
        $namaKegiatan = $agenda->nama_kegiatan;

        DB::transaction(function () use ($agenda) {
            if ($agenda->project_id) {
                Project::withoutGlobalScopes()
                    ->where('id', $agenda->project_id)
                    ->delete();
            }
            $agenda->delete();
        });

        ActivityLogger::log('agenda.deleted', "Menghapus agenda: {$namaKegiatan}");

        return response()->json(['success' => true, 'message' => 'Kegiatan berhasil dihapus.']);
    }

    /**
     * Build array struktur bulan + minggu + hari untuk header Gantt
     */
    private function buildBulanData(int $tahun): array
    {
        $bulanData = [];
        $namaBulan = [
            1  => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4  => 'April',   5 => 'Mei',       6 => 'Juni',
            7  => 'Juli',    8 => 'Agustus',   9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
            $hariList   = [];

            for ($hari = 1; $hari <= $jumlahHari; $hari++) {
                $tanggal    = sprintf('%04d-%02d-%02d', $tahun, $bulan, $hari);
                $minggu     = (int) ceil($hari / 7);
                $hariInWeek = date('N', strtotime($tanggal)); // 1=Mon, 7=Sun

                $hariList[] = [
                    'tanggal'  => $tanggal,
                    'hari'     => $hari,
                    'minggu'   => $minggu,
                    'is_minggu' => $hariInWeek == 7,
                ];
            }

            // Kelompokkan per minggu
            $mingguData = [];
            foreach ($hariList as $h) {
                $mingguData[$h['minggu']][] = $h;
            }

            $bulanData[] = [
                'bulan'       => $bulan,
                'nama'        => $namaBulan[$bulan],
                'jumlah_hari' => $jumlahHari,
                'hari_list'   => $hariList,
                'minggu_data' => $mingguData,
            ];
        }

        return $bulanData;
    }
}
