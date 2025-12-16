<?php

namespace App\Http\Controllers;



use App\Http\Controllers\Controller;
use App\Jobs\SendResetOTPJob;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserForgotPasswordController extends Controller
{
    protected int $otpDurationMinutes = 10;
    protected string $guard = 'web';

    /**
     * Send OTP to user's email
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        $otpCode   = Str::padLeft(random_int(100000, 999999), 6, '0');
        $expiresAt = now()->addMinutes($this->otpDurationMinutes);

        // Remove existing tokens for this email + guard
        PasswordResetToken::where('email', $user->email)
            ->where('guard', $this->guard)
            ->delete();

        PasswordResetToken::create([
            'email'      => $user->email,
            'guard'      => $this->guard,
            'token'      => Hash::make($otpCode), // consider hashing later
            'expires_at' => $expiresAt,
        ]);

        SendResetOTPJob::dispatch($user->email, $otpCode);

        return response()->json([
            'message' => 'Password reset token has been sent to your email.',
        ]);
    }

    /**
     * Reset user's password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users,email',
            'token'    => 'required|digits:6',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        $otpRecord = PasswordResetToken::where('email', $user->email)
            ->where('guard', $this->guard)
            ->where('token', $request->token)
            ->latest()
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'message' => 'OTP verification failed: Invalid code.',
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

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        $otpRecord->delete();

        return response()->json([
            'message' => 'Password has been successfully reset. You can now log in.',
        ]);
    }
}
