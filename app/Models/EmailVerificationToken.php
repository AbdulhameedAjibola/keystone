<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailVerificationToken extends Model
{
     protected $fillable = [
        'guard',
        'email',
        'token',
        'expires_at',
    ];
}
