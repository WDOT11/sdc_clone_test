<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Admin\Role;
use App\Models\Admin\RoutePermission;
use App\Models\User;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /** Function to list all the Roles */
    public function index(Request $request)
    {
        $where = $this->getFilter($request);
        $roles = self::getPaginatedUserRoles(20, $where);
        $pagination = [
            'total' => $roles->total(),
            'per_page' => $roles->perPage(),
            'current_page' => $roles->currentPage(),
            'last_page' => $roles->lastPage(),
            'from' => $roles->firstItem(),
            'to' => $roles->lastItem()
        ];

        /** If the page parameter is not empty */
        if (!empty($request->page)) {
            return response()->json(["roledata" => $roles, "pagination" => $pagination, "msg" => "Roles Fetched.", "success" => true], 200);
        } else {
            return view('admin.userrolemaster.index', compact('roles', 'pagination'));
        }
    }

    /* Function to Create role (save the role data) */
    public function store(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');

            /* Validate to the requests */
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50|unique:roles,name,Null,id,active,1,service_provider_id,' . $serviceProvider,
                'roleType' => 'required|in:1,2',
                'roleFor' => 'required|string|in:is_admin,is_org_it_hod,is_org_it_director,is_org_subscriber,is_subscriber',
            ], [
                'name.required' => "Role name can't be empty.",
                'name.unique' => "Role name must be unique",
                'name.max' => "Role name can't be more than 50 characters.",
                'name.string' => "Role name must be a string.",
                'roleType.in' => "Invalid role type.",
                'roleType.required' => "Role type can't be empty.",
                'roleFor.required' => "Role for can't be empty.",
                'roleFor.in' => "Invalid role for.",
            ]);

            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }

            /**
             * Role type
             * 1 = admin
             * 2 = user
             * */
            $role = Role::create([
                'name' => $request->name,
                'role_type' => $request->roleType,
                'role_for' => $request->roleFor,
                'service_provider_id' => $serviceProvider,
            ]);
            if ($role->wasRecentlyCreated) {
                return response()->json(["msg" => "Role created successfully.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Role creation failed.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /* Function to view the Update role form */
    public function edit($id)
    {
        if (!empty($id)) {
            $where = [
                'where' => [
                    'id' => $id
                ]
            ];
            $role = self::getUserRolesQuery($where)->first();
            if (!empty($role)) {
                return response()->json(['editData' => $role, "msg" => "Edit Role Data.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }
        } else {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /* Function to Update the role(save the role data) */
    public function update(Request $request, $id)
    {
        try {
            /* Getting service provider id using session */
            $serviceProvider = Session::get('service_provider');
            /* Validate to the requests */
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50|unique:roles,name,' . $id . ',id,active,1,service_provider_id,' . $serviceProvider,
                'roleType' => 'required|in:1,2',
                'roleFor' => 'required|string|in:is_admin,is_org_it_hod,is_org_it_director,is_org_subscriber,is_subscriber',
            ], [
                'name.required' => "Role name can't be empty.",
                'name.unique' => "Role name must be unique",
                'name.max' => "Role name can't be more than 50 characters.",
                'name.string' => "Role name must be a string.",
                'roleType.in' => "Invalid role type.",
                'roleType.required' => "Role type can't be empty.",
                'roleFor.required' => "Role for can't be empty.",
                'roleFor.in' => "Invalid role for.",
            ]);

            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }

            $role = Role::where('id', $id)->where('service_provider_id', $serviceProvider)->first();
            if (!empty($role)) {
                /**
                 * Role type
                 * 1 = admin
                 * 2 = user
                 * */
                $roleUpdated = $role->update([
                    'name' => $request->name,
                    'role_type' => $request->roleType,
                    'role_for' => $request->roleFor,
                    'service_provider_id' => $serviceProvider,
                ]);

                if (!empty($roleUpdated)) {
                    return response()->json(["msg" => "Role updated successfully.", "success" => true], 200);
                } else {
                    return response()->json(["msg" => "Role update failed.", "success" => false], 200);
                }
            } else {
                return response()->json(["msg" => "Role not found.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later." . $e->getMessage(), "success" => false], 200);
        }
    }

    /* Function to delete the role */
    public function destroy($id)
    {
        try {
            $serviceProvider = Session::get('service_provider');
            $role = Role::where('id', $id)->where('service_provider_id', $serviceProvider)->first();

            if (!empty($role)) {
                /* Toggle active status */
                $role->active = $role->active == 1 ? 0 : 1;
                $role->save();

                /* Set the same active status for related users and permissions */
                User::where('role_id', $id)
                    ->where('service_provider_id', $serviceProvider)
                    ->update(['active' => $role->active]);

                RoutePermission::where('role_id', $id)
                    ->where('service_provider_id', $serviceProvider)
                    ->update(['active' => $role->active]);

                /* Dynamic message based on new active status */
                $message = $role->active == 1 ? 'Activated' : 'Deactivated';
                return response()->json(["msg" => "Role {$message} successfully.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Role not found.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /* get User Roles Query */
    private static function getUserRolesQuery($where = [], $fields = [])
    {
        /* Get service provider is using session */
        $serviceProvider = Session::get('service_provider');
        $query = Role::where('service_provider_id', $serviceProvider)->where('active', 1);
        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select('id', 'name', 'role_type', 'role_for', 'active', 'created_at');
        }
        if (!empty($where) && is_array($where)) {
            foreach ($where as $field => $value) {
                if (is_array($value)) {
                    foreach ($value as $secondField => $secondValue) {
                        if ($field == 'where' && is_array($secondValue)) {
                            /* Correct handling for [operator, value] format */
                            $query->$field($secondField, $secondValue[0], $secondValue[1]);
                        } else {
                            $query->$field($secondField, $secondValue);
                        }
                    }
                }
            }
        }
        $query->orderBy('name', 'asc');
        return $query;
    }

    /* Get all the user roles with. */
    public static function getPaginatedUserRoles($limit = 20, $where = [], $fields = [])
    {
        $data = self::getUserRolesQuery($where, $fields);
        $limit = (is_numeric($limit) && $limit > 0) ? $limit : 20;
        return $data->paginate($limit);
    }

    /* get all user roles without pagination */
    public static function getAllUserRoles($where = [], $fields = [])
    {
        /* $where['where']['active'] = 1; */
        $data = self::getUserRolesQuery($where, $fields)->orderBy('name', 'asc');
        return $data->get();
    }

    /* get all roles for dropdown */
    public static function getRolesForDropdown($where = [], $fields = [])
    {
        /* $where['where']['active'] = 1; */
        $fields = ['id', 'name', 'role_for'];
        $data = self::getUserRolesQuery($where, $fields)->orderBy('name', 'asc');
        $roles = $data->get();
        return $roles;
    }

    /* Get Only Specific role */
    public static function firstRole($where = [], $fields = [])
    {
        $role = self::getUserRolesQuery($where, $fields)->first();
        return $role;
    }

    /** For Filter's */
    private function getFilter(Request $request)
    {
        $where = [];
        if (!empty($request->name) || !empty($request->roleType) || !is_null($request->status)) {
            $where['where'] = [];

            /* If name is not empty */
            if (!empty($request->name)) {
                $where['where']['name'] = ['LIKE', "%{$request->name}%"];
            }
            /**
             * If role type is not empty
             * 1 = admin
             * 2 = user
             * */
            if (!empty($request->roleType)) {
                $where['where']['role_type'] = ['=', $request->roleType];
            }
            /**
             * If status is not empty
             * 1 = active
             * 0 = deactive
             * */
            if (!is_null($request->status)) {
                $where['where']['active'] = ['=', $request->status];
            }
        }
        return $where;
    }
}
