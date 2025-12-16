<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\SendResetOTPJob;
use App\Models\Agent;
use App\Models\PasswordResetToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AgentForgotPasswordController extends Controller
{
    protected int $otpDurationMinutes = 10;
    protected string $guard = 'agent';

    /**
     * Send OTP to agent's email
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:agents,email',
        ]);

        $agent = Agent::where('email', $request->email)->first();

        $otpCode   = Str::padLeft(random_int(100000, 999999), 6, '0');
        $expiresAt = now()->addMinutes($this->otpDurationMinutes);

        PasswordResetToken::where('email', $agent->email)
            ->where('guard', $this->guard)
            ->delete();

        PasswordResetToken::create([
            'email'      => $agent->email,
            'guard'      => $this->guard,
            'token'      => Hash::make($otpCode),
            'expires_at' => $expiresAt,
        ]);

        SendResetOTPJob::dispatch($agent->email, $otpCode);

        return response()->json([
            'message' => 'Password reset token has been sent to your email.',
        ]);

        
    }

    /**
     * Reset agent password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:agents,email',
            'token'    => 'required|digits:6',
            'password' => 'required|string|min:8',
        ]);

        $agent = Agent::where('email', $request->email)->first();

        $otpRecord = PasswordResetToken::where('email', $agent->email)
            ->where('guard', $this->guard)
            ->latest()
            ->first();

        if (!$otpRecord) {
                return response()->json([
                    'message' => 'OTP verification failed: No reset request found.',
                ], 401);
            }

            if ($otpRecord->expires_at->isPast()) {
                $otpRecord->delete();
                return response()->json([
                    'message' => 'OTP verification failed: Code has expired.',
                ], 401);
            }

            if (!Hash::check($request->token, $otpRecord->token)) {
                return response()->json([
                    'message' => 'OTP verification failed: Invalid code.',
                ], 401);
            }

        $agent->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        $otpRecord->delete();

        return response()->json([
            'message' => 'Password has been successfully reset. You can now log in.',
        ]);
    }
}
