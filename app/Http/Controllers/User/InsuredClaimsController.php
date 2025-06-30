<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Admin\AdminShippingOptionController;
use App\Http\Controllers\Admin\ClaimReasonController;
use App\Http\Controllers\Admin\SDCOptionController;
use App\Http\Controllers\Admin\ZohoController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\User\UserDeviceController;
use App\Models\Admin\ClaimReason;
use App\Models\Admin\Device;
use App\Models\Admin\DevicePlan;
use App\Models\Admin\OrgAllowedModel;
use App\Models\Admin\OrgAllowedRenewalModel;
use App\Models\Admin\OrganizationClaimReason;
use App\Models\Admin\Role;
use App\Models\OrgRelationship;
use App\Models\Transaction;
use App\Models\User;
use App\Models\User\DeviceClaim;
use App\Models\User\ShippingAddress;
use App\Models\UserMeta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InsuredClaimsController extends Controller
{

    /** Fetch devices by type and search */
    public function fetchDeviceBySerialNumbers(Request $request)
    {
        try {
            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|min:5',
            ]);

            /* If Validation's fails */
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $where['whereDate'] = ['devices.expiration_date' => ['>=', date('Y-m-d')]];
            $fields = [
                'devices.id',
                'devices.device_title',
                'devices.serial_number',
                'deviceModel.title as device_model_name',
                'devicePlan.deductible_price',
            ];
            $totalDevices = UserDeviceController::getAllDevices($where)->count();

            if ($totalDevices == 0) {
                return response()->json(['success' => true, 'msg' => "No devices found.", 'totalDevices' => 0, 'deviceData' => []], 200);
            } elseif ($totalDevices > 0 && $totalDevices <= 100) {
                $deviceData = UserDeviceController::getDevicesForDropDown($where, $fields)->get();
            } elseif ($totalDevices > 0 && $totalDevices > 100) {
                /** Get all 20 devices list and further show by search(name or serial number) */
                $deviceData = UserDeviceController::getDevicesForDropDown($where, $fields)->limit(20)->get();
                if (!empty($request->name)) {
                    $where['orWhere'] = [
                        'devices.device_title' => ['LIKE', "%{$request->name}%"],
                        'devices.serial_number' => ['LIKE', "%{$request->name}%"],
                    ];
                    $deviceData = UserDeviceController::getDevicesForDropDown($where, $fields)->get();
                }
            }
            return response()->json(['success' => true, 'totalDevices' => $totalDevices, 'deviceData' => $deviceData], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => "Failed to get device."], 200);
        }
    }

    /** Common function to get the claim reasons */
    public function getAllClaimReasons(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'deviceId' => 'required|exists:devices,id,active,1,service_provider_id,' . $serviceProvider,
            ]);
            /* If Validation's fails */
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }

            $claim_reasons = null;
            $org_id = null;


            /** Getting device data to get the id of the device */
            $deviceId = $request->deviceId;
            $deviceData = Device::select('org_id', 'sub_org_id')->where('id', $deviceId)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
            if (empty($deviceData)) {
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            } else {
                $org_id = $deviceData->org_id;
            }
            if (!empty($org_id)) {
                /** If user associated with any organization then get the reasons by using organization id */
                $org_claim_reasons = OrganizationClaimReason::select('id', 'claim_reason_id', 'org_id')->where('org_id', $org_id)->where('service_provider_id', $serviceProvider)->where('active', 1)->get();
                if ($org_claim_reasons->isEmpty()) {
                    $claim_reasons = ClaimReasonController::getClaimReasonsDropdown();
                } else {
                    $claim_reasons = ClaimReasonController::getClaimReasonsDropdown()->filter(function ($reason) use ($org_claim_reasons) {
                        foreach ($org_claim_reasons as $org_reason) {
                            if ($reason->id == $org_reason->claim_reason_id) {
                                return true;
                            }
                        }
                        return false;
                    });
                }
            } else {
                /** If user is not associated with any organization then get the reasons by using service provider id */
                $claim_reasons = ClaimReasonController::getClaimReasonsDropdown();
            }
            return response()->json(['success' => true, 'claim_reasons' => $claim_reasons,], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => "Failed to claim reasons."], 200);
        }
    }

    public function getDevicePlans(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'deviceId' => 'required|exists:devices,id,active,1,service_provider_id,' . $serviceProvider,
            ]);
            /* If Validation's fails */
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $plan = $this->getPlansAccordingToDevice($request->deviceId, $serviceProvider);
            return response()->json(['success' => true, 'plan' => $plan], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => "Something went wrong, please try again later."], 200);
        }
    }
    private function getPlansAccordingToDevice($deviceId, $serviceProvider)
    {
        $devicePlanFields = [
            'id',
            'deductible_price',
            'plan_type'
        ];

        /** Device data */
        $device = Device::where('id', $deviceId)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
        if (empty($device)) {
            return response()->json(["msg" => "Something went wrong, please try again later.", "success" => false], 200);
        } else {
            if (isset($device->plan_id)) {
                /* Primary plan fetch */
                $plan = DevicePlan::select($devicePlanFields)->where('id', $device->plan_id)->where('insured_uninsured_devices', '=', 1)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
                if (empty($fields) || !is_array($fields)) {
                    /* Default plan fields */
                    $fields = $devicePlanFields;
                }
                /* Fallback plan fetch */
                if (!empty($plan)) {
                    if (isset($device->org_id) && $plan->plan_type == 1) {
                        $orgPlan = OrgAllowedModel::where('device_plan_id', $plan->id)->where('org_id', $device->org_id)->where('model_id', $device->device_model_id)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
                        /* Check if organization has specific device plans */
                        if (!empty($orgPlan)) {
                            // Override default values with organization-specific ones
                            $plan->deductible_price = $orgPlan->deductible;
                            return $plan->only($fields);
                        }
                    }
                    if (isset($device->org_id) && $plan->plan_type == 2) {
                        $orgPlan = OrgAllowedRenewalModel::where('device_plan_id', $plan->id)->where('org_id', $device->org_id)->where('model_id', $device->device_model_id)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
                        /* Check if organization has specific device plans */
                        if (!empty($orgPlan)) {
                            // Override default values with organization-specific ones
                            $plan->deductible_price = $orgPlan->deductible;
                            return $plan->only($fields);
                        }
                    }
                }
                /* Return default plan with filtered fields */
                return $plan->only($fields);

            }else {
                return (object) [
                    'id' => null ,
                    'deductible_price' => null,
                    'plan_type' => null,
                ];
            }
        }
    }
    /** File a claim */
    public function fileClaim()
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $shippingOptions =  AdminShippingOptionController::getShippingOptionsDropDown();
        $stripePublicKey = SDCOptionController::getOption('sdcsm_stripe_publishable_key', $serviceProvider);
        config(['cashier.key' => $stripePublicKey]);
        return view('user.insuredClaims.fileClaim', compact('shippingOptions', 'stripePublicKey'));
    }

    /** To save the data of filed claims */
    public function createClaim(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');

            /* Validate to the requests */
            $validator = Validator::make($request->all(), [
                'firstName' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|regex:/^[0-9]{10}$/',
                'metaValue' => 'required|string',
                'deviceData' => 'required|array',
                'deviceData.*.selectedDevice' => 'required|exists:devices,id,active,1,service_provider_id,' . $serviceProvider,
                'deviceData.*.claimReason' => 'required|exists:claim_reasons,id,active,1,service_provider_id,' . $serviceProvider,
                'deviceData.*.claimDetails' => 'required|string|max:255',
                'shippingOption' => [
                    Rule::requiredIf(!in_array($request->metaValue, ['org_it_hod', 'org_it_director']))
                ],
                'deductibleCost' => 'integer',
                'cardHolderName' => 'nullable|string|max:40',
                /*  'cardHolderName' => [
                     Rule::requiredIf(!in_array($request->metaValue, ['org_it_hod', 'org_it_director'])),
                     'string',
                 ], */
                'streetAddress' => 'required|string',
                'addressLine2' => 'nullable|string',
                'city' => 'required',
                'state' => 'required',
                'zipCode' => 'required|regex:/^[0-9]{5}$/',
                'country' => 'required',
            ], [
                'firstName.required' => "Please enter the first name.",
                'firstName.string' => "First name must be a string.",
                'firstName.max' => "First name may not be greater than 255 characters.",
                'lastName.required' => "Please enter the last name.",
                'lastName.string' => "Last name must be a string.",
                'lastName.max' => "Last name may not be greater than 255 characters.",
                'email.required' => "Please enter the email address.",
                'email.email' => "Please enter a valid email address.",
                'email.max' => "Email may not be greater than 255 characters.",

                'phone.required' => "Please enter the phone number.",
                'phone.regex' => "Phone Number must be numeric.",

                'deviceData.*.selectedDevice.required' => "Please select the device.",
                'deviceData.*.selectedDevice.integer' => "Please select the device.",

                'deviceData.*.claimReason.required' => "Please select the claim reason.",
                'deviceData.*.claimReason.integer' => "Please select the claim reason.",

                'deviceData.*.claimDetails.required' => "Please enter the claim details.",
                'deviceData.*.claimDetails.string' => "Claim details must be in string.",

                'deductibleCost.integer' => "Deductible price must be an integer",

                'shippingOption.required' => "Please select the shipping option.",

                'cardHolderName.required' => "Please enter the card holder name.",
                'cardHolderName.string' => "Cardholder name must be string.",

                'streetAddress.required' => "Please enter the street address.",
                'city.required' => "Please enter the city name.",
                'state.required' => "Please enter the state name.",
                'zipCode.required' => "Please enter the zip code.",
                'zipCode.regex' => "Zipcode must be a 5 digit number.",
                'country.required' => "Please enter the country name.",
            ]);
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            /** Getting user data to get the id of the user */
            $userData = Session::get('auth_user');
            $user_id = $userData->id;
            $user_fullName = $userData->full_name;
            $org_id = null;
            $sub_org_id = null;
            $transactionId = null;
            $isPaymentSucess = false;
            $organdSubOrgId = $this->getAuthUserOrgSubOrg($user_id);
            $deviceData = request()->input('deviceData', []);
            if (empty($organdSubOrgId) && isset($request->metaValue) &&  ($request->metaValue == 'org_it_hod' || $request->metaValue == 'org_it_director')) {
                return response()->json(['success' => false, 'msg' => "Something went wrong, Please try again later."], 200);
            }
            /* if (isset($organdSubOrgId->org_id) && isset($organdSubOrgId->parent_org_id)) {
                $org_id = $organdSubOrgId->parent_org_id;
                $sub_org_id = $organdSubOrgId->org_id;
            } elseif (isset($organdSubOrgId->org_id) && is_null($organdSubOrgId->parent_org_id)) {
                $org_id = $organdSubOrgId->org_id;
                $sub_org_id = null;
            } else {
                $org_id = null;
                $sub_org_id = null;
            } */
            if (isset($request->metaValue) &&  ($request->metaValue == 'org_it_hod' || $request->metaValue == 'org_it_director')) {
                $isPaymentSucess = true;
            } else {
                /** Card Details Save */
                if ($request->amount > 0) {
                    /* Step 1: Configure Stripe */
                    $stripeSecretKey = trim(SDCOptionController::getOption('sdcsm_stripe_secret_key', $serviceProvider), '"');
                    if (empty($stripeSecretKey)) {
                        return response()->json(["msg" => "Something went wrong, please try again later.", "success" => false], 200);
                    }
                    config(['cashier.secret' => $stripeSecretKey]);
                    $userData->createOrGetStripeCustomer();
                    $userData->updateDefaultPaymentMethod($request->paymentMethodId);
                    /** Build detailed description */
                    $deviceDescriptions = [];
                    $totalShipping = 0;

                    foreach ($deviceData as $key => $device) {
                        $deviceTitle = ($device['selectedDeviceTitle'] ?? 'Device') . ' (' . ($device['selectedDevice'] ?? '-') . ')';
                        $claimFee = number_format($device['claimChargesAmount'] ?? 0, 2);
                        $deviceDescriptions[] = "{$deviceTitle}: \${$claimFee}";
                        $totalShipping = $request->shippingAmount ?? 0;
                    }

                    $shippingFormatted = number_format($totalShipping, 2);
                    $description = 'Claim Charges - ' . implode(', ', $deviceDescriptions) . ". Shipping: \${$shippingFormatted}";
                    /* One-time charge */
                    $payment = $userData->charge($request->amount * 100, $request->paymentMethodId, [
                        'payment_method_types' => ['card'],
                        'off_session' => true,
                        'confirm' => true,
                        // 'description' => 'Shipping charges(Claim Devices).',
                        'description' => $description,
                        'metadata' => [
                            'payment_for' => 2,
                            'service_provider_id' => $serviceProvider,
                        ],
                    ]);
                    if ($payment->status == 'succeeded') {
                        $isPaymentSucess = true;
                        $transactionId = $payment->id;
                    }
                } else {
                    $isPaymentSucess = true;
                }
            }
            if ($isPaymentSucess) {
                // User::where('id', $user_id)->update(['phone'=> $request->phoneNumber]);
                /** Update card Holder Name */
                if (!empty($request->cardHolderName)) {
                    User::where('id', $user_id)->update([
                        'cardholder_name' => $request->cardHolderName,
                    ]);
                }

                if (!empty($deviceData)) {
                    foreach ($deviceData as $device) {
                        $selectedDevice = $device['selectedDevice'];
                        $selectedDeviceTitle = $device['selectedDeviceTitle'];
                        $claimReason = $device['claimReason'];
                        $claimDetails = $device['claimDetails'];
                        $claimChargesAmount = $device['claimChargesAmount'];
                        $deviceOrgSubOrg = Device::select('org_id', 'sub_org_id', 'is_org_subscriber_device')->where('id', $selectedDevice)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
                        if (empty($deviceOrgSubOrg)) {
                            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
                        } else {
                            $org_id = $deviceOrgSubOrg->org_id;
                            $sub_org_id = $deviceOrgSubOrg->sub_org_id;
                        }
                        $claimData = DeviceClaim::create([

                            'first_name' => $request->firstName,
                            'last_name' => $request->lastName,
                            'email' => $request->email,

                            'user_id' => $user_id,
                            'org_id' => $org_id,
                            'sub_org_id' => $sub_org_id,
                            'is_org_subsciber_claim' => isset($deviceOrgSubOrg->is_org_subscriber_device) && $deviceOrgSubOrg->is_org_subscriber_device == 1 ? 1 : 0,

                            'device_id' => $selectedDevice,
                            'claim_reason_id' => $claimReason,
                            'claim_details' => $claimDetails,
                            'claim_amount' => $claimChargesAmount,

                            'shipping_option' => $request->shippingOption,
                            'status_updated_date' => Carbon::now(),
                            'service_provider_id' => $serviceProvider,
                            'transaction_id' => $transactionId ?? null,

                            /** Insert the address here in claim table */
                            'phone' => $request->phone,
                            'street_address' => $request->streetAddress,
                            'address_line_2' => $request->addressLine2,
                            'city' => $request->city,
                            'state' => $request->state,
                            'zip' => $request->zipCode,
                            'country' => $request->country,
                        ]);

                        /** Send data to zoho */
                        $zohoResponse = ZohoController::sdcsmInsertInsuredClaimsInZoho($claimData);

                        if (empty($claimData->id) || empty($zohoResponse)) {
                            return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                        }

                        if (!empty($claimData->transaction_id)) {
                            Transaction::where('stripe_transaction_id', $claimData->transaction_id)->update(['device_claim_id' => $claimData->id]);
                        }
                        $notificationRequest = [
                            'user_id' => $claimData->user_id,
                            'message' => $user_fullName . " has filed a claim request for the device: " . $selectedDeviceTitle,
                            'notification_for' => 'claim_raised',
                            'is_claim_status_changed' => 1,
                            'device_claim_id' => $claimData->id,
                            'org_id' => $claimData->is_org_subsciber_claim == 1 ? null : $claimData->org_id,
                            'sub_org_id' => $claimData->is_org_subsciber_claim == 1 ? null : $claimData->sub_org_id,
                            'user_target_link' => 'sdcsmuser/user-track-claims',
                            'admin_target_link' => 'smarttiusadmin/track-claims',
                            'service_provider_id' => $claimData->service_provider_id,
                        ];
                        NotificationController::addNotification($notificationRequest);
                    }

                    if ($request->isAddressEdit == 1) {
                        $shippingData = ShippingAddress::updateOrCreate(
                            ['user_id' => $user_id],
                            [
                                'street_address' => $request->streetAddress,
                                'address_line_2' => $request->addressLine2,
                                'city' => $request->city,
                                'state' => $request->state,
                                'zip' => $request->zipCode,
                                'country' => $request->country,
                            ]
                        );
                        if (empty($shippingData->id)) {
                            return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                        }
                    }
                    return response()->json(["msg" => "Claim is filed successfully", "success" => true], 200);
                } else {
                    return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                }
            } else {
                return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** track insured claims */
    public function trackClaim(Request $request)
    {
        $where = $this->filterClaims($request);
        $claimData = self::paginateclaims(20, $where);
        $claim_reasons = ClaimReasonController::getClaimReasonsDropdown();
        $pagination = [
            'total' => $claimData->total(),
            'per_page' => $claimData->perPage(),
            'current_page' => $claimData->currentPage(),
            'last_page' => $claimData->lastPage(),
            'from' => $claimData->firstItem(),
            'to' => $claimData->lastItem()
        ];

        if (!empty($request->page)) {
            return response()->json(["claimData" => $claimData, "pagination" => $pagination, "msg" => "Paginated records here.", "success" => true], 200);
        } else {
            return view('user.insuredClaims.trackClaim', compact('claimData', 'pagination', 'claim_reasons'));
        }
    }

    /* Get All the Claims(paginate). */
    public static function paginateclaims($limit = 10, $where = [], $fields = [])
    {
        $data = self::getClaimsQuery($where, $fields)->paginate($limit);
        return $data;
    }

    /** Get All the Claims Without Pagination */
    public static function getClaims($where = [], $fields = [])
    {
        $data = self::getClaimsQuery($where, $fields)->get();
        return $data;
    }

    /* Claim-List get Query */
    public static function getClaimsQuery($where = [], $fields = [])
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $query = DB::table('device_claims')
            ->leftJoin('devices as device', 'device_claims.device_id', '=', 'device.id')
            ->leftJoin('device_models', 'device.device_model_id', '=', 'device_models.id')
            ->leftJoin('org_relationship as orgRelationship', 'device_claims.user_id', '=', 'orgRelationship.user_id')

            // Join organizations dynamically depending on parent_org_id
            ->leftJoin('organizations as org', function ($join) {
                $join->on('org.id', '=', DB::raw('IF(orgRelationship.parent_org_id IS NOT NULL, orgRelationship.parent_org_id, orgRelationship.org_id)'));
            })

            ->where('device_claims.active', 1)
            ->where('device_claims.service_provider_id', $serviceProvider);

        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select(
                'device_claims.id',
                'device_claims.zoho_claim_id',
                'device_claims.zoho_ticket_number',
                'device_claims.claim_status',
                'device_claims.user_claim_status',
                'device_claims.created_at',
                'org.org_type as org_type',
                'device.device_title as device_name',
                'device_models.title as device_model_name',
                'device.serial_number as serial_number',
                'device.device_type as device_type',
            );
        }
        /* Auth User */
        $authUser = Session::get('auth_user');

        // $org_relationship = OrgRelationship::select('user_id', 'org_id', 'parent_org_id', 'is_org_subscriber')->where('user_id', $authUser['id'])->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
        $org_relationship = self::getAuthUserOrgSubOrg($authUser->id);

        if (!empty($org_relationship) &&  !empty($org_relationship->org_id) && empty($org_relationship->parent_org_id)) {
            /** IT HOD */
            $where['where']['device_claims.org_id'] = $org_relationship->org_id;
            $where['whereNot']['device_claims.is_org_subsciber_claim'] = 1;
        } elseif (!empty($org_relationship) &&  !empty($org_relationship->org_id) && !empty($org_relationship->parent_org_id)) {
            /* IT Director */
            $where['where']['device_claims.org_id'] = $org_relationship->parent_org_id;
            $where['where']['device_claims.sub_org_id'] = $org_relationship->org_id;
            $where['whereNot']['device_claims.is_org_subsciber_claim'] = 1;
        } else {
            /** Subscriber Or Organization Subscriber */
            $where['where']['device_claims.user_id'] = $authUser->id;
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
                    } elseif ($field == 'whereBetween') {
                        /* For 'whereBetween', handle differently */
                        $query->whereBetween(key($value), $value[key($value)]);
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
        return $query->orderBy('device_claims.created_at', 'desc');
    }

    /* Get claim according to claim id and default latest */
    public function getClaimById(string $id)
    {
        if (!empty($id)) {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            $query = DB::table('device_claims')
                ->leftJoin('devices as device', 'device_claims.device_id', '=', 'device.id')
                ->leftJoin('device_models', 'device.device_model_id', '=', 'device_models.id')
                ->leftJoin('claim_reasons as claim_reason', 'device_claims.claim_reason_id', '=', 'claim_reason.id')
                ->leftJoin('users as user', 'device_claims.user_id', '=', 'user.id')
                ->leftJoin('organizations as org', 'device_claims.org_id', '=', 'org.id')
                ->leftJoin('sub_organizations as subOrg', 'device_claims.sub_org_id', '=', 'subOrg.id')
                ->leftJoin('transactions as transaction', 'device_claims.transaction_id', '=', 'transaction.stripe_transaction_id')
                ->select(
                    'device_claims.id',
                    'device_claims.zoho_claim_id',
                    'device_claims.zoho_ticket_number',
                    'device_claims.user_claim_status',
                    'device_claims.claim_details',
                    'device_claims.created_at',
                    'device_claims.status_updated_date',
                    'device_claims.first_name',
                    'device_claims.last_name',
                    'device_claims.email',
                    'device_claims.phone',
                    'device_claims.street_address',
                    'device_claims.address_line_2',
                    'device_claims.city',
                    'device_claims.state',
                    'device_claims.zip',
                    'device_claims.country',
                    'device.device_title as device_name',
                    'device.serial_number as serial_number',
                    'device_models.title as device_model_name',
                    'device.device_type as device_type',
                    'device.created_at as device_created_at',
                    'claim_reason.claim_reason_name',
                    'user.full_name as user_name',
                    'user.phone as user_phone',
                    'user.email as user_email',
                    'user.created_at as user_created_at',
                    'org.name as org_name',
                    'org.org_type as org_type',
                    'subOrg.name as sub_org_name',
                    'device_claims.claim_amount as transaction_amount',
                    // 'transaction.amount as transaction_amount',
                    'transaction.stripe_transaction_id as transaction_stripe_transaction_id',
                    'transaction.status as transaction_status',
                    'transaction.created_at as transaction_created_at',
                )
                ->where('device_claims.active', 1)
                ->where('device_claims.service_provider_id', $serviceProvider);
            $authUser = Session::get('auth_user');
            $org_relationship = self::getAuthUserOrgSubOrg($authUser->id);
            if (!empty($org_relationship) &&  !empty($org_relationship->org_id) && empty($org_relationship->parent_org_id)) {
                /** IT HOD */
                $query->where('device_claims.org_id', $org_relationship->org_id)->whereNot('device_claims.is_org_subsciber_claim', 1);
            } elseif (!empty($org_relationship) &&  !empty($org_relationship->org_id) && !empty($org_relationship->parent_org_id)) {
                /* IT Director */
                $query->where('device_claims.org_id', $org_relationship->parent_org_id)->where('device_claims.sub_org_id', $org_relationship->org_id)->whereNot('device_claims.is_org_subsciber_claim', 1);

            } else {
                /** Subscriber Or Organization Subscriber */
                $query->where('device_claims.user_id', $authUser->id);
            }
            $claim = $query->where('device_claims.id', $id)->first();
            if (!empty($claim)) {
                return response()->json(["viewData" => $claim, "msg" => "Claim Data.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Claim not found.", "success" => false], 200);
            }
        } else {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** To get the user data(address and other) by using the user Id to show the data in claim form */
    public static function getUserdataById()
    {
        try {
            $serviceProvider = Session::get('service_provider');
            /** Getting current user details */
            $userDetails = Session::get('auth_user');
            $userMeta =  UserMeta::select('meta_key', 'meta_value')->where('user_id', $userDetails->id)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
            $user_shipping_data = ShippingAddress::where('user_id', $userDetails->id)->where('active', 1)->where('service_provider_id', $serviceProvider)->select('phone', 'street_address', 'address_line_2', 'city', 'state', 'zip', 'country', 'card_number', 'card_holder_name')->first();
            /** Creating an to store the required data */
            $userData = [
                'first_name' => $userDetails->first_name,
                'last_name' => $userDetails->last_name,
                'email' => $userDetails->email,
                'phone' => $userDetails->phone,
                'meta_key' => $userMeta->meta_key,
                'street_address' => $user_shipping_data->street_address,
                'address_line_2' => $user_shipping_data->address_line_2,
                'city' => $user_shipping_data->city,
                'state' => $user_shipping_data->state,
                'country' => $user_shipping_data->country,
                'zip' => $user_shipping_data->zip,
            ];
            /** Query to get the data */

            if (!empty($userData)) {
                return response()->json(["user_Details" => $userData, "msg" => "User details", "success" => true], 200);
            } else {
                return response()->json(["msg" => "User details not found.", "false" => true], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /* filter according to claim request date, claim status */
    public static function filterClaims(Request $request)
    {
        /* Auth User */
        $where = [];
        if (!empty($request->search) || !empty($request->selectedStatus) || !empty($request->selectedClaimReasons) || !empty($request->startDate) ||  !empty($request->endDate)) {
            /* Search by device name and serial number */
            if (!empty($request->search)) {
                $where = [
                    'orWhere' => [
                        'device.device_title' => ['LIKE', "%{$request->search}%"],
                        'device.serial_number' => ['LIKE', "%{$request->search}%"],
                        'device_claims.zoho_ticket_number' => ['LIKE', "%{$request->search}%"],
                    ]
                ];
            }
            if (!empty($request->selectedStatus)) {
                if ($request->selectedStatus == 'all') {
                    // $where = [];
                } else {
                    $where['where']['device_claims.user_claim_status'] = $request->selectedStatus;
                }
            }
            if (!empty($request->selectedClaimReasons)) {
                if ($request->selectedClaimReasons == 'all') {
                    // $where = [];
                } else {
                    $where['where']['device_claims.claim_reason_id'] = $request->selectedClaimReasons;
                }
            }
            if (!empty($request->startDate) ||  !empty($request->endDate)) {
                $start = Carbon::parse($request->startDate)->startOfDay(); /* 00:00:00 */
                $end = Carbon::parse($request->endDate)->endOfDay();       /* 23:59:59 */
                $where['whereBetween']['device_claims.created_at'] = [$start, $end];
            }
        }
        return $where;
    }

    /** Total Insured Claims (without role)*/
    public static function totalInsuredClaims($where = [])
    {
        $serviceProvider = Session::get('service_provider');
        $query = DeviceClaim::where('active', 1)->where('service_provider_id', $serviceProvider);
        /* Where */
        if (!empty($where) && is_array($where)) {
            foreach ($where as $field => $value) {
                if (is_array($value)) {
                    foreach ($value as $secondField => $secondValue) {
                        $query->$field($secondField, $secondValue);
                    }
                }
            }
        }
        return $query->count();
    }

    /** Recent 5 Filed Claims(All) */
    public static function recentFiledClaims($orderBy = '', $fields = [], $where = [])
    {
        $serviceProvider = Session::get('service_provider');
        $query = DB::table('device_claims')
            ->leftJoin('devices', 'device_claims.device_id', '=', 'devices.id')
            ->leftJoin('users', 'device_claims.user_id', '=', 'users.id')
            ->leftJoin('device_models', 'devices.device_model_id', '=', 'device_models.id')
            ->leftJoin('device_families', 'device_models.device_family_id', '=', 'device_families.id')
            ->where('device_claims.active', 1)
            ->where('device_claims.service_provider_id', $serviceProvider);
        /*  ->where('devices.service_provider_id', $serviceProvider)
            ->where('users.service_provider_id', $serviceProvider)
            ->where('device_models.service_provider_id', $serviceProvider)
            ->where('device_families.service_provider_id', $serviceProvider); */
        /* Select Fields */
        if (!empty($fields)) {
            $query->select($fields);
        } else {
            $query->select(
                'device_claims.id',
                'device_claims.claim_status',
                'device_claims.user_claim_status',
                'devices.device_title',
                'devices.serial_number',
                'device_families.name as device_family_name',
                'device_models.title as device_model_name',
                'users.full_name',
                $orderBy
            );
        }
        /* Where */
        if (!empty($where) && is_array($where)) {
            foreach ($where as $field => $value) {
                if (is_array($value)) {
                    foreach ($value as $secondField => $secondValue) {
                        $query->$field($secondField, $secondValue);
                    }
                }
            }
        }
        $query->orderBy($orderBy, 'desc')->limit(5);
        return $query->get();
    }

    /** Auth User Organization and sub-organization id get (IT Hod, IT Director) */
    private static function getAuthUserOrgSubOrg($userId)
    {
        /*  $serviceProvider = Session::get('service_provider');
        $orgRelationship = OrgRelationship::select('user_id', 'org_id', 'parent_org_id')->where('user_id', $userId)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
        return $orgRelationship; */
        $orgRelationship = UserDeviceController::getOrgSubOrg($userId);
        return $orgRelationship;
    }

    /** Update claim form */
    public function updateClaim(string $id)
    {
        if (!empty($id)) {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            $query = DB::table('device_claims')
                ->leftJoin('devices as device', 'device_claims.device_id', '=', 'device.id')
                ->leftJoin('device_models', 'device.device_model_id', '=', 'device_models.id')
                ->leftJoin('claim_reasons as claim_reason', 'device_claims.claim_reason_id', '=', 'claim_reason.id')
                ->leftJoin('users as user', 'device_claims.user_id', '=', 'user.id')
                ->select(
                    'device_claims.id',
                    'device_claims.device_id',
                    'device_claims.claim_reason_id',
                    'device_claims.user_claim_status',
                    'device_claims.claim_details',
                    'device_claims.first_name',
                    'device_claims.last_name',
                    'device_claims.email',
                    'device_claims.phone',
                    'device_claims.street_address',
                    'device_claims.address_line_2',
                    'device_claims.city',
                    'device_claims.state',
                    'device_claims.zip',
                    'device_claims.country',
                    'device.device_title as device_name',
                    'device.serial_number as serial_number',
                    'device_models.title as device_model_name',
                    'claim_reason.claim_reason_name',
                    'user.full_name as user_name',
                    'user.phone as user_phone',
                    'user.email as user_email'
                )->where('device_claims.active', 1)->where('device_claims.service_provider_id', $serviceProvider);
            $authUser = Session::get('auth_user');
            $org_relationship = self::getAuthUserOrgSubOrg($authUser->id);
            if (!empty($org_relationship) &&  !empty($org_relationship->org_id) && empty($org_relationship->parent_org_id)) {
                /** IT HOD */
                $query->where('device_claims.org_id', $org_relationship->org_id)->whereNot('device_claims.is_org_subsciber_claim', 1);
            } elseif (!empty($org_relationship) &&  !empty($org_relationship->org_id) && !empty($org_relationship->parent_org_id)) {
                /* IT Director */
                $query->where('device_claims.org_id', $org_relationship->parent_org_id)->where('device_claims.sub_org_id', $org_relationship->org_id)->whereNot('device_claims.is_org_subsciber_claim', 1);

            } else {
                /** Subscriber Or Organization Subscriber */
                $query->where('device_claims.user_id', $authUser->id);
            }

            $claim = $query->where('device_claims.id', $id)->first();
            if (!empty($claim)) {
                return view('user.insuredClaims.updateClaim', compact('claim'));
            } else {
                return redirect()->route('sdcsmuser.insuredclaim.trackclaim');
            }
        }
    }

    /** Update claim data */
    public function updateClaimData(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            $request->merge([
                'phone' => preg_replace('/\D/', '', $request->phone)
            ]);
            /* Validate request data */
            $validator = Validator::make($request->all(), [
                'firstName' => 'required|string|max:100',
                'lastName' => 'required|string|max:100',
                'email' => 'required|email|max:100',
                'phone' => 'required|regex:/^[0-9]{10}$/',

                'claimReason' => 'required|exists:claim_reasons,id,active,1,service_provider_id,' . $serviceProvider,
                'claimDetails' => 'required|string|max:255',

                'streetAddress' => 'required|string',
                'country' => 'required',
                'state' => 'required',
                'city' => 'required',
                'zipCode' => 'required',
            ], [
                'firstName.required' => "Please enter the first name.",
                'lastName.required' => "Please enter the last name.",
                'email.required' => "Please enter the email address.",
                'email.email' => "Please enter a valid email address.",

                'phone.required' => "Please enter the phone number.",
                'phone.regex' => "Phone Number must be numeric.",

                'claimReason.required' => "Please select the claim reason.",
                'claimReason.integer' => "Please select the claim reason.",

                'claimDetails.required' => "Please enter the claim details.",
                'claimDetails.string' => "Claim details must be in string.",

                'streetAddress.required' => "Please enter the street address.",
                'city.required' => "Please enter the city name.",
                'state.required' => "Please enter the state name.",
                'zipCode.required' => "Please enter the zipcode number.",
                'country.required' => "Please enter the country name.",
            ]);

            /* Check if validation fails */
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }

            $id = $request->claimID;

            /* Getting device claim data to update */
            $deviceClaim = DeviceClaim::where('id', $id)->where('service_provider_id', $serviceProvider)->where('active', 1)->first();
            if (empty($deviceClaim)) {
                return response()->json(["msg" => "Device claim not found.", "success" => false], 200);
            }

            /** update the device claim data */
            $deviceClaimUpdated = $deviceClaim->update([
                'claim_reason_id' => $request->claimReason,
                'claim_details' => $request->claimDetails,
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'email' => $request->email,
                'phone' => $request->phone,
                'street_address' => $request->streetAddress,
                'address_line_2' => $request->addressLine2,
                'city' => $request->city['province'],
                'state' => $request->state['abbreviation'],
                'zip' => $request->zipCode,
                'country' => $request->country['code'],
            ]);

            if (!empty($deviceClaimUpdated)) {
                return response()->json([
                    "msg" => "Device claim updated successfully.",
                    "success" => true,
                ], 200);
            } else {
                return response()->json(["msg" => "Device claim updation failed.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }
}
