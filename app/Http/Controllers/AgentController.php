<?php

namespace App\Http\Controllers;


use App\Http\Resources\PropertyCollection;
use App\Models\Agent;
use App\Models\Property;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Support\Str;
use App\Http\Requests\StoreAgentRequest;
use App\Http\Requests\UpdateAgentRequest;
use App\Http\Requests\UploadAgentVerificationRequest;
use App\Http\Resources\AgentCollection;
use App\Http\Resources\AgentResource;
use App\Models\Inquiry;
use App\Models\User;

/**
 * @group Agent Management
 *
 * APIs for managing Everything concerning agents
 * 
 * right now i have boken them into subgroups, i.e for admins, for agents etc
 * it contains the following endpoints:
 * - Get all agents and their properties
 * - Get one agent
 * - Create Agent
 * - Update Agent
 * - Delete Agent
 * - Start Agent Verification
 * - Verify Agent
 * - Upload Agent Verification Document
 * - Get Agent Properties
 * - Get Unverified Agents
 * - Get Verified Agents
 * - Get Rejected Agents

 * 
 */


class AgentController extends Controller
{
    /**
     *  @subgroup Admin Agent Management
     * @subgroupDescription These endpoints are available to admins only to manage agents 
     * get all agents 
     * 
     * this route returns all agents, their properties, and the corresponding media for those properties
     */
    public function index()
    {
        return new AgentCollection((Agent::with(['properties.media'])->paginate(15)));
    }

  
    /**
     * Create a new agent
     * 
     * when creating agents, dont pass status, it is sent to pending as default, and is updated once agent has completed verification
     */
    public function store(StoreAgentRequest $request)
    {
        return new AgentResource(Agent::create($request->all()));
    }

    /**
     * Get one agent
     */
    public function show(Agent $agent)
    {
        return new AgentResource($agent);
    }

    

    /**
     * Update agent details
     */
    public function update(UpdateAgentRequest $request, Agent $agent)
    {
        $this->authorize("update", $agent);

        $agent->update($request->all());
        return new AgentResource($agent);
    }

    /**
     * Delete agent
     */
    public function destroy(Agent $agent)
    {
        $agent->delete();
    }

    /**
     * Start the agent verification process
     * 
     * this route basically generates an 11 digit alpha numeric code and is sent as a prt of the request
     * it is stored in the db, and can be displayed on the frontend along with further verification isntructions
     * i.e a picture of head and torso holding a paper with your full name and your verification code written on a sheet of paper
     */
    public function startVerification(Agent $agent)
    {
        $this->authorize('startVerification', $agent);


        $verificationCode = Str::random(11);
        $agent->verification_code = $verificationCode;
        $agent->save();
    }


     /**
     * Upload Verification image
     * 
     * this takes a file and upload to cloudinary
     * hope it works
     */
    public function uploadVerificationDocument(UploadAgentVerificationRequest $request, Agent $agent)
    {
        $this->authorize('update', $agent);

        
        $file = $request->file('file');

        $result = (new UploadApi())->upload($file->getRealPath(), [
            'folder' => "agent_verifications/{$agent->id}"
        ]);

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

    /**
     * Agent Dashboard Endpoint
     * 
     * this is an endpoint for the agent to get their dashboard data
     * currently it returns the number of properties, properties with inquiries, and total inquiries
     */
    public function agentDashboard(){
        $agent = auth('api-agent')->user();
        
        $properties = count($agent->properties()->get());
        $propertiesWithInquiries = count($agent->properties()->whereHas('inquiries')->get());
        $totalInquiries = Inquiry::whereHas('property', 
        function ($q) use ($agent) {
            $q->where('agent_id', $agent->id);
        })->count();

        return response()->json([
            'properties'=>$properties,
            'propertiesWithInquiries'=>$propertiesWithInquiries,
            
            'totalInquiries'=>$totalInquiries
        ]);
    }

    /**
     * @subgroup Admin Agent Management
     * @subgroupDescription These endpoints are available to admins only to manage agents 
     * 
     * this is an endpoint to get agents properties
     * it's a bit redundant and I might remove it, since the get all agents endpoint already returns the properties
     * i'm lazy to do that now
     */
        public function getAgentProperties()
    {
        $agent = auth('api-agent')->user();

        
        $properties = $agent->properties()->get();

        
        

        return response()->json([
            'properties' => new PropertyCollection($properties),
            
        ]);
    }


    //ADMIN CONTROLLER FUNCTIONS FOR AGENTS

     /**
     * @subgroup Admin Agent Management
     * 
     * this is an endpoint to get all unverified agents
     * 
     */
    public function getUnverifiedAgents(){
        $unverifiedAgents = Agent::where('status', 'pending')->with('media')->paginate(15);
        return new AgentCollection($unverifiedAgents);
    }

     /**
     * @subgroup Admin Agent Management
     * 
     * this is an endpoint to get all verified agents
     * 
     */
    public function getVerifiedAgents(){
        return new AgentCollection(Agent::where('status', 'approved')->get());
           
    }

    /**
     * @subgroup Admin Agent Management
     * 
     * this is an endpoint to get all rejected agents
     * 
     */
    public function getRejectedAgents(){
        return new AgentCollection(Agent::where('status', 'rejected')->get());   
    }

    /**
     * @subgroup Admin Agent Management
     * Get all agents with properties
     * 
     * this is an endpoint to get all verified agents with properties and their properties
     * might seem redundant, but the purpose is to only get agents with properties
     * 
     */
    public function agentsWithProperties(){
        $agents = Agent::has('properties')::with('properties')->get();
        return new AgentCollection($agents);
    }


    /**
     * @subgroup Admin Agent Management
     * Verify Agent
     * 
     * this is an endpoint for the admin to verify agent
     * it takes in the agent id
     * 
     */
    public function verifyAgent(Agent $agent){
        $agent = Agent::find($agent->id);
        if(!$agent){
            return response()->json(['message'=>'Agent not found'],404);
        }
        $agent->status = 'approved';
        $agent->save();
        return response()->json(['message'=>'Agent verified successfully'],200);
    }


    /**
     * @subgroup Admin Agent Management
     * 
     * this endpoint is basically a dashboard for the admin
     * it returns the total number of agents, properties, inquiries, and users
     * 
     */

    public function adminSummary(){
        $pending = Agent::where('status', 'pending')->get()->count();
        $approved = Agent::where('status', 'approved')->get()->count();
        $rejected = Agent::where('status', 'rejected')->get()->count();
        $properties = Property::all()->count();
        $inquiries = Inquiry::all()->count();
        $users = User::where('role', 'user')->get()->count();
        return response()->json([
            'pending'=>$pending,
            'approved'=>$approved,
            'rejected'=>$rejected,
            'totalProperties'=>$properties,
            'totalInquiries'=>$inquiries,
            'totalUsers'=>$users
        ]);

    }

    
}
