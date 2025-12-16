<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Http\Requests\StoreInquiryRequest;
use App\Http\Requests\UpdateInquiryRequest;
use App\Http\Resources\InquiryCollection;
use App\Http\Resources\InquiryResource;
use App\Models\Property;

/**
 * @group User Inquiries management
 *
 * APIs for managing the user inquiries
 * it contains the following endpoints:
 * - Create Inquiry
 * - Get one Inquiry
 * - Get all inquiries for a user
 * - Get all inquiries
 * - Update Inquiry
 * - Delete Inquiry

 * 
 */

class InquiryController extends Controller
{
    /**
     * Display all the inquries
     * this is an admin only endpoint btw
     */
    public function index()
    {
         return new InquiryCollection(Inquiry::paginate(15));
    }

 
    /**
     * create a new inquiry for a property.
     * 
     * so, the inquiry takes a couple fields, and one of the fields are appointment_date
     * appointment date takes date time, but it is optional.
     * so, to create inquiries with an appointment, you can add in the appointment date
     * if you want, I can add in an endpoint for the agent to get only inquiries with appointment dates to provide a way to view the appointments
     */
    public function store(Property $property, StoreInquiryRequest $request)
    {
        $user = auth('sanctum')->user();
       $data = $request->validated();
    
    // Override user_id with authenticated user
    $data['user_id'] = $user->id; 
    
    $inquiry = $property->inquiries()->create($data);
    
    return new InquiryResource($inquiry);
    }
     

    /**
     * View one inquiry
     * 
     * still an admin only endpoint
     */
    public function show(Inquiry $inquiry)
    {
        $this->authorize('view', $inquiry);

        return new InquiryResource($inquiry);
    }

    

    /**
     * Update an inquiry
     * 
     * only the user is allowed to update the inquiry
     */
    public function update(UpdateInquiryRequest $request, Inquiry $inquiry)
    {
        $this->authorize('update', $inquiry);

        $inquiry->update($request->all());
        return new InquiryResource($inquiry);
    }

    /**
     * Delete the inquiry
     */
    public function destroy(Inquiry $inquiry)
    {

        $this->authorize('delete', $inquiry);


        $inquiry->delete();
        return response()->json(['message' => "Inquiry deleted successfully"]);
    }

    

    /**
     * Get all inquiries for a user
     * 
     * well, the user can get all their inquiries
     */
    public function getUserInquiries(){
        $inquiries = auth('sanctum')->user()->inquiries;
        return new InquiryCollection($inquiries);
    }
}
