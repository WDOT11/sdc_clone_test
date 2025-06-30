<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin\ServiceProvider;
use App\Models\LoginLog;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';
    // protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
    /**
     * Login
     */
    /*
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        if (auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = auth()->user();

            $service_provider = ServiceProvider::select('id', 'active')->where('active', 1)->first();
            if (empty($service_provider)) {
                auth()->logout();
                return redirect()->route('error')->with('error', 'Service temporarily unavailable');
            }

            if (!$user->hasServiceProviderAccess()) {
                auth()->logout();
                return redirect()->route('error')->with('error', 'You do not have access to this service provider');
            }

            if ($user->hasRole(1)) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole(2)) {
                return redirect()->route('user.dashboard');
            } else {
                auth()->logout();
                return redirect()->route('error')->with('error', 'Invalid login credentials');
            }
        }

        return redirect()->back()->withInput($request->only('email', 'remember'));
    }  */
    public function login(Request $request)
    {
        /* Validate the email and password inputs */
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        /* Attempt to authenticate the user */
        if (auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();
            $user = auth()->user();

            /* Check if the service provider is active */
            $service_provider = ServiceProvider::select('id', 'active')->where('active', 1)->first();
            if (empty($service_provider)) {
                auth()->logout();

                /* Log failed login */
                $loginlog = self::failedLogin();

                return redirect()->route('error')->with('error', 'Service temporarily unavailable');
            }

            /* Check if the user has access to the service provider */
            if (!$user->hasServiceProviderAccess()) {
                auth()->logout();

                /* Log failed login */
                $loginlog = self::failedLogin($request);
                return redirect()->route('error')->with('error', 'You do not have access to this service provider');
            }

            /* Redirect based on the user's role */
            if ($user->hasRole(1)) {

                /* Log successful login */
                $loginlog = self::successfulLogin($request);

                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole(2)) {

                /* Log successful login */
                $loginlog = self::successfulLogin($request);

                return redirect()->route('sdcsmuser.dashboard');
            } else {
                auth()->logout();

                /* Log failed login */
                $loginlog = self::failedLogin($request);

                return redirect()->route('error')->with('error', 'Invalid login credentials');
            }
        } else {
            /* If login failed, return back with error message */
            /* Log failed login */
            $loginlog = self::failedLogin($request);
            return redirect()->back()->withErrors(['email' => 'Invalid login credentials'])->withInput($request->only('email', 'remember'));
        }
    }

    /** Private function to create login logs */
    private function successfulLogin($request){
        LoginLog::create([
            'ip_address' => $request->ip(),
            'browser' => $request->header('User-Agent'),
            // 'id_type' => $fieldType,
            // 'panel' => $this->getPanelName($request->loginPath),
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
            // 'panel' => $this->getPanelName($request->loginPath),
            'user_email' => $request->email,
            'user_id' => null,
            'date' => Carbon::now(),
            'success' => 0,
            'password_attempt' => $request->password,
        ]);
    }



}
