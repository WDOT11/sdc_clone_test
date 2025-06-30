<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RouteController;

use App\Models\Admin\RoutePermission;

use Illuminate\Http\Request;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Session;

class RoutePermissionController extends Controller
{
    /* Function to view the Routepermission page and list the route permissions */
    public function index(Request $request)
    {
        /* Get All Roles */
        $roles = RoleController::getAllUserRoles();
        $roleId = $request->roleId ? (int) $request->roleId : 1;
        /* Fetch route permission data */
        $routePermissionData = [];
        $where['where']['route_permissions.role_id'] = $roleId;
        /* Grouping route permissions by Group Name */
        $groupedPermissions = $this->getRoutePermission($where)->groupBy('group_name');
        foreach ($groupedPermissions as $groupName => $routes) {
            $roleName = $routes->first()->role_name;
            $accessTypes = $routes->pluck('access_type')->unique();
            /* Ensure the structure is initialized */
            if (!isset($routePermissionData[$roleName])) {
                $routePermissionData[$roleName] = ['View' => [], 'All' => []];
            }
            /* Determine the access level */
            if ($accessTypes->contains(2)) {
                $routePermissionData[$roleName]['All'][] = $groupName;
            } else {
                $routePermissionData[$roleName]['View'][] = $groupName;
            }
        }
        /* Convert group lists to comma-separated strings */
        foreach ($routePermissionData as $role => $accessTypes) {
            foreach ($accessTypes as $accessType => $groups) {
                $routePermissionData[$role][$accessType] = implode(', ', array_unique($groups));
            }
        }
        if ($request->page) {
            return response()->json(["routePermission" => $routePermissionData, "msg" => "Paginated records here.", "success" => true ], 200);
        }
        return view('admin.userroutepermission.index', compact('routePermissionData', 'roles'));
    }

    /* Function to add the permission(store in DB) */
    public function store(Request $request)
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');

        /* Validate request data */
        $validator = Validator::make($request->all(), [
            'role_id' => ['required', 'integer',
                Rule::exists('roles', 'id')->where(fn($query) => $query->where('active', 1)->where('service_provider_id', $serviceProvider)),
            ],
            'selected_routes' => 'required|array',
        ], [
            'role_id.required' => "Please select the role.",
            'selected_routes.required' => "Please select atleast one permission.",
        ]);

        /* If Validation Fails */
        if ($validator->fails()) {
            return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
        }

