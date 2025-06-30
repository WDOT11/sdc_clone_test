<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Admin\AdminRepairsShippingOptionController;
use App\Http\Controllers\Admin\ClaimReasonController;
use App\Http\Controllers\Admin\DeviceBrandController;
use App\Http\Controllers\Admin\DeviceModelController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\RepairReasonController;
use App\Http\Controllers\Admin\SDCOptionController;
use App\Http\Controllers\Admin\ZohoController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\User\UserDeviceController;
use App\Models\Admin\OrganizationClaimReason;
use App\Models\OrgRelationship;
use App\Models\Transaction;
use App\Models\User;
use App\Models\User\DeviceRepair;
use App\Models\User\ShippingAddress;
use App\Models\UserMeta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UninsuredClaimsController extends Controller
{

    public function requestRepair()
    {
        /** get all the repair reasons from the table */
        $repair_reasons = RepairReasonController::getRepairReasonsDropdown();
        $shippingOptions =  AdminRepairsShippingOptionController::getShippingOptionsDropDown();
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        /** Getting current user details */
        $userDetails = Session::get('auth_user');
        $userMeta =  UserMeta::select('meta_key', 'meta_value')->where('user_id', $userDetails->id)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
        /** Creating an to store the required data */
        $userData = [
            'firstName' => $userDetails->first_name,
            'lastName' => $userDetails->last_name,
            'email' => $userDetails->email,
            'phone' => $userDetails->phone,
            'meta_key' => $userMeta->meta_key,
        ];
        $stripePublicKey = SDCOptionController::getOption('sdcsm_stripe_publishable_key', $serviceProvider);
        config(['cashier.key' => $stripePublicKey]);
        return view('user.uninsuredRepairs.repairRequest', compact('repair_reasons', 'userData', 'shippingOptions', 'stripePublicKey'));
    }

    /** Store Repair Devices */
    public function store(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'firstName' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'phone' => 'required|regex:/^[0-9]{10}$/',
                'email' => 'required|email|max:255',
                'metaValue' => 'required|string',
                'devices' => 'required|array',
                'devices.*.modelId' => 'required|exists:device_models,id,active,1,service_provider_id,' . $serviceProvider,
                'devices.*.serialNumber' => 'required|string|max:255',
                'devices.*.repairReason' => 'required|exists:repair_reasons,id,active,1,service_provider_id,' . $serviceProvider,
                'devices.*.repairDetails' => 'required|string|max:255',

                'shippingOption' => [
                    Rule::requiredIf(!in_array($request->metaValue, ['org_it_hod', 'org_it_director'])),
                ],
                'cardHolderName' => 'nullable|string|max:40',
                /*  'cardHolderName' => [
                    Rule::requiredIf(!in_array($request->metaValue, ['org_it_hod', 'org_it_director'])),
                ], */
                'streetAddress' => 'required|string',
                'addressLine1' => 'nullable|string|max:255',
                'country' => 'required|string',
                'state' => 'required|string',
                'city' => 'required|string',
                'zipCode' => 'required|regex:/^[0-9]{5}$/',
            ], [
                'firstName.required' => 'First name is required.',
                'firstName.string' => 'First name must be a string.',
                'firstName.max' => 'First name cannot be more than 255 characters.',
                'lastName.required' => 'Last name is required.',
                'lastName.max' => 'Last name cannot be more than 255 characters.',
                'lastName.string' => 'Last name must be a string.',
                'phone.required' => 'Phone number is required.',
                'phone.regex' => 'Please Enter valid phone number.',
                'email.required' => 'Email is required.',
                'email.email' => 'Email is not valid.',
                'email.max' => 'Email cannot be more than 255 characters.',
                'devices.*.modelId.required' => 'Model is required.',
                'devices.*.serialNumber.required' => 'Serial number is required.',
                'devices.*.repairReason.required' => 'Repair reason is required.',
                'devices.*.repairDetails.required' => 'Repair details are required.',
                'devices.*.modelId.exists' => 'The selected model is invalid.',
                'devices.*.repairReason.exists' => 'The selected repair reason is invalid.',
                'devices.*.repairDetails.max' => 'The repair details cannot be more than 255 characters.',
                'devices.*.serialNumber.string' => 'Serial number must be a string.',
                'devices.*.modelId.integer' => 'Model must be an integer.',
                'devices.*.repairReason.integer' => 'Repair reason must be an integer.',
                'devices.*.repairReason.exists' => 'The selected repair reason is invalid.',
                'devices.*.repairDetails.required' => 'Repair details are required.',
                'shippingOption.required' => 'Shipping Option is required.',
                'shippingOption.exists' => 'The selected shipping option is invalid.',
                // 'cardHolderName.required' => 'Please Enter Cardholder Name.',
                'cardHolderName.string' => 'Cardholder name must be a string.',
                'cardHolderName.max' => 'Cardholder name cannot be more than 40 characters.',
                'streetAddress.required' => 'Street address is required.',
                'addressLine1.string' => 'Address line 1 must be a string.',
                'country.required' => 'Country is required.',
                'state.required' => 'State is required.',
                'city.required' => 'City is required.',
                'zipCode.required' => 'Zip code is required.',
                'zipCode.regex' => 'Please enter valid zip code.',
            ]);

            /* If Validation's fails */
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }

            $userData = Session::get('auth_user');
            $user_id = $userData->id;
            $user_fullName = $userData->full_name;
            $devices = $request->devices;
            $organdSubOrgId = $this->getAuthUserOrgSubOrg($user_id);
            if (empty($organdSubOrgId) && isset($request->metaValue) &&  ($request->metaValue == 'org_it_hod' || $request->metaValue == 'org_it_director')) {
                return response()->json(['success' => false, 'msg' => "Something went wrong, Please try again later."], 200);
            }
            $orgId = null;
            $subOrgId = null;

            $transactionId = null;

            if (isset($organdSubOrgId->org_id) && isset($organdSubOrgId->parent_org_id)) {
                $orgId = $organdSubOrgId->parent_org_id;
                $subOrgId = $organdSubOrgId->org_id;
            } elseif (isset($organdSubOrgId->org_id) && is_null($organdSubOrgId->parent_org_id)) {
                $orgId = $organdSubOrgId->org_id;
                $subOrgId = null;
            } else {
                $orgId = null;
                $subOrgId = null;
            }
            $isPaymentSucess = false;
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

                    foreach ($devices as $key => $device) {
                        $deviceTitle = ($device['deviceModelName'] ?? 'Device') . ' (' . ($device['serialNumber'] ?? '-') . ')';
                        $repairFee = number_format($device['repairAmount'] ?? 0, 2);
                        $deviceDescriptions[] = "{$deviceTitle}: \${$repairFee}";
                        $totalShipping = $request->shippingAmount ?? 0;
                    }

                    $shippingFormatted = number_format($totalShipping, 2);
                    $description = 'Repair Charges - ' . implode(', ', $deviceDescriptions) . ". Shipping: \${$shippingFormatted}";
                    /* One-time charge */
                    $payment = $userData->charge($request->amount * 100, $request->paymentMethodId, [
                        'payment_method_types' => ['card'],
                        'off_session' => true,
                        'confirm' => true,
                        'description' => $description,
                        // 'description' => 'Shipping charges(Repair Devices).',
                        'metadata' => [
                            'payment_for' => 3,
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
                $data = [];
                if (!empty($request->cardHolderName)) {
                    /** Update card Holder Name */
                    User::where('id', $user_id)->update(['cardholder_name' => $request->cardHolderName]);
                }

                /* creating the request for repair */
                foreach ($devices as $key => $value) {
                    $deviceModelName = $value['deviceModelName'];
                    $repairFee = number_format($value['repairAmount'] ?? 0, 2);
                    $data = [
                        'user_id' => $user_id,
                        'org_id' => $orgId,
                        'sub_org_id' => $subOrgId,
                        'first_name' => $request->firstName,
                        'last_name' => $request->lastName,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'device_model_id' => $value['modelId'],
                        'device_serial_number' => $value['serialNumber'],
                        'repair_reason_id' => $value['repairReason'],
                        'repair_details' => $value['repairDetails'],
                        'repair_amount' => $repairFee ?? null,
                        'transaction_id' => $transactionId ?? null,
                        'shipping_option' => $request->shippingOption,
                        'street_address' => $request->streetAddress,
                        'address_line_2' => $request->addressLine1,
                        'country' => $request->country,
                        'state' => $request->state,
                        'city' => $request->city,
                        'zip' => $request->zipCode,
                        'service_provider_id' => $serviceProvider,
                    ];
                    $deviceRepair = DeviceRepair::create($data);

                    /** Inserting the data in the zoho */
                    $zohoResponse = ZohoController::sdcsmInsertUninsuredRepairsInZoho($deviceRepair);

                    if (empty($deviceRepair->id) || empty($zohoResponse)) {
                        return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                    }
                    if (!empty($deviceRepair->transaction_id)) {
                        Transaction::where('stripe_transaction_id', $deviceRepair->transaction_id)->update(['device_repair_id' => $deviceRepair->id]);
                    }
                    $notificationRequest = [
                        'user_id' => $deviceRepair->user_id,
                        'message' => $user_fullName . "  has filed a repair request for the device model: " . $deviceModelName,
                        'notification_for' => 'repair_request',
                        'device_repair_id' => $deviceRepair->id,
                        'is_repair_status_changed' => 1,
                        'org_id' => $deviceRepair->org_id,
                        'sub_org_id' => $deviceRepair->sub_org_id,
                        'user_target_link' => 'sdcsmuser/user-track-repairs',
                        'admin_target_link' => 'smarttiusadmin/track-repairs',
                        'service_provider_id' => $deviceRepair->service_provider_id,
                    ];
                    NotificationController::addNotification($notificationRequest);
                }

                /** To add or update the shipping address */
                if ($request->isAddressEdit == 1) {
                    $shippingData = ShippingAddress::updateOrCreate(
                        ['user_id' => $user_id],
                        [
                            'street_address' => $request->streetAddress,
                            'address_line_2' => $request->addressLine1,
                            'city' => $request->city,
                            'state' => $request->state,
                            'zip' => $request->zipCode,
                            'country' => $request->country,
                        ]
                    );
                    if (empty($shippingData->id)) {
                        return response()->json(["msg" => "Something went wrong, please try again later.", "success" => false], 200);
                    }
                }
                if (count($devices) > 0) {
                    return response()->json(["msg" => "Device Repair request submitted successfully", "success" => true], 200);
                } else {
                    return response()->json(['success' => false, 'msg' => "Failed to submit repair request"], 200);
                }
            } else {
                return response()->json(["success" => false, "msg" => "Payment Failed."], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => "Something went wrong, please try again later."], 200);
        }
    }

    /** To list the repair requests on the track repair list page */
    public function trackRepairs(Request $request)
    {
        $where = $this->filterRepairs($request);
        $repairData = self::getRepairsPaginate(20, $where);
        $pagination = [
            'total' => $repairData->total(),
            'per_page' => $repairData->perPage(),
            'current_page' => $repairData->currentPage(),
            'last_page' => $repairData->lastPage(),
            'from' => $repairData->firstItem(),
            'to' => $repairData->lastItem()
        ];
        /* If getting page number */
        if (!empty($request->page)) {
            return response()->json(["repairData" => $repairData, "pagination" => $pagination, "msg" => "Paginated records here.", "success" => true], 200);
        } else {
            return view('user.uninsuredRepairs.trackRepair', compact('repairData', 'pagination'));
        }
    }

    /** Repairs get query */
    public static function getRepairQuery($where = [], $fields = [])
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $query = DB::table('device_repairs')
            ->leftJoin('device_models as device', 'device_repairs.device_model_id', '=', 'device.id')
            ->leftJoin('device_families as device_family', 'device.device_family_id', '=', 'device_family.id')
            ->leftJoin('repair_reasons as repair_reason', 'device_repairs.repair_reason_id', '=', 'repair_reason.id')
            ->leftJoin('organizations as org', 'device_repairs.org_id', '=', 'org.id')
            ->leftJoin('sub_organizations as subOrg', 'device_repairs.sub_org_id', '=', 'subOrg.id')
            ->where('device_repairs.active', 1)
            ->where('device_repairs.service_provider_id', $serviceProvider);

        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select(
                'device_repairs.id',
                'device_repairs.zoho_repair_id',
                'device_repairs.zoho_ticket_number',
                'device_repairs.email',
                'device_repairs.created_at',
                'device_repairs.device_serial_number',
                'device_repairs.repair_status',
                'device_repairs.user_repair_status',
                'org.name as org_name',
                'subOrg.name as sub_org_name',
                'device.title as device_name',
                'device_family.name as device_family_name',
                'repair_reason.repair_reason_name as repair_reason'
            );
        }
        /* Auth User */
        $authUser = Session::get('auth_user');
        $org_relationship = UserDeviceController::getOrgSubOrg($authUser->id);
        if (!empty($org_relationship) && !empty($org_relationship->org_id) && empty($org_relationship->parent_org_id)) {
            /** IT HOD */
            $where['where']['device_repairs.org_id'] = $org_relationship->org_id;
        } elseif (!empty($org_relationship) && !empty($org_relationship->org_id) && !empty($org_relationship->parent_org_id)) {
            /* IT Director */
            $where['where']['device_repairs.org_id'] = $org_relationship->parent_org_id;
            $where['where']['device_repairs.sub_org_id'] = $org_relationship->org_id;
        } else {
            /** Subscriber or Organization Subscriber */
            $where['where']['device_repairs.user_id'] = $authUser->id;
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
                                if ($field == 'whereIn') {
                                    $query->$field($secondField, $secondValue);
                                } else {
                                    $query->$field($secondField, $secondValue[0], $secondValue[1]);
                                }
                            } else {
                                $query->$field($secondField, $secondValue);
                            }
                        }
                    }
                }
            }
        }
        return $query->orderBy('device_repairs.created_at', 'desc');
    }

    /** Get all the repairs with paginate */
    public static function getRepairsPaginate($limit = 10, $where = [], $fields = [])
    {

        $query = self::getRepairQuery($where);
        return $query->paginate($limit);
    }

    /** Get All the Repairs Without Pagination */
    public static function getRepairs($where = [], $fields = [])
    {
        $data = self::getRepairQuery($where, $fields)->get();
        return $data;
    }

    /** uninsured and show everywhere device models  */
    public function uninsuredDevices(Request $request)
    {
        /* try {
            $modelData = DeviceModelController::getUninsuredAndShowDevices();
            return response()->json(['success' => true, 'deviceModels' => $modelData], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => "Failed to get device models."], 200);
        } */
        return DeviceModelController::fetchUninsuredDeviceModels($request);
    }

    /* Get repair according to repair id and default latest */
    public function getRepairById(string $id)
    {
        if (!empty($id)) {

            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            $query = DB::table('device_repairs')
                ->leftJoin('device_models as device', 'device_repairs.device_model_id', '=', 'device.id')
                ->leftJoin('device_families as device_family', 'device.device_family_id', '=', 'device_family.id')
                ->leftJoin('repair_reasons as repair_reason', 'device_repairs.repair_reason_id', '=', 'repair_reason.id')
                ->leftJoin('users as user', 'device_repairs.user_id', '=', 'user.id')
                ->leftJoin('organizations as org', 'device_repairs.org_id', '=', 'org.id')
                ->leftJoin('sub_organizations as subOrg', 'device_repairs.sub_org_id', '=', 'subOrg.id')
                ->leftJoin('transactions as transaction', 'device_repairs.transaction_id', '=', 'transaction.stripe_transaction_id')
                ->select(
                    'device_repairs.*',
                    'org.name as org_name',
                    'subOrg.name as sub_org_name',
                    'device.title as device_name',
                    'device_family.name as device_family_name',
                    'user.full_name as user_name',
                    'user.email as user_email',
                    'user.created_at as user_registration_date',
                    'repair_reason.repair_reason_name as repair_reason',
                    // 'transaction.amount as transaction_amount',
                    'transaction.status as transaction_status',
                    'transaction.stripe_transaction_id as transaction_id',
                    'transaction.created_at as transaction_created_at',
                )
                ->where('device_repairs.id', $id)
                ->where('device_repairs.active', 1)
                ->where('device_repairs.service_provider_id', $serviceProvider);
            /* Auth User */
            $authUser = Session::get('auth_user');
            $org_relationship = UserDeviceController::getOrgSubOrg($authUser->id);
            if (!empty($org_relationship) && !empty($org_relationship->org_id) && empty($org_relationship->parent_org_id)) {
                /** IT HOD */
                $where['where']['device_repairs.org_id'] = $org_relationship->org_id;
            } elseif (!empty($org_relationship) && !empty($org_relationship->org_id) && !empty($org_relationship->parent_org_id)) {
                /* IT Director */
                $where['where']['device_repairs.org_id'] = $org_relationship->parent_org_id;
                $where['where']['device_repairs.sub_org_id'] = $org_relationship->org_id;
            } else {
                /** Subscriber or Organization Subscriber */
                $where['where']['device_repairs.user_id'] = $authUser->id;
            }

            $repair = $query->first();
            if (!empty($repair)) {
                return response()->json(["viewData" => $repair, "msg" => "Repair Data.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Repair not found.", "success" => false], 200);
            }
        } else {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /* filter according to repair request date repair status */
    public static function filterRepairs(Request $request)
    {
        $where = [];
        if (!empty($request->search) || !empty($request->selectedStatus) || !empty($request->startDate) ||  !empty($request->endDate)) {
            /* Search by device model name and serial number  or ticket number*/
             if (!empty($request->search)) {
                $where = [
                    'orWhere' => [
                        // 'device.title' => ['LIKE', "%{$request->search}%"],
                        'device_repairs.device_serial_number' => ['LIKE', "%{$request->search}%"],
                        'device_repairs.zoho_ticket_number' => ['LIKE', "%{$request->search}%"],
                    ]
                ];
            }
            if (!empty($request->selectedStatus)) {
                if ($request->selectedStatus == 'all') {
                    // $where = [];
                } else {
                    $where['where']['device_repairs.user_repair_status'] = $request->selectedStatus;
                }
            }
            if (!empty($request->startDate) ||  !empty($request->endDate)) {
                $start = Carbon::parse($request->startDate)->startOfDay(); /* 00:00:00 */
                $end = Carbon::parse($request->endDate)->endOfDay();       /* 23:59:59 */
                $where['whereBetween']['device_repairs.created_at'] = [$start, $end];
            }
        }
        return $where;
    }

    /** Total Uninsured Claims */
    public static function totalUninsuredClaims($where = [])
    {
        $serviceProvider = Session::get('service_provider');
        $query = DeviceRepair::where('active', 1)->where('service_provider_id', $serviceProvider);
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

    /** Recent 5 Uninsured Claims(All) requests without role */
    public static function recentUninsuredClaims($orderBy = '', $fields = [], $where = [])
    {
        $serviceProvider = Session::get('service_provider');
        $query = DB::table('device_repairs')
            ->leftJoin('device_models', 'device_repairs.device_model_id', '=', 'device_models.id')
            ->leftJoin('device_families', 'device_models.device_family_id', '=', 'device_families.id')
            ->where('device_repairs.active', 1)
            ->where('device_repairs.service_provider_id', $serviceProvider)
            ->where('device_models.service_provider_id', $serviceProvider)
            ->where('device_families.service_provider_id', $serviceProvider);
        /* Select Fields */
        if (!empty($fields)) {
            $query->select($fields);
        } else {
            $query->select(
                'device_repairs.id',
                'device_repairs.first_name',
                'device_repairs.last_name',
                'device_repairs.device_serial_number',
                'device_repairs.repair_status',
                'device_repairs.user_repair_status',
                'device_models.title as device_model_name',
                'device_families.name as device_family_name',
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

    /** Auth User Organization and sub-organization id get */
    private function getAuthUserOrgSubOrg($userId)
    {
        $orgRelationship = UserDeviceController::getOrgSubOrg($userId);
        return $orgRelationship;
    }
}
