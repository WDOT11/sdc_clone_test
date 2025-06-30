<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\ClaimReason;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;


class ClaimReasonController extends Controller
{
    /** Display a listing of the resource. */
    public function index(Request $request)
    {
        $where = $this->getFilter($request);
        $claimReasons = self::getPaginatedClaimReasons(20, $where);
        $pagination = [
            'total' => $claimReasons->total(),
            'per_page' => $claimReasons->perPage(),
            'current_page' => $claimReasons->currentPage(),
            'last_page' => $claimReasons->lastPage(),
            'from' => $claimReasons->firstItem(),
            'to' => $claimReasons->lastItem()
        ];

        if (!empty($request->page)) {
            return response()->json(["claimReasonData"=>$claimReasons, "pagination"=> $pagination, "msg" => "Claim Reasons Fetched.", "success" => true], 200);
        } else {
            return view('admin.claimreasonmaster.index', compact('claimReasons', 'pagination'));
        }
    }

    /** Show the form for creating a new resource. */
    public function addClaimReason(Request $request)
    {
        try{
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate to the requests */
            $validator = Validator::make($request->all(), [
                'reason_name' => 'required|string|max:50|unique:claim_reasons,claim_reason_name,Null,id,active,1,service_provider_id,' . $serviceProvider,
            ], [
                'reason_name.required' => "Claim Reason can't be empty.",
                'reason_name.unique' => "Claim Reason must be unique",
            ]);
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            /* Creating claim reason */
            $claimReason = ClaimReason::create([
                'claim_reason_name' => $request->reason_name,
                'service_provider_id' => $serviceProvider,
            ]);
            if($claimReason->wasRecentlyCreated){
                return response()->json(["msg" => "Claim Reason added successfully.", "success" => true], 200);
            }else {
                return response()->json(["msg" => "Claim Reason creation failed.", "success" => false], 200);
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
            $claimReason = self::getClaimReasonsQuery($where)->first();
            if(!empty($claimReason)){
                return response()->json(["editData" => $claimReason, "msg" => "Edit Claim Reason Data.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
            }
        } else {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Update the specified resource in storage. */
    public function updateClaimReason(Request $request, string $id){
        try {
              /* Getting service provider id using session */
              $serviceProvider = Session::get('service_provider');
              /* Validate to the requests */
              $validator = Validator::make($request->all(), [
                  'reason_name' => 'required|string|max:50|unique:claim_reasons,claim_reason_name,' . $id . ',id,active,1,service_provider_id,' . $serviceProvider,
              ], [
                    'reason_name.required' => "Claim Reason can't be empty.",
                    'reason_name.unique' => "Claim Reason must be unique",
              ]);
            if ($validator->fails())
            {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            /* Updating claim reason */
            $claimReason = ClaimReason::where('id', $id)->where('service_provider_id', $serviceProvider)->where('active', 1)->first();
            if (!empty($claimReason)) {
                $claimReasonUpdate = $claimReason->update([
                    'claim_reason_name' => $request->reason_name,
                ]);

                if (!empty($claimReasonUpdate)) {
                    return response()->json(["msg" => "Claim Reason updated successfully.", "success" => true], 200);
                } else {
                    return response()->json(["msg" => "Claim Reason updation failed.", "success" => false], 200);
                }
            } else {
                return response()->json(["msg" => "Claim Reason not found.", "success" => false], 200);
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
                /* Get claim reason from database */
                $claimReason = ClaimReason::where('id', $id)->where('service_provider_id', $serviceProvider)->first();

                if (!empty($claimReason)) {
                    /* Toggle active status */
                    $claimReason->active = $claimReason->active == 1 ? 0 : 1;
                    $claimReason->save();

                    /* Dynamic message based on new active status */
                    $message = $claimReason->active == 1 ? 'Activated' : 'Deactivated';
                    return response()->json(["msg" => "Claim Reason {$message} successfully.", "success" => true], 200);
                } else {
                    return response()->json(["msg" => "Claim Reason not found.", "success" => false], 200);
                }
            } else {
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Common private function to get records */
    private static function getClaimReasonsQuery($where = [], $fields = []){
        /* Get service provider is using session */
        $serviceProvider = Session::get('service_provider');
        $query = ClaimReason::where('service_provider_id', $serviceProvider)->where('active', 1);
        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select('id', 'claim_reason_name', 'active', 'created_at');
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

    /**get all claim reasons without pagination */
    public static function getAllClaimReasons($where = [], $fields = []){
        /* $where['where']['active'] = 1; */
        $data = self::getClaimReasonsQuery($where, $fields)->orderBy('claim_reason_name', 'asc');
        return $data->get();
    }

    /* Get all the claim reasons with pagination. */
    public static function getPaginatedClaimReasons($limit = 20, $where = [], $fields = []){
        $data = self::getClaimReasonsQuery($where, $fields);
        $limit = (is_numeric($limit) && $limit > 0) ? $limit : 20;
        return $data->orderBy('claim_reason_name', 'asc')->paginate($limit);
    }

    /* Get all claim reasons for drop-down */
    public static function getClaimReasonsDropdown($where = [], $fields = [])
    {
        $fields = ['id', 'claim_reason_name'];
        /* $where['where']['active'] = 1; */
        $data = self::getClaimReasonsQuery($where, $fields)->orderBy('claim_reason_name', 'asc');
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
                $where['where']['claim_reason_name'] = ['LIKE', "%{$request->search}%"];
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

    /** Function to get the data by claim reason id */
    public static function getReasonById($id)
    {
        $where['where']['id'] = $id;
        $claimReason = ClaimReasonController::getClaimReasonsQuery($where)->first();
        return $claimReason;
    }
}
