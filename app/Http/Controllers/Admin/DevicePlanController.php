<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\DeviceModelController;
use App\Http\Controllers\Controller;
use App\Models\Admin\Device;

use App\Models\Admin\DevicePlan;
use App\Models\Admin\OrgAllowedModel;
use App\Models\Admin\OrgAllowedRenewalModel;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use LengthException;


use Stripe\Product;
use Stripe\Price;
use Stripe\Stripe;

class DevicePlanController extends Controller
{
    /** Display a listing of the resource. */
    public function index(Request $request)
    {
        $where = $this->getFilter($request);
        $planData = self::getPaginateDevicePlans(20, $where);
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
            return view('admin.deviceplanmaster.index', compact('planData', 'pagination'));
        }
    }

    /** Store a newly created resource in storage. */
    public function store(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate request data */
            $validator = Validator::make($request->all(), [
                'devicePlanType' => 'required|in:1,2',
                'selectedDeviceModel' => 'required|exists:device_models,id,active,1,service_provider_id,' . $serviceProvider,

                'coverage_plans' => 'required_if:devicePlanType,1|array|max:5',
                'renewal_plans' => 'required_if:devicePlanType,2|array|max:5',

                /* 'coverage_plans.*.PlanName' => 'required|string|max:50|unique:device_plans,plan_name,coverage_plans.*.planId,id,active,1,service_provider_id,' . $serviceProvider . ',device_model_id,' . $request->selectedDeviceModel . ',plan_type,1', */
                'coverage_plans.*.PlanName' => [
                    'required',
                    'string',
                    'max:50',
                    function ($attribute, $value, $fail) use ($serviceProvider, $request) {
                        /* Extract the index from the attribute name */
                        preg_match('/coverage_plans\.(\d+)\.PlanName/', $attribute, $matches);
                        $currentIndex = $matches[1] ?? null;

                        /* Get the planId if it exists */
                        $planId = isset($request->coverage_plans[$currentIndex]['planId'])
                            ? $request->coverage_plans[$currentIndex]['planId']
                            : null;

                        /* 1. Check for duplicates within the submitted plans when planId is null */
                        if (!$planId) {
                            /* Check other plan names in the same request */
                            foreach ($request->coverage_plans as $index => $plan) {
                                /* Skip comparing with itself */
                                if ($index == $currentIndex) {
                                    continue;
                                }

                                /* If another plan in the request has the same name, it's a duplicate */
                                if ($plan['PlanName'] == $value) {
                                    $fail('The plan name must be unique.');
                                    return;
                                }
                            }
                        }

                        /* 2. Check against existing records in the database */
                        $query = DB::table('device_plans')
                            ->where('plan_name', $value)
                            ->where('service_provider_id', $serviceProvider)
                            ->where('active', 1)
                            ->where('device_model_id', $request->selectedDeviceModel)
                            ->where('plan_type', 1);

                        /* Exclude current plan ID if it exists */
                        if ($planId) {
                            $query->where('id', '!=', $planId);
                        }

                        /* If a record exists, it means the name is not unique */
                        if ($query->exists()) {
                            $fail('The plan name has already been taken.');
                        }
                    }
                ],
                'coverage_plans.*.PlanPrice' => 'required|numeric|min:0',
                'coverage_plans.*.PlanDeductPrice' => 'nullable|numeric|min:0',
                'coverage_plans.*.freqOccurence' => 'required|in:1,2,3,4,5',
                'coverage_plans.*.planExpireDays' => 'nullable|integer|min:0',

                /* 'renewal_plans.*.PlanName' => 'required|string|max:50|unique:device_plans,plan_name,renewal_plans.*.planId,id,active,1,service_provider_id,' . $serviceProvider . ',device_model_id,' . $request->selectedDeviceModel . ',plan_type,2', */
                'renewal_plans.*.PlanName' => [
                    'required',
                    'string',
                    'max:50',
                    function ($attribute, $value, $fail) use ($serviceProvider, $request) {
                        /* Extract the index from the attribute name */
                        preg_match('/renewal_plans\.(\d+)\.PlanName/', $attribute, $matches);
                        $index = $matches[1] ?? null;

                        /* Get the planId if it exists */
                        $planId = isset($request->renewal_plans[$index]['planId'])
                            ? $request->renewal_plans[$index]['planId']
                            : null;
                        /* 1. Check for duplicates within the submitted plans when planId is null */
                        if (!$planId) {
                            /* Check other plan names in the same request */
                            foreach ($request->renewal_plans as $index => $plan) {
                                /* Skip comparing with itself */
                                if ($index == $index) {
                                    continue;
                                }

                                /* If another plan in the request has the same name, it's a duplicate */
                                if ($plan['PlanName'] == $value) {
                                    $fail('The plan name must be unique.');
                                    return;
                                }
                            }
                        }
                        /* 2. Check for uniqueness with DB query */
                        $query = DB::table('device_plans')
                            ->where('plan_name', $value)
                            ->where('service_provider_id', $serviceProvider)
                            ->where('active', 1)
                            ->where('device_model_id', $request->selectedDeviceModel)
                            ->where('plan_type', 2);

                        /* Exclude current plan ID if it exists */
                        if ($planId) {
                            $query->where('id', '!=', $planId);
                        }

                        /* If a record exists, it means the name is not unique */
                        if ($query->exists()) {
                            $fail('The plan name has already been taken.');
                        }
                    }
                ],
                'renewal_plans.*.PlanPrice' => 'required|numeric|min:0',
                'renewal_plans.*.PlanDeductPrice' => 'nullable|numeric|min:0',
                'renewal_plans.*.freqOccurence' => 'required|in:1,2,3,4,5',
                'renewal_plans.*.planExpireDays' => 'nullable|integer|min:0',

            ], [
                'devicePlanType.required' => 'Device plan type is required.',
                'devicePlanType.in' => 'Invalid device plan type.',

                'selectedDeviceModel.required' => 'Device model is required.',
                'selectedDeviceModel.exists' => 'Invalid device model.',

                'coverage_plans.*.PlanName.required' => 'Plan name is required.',
                'coverage_plans.*.PlanName.unique' => 'Plan name already exists.',
                'coverage_plans.*.PlanName.max' => 'Plan name should not exceed 50 characters.',
                'coverage_plans.*.PlanPrice.required' => 'Plan price is required.',
                'coverage_plans.*.PlanPrice.numeric' => 'Plan price should be a numeric value.',
                'coverage_plans.*.PlanPrice.min' => 'Plan price should be at least 0.',
                'coverage_plans.*.PlanDeductPrice.numeric' => 'Deductible price should be a numeric value.',
                'coverage_plans.*.PlanDeductPrice.min' => 'Deductible price should be at least 0.',
                'coverage_plans.*.freqOccurence.required' => 'Frequency of occurrence is required.',
                'coverage_plans.*.freqOccurence.in' => 'Invalid frequency of occurrence.',
                'coverage_plans.*.planExpireDays.required' => 'Expiration days are required.',
                'coverage_plans.*.planExpireDays.integer' => 'Expiration days should be an integer.',
                'coverage_plans.*.planExpireDays.min' => 'Expiration days should be at least 0.',

                'renewal_plans.*.PlanName.required' => 'Plan name is required.',
                'renewal_plans.*.PlanName.unique' => 'Plan name already exists.',
                'renewal_plans.*.PlanName.max' => 'Plan name should not exceed 50 characters.',
                'renewal_plans.*.PlanPrice.required' => 'Plan price is required.',
                'renewal_plans.*.PlanPrice.numeric' => 'Plan price should be a numeric value.',
                'renewal_plans.*.PlanPrice.min' => 'Plan price should be at least 0.',
                'renewal_plans.*.PlanDeductPrice.numeric' => 'Deductible price should be a numeric value.',
                'renewal_plans.*.PlanDeductPrice.min' => 'Deductible price should be at least 0.',
                'renewal_plans.*.freqOccurence.required' => 'Frequency of occurrence is required.',
                'renewal_plans.*.freqOccurence.in' => 'Invalid frequency of occurrence.',
                'renewal_plans.*.planExpireDays.required' => 'Expiration days are required.',
                'renewal_plans.*.planExpireDays.integer' => 'Expiration days should be an integer.',
                'renewal_plans.*.planExpireDays.min' => 'Expiration days should be at least 0.',
            ]);
            /* If Validations fails */
            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $validator->errors()
                ], 200);
            }
            /* Total Device Plan according to device model and plan type */
            /* Proceed with creating/updating plans */
            $deviceModelId = $request->selectedDeviceModel;
            $deviceModelName = $request->selectedDeviceModelName;
            $planType = $request->devicePlanType;
            $devicePlans = $request->devicePlanType == 1 ? $request->coverage_plans : $request->renewal_plans;

           /** Stripe Secret key */
            $stripeSecretKey = trim(SDCOptionController::getOption('sdcsm_stripe_secret_key'), '"');

            if (empty($stripeSecretKey)) {
                return response()->json(["msg" => "Stripe secret key missing.", "success" => false], 200);
            }

            /* Dynamically override the Cashier key */
            config(['cashier.secret' => $stripeSecretKey]);
            Stripe::setApiKey(config('cashier.secret'));

            foreach ($devicePlans as $plan) {
                $existingPlan = isset($plan['planId']) ? DevicePlan::find($plan['planId']) : null;
                $stripeProductId = $existingPlan ? $existingPlan->stripe_product_id : null;
                $stripePriceId = $existingPlan ? $existingPlan->stripe_price_id : null;
                if ($plan['freqOccurence'] != 1) {
                    // Create or Update Stripe Product
                    if (!$stripeProductId) {
                        $stripeProduct = Product::create([
                            'name' => $plan['PlanName'],
                            'description' => 'Plan for Device model ' . ($deviceModelName ?? ''),
                        ]);
                        $stripeProductId = $stripeProduct->id;
                    } else {
                        // Update existing product if needed (e.g., name, description)
                        $stripeProduct = Product::retrieve($stripeProductId);
                        Product::update($stripeProductId, [
                            'name' => $plan['PlanName'],
                            'description' => 'Plan for Device model ' . ($deviceModelName ?? ''),
                        ]);
                    }

                    // Step 2: Create Stripe Price (convert to cents)
                   $needsNewStripePrice = !$existingPlan || $existingPlan->price != $plan['PlanPrice'] || $existingPlan->freq_occurence != $plan['freqOccurence'];

                    if (!$stripePriceId || $needsNewStripePrice) {
                        /* if ($plan['freqOccurence'] == 1) {
                            $stripePrice = Price::create([
                                'product' => $stripeProductId,
                                'unit_amount' => intval($plan['PlanPrice'] * 100),
                                'currency' => 'usd',
                            ]);
                        } else { */
                            [$interval, $intervalCount] = match ($plan['freqOccurence']) {
                                2 => ['month', 1],
                                3 => ['month', 3],
                                4 => ['month', 6],
                                5 => ['year', 1],
                                default => ['month', 1],
                            };

                            $stripePrice = Price::create([
                                'product' => $stripeProductId,
                                'unit_amount' => round($plan['PlanPrice'] * 100),
                                'currency' => 'usd',
                                'recurring' => [
                                    'interval' => $interval,
                                    'interval_count' => $intervalCount,
                                ],
                            ]);
                       /*  } */

                        $stripePriceId = $stripePrice->id;
                    }
                }


                // Step 3: Update or Create the device plan
                $searchCriteria = isset($plan['planId']) && $plan['planId']
                    ? ['id' => $plan['planId']]
                    : [
                        'device_model_id' => $deviceModelId,
                        'plan_type' => $planType,
                        'plan_name' => $plan['PlanName'],
                        'service_provider_id' => $serviceProvider
                    ];

                $planData = [
                    'device_model_id' => $deviceModelId,
                    'plan_type' => $planType,
                    'plan_name' => $plan['PlanName'],
                    'price' => $plan['PlanPrice'],
                    'deductible_price' => $plan['PlanDeductPrice'],
                    'freq_occurence' => $plan['freqOccurence'],
                    // 'expiration_days' => $plan['planExpireDays'],
                    'expiration_days' => $plan['freqOccurence'] == 1
                                         ? $plan['planExpireDays']
                                         : match ($plan['freqOccurence']) {
                                             2 => 30,    /* Monthly */
                                             3 => 90,    /* Quarterly */
                                             4 => 180,   /* Half-Yearly */
                                             5 => 365,   /* Yearly */
                                             default => 30,
                                         },
                    'service_provider_id' => $serviceProvider,
                    'stripe_product_id' => $stripeProductId,
                    'stripe_price_id' => $stripePriceId,
                ];

                DevicePlan::updateOrCreate($searchCriteria, $planData);
            }
            return response()->json([
                "msg" => "Device Plan(s) Managed successfully.",
                "success" => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Show Device Plan */
    public function show($id)
    {
        if (!empty($id)) {
            $where = [
                'where' => [
                    'device_plans.id' => $id,
                ]
            ];
            $viewData = self::getAllDevicePlansQuery($where)->first();
            if (!empty($viewData)) {
                return response()->json(["viewData" => $viewData, "msg" => "Device Plan retrieved successfully.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
            }
        } else {
            return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
        }
    }

    /**Edit Device Plan */
    public function edit($id)
    {
        if (!empty($id)) {
            $where = [
                'where' => [
                    'device_plans.id' => $id,
                ]
            ];
            $devicePlan = self::getAllDevicePlansQuery($where)->first();
            if (!empty($devicePlan)) {
                return response()->json(["editData" => $devicePlan, "msg" => "Device Plan retrieved successfully.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }
        } else {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Updates a device plan */
    public function update(Request $request, $id)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate request data */
            $validator = Validator::make($request->all(), [
                'devicePlanType' => 'required|in:1,2',
                'deviceModel' => 'required|exists:device_models,id,active,1,service_provider_id,' . $serviceProvider,
                'planName' => 'required|string|max:50|unique:device_plans,plan_name,' . $request->id . ',id,service_provider_id,' . $serviceProvider . ',device_model_id,' . $request->selectedDeviceModel . ',active,1,plan_type,' . $request->devicePlanType,
                'planPrice' => 'required|numeric|min:0',
                'planDeductPrice' => 'nullable|numeric|min:0',
                'freqOccurence' => 'required|in:1,2,3,4,5',
                'planExpireDays' => 'required|integer|min:0',
            ], [
                'devicePlanType.required' => 'Device plan type is required.',
                'devicePlanType.in' => 'Invalid device plan type.',
                'deviceModel.required' => 'Device model is required.',
                'deviceModel.exists' => 'Invalid device model.',
                'planName.required' => 'Plan name is required.',
                'planName.max' => 'Plan name should not exceed 50 characters.',
                'planName.unique' => 'Plan name already exists for this device model.',
                'planPrice.required' => 'Plan price is required.',
                'planPrice.numeric' => 'Plan price should be a numeric value.',
                'planPrice.min' => 'Plan price should be at least 0.',
                'planDeductPrice.numeric' => 'Deductible price should be a numeric value.',
                'planDeductPrice.min' => 'Deductible price should be at least 0.',
                'freqOccurence.required' => 'Frequency of occurrence is required.',
                'freqOccurence.in' => 'Invalid frequency of occurrence.',
                'planExpireDays.required' => 'Expiration days are required.',
                'planExpireDays.integer' => 'Expiration days should be an integer.',
                'planExpireDays.min' => 'Expiration days should be at least 0.',
            ]);
            /* If Validations fails */
            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $validator->errors()
                ], 200);
            }
            /* Update Device Plan */
            $devicePlan = DevicePlan::where('id', $id)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
            if (!empty($devicePlan)) {
                $planUpdated = $devicePlan->update([
                    'device_model_id' => $request->deviceModel,
                    'plan_type' => $request->devicePlanType,
                    'plan_name' => $request->planName,
                    'price' => $request->planPrice,
                    'deductible_price' => $request->planDeductPrice,
                    'freq_occurence' => $request->freqOccurence,
                    'expiration_days' => $request->planExpireDays,
                ]);
                if (!empty($planUpdated)) {
                    return response()->json(["msg" => "Device Plan updated successfully.", "success" => true], 200);
                } else {
                    return response()->json(["msg" => "Device Plan updatation failed.", "success" => false], 200);
                }
            } else {
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Delete a device plan */
    public function destroy($id)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Find the device plan */
            $devicePlan = DevicePlan::where('id', $id)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
            /* Check If Device Model is not Empty */
            if (!empty($devicePlan)) {
                /* Toggle active status */
                $devicePlan->active = $devicePlan->active == 1 ? 0 : 1;
                $devicePlan->save();
                /* Set the same active status for related device and organizations */
                /*  Device::where('device_plan_id', $id)->where('service_provider_id', $serviceProvider)->where('active', 1)->update(['active' => $devicePlan->active]);
                OrgAllowedModel::where('device_plan_id', $id)->where('service_provider_id', $serviceProvider)->where('active', 1)->update(['active' => $devicePlan->active]);
                OrgAllowedRenewalModel::where('device_plan_id', $id)->where('service_provider_id', $serviceProvider)->where('active', 1)->update(['active' => $devicePlan->active]); */
                /* Dynamic message based on new active status */
                $message = $devicePlan->active == 1 ? 'Activated' : 'Deactivated';
                return response()->json(["msg" => "Device Plan {$message} successfully.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Device Plan not found.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** get all device plans (query) */
    private static function getAllDevicePlansQuery($where = [], $fields = [])
    {
        $serviceProvider = Session::get('service_provider');
        $query = DB::table('device_plans')
            ->leftJoin('device_models as deviceModel', 'device_plans.device_model_id', '=', 'deviceModel.id')
            ->leftJoin('device_brands as deviceBrand', 'deviceModel.device_brand_id', '=', 'deviceBrand.id')
            ->leftJoin('device_families as deviceFamily', 'deviceModel.device_family_id', '=', 'deviceFamily.id')
            ->where('device_plans.insured_uninsured_devices', '=', 1)
            ->where('device_plans.service_provider_id', $serviceProvider)
            ->where('device_plans.active', 1);
           /*  ->where(function ($query) use ($serviceProvider) {
                $query->whereNull('device_plans.device_model_id')
                    ->orWhere(function ($query) use ($serviceProvider) {
                        $query->where('deviceModel.active', 1)
                            ->where('deviceModel.service_provider_id', $serviceProvider)
                            ->where(function ($query) use ($serviceProvider) {
                                $query->whereNull('deviceModel.device_family_id')
                                    ->orWhere('deviceFamily.active', 1)
                                    ->where('deviceFamily.service_provider_id', $serviceProvider);
                            })
                            ->where(function ($query) use ($serviceProvider) {
                                $query->whereNull('deviceModel.device_brand_id')
                                    ->orWhere('deviceBrand.active', 1)
                                    ->where('deviceBrand.service_provider_id', $serviceProvider);
                            });
                    });
            }); */
        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select(
                'device_plans.id',
                'device_plans.device_model_id',
                'device_plans.plan_type',
                'device_plans.plan_name',
                'device_plans.price',
                'device_plans.deductible_price',
                'device_plans.expiration_days',
                'device_plans.freq_occurence',
                'device_plans.insured_uninsured_devices',
                'device_plans.active',
                'deviceFamily.name as device_family_name',
                'deviceBrand.name as device_brand_name',
                'deviceModel.title as device_model_name',
            );
        }
        if (!empty($where) && is_array($where)) {
            foreach ($where as $field => $value) {
                if (is_array($value)) {
                    if ($field == 'orWhere') {
                        /* For 'orWhere', wrap conditions in a closure */
                        $query->where(function ($query) use ($value) {
                            foreach ($value as $secondField => $secondValue) {
                                if (is_array($secondValue)) {
                                    /* Handle [operator, value] format correctly */
                                    $query->orWhere($secondField, $secondValue[0], $secondValue[1]);
                                } else {
                                    $query->orWhere($secondField, $secondValue);
                                }
                            }
                        });
                    } else {
                        /* For 'where', 'whereIn', etc., handle normally */
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
        }
        return $query->orderBy('device_plans.created_at', 'asc');
    }

    /* get all devices plans list with paginate */
    public static function getPaginateDevicePlans($limit = 20, $where = [], $fields = [])
    {
        $defaultWhere = [
            'where' => [
                'device_plans.device_model_id' => ['!=', null],
            ]
        ];
        $where = array_merge_recursive($defaultWhere, $where);
        $data = self::getAllDevicePlansQuery($where, $fields);
        $limit = (is_numeric($limit) && $limit > 0) ? $limit : 20;
        return $data->paginate($limit);
    }

    /* get all devices plans  list without paginate */
    public static function getDevicePlans($where = [], $fields = [])
    {
        /* $where['where']['device_plans.active'] = 1; */
        $data = self::getAllDevicePlansQuery($where, $fields);
        return $data->get();
    }

    /** Device Plan according to device model */
    public function getDevicePlan(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'modelId' => 'nullable|exists:device_models,id,active,1,service_provider_id,' . $serviceProvider,
                'planType' => 'required|in:1,2',
                'planId' => 'nullable|exists:device_plans,id,active,1,service_provider_id,' . $serviceProvider,
                'orgId' => 'nullable|exists:organizations,id,active,1,service_provider_id,' . $serviceProvider,
            ]);
            /* If validation's fails */
            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $validator->errors()
                ], 200);
            }
            $planType = $request->planType ?? 1;
            $where = [
                'where' => [
                    'device_plans.plan_type' => $planType,
                ]
            ];
            /* If Plan Id is given */
            if (!empty($request->planId)) {
                $where['where']['device_plans.id'] = $request->planId;
            }

            if (!empty($request->modelId)) {
                $where['where']['device_plans.device_model_id'] = $request->modelId;
            }
            $planData = self::getDevicePlans($where);
            /* If No Plan found */
            /* if ($planData->isEmpty()) {
                $where['where']['device_plans.device_model_id'] = null;
                $planData = self::getDevicePlans($where);
            } */

            if (!empty($request->orgId)) {
                $orgId = $request->orgId;
                if (!is_numeric($orgId)) {
                    return response()->json([
                        'success' => false,
                        'msg' => "Invalid Organization ID"
                    ], 200);
                } else {
                    if ($planType == 1) {
                        /* Fetch Organizations Plan based on device plan ID and Service Provider ID  (Available Devices)*/
                        $orgDeviceModelPlans = OrgAllowedModel::select('id', 'device_plan_id', 'expiration_date')->where('org_id', $orgId)->where('service_provider_id', $serviceProvider)->where('active', 1)->get();
                        if (empty($orgDeviceModelPlans)) {
                            return response()->json([
                                'success' => false,
                                'msg' => "No Device Model Plans are found in this Organization"
                            ], 200);
                        } else {
                            /* Fetch Device Models based on Model IDs */
                            $devicePlans = $planData->filter(function ($plan) use ($orgDeviceModelPlans) {
                                return in_array($plan->id, $orgDeviceModelPlans->pluck('device_plan_id')->toArray());
                            })->map(function ($plan) use ($orgDeviceModelPlans) {
                                // Find corresponding org model plan
                                $orgPlan = $orgDeviceModelPlans->firstWhere('device_plan_id', $plan->id);
                                $plan->expiration_date = $orgPlan->expiration_date ?? null;
                                $plan->expiration_days = null;
                                return $plan;
                            })->values(); /* Resetting array keys */
                            if (empty($devicePlans)) {
                                return response()->json([
                                    'success' => false,
                                    'msg' => "No Device Model Plans (Available) are found for selected Organization"
                                ], 200);
                            } else {
                                return response()->json([
                                    'success' => true,
                                    'deviceModelPlans' => $devicePlans
                                ], 200);
                            }
                        }
                    }

                    if ($planType == 2) {
                        /* Fetch Organizations Plan based on device plan ID and Service Provider ID  (Renewal Devices)*/
                        $orgDeviceModelPlans = OrgAllowedRenewalModel::select('id', 'device_plan_id', 'expiration_date')->where('org_id', $orgId)->where('service_provider_id', $serviceProvider)->where('active', 1)->get();
                        if (empty($orgDeviceModelPlans)) {
                            return response()->json([
                                'success' => false,
                                'msg' => "No Device Model Plans are found in this Organization"
                            ], 200);
                        } else {
                            /* Fetch Device Models based on Model IDs */
                            $devicePlans = $planData->filter(function ($plan) use ($orgDeviceModelPlans) {
                                return in_array($plan->id, $orgDeviceModelPlans->pluck('device_plan_id')->toArray());
                            })->map(function ($plan) use ($orgDeviceModelPlans) {
                                // Find corresponding org model plan
                                $orgPlan = $orgDeviceModelPlans->firstWhere('device_plan_id', $plan->id);
                                $plan->expiration_date = $orgPlan->expiration_date ?? null;
                                $plan->expiration_days = null;
                                return $plan;
                            })->values(); /* Resetting array keys */
                            if (empty($devicePlans)) {
                                return response()->json([
                                    'success' => false,
                                    'msg' => "No Device Model Plans (Renewal) are found for selected Organization"
                                ], 200);
                            } else {
                                return response()->json([
                                    'success' => true,
                                    'deviceModelPlans' => $devicePlans
                                ], 200);
                            }
                        }
                    }
                }
            } else {
                return response()->json([
                    'success' => true,
                    'deviceModelPlans' => $planData
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => "Something went wrong."
            ], 200);
        }
    }
    /** Get One-Time Device Plan according to device model */
    public function getOneTimeDevicePlan(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'modelId' => 'nullable|exists:device_models,id,active,1,service_provider_id,' . $serviceProvider,
                'planType' => 'required|in:1,2',
                'planId' => 'nullable|exists:device_plans,id,active,1,service_provider_id,' . $serviceProvider,
                'orgId' => 'nullable|exists:organizations,id,active,1,service_provider_id,' . $serviceProvider,
            ]);
            /* If validation's fails */
            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $validator->errors()
                ], 200);
            }
            $planType = $request->planType ?? 1;
            $where = [
                'where' => [
                    'device_plans.plan_type' => $planType,
                    'device_plans.freq_occurence' => 1,
                ]
            ];
            /* If Plan Id is given */
            if (!empty($request->planId)) {
                $where['where']['device_plans.id'] = $request->planId;
            }

            if (!empty($request->modelId)) {
                $where['where']['device_plans.device_model_id'] = $request->modelId;
            }
            $planData = self::getDevicePlans($where);

            if (!empty($request->orgId)) {
                $orgId = $request->orgId;
                if (!is_numeric($orgId)) {
                    return response()->json([
                        'success' => false,
                        'msg' => "Invalid Organization ID"
                    ], 200);
                } else {
                    if ($planType == 1) {
                        /* Fetch Organizations Plan based on device plan ID and Service Provider ID  (Available Devices)*/
                        $orgDeviceModelPlans = OrgAllowedModel::select('id', 'device_plan_id')->where('org_id', $orgId)->where('service_provider_id', $serviceProvider)->where('active', 1)->get();
                        if (empty($orgDeviceModelPlans)) {
                            return response()->json([
                                'success' => false,
                                'msg' => "No Device Model Plans are found in this Organization"
                            ], 200);
                        } else {
                            /* Fetch Device Models based on Model IDs */
                            $devicePlans = $planData->filter(function ($plan) use ($orgDeviceModelPlans) {
                                return in_array($plan->id, $orgDeviceModelPlans->pluck('device_plan_id')->toArray());
                            })->values(); /* Resetting array keys */
                            if (empty($devicePlans)) {
                                return response()->json([
                                    'success' => false,
                                    'msg' => "No Device Model Plans (Available) are found for selected Organization"
                                ], 200);
                            } else {
                                return response()->json([
                                    'success' => true,
                                    'deviceModelPlans' => $devicePlans
                                ], 200);
                            }
                        }
                    }

                    if ($planType == 2) {
                        /* Fetch Organizations Plan based on device plan ID and Service Provider ID  (Renewal Devices)*/
                        $orgDeviceModelPlans = OrgAllowedRenewalModel::select('id', 'device_plan_id')->where('org_id', $orgId)->where('service_provider_id', $serviceProvider)->where('active', 1)->get();
                        if (empty($orgDeviceModelPlans)) {
                            return response()->json([
                                'success' => false,
                                'msg' => "No Device Model Plans are found in this Organization"
                            ], 200);
                        } else {
                            /* Fetch Device Models based on Model IDs */
                            $devicePlans = $planData->filter(function ($plan) use ($orgDeviceModelPlans) {
                                return in_array($plan->id, $orgDeviceModelPlans->pluck('device_plan_id')->toArray());
                            })->values(); /* Resetting array keys */
                            if (empty($devicePlans)) {
                                return response()->json([
                                    'success' => false,
                                    'msg' => "No Device Model Plans (Renewal) are found for selected Organization"
                                ], 200);
                            } else {
                                return response()->json([
                                    'success' => true,
                                    'deviceModelPlans' => $devicePlans
                                ], 200);
                            }
                        }
                    }
                }
            } else {
                return response()->json([
                    'success' => true,
                    'deviceModelPlans' => $planData
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => "Something went wrong."
            ], 200);
        }
    }

    /* Device Plan Count (according to device model) */
    private function getDevicePlanCount($deviceModelId, $devicePlanType)
    {
        /* Get Service Provider id using session */
        $serviceProvider = Session::get('service_provider');
        $data = DevicePlan::where('device_model_id', $deviceModelId)->where('plan_type', $devicePlanType)->where('insured_uninsured_devices', '=', 1)->where('service_provider_id', $serviceProvider)->where('active', 1)->count();
        return $data;
    }
    /** For Filter's */
    private function getFilter(Request $request)
    {
        $where = [];
        if (!empty($request->search) || !empty($request->planType)) {
            /* Search By Plan Name, Family Name , Brand Name, Model Title*/
            if (!empty($request->search)) {
                $where = [
                    'orWhere' => [
                        'device_plans.plan_name' => ['LIKE', "%{$request->search}%"],
                        'deviceFamily.name' => ['LIKE', "%{$request->search}%"],
                        'deviceBrand.name' => ['LIKE', "%{$request->search}%"],
                        'deviceModel.title' => ['LIKE', "%{$request->search}%"],
                    ]
                ];
            }
            /*  Search By Device PLan Type */
            if (!empty($request->planType)) {
                $where['where']['device_plans.plan_type'] = $request->planType;
            }
        }
        return $where;
    }

    /** Getting plans of devices while managing the plans in device plan module */
    public function getDevicePlanData(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'modelId' => 'required|exists:device_models,id,active,1,service_provider_id,' . $serviceProvider,
                'planType' => 'required|in:1,2',
            ]);
            /* If validation's fails */
            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $validator->errors()
                ], 200);
            }
            $planType = $request->planType ?? 1;
            $where = [
                'where' => [
                    'device_plans.plan_type' => $planType,
                ]
            ];
            if (!empty($request->modelId)) {
                $where['where']['device_plans.device_model_id'] = $request->modelId;
            }
            $planData = self::getDevicePlans($where);
            if ($planData->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'deviceModelPlans' => []
                ], 200);
            } else {
                return response()->json([
                    'success' => true,
                    'deviceModelPlans' => $planData
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => "Something went wrong."
            ], 200);
        }
    }

    /** Single Device Plan Get */
    public static function getSingleDevicePlanById($id, $fields = []){
        $serviceProvider = Session::get('service_provider');
        $devicePlanQuery = DB::table('device_plans')
            ->leftJoin('device_models as deviceModel', 'device_plans.device_model_id', '=', 'deviceModel.id')
            ->where('device_plans.id', $id)
            ->where('device_plans.insured_uninsured_devices', '=', 1)
            ->where('device_plans.service_provider_id', $serviceProvider)
            ->where('device_plans.active', 1);

        if (!empty($fields) && is_array($fields)) {
            $devicePlanQuery->select($fields);
        } else {
            $devicePlanQuery->select(
                'device_plans.id',
                'device_plans.device_model_id',
                'device_plans.plan_type',
                'device_plans.plan_name',
                'device_plans.price',
                'device_plans.deductible_price',
                'device_plans.expiration_days',
                'device_plans.freq_occurence',
                'device_plans.insured_uninsured_devices',
                'device_plans.active',
                'deviceModel.title as device_model_name',
            );
        }
        $devicePlan = $devicePlanQuery->first();
        return $devicePlan;
    }

    public static function getPlansByPlanId($planId, $modelId, $orgId, $serviceProvider, $fields = [])
    {
        /* Primary plan fetch */
        $devicePlanQuery = DB::table('device_plans')
                           ->leftJoin('device_models as deviceModel', 'device_plans.device_model_id', '=', 'deviceModel.id')
                           ->where('device_plans.id', $planId)
                           ->where('device_plans.service_provider_id', $serviceProvider)
                           ->where('device_plans.active', 1)
                           ->where('deviceModel.service_provider_id', $serviceProvider);


        if (!empty($fields) && is_array($fields)) {
            $devicePlanQuery->select($fields);
        } else {
            $devicePlanQuery->select(
                'device_plans.id',
                'device_plans.plan_name',
                'device_plans.price',
                'device_plans.deductible_price',
                'device_plans.freq_occurence',
                'device_plans.plan_type',
                'device_plans.stripe_product_id',
                'device_plans.stripe_price_id',
                'device_plans.expiration_days',
                'deviceModel.title as device_model_name',
            );
        }
        $plan = $devicePlanQuery->first();

        $orgPlan = OrgAllowedModel::where('device_plan_id', $planId)->where('org_id', $orgId)->where('model_id', $modelId)->where('service_provider_id', $serviceProvider)->where('active', 1)->first();

        /* Check if organization has specific device plans */
        if (!empty($orgPlan)) {
            // Override default values with organization-specific ones
            $plan->price = $orgPlan->coverage_price;
            $plan->deductible_price = $orgPlan->deductible_price;
            $plan->expiration_days = $orgPlan->expiration_days;
            $plan->stripe_product_id = $orgPlan->stripe_product_id;
            $plan->stripe_price_id = $orgPlan->stripe_price_id;

            return $plan;
        }

        /* Return default plan with filtered fields */
        return $plan;
    }
}
