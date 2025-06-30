<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Admin\SDCOptionController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Models\Admin\Device;
use App\Models\Admin\DevicePlan;
use App\Models\Admin\OrgAllowedRenewalModel;
use App\Models\Transaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Laravel\Cashier\Subscription;

class RenewDevicesController extends Controller
{
    /**
     * Renewal Devices List
     */
    public function renewalDevicesIndex()
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $stripePublicKey = SDCOptionController::getOption('sdcsm_stripe_publishable_key', $serviceProvider);
        config(['cashier.key' => $stripePublicKey]);
        $deviceData = self::renewalDevices();
        return view('user.devices.renewal_device_list', compact('deviceData', 'stripePublicKey'));
    }

    /* Get All Renewal Devices(without pagination). */
    public static function renewalDevices($where = [])
    {
        $defaultFields = [
            'devices.id',
            'devices.device_model_id',
            'devices.org_id',
            'devices.sub_org_id',
            'devices.device_title',
            'devices.serial_number',
            'devices.device_type',
            'devices.payment_added_date',
            'devices.expiration_date',
            'devices.created_at',
            'deviceModel.title as device_model_name',
        ];

        $serviceProvider = Session::get('service_provider');
        $authUser = Session::get('auth_user');
        $devicesData = DB::table('devices')
            ->leftJoin('device_models as deviceModel', 'devices.device_model_id', '=', 'deviceModel.id')
            ->select($defaultFields)
            ->where('devices.active', 1)
            ->where('devices.service_provider_id', $serviceProvider)
            ->where('devices.user_id', $authUser->id)
            ->where('expiration_date', '<', date('Y-m-d'))->get();
        return $devicesData;
    }

    /** Get renewal plans by device model id */
    public function getRenewalPlansByModelId(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'modelId' => 'required|exists:device_models,id,active,1,service_provider_id,' . $serviceProvider,
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
            // $devicePlanFields = [
            //     'id',
            //     'plan_name',
            //     'price',
            // ];
            /** Plans Data */
            $modelPlansData = $this->getPlansByModelId($request->modelId, $request->orgId, $serviceProvider);
            return response()->json([
                'deviceModelPlans' => $modelPlansData,
                'success' => true
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'msg' => "Something went wrong, please try again later." . $e->getMessage()], 200);
        }
    }

    /** Renew devices data store */
    public function renewNow(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Step 1: Validate Request */
            $validator = Validator::make($request->all(), [
                'devices' => 'required|array',
                'cardHolderName' => 'required|string|max:50',

            ], [
                'devices.required' => 'Devices are required.',
                'cardHolderName.required' => 'Card holder name is required.',
                'cardHolderName.string' => 'Card holder name must be a string.',
            ]);

            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }

            /* Step 1: Configure Stripe */
            $stripeSecretKey = trim(SDCOptionController::getOption('sdcsm_stripe_secret_key', $serviceProvider), '"');
            if (empty($stripeSecretKey)) {
                return response()->json(["msg" => "Something went wrong, please try again later.", "success" => false], 200);
            }
            config(['cashier.secret' => $stripeSecretKey]);

            /* Step 2: Create Stripe Customer (temp user for billing first) */
            $tempUser = Session::get('auth_user');
            $tempUser->setRelation('subscriptions', collect());
            $tempUser->createOrGetStripeCustomer();
            $tempUser->updateDefaultPaymentMethod($request->paymentMethodId);
            $isPaymentSucess = false;
            $subscriptionIds = [];
            $transactionIds = [];
            $successfulPayments = [];
            /* Step 3: Process payment/subscription before DB save */
            foreach ($request->devices as $device) {
                $planData = $this->getPlansById($device['selectedPlan'], $device['modelId'], $device['orgId'], $serviceProvider);
                if (empty($planData)) {
                    return response()->json(["msg" => "Something went wrong, please try again later.", "success" => false], 200);
                }
                if ($planData->freq_occurence == 1) {
                    /* One-time charge */
                    $payment = $tempUser->charge($planData->price * 100, $request->paymentMethodId, [
                        'payment_method_types' => ['card'],
                        'off_session' => true,
                        'confirm' => true,
                        'description' => 'Device Coverage Renew.',
                        'metadata' => [
                            'payment_for' => 1,
                            'service_provider_id' => $serviceProvider,
                        ],
                    ]);
                    if ($payment->status == 'succeeded') {
                        $isPaymentSucess = true;
                        $transactionIds[$device['deviceId']] = $payment->id;
                    }
                } elseif (!empty($planData->stripe_price_id) && ($planData->freq_occurence == 2 || $planData->freq_occurence == 3 || $planData->freq_occurence == 4 || $planData->freq_occurence == 5)) {
                    $subscription = $tempUser->newSubscription($planData->plan_name, $planData->stripe_price_id)->create($request->paymentMethodId, [
                        'description' => 'Device Coverage Renew Subscription for ' . $planData->plan_name,
                        'metadata' => [
                            'payment_for' => 1,
                            'service_provider_id' => $request->serviceProvider,
                        ]
                    ]);
                    if ($subscription->stripe_status == 'active') {
                        $isPaymentSucess = true;
                        $subscriptionIds[$device['deviceId']] = $subscription->stripe_id;
                    }
                }
                /*  else {
                    $isPaymentSucess = false;
                } */
                if ($isPaymentSucess) {
                    $successfulPayments[] = $device;
                }
            }
            /* Step 4: Check if we have any successful payments */
            if (empty($successfulPayments)) {
                return response()->json(["success" => false, "msg" => "Payment failed"], 200);
            } else {
                /* Step 5: Now save device data AFTER payment succeeds */

                $userId = $tempUser->id;

                foreach ($successfulPayments as $device) {
                    $planData = $this->getPlansById($device['selectedPlan'], $device['modelId'], $device['orgId'], $serviceProvider);
                    if (!$planData) {
                        return false;
                    }
                    $deviceRecord = Device::where('id', $device['deviceId'])
                        ->where('user_id', $userId)
                        ->where('active', 1)
                        ->where('service_provider_id', $serviceProvider)
                        ->first();

                    if ($deviceRecord) {
                        $deviceRecord->plan_id = $planData->id ?? null;
                        $deviceRecord->subscription_id = $subscriptionIds[$device['deviceId']] ?? null;
                        $deviceRecord->transaction_id = $transactionIds[$device['deviceId']] ?? null;
                        $deviceRecord->payment_added_date = Carbon::now();
                        $deviceRecord->expiration_date = isset($planData->expiration_days)  ? Carbon::now()->addDays((int) $planData->expiration_days) : $planData->expiration_date;
                        $deviceRecord->save();

                        // Now you can safely use $deviceRecord->id
                        if (!empty($deviceRecord->transaction_id)) {
                            $transactionUpdate = Transaction::where('stripe_transaction_id', $deviceRecord->transaction_id)->first();
                            if ($transactionUpdate) {
                                $transactionUpdate->device_id = $deviceRecord->id;
                                $transactionUpdate->save();
                            }
                        }

                        if (!empty($deviceRecord->subscription_id)) {
                            $subscriptionUpdate = Transaction::where('stripe_transaction_id', $deviceRecord->subscription_id)->first();
                            if ($subscriptionUpdate) {
                                $subscriptionUpdate->device_id = $deviceRecord->id;
                                $subscriptionUpdate->save();
                            }
                        }


                        $notificationRequest = [
                            'user_id' => $deviceRecord->user_id,
                            'message' => ($deviceRecord->org_user_full_name ?? $deviceRecord->device_owner_name) . " has renew a device: " . $deviceRecord->device_title,
                            'notification_for' => 'device_added',
                            'device_id' => $deviceRecord->id,
                            'org_id' => null,
                            'sub_org_id' => null,
                            'user_target_link' => 'sdcsmuser/device-list',
                            'admin_target_link' => 'smarttiusadmin/devices',
                            'service_provider_id' => $deviceRecord->service_provider_id,
                        ];
                        NotificationController::addNotification($notificationRequest);
                    }
                }
                /* Step 6: Done */
                return response()->json([
                    "msg" => "Device's Renewed Successfully!",
                    "success" => true
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json(['success' => false, 'msg' => "Something went wrong, please try again later.". $e->getMessage()], 200);
        }
    }

    /** Get Renewal plans using device model id or org id*/
    private function getPlansByModelId($modelId, $orgId, $serviceProvider)
    {
        $devicePlanFields = [
            'id',
            'plan_name',
            'price',
        ];

        /** OrgID is not empty */
        if (!empty($orgId)) {
            $orgDeviceModelPlans = DB::table('org_allowed_renewal_models')
                ->leftJoin('device_plans', 'org_allowed_renewal_models.device_plan_id', '=', 'device_plans.id')
                ->select([
                    'org_allowed_renewal_models.device_plan_id as id',
                    'device_plans.plan_name',
                    'org_allowed_renewal_models.coverage_price as price',
                    'org_allowed_renewal_models.expiration_date'
                ])
                ->where('org_allowed_renewal_models.org_id', $orgId)
                ->where('org_allowed_renewal_models.model_id', $modelId)
                ->where('org_allowed_renewal_models.active', 1)
                ->where('org_allowed_renewal_models.service_provider_id', $serviceProvider)
                ->get();
            return $orgDeviceModelPlans;
        } else {
            $plans = DevicePlan::select($devicePlanFields)->where('device_model_id', $modelId)->where('plan_type', 2)->where('active', 1)->where('service_provider_id', $serviceProvider)->get();
            return $plans;
        }
    }


    /** Get Renewal Plan using plan id */
    private function getPlansById($planId, $modelId, $orgId, $serviceProvider)
    {
        $devicePlanFields = [
            'id',
            'plan_name',
            'price',
            'deductible_price',
            'freq_occurence',
            'plan_type',
            'stripe_product_id',
            'stripe_price_id',
            'expiration_days'
        ];
        if (!empty($orgId)) {
            $orgDeviceModelPlan = DB::table('org_allowed_renewal_models')
                ->leftJoin('device_plans', 'org_allowed_renewal_models.device_plan_id', '=', 'device_plans.id')
                ->select([
                    'org_allowed_renewal_models.device_plan_id as id',
                    'org_allowed_renewal_models.coverage_price as price',
                    'org_allowed_renewal_models.expiration_date',
                    'org_allowed_renewal_models.stripe_product_id',
                    'org_allowed_renewal_models.stripe_price_id',
                    'device_plans.freq_occurence',
                ])
                ->where('org_allowed_renewal_models.device_plan_id', $planId)
                ->where('org_allowed_renewal_models.org_id', $orgId)
                ->where('org_allowed_renewal_models.model_id', $modelId)
                ->where('org_allowed_renewal_models.active', 1)
                ->where('org_allowed_renewal_models.service_provider_id', $serviceProvider)
                ->first();
            return $orgDeviceModelPlan;
        } else {
            $plan = DevicePlan::select($devicePlanFields)->where('id', $planId)->where('device_model_id', $modelId)->where('plan_type', 2)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
            return $plan;
        }
    }







    /** Early Renewal */
    public function earlyRenewalIndex()
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $stripePublicKey = SDCOptionController::getOption('sdcsm_stripe_publishable_key', $serviceProvider);
        config(['cashier.key' => $stripePublicKey]);
        $devices = $this->earlyRenewalDevices();
        return view('user.devices.early_renewal_device_list', compact('devices', 'stripePublicKey'));
    }
    /* Get All Early Renewal Devices(without pagination). (Only Personal devices) */
    private function earlyRenewalDevices($where = [])
    {
        $defaultFields = [
            'devices.id',
            'devices.device_model_id',
            'devices.device_title',
            'devices.serial_number',
            'devices.payment_added_date',
            'devices.expiration_date',
            'devices.created_at',
            'deviceModel.title as device_model_name',
        ];

        $serviceProvider = Session::get('service_provider');
        $authUser = Session::get('auth_user');
        $devicesData = DB::table('devices')
            ->leftJoin('device_models as deviceModel', 'devices.device_model_id', '=', 'deviceModel.id')
            ->leftJoin('device_plans', 'devices.plan_id', '=', 'device_plans.id')
            ->select($defaultFields)
            ->where('device_plans.freq_occurence', 1)
            ->where('devices.user_id', $authUser->id)
            ->where('devices.device_type', 1)
            ->where('devices.active', 1)
            ->where('devices.service_provider_id', $serviceProvider)
            ->where('expiration_date', '>=', date('Y-m-d'))->get();
        return $devicesData;
    }


    /** Get Early Renewal Plans by device model id */
    public function getEarlyRenewalPlansByModelId(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'modelId' => 'required|exists:device_models,id,active,1,service_provider_id,' . $serviceProvider,
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
            $devicePlanFields = [
                'id',
                'plan_name',
                'price',
            ];
            /** Plans Data */
            $modelPlansData = DevicePlan::select($devicePlanFields)->where('device_model_id', $request->modelId)->where('freq_occurence', 1)->where('plan_type', 2)->where('active', 1)->where('service_provider_id', $serviceProvider)->get();
            return response()->json([
                'deviceModelPlans' => $modelPlansData,
                'success' => true
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'msg' => "Something went wrong, please try again later."], 200);
        }
    }

    /** Get Early Renewal Plan using plan id */
    private function getEarlyRenewalPlansById($planId, $modelId, $serviceProvider)
    {
        $devicePlanFields = [
            'id',
            'plan_name',
            'price',
            'deductible_price',
            'freq_occurence',
            'plan_type',
            'stripe_product_id',
            'stripe_price_id',
            'expiration_days',
        ];

        /* Primary plan fetch */
        $plan = DevicePlan::select($devicePlanFields)->where('id', $planId)->where('device_model_id', $modelId)->where('freq_occurence', 1)->where('plan_type', 2)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
        return $plan;
    }

    /** Early Renew devices data store */
    public function earlyRenewNow(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Step 1: Validate Request */
            $validator = Validator::make($request->all(), [
                'devices' => 'required|array',
                'cardHolderName' => 'required|string|max:50',

            ], [
                'devices.required' => 'Devices are required.',
                'cardHolderName.required' => 'Card holder name is required.',
                'cardHolderName.string' => 'Card holder name must be a string.',
            ]);

            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }

            /* Step 1: Configure Stripe */
            $stripeSecretKey = trim(SDCOptionController::getOption('sdcsm_stripe_secret_key', $serviceProvider), '"');
            if (empty($stripeSecretKey)) {
                return response()->json(["msg" => "Something went wrong, please try again later.", "success" => false], 200);
            }
            config(['cashier.secret' => $stripeSecretKey]);

            /* Step 2: Create Stripe Customer (temp user for billing first) */
            $tempUser = Session::get('auth_user');
            $tempUser->setRelation('subscriptions', collect());
            $tempUser->createOrGetStripeCustomer();
            $tempUser->updateDefaultPaymentMethod($request->paymentMethodId);
            $isPaymentSucess = false;
            $transactionIds = [];
            $successfulPayments = [];
            /* Step 3: Process payment/subscription before DB save */
            foreach ($request->devices as $device) {
                $planData = $this->getEarlyRenewalPlansById($device['selectedPlan'], $device['modelId'], $serviceProvider);
                if (empty($planData)) {
                    return response()->json(["msg" => "Something went wrong, please try again later.", "success" => false], 200);
                }

                /* One-time charge */
                $payment = $tempUser->charge($planData->price * 100, $request->paymentMethodId, [
                    'payment_method_types' => ['card'],
                    'off_session' => true,
                    'confirm' => true,
                    'description' => 'Device Coverage Renew.',
                    'metadata' => [
                        'payment_for' => 1,
                        'service_provider_id' => $serviceProvider,
                    ],
                ]);
                if ($payment->status == 'succeeded') {
                    $isPaymentSucess = true;
                    $transactionIds[$device['deviceId']] = $payment->id;
                }
                /*  else {
                    $isPaymentSucess = false;
                } */
                if ($isPaymentSucess) {
                    $successfulPayments[] = $device;
                }
            }
            /* Step 4: Check if we have any successful payments */
            if (empty($successfulPayments)) {
                return response()->json(["success" => false, "msg" => "Payment failed"], 200);
            } else {
                /* Step 5: Now save device data AFTER payment succeeds */
                $userId = $tempUser->id;
                foreach ($successfulPayments as $device) {
                    $planData = $this->getEarlyRenewalPlansById($device['selectedPlan'], $device['modelId'], $serviceProvider);
                    if (!$planData) {
                        return false;
                    }
                    $deviceRecord = Device::where('id', $device['deviceId'])
                        ->where('user_id', $userId)
                        ->where('active', 1)
                        ->where('service_provider_id', $serviceProvider)
                        ->first();

                    if (!empty($deviceRecord)) {
                        $deviceRecord->plan_id = $planData->id ?? null;
                        $deviceRecord->transaction_id = $transactionIds[$device['deviceId']] ?? null;
                        // $deviceRecord->payment_added_date = Carbon::now();
                        $deviceRecord->expiration_date = Carbon::parse($deviceRecord->expiration_date)->addDays((int) $planData->expiration_days);
                        $deviceRecord->save();

                        // Now you can safely use $deviceRecord->id
                        if (!empty($deviceRecord->transaction_id)) {
                            $transactionUpdate = Transaction::where('stripe_transaction_id', $deviceRecord->transaction_id)->first();
                            if ($transactionUpdate) {
                                $transactionUpdate->device_id = $deviceRecord->id;
                                $transactionUpdate->save();
                            }
                        }
                        $notificationRequest = [
                            'user_id' => $deviceRecord->user_id,
                            'message' => ($deviceRecord->org_user_full_name ?? $deviceRecord->device_owner_name) . " has early renewed a device: " . $deviceRecord->device_title,
                            'notification_for' => 'device_added',
                            'device_id' => $deviceRecord->id,
                            'org_id' => null,
                            'sub_org_id' => null,
                            'user_target_link' => 'sdcsmuser/device-list',
                            'admin_target_link' => 'smarttiusadmin/devices',
                            'service_provider_id' => $deviceRecord->service_provider_id,
                        ];
                        NotificationController::addNotification($notificationRequest);
                    }
                }
                /* Step 6: Done */
                return response()->json([
                    "msg" => "Device's Renewed Successfully!",
                    "success" => true
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json(['success' => false, 'msg' => "Something went wrong, please try again later." . $e->getMessage()], 200);
        }
    }
}
