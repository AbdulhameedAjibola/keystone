<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use Illuminate\Support\Str;
use Cloudinary\Api\Upload;
use App\Http\Requests\StoreAgentRequest;
use App\Http\Requests\UpdateAgentRequest;
use App\Http\Requests\UploadAgentVerificationRequest;

class AgentController extends Controller
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
    public function store(StoreAgentRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Agent $agent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Agent $agent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAgentRequest $request, Agent $agent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agent $agent)
    {
        //
    }

    //start agent verification process
    public function startVerification(Agent $agent)
    {
        $verificationCode = Str::random(11);
        $agent->verification_code = $verificationCode;
        $agent->save();
    }

    //upload verification documents
    public function uploadVerificationDocuments(UploadAgentVerificationRequest $request, Agent $agent)
    {
        // add authorization: only logged in agent can upload their documents
        $file = $request->file('file');

        $result = storeOnCloudinary($file, 'agent_verifications');

        $agent->media()->create([
             'public_id' => $result['public_id'],
        'url'       => $result['secure_url'],
        'type'      => 'image',
        'format'    => $result['format'],
        'size'      => $result['bytes'],
        'collection'=> 'agent_verifications',
        ]);

        return response()->json(['message' => 'Verification document uploaded successfully.'], 200);

    }
}
