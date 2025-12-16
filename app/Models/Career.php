<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Career extends Model
{
    /** @use HasFactory<\Database\Factories\CareersFactory> */
    use HasFactory, SoftDeletes;


    protected $fillable = [
    'user_id',
    'title',
    'location',
    'type',
    'description',
    'requirements',
    'salary',
    'is_active',
    'application_deadline',
];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
