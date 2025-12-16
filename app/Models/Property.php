<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    /** @use HasFactory
     * <\Database\Factories\PropertyFactory> */
    use HasFactory;


    protected $fillable = [
        'agent_id',
        'title',
        'description',
        'price',
        'property_type',
        'listing_type',
        'bedrooms',
        'bathrooms',
        'size',
        'address',
        'city',
        'state',
        'status',
    ];

    //relationship between properties and agents
    public function agent(){
        return $this->belongsTo(Agent::class);
    }

    //relationship between properties and media
    public function media(){
        return $this->morphMany('App\Models\Media', 'mediable');
    }

        public function images()
    {
        return $this->media()->where('type', 'image');
    }

    public function videos()
    {
        return $this->media()->where('type', 'video');
    }

    public function inquiries(){
        return $this->hasMany(Inquiry::class);
    }
}
