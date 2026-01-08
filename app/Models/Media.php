<?php

namespace App\Models;

use App\Observers\MediaObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


#[ObservedBy([MediaObserver::class])]
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
        'collection'
    ];

    public function mediable(){
        return $this->morphTo();
    }
}
