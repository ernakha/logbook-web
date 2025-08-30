<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BKPH;
use App\Models\Laporan;
use App\Models\RPH;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(BKPH $bkph, RPH $rph)
    {
        // $rph sudah instance model karena Route Model Binding
        $laporan = Laporan::where('pegawai_id', $rph->pegawai_id)
            ->with('pegawai.user')
            ->get();
        $rph = RPH::where('bkph_id', $bkph->id)->first();
        return view('pages.super.laporan.index', compact('bkph', 'rph', 'laporan'));
    }

    public function approve(Laporan $laporan)
    {
        try {
            // Cek apakah laporan masih dalam status 'proses'
            if ($laporan->status !== 'proses') {
                return response()->json([
                    'success' => false,
                    'message' => 'Laporan sudah tervalidasi atau tidak dapat diubah.'
                ], 422);
            }

            // Update status laporan
            $laporan->update(['status' => 'divalidasi']);

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil divalidasi.'
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memvalidasi laporan.'
            ], 500);
        }
    }
}
