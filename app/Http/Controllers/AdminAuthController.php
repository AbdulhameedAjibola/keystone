<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * @group Admin Auth Endpoints
 *
 * APIs for managing Authentication for admins
 * it contains the following endpoints:
 * - Register Admin
 * - Login Admin
 * - Logout Admin
 
 * 
 */

class AdminAuthController extends Controller
{

    /**
     * Endpoint to Register Admin
     *
     * This endpoint lets you create an admin.
     *
     * Endpoint to Register Agent
     * @bodyParam name string required The name of the user. 
     * @bodyParam email: the admin email
     * @bodyParam password: the admin password
     * @bodyParam role: the admin role. only accept admin
     * @bodyParam phoneNumber: the admin phone number
     * 
     * @unauthenticated
     */
    public function registerAdmin(Request $request){
        try{


            $request->validate([
                "name"=> "required",
                'email'=> 'required',
                'password'=> 'required|min:10',
                'role'=> 'required|in:admin',
                'phoneNumber' => 'required'
            ]);

             $admin = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'phone_number' => $request->phoneNumber
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Admin Registered Successfully',
                'token' => $admin->createToken(
                'API TOKEN',
                ['*'],
                now()->addDay()
                )->plainTextToken,
                'admin' => $admin
            ]);
        }
        catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

     /**
     * Endpoint to Login Admin
     *
     * This endpoint lets you login an admin.
     * 
     * @bodyParam email: the admin email
     * @bodyParam password: the admin password
     * @unauthenticated
     */

     public function loginAdmin(Request $request){
        try{

            $request->validate([
                'email'=> 'required|email|exists:users,email',
                'password'=> 'required',
                
            ]);

            $admin = User::where('email', $request->email)
             ->where('role', 'admin')
             ->first();


            if (!$admin || !Hash::check($request->password, $admin->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password do not match our records.',
                ], 401);
            }

            $token = $admin->createToken(
                'API TOKEN',
                ['*'],
                now()->addDay()
                )->plainTextToken;

            return response()->json([
                'status' => true,
                'token' => $token,
            ]);
        }
        catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


     /**
     * Endpoint to Logout Admin
     *
     * This endpoint lets you logout for an admin.
     * it is a post request, but it has no body, just ensure the request is authenticated using the token from the login reguest
     * 
     */
     public function logoutAdmin(Request $request){
        try{

             $request->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
        ]);


   
    } catch(\Throwable $th){
        return response()->json([
            'status' => false,
            'message' => $th->getMessage()
        ], 500);
    }

}


      
}