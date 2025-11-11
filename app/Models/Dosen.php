<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'dosen';

    protected $fillable = [
        'id_user',
        'nip',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function dplAssignments()
    {
        return $this->hasMany(Dpl::class, 'id_dosen');
    }

    public function timMonevAssignments()
    {
        return $this->hasMany(TimMonev::class, 'id_dosen');
    }

    public function isDpl()
    {
        return $this->dplAssignments()->exists();
    }

    public function isMonev()
    {
        return $this->timMonevAssignments()->exists();
    }
}