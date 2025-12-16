<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

/**
 * @group User registration management
 *
 * APIs for managing Authentication details and status for users
 * it contains the following endpoints:
 * - Register User
 * - Login User
 * - Logout User
 * - Email Verification for User
 * - Password Reset for User
 * 
 */



class AuthController extends Controller
{


    /**
     * Endpoint to Register USER
     *
     * This endpoint lets you create a user.
     * 
     * @unauthenticated
     */
    public function registerUser(Request $request){
        try{

            $validateUser = Validator::make($request->all(),[
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:10',
                'role' => 'nullable|in:user,admin',
                'phoneNumber' => 'required'
                
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'phone_number' => $request->phoneNumber
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User Registered Successfully',
                'token' => $user->createToken('API TOKEN')->plainTextToken
            ]);

        }catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    /**
     * Endpoint to Login USER
     *
     * This endpoint lets you log a user in.
     * @unauthenticated
     */
     public function loginUser(Request $request){
        try{

            $validateUser = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 422);
            }

            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email or password is incorrect',
                ], 401);
            }

            $user = Auth::user();

            // revoke old tokens
             //$user->tokens()->delete();

            return response()->json([
                'status' => true,
                'message' => 'User logged in successfully',
                'token' => $user->createToken('API TOKEN')->plainTextToken
            ], 200);


        }catch(\Throwable $th){
             return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

   


//     public function logoutUser(Request $request){
//     try{
//         $user = $request->user();
//         dd($request->user()->currentAccessToken());
//         // // Check if authenticated via token
//         // if ($user->currentAccessToken()) {
//         //     echo'access token', $user->currentAccessToken()->plainTextToken,'';
//         //     //$user->currentAccessToken()->delete();
//         // } else {
//         //     // Fallback: delete all tokens if none is current
//         //     $user->tokens()->delete();
//         // }
        
//         // return response()->json([
//         //     'status' => true,
//         //     'message' => 'User logged out successfully'
//         // ], 200);
//     }catch(\Throwable $th){
//         return response()->json([
//             'status' => false,
//             'message' => $th->getMessage()
//         ], 500);
//     }
// }

/**
     * Endpoint to logout USER
     *
     * This endpoint lets you create a user.
     * 
     */

public function logoutUser(Request $request) 
{
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
