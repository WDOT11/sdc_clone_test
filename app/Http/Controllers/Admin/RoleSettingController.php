<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SDCOptionController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RoleSettingController extends Controller
{
    /** Index function to call file */
    public function index(){
        /** Getting service provider id from the session */
        $serviceProviderId = Session::get('service_provider');
        $roles = RoleController::getAllUserRoles();
        $persoanlCoverageRole = SDCOptionController::getOption("personal_coverage_role", $serviceProviderId);
        $eduCoverageRole = SDCOptionController::getOption("educational_coverage_role", $serviceProviderId);
        $newUserRole = SDCOptionController::getOption("register_user_role", $serviceProviderId);
        return view('admin.rolesettings.index', compact('roles', 'persoanlCoverageRole', 'eduCoverageRole', 'newUserRole'));
    }

    /** To save the role settings */
    public function store(Request $request){
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate to the requests */
            $validator = Validator::make($request->all(), [
                'personalCoverageUserRole' =>  'required|exists:roles,id,active,1,service_provider_id,' . $serviceProvider,
                'EducationalCoverageUserRole' => 'required|exists:roles,id,active,1,service_provider_id,' . $serviceProvider,
                'NewRegisterUserRole' => 'required|exists:roles,id,active,1,service_provider_id,' . $serviceProvider,
            ], [
                'personalCoverageUserRole.required' => 'Personal Coverage User Role is required.',
                'personalCoverageUserRole.exists' => 'Personal Coverage User Role does not exist or is inactive.',
                'EducationalCoverageUserRole.required' => 'Educational Coverage User Role is required.',
                'EducationalCoverageUserRole.exists' => 'Educational Coverage User Role does not exist or is inactive.',
                'NewRegisterUserRole.required' => 'New Register User Role is required.',
                'NewRegisterUserRole.exists' => 'New Register User Role does not exist or is inactive.',
            ]);
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $persoanlCoverageRoleId = $request->personalCoverageUserRole;
            $EducationalCoverageUserRoleId = $request->EducationalCoverageUserRole;
            $NewRegisterUserRoleId = $request->NewRegisterUserRole;

            /** Add/Update the option to store the role data */
            $per_cov_role = SDCOptionController::updateOption("personal_coverage_role", $persoanlCoverageRoleId);
            $edu_cov_role = SDCOptionController::updateOption("educational_coverage_role", $EducationalCoverageUserRoleId);
            $new_user_role = SDCOptionController::updateOption("register_user_role", $NewRegisterUserRoleId);

            if(!empty($per_cov_role) && $per_cov_role->option_value == $persoanlCoverageRoleId && !empty($edu_cov_role) && $edu_cov_role->option_value == $EducationalCoverageUserRoleId && !empty($new_user_role) && $new_user_role->option_value == $NewRegisterUserRoleId) {
                return response()->json(["msg" => "Role Saved Successfully.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }
}
