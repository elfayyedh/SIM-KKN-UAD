<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Proker extends Model
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

    protected $table = 'proker';
    protected $fillable = ['id_kkn', 'id_unit', 'id_bidang', 'nama', 'tempat', 'total_jkem', 'jabatan'];


    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit');
    }

    public function kegiatan()
    {
        return $this->hasMany(Kegiatan::class, 'id_proker');
    }

    public function bidang()
    {
        return $this->belongsTo(BidangProker::class, 'id_bidang');
    }

    public function tempatDanSasaran()
    {
        return $this->hasMany(TempatSasaran::class, 'id_proker');
    }

    public function organizer(){
        return $this->hasMany(Organizer::class, 'id_proker');
    }

    public function logbookKegiatan()
    {
        return $this->hasMany(LogbookKegiatan::class, 'id_proker');
    }
}