<?php

namespace App\Http\Controllers;

use App\Models\Informasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InformasiController extends Controller
{
    //

    public function faqIndex()
    {
        $faqs = Informasi::where('type', 'faq')->get();
        return view('administrator.manajemen-informasi.faqs.read-faq', compact('faqs'));
    }


    public function saveInformasi(Request $request)
    {
        try {
            $author = Auth::user()->id;
            $request->validate([
                'id' => 'nullable',
                'judul' => 'required',
                'isi' => 'required',
                'type' => 'required|in:faq,pengumuman',
            ], [
                'judul.required' => 'judul harus diisi',
                'isi.required' => 'isi harus diisi',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        if ($request->input('type') == 'faq') {

            try {
                if ($request->input('id') == null) {
                    $faq = new Informasi();
                    $faq->judul = $request->input('judul');
                    $faq->isi = $request->input('isi');
                    $faq->type = $request->input('type');
                    $faq->author = $author;
                    $faq->save();
                    return redirect()->back()->with('success', 'Berhasil menyimpan Informasi');
                } else {
                    $faq = Informasi::find($request->input('id'));
                    $faq->judul = $request->input('judul');
                    $faq->isi = $request->input('isi');
                    $faq->type = $request->input('type');
                    $faq->author = $author;
                    $faq->save();
                    return redirect()->back()->with('success', 'Berhasil mengubah Informasi');
                }
            } catch (\Exception $ex) {
                return redirect()->back()->with('error', $ex->getMessage());
            }
        } else if ($request->input('type') == 'pengumuman') {

            try {
                if ($request->input('id') == null) {
                    $faq = new Informasi();
                    $faq->judul = $request->input('judul');
                    $faq->isi = $request->input('isi');
                    $faq->type = $request->input('type');
                    $faq->author = $author;
                    $faq->save();
                    return redirect()->back()->with('success', 'Berhasil menyimpan Pengumuman');
                } else {
                    $faq = Informasi::find($request->input('id'));
                    $faq->judul = $request->input('judul');
                    $faq->isi = $request->input('isi');
                    $faq->type = $request->input('type');
                    $faq->author = $author;
                    $faq->save();
                    return redirect()->back()->with('success', 'Berhasil mengubah Pengumuman');
                }
            } catch (\Exception $ex) {
                return redirect()->back()->with('error', $ex->getMessage());
            }
        }
    }

    public function setFaqStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:informasi,id',
            'status' => 'required',
        ]);

        $faq = Informasi::find($request->input('id'));
        $faq->status = !$request->input('status');
        $faq->save();
        if ($faq) {
            return redirect()->back()->with('success', 'Berhasil mengubah status Informasi');
        } else {
            return redirect()->back()->with('error', 'Gagal mengubah status Informasi');
        }
    }

    public function deleteInformasi(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:informasi,id'
        ]);

        $faq = Informasi::find($request->input('id'));
        $faq->delete();
        if ($faq) {
            return redirect()->back()->with('success', 'Berhasil menghapus Informasi');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus Informasi');
        }
    }

    // Pengumuman


    public function pengumumanIndex()
    {
        $data = Informasi::where('type', 'pengumuman')->get();
        return view('administrator.manajemen-informasi.faqs.read-pengumuman', compact('data'));
    }


    // View

    public function faqView()
    {
        $data = Informasi::where(['status' => true, 'type' => 'faq'])->get();
        return view('faq', compact('data'));
    }

    public function timelineView()
    {
        return view('timeline');
    }

    public function pengumumanView()
    {
        $data = Informasi::where(['status' => true, 'type' => 'pengumuman'])->get();
        return view('pengumuman', compact('data'));
    }
}