        $role_id = $request->role_id;
        $selected_routes = $request->selected_routes;
        /* Validating selected_routes or role_id */
        if (empty($selected_routes) || empty($role_id)) {
            return response()->json([
                "msg" => "Please select the Role and Routes.",
                "success" => false
            ], 200);
        }
        $addedPermissions = 0;
        foreach ($selected_routes as $selected_route) {
            $group_name = $selected_route['groupName'] ?? null;
            $access_type = $selected_route['accessType'] ?? null;
            /* If Group Name Or Access Type is Empty */
            if (empty($group_name) || empty($access_type)) {
                return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
            }
            /* Define group name conditions */
            $where = ['where' => ['group_name' => $group_name]];
            $routes = RouteController::getAllUserRoutes($where);
            if (empty($routes)) {
                return response()->json(["msg" => "No routes found for this group.", "success" => false], 200);
            }
            foreach ($routes as $route) {
                /* Delete existing permissions before adding new ones */
                RoutePermission::where('route_id', $route->id)
                    ->where('role_id', $role_id)
                    ->where('active', 1)
                    ->where('service_provider_id', $serviceProvider)
                    ->delete();
                /* Check If the route Acces type is 1 means View then get only those routes */
                if ($route->access_type == 1 && $access_type == 'View') {
                    /* Add new permission */
                    RoutePermission::create([
                        'route_id' => $route->id,
                        'role_id' => $role_id,
                        'service_provider_id' => $serviceProvider
                    ]);
                    $addedPermissions++;
                } elseif ($access_type == 'All') {
                    /* Add new permission */
                    RoutePermission::create([
                        'route_id' => $route->id,
                        'role_id' => $role_id,
                        'service_provider_id' => $serviceProvider
                    ]);
                    $addedPermissions++;
                }
            }
        }
        /* If new route permission created successfully */
        if ($addedPermissions > 0) {
            return response()->json(["msg" => "Route Permissions added successfully.", "success" => true], 200);
        }
        return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
    }

    /* Edit Route Permission */
    public function edit(String $role_id)
    {
        if (!empty($role_id)) {
            $where['where']['route_permissions.role_id'] = $role_id;
            /* Fetch route permission data */
            $routePermissionData = [
                'View' => [],
                'All' => []
            ];
            /* Grouping route permissions by Group Name */
            $groupedPermissions = $this->getRoutePermission($where)->groupBy('group_name');
            if(!empty($groupedPermissions)) {
                foreach ($groupedPermissions as $groupName => $routes) {
                    $accessTypes = $routes->pluck('access_type')->unique();
                    /** Access Type
                     * 1 = View
                     * 2 = All
                     */
                    if ($accessTypes->contains(2))
                    {
                        $routePermissionData['All'][] = $groupName;
                    } else {
                        $routePermissionData['View'][] = $groupName;
                    }
                }
                return response()->json(["editData" => $routePermissionData, "msg" => "Edit Route Permission Data.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
            }
        } else {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Update the specified resource in storage. */
    public function update(Request $request)
    {
        $serviceProvider = Session::get('service_provider');
        /* Validate request data */
        $validator = Validator::make($request->all(), [
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where(
                    fn($query) =>
                    $query->where('active', 1)->where('service_provider_id', $serviceProvider)
                ),
            ],
            'selected_routes' => 'required|array',
        ]);
        /* If Validation Fails */
        if ($validator->fails()) {
            return response()->json([
                "msg" => "Validation errors",
                "success" => false,
                "errors" => $validator->errors()
            ], 200);
        }
        $role_id = $request->role_id;
        $selected_routes = $request->selected_routes;
        /* Validating selected_routes or role_id */
        if (empty($selected_routes) || empty($role_id)) {
            return response()->json([
                "msg" => "No routes or role selected.",
                "success" => false
            ], 200);
        }
        $updatePermissions = 0;
        foreach ($selected_routes as $selected_route) {
            $group_name = $selected_route['groupName'] ?? null;
            $access_type = $selected_route['accessType'] ?? null;
            /* If Group Name Or Access Type is Empty */
            if (empty($group_name) || empty($access_type)) {
                return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
            }
            /* Define group name conditions */
            $where = ['where' => ['group_name' => $group_name]];
            $routes = RouteController::getAllUserRoutes($where);
            if (empty($routes)) {
                return response()->json(["msg" => "No routes found for this group name.", "success" => false], 200);
            }
            foreach ($routes as $route) {
                /* Delete existing permissions before adding new ones */
                RoutePermission::where('route_id', $route->id)
                    ->where('role_id', $role_id)
                    ->where('active', 1)
                    ->where('service_provider_id', $serviceProvider)
                    ->delete();
                /* Check If the route Acces type is 1 means View then get only those routes */
                if ($route->access_type == 1 && $access_type == 'View') {
                    /* Add new permission */
                    RoutePermission::create([
                        'route_id' => $route->id,
                        'role_id' => $role_id,
                        'service_provider_id' => $serviceProvider
                    ]);
                    $updatePermissions++;
                } elseif ($access_type == 'All') {
                    /* Add new permission */
                    RoutePermission::create([
                        'route_id' => $route->id,
                        'role_id' => $role_id,
                        'service_provider_id' => $serviceProvider
                    ]);
                    $updatePermissions++;
                } elseif ($access_type == 'None') {
                    /* Delete existing permission */
                    RoutePermission::where('route_id', $route->id)
                        ->where('role_id', $role_id)
                        ->where('active', 1)
                        ->where('service_provider_id', $serviceProvider)
                        ->delete();
                    $updatePermissions++;
                }
            }
        }
        /* If route permission updated successfully */
        if ($updatePermissions > 0) {
            return response()->json(["msg" => "Route Permissions Updated successfully.", "success" => true], 200);
        }
        return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
    }

    /** Get Route Pemission Query */
    private function getRoutePermissionQuery($where = [], $fields = [])
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $query = DB::table('route_permissions')
            ->leftJoin('roles as role', 'route_permissions.role_id', '=', 'role.id')
            ->leftJoin('routes as route', 'route_permissions.route_id', '=', 'route.id')
            ->where('route_permissions.service_provider_id', $serviceProvider)
            ->where('route_permissions.active', 1)
            ->where('role.service_provider_id', $serviceProvider)
            ->where('role.active', 1)
            ->where('route.service_provider_id', $serviceProvider)
            ->where('route.active', 1);
        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select([
                'route_permissions.id',
                'route_permissions.role_id',
                'route_permissions.route_id',
                'role.name as role_name',
                'route.route_name',
                'route.route_type',
                'route.access_type',
                'route.group_name',
                'route_permissions.created_at'
            ]);
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
        return $query->orderBy('route_permissions.created_at', 'desc');
    }

    /* Get All Route Pemission */
    private function getRoutePermission($where = [], $fields = [])
    {
        $data = $this->getRoutePermissionQuery($where, $fields);
        return $data->get();
    }
}
