<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'jenis_kelamin',
        'no_telp'
    ];

    public function kkn()
    {
        return $this->belongsTo(Kkn::class, 'id_kkn');
    }

    public function dosen()
    {
        return $this->hasOne(Dosen::class, 'id_user');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role', 'id_user', 'id_role')->withPivot('id as id_user_role', 'id_kkn');;
    }

    public function userRoles()
    {
        return $this->hasMany(UserRole::class, 'id_user');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hasRole(string $nama_role, string $id_kkn = null): bool
    {
        // 1. Ambil query dasar ke tabel user_roles
        $query = $this->userRoles()
                      ->join('roles', 'user_role.id_role', '=', 'roles.id')
                      ->where('roles.nama_role', $nama_role);

        // 2. Jika ID KKN disediakan, cek secara spesifik (kontekstual)
        if ($id_kkn) {
            $query->where('user_role.id_kkn', $id_kkn);
        }
        
        // 3. Cek apakah ada.
        // Jika tidak ada ID KKN (global), dia akan cek:
        //    a) Role spesifik KKN (jika ada)
        //    b) Role global (jika id_kkn di tabel adalah NULL, e.g., Admin)
        if (!$id_kkn) {
             $query->orWhere(function ($q) use ($nama_role) {
                $q->whereNull('user_role.id_kkn')
                  ->join('roles', 'user_role.id_role', '=', 'roles.id')
                  ->where('roles.nama_role', $nama_role)
                  ->where('user_role.id_user', $this->id); // Pastikan untuk user ini
             });
        }

        return $query->exists();
    }

    public function kknWhereUserHasRole(string $nama_role)
    {
        $roleId = Role::where('nama_role', $nama_role)->value('id');

        $kkn_ids = $this->userRoles()
                        ->where('id_role', $roleId)
                        ->whereNotNull('id_kkn')
                        ->pluck('id_kkn');

        return KKN::whereIn('id', $kkn_ids)->get();
    }
}
