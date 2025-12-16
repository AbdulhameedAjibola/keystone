<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    /** @use HasFactory<\Database\Factories\MediaFactory> */
    use HasFactory;

    protected $fillable = [
        'mediable_id',
        'mediable_type',
        'public_id',
        'url',
        'type',
        'format',
        'size',
    ];

    public function mediable(){
        return $this->morphTo();
    }
}
