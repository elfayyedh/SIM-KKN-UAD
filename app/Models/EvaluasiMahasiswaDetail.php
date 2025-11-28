<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; 

class EvaluasiMahasiswaDetail extends Model
{
    use HasFactory;

    protected $table = 'evaluasi_mahasiswa_detail';
    
    protected $fillable = [
        'id_evaluasi_mahasiswa', 
        'id_kriteria_monev', 
        'nilai'
    ];

    public $incrementing = false; 
    protected $keyType = 'string'; 

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function kriteria()
    {
        return $this->belongsTo(KriteriaMonev::class, 'id_kriteria_monev');
    }
}