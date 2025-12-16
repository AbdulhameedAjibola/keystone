<?php

namespace App\Models;

use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;


class Agent extends Model
{
    /** @use HasFactory<\Database\Factories\AgentFactory> */
    use HasFactory, HasApiTokens;


    protected $fillable= [
        'name',
        'email',
        'password',
        'role',
        'verification_code',
        'verification_document_url',
        'status',
        'phone_number',
        'address',
        'city',
        'state',
    ];
        public function properties(){
            return $this->hasMany(Property::class);
        }

         public function media(){
        return $this->morphMany('App\Models\Media', 'mediable');
    }

        public function verificationMedia()
    {
        return $this->media()->where('collection', 'agent_verifications');
    }
}
