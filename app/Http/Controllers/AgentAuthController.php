<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

/**
 * @group Agent registration management
 *
 * APIs for managing Authentication for agents
 * it contains the following endpoints:
 * - Register Agent
 * - Login Agent
 * - Logout Agent
 * - Email Verification for Agent
 * - Password Reset for Agent
 * 
 */

class AgentAuthController extends Controller
{

    /**
     * Endpoint to Register Agent
     *@bodyParam name string required The name of the user. Example: 9
    * @bodyParam email string The email of the user. 
    * @bodyParam password string The password of the user. minimum 10 characters
    * @bodyParam phoneNumber string The phone number of the user. Example: 1234567890
     * @bodyParam address string The address of the user. Example: 123 Main St
     * @bodyParam city string The city of the user. Example: New York
     * @bodyParam state string The state of the user. Example: New York
     * @unauthenticated
     */
    public function registerAgent(Request $request){
        try{

            $validateUser = Validator::make($request->all(),[
                'name' => 'required',
                'email' => 'required|email|unique:agents,email',
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
                'token' => $agent->createToken(
                    'API TOKEN',
                    ['*'],
                    now()->addDays(7)
                    )->plainTextToken,
                'agent' => $agent
            ]);

        }catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

     /**
     * Endpoint to login Agent
     *
     * 
     *  @bodyParam email string The email of the user.
    * @bodyParam password string The password of the user. minimum 10 characters
     * This endpoint lets you login for an agent.
     * @unauthenticated
     */
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
                    $agent->createToken(
                    'API TOKEN',
                    ['*'],
                    now()->addDays(7)
                    )->plainTextToken,
                    'agent' => $agent
                ], 200);
            


        }catch(\Throwable $th){
             return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


     /**
     * Endpoint to logout Agent
     *
     * This endpoint lets you logout an agent.
     * it is a post request, but it has no body, just ensure the request is authenticated using the token from the login reguest
     * @authenticated
     */
 public function logoutAgent(Request $request){
     try {
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $token = $request->user()->currentAccessToken();

        // Ensure we actually have a token instance
        if ($token && method_exists($token, 'delete')) {
            $token->delete();
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Token not found or already invalidated.'
            ], 401);
        }

        return response()->json([
            'status' => true,
            'message' => 'User logged out successfully'
        ], 200);

    } catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => 'Logout failed: ' . $th->getMessage()
        ], 500);
    }
}

}
