<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Admin\SDCOptionController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;

use App\Models\Admin\Device;
use App\Models\Admin\DeviceModel;
use App\Models\Admin\DevicePlan;
use App\Models\Transaction;
use App\Models\User;
use App\Models\User\ShippingAddress;

use App\Models\UserMeta;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PublicGetCoverageController extends Controller
{

    /** Function to call the get coverage form*/
    public function getCoverageForm()
    {
        $stripePublicKey = SDCOptionController::getOption('sdcsm_stripe_publishable_key', 1);
        /** Getting all device models */
        config(['cashier.key' => $stripePublicKey]);
        return view('public.getcoverage', compact('stripePublicKey'));
    }

    /** Function to store the data according to steps */
    public function createCoverage(Request $request)
    {

        try {
             /* Step 1: Validate Request */
            $validator = Validator::make($request->all(), [
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
                'devices' => 'required|array',
                'devices.*.deviceModelId' => 'required|exists:device_models,id,active,1,service_provider_id,1',
                'devices.*.serialNumber' => 'required|string',
                'devices.*.devicePlan' => 'required|exists:device_plans,id,active,1,service_provider_id,1',
                'devices.*.cellularService' => 'nullable|string',
                'devices.*.carrier' => 'nullable|string',
                'devices.*.imei' => 'nullable|string',
                'devices.*.capacity' => 'nullable|string',
                'devices.*.color' => 'nullable|string',
                'cardholderName' => 'required|string',
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
            $stripeSecretKey = trim(SDCOptionController::getOption('sdcsm_stripe_secret_key', 1), '"');
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
                $roleId = SDCOptionController::getOption("personal_coverage_role", 1);
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
                $planData = $this->getPlansByPlanId($device['devicePlan'], $device['deviceModelId']);
                if (empty($planData)) {
                    return response()->json(["msg" => "Something went wrong, please try again later.", "success" => false], 200);
                }

                if ($planData['freq_occurence'] == 1) {
                    /* One-time charge */
                    $payment = $tempUser->charge($planData['price'] * 100, $request->paymentMethodId, [
                        'payment_method_types' => ['card'],
                        'off_session' => true,
                        'confirm' => true,
                        'description' => 'Personal Device Coverage.',
                        'metadata' => [
                            'payment_for' => 1,
                            'service_provider_id' => 1,
                        ],
                    ]);
                    if ($payment->status == 'succeeded') {
                        $isPaymentSucess = true;
                        $transactionIds[$device['serialNumber']] = $payment->id;
                    }
                } elseif (!empty($planData['stripe_price_id']) && ($planData['freq_occurence'] == 2 || $planData['freq_occurence'] == 3 || $planData['freq_occurence'] == 4 || $planData['freq_occurence'] == 5)) {
                    $subscription = $tempUser->newSubscription($planData['plan_name'], $planData['stripe_price_id'])->create($request->paymentMethodId, [
                        'description' => 'Personal Device Coverage Subscription for ' . $planData['plan_name'],
                        'metadata' => [
                            'payment_for' => 1,
                            'service_provider_id' => 1,
                        ]
                    ]);
                    if ($subscription->stripe_status == 'active') {
                        $isPaymentSucess = true;
                        $subscriptionIds[$device['serialNumber']] = $subscription->stripe_id;
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
                /* Step 5: Now save user data AFTER payment succeeds */
                /* Use firstOrNew to prevent duplicate creation */
                $user = User::firstOrNew(['email' => $request->email]);
                $roleId = optional($user)->role_id ?? SDCOptionController::getOption("personal_coverage_role", 1);
                /* Update user data */
                $user->fill([
                    'first_name' => $request->firstName,
                    'last_name' => $request->lastName,
                    'full_name' => $request->firstName . ' ' . $request->lastName,
                    'phone' => $request->phone,
                    'role_id' => $roleId,
                    'service_provider_id' => 1,
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
                    $userFullName = $user->full_name;
                    $serviceProvider = $user->service_provider_id;
                    $userPhone = $user->phone;
                    $subOrgId = $request->subOrg;
                    /** User Meta Check */
                    $userMetaExists = UserMeta::where("user_id", $userId)->exists();
                    if (!$userMetaExists) {
                        UserMeta::create(
                            ['user_id' => $userId, 'meta_key' => 'subscriber', 'meta_value' => 'yes', 'service_provider_id' => $serviceProvider]
                        );
                        $notificationRequest = [
                            'user_id' => $user->id,
                            'message' => $user->full_name  . " has registered.",
                            'notification_for' => 'user_added',
                            'user_target_link' => 'sdcsmuser/user-list',
                            'admin_target_link' => 'smarttiusadmin/users',
                            'service_provider_id' => $user->service_provider_id,
                        ];
                        NotificationController::addNotification($notificationRequest);
                    }
                    /* Step : Devices */
                    // foreach ($request->devices as $device) {
                    foreach ($successfulPayments as $device) {
                        $planData = $this->getPlansByPlanId($device['devicePlan'], $device['deviceModelId']);

                        if (!$planData) {
                            return false;
                        }
                        $deviceTitle = $device['deviceModelTitle'] . ' (#' . $device['serialNumber'] . ')';
                        $deviceRecord = Device::create(
                            [
                                'device_title' => $deviceTitle,
                                'device_model_id' => $device['deviceModelId'],
                                'serial_number' => $device['serialNumber'],
                                'device_type' => 1, /** 1 for personal and 2 for organizational */
                                'user_id' => $userId,
                                'org_user_first_name' => $userFirstName,
                                'org_user_last_name' => $userLastName,
                                'org_user_full_name' => $userFullName,
                                'cellular_service' => $device['cellularService'],
                                'imei' => $device['imei'],
                                'carrier' => $device['carrier'],
                                'capacity' => $device['capacity'],
                                'color' => $device['color'],
                                'plan_id' => $planData['id'] ?? null,
                                'subscription_id' => $subscriptionIds[$device['serialNumber']] ?? null,
                                'transaction_id' => $transactionIds[$device['serialNumber']] ?? null,
                                'payment_added_date' => Carbon::now(),
                                'expiration_date' => Carbon::now()->addDays((int) $planData['expiration_days']),
                                'service_provider_id' => $serviceProvider,
                                'membership_agreement' => $request->membershipAgreement,
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
                                'message' => $deviceRecord->org_user_full_name . " has registered a new device: " . $deviceRecord->device_title,
                                'notification_for' => 'device_added',
                                'device_id' => $deviceRecord->id,
                                'org_id' => $deviceRecord->org_id,
                                'sub_org_id' => $deviceRecord->sub_org_id,
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
                            'service_provider_id' => 1,
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
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Function to get the publicly available device model */
    public function getDeviceModels(Request $request)
    {
        try {
            /* Get Filters */
            $select = [
                'id',
                'title',
                'show_device_model',
                'show_device_publicly',
                'device_family_id',
                'active',
                'service_provider_id',
            ];

            /* $modelData = DeviceModel::select($select)->where('title', 'LIKE', "%{$request->name}%")->where('show_device_publicly', 1)->where('active', 1)->get(); */
            $modelData = DeviceModel::select($select)->where('show_device_publicly', 1)->where('active', 1)->orderBy('title', 'asc')->get();

            if ($modelData) {
                return response()->json(['success' => true, 'modelData' => $modelData], 200);
            } else {
                return response()->json(['success' => false, 'msg' => 'No device found.'], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => "Failed to get device models."], 200);
        }
    }

    /** check user by email */
    public function userCheck(Request $request)
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

    /** get plans by device model */
    public function getPlans(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'modelId' => 'required|exists:device_models,id,active,1,show_device_publicly,1',
            ]);

            /* If validation's fails */
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $planData = DevicePlan::select('id', 'plan_name', 'price', 'expiration_days')->where('device_model_id', $request->modelId)->where('plan_type', 1)->where('insured_uninsured_devices', '!=', 2)->where('active', 1)->get();
            if (!empty($planData)) {
                return response()->json(['success' => true, 'planData' => $planData], 200);
            } else {
                return response()->json(['success' => false, 'msg' => "Something went wrong."], 200);
            }
        } catch (Exception $e) {
            return response()->json(['success' => false, 'msg' => "Something went wrong."], 200);
        }
    }

    private function getPlansByPlanId($planId, $modelId)
    {
        $devicePlanFields = [
            'id',
            'plan_name',
            'price',
            'deductible_price',
            'freq_occurence',
            'stripe_product_id',
            'stripe_price_id',
            'expiration_days'
        ];

        /* Primary plan fetch */
        $plan = DevicePlan::select($devicePlanFields)->where('id', $planId)->where('device_model_id', $modelId)->where('plan_type', 1)->where('insured_uninsured_devices', '!=', 2)->where('active', 1)->first();
        return $plan;
    }

    /** Function to check the serial number existance*/
    public function checkSerialNumber(Request $request){
        try{
            if(!empty($request->serialNumber)){
                $device = Device::where('serial_number', $request->serialNumber)->first();
                if($device){
                    return response()->json(['success' => false, 'msg' => 'Serial number already exist.'], 200);
                }else{
                    return response()->json(['success' => true], 200);
                }
            }
        } catch (Exception $e){
            return response()->json(['success' => false, 'msg' => "Something went wrong."], 200);
        }
    }
}
