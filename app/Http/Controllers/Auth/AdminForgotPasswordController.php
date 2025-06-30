<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Smtp2GoMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class AdminForgotPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showLinkRequestForm()
    {
        return view('auth.admindash.passwords.email');
    }

    protected function validateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email,active,1'
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.exists' => 'We couldn\'t find an active account with this email.'
        ]);
    }
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        $user = User::where('email', $request->email)->where('active', 1)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No user found with this email address.']);
        }

        /* Generate token */
        $token = Password::broker('users')->createToken($user);
        /* Build reset URL */
        $resetUrl = route('smarttiusadmin.password.reset', ['token' => $token]) . '?email=' . urlencode($user->email);

        // Send email manually using your Mailable
        /* Mail::to($user->email)->send(new UserPasswordResetMail($resetUrl)); */

        /** Send email using SMTP2Go */
        Smtp2GoMail::to($user->email)->subject('Reset Your Password')->view('emails.user_password_reset', ['resetUrl' => $resetUrl])->send();

        return back()->with('status', 'We have emailed your password reset link!');
    }
}
