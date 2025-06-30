<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\RepairReason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RepairReasonController extends Controller
{
    /** Display a listing of the resource. */
    public function index(Request $request)
    {
        $where = $this->getFilter($request);
        $repairReasons = self::getPaginatedRepairReasons(20, $where);
        $pagination = [
            'total' => $repairReasons->total(),
            'per_page' => $repairReasons->perPage(),
            'current_page' => $repairReasons->currentPage(),
            'last_page' => $repairReasons->lastPage(),
            'from' => $repairReasons->firstItem(),
            'to' => $repairReasons->lastItem()
        ];

        if (!empty($request->page)) {
            return response()->json(["repairReasonData"=>$repairReasons, "pagination"=> $pagination, "msg" => "Repair Reasons Fetched.", "success" => true], 200);
        } else {
            return view('admin.repairreasonmaster.index', compact('repairReasons', 'pagination'));
        }
    }

    /** Show the form for creating a new resource. */
    public function addRepairReason(Request $request)
    {
        try{
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate to the requests */
            $validator = Validator::make($request->all(), [
                'reason_name' => 'required|string|max:50|unique:repair_reasons,repair_reason_name,Null,id,active,1,service_provider_id,' . $serviceProvider,
            ], [
                'reason_name.required' => "Repair Reason can't be empty.",
                'reason_name.unique' => "Repair Reason must be unique",
            ]);
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            /* Creating repair reason */
            $repairReason = RepairReason::create([
                'repair_reason_name' => $request->reason_name,
                'service_provider_id' => $serviceProvider,
            ]);
            if($repairReason->wasRecentlyCreated){
                return response()->json(["msg" => "Repair Reason added successfully.", "success" => true], 200);
            }else {
                return response()->json(["msg" => "Repair Reason creation failed.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }

    }

    /** Show the form for editing the specified resource. */
    public function getEditData(string $id)
    {
        if(!empty($id))
        {
            $where['where']['id'] = $id;
            $repairReason = self::getRepairReasonsQuery($where)->first();
            if(!empty($repairReason)){
                return response()->json(["editData" => $repairReason, "msg" => "Edit Repair Reason Data.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
            }
        } else {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Update the specified resource in storage. */
    public function updateRepairReason(Request $request, string $id){
        try {
              /* Getting service provider id using session */
              $serviceProvider = Session::get('service_provider');
              /* Validate to the requests */
              $validator = Validator::make($request->all(), [
                  'reason_name' => 'required|string|max:50|unique:repair_reasons,repair_reason_name,' . $id . ',id,active,1,service_provider_id,' . $serviceProvider,
              ], [
                    'reason_name.required' => "Repair Reason can't be empty.",
                    'reason_name.unique' => "Repair Reason must be unique",
              ]);
            if ($validator->fails())
            {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            /* Updating repair reason */
            $repairReason = RepairReason::where('id', $id)->where('service_provider_id', $serviceProvider)->where('active', 1)->first();
            if (!empty($repairReason)) {
                $repairReasonUpdate = $repairReason->update([
                    'repair_reason_name' => $request->reason_name,
                ]);

                if (!empty($repairReasonUpdate)) {
                    return response()->json(["msg" => "Repair Reason updated successfully.", "success" => true], 200);
                } else {
                    return response()->json(["msg" => "Repair Reason updation failed.", "success" => false], 200);
                }
            } else {
                return response()->json(["msg" => "Repair Reason not found.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /* Function to delete the claim reason */
    public function destroy($id)
    {
        try {
            if(!empty($id))
            {
                /* Get service provider is using session */
                $serviceProvider = Session::get('service_provider');
                /* Get repair reason from database */
                $repairReason = RepairReason::where('id', $id)->where('service_provider_id', $serviceProvider)->first();

                if (!empty($repairReason)) {
                    /* Toggle active status */
                    $repairReason->active = $repairReason->active == 1 ? 0 : 1;
                    $repairReason->save();

                    /* Dynamic message based on new active status */
                    $message = $repairReason->active == 1 ? 'Activated' : 'Deactivated';
                    return response()->json(["msg" => "Repair Reason {$message} successfully.", "success" => true], 200);
                } else {
                    return response()->json(["msg" => "Repair Reason not found.", "success" => false], 200);
                }
            } else {
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Common private function to get records */
    private static function getRepairReasonsQuery($where = [], $fields = []){
        /* Get service provider is using session */
        $serviceProvider = Session::get('service_provider');
        $query = RepairReason::where('service_provider_id', $serviceProvider)->where('active', 1);
        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select('id', 'repair_reason_name', 'active', 'created_at');
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
        // $query;
        return $query;
    }

    /**get all repair reasons without pagination */
    public static function getAllRepairReasons($where = [], $fields = []){
        /* $where['where']['active'] = 1; */
        $data = self::getRepairReasonsQuery($where, $fields)->orderBy('repair_reason_name', 'asc');
        return $data->get();
    }

    /* Get all the repair reasons with pagination. */
    public static function getPaginatedRepairReasons($limit = 20, $where = [], $fields = []){
        $data = self::getRepairReasonsQuery($where, $fields);
        $limit = (is_numeric($limit) && $limit > 0) ? $limit : 20;
        return $data->orderBy('repair_reason_name', 'asc')->paginate($limit);
    }

    /* Get all repair reasons for drop-down */
    public static function getRepairReasonsDropdown($where = [], $fields = [])
    {
        $fields = ['id', 'repair_reason_name'];
        /* $where['where']['active'] = 1; */
        $data = self::getRepairReasonsQuery($where, $fields)->orderBy('repair_reason_name', 'asc');
        return $data->get();
    }

    /** For Filter's */
    private function getFilter(Request $request)
    {
        $where = [];
        /** Search by clasim reason name  and status*/
        if (!empty($request->search) || !is_null($request->status)) {

            $where['where'] = [];

            if (!empty($request->search)) {
                $where['where']['repair_reason_name'] = ['LIKE', "%{$request->search}%"];
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
