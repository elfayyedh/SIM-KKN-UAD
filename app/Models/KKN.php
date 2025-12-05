<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KKN extends Model
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

    protected $table = 'kkn';
    protected $fillable = ['nama', 'tanggal_mulai', 'tanggal_selesai', 'tanggal_cutoff_penilaian', 'thn_ajaran', 'status'];
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'status' => 'boolean',
    ];

    public function mahasiswa()
    {
        return $this->hasMany(Mahasiswa::class, 'id_kkn');
    }

    public function units()
    {
        return $this->hasMany(Unit::class, 'id_kkn');
    }

    public function proker()
    {
        return $this->hasMany(Proker::class, 'id_kkn');
    }

    public function logbookHarian()
    {
        return $this->hasMany(LogbookHarian::class, 'id_kkn');
    }

    public function dpl()
    {
        return $this->hasMany(Dpl::class, 'id_kkn');
    }

    public function timMonev()
    {
        return $this->hasMany(TimMonev::class, 'id_kkn');
    }

    public function bidangProker()
    {
        return $this->hasMany(BidangProker::class, 'id_kkn');
    }

    public function kriteriaMonev()
    {
        return $this->hasMany(KriteriaMonev::class, 'id_kkn')->orderBy('urutan', 'asc');
    }
}