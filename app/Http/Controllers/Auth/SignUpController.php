<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Admin\SDCOptionController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\User\ShippingAddress;
use App\Models\UserMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SignUpController extends Controller
{
    public function __construct()
    {
        /** Auth Check if the user is logged in then logout first */
        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                Auth::logout();
                /* Clear any session data if needed */
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('register');
            }
            return $next($request);
        });
    }
    /** Form View */
    public function index()
    {
        /** Return Form blade */
        return view('auth.userdash.signup');
    }

    /** Register */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'first_name.required' => 'Please enter your first name.',
            'first_name.string' => 'First name must be a valid string.',
            'first_name.max' => 'First name may not be longer than 255 characters.',

            'last_name.required' => 'Please enter your last name.',
            'last_name.string' => 'Last name must be a valid string.',
            'last_name.max' => 'Last name may not be longer than 255 characters.',

            'email.required' => 'Please enter your email address.',
            'email.string' => 'Email must be a valid string.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email address may not be longer than 255 characters.',
            'email.unique' => 'This email is already registered. Please use a different one.',

            'password.required' => 'Please enter a password.',
            'password.string' => 'Password must be a valid string.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Passwords do not match. Please confirm your password.',
        ]);
        $newUserRole = SDCOptionController::getOption("register_user_role");
        $user =  User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'full_name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $newUserRole,
        ]);
        if ($user->wasRecentlyCreated) {
            $userMetaData = UserMeta::create([
                'user_id' => $user->id,
                'meta_key' => 'subscriber',
                'meta_value' => 'yes',
                'service_provider_id' => 1,
            ]);
            $shippingData = ShippingAddress::create([
                'user_id' => $user->id,
                'street_address' => null,
                'address_line_2' => null,
                'city' => null,
                'state' => null,
                'zip' => null,
                'country' => null,
            ]);
            if ($userMetaData->wasRecentlyCreated && $shippingData->wasRecentlyCreated) {
                return redirect()->route('sdcsmuser.login.index')->with('status', 'Thanks for signing up! You’re all set to log in and get started.');
            } else {
                return redirect()->route('register');
            }
        } else {
            return redirect()->route('register');
        }
    }
}
