<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LogbookKegiatan extends Model
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

    protected $table = 'logbook_kegiatan';
    protected $fillable = ['id', 'id_logbook_harian', 'id_unit', 'jam_mulai', 'jam_selesai', 'total_jkem', 'jenis', 'id_kegiatan', 'id_mahasiswa', 'deskripsi'];

    public function logbookHarian()
    {
        return $this->belongsTo(LogbookHarian::class, 'id_logbook_harian');
    }


    public function dana()
    {
        return $this->hasMany(DanaKegiatan::class, 'id_logbook_kegiatan');
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan', 'id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id');
    }
}
