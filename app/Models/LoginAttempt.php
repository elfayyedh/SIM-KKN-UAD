<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $table = 'login_attempts';

    protected $fillable = [
        'username',
        'failed_attempts',
        'locked',
        'locked_at',
    ];

    protected $casts = [
        'locked' => 'boolean',
        'locked_at' => 'datetime',
    ];
}

