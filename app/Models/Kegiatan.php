<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LogbookKegiatan;
use Illuminate\Support\Str;

class Kegiatan extends Model
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

    protected $table = 'kegiatan';
    protected $fillable = ['id_proker', 'id_mahasiswa', 'id_tempat_sasaran', 'nama', 'frekuensi', 'jkem', 'total_jkem'];

    public function proker()
    {
        return $this->belongsTo(Proker::class, 'id_proker');
    }



    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa');
    }

    public function tanggalRencanaProker()
    {
        return $this->hasMany(TanggalRencanaProker::class, 'id_kegiatan');
    }

    public function logbookKegiatan()
    {
        return $this->hasMany(LogbookKegiatan::class, 'id_kegiatan');
    }
}
