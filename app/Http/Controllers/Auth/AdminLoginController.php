<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin\ServiceProvider;
use App\Models\LoginLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AdminLoginController extends Controller
{
    /** Private function to create login logs */
    private function successfulLogin($request)
    {
        LoginLog::create([
            'ip_address' => $request->ip(),
            'browser' => $request->header('User-Agent'),
            // 'id_type' => $fieldType,
            'panel' => 'Admin',
            'user_email' => $request->email,
            'user_id' => Auth::id(),
            'date' => Carbon::now(),
            'success' => 1,
            'password_attempt' => null,
        ]);
    }

    private function failedLogin($request)
    {

        LoginLog::create([
            'ip_address' => $request->ip(),
            'browser' => $request->header('User-Agent'),
            // 'id_type' => $fieldType,
            'panel' => 'Admin',
            'user_email' => $request->email,
            'user_id' => null,
            'date' => Carbon::now(),
            'success' => 0,
            'password_attempt' => $request->password,
        ]);
    }

    /** Admin Login  Index*/
    public function adminLoginIndex()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }else {
            return view('auth.admindash.adminlogin');

        }
    }
    /** Admin Login */

    public function adminLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $key = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            return redirect()->back()->with('lockout_seconds', $seconds)->withInput($request->only('email'));
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            RateLimiter::clear($key);
            $request->session()->regenerate();

            $user = Auth::user();
            $service_provider = ServiceProvider::select('id', 'active')->where('active', 1)->first();

            if (empty($service_provider)) {
                Auth::logout();
                self::failedLogin($request);
                RateLimiter::hit($key, 15);  /* Hit here as well on failure */
                return redirect()->route('error')->with('error', 'Service temporarily unavailable');
            }

            $activeUser = User::where('active', 1)->where('email', $user->email)->first();
            if (empty($activeUser) || $activeUser->email !== $user->email) {
                Auth::logout();
                self::failedLogin($request);
                RateLimiter::hit($key, 15);
                return redirect()->back()->withErrors(['email' => 'Invalid login credentials.'])->withInput($request->only('email'));
            }

            if (!$activeUser->hasServiceProviderAccess()) {
                Auth::logout();
                self::failedLogin($request);
                RateLimiter::hit($key, 15);
                return redirect()->route('error')->with('error', 'You do not have access to this service provider');
            }

            if ($activeUser->hasRole(1)) {
                self::successfulLogin($request);
                return redirect()->route('admin.dashboard');
            } else {
                Auth::logout();
                self::failedLogin($request);
                RateLimiter::hit($key, 15);
                return redirect()->back()->withErrors(['email' => 'Invalid login credentials'])->withInput($request->only('email'));
            }
        } else {
            RateLimiter::hit($key, 15);  /* Always hit rate limiter on any failed attempt */
            self::failedLogin($request);
            return redirect()->back()->withErrors(['email' => 'Invalid login credentials'])->withInput($request->only('email'));
        }
    }

    /** Admin Logout */
    public function adminLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['msg' => 'Logged out successfully', 'redirect_at' => route('smarttiusadmin.login.index')], 200);
    }
}
