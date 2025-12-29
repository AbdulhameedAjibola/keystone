<?php

namespace App\Http\Controllers;

use App\Http\Resources\InquiryCollection;
use App\Http\Resources\MediaCollection;
use App\Http\Resources\MediaResource;
use App\Models\Property;
use App\Services\PropertyQuery;
use Illuminate\Http\Request;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Http\Resources\PropertyResource;
use App\Http\Resources\PropertyCollection;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Http\Requests\UploadPropertyMediaRequest;
use App\Models\Agent;

/**
 * @group Property management
 *
 * Endpoints for managing Properties
 * 
 * @subgroup Publicly Available Endpoints
 * @subgroupDescription These endpoints are available to all users
 * 
 * @subgroup Agent and admin only Endpoints
 * @subgroupDescription These endpoints are available to admins and agents only
 * 
 * @authenticated
 */


class PropertyController extends Controller
{
    /**
     * @subgroup Publicly Available Endpoints
     * Display all properties
     * 
     * @queryparam sortBy string The column to sort by. Optional. default: created_at
     * @queryparam sortDirection string The direction to sort. default: asc, options: asc, desc
     * 
     * the following query parameters can be used to filter the results. you can either combine, i guess, or just use one
     * it takes key value pairs, the key is the column name and the value is the operator to filter by
     * accepted operators are eq, gt, gte, lt, lte
     * @queryparam price number The price to filter by. Optional.
     * @queryparam city string The city to filter by. Optional.
     * @queryparam propertyType string The property type to filter by. Optional. takes either: 'apartment', 'house', 'shortlet', 'penthouse', 'land', 'commercial'
     * @queryparam status string The status to filter by. Optional. takes either: 'available', 'sold', 'unavailable'
     * @queryparam listingType string The listing type to filter by. Optional. takes either: 'sale', 'rent'
     * @queryparam bedrooms number The number of bedrooms to filter by. Optional.
     * 
     * 
     * @unauthenticated
     */
    public function index(Request $request)
    {

        $filter = new PropertyQuery();
        $queryItems =  $filter->transform($request);

        $query = Property::query();

         if(count($queryItems) > 0){
            $query->where($queryItems);
           
        }

        if($request->has('sortBy')){
            $sortColumn = $request->get('sortBy', 'created_at');
            $sortDirection = $request->get('sortDirection', 'asc');

            $query->orderBy($sortColumn, $sortDirection);
        }

        return new PropertyCollection(Property::with('media')->where($queryItems)->paginate(15));
    }

    /**
     * @subgroup agent only endpoints
     * Endpoint to create a property
     *
     * This endpoint lets an agent create a property
     * this endpoint handles only property creation
     * there is a separate endpoint to upload media for properties
     * however, be rest assured, so far the authenticated agent is the owner of the property, 
     * the media will be linked to the property
     * 
     * 
     */
   

    
    public function store(StorePropertyRequest $request)
    {
        $agent = request()->user();

        $property = $agent->properties()->create($request->all());
        return new PropertyResource($property);
    }

    /**
     *  @subgroup Publicly Available Endpoints
     * Get one property
     * 
     * This is to get one property, this endpoint automatically loads the media for the property
     * just run the request with the property id
     * @unauthenticated
     */

    public function show(Property $property)
    {
        $property->load('media');

    return new PropertyResource($property);

    }

    /**
     *  @subgroup Admin and Agent property management
     * Update a property
     * 
     * This will work with either a put or patch request
     * 
     */
    
    public function update(UpdatePropertyRequest $request, Property $property)
    {
        $this->authorize("update", $property);

        $property->update($request->all());
        return new PropertyResource($property);
    }

    /**
     *  @subgroup Admin and Agent property management
     * delete a property
     * 
     * Delete the specified property
     * takes property Id
     * 
     */
    public function destroy(Property $property)
    {

        $this->authorize("delete", $property);

        $property->delete();
        return response()->json(['message' => 'Property deleted Successfully']);

    }

     /**
     *  
     * Search for a property
     * 
     * @queryparam search string The search term, the search is done on the title of the property, 
     * 
     * @queryparam column string The column to search on. Optional. default: title
     * if you have other columns that you want to search on, you can add them as query parameters
     * allowed columns are: title, name, location, description
     * 
     * 
     * @unauthenticated
     */
     public function searchProperties(Request $request){

        $allowedColumns = ['title', 'name', 'location', 'description'];

        $column = $request->input('column', 'title');

      if (!in_array($column, $allowedColumns)) {
            $column = 'title';
        }

        $search = $request->get('search');
        

        //add more where clauses as required
        $property = Property::where($column, 'LIKE', '%'. $search .'%')->paginate(5);
        return PropertyResource::collection($property);
    }

    /**
     *  @subgroup Agent property management
     * Upload media for a property
     * 
     * This endpoint takes the property id as a parameter
     * Then it takes a file input to be uploaded to cloudinary
     * 
     * 
     */
    public function uploadMedia(UploadPropertyMediaRequest $request, Property $property)
    {

        $this->authorize('update', $property);

        Configuration::instance([
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key'    => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
            ],
            'url' => [
                'secure' => true
            ]
        ]);

        $files = $request->file('files');
        $uploadedMedia = [];

        $fileArray = is_array($files) ? $files : [$files];

        foreach ($fileArray as $file) {
             $result = (new UploadApi())->upload($file->getRealPath(), [
                'context' => ['verify' => config('app.env') === 'local' ? false : true],
                'folder' => "properties/{$property->id}"
            ]);


            // $result = Cloudinary::upload($file->getRealPath(), [
            //     'folder' => "properties/{$property->id}"
            // ]);

             $media = $property->media()->create([
             'public_id' => $result['public_id'],
             'url' => $result['secure_url'],
             'type' => $request->input('type'),
             'format' => $result['format'],
             'size' => $result['bytes'],
             'collection' => 'property_media',
            ]);

            $uploadedMedia[] = $media;

        }

       // dd(env('CLOUDINARY_SECRET'), config('cloudinary.api_secret'));
    
       return response()->json([
        'message' => count($uploadedMedia) . ' file(s) uploaded successfully',
        'data' => $uploadedMedia
        ], 201);
    }

    /**
     *  @subgroup Admin and Agent property management
     * get all inquries for a property
     * 
     * Get all the user inquries for that property
     * takes property Id
     * 
     */

        public function getPropertyInquiries(Property $property)
    {
        return new InquiryCollection($property->inquiries);
    }   
    
    

   
}
