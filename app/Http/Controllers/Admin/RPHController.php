<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BKPH;
use App\Models\Pegawai;
use App\Models\RPH;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RPHController extends Controller
{
    public function index()
    {
        // Ambil pegawai dari user login
        $pegawai = Pegawai::where('user_id', Auth::id())->first();

        if (!$pegawai) {
            return redirect()->back()->with('error', 'Pegawai untuk user login tidak ditemukan.');
        }

        // Ambil BKPH yang memiliki pegawai ini
        $bkph = BKPH::whereHas('pegawai', function ($q) use ($pegawai) {
            $q->where('id', $pegawai->id);
        })->first();

        if (!$bkph) {
            return redirect()->back()->with('error', 'BKPH untuk pegawai ini tidak ditemukan.');
        }

        // Ambil RPH yang memiliki bkph_id sama
        $rph = RPH::with(['pegawai.user', 'bkph'])
            ->where('bkph_id', $bkph->id)
            ->get();

        // Ambil semua pegawai (jika perlu untuk dropdown atau info tambahan)
        $allPegawai = Pegawai::with('user')->get();

        return view('pages.admin.rph.index', compact('rph', 'allPegawai'));
    }

    public function create()
    {
        // Ambil user yang sedang login
        $currentUser = auth()->user();

        // Cari pegawai berdasarkan user yang sedang login
        $currentPegawai = Pegawai::where('user_id', $currentUser->id)->first();

        if (!$currentPegawai) {
            return redirect()->back()->with('error', 'Data pegawai tidak ditemukan untuk user yang sedang login.');
        }

        // Cari BKPH berdasarkan pegawai yang sedang login
        $bkph = BKPH::where('pegawai_id', $currentPegawai->id)->first();

        if (!$bkph) {
            return redirect()->back()->with('error', 'BKPH tidak ditemukan untuk pegawai yang sedang login.');
        }

        // Ambil semua pegawai yang memiliki user dengan role 'user' (untuk dropdown jika diperlukan)
        $pegawai = Pegawai::whereHas('user', function ($q) {
            $q->where('role', 'user');
        })->with('user')->get();

        return view('pages.admin.rph.create', compact('pegawai', 'bkph', 'currentPegawai'));
    }

    // Store RPH
    public function store(Request $request)
    {
        $request->validate([
            'bkph_id'    => 'required|exists:bkph,id',
            'pegawai_id' => 'required|exists:pegawai,id',
            'sektor'     => 'required|string|max:255',
            'no_telp'    => 'required|string|max:20',
        ]);

        // Validasi bahwa bkph_id yang dikirim sesuai dengan user yang login
        $currentUser = auth()->user();
        $currentPegawai = Pegawai::where('user_id', $currentUser->id)->first();

        if (!$currentPegawai) {
            return redirect()->back()->with('error', 'Data pegawai tidak ditemukan untuk user yang sedang login.');
        }

        $bkph = BKPH::where('pegawai_id', $currentPegawai->id)->first();

        if (!$bkph || $bkph->id != $request->bkph_id) {
            return redirect()->back()->with('error', 'BKPH tidak sesuai dengan pegawai yang sedang login.');
        }

        // Ambil pegawai yang dipilih dari form
        $pegawai = Pegawai::with('user')
            ->where('id', $request->pegawai_id)
            ->whereHas('user', fn($q) => $q->where('role', 'user'))
            ->first();

        if (!$pegawai) {
            return redirect()->back()->with('error', 'Pegawai tidak ditemukan atau bukan role user.');
        }

        RPH::create([
            'bkph_id'    => $request->bkph_id, // Menggunakan bkph_id dari form (yang sudah otomatis)
            'pegawai_id' => $pegawai->id,
            'sektor'     => $request->sektor,
            'no_telp'    => $request->no_telp,
        ]);

        return redirect()->route('adminrph.index')->with('success', 'RPH berhasil ditambahkan.');
    }

    // Edit RPH
    public function edit(RPH $rph)
    {
        // Ambil user yang sedang login
        $currentUser = auth()->user();

        // Cari pegawai berdasarkan user yang sedang login
        $currentPegawai = Pegawai::where('user_id', $currentUser->id)->first();

        if (!$currentPegawai) {
            return redirect()->back()->with('error', 'Data pegawai tidak ditemukan untuk user yang sedang login.');
        }

        // Debug: cek struktur relasi BKPH
        // Jika BKPH memiliki relasi pegawai (belongsTo), gunakan:
        $bkph = BKPH::where('pegawai_id', $currentPegawai->id)->first();

        // Atau jika BKPH memiliki relasi hasMany dengan pegawai, gunakan:
        // $bkph = BKPH::whereHas('pegawai', function($q) use ($currentPegawai) {
        //     $q->where('id', $currentPegawai->id);
        // })->first();

        // Debug: cek apakah $bkph ada dan tidak null
        if (!$bkph) {
            return redirect()->back()->with('error', 'BKPH tidak ditemukan untuk pegawai yang sedang login.');
        }

        // Ambil semua pegawai dengan role 'user' (untuk dropdown)
        $pegawai = Pegawai::whereHas('user', fn($q) => $q->where('role', 'user'))
            ->with('user')
            ->get();

        return view('pages.admin.rph.edit', compact('rph', 'pegawai', 'bkph'));
    }

    // Update RPH
    public function update(Request $request, RPH $rph)
    {
        $request->validate([
            'bkph_id'    => 'required|exists:bkph,id',
            'pegawai_id' => 'required|exists:pegawai,id',
            'sektor'     => 'required|string|max:255',
            'no_telp'    => 'required|string|max:20',
        ]);

        // Validasi BKPH sesuai pegawai yang login
        $currentUser = auth()->user();
        $currentPegawai = Pegawai::where('user_id', $currentUser->id)->first();

        if (!$currentPegawai) {
            return redirect()->back()->with('error', 'Data pegawai tidak ditemukan untuk user yang sedang login.');
        }

        // Sama seperti di method edit, pastikan query menghasilkan single model
        $bkph = BKPH::where('pegawai_id', $currentPegawai->id)->first();

        // Validasi BKPH ditemukan
        if (!$bkph) {
            return redirect()->back()->with('error', 'BKPH tidak ditemukan untuk pegawai yang sedang login.');
        }

        // Validasi BKPH ID sesuai
        if ($bkph->id != $request->bkph_id) {
            return redirect()->back()->with('error', 'BKPH tidak sesuai dengan pegawai yang sedang login.');
        }

        // Ambil pegawai yang dipilih dari form dan pastikan role user
        $pegawai = Pegawai::with('user')
            ->where('id', $request->pegawai_id)
            ->whereHas('user', fn($q) => $q->where('role', 'user'))
            ->first();

        if (!$pegawai) {
            return redirect()->back()->with('error', 'Pegawai tidak ditemukan atau bukan role user.');
        }

        $rph->update([
            'bkph_id'    => $request->bkph_id,
            'pegawai_id' => $pegawai->id,
            'sektor'     => $request->sektor,
            'no_telp'    => $request->no_telp,
        ]);

        return redirect()->route('adminrph.index')->with('success', 'Data RPH berhasil diperbarui.');
    }

    // Delete RPH method (sesuai route yang ada)
    public function delete(RPH $rph)
    {
        // Opsional: validasi bahwa user hanya bisa hapus RPH yang sesuai dengan BKPH-nya
        $currentUser = auth()->user();
        $currentPegawai = Pegawai::where('user_id', $currentUser->id)->first();

        if ($currentPegawai) {
            $bkph = BKPH::where('pegawai_id', $currentPegawai->id)->first();

            if ($bkph && $rph->bkph_id !== $bkph->id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus data RPH ini.');
            }
        }

        $rph->delete();

        return redirect()->route('adminrph.index')->with('success', 'Data RPH berhasil dihapus.');
    }

}
