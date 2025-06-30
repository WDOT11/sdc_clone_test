<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\DevicePlan;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Stripe\Product;
use Stripe\Price;
use Stripe\Stripe;

class DeviceRepairPlanController extends Controller
{
    public function index(Request $request)
    {
        $where = $this->getFilter($request);
        $planData = self::getPaginateDeviceRepairPlans(20, $where);
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
            return view('admin.repairplanmaster.index', compact('planData', 'pagination'));
        }
    }

    /* get all devices plans list with paginate */
    public static function getPaginateDeviceRepairPlans($limit = 20, $where = [], $fields = [])
    {
        $defaultWhere = [
            'where' => [
                'device_plans.device_model_id' => ['!=', null],
            ]
        ];
        $where = array_merge_recursive($defaultWhere, $where);
        $data = self::getAllDeviceRepairPlansQuery($where, $fields);
        $limit = (is_numeric($limit) && $limit > 0) ? $limit : 20;
        return $data->paginate($limit);
    }

    /** get all device repair plans (query) */
    private static function getAllDeviceRepairPlansQuery($where = [], $fields = [])
    {
        $serviceProvider = Session::get('service_provider');
        $query = DB::table('device_plans')
            ->leftJoin('device_models as deviceModel', 'device_plans.device_model_id', '=', 'deviceModel.id')
            ->leftJoin('device_brands as deviceBrand', 'deviceModel.device_brand_id', '=', 'deviceBrand.id')
            ->leftJoin('device_families as deviceFamily', 'deviceModel.device_family_id', '=', 'deviceFamily.id')
            ->where('device_plans.insured_uninsured_devices', '=', 2)
            ->where('device_plans.service_provider_id', $serviceProvider)
            ->where('device_plans.active', 1);
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
        return $query->orderBy('device_plans.created_at', 'desc');
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
        }
        return $where;
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
            $viewData = self::getAllDeviceRepairPlansQuery($where)->first();
            if (!empty($viewData)) {
                return response()->json(["viewData" => $viewData, "msg" => "Device Plan retrieved successfully.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
            }
        } else {
            return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
        }
    }

    /** Getting repair plans of devices */
    public function getDeviceRepairPlanData(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'modelId' => 'required|exists:device_models,id,active,1,service_provider_id,' . $serviceProvider,
            ]);
            /* If validation's fails */
            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $validator->errors()
                ], 200);
            }
         /*    $where = [
                'where' => [
                    'device_plans.insured_uninsured_devices' => 2,
                ]
            ]; */
            $where['where']['device_plans.device_model_id'] = $request->modelId;
           /*  if (!empty($request->modelId)) {
            } */

            /** Getting device repair plans by passing the where clause here */
            $planData = self::getDevicePlans($where);
            if ($planData->isEmpty()) {
                return response()->json(['success' => true, 'deviceModelPlans' => []], 200);
            } else {
                return response()->json(['success' => true, 'deviceModelPlans' => $planData], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => "Something went wrong."], 200);
        }
    }

    /* get all devices plans  list without paginate */
    public static function getDevicePlans($where = [], $fields = [])
    {
        /* $where['where']['device_plans.active'] = 1; */
        $data = self::getDeviceRepairPlansQuery($where, $fields);
        return $data->get();
    }

    /** Get Single Plan */
    public static function getDeviceSinglePlan($where = [], $fields = [])
    {
        /* $where['where']['device_plans.active'] = 1; */
        $data = self::getDeviceRepairPlansQuery($where, $fields);
        return $data->first();
    }

    /** get all device plans (query) */
    private static function getDeviceRepairPlansQuery($where = [], $fields = [])
    {
        $serviceProvider = Session::get('service_provider');
        $query = DB::table('device_plans')
            ->where('device_plans.insured_uninsured_devices', 2)
            ->where('device_plans.service_provider_id', $serviceProvider)
            ->where('device_plans.active', 1);
        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select(
                'device_plans.id',
                'device_plans.device_model_id',
                'device_plans.plan_type',
                'device_plans.plan_name',
                'device_plans.price',
                'device_plans.insured_uninsured_devices',
                'device_plans.active'
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

        /** Store a newly created resource in storage. */
    public function store(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate request data */
            $validator = Validator::make($request->all(), [
                'selectedDeviceModel' => 'required|exists:device_models,id,active,1,service_provider_id,' . $serviceProvider,
                'repair_plans' => 'required|array|max:1',
                'repair_plans.*.PlanName' => ['required', 'string', 'max:50',
                    function ($attribute, $value, $fail) use ($serviceProvider, $request) {
                        /* Extract the index from the attribute name */
                        preg_match('/repair_plans\.(\d+)\.PlanName/', $attribute, $matches);
                        $currentIndex = $matches[1] ?? null;

                        /* Get the planId if it exists */
                        $planId = isset($request->repair_plans[$currentIndex]['planId'])
                            ? $request->repair_plans[$currentIndex]['planId']
                            : null;

                        /* 1. Check for duplicates within the submitted plans when planId is null */
                        if (!$planId) {
                            /* Check other plan names in the same request */
                            foreach ($request->repair_plans as $index => $plan) {
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
                            ->where('insured_uninsured_devices', 2);

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
                'repair_plans.*.PlanPrice' => 'required|numeric|min:0',
            ], [
                'selectedDeviceModel.required' => 'Device model is required.',
                'selectedDeviceModel.exists' => 'Invalid device model.',

                'repair_plans.*.PlanName.required' => 'Plan name is required.',
                'repair_plans.*.PlanName.unique' => 'Plan name already exists.',
                'repair_plans.*.PlanName.max' => 'Plan name should not exceed 50 characters.',
                'repair_plans.*.PlanPrice.required' => 'Plan price is required.',
                'repair_plans.*.PlanPrice.numeric' => 'Plan price should be a numeric value.',
                'repair_plans.*.PlanPrice.min' => 'Plan price should be at least 0.',
            ]);

            /* If Validations fails */
            if ($validator->fails()) {
                return response()->json([ "msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            /* Total Device Plan according to device model and plan type */
            /* Proceed with creating/updating plans */
            $deviceModelId = $request->selectedDeviceModel;
            $deviceModelName = $request->selectedDeviceModelName;
            $devicePlans = $request->repair_plans;

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
                // if ($plan['freqOccurence'] != 1) {
                //     // Create or Update Stripe Product
                //     if (!$stripeProductId) {
                //         $stripeProduct = Product::create([
                //             'name' => $plan['PlanName'],
                //             'description' => 'Plan for Device model ' . ($deviceModelName ?? ''),
                //         ]);
                //         $stripeProductId = $stripeProduct->id;
                //     } else {
                //         // Update existing product if needed (e.g., name, description)
                //         $stripeProduct = Product::retrieve($stripeProductId);
                //         Product::update($stripeProductId, [
                //             'name' => $plan['PlanName'],
                //             'description' => 'Plan for Device model ' . ($deviceModelName ?? ''),
                //         ]);
                //     }

                //     // Step 2: Create Stripe Price (convert to cents)
                //    $needsNewStripePrice = !$existingPlan || $existingPlan->price != $plan['PlanPrice'] || $existingPlan->freq_occurence != $plan['freqOccurence'];

                //     if (!$stripePriceId || $needsNewStripePrice) {
                //         /* if ($plan['freqOccurence'] == 1) {
                //             $stripePrice = Price::create([
                //                 'product' => $stripeProductId,
                //                 'unit_amount' => intval($plan['PlanPrice'] * 100),
                //                 'currency' => 'usd',
                //             ]);
                //         } else { */
                //             [$interval, $intervalCount] = match ($plan['freqOccurence']) {
                //                 2 => ['month', 1],
                //                 3 => ['month', 3],
                //                 4 => ['month', 6],
                //                 5 => ['year', 1],
                //                 default => ['month', 1],
                //             };

                //             $stripePrice = Price::create([
                //                 'product' => $stripeProductId,
                //                 'unit_amount' => round($plan['PlanPrice'] * 100),
                //                 'currency' => 'usd',
                //                 'recurring' => [
                //                     'interval' => $interval,
                //                     'interval_count' => $intervalCount,
                //                 ],
                //             ]);
                //        /*  } */

                //         $stripePriceId = $stripePrice->id;
                //     }
                // }


                // Step 3: Update or Create the device plan
                $searchCriteria = isset($plan['planId']) && $plan['planId']
                    ? ['id' => $plan['planId']]
                    : [
                        'device_model_id' => $deviceModelId,
                        'plan_name' => $plan['PlanName'],
                        'service_provider_id' => $serviceProvider
                    ];

                $planData = [
                    'device_model_id' => $deviceModelId,
                    'plan_name' => $plan['PlanName'],
                    'price' => $plan['PlanPrice'],
                    'insured_uninsured_devices' => 2,
                    'service_provider_id' => $serviceProvider,
                    'stripe_product_id' => $stripeProductId,
                    'stripe_price_id' => $stripePriceId,
                ];

                DevicePlan::updateOrCreate($searchCriteria, $planData);
            }
            return response()->json(["msg" => "Device Plan Managed Successfully.", "success" => true,], 200);
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }
}
