<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Unit extends Model
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
    protected $table = 'unit';
    protected $fillable = ['id_kkn', 'tanggal_penerjunan', 'tanggal_penarikan', 'id_dpl', 'id_lokasi', 'nama'];

    public function kkn()
    {
        return $this->belongsTo(KKN::class, 'id_kkn');
    }

    public function dpl()
    {
        return $this->belongsTo(Dpl::class, 'id_dpl');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }

    public function mahasiswa()
    {
        return $this->hasMany(Mahasiswa::class, 'id_unit');
    }

    public function proker()
    {
        return $this->hasMany(Proker::class, 'id_unit');
    }
}
