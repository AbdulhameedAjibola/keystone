<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;




class AuthController extends Controller
{
    
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

     public function loginUser(Request $request){
        try{

             $validateUser = Validator::make($request->all(),[
               
                'email' => 'required|email|exists:users,email',
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

            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }


            $user = User::where('email', $request->email)->first();
            
                return response()->json([
                    'status' => true,
                    'message' => "User logged in successfully",
                    'token' => $user->createToken('API TOKEN')->plainTextToken
                ], 200);
            


        }catch(\Throwable $th){
             return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function logoutUser(Request $request){
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
