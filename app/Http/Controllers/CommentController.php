<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\BidangProker;
use App\Models\Dpl;    
use App\Models\Dosen;  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    private function getActiveDplAssignmentId()
    {
        $activeUserRole = Auth::user()->userRoles()->find(session('selected_role'));
        if (!$activeUserRole || $activeUserRole->role->nama_role != 'DPL') {
             throw new \Exception('Aksi ini hanya untuk DPL.');
        }
        $kkn_id = $activeUserRole->id_kkn;

        $dosen = Auth::user()->dosen; 
        if (!$dosen) {
            throw new \Exception('Profil Dosen tidak ditemukan.');
        }

        $dplAssignment = Dpl::where('id_dosen', $dosen->id)
                            ->where('id_kkn', $kkn_id)
                            ->first();
        
        if (!$dplAssignment) {
            throw new \Exception('Penugasan DPL untuk KKN ini tidak ditemukan.');
        }
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
            $id_dpl = $this->getActiveDplAssignmentId();

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

            $id_dpl = $this->getActiveDplAssignmentId();
            
            if ($comment->id_dpl !== $id_dpl) {
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
            $id_dpl = $this->getActiveDplAssignmentId();

            if ($comment->id_dpl !== $id_dpl) {
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