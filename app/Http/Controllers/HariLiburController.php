<?php

namespace App\Http\Controllers;

use App\Models\HariLibur;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class HariLiburController extends Controller
{
    public function index(Request $request)
    {
        $tahun    = (int) $request->query('tahun', date('Y'));
        $hariLibur = HariLibur::forYear($tahun)->orderBy('tanggal')->paginate(30)->withQueryString();

        return view('hari-libur.index', compact('hariLibur', 'tahun'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal'    => 'required|date',
            'nama_libur' => 'required|string|max:255',
            'sumber'     => 'required|in:nasional,internal',
        ]);

        $data['tahun'] = (int) date('Y', strtotime($data['tanggal']));

        // Cek duplikat
        $existing = HariLibur::where('tanggal', $data['tanggal'])
                              ->where('sumber', $data['sumber'])
                              ->first();

        if ($existing) {
            return back()->withErrors(['tanggal' => 'Tanggal ini sudah ada untuk sumber ' . $data['sumber']]);
        }

        HariLibur::create($data);
        ActivityLogger::log('hari_libur.created', 'Menambahkan hari libur: ' . $data['nama_libur'] . ' (' . $data['tanggal'] . ')');

        return back()->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function destroy(HariLibur $hariLibur)
    {
        $nama = $hariLibur->nama_libur;
        $hariLibur->delete();

        ActivityLogger::log('hari_libur.deleted', 'Menghapus hari libur: ' . $nama);

        return back()->with('success', 'Hari libur berhasil dihapus.');
    }

    /**
     * Trigger sync dari API (bisa dipanggil dari tombol di halaman)
     */
    public function sync(Request $request, ?int $tahun = null)
    {
        $tahun = $tahun ?? (int) date('Y');

        $exitCode = Artisan::call('libur:sync', ['tahun' => $tahun]);
        $output   = Artisan::output();

        if ($exitCode === 0) {
            ActivityLogger::log('hari_libur.synced', "Sync hari libur tahun {$tahun} dari API.");
            return back()->with('success', "Sync hari libur {$tahun} berhasil!");
        }

        return back()->withErrors(['sync' => 'Gagal sync dari API. ' . $output]);
    }
}
