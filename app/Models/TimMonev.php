<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TimMonev extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

     /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }

    protected $table = 'tim_monev';
    protected $fillable = ['id_dosen', 'id_kkn'];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'id_dosen');
    }

    public function kkn()
    {
        return $this->belongsTo(Kkn::class, 'id_kkn');
    }

    public function dplYangDievaluasi()
    {
        return $this->belongsToMany(
            Dpl::class,    
            'evaluasi_monev', 
            'id_tim_monev',   
            'id_dpl'          
        );
    }

    public function evaluasiMahasiswa()
    {
        return $this->hasMany(EvaluasiMahasiswa::class, 'id_tim_monev');
    }
}