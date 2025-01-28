<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Mahasiswa extends Model
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

    protected $table = 'mahasiswa';
    protected $fillable = ['id_user_role', 'id_unit', 'id_kkn', 'id_prodi', 'jabatan', 'nim'];

    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'id_user_role');
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit');
    }

    public function kkn()
    {
        return $this->belongsTo(KKN::class, 'id_kkn');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi');
    }

    public function kegiatan()
    {
        return $this->hasMany(Kegiatan::class, 'id_mahasiswa');
    }

    public function logbookHarian()
    {
        return $this->hasMany(LogbookHarian::class, 'id_mahasiswa');
    }

    public function logbookKegiatan()
    {
        return $this->hasMany(LogbookKegiatan::class, 'id_mahasiswa');
    }

    public function logbookSholat()
    {
        return $this->hasMany(LogbookSholat::class, 'id_mahasiswa');
    }
}
