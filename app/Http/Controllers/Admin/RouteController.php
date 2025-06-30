<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Admin\Route;

use App\Models\Admin\RoutePermission;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;


class RouteController extends Controller
{
    /* Function to list the Routs on the page load */
    public function index(Request $request)
    {
        $where = $this->getFilter($request);
        $routes = self::getPaginatedUserRoutes(20, $where);
        $pagination = [
            'total' => $routes->total(),
            'per_page' => $routes->perPage(),
            'current_page' => $routes->currentPage(),
            'last_page' => $routes->lastPage(),
            'from' => $routes->firstItem(),
            'to' => $routes->lastItem()
        ];

        if (!empty($request->page)) {
            return response()->json(["routedata" => $routes, "pagination" => $pagination, "msg" => "Routes Fetched.", "success" => true], 200);
        } else {
            return view('admin.userroutes.index', compact('routes', 'pagination'));
        }
    }

    /* Store a newly created resource in storage. */
    public function store(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            $appUrl = preg_quote(parse_url(env('APP_URL'), PHP_URL_HOST), '/'); /* Escape special characters in host part of APP_URL */
            /* Validate to the requests */
            $validator = Validator::make($request->all(), [
                'routeName' => [
                    'required',
                    'unique:routes,route_name,Null,id,active,1,service_provider_id,' . $serviceProvider,
                    'max:255',
                    'regex:/^[a-zA-Z0-9]/', /* Must start with a letter or number */
                    'not_regex:/^(https?:\/\/|www\.)/', /* Prevents full URLs */
                    "not_regex:/$appUrl/i", /* Blocks APP_URL's host part anywhere in the string */
                ],
                'accessType' => 'required|in:1,2',
                'routeType' => 'required|in:1,2,3',
                'groupName' => 'required|string',
            ], [
                'routeName.required' => "Route name can't be empty.",
                'routeName.unique' => "Route name must be unique",
                'accessType.required' => "Access type can't be empty.",
                'routeType.required' => "Route type can't be empty.",
                'groupName.required' => "Please select at least one Group Name",
            ]);
            /* If Validation Fails */
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            /**
                 * Route Type
                 * 1 = Admin
                 * 2 = User
                 * 3 = Public
            */
            /**
                 * Access Type
                 * 1 = View
                 * 2 = All
            */
            $route = Route::create([
                'route_name' => $request->routeName,
                'access_type' => $request->accessType,
                'route_type' => $request->routeType,
                'group_name' => $request->groupName,
                'service_provider_id' => $serviceProvider,
            ]);
            /* If route created successfully */
            if ($route->wasRecentlyCreated) {
                return response()->json(["msg" => "Route created successfully.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Route creation failed.", "success" => false], 200);
            }
        }catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }

    }

    /* Show the form for editing the specified resource. */
    public function edit(string $id)
    {
        if (!empty($id)) {
            $where = [
                'where' => [
                    'id' => $id
                ]
            ];
            $route = self::getUserRouteQuery($where)->first();
            /* If Route Data Found */
            if (!empty($route)) {
                return response()->json([
                    'editData' => $route,
                    "msg" => "Edit Route Data.",
                    "success" => true,
                ], 200);
            } else {
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }
        } else {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /* Update the specified resource in storage. */
    public function update(Request $request, string $id)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            $appUrl = preg_quote(parse_url(env('APP_URL'), PHP_URL_HOST), '/'); /* Escape special characters in host part of APP_URL */
            /* Validate to the requests */
            $validator = Validator::make($request->all(), [
                'routeName' => [
                    'required',
                    'unique:routes,route_name,' . $id . ',id,active,1,service_provider_id,' . $serviceProvider,
                    'max:255',
                    'regex:/^[a-zA-Z0-9]/', /* Must start with a letter or number */
                    'not_regex:/^(https?:\/\/|www\.)/', /* Prevents full URLs */
                    "not_regex:/$appUrl/i", /* Blocks APP_URL's host part anywhere in the string */
                ],
                'accessType' => 'required|in:1,2',
                'routeType' => 'required|in:1,2,3',
                'groupName' => 'required|string',
            ], [
                'routeName.required' => "Route name can't be empty.",
                'routeName.unique' => "Route name must be unique",
                'accessType.required' => "Access type can't be empty.",
                'routeType.required' => "Route type can't be empty.",
                'groupName.required' => "Please select at least one Group Name",
            ]);
            /* If validation fails */
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $route = Route::where('id', $id)->where('service_provider_id', $serviceProvider)->where('active', 1)->first();
            /* If route found */
            if (!empty($route)) {
                   /**
                         * Route Type
                         * 1 = Admin
                         * 2 = User
                         * 3 = Public
                    */
                    /**
                         * Access Type
                         * 1 = View
                         * 2 = All
                    */
                $routeUpdated = $route->update([
                    'route_name' => $request->routeName,
                    'access_type' => $request->accessType,
                    'route_type' => $request->routeType,
                    'group_name' => $request->groupName,
                ]);
                if (!empty($routeUpdated)) {
                    return response()->json(["msg" => "Route updated successfully.", "success" => true], 200);
                } else {
                    return response()->json(["msg" => "Route update failed.", "success" => false], 200);
                }
            } else {
                return response()->json(["msg" => "Route not found.", "success" => false], 200);
            }
        } catch(\Exception $e){
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /* Remove the specified resource from storage. */
    public function destroy(string $id)
    {
        try {
            /* Getting service provider id using session */
            $serviceProvider = Session::get('service_provider');
            $route = Route::where('id', $id)->where('service_provider_id', $serviceProvider)->first();
            /* If route found */
            if (!empty($route)) {
                /* Toggle active status */
                $route->active = $route->active == 1 ? 0 : 1;
                $route->save();
                /* Set the same active status for related route permissions */
                RoutePermission::where('route_id', $id)
                    ->where('service_provider_id', $serviceProvider)
                    ->update(['active' => $route->active]);

                /* Dynamic message based on new active status */
                $message = $route->active == 1 ? 'Activated' : 'Deactivated';
                return response()->json(["msg" => "Route {$message} successfully.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Route not found.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Private function to fetch user routes (query) */
    private static function getUserRouteQuery($where = [], $fields = [])
    {
        /* Get service provider isd using session */
        $serviceProvider = Session::get('service_provider');
        $query = Route::where('service_provider_id', $serviceProvider)->where('active', 1);
        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select('id', 'route_name', 'active', 'access_type', 'route_type', 'group_name', 'created_at');
        }
        if (!empty($where) && is_array($where)) {
            foreach ($where as $field => $value) {
                if (is_array($value)) {
                    foreach ($value as $secondField => $secondValue) {
                        if (is_array($secondValue)) {
                            /* Correct handling for [operator, value] format */
                            $query->$field($secondField, $secondValue[0], $secondValue[1]);
                        } else {
                            $query->$field($secondField, $secondValue);
                        }
                    }
                }
            }
        }
        $query->orderBy('created_at', 'desc');
        return $query;
    }

    /* Get all the user routes with paginate. */
    public static function getPaginatedUserRoutes($limit = 20, $where = [], $fields = [])
    {
        $data = self::getUserRouteQuery($where, $fields);
        $limit = (is_numeric($limit) && $limit > 0) ? $limit : 20;
        return $data->paginate($limit);
    }

    /* Get all user routes without pagination */
    public static function getAllUserRoutes($where = [], $fields = [])
    {
        /* $where['where']['active'] = 1; */
        $data = self::getUserRouteQuery($where, $fields);
        return $data->get();
    }

    /** For Filter's */
    private function getFilter(Request $request)
    {
        $where = [];
        if (!empty($request->route_type) || !empty($request->access_type) || !empty($request->group_name) || !is_null($request->status)) {
            $where['where'] = [];
            /**
             * If Route Type is not empty
             * 1 = Admin
             * 2 = User
             * 3 = Public
             */
            if (!empty($request->route_type)){
                $where['where']['route_type'] = $request->route_type;

            }
            /**
            * If Access Type is not empty
            * 1 = View
            * 2 = All
            */
            if (!empty($request->access_type)){
                $where['where']['access_type'] = $request->access_type;

            }
            /**
             * If Group name is not empty
             */
           if (!empty($request->group_name)) {
                $where['where']['group_name'] = ['LIKE', "%{$request->group_name}%"];
            }
            /**
            * If status is not empty
            * 1 = active
            * 0 = deactive
            * */
           if (!is_null($request->status)) {
               $where['where']['active'] = $request->status;
           }
        }
        return $where;
    }
}
