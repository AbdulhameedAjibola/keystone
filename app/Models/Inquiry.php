<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    /** @use HasFactory<\Database\Factories\InquiryFactory> */
    use HasFactory;

     public function user(){
        return $this->belongsTo(User::class);
    }
    public function property(){
        return $this->belongsTo(Property::class);
    }
}
