<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Comment extends Model
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

    protected $table = 'comments';
    protected $fillable = ['id_bidang_proker', 'id_dpl', 'komentar'];

    public function bidangProker()
    {
        return $this->belongsTo(BidangProker::class, 'id_bidang_proker');
    }

    public function dpl()
    {
        return $this->belongsTo(Dpl::class, 'id_dpl');
    }
}
