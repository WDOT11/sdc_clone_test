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

class UserLoginController extends Controller
{
    /** User Login Index */
    public function userLoginIndex (Request $request) {
        $public_route = $request->public;
        $orgName = $request->org;
        $getCoverage = $request->coverage;
        return view('auth.userdash.userlogin', compact('public_route', 'orgName', 'getCoverage'));
    }
    /** User Login */
    public function userLogin(Request $request) {
        /* Validate the email and password inputs */
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);
        /* Unique key for throttling based on IP & email */
        $key = Str::lower($request->input('email')) . '|' . $request->ip();
        /* Check if the user is rate limited */
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
           return redirect()->back()->with('lockout_seconds', $seconds)->withInput($request->only('email'));
        }

        /* Attempt to authenticate the user */
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            RateLimiter::clear($key); /* Clear on success */
            $request->session()->regenerate();
            $user = Auth::user();

            /* Check if the service provider is active */
            $service_provider = ServiceProvider::select('id', 'active')->where('active', 1)->first();
            if (empty($service_provider)) {
                Auth::logout();
                RateLimiter::hit($key, 15);  /* Hit here as well on failure */
                /* Log failed login */
                self::failedLogin($request);
                return redirect()->route('error')->with('error', 'Service temporarily unavailable');
            }
            $activeUser = User::where('active', 1)->where('email', $user->email)->first();

            if (empty($activeUser) || $activeUser->email !== $user->email) {
                Auth::logout();
                self::failedLogin($request);
                RateLimiter::hit($key, 15);
                return redirect()->back()->withErrors(['email' => 'Invalid login credentials.'])->withInput($request->only('email'));
            }
            /* Check if the user has access to the service provider */
            if (!$activeUser->hasServiceProviderAccess()) {
                Auth::logout();
                RateLimiter::hit($key, 15);  /* Hit here as well on failure */
                /* Log failed login */
                self::failedLogin($request);
                return redirect()->route('error')->with('error', 'You do not have access to this service provider');
            }
            /* Redirect based on the user's role */
            if ($activeUser->hasRole(2)) {
                /* Log successful login */
                self::successfulLogin($request);
                if (isset($request->public) && $request->public == true && isset($request->org) && $request->org) {
                    return redirect()->route('public.org.view', ['slug' => $request->org]);
                }elseif (isset($request->public) && $request->public == true && isset($request->coverage) && $request->coverage == "get-coverage") {
                    return redirect()->route("public.get-coverage.index");
                }else {
                    return redirect()->route('sdcsmuser.dashboard');
                }
            }else {
                Auth::logout();
                /* Log failed login */
                RateLimiter::hit($key, 15);  /* Hit here as well on failure */
                self::failedLogin($request);
                return redirect()->back()->withErrors(['email' => 'Invalid login credentials'])->withInput($request->only('email', 'remember'));
            }
        } else {
            RateLimiter::hit($key, 15); /* Lockout for 15 seconds */
            /* Log failed login */
            self::failedLogin($request);
            /* If login failed, return back with error message */
            return redirect()->back()->withErrors(['email' => 'Invalid login credentials'])->withInput($request->only('email', 'remember'));
        }
    }

    public function userLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['msg' => 'Logged out successfully', 'redirect_at' => route('sdcsmuser.login.index')],200);
    }



     /** Private function to create login logs */
     private function successfulLogin($request){
        LoginLog::create([
            'ip_address' => $request->ip(),
            'browser' => $request->header('User-Agent'),
            // 'id_type' => $fieldType,
            'panel' => 'User',
            'user_email' => $request->email,
            'user_id' => Auth::id(),
            'date' => Carbon::now(),
            'success' => 1,
            'password_attempt' => null,
        ]);
    }

    private function failedLogin($request){

        LoginLog::create([
            'ip_address' => $request->ip(),
            'browser' => $request->header('User-Agent'),
            // 'id_type' => $fieldType,
            'panel' => 'User',
            'user_email' => $request->email,
            'user_id' => null,
            'date' => Carbon::now(),
            'success' => 0,
            'password_attempt' => $request->password,
        ]);
    }
}
