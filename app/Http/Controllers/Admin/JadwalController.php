<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BKPH;
use App\Models\Jadwal;
use App\Models\Pegawai;
use App\Models\RPH;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    public function index()
    {
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        if (!$pegawai) {
            return redirect()->back()->with('error', 'Pegawai untuk user login tidak ditemukan.');
        }

        $bkph = BKPH::whereHas('pegawai', function ($q) use ($pegawai) {
            $q->where('id', $pegawai->id);
        })->first();

        if (!$bkph) {
            return redirect()->back()->with('error', 'BKPH untuk pegawai ini tidak ditemukan.');
        }

        $rph = RPH::where('bkph_id', $bkph->id)->get();

        if ($rph->isEmpty()) {
            return view('pages.admin.jadwal.index', [
                'jadwal' => collect()
            ]);
        }

        $pegawaiIds = $rph->pluck('pegawai_id')->unique();

        $jadwal = Jadwal::with([
            'pegawai.user',
            'pegawai.rph'
        ])
            ->whereIn('pegawai_id', $pegawaiIds)
            ->orderBy('tanggal')
            ->orderBy('waktu')
            ->get();


        return view('pages.admin.jadwal.index', compact('jadwal'));
    }

    public function create()
    {
        // Ambil pegawai login
        $pegawaiLogin = Pegawai::where('user_id', Auth::id())->first();

        // Default kosong
        $pegawai = collect();

        if ($pegawaiLogin) {

            // Ambil BKPH milik admin (lewat relasi pegawai → bkph)
            $bkph = BKPH::whereHas('pegawai', function ($q) use ($pegawaiLogin) {
                $q->where('id', $pegawaiLogin->id);
            })->first();

            if ($bkph) {

                // Ambil SEMUA pegawai RPH di bawah BKPH ini
                $pegawaiIds = RPH::where('bkph_id', $bkph->id)
                    ->pluck('pegawai_id')
                    ->unique();

                $pegawai = Pegawai::with('user')
                    ->whereIn('id', $pegawaiIds)
                    ->get();
            }
        }

        return view('pages.admin.jadwal.create', compact('pegawai'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'hari'       => 'required|string',
            'tanggal'    => 'required|date',
            'waktu'      => 'required',
            'kegiatan'   => 'required|string',
        ]);

        Jadwal::create($request->only([
            'pegawai_id',
            'hari',
            'tanggal',
            'waktu',
            'kegiatan'
        ]));

        return redirect()
            ->route('jadwalbkph.index')
            ->with('success', 'Jadwal berhasil ditambahkan');
    }

    public function edit($id)
    {
        $pegawaiLogin = Pegawai::where('user_id', Auth::id())->first();

        $pegawai = collect();
        $jadwal  = null;

        if ($pegawaiLogin) {

            $bkph = BKPH::whereHas('pegawai', function ($q) use ($pegawaiLogin) {
                $q->where('id', $pegawaiLogin->id);
            })->first();

            if ($bkph) {

                $pegawaiIds = RPH::where('bkph_id', $bkph->id)
                    ->pluck('pegawai_id')
                    ->unique();

                $pegawai = Pegawai::with('user')
                    ->whereIn('id', $pegawaiIds)
                    ->get();

                $jadwal = Jadwal::where('id', $id)
                    ->whereIn('pegawai_id', $pegawaiIds)
                    ->firstOrFail();
            }
        }

        return view('pages.admin.jadwal.edit', compact('jadwal', 'pegawai'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'hari'       => 'required|string',
            'tanggal'    => 'required|date',
            'waktu'      => 'required',
            'kegiatan'   => 'required|string',
        ]);

        $pegawaiLogin = Pegawai::where('user_id', Auth::id())->first();

        if (!$pegawaiLogin) {
            abort(403);
        }

        $bkph = BKPH::whereHas('pegawai', function ($q) use ($pegawaiLogin) {
            $q->where('id', $pegawaiLogin->id);
        })->firstOrFail();

        $pegawaiIds = RPH::where('bkph_id', $bkph->id)
            ->pluck('pegawai_id')
            ->unique();

        $jadwal = Jadwal::where('id', $id)
            ->whereIn('pegawai_id', $pegawaiIds)
            ->firstOrFail();

        $jadwal->update($request->only([
            'pegawai_id',
            'hari',
            'tanggal',
            'waktu',
            'kegiatan'
        ]));

        return redirect()
            ->route('jadwalbkph.index')
            ->with('success', 'Jadwal berhasil diperbarui');
    }

    /**
     * Hapus jadwal
     */
    public function delete($id)
    {
        Jadwal::findOrFail($id)->delete();

        return redirect()
            ->route('jadwalbkph.index')
            ->with('success', 'Jadwal berhasil dihapus');
    }
}
