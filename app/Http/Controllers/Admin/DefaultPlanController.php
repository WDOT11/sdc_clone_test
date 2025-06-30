<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Admin\DevicePlan;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DefaultPlanController extends Controller
{
    /** Display a listing of the resource. */
    public function index(Request $request)
    {
        $where = [];
        if (!empty($request->search) || !empty($request->planType)) {
            /* Search By Plan Name, Family Name , Brand Name, Model Title*/
            if (!empty($request->search)) {
                $where['where']['plan_name'] = ['LIKE', "%{$request->search}%"];
            }
            /*  Search By Device PLan Type */
            if (!empty($request->planType)) {
                $where['where']['plan_type'] = $request->planType;
            }
        }
        $planData = self::getPaginateDefaultPlans(20, $where);
        $pagination = [
            'total' => $planData->total(),
            'per_page' => $planData->perPage(),
            'current_page' => $planData->currentPage(),
            'last_page' => $planData->lastPage(),
            'from' => $planData->firstItem(),
            'to' => $planData->lastItem()
        ];
        if (!empty($request->page)) {
            return response()->json(["planData" => $planData, "pagination" => $pagination, "msg" => "Paginated records here.", "success" => true], 200);
        } else {
            return view('admin.devicedefaultplanmaster.index', compact('planData', 'pagination'));
        }
    }

    /** To store the plan data */
    public function store(Request $request) {
        try {

            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');

            /* Validate request data */
            $validator = Validator::make($request->all(), [
                'device_plan_type' => 'required|in:1,2',
                'plan_name' => 'required|string|max:50|unique:device_plans,plan_name,Null,id,active,1,service_provider_id,' . $serviceProvider.',plan_type,'.$request->device_plan_type,
                'plan_price' => 'required|numeric|min:0',
                'plan_deductibe' => 'nullable|numeric|min:0',
                'plan_occurence' => 'required|in:1,2,3,4,5',
                'plan_expiration_days' => 'nullable|integer|min:0',
            ], [
                'device_plan_type.required' => 'Device plan type is required.',
                'device_plan_type.in' => 'Invalid device plan type.',

                'plan_name.required' => 'Plan name is required.',
                'plan_name.unique' => 'Plan name already exists.',
                'plan_name.max' => 'Plan name should not exceed 50 characters.',

                'plan_price.required' => 'Plan price is required.',
                'plan_price.numeric' => 'Plan price should be a numeric value.',
                'plan_price.min' => 'Plan price should be at least 0.',

                'plan_deductibe.numeric' => 'Deductible price should be a numeric value.',
                'plan_deductibe.min' => 'Deductible price should be at least 0.',

                'plan_occurence.required' => 'Frequency of occurrence is required.',
                'plan_occurence.in' => 'Invalid frequency of occurrence.',

                'plan_expiration_days.required' => 'Expiration days are required.',
                'plan_expiration_days.integer' => 'Expiration days should be an integer.',
                'plan_expiration_days.min' => 'Expiration days should be at least 0.',
            ]);

            /* If Validations fails */
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $planExpirationDays = $request->plan_expiration_days;
            if ($request->plan_occurence == 1) {
                $planExpirationDays = $request->plan_expiration_days ?? 30; /** Default to 30 days if not provided */
            }elseif ($request->plan_occurence == 2) {
                /** Monthly */
                $planExpirationDays = 30;
            }elseif ($request->plan_occurence == 3) {
                /** Quarterly */
                $planExpirationDays = 90;
            }elseif ($request->plan_occurence == 4) {
                /** Half-Yearly */
                $planExpirationDays = 180;
            }elseif ($request->plan_occurence == 5) {
                /** Annually */
                $planExpirationDays = 365;
            }

            $plan = DevicePlan::create([
                'plan_type' => $request->device_plan_type,
                'plan_name' => $request->plan_name,
                'price' => $request->plan_price,
                'deductible_price' => $request->plan_deductibe,
                'freq_occurence' => $request->plan_occurence,
                'expiration_days' => $planExpirationDays,
                'service_provider_id' => $serviceProvider,
            ]);
            if ($plan->wasRecentlyCreated) {

                return response()->json(["msg" => "Plan created successfully.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Plan creation failed.", "success" => false], 200);
            }
        } catch (\Exception $e){
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** To show the data in update plan form */
    public function edit($id) {
        if(!empty($id))
        {
            $where['where']['id'] = $id;
            $plan = self::getAllDefaultPlansQuery($where)->first();
            if(!empty($plan)){
                return response()->json(["editData" => $plan, "msg" => "Edit Claim Reason Data.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
            }
        } else {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Update the specified resource in storage. */
    public function update(Request $request, $id) {
        try {
            /* Getting service provider id using session */
            $serviceProvider = Session::get('service_provider');
            /* Validate to the requests */
            $validator = Validator::make($request->all(), [
                'device_plan_type' => 'required|in:1,2',
                'plan_name' => 'required|string|max:50|unique:device_plans,plan_name,' . $id . ',id,active,1,service_provider_id,' . $serviceProvider.',plan_type,'.       $request->device_plan_type,
                'plan_price' => 'required|numeric|min:0',
                'plan_deductibe' => 'nullable|numeric|min:0',
                'plan_occurence' => 'required|in:1,2,3,4,5',
                'plan_expiration_days' => 'nullable|integer|min:0',
            ], [
                'device_plan_type.required' => 'Device plan type is required.',
                'device_plan_type.in' => 'Invalid device plan type.',

                'plan_name.required' => 'Plan name is required.',
                'plan_name.unique' => 'Plan name already exists.',
                'plan_name.max' => 'Plan name should not exceed 50 characters.',

                'plan_price.required' => 'Plan price is required.',
                'plan_price.numeric' => 'Plan price should be a numeric value.',
                'plan_price.min' => 'Plan price should be at least 0.',

                'plan_deductibe.numeric' => 'Deductible price should be a numeric value.',
                'plan_deductibe.min' => 'Deductible price should be at least 0.',

                'plan_occurence.required' => 'Frequency of occurrence is required.',
                'plan_occurence.in' => 'Invalid frequency of occurrence.',

                'plan_expiration_days.required' => 'Expiration days are required.',
                'plan_expiration_days.integer' => 'Expiration days should be an integer.',
                'plan_expiration_days.min' => 'Expiration days should be at least 0.'
            ]);
          if ($validator->fails())
          {
              return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
          }
          /* Updating plan */
          $plan = DevicePlan::where('id', $id)->where('service_provider_id', $serviceProvider)->where('active', 1)->first();
          if (!empty($plan)) {
                $planExpirationDays = $request->plan_expiration_days;
                if ($request->plan_occurence == 1) {
                    $planExpirationDays = $request->plan_expiration_days ?? 30; /** Default to 30 days if not provided */
                } elseif ($request->plan_occurence == 2) {
                    /** Monthly */
                    $planExpirationDays = 30;
                } elseif ($request->plan_occurence == 3) {
                    /** Quarterly */
                    $planExpirationDays = 90;
                } elseif ($request->plan_occurence == 4) {
                    /** Half-Yearly */
                    $planExpirationDays = 180;
                } elseif ($request->plan_occurence == 5) {
                    /** Annually */
                    $planExpirationDays = 365;
                }
              /* Updating the plan */
              $planUpdate = $plan->update([
                'plan_type' => $request->device_plan_type,
                'plan_name' => $request->plan_name,
                'price' => $request->plan_price,
                'deductible_price' => $request->plan_deductible,
                'freq_occurence' => $request->plan_occurence,
                'expiration_days' => $planExpirationDays,
              ]);

              if (!empty($planUpdate)) {
                  return response()->json(["msg" => "Plan updated successfully.", "success" => true], 200);
              } else {
                  return response()->json(["msg" => "Plan updation failed.", "success" => false], 200);
              }
          } else {
              return response()->json(["msg" => "Plan not found.", "success" => false], 200);
          }
        } catch (\Exception $e){
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** get all default plans (query) */
    private static function getAllDefaultPlansQuery($where = [], $fields = [])
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $query =  DevicePlan::whereNull('device_model_id')->where('active', 1)->where('service_provider_id', $serviceProvider);
        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select(
                'id',
                'device_model_id',
                'plan_type',
                'plan_name',
                'price',
                'deductible_price',
                'expiration_days',
                'freq_occurence',
                'insured_uninsured_devices',
                'active',
            );
        }
        if (!empty($where) && is_array($where)) {
            foreach ($where as $field => $value) {
                if (is_array($value)) {
                    foreach ($value as $secondField => $secondValue) {
                        if (is_array($secondValue)) {
                            $query->$field($secondField, $secondValue[0], $secondValue[1]);
                        } else {
                            $query->$field($secondField, $secondValue);
                        }
                    }
                }
            }
        }
        return $query->orderBy('created_at', 'asc');
    }

    /* get all default plans list with paginate */
    public static function getPaginateDefaultPlans($limit = 20, $where = [], $fields = [])
    {
        $data = self::getAllDefaultPlansQuery($where, $fields);
        $limit = (is_numeric($limit) && $limit > 0) ? $limit : 20;
        return $data->paginate($limit);
    }

    /* get all default plans  list without paginate */
    public static function getDefaultPlans($where = [], $fields = [])
    {
        $data = self::getAllDefaultPlansQuery($where, $fields);
        return $data->get();
    }
}
