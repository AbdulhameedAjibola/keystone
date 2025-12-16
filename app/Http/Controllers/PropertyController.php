<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Http\Requests\UploadPropertyMediaRequest;


class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    // /**
    //  * Show the form for creating a new resource.
    //  */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePropertyRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        //
    }

    // /**
    //  * Show the form for editing the specified resource.
    //  */
    // public function edit(Property $property)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePropertyRequest $request, Property $property)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        //
    }

    //property media upload
    public function uploadMedia(UploadPropertyMediaRequest $request, Property $property)
    {
        // add authorization: only the agent who owns the property can upload media

        $file = $request->file('file');

        $result = storeOnCloudinary($file, 'property_media');
        // $result = cloudinary()->upload($file->getRealPath(), [ --- IGNORE ---
        //     'folder' => "property_media/{$property->id}" --- IGNORE ---
        // ]); --- IGNORE ---

        $property->media()->create([
             'public_id' => $result['public_id'],
             'url' => $result['secure_url'],
             'type' => $request->input('type'),
             'format' => $result['format'],
             'size' => $result['bytes'],
             'collection' => 'property_media',
        ]);

        return response()->json(['message' => 'Media uploaded successfully'], 201);
    }
}
