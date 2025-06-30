<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        /** Auth Check if the user is logged in then logout first */
        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                Auth::logout();
                /* Clear any session data if needed */
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            return $next($request);
        });
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        /* return view('index'); */
        return redirect()->route('sdcsmuser.login.index');

    }
}
