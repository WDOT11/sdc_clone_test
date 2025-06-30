<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Admin\SDCOptionController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;

use App\Models\Admin\Device;
use App\Models\Admin\DeviceModel;
use App\Models\Admin\DevicePlan;
use App\Models\Admin\OrgAllowedModel;
use App\Models\Admin\Organization;
use App\Models\Admin\SubOrganization;
use App\Models\OrgRelationship;
use App\Models\Transaction;
use App\Models\User;
use App\Models\User\ShippingAddress;
use App\Models\UserMeta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PublicOrgController extends Controller
{
    /** View Organization (new page) for parent's */
    public function viewOrganization(string $slug)
    {
        $where = [];
        $organization = Organization::select('id', 'org_slug', 'org_link', 'name', 'portal_status', 'close_portal_message', 'org_type', 'service_provider_id')->where('org_link', $slug)->first();
        if (empty($organization)) {
            return abort(404, 'Page not found');
        }
        $orgId = $organization->id;
        $subOrgs = SubOrganization::where('org_id', $orgId)->where('service_provider_id', $organization->service_provider_id)->where('active', 1)->get();
        $stripePublicKey = SDCOptionController::getOption('sdcsm_stripe_publishable_key', $organization->service_provider_id);
        config(['cashier.key' => $stripePublicKey]);
        return view('public.organization', compact('organization', 'subOrgs', 'stripePublicKey'));
    }

    /* Get Organization models for public url */


    public function getOrgDeviceModels(Request $request)
    {
        try {
            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'serviceProvider' => 'required|exists:service_providers,id,active,1',
                'name' => 'nullable|string',
                'orgId' => 'required|exists:organizations,id,active,1,service_provider_id,' . $request->serviceProvider,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $validator->errors()
                ], 200);
            }

            $orgId = $request->orgId;

            // Base query
            $baseQuery = DB::table('device_models')
                ->leftJoin('device_families as deviceFamily', 'device_models.device_family_id', '=', 'deviceFamily.id')
                ->select(
                    'device_models.id',
                    'device_models.title',
                    'deviceFamily.name as device_family_name'
                )
                ->where('device_models.title', 'LIKE', "%{$request->name}%")
                ->whereNot('device_models.show_device_model', 1)
                ->where('device_models.active', 1)
                ->where('device_models.service_provider_id', $request->serviceProvider);

            // If orgId is provided, restrict to allowed models
            if (!empty($orgId)) {
                if (!is_numeric($orgId)) {
                    return response()->json(['success' => false, 'msg' => "Invalid Organization ID"], 400);
                }

                $orgDeviceModels = OrgAllowedModel::where([
                    ['org_id', $orgId],
                    ['service_provider_id', $request->serviceProvider],
                    ['active', 1]
                ])->pluck('model_id')->toArray();

                if (empty($orgDeviceModels)) {
                    return response()->json([
                        'success' => false,
                        'msg' => "No Device Models are found in this Organization"
                    ], 200);
                }

                $baseQuery->whereIn('device_models.id', $orgDeviceModels);
            }

            // Count total before limiting
            $total = $baseQuery->count();

            // Apply limit conditionally if results are many
            $limit = ($total > 100) ? 20 : null;

            if ($limit) {
                $baseQuery->limit($limit);
            }

            $deviceModels = $baseQuery->get();

            if ($deviceModels->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'msg' => "No Device Models found."
                ], 200);
            }

            return response()->json([
                'success' => true,
                'deviceModels' => $deviceModels
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => "Failed to get device models."
            ], 200);
        }
    }


    public function getDevicePlan(Request $request)
    {
        try {
            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'serviceProvider' => 'required|exists:service_providers,id,active,1',
                'modelId' => 'required|exists:device_models,id,active,1,service_provider_id,' . $request->serviceProvider,
                'orgId' => 'required|exists:organizations,id,active,1,service_provider_id,' . $request->serviceProvider,
            ]);
            /* If validation's fails */
            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $validator->errors()
                ], 200);
            }
            $planFields = [
                'org_allowed_models.device_plan_id as id',
                'device_plans.plan_name',
                'org_allowed_models.coverage_price as price',
                'org_allowed_models.deductible as deductible_price',
                'org_allowed_models.expiration_date'
            ];
            $planData = $this->getPlansById($request->modelId, $request->orgId, $request->serviceProvider, $planFields);

            return response()->json([
                'success' => true,
                'deviceModelPlans' => $planData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => "Something went wrong."
            ], 200);
        }
    }

    /** Creation of organization subscriber */
    public function storePublicOrgData(Request $request)
    {

        try {
            /* Step 1: Validate Request */
            $validator = Validator::make($request->all(), [
                'serviceProvider' => 'required|exists:service_providers,id,active,1',
                'firstName' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'phone' => 'required|regex:/^[0-9]{10}$/',
                'email' => 'required|email|max:255',
                'password' => [
                    'nullable',
                    function ($attribute, $value, $fail) use ($request) {
                        if ($request->isLoginUser == false) {
                            if (empty($value)) {
                                $fail('The password field is required.');
                            }
                        }
                    },
                    'string',
                    'min:8',
                    'confirmed',
                ],
                'orgId' => 'required|exists:organizations,id,active,1,service_provider_id,' . $request->serviceProvider,
                'paymentMethodId' => 'required|string',
                'subOrg' => 'nullable|exists:sub_organizations,id,org_id,' . $request->orgId . ',active,1,service_provider_id,' . $request->serviceProvider,
                'devices' => 'required|array',
                'devices.*.modelId' => 'required|exists:device_models,id,active,1,service_provider_id,' . $request->serviceProvider,
                'devices.*.selectedPlan' => 'required|exists:device_plans,id,active,1,service_provider_id,' . $request->serviceProvider,
                'devices.*.serialNumber' => 'required|string',
                'devices.*.stFirstName' => 'nullable|string',
                'devices.*.stLastName' => 'nullable|string',
                'devices.*.stGrade' => 'nullable|string',
                'devices.*.stId' => 'nullable|string',
                'cardHolderName' => 'required|string',
                'streetAddress' => 'required|string',
                'addressLine2' => 'nullable|string|max:255',
                'country' => 'required|string',
                'state' => 'required|string',
                'city' => 'required|string',
                'zipCode' => 'required|regex:/^[0-9]{5}$/',
                'membershipAgreement' => 'required|in:1',
            ], [

                'firstName.required' => 'First name is required.',
                'firstName.string' => 'First name must be a string.',
                'firstName.max' => 'First name must not exceed 255 characters.',
                'lastName.required' => 'Last name is required.',
                'lastName.string' => 'Last name must be a string.',
                'lastName.max' => 'Last name must not exceed 255 characters.',
                'phone.required' => 'Phone number is required.',
                'phone.regex' => 'Phone number must be a 10-digit number.',
                'email.required' => 'Email is required.',
                'email.email' => 'Email must be a valid email address.',
                'email.max' => 'Email must not exceed 255 characters.',
                'password.required' => 'Password is required.',
                'password.string' => 'Password must be a string.',
                'password.min' => 'Password must be at least 8 characters long.',
                'password.confirmed' => 'Password and confirmation should match.',
                'orgId.required' => 'Organization ID is required.',
                'orgId.exists' => 'Organization does not exist.',
                'paymentMethodId.required' => 'Payment method ID is required.',
                'subOrg.exists' => 'Sub-organization does not exist.',
                'subOrg.nullable' => 'Sub-organization is optional.',
                'devices.array' => 'Devices must be an array.',
                'devices.required' => 'Devices are required.',
                'devices.*.modelId.required' => 'Device Model is required.',
                'devices.*.modelId.exists' => 'Device Model does not exist.',
                'devices.*.selectedPlan.required' => 'Device Plan is required.',
                'devices.*.selectedPlan.exists' => 'Device Plan does not exist.',
                'devices.*.serialNumber.required' => 'Serial number is required.',
                'devices.*.serialNumber.string' => 'Serial number must be a string.',
                'devices.*.stFirstName.string' => "Owner's first name must be a string.",
                'devices.*.stLastName.string' => "Owner's last name must be a string.",
                'devices.*.stGrade.string' => "Owner's grade must be a string.",
                'devices.*.stId.string' => "Owner's ID must be a string.",
                'cardHolderName.required' => 'Card holder name is required.',
                'cardHolderName.string' => 'Card holder name must be a string.',
                'streetAddress.required' => 'Street address is required.',
                'addressLine2.string' => 'Address line 2 must be a string.',
                'addressLine2.max' => 'Address line 2 must not exceed 255 characters.',
                'country.required' => 'Country is required.',
                'state.required' => 'State is required.',
                'city.required' => 'City is required.',
                'zipCode.required' => 'Zip code is required.',
                'zipCode.regex' => 'Zip code must be a 5-digit number.',
                'membershipAgreement.required' => 'Please accept terms and conditions.',
                'membershipAgreement.in' => 'Please accept terms and conditions.',
            ]);

            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }

            /* Step 1: Configure Stripe */
            $stripeSecretKey = trim(SDCOptionController::getOption('sdcsm_stripe_secret_key', $request->serviceProvider), '"');
            if (empty($stripeSecretKey)) {
                return response()->json(["msg" => "Something went wrong, please try again later.", "success" => false], 200);
            }
            config(['cashier.secret' => $stripeSecretKey]);

            /* Step 2: Create Stripe Customer (temp user for billing first) */
            $userId = null;
            $tempUser = User::where('email', $request->email)->first();
            if (!empty($tempUser)) {
                $userId = $tempUser->id;
            } else {
                $roleId = SDCOptionController::getOption("educational_coverage_role", $request->serviceProvider);
                $tempUser = new User([
                    'first_name' => $request->firstName,
                    'last_name' => $request->lastName,
                    'full_name' => $request->firstName . ' ' . $request->lastName,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'role_id' => $roleId,
                ]);
                $userId = $tempUser->id;
            }
            $tempUser->setRelation('subscriptions', collect());
            $tempUser->createOrGetStripeCustomer();
            $tempUser->updateDefaultPaymentMethod($request->paymentMethodId);
            $isPaymentSucess = false;
            $subscriptionIds = [];
            $transactionIds = [];
            $successfulPayments = [];
            /* Step 3: Process payment/subscription before DB save */
            foreach ($request->devices as $device) {
                $planData = $this->getPlansByPlanId($device['selectedPlan'], $device['modelId'], $request->orgId, $request->serviceProvider);
                if (empty($planData)) {
                    return response()->json(["msg" => "Something went wrong, please try again later.", "success" => false], 200);
                }else {

                /* if ($planData['freq_occurence'] == 1) { */
                    /* One-time charge */
                    $payment = $tempUser->charge($planData->price * 100, $request->paymentMethodId, [
                        'payment_method_types' => ['card'],
                        'off_session' => true,
                        'confirm' => true,
                        'description' => 'Organizational Device Coverage.',
                        'metadata' => [
                            'payment_for' => 1,
                            'service_provider_id' => $request->serviceProvider,
                        ],
                    ]);
                    if ($payment->status == 'succeeded') {
                        $isPaymentSucess = true;
                        $transactionIds[$device['serialNumber']] = $payment->id;
                    }
                }
               /*  elseif (!empty($planData['stripe_price_id']) && ($planData['freq_occurence'] == 2 || $planData['freq_occurence'] == 3 || $planData['freq_occurence'] == 4 || $planData['freq_occurence'] == 5)) {
                    $subscription = $tempUser->newSubscription($planData['plan_name'], $planData['stripe_price_id'])->create($request->paymentMethodId, [
                        'description' => 'Organizational Device Coverage Subscription for ' . $planData['plan_name'],
                        'metadata' => [
                            'payment_for' => 1,
                            'service_provider_id' => $request->serviceProvider,
                        ]
                    ]);
                    if ($subscription->stripe_status == 'active') {
                        $isPaymentSucess = true;
                        $subscriptionIds[$device['serialNumber']] = $subscription->stripe_id;
                    }
                } */
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
                /* Step 5: Now save user data AFTER payment succeeds */

                $orgId = $request->orgId;
                /* Use firstOrNew to prevent duplicate creation */
                $user = User::firstOrNew(['email' => $request->email]);
                $roleId = optional($user)->role_id ?? SDCOptionController::getOption("educational_coverage_role", $request->serviceProvider);
                /* Update user data */
                $user->fill([
                    'first_name' => $request->firstName,
                    'last_name' => $request->lastName,
                    'full_name' => $request->firstName . ' ' . $request->lastName,
                    'phone' => $request->phone,
                    'role_id' => $roleId,
                    'service_provider_id' => $request->serviceProvider,
                    'stripe_id' => $tempUser->stripe_id,
                    'cardholder_name' => $request->cardHolderName,
                ]);

                /* Only set password for new users or when explicitly changing it */
                if (!$user->exists || ($request->password && !$request->isLoginUser)) {
                    $user->password = Hash::make($request->password);
                }

                $user->save();

                if (empty($user)) {
                    return response()->json(["msg" => "Something went wrong, please try again later.", "success" => false], 200);
                } else {
                    /******************************************* */
                    $userId = $user->id;
                    $userFirstName = $user->first_name;
                    $userLastName = $user->last_name;
                    $serviceProvider = $user->service_provider_id;
                    $userPhone = $user->phone;
                    $subOrgId = $request->subOrg;
                    /** User Meta Check */
                    $userMetaExists = UserMeta::where("user_id", $userId)->exists();
                    $userMetaKey = $userMetaExists ? UserMeta::select('meta_key')->where("user_id", $userId)->where("meta_key", "org_subscriber")->where('meta_value', 'yes')->where('active', 1)->where('service_provider_id', $serviceProvider)->first() : null;
                    if (!$userMetaExists) {
                        $userMetaCreated = UserMeta::create(
                            ['user_id' => $userId, 'meta_key' => 'org_subscriber', 'meta_value' => 'yes', 'service_provider_id' => $serviceProvider]
                        );
                        $userMetaKey = $userMetaCreated;
                        // $userMetaKey = $userMetaCreated->meta_key;
                        $notificationRequest = [
                            'user_id' => $user->id,
                            'message' => $user->full_name  . " has registered.",
                            'notification_for' => 'user_added',
                            'org_id' => null,
                            'sub_org_id' => null,
                            /*  'org_id' => $orgId,
                            'sub_org_id' => $request->subOrg ?? null, */
                            'user_target_link' => 'sdcsmuser/user-list',
                            'admin_target_link' => 'smarttiusadmin/users',
                            'service_provider_id' => $user->service_provider_id,
                        ];
                        NotificationController::addNotification($notificationRequest);
                    }
                    $orgRelationShipExists = OrgRelationship::where('user_id', $userId)->first();
                    /* $orgRelationShipExists = OrgRelationship::where('user_id', $userId)->get(); */
                    /* if ($orgRelationShipExists->isEmpty()) { */
                    if (empty($orgRelationShipExists) && isset($userMetaKey) && $userMetaKey->meta_key == 'org_subscriber') {
                        OrgRelationship::updateOrCreate(
                            ['user_id' => $userId],
                            ['is_org_subscriber' => 1, 'service_provider_id' => $serviceProvider, 'org_id' => $orgId]
                        );
                        /** update org relation table */
                        if (!empty($subOrgId)) {
                            OrgRelationship::where('user_id', $userId)->where('org_id', $orgId)->where('is_org_subscriber', 1)->where('service_provider_id', $serviceProvider)->where('active', 1)
                                ->update([
                                    'org_id' => $subOrgId,
                                    'parent_org_id' => $orgId
                                ]);
                        }
                    }
                    /* Step : Devices */
                    // foreach ($request->devices as $device) {
                    foreach ($successfulPayments as $device) {
                        $planData = $this->getPlansByPlanId($device['selectedPlan'], $device['modelId'], $orgId, $serviceProvider);
                        if (!$planData) {
                            return false;
                        }
                        $deviceTitle = $device['modelTitle'] . ' (#' . $device['serialNumber'] . ')';
                        $deviceRecord = Device::create(
                            [
                                'device_title' => $deviceTitle,
                                'device_model_id' => $device['modelId'],
                                'serial_number' => $device['serialNumber'],
                                'device_type' => 2,/** 1 for personal and 2 for organizational */
                                'org_id' => $orgId,
                                // 'org_id' => isset($userMetaKey) && $userMetaKey->meta_key == 'org_subscriber' ? null : $orgId,
                                'user_id' => $userId,
                                'sub_org_id' => $subOrgId,
                                // 'sub_org_id' => isset($userMetaKey) && $userMetaKey->meta_key == 'org_subscriber' ? null : $subOrgId,
                                'device_owner_name' => $userFirstName . ' ' . $userLastName,
                                'org_user_first_name' => $device['stFirstName'],
                                'org_user_last_name' => $device['stLastName'],
                                'org_user_full_name' => isset($device['stFirstName']) || isset($device['stFirstName']) ? $device['stFirstName'] . ' ' . $device['stLastName'] : null,
                                'org_user_designation' => $device['stGrade'],
                                'org_user_id' => $device['stId'],
                                'plan_id' => $planData->id ?? null,
                                'subscription_id' => null,
                                /* 'subscription_id' => $subscriptionIds[$device['serialNumber']] ?? null, */
                                'transaction_id' => $transactionIds[$device['serialNumber']] ?? null,
                                'payment_added_date' => Carbon::now(),
                                'expiration_date' => $planData->expiration_date,
                                // 'expiration_date' => Carbon::now()->addDays((int) $planData['expiration_days']),
                                'service_provider_id' => $serviceProvider,
                                'membership_agreement' => $request->membershipAgreement,
                                'is_org_subscriber_device' => isset($userMetaKey) && $userMetaKey->meta_key == 'org_subscriber' ? 1 : 0,
                            ]
                        );
                        if ($deviceRecord->id) {

                            if (!empty($deviceRecord->transaction_id)) {
                                $transactionUpdate = Transaction::where('stripe_transaction_id', $deviceRecord->transaction_id)->first(); // Make sure we get the first matching transaction

                                if ($transactionUpdate) {
                                    $transactionUpdate->device_id = $deviceRecord->id;
                                    $transactionUpdate->save(); // Save the transaction with the updated device ID
                                }
                            }
                            if (!empty($deviceRecord->subscription_id)) {
                                $transactionUpdate = Transaction::where('stripe_transaction_id', $deviceRecord->subscription_id)->first(); // Make sure we get the first matching transaction

                                if ($transactionUpdate) {
                                    $transactionUpdate->device_id = $deviceRecord->id;
                                    $transactionUpdate->save(); // Save the transaction with the updated device ID
                                }
                            }
                            $notificationRequest = [
                                'user_id' => $deviceRecord->user_id,
                                'message' => ($deviceRecord->org_user_full_name ?? $deviceRecord->device_owner_name) . " has registered a new device: " . $deviceRecord->device_title,
                                'notification_for' => 'device_added',
                                'device_id' => $deviceRecord->id,
                                'org_id' => isset($userMetaKey) && $userMetaKey->meta_key == 'org_subscriber' ? null : $deviceRecord->org_id,
                                'sub_org_id' => isset($userMetaKey) && $userMetaKey->meta_key == 'org_subscriber' ? null : $deviceRecord->sub_org_id,
                                'user_target_link' => 'sdcsmuser/device-list',
                                'admin_target_link' => 'smarttiusadmin/devices',
                                'service_provider_id' => $deviceRecord->service_provider_id,
                            ];
                            NotificationController::addNotification($notificationRequest);
                        }
                    }

                    /* Shipping Addresses */
                    $shippingRecord = ShippingAddress::updateOrCreate(
                        [
                            'user_id' => $userId,
                            'service_provider_id' => $serviceProvider,
                            'active' => 1
                        ],
                        [
                            'user_id'           => $userId,
                            'phone'             => $userPhone,
                            'street_address'    => $request->streetAddress,
                            'address_line_2'    => $request->addressLine2 ?? null,
                            'city'              => $request->city,
                            'state'             => $request->state,
                            'zip'               => $request->zipCode,
                            'country'           => $request->country,
                            'service_provider_id' => $serviceProvider,
                        ]
                    );

                    if (empty($shippingRecord)) {
                        return response()->json(['success' => false, 'msg' => "Something went wrong, please try again later. "], 200);
                    }
                    /* Step 10: Done */
                    return response()->json([
                        "msg" => 'Coverage Purchased Successfully!',
                        "paymentMsg" => 'Payment successful!',
                        'redirectUrl' => route('sdcsmuser.login.index'),
                        "success" => true
                    ], 200);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => "Something went wrong, please try again later."], 200);
        }
    }

    /** Chcek the user email(existance) */
    public function checkUserEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users,email',
        ], [
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'Email address already exist, Please login first.',
        ]);
        if ($validator->fails()) {
            return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors(), 'showLoginButton' => true], 200);
        }
        return response()->json(["msg" => "Email verified", "success" => true, 'showLoginButton' => false], 200);
    }

    private function getPlansById($modelId, $orgId, $serviceProvider, $fields = [])
    {
        $devicePlanFields = [
            'org_allowed_models.device_plan_id as id',
            'device_plans.plan_name',
            'org_allowed_models.coverage_price as price',
            'org_allowed_models.deductible as deductible_price',
            'device_plans.plan_type',
            'org_allowed_models.stripe_product_id',
            'org_allowed_models.stripe_price_id',
            'org_allowed_models.expiration_date'
        ];

        if (empty($fields) || !is_array($fields)) {
            /* Default plan fields */
            $fields = $devicePlanFields;
        }

        $orgDeviceModelPlans = DB::table('org_allowed_models')
                               ->leftJoin('device_plans', 'org_allowed_models.device_plan_id', '=', 'device_plans.id')
                               ->select($fields)
                               ->where('org_allowed_models.org_id', $orgId)
                               ->where('org_allowed_models.model_id', $modelId)
                               ->where('device_plans.plan_type', 1)
                               ->where('device_plans.freq_occurence', 1)
                               ->where('device_plans.insured_uninsured_devices', 1)
                               ->where('org_allowed_models.active', 1)
                               ->where('org_allowed_models.service_provider_id', $serviceProvider)
                               ->get();

       return $orgDeviceModelPlans;
    }
    private function getPlansByPlanId($planId, $modelId, $orgId, $serviceProvider, $fields = [])
    {
        $devicePlanFields = [
            'org_allowed_models.device_plan_id as id',
            'device_plans.plan_name',
            'org_allowed_models.coverage_price as price',
            'org_allowed_models.deductible as deductible_price',
            'device_plans.plan_type',
            'org_allowed_models.stripe_product_id',
            'org_allowed_models.stripe_price_id',
            'org_allowed_models.expiration_date'
        ];

        /* Primary plan fetch */
        if (empty($fields) || !is_array($fields)) {
            /* Default plan fields */
            $fields = $devicePlanFields;
        }

        $orgPlan = DB::table('org_allowed_models')
                   ->leftJoin('device_plans', 'org_allowed_models.device_plan_id', '=', 'device_plans.id')
                   ->select($fields)
                   ->where('org_allowed_models.device_plan_id', $planId)
                   ->where('org_allowed_models.org_id', $orgId)
                   ->where('org_allowed_models.model_id', $modelId)
                   ->where('device_plans.plan_type', 1)
                    ->where('device_plans.freq_occurence', 1)
                    ->where('device_plans.insured_uninsured_devices', 1)
                   ->where('org_allowed_models.active', 1)
                   ->where('org_allowed_models.service_provider_id', $serviceProvider)
                   ->first();
        return $orgPlan;
    }
}
