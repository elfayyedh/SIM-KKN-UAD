<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\BidangProker;
use App\Models\Dpl;
use App\Models\Dosen; // Pastikan Dosen di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Helper baru yang sudah divaksinasi.
     * * Cek apakah Dosen yang login adalah DPL yang berhak
     * untuk Bidang Proker ini.
     */
    private function getActiveDplAssignmentId($id_bidang_proker)
    {
        // 1. Cek apakah role aktifnya 'dpl'
        if (session('active_role') != 'dpl') {
            throw new \Exception('Aksi ini hanya untuk DPL yang aktif.');
        }

        // 2. Dapatkan data Dosen
        $dosen = Auth::user()->dosen; 
        if (!$dosen) {
            throw new \Exception('Profil Dosen tidak ditemukan.');
        }

        // 3. Cari KKN dari Bidang Proker
        $bidangProker = BidangProker::find($id_bidang_proker);
        if (!$bidangProker) {
            throw new \Exception('Bidang Proker tidak ditemukan.');
        }
        $kkn_id = $bidangProker->id_kkn; // Asumsi relasi ini ada

        // 4. Cek apakah Dosen ini adalah DPL untuk KKN tersebut
        $dplAssignment = Dpl::where('id_dosen', $dosen->id)
                            ->where('id_kkn', $kkn_id)
                            ->first();
        
        if (!$dplAssignment) {
            throw new \Exception('Anda bukan DPL penanggung jawab untuk KKN ini.');
        }

        // 5. Kembalikan ID penugasan DPL yang valid
        return $dplAssignment->id;
    }


    /**
     * Store a newly created comment.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_bidang_proker' => 'required|exists:bidang_proker,id', 
            'komentar' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error', 'message' => 'Validasi gagal', 'errors' => $validator->errors()
            ], 422);
        }

        try {
            // PERUBAHAN: Kirim 'id_bidang_proker' ke helper
            $id_dpl = $this->getActiveDplAssignmentId($request->id_bidang_proker);

            $comment = Comment::create([
                'id_bidang_proker' => $request->id_bidang_proker,
                'id_dpl' => $id_dpl,
                'komentar' => $request->komentar,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Komentar berhasil ditambahkan',
                'data' => $comment->load('dpl.dosen.user') 
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal simpan: ' . $e->getMessage()
            ], 403);
        }
    }

    /**
     * Get comments for a specific bidang proker.
     * (Fungsi ini tidak perlu diubah, sudah benar)
     */
    public function getComments($id_bidang_proker)
    {
        $comments = Comment::where('id_bidang_proker', $id_bidang_proker)
            ->with('dpl.dosen.user') 
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $comments
        ]);
    }

    /**
     * Update the specified comment.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'komentar' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error', 'message' => 'Validasi gagal', 'errors' => $validator->errors()
            ], 422);
        }

        try {
            $comment = Comment::findOrFail($id);

            // PERUBAHAN: Dapatkan id_bidang_proker dari komentar yang ada
            $id_dpl_dari_helper = $this->getActiveDplAssignmentId($comment->id_bidang_proker);
            
            // Cek kepemilikan
            if ($comment->id_dpl !== $id_dpl_dari_helper) {
                return response()->json([
                    'status' => 'error', 'message' => 'Anda tidak memiliki akses untuk mengedit komentar ini'
                ], 403);
            }

            $comment->update([
                'komentar' => $request->komentar,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Komentar berhasil diupdate',
                'data' => $comment->load('dpl.dosen.user')
            ]);
        
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal update: ' . $e->getMessage()
            ], 403);
        }
    }

    /**
     * Remove the specified comment.
     */
    public function destroy($id)
    {
        try {
            $comment = Comment::findOrFail($id);
            
            // PERUBAHAN: Dapatkan id_bidang_proker dari komentar yang ada
            $id_dpl_dari_helper = $this->getActiveDplAssignmentId($comment->id_bidang_proker);

            // Cek kepemilikan
            if ($comment->id_dpl !== $id_dpl_dari_helper) {
                return response()->json([
                    'status' => 'error', 'message' => 'Anda tidak memiliki akses untuk menghapus komentar ini'
                ], 403);
            }

            $comment->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Komentar berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal hapus: ' . $e->getMessage()
            ], 403);
        }
    }
}