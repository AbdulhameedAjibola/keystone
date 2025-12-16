<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agent;


class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    //get all unverified agents
    public function getUnverifiedAgents(){
        Agent::where('status', 'pending')->get();   
    }
    //get all verified agents
    public function getVerifiedAgents(){
        Agent::where('status', 'approved')->get();   
    }
    //get all rejected agents
    public function getRejectedAgents(){
        Agent::where('status', 'rejected')->get();   
    }
    public function verifyAgent(Agent $agent){
        $agent = Agent::find($agent->id);
        if(!$agent){
            return response()->json(['message'=>'Agent not found'],404);
        }
        $agent->status = 'approved';
        $agent->save();
        return response()->json(['message'=>'Agent verified successfully'],200);
    }
}
