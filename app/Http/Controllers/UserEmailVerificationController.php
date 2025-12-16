<?php

namespace App\Http\Controllers;



use App\Http\Controllers\Controller;
use App\Jobs\SendEmailVerificationOTPJob;
use App\Models\EmailVerificationToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;


class UserEmailVerificationController extends Controller
{
    protected string $guard = 'web';
    protected int $expiryMinutes = 15;

    /**
     * Send verification OTP
     */
    public function sendVerificationOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Email already verified.',
            ]);
        }

        $token = Str::padLeft(random_int(100000, 999999), 6, '0');

        EmailVerificationToken::where('email', $user->email)
            ->where('guard', $this->guard)
            ->delete();

        EmailVerificationToken::create([
            'email'      => $user->email,
            'guard'      => $this->guard,
            'token'      => Hash::make($token),
            'expires_at' => now()->addMinutes($this->expiryMinutes),
        ]);

        SendEmailVerificationOTPJob::dispatch($user->email, $token);

        return response()->json([
            'message' => 'Verification code sent to email.',
        ]);
    }

    /**
     * Verify email
     */
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|digits:6',
        ]);

        $record = EmailVerificationToken::where('email', $request->email)
            ->where('guard', $this->guard)
            ->where('token', $request->token)
            ->first();

        if (!$record || $record->expires_at->isPast()) {
            return response()->json([
                'message' => 'Invalid or expired verification code.',
            ], 401);
        }
         if (!Hash::check($request->token, $record->token)) {
                return response()->json([
                    'message' => 'OTP verification failed: Invalid code.',
                ], 401);
            }

        User::where('email', $request->email)
            ->update(['email_verified_at' => now()]);

        $record->delete();

        return response()->json([
            'message' => 'Email successfully verified.',
        ]);
    }
}

