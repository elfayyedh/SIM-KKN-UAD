<?php

namespace App\Http\Controllers;

use App\Models\DanaKegiatan;

class DanaController extends Controller
{
    public function index(string $id)
    {
        $dana = DanaKegiatan::where('id_logbook_kegiatan', $id)->get();
        return response()->json(['status' => 'ok', 'data' => $dana], 200);
    }
}
