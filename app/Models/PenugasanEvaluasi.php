<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PenugasanEvaluasi extends Model
{
    use HasFactory;
    protected $table = 'penugasan_evaluasi';
    protected $fillable = ['id_tim_monev', 'id_dpl', 'status'];

    public function timMonev()
    {
        return $this->belongsTo(TimMonev::class, 'id_tim_monev');
    }

    public function dpl()
    {
        return $this->belongsTo(Dpl::class, 'id_dpl');
    }
}
