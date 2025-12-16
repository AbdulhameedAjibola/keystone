<?php

namespace App\Http\Controllers;



use App\Http\Controllers\Controller;
use App\Jobs\SendEmailVerificationOTPJob;
use App\Models\Agent;
use App\Models\EmailVerificationToken;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
class AgentEmailVerificationController extends Controller
{
    protected string $guard = 'agent';
    protected int $expiryMinutes = 15;

    /**
     * Send Email Verification OTP to Agent Email
     *
     * This endpoint send Email Verification OTP.
     * @unauthenticated
     */

    public function sendVerificationOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:agents,email',
        ]);

        $agent = Agent::where('email', $request->email)->first();

        if ($agent->email_verified_at) {
            return response()->json([
                'message' => 'Email already verified.',
            ]);
        }

        $token = Str::padLeft(random_int(100000, 999999), 6, '0');

        EmailVerificationToken::where('email', $agent->email)
            ->where('guard', $this->guard)
            ->delete();

        EmailVerificationToken::create([
            'email'      => $agent->email,
            'guard'      => $this->guard,
            'token'      => Hash::make($token),
            'expires_at' => now()->addMinutes($this->expiryMinutes),
        ]);

        SendEmailVerificationOTPJob::dispatch($agent->email, $token);

        return response()->json([
            'message' => 'Verification code sent to email.',
        ]);
    }

    /**
     * Send Password Reset OTP to Agent Email
     *
     * This endpoint lets you verify the agent's email using the OTP sent.
     * @unautheticated
     */

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:agents,email',
            'token' => 'required|digits:6',
        ]);

        $record = EmailVerificationToken::where('email', $request->email)
            ->where('guard', $this->guard)
            ->where('token', $request->token)
            ->first();
        
        //if this doesnt work, change model to use datetime and use carbon
        if (!$record || $record->expires_at < now()) {
            return response()->json([
                'message' => 'Invalid or expired verification code.',
            ], 401);
        }

         if (!Hash::check($request->token, $record->token)) {
                return response()->json([
                    'message' => 'OTP verification failed: Invalid code.',
                ], 401);
            }

        Agent::where('email', $request->email)
            ->update(['email_verified_at' => now()]);

        $record->delete();

        return response()->json([
            'message' => 'Email successfully verified.',
        ]);
    }
}
