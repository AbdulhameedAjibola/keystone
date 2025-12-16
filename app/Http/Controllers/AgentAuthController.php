<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AgentAuthController extends Controller
{
    public function registerAgent(Request $request){
        try{

            $validateUser = Validator::make($request->all(),[
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:10',
                'phoneNumber' => 'required',
                'address' => 'required',
                'state' => 'required',
                'city' => 'required',
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $agent = Agent::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phoneNumber,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Agent Registered Successfully',
                'token' => $agent->createToken('API TOKEN')->plainTextToken
            ]);

        }catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

     public function loginAgent(Request $request){
        try{

             $validateUser = Validator::make($request->all(),[
               
                'email' => 'required|email|exists:agents,email',
                'password' => 'required',
                //'role' => 'required|in:user,admin|default:user',
            ]);

             if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

           $agent = Agent::where('email', $request->email)->first();

            if(!$agent || !Hash::check($request->password, $agent->password)){
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
                
            }
            $token = $agent->createToken('API TOKEN')->plainTextToken;
                return response()->json([
                    'status' => true,
                    'message' => "Agent logged in successfully",
                    'token' => $token
                ], 200);
            


        }catch(\Throwable $th){
             return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


 public function logoutAgent(Request $request){
    try{
        $user = $request->user();
        
        // Check if authenticated via token
        if ($user->currentAccessToken()) {
            echo'access token', $user->currentAccessToken()->plainTextToken,'';
           // $user->currentAccessToken()->delete();
        } else {
            // Fallback: delete all tokens if none is current
            $user->tokens()->delete();
        }
        
        return response()->json([
            'status' => true,
            'message' => 'User logged out successfully'
        ], 200);
    }catch(\Throwable $th){
        return response()->json([
            'status' => false,
            'message' => $th->getMessage()
        ], 500);
    }
}

}
