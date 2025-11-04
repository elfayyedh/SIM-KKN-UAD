<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\BidangProker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Store a newly created comment.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_bidang_proker' => 'required|uuid|exists:bidang_proker,id',
            'komentar' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get DPL ID from authenticated user
        $id_dpl = Auth::user()->userRoles->find(session('selected_role'))->dpl->id;

        $comment = Comment::create([
            'id_bidang_proker' => $request->id_bidang_proker,
            'id_dpl' => $id_dpl,
            'komentar' => $request->komentar,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Komentar berhasil ditambahkan',
            'data' => $comment->load('dpl.userRole.user')
        ]);
    }

    /**
     * Get comments for a specific bidang proker.
     */
    public function getComments($id_bidang_proker)
    {
        $comments = Comment::where('id_bidang_proker', $id_bidang_proker)
            ->with('dpl.userRole.user')
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
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $comment = Comment::findOrFail($id);

        // Check if the authenticated DPL owns this comment
        $id_dpl = Auth::user()->userRoles->find(session('selected_role'))->dpl->id;
        if ($comment->id_dpl !== $id_dpl) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk mengedit komentar ini'
            ], 403);
        }

        $comment->update([
            'komentar' => $request->komentar,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Komentar berhasil diupdate',
            'data' => $comment->load('dpl.userRole.user')
        ]);
    }

    /**
     * Remove the specified comment.
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        // Check if the authenticated DPL owns this comment
        $id_dpl = Auth::user()->userRoles->find(session('selected_role'))->dpl->id;
        if ($comment->id_dpl !== $id_dpl) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk menghapus komentar ini'
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Komentar berhasil dihapus'
        ]);
    }
}
