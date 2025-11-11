<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluasiMahasiswa extends Model
{
    use HasFactory, HasUuids;

    /**
     * Nama tabel di database.
     */
    protected $table = 'evaluasi_mahasiswa';
    
    /**
     * Kolom yang boleh diisi secara massal (penting untuk create/update).
     */
    protected $fillable = [
        'id_tim_monev',
        'id_mahasiswa',
        'eval_jkem',
        'eval_form1',
        'eval_form2',
        'eval_form3',
        'eval_form4',
        'eval_sholat',
        'catatan_monev',
    ];

    /**
     * Relasi ke TimMonev (yang menilai).
     */
    public function timMonev()
    {
        return $this->belongsTo(TimMonev::class, 'id_tim_monev');
    }

    /**
     * Relasi ke Mahasiswa (yang dinilai).
     */
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa');
    }
}