<?php

namespace App\Http\Middleware;

use App\Models\Admin\Route;
use App\Models\Admin\RoutePermission;
use App\Models\Admin\ServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckUserPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $routePath = request()->path();

        /* If route is empty. */
        if (empty($routePath)) {
            return redirect()->route('error')->with('error', 'Page not found');
        }
        /* $service_provider = ServiceProvider::select('id', 'active')->where('active', 1)->first(); */
        $service_provider = 1;
        /* If service provider is empty */
        if (empty($service_provider)) {
            /* Redirect to 503 page */
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Service temporarily unavailable']);
            }
            return redirect()->route('error')->with('error', 'Service temporarily unavailable');
        }

        /* Remove query parameters from the route path */
        $routePath = strtok($routePath, '?');
        /* If route is empty after remove '?' */
        if (empty($routePath)) {
            return redirect()->route('error')->with('error', 'Page not found');
        }

        /* Remove trailing numeric segments or other unnecessary parts */
        $routePath = preg_replace('/\/[0-9]+$/', '', $routePath);
        /* Check if route exists in database */
        $route = Route::select('id', 'route_name', 'route_type', 'access_type')->where('route_name', $routePath)->where('service_provider_id', $service_provider)->where('active', 1)->first();
        if (empty($route)) {
            /** Redirect to 404 page */
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Page Not Found'], 404);
            }
            return redirect()->route('error')->with('error', 'Page not found');
        }
        /* route_type = 1 for admin
         route_type = 2 for user
         route_type = 3 for public
         */
        if (!empty($route) && !empty($route->route_type) && $route->route_type == 3) {
            return $next($request);
        }
        if (!Auth::check()){
            return redirect()->route('sdcsmuser.login.index');
        }
        $user = Auth::user();
        /* Check if the user's service_provider matches */
        if ($user->service_provider_id == $service_provider && $user->active == 1) {
            $service_user = $user; /* The user matches the criteria */
            /* Set session for service_provider_id */
            Session::put('service_provider', $user->service_provider_id);
            /* Set session for Authenticate User */
            Session::put('auth_user', $user);
            /* Session::put('service_provider', ''); */
        } else {
            $service_user = null; /* No match found */
        }
        if (empty($service_user)) {
            return redirect()->route('sdcsmuser.login.index');
        }
        /* Check if user's role has permission for this route */
        $hasPermission = RoutePermission::where('route_id', $route->id)->where('role_id', $service_user->role_id)->where('service_provider_id', $service_user->service_provider_id)->where('active', 1)->exists();
        if (!$hasPermission) {
            /* Redirect to unauthorized page */
            if ($request->expectsJson()) {
                return response()->json(['error' => 'You do not have permission to access this page'], 403);
            }
            return redirect()->route('error')->with('error', 'You do not have permission to access this page');
        }
        /* If all checks pass, proceed with the request */
        return $next($request);
    }

    /** Check for critical URL's */
    private function checkcriticalurls($route){
        if($route == 'zoho/handelresponse'){
            return 'true';
        }
        /** Public Organization */
        if($route == 'public/organization'){
            return 'true';
        }
    }
}
