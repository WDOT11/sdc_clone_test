<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\User\InsuredClaimsController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\ClaimReasonController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\SDCOptionController;
use App\Http\Controllers\Admin\ZohoController;
use App\Http\Controllers\User\UserDeviceController;
use App\Models\Admin\OrganizationClaimReason;
use App\Models\Admin\ClaimRepairStatus;
use App\Models\Admin\Device;
use App\Models\OrgRelationship;
use App\Models\User;
use App\Models\User\ShippingAddress;
use App\Models\UserMeta;
use App\Models\Transaction;
use App\Models\User\DeviceClaim;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AdminClaimsController extends Controller
{
    /* Function to get all claims */
    public function index(Request $request)
    {
        $where = self::filterClaims($request);
        $claimsData = self::paginateclaims(20, $where);
        $claimReasons = ClaimReasonController::getClaimReasonsDropdown();
        /* Total Insured Claims File*/
        $totalClaimRequests = InsuredClaimsController::totalInsuredClaims();

        $pagination = [
            'total' => $claimsData->total(),
            'per_page' => $claimsData->perPage(),
            'current_page' => $claimsData->currentPage(),
            'last_page' => $claimsData->lastPage(),
            'from' => $claimsData->firstItem(),
            'to' => $claimsData->lastItem()
        ];

        if (!empty($request->page)) {
            return response()->json(["claimsData" => $claimsData, "pagination" => $pagination, "msg" => "Paginated records here.", "success" => true], 200);
        } else {
            return view('admin.adminclaims.index', compact('claimsData', 'claimReasons', 'pagination', 'totalClaimRequests'));
        }
    }

    /** File a claim */
    public function fileClaim()
    {
        $shippingOptions =  AdminShippingOptionController::getShippingOptionsDropDown();
        return view('admin.adminclaims.fileclaim', compact('shippingOptions'));
    }

    /** To save the data of filed claims */
    public function createClaim(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');

            /* Validate to the requests */
            $validator = Validator::make($request->all(), [
                'metaValue' => 'required|string',
                'selectedUser' => 'required|exists:users,id,active,1,service_provider_id,' . $serviceProvider,
                'deviceData' => 'required|array',
                'deviceData.*.selectedDevice' => 'required|exists:devices,id,active,1,service_provider_id,' . $serviceProvider,
                'deviceData.*.claimReason' => 'required|exists:claim_reasons,id,active,1,service_provider_id,' . $serviceProvider,
                'deviceData.*.claimDetails' => 'required|string',
                'shippingOption' => [
                    Rule::requiredIf(!in_array($request->metaValue, ['org_it_hod', 'org_it_director']))
                ],
                'phone' => 'required|regex:/^[0-9]{10}$/',
                'streetAddress' => 'required|string',
                'addressLine2' => 'nullable|string',
                'city' => 'required',
                'state' => 'required',
                'zipCode' => 'required|regex:/^[0-9]{5}$/',
                'country' => 'required',
            ], [
                'selectedUser.required' => "Please select the user.",
                'selectedUser.exists' => "Please select the user.",

                'deviceData.*.selectedDevice.required' => "Please select the device.",
                'deviceData.*.selectedDevice.integer' => "Please select the device.",

                'deviceData.*.claimReason.required' => "Please select the claim reason.",
                'deviceData.*.claimReason.integer' => "Please select the claim reason.",

                'deviceData.*.claimDetails.required' => "Please enter the claim details.",
                'deviceData.*.claimDetails.string' => "Claim details must be in string.",

                'shippingOption.required' => "Please select the shipping option.",

                'phone.required' => "Please enter the phone number.",
                'phone.regex' => "Phone Number must be numeric.",

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
            $user_id = $request->selectedUser;
            $user_fullName = $request->full_name;
            $organdSubOrgId = UserDeviceController::getOrgSubOrg($user_id);
            if (empty($organdSubOrgId) && isset($request->metaValue) &&  ($request->metaValue == 'org_it_hod' || $request->metaValue == 'org_it_director')) {
                return response()->json(['success' => false, 'msg' => "Something went wrong, Please try again later."], 200);
            }
            $org_id = null;
            $sub_org_id = null;

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

            $deviceData = request()->input('deviceData', []);
            if (!empty($deviceData)) {
                foreach ($deviceData as $device) {
                    $selectedDevice = $device['selectedDevice'];
                    $selectedDeviceTitle = $device['selectedDeviceTitle'];
                    $claimReason = $device['claimReason'];
                    $claimDetails = $device['claimDetails'];
                    $device = Device::select('org_id', 'sub_org_id')->where('id', $selectedDevice)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
                    if (empty($device)) {
                        return response()->json(['success' => false, 'msg' => "Something went wrong, Please try again later."], 200);
                    }else {
                        $org_id = $device->org_id;
                        $sub_org_id = $device->sub_org_id;
                    }
                   $userData = User::select('first_name','last_name', 'email')->where('id', $user_id)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
                    $claimData = DeviceClaim::create([
                         'first_name' => $userData->first_name,
                        'last_name' => $userData->last_name,
                        'email' => $userData->email,
                        'user_id' => $user_id,
                        'org_id' => $org_id,
                        'sub_org_id' => $sub_org_id,
                        'is_org_subsciber_claim' => isset($device->is_org_subscriber_device) && $device->is_org_subscriber_device == 1 ? 1 : 0,

                        'device_id' => $selectedDevice,
                        'claim_reason_id' => $claimReason,
                        'claim_details' => $claimDetails,

                        'shipping_option' => $request->shippingOption,
                        'status_updated_date' => Carbon::now(),
                        'service_provider_id' => $serviceProvider,
                        'transaction_id' => null,

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
                    $notificationRequest = [
                        'user_id' => $claimData->user_id,
                        'message' => "A New claim is raised for device: " . $selectedDeviceTitle,
                        'notification_for' => 'claim_raised',
                        'is_claim_status_changed' => 1,
                        'device_claim_id' => $claimData->id,
                       /*  'org_id' => $claimData->org_id,
                        'sub_org_id' => $claimData->sub_org_id, */
                        'is_admin' => 2,
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
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
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

    /** To get the user data(address and other) by using the user name to show the data in claim form */
    public function getUserdataByName(Request $request)
    {
        try {
            $serviceProvider = Session::get('service_provider');
            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'search' => 'required|string|min:5',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $validator->errors(),
                ], 200);
            }
            /** Getting current user details */
            $search =  $request->search;
            /** Query to get the data */
            $userData = User::select('id','first_name', 'last_name', 'full_name', 'email', 'phone')->whereAny(['first_name', 'last_name', 'full_name', 'email'], 'LIKE', "%$search%")->where('active', 1)->where('service_provider_id', $serviceProvider)->get();
            if (!empty($userData)) {
                return response()->json(["user_Details" => $userData, "msg" => "User details", "success" => true], 200);
            } else {
                return response()->json(["msg" => "User details not found.", "false" => true], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    public static function getUserData(Request $request)
    {
        try {
            $serviceProvider = Session::get('service_provider');
            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'userId' => [
                    'required',
                    Rule::exists('users', 'id')->where(function ($query) use ($serviceProvider) {
                        $query->where('active', 1)
                            ->where('service_provider_id', $serviceProvider);
                    }),
                ],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $validator->errors(),
                ], 200);
            }
            /** Getting current user details */
            $userID =  $request->userId;
            $userMeta =  UserMeta::select('meta_key', 'meta_value')->where('user_id', $userID)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
            $user_shipping_data = ShippingAddress::select('street_address', 'address_line_2', 'city', 'state', 'zip', 'country')->where('user_id', $userID)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
            $user_data = User::select('phone')->where('id', $userID)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
            /** Creating an to store the required data */
            $userData = [
                'meta_key' => $userMeta->meta_key,
                'phone' => $user_data->phone,
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
                return response()->json(["msg" => "User details not found.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Fetch devices by type and search */
    public function fetchDeviceByName(Request $request)
    {
        try {
            $serviceProvider = Session::get('service_provider');

            $validator = Validator::make($request->all(), [
                'user_id' => [
                    'required',
                    Rule::exists('users', 'id')->where(function ($query) use ($serviceProvider) {
                        $query->where('active', 1)
                            ->where('service_provider_id', $serviceProvider);
                    }),
                ],
                'name' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $validator->errors(),
                ], 200);
            }

            $query = DB::table('devices')
                    ->leftJoin('device_models', 'devices.device_model_id', '=', 'device_models.id')
                    ->select(
                    'devices.id', 'devices.device_title', 'devices.serial_number',
                    'device_models.title as device_model_name')
                    ->where('devices.active', 1)->where('devices.service_provider_id', $serviceProvider)->whereDate('devices.expiration_date', '>=', date('Y-m-d'));
            /* Apply org relationship logic */
            $orgRelationship = UserDeviceController::getOrgSubOrg($request->user_id);

            if (!empty($orgRelationship)) {
                if ($orgRelationship->org_id && !$orgRelationship->parent_org_id) {
                    $query->where('org_id', $orgRelationship->org_id)->whereNot('is_org_subscriber_device', 1);
                } elseif ($orgRelationship->org_id && $orgRelationship->parent_org_id) {
                    $query->where('org_id', $orgRelationship->parent_org_id)->where('sub_org_id', $orgRelationship->org_id)->whereNot('is_org_subscriber_device', 1);
                }
            } else {
                $query->where('user_id', $request->user_id);
            }

            $totalDevices = $query->count();
            if ($totalDevices == 0) {
                return response()->json(['success' => true, 'msg' => "No devices found.", 'totalDevices' => 0, 'deviceData' => []], 200);
            } elseif ($totalDevices > 0 && $totalDevices <= 100) {
                $deviceData = $query->get();
            } elseif ($totalDevices > 0 && $totalDevices > 100) {
                /** Get all 20 devices list and further show by search(name or serial number) */
                if (!empty($request->name)) {
                    $query->whereAny(['device_title', 'serial_number'], 'LIKE', "%$request->name%");
                }
                $deviceData = $query->limit(20)->get();
            }
            return response()->json(['success' => true, 'totalDevices' => $totalDevices, 'deviceData' => $deviceData], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => "Something went wrong, Please try again later.", $e->getMessage()], 200);
        }
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

    /* Get All the Claims(paginate). */
    public static function paginateclaims($limit = 10, $where = [], $fields = [])
    {
        $fields = [
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
        ];
        $data = self::getClaimsQuery($where, $fields)->paginate($limit);
        return $data;
    }

    /** Get All the Claims Without Pagination */
    public static function getFullClaimDetailsQuery($where = [], $fields = [])
    {

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
                'device_claims.claim_notes',
                'device_claims.first_name',
                'device_claims.last_name',
                'device_claims.email',
                'device_claims.phone',
                'device_claims.user_claim_status',
                'device_claims.claim_details',
                'device_claims.created_at',
                'device_claims.status_updated_date',
                'device_claims.street_address',
                'device_claims.address_line_2',
                'device_claims.city',
                'device_claims.state',
                'device_claims.country',
                'device_claims.zip',
                'device_claims.phone',
                'device.device_title as device_name',
                'device.serial_number as serial_number',
                'device_models.title as device_model_name',
                'device.device_type as device_type',
                'device.created_at as device_created_at',
                'claim_reason.claim_reason_name',
                'user.full_name as user_name',
                'user.id as user_id',
                'user.phone as user_phone',
                'user.email as user_email',
                'user.created_at as user_created_at',
                'org.name as org_name',
                'org.org_type as org_type',
                'subOrg.name as sub_org_name',
                'transaction.amount as transaction_amount',
                'transaction.stripe_transaction_id as transaction_stripe_transaction_id',
                'transaction.status as transaction_status',
                'transaction.created_at as transaction_created_at',
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

    public static function getClaims($where = [], $fields = [])
    {
        $data = self::getFullClaimDetailsQuery($where, $fields)->get();
        return $data;
    }

    /* filter according to claim request date, claim status */
    public static function filterClaims(Request $request)
    {
        /* Auth User */
        $where = [];
        if (!empty($request->search) || !empty($request->selectedStatus) || !empty($request->selectedClaimReasons) || !empty($request->startDate) ||  !empty($request->endDate) || !empty($request->userId)) {
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
                    $where['where']['device_claims.claim_status'] = $request->selectedStatus;
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
            if (!empty($request->userId)) {
                $where['where']['device_claims.user_id'] = $request->userId;
            }
        }
        return $where;
    }

    /** Function to view the details of the claim */
    public function getClaimDataById(string $id)
    {
        if (!empty($id)) {
            $where['where'] = ['device_claims.id' => $id];
            $query = self::getFullClaimDetailsQuery($where);
            $claim = $query->first();
            if (!empty($claim)) {
                return response()->json(["viewData" => $claim, "msg" => "Claim Data.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Claim not found.", "success" => false], 200);
            }
        } else {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Export Devices claims report as csv */
    public function exportDeviceClaims(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate request data */
            $validator = Validator::make($request->all(), [
                'search' => 'nullable|string',
                'selectedStatus' => 'nullable|string',
                'selectedClaimReasons' => 'nullable|string',
                'startDate' => 'nullable|date',
                'endDate' => 'nullable|date',
            ], [
                'search.string' => 'Search must be a string',
                'selectedStatus.string' => 'Selected Status must be a string',
                'selectedClaimReasons.string' => 'Claim reason must be a string',
                'startDate.date' => 'Start date must be a valid date',
                'endDate.date' => 'End date must be a valid date',
            ]);
            /* Check if validation fails */
            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $validator->errors()
                ], 200);
            }

            $where = self::filterClaims($request);

            $deviceClaimData = self::getClaims($where);
            $reportData =  $deviceClaimData;
            if ($reportData->isEmpty()) {
                return response()->json(['success' => false, 'msg' => "Claims Not Found."], 200);
            }

            /** User status */
            $userClaimsStatus = config('dashboard.userClaimRepairStatus');
            // Check if userClaimsStatus is empty and not an array
            if (empty($userClaimsStatus) || !is_array($userClaimsStatus)) {
                return response()->json(['success' => false, 'msg' => "Something went wrong, Please try again later."], 200);
            }
            $userClaimsStatusMap = [];
            foreach ($userClaimsStatus as $status) {
                $userClaimsStatusMap[$status['id']] = $status['status'];
            }

            /** Zoho status */
            $claimStatus = ClaimRepairStatus::select('id', 'name')->get();
            // Check if repairStatus is empty and not an array
            if ($claimStatus->isEmpty()) {
                return response()->json(['success' => false, 'msg' => "Something went wrong, Please try again later."], 200);
            }
            // Convert to simple mapping
            $claimStatusMap = [];
            foreach ($claimStatus as $status) {
                $claimStatusMap[$status['id']] = $status['name'];
            }

            /** Countries */
            $countryMap = self::getCountries();
            /** States */
            $stateMap = self::getStates();
            /** Cities */
            $cityMap = self::getCities();
            /* Set the CSV file name */
            $fileName = 'devices_claim_report.csv';
            /* Set the CSV column headers */
            $headers = ['Device Name', 'Device Type', 'Serial Number/ Asset tag', 'Claim Reason', 'Claim Details', 'Zoho Claim Status', 'Claim Status', 'Status Updated Date', 'Claim Opened Date', 'User Name', 'User Email', 'User Phone', 'Organization', 'Sub Organization', 'Country', 'State', 'City', 'ZipCode', 'Street Address', 'Address line 2', 'Transaction ID', 'Transaction Amount', 'Transaction Status', 'Transaction Created At'];
            /* Create a new CSV file in memory */
            $output = fopen('php://memory', 'w');
            fputcsv($output, $headers);

            // Loop through the data and write it to the CSV file
            foreach ($reportData as $key => $value) {
                fputcsv($output, [
                    $value->device_model_name ?? '',
                    // $value->device_name ?? '',
                    $value->device_type == 1 ? 'Personal' : 'Organization',
                    $value->serial_number,
                    $value->claim_reason_name,
                    $value->claim_details,
                    isset($claimStatusMap[$value->claim_status]) ? $claimStatusMap[$value->claim_status] : 'Unknown',
                    isset($userClaimsStatusMap[$value->claim_status]) ? $userClaimsStatusMap[$value->claim_status] : 'Unknown',
                    $this->dateFormat($value->status_updated_date),
                    $this->dateFormat($value->created_at),
                    $value->user_name,
                    $value->user_email,
                    $value->user_phone,
                    $value->org_name ?? '',
                    $value->sub_org_name ?? '',
                    isset($countryMap[$value->country]) ? $countryMap[$value->country] : 'Unknown',
                    isset($stateMap[$value->state]) ? $stateMap[$value->state] : 'Unknown',
                    isset($cityMap[$value->city]) ? $cityMap[$value->city] : 'Unknown',
                    $value->zip,
                    $value->street_address,
                    $value->address_line_2,
                    $value->transaction_stripe_transaction_id ?? '',
                    $value->transaction_amount ?? '',
                    $value->transaction_status ?? '',
                    $this->dateFormat($value->transaction_created_at) ?? '',
                ]);
            }
            /* Rewind the memory stream to the beginning */
            rewind($output);
            /* Get the contents of the memory stream (CSV content) */
            $csvContent = stream_get_contents($output);
            /* Close the memory stream */
            fclose($output);
            /* Set the headers for the CSV download */
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ];
            /* Return the CSV content as a response */
            return response($csvContent, 200, $headers);
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Date Format in month(name),date,year */
    private function dateFormat($date)
    {
        if (empty($date)) {
            return '';
        } else {
            return date('F j, Y', strtotime($date));
        }
    }

    /** Countries */
    public static function getCountries()
    {
        /** Countries */
        $countries = config('dashboard.countries');
        /** Check if countries is empty and not an array */
        if (empty($countries) || !is_array($countries)) {
            return response()->json(['success' => false, 'msg' => "Something went wrong, Please try again later."], 200);
        }
        /* Convert to simple mapping */
        $countryMap = [];
        foreach ($countries as $country) {
            $countryMap[$country['code']] = $country['name'];
        }
        return $countryMap;
    }

    /** States */
    public static function getStates()
    {
        /** States */
        $states = config('dashboard.states');
        /* Check if states is empty and not an array */
        if (empty($states) || !is_array($states)) {
            return response()->json(['success' => false, 'msg' => "Something went wrong, Please try again later."], 200);
        }
        /* Convert to simple mapping */
        $stateMap = [];
        foreach ($states as $state) {
            $stateMap[$state['abbreviation']] = $state['name'];
        }
        return $stateMap;
    }

    /** Cities */
    public static function getCities()
    {
        /** Cities */
        $cities = config('dashboard.cities');
        /* Check if cities is empty and not an array */
        if (empty($cities) || !is_array($cities)) {
            return response()->json(['success' => false, 'msg' => "Something went wrong, Please try again later."], 200);
        }
        /* Convert to simple mapping */
        $cityMap = [];
        foreach ($cities as $city) {
            $cityMap[$city['province']] = $city['name'];
        }
        return $cityMap;
    }

    /** User Organization and sub-organization id get */
    public static function getAuthUserOrgSubOrg($userId)
    {
        $serviceProvider = Session::get('service_provider');
        $orgRelationship = OrgRelationship::select('user_id', 'org_id', 'parent_org_id', 'is_org_subscriber')->where('user_id', $userId)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
        return $orgRelationship;
    }

    /** Function to get the claim and repair status globaly */
    public static function getClaimRepairStatus()
    {
        $claimRepairStatus = ClaimRepairStatus::select('id', 'name')->get();
        if (!empty($claimRepairStatus)) {
            return response()->json(["claimRepairStatus" => $claimRepairStatus, "msg" => "Claim Repair Status.", "success" => true], 200);
        } else {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** To open the device claim update form */
    public function editDeviceClaims(string $id){
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
                )

                ->where('device_claims.id', $id)
                ->where('device_claims.active', 1)
                ->where('device_claims.service_provider_id', $serviceProvider);

            $claim = $query->first();

            if(!empty($claim)){
                return view('admin.adminclaims.updateclaim', compact('claim'));
            } else {
                return redirect()->route('admintrackclaims.index');
            }
        }
    }

    /** Update the claim data */
    public function updateDeviceClaims(Request $request){
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

    /** Get more ingo about claim (Data from ZOHO) */
    public function getClaimMoreInfo(String $zohoClaimID){
        $zohoClaimData = ZohoController::sdcsmGetRecordFromZoho('Cases', $zohoClaimID);
        if(!empty($zohoClaimData)){

            $claimData = [];
            $repairTechnician = $zohoClaimData['data'][0]['Repair_Technician'];
            $usedPart1 = $zohoClaimData['data'][0]['Part_Used_1'];
            $usedPart2 = $zohoClaimData['data'][0]['Part_Used_2'];
            $usedPart3 = $zohoClaimData['data'][0]['Part_Used_3'];
            $usedPart4 = $zohoClaimData['data'][0]['Part_Used_4'];
            $usedPart5 = $zohoClaimData['data'][0]['Part_Used_5'];

            $claimData['repairTechnician'] = $repairTechnician;
            $claimData['usedParts'] = [
                $usedPart1,
                $usedPart2,
                $usedPart3,
                $usedPart4,
                $usedPart5
            ];

            return response()->json(["claimData" => $claimData,"msg" => "Claim data found.", "success" => true], 200);
        } else {
            return response()->json(["msg" => "No claim data found.", "success" => false], 200);
        }

        /**
            Repair_Technician

            Billable_Repair
        */
    }

    /** To update the claim note */
    public function updateClaimNote(String $claimID, Request $request){
        try{
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');

            /* Getting device claim data to update */
            $deviceClaim = DeviceClaim::where('id', $claimID)->where('service_provider_id', $serviceProvider)->where('active', 1)->first();
            if (empty($deviceClaim)) {
                return response()->json(["msg" => "Device claim not found.", "success" => false], 200);
            }

            /** update the device claim data */
            $deviceClaimUpdated = $deviceClaim->update([
                'claim_notes' => $request->claimNote,
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
    /** Total Claim Requests Count */
    // private function totalClaimRequests()
    // {
    //     $serviceProvider = $serviceProvider = Session::get('service_provider');
    //     $total = DeviceClaim::where('active', 1)->where('service_provider_id', $serviceProvider)->count();
    //     return $total;
    // }
}
