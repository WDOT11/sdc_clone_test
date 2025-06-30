<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\User\UninsuredClaimsController;
use App\Http\Controllers\Admin\AdminClaimsController;
use App\Http\Controllers\Admin\DeviceModelController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\User\UserDeviceController;
use App\Models\User\DeviceRepair;
use App\Models\Admin\ClaimRepairStatus;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User\ShippingAddress;
use Illuminate\Validation\Rule;

class AdminRepairsController extends Controller
{
   /** To list the reapir requests on the track repair list page */
    public function index(Request $request)
    {
        /* $where = UninsuredClaimsController::filterRepairs($request); */
        $where = self::filterRepairs($request);
        $repairData = self::getRepairsPaginate(20, $where);
        $pagination = [
            'total' => $repairData->total(),
            'per_page' => $repairData->perPage(),
            'current_page' => $repairData->currentPage(),
            'last_page' => $repairData->lastPage(),
            'from' => $repairData->firstItem(),
            'to' => $repairData->lastItem()
        ];
        $totalRepairRequests = UninsuredClaimsController::totalUninsuredClaims();
        /* If getting page number */
        if (!empty($request->page)) {
            return response()->json(["repairData" => $repairData, "pagination" => $pagination, "msg" => "Paginated records here.", "success" => true], 200);
        } else {
            return view('admin.adminrepairs.index', compact('repairData', 'pagination', 'totalRepairRequests'));
        }
    }
    /** To view the repair request form */
    public function repairRequest()
    {
        /** get all the repair reasons from the table */
        $repair_reasons = RepairReasonController::getRepairReasonsDropdown();
        $shippingOptions =  AdminRepairsShippingOptionController::getShippingOptionsDropDown();
        return view('admin.adminrepairs.repairrequest', compact('repair_reasons', 'shippingOptions'));
    }

    /** To store Repair Request Details */
    public function store(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'selectedUser' => 'required|exists:users,id,active,1,service_provider_id,' . $serviceProvider,
                'firstName' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'phone' => 'required|regex:/^(\+?\d{1,3}[- ]?)?\d{10}$/',
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
                // 'cardHolderName' => 'required|string|max:40',
                'streetAddress' => 'required|string',
                'addressLine1' => 'nullable|string|max:255',
                'country' => 'required|string',
                'state' => 'required|string',
                'city' => 'required|string',
                'zipCode' => 'required|regex:/^[0-9]{5}$/',
            ], [
                'selectedUser.required' => 'User selection is required.',
                'selectedUser.exists' => 'The selected user is invalid.',
                'metaValue.required' => 'Meta value is required.',
                'firstName.required' => 'First name is required.',
                'firstName.string' => 'First name must be a string.',
                'firstName.max' => 'First name may not be greater than 255 characters.',
                'lastName.required' => 'Last name is required.',
                'lastName.string' => 'Last name must be a string.',
                'lastName.max' => 'Last name may not be greater than 255 characters.',
                'phone.required' => 'Phone number is required.',
                'phone.regex' => 'Please Enter valid phone number.',
                'email.required' => 'Email is required.',
                'email.email' => 'Email is not valid.',
                'email.max' => 'Email may not be greater than 255 characters.',
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

            $user_id = $request->selectedUser;
            $devices = $request->devices;
            $organdSubOrgId = UserDeviceController::getOrgSubOrg($user_id);
            if (empty($organdSubOrgId) && isset($request->metaValue) &&  ($request->metaValue == 'org_it_hod' || $request->metaValue == 'org_it_director')) {
                return response()->json(['success' => false, 'msg' => "Something went wrong, Please try again later."], 200);
            }
            $orgId = null;
            $subOrgId = null;

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

            $data = [];
            /* creating the request for repair */
            foreach ($devices as $key => $value) {
                $deviceModelName = $value['deviceModelName'];
                $where =  [
                    'where' => [
                        'device_plans.device_model_id' => $value['modelId']
                    ]
                ];
                $repairFee = DeviceRepairPlanController::getDeviceSinglePlan($where,['device_plans.price']);
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
                    'repair_amount' => isset($repairFee) && isset($repairFee->price) ? $repairFee->price : null,
                    'transaction_id' => null,
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
                $notificationRequest = [
                    'user_id' => $deviceRepair->user_id,
                    'message' => "A new repair for your device {$deviceModelName} with {$deviceRepair->device_serial_number} is requested.",
                    'notification_for' => 'repair_request',
                    'device_repair_id' => $deviceRepair->id,
                    'is_repair_status_changed' => 1,
                    'org_id' => null,
                    'sub_org_id' => null,
                    'is_admin' => 2,
                 /*    'org_id' => $deviceRepair->org_id,
                    'sub_org_id' => $deviceRepair->sub_org_id, */
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
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => "Something went wrong, please try again later."], 200);
        }
    }

    /** Device Models */
    public function fetchDeviceModelsByName(Request $request)
    {
        return DeviceModelController::fetchUninsuredDeviceModels($request);
    }

    /** To view the repair request details */
    public function getRepairDataById(String $id)
    {
        if (!empty($id)) {
            $where['where']['device_repairs.id'] = $id;
            $repair = self::getFullRepairDetailsQuery($where)->first();
            if (!empty($repair)) {
                return response()->json(["viewData" => $repair, "msg" => "Repair Data.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Repair not found.", "success" => false], 200);
            }
        } else {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
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
        /* Check if there are any conditions to apply */
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

    /** Get All the Repair Requests Query */
    public static function getFullRepairDetailsQuery($where = [], $fields = [])
    {

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
            ->where('device_repairs.active', 1)
            ->where('device_repairs.service_provider_id', $serviceProvider);

        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select(
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

        return $query->orderBy('device_repairs.created_at', 'desc');
    }

    /** Get All the Repairs Without Pagination */
    public static function getRepairs($where = [], $fields = [])
    {
        $data = self::getFullRepairDetailsQuery($where, $fields)->get();
        return $data;
    }

    /* filter according to repair request date repair status */
    public static function filterRepairs(Request $request)
    {
        $where = [];
        if (!empty($request->search) || !empty($request->selectedStatus) || !empty($request->startDate) ||  !empty($request->endDate) || !empty($request->userId)) {
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
                    $where['where']['device_repairs.repair_status'] = $request->selectedStatus;
                }
            }
            if (!empty($request->startDate) ||  !empty($request->endDate)) {
                $start = Carbon::parse($request->startDate)->startOfDay(); /* 00:00:00 */
                $end = Carbon::parse($request->endDate)->endOfDay();       /* 23:59:59 */
                $where['whereBetween']['device_repairs.created_at'] = [$start, $end];
            }
            if (!empty($request->userId)) {
                $where['where']['device_repairs.user_id'] = $request->userId;
            }
        }
        return $where;
    }

    /** Export Devices Repair Requests */
    public function exportRepairRequests(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate request data */
            $validator = Validator::make($request->all(), [
                'selectedStatus' => 'nullable|string',
                'startDate' => 'nullable|date',
                'endDate' => 'nullable|date',
            ], [
                'selectedStatus.string' => 'Selected Status must be a string',
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

            $where = self::filterRepairs($request);
            $fields = [
                'device.title as device_name',
                'device_family.name as device_family_name',
                'device_repairs.device_serial_number as serial_number',
                'device_repairs.repair_details',
                'device_repairs.first_name as owner_first_name',
                'device_repairs.last_name as owner_last_name',
                'device_repairs.email as owner_email',
                'device_repairs.phone as owner_phone',
                'device_repairs.repair_status',
                'device_repairs.user_repair_status',
                'device_repairs.status_updated_date',
                'device_repairs.created_at',
                'device_repairs.street_address',
                'device_repairs.address_line_2',
                'device_repairs.city',
                'device_repairs.state',
                'device_repairs.zip',
                'device_repairs.country',
                'device_repairs.repair_amount',
                'repair_reason.repair_reason_name as repair_reason',
                'org.name as org_name',
                'subOrg.name as sub_org_name',
                'transaction.amount as transaction_amount',
                'transaction.status as transaction_status',
                'transaction.stripe_transaction_id as transaction_id',
                'transaction.created_at as transaction_created_at',
            ];
            $deviceRepairsData = self::getRepairs($where, $fields);
            $reportData =  $deviceRepairsData;
            if ($reportData->isEmpty()) {
                return response()->json(['success' => false, 'msg' => "No Repair Requests Found."], 200);
            }

            /** User status */
            $userRepairStatus = config('dashboard.userClaimRepairStatus');
            // Check if repairStatus is empty and not an array
            if (empty($userRepairStatus) || !is_array($userRepairStatus)) {
                return response()->json(['success' => false, 'msg' => "Something went wrong, Please try again later."], 200);
            }
            $userRepairStatusMap = [];
            foreach ($userRepairStatus as $status) {
                $userRepairStatusMap[$status['id']] = $status['status'];
            }

            /** To get the zoho status */
            $repairStatus = ClaimRepairStatus::select('id', 'name')->get();

            // Check if repairStatus is empty and not an array
            if ($repairStatus->isEmpty()) {
                return response()->json(['success' => false, 'msg' => "Something went wrong, Please try again later."], 200);
            }
            // Convert to simple mapping
            $zohoRepairStatusMap = [];
            foreach ($repairStatus as $status) {
                $zohoRepairStatusMap[$status['id']] = $status['name'];
            }
            /** Countries */
            $countryMap = AdminClaimsController::getCountries();
            /** States */
            $stateMap = AdminClaimsController::getStates();
            /** Cities */
            $cityMap = AdminClaimsController::getCities();

            /* Set the CSV file name */
            $fileName = 'devices_repair_report.csv';
            /* Set the CSV column headers */
            $headers = ['Device Model Name', 'Serial Number/ Asset tag', 'Repair Reason', 'Repair Details', 'Zoho Repair Status', 'Repair Status', 'Status Updated Date', 'Repair Opened Date', 'User Name', 'User Email', 'User Phone', 'Organization', 'Sub Organization', 'Country', 'State', 'City', 'ZipCode', 'Street Address', 'Address line 2', 'Repair Fee', 'Transaction ID', 'Transaction Status', 'Transaction Created At'];
            /* Create a new CSV file in memory */
            $output = fopen('php://memory', 'w');
            fputcsv($output, $headers);

            // Loop through the data and write it to the CSV file
            foreach ($reportData as $key => $value) {
                fputcsv($output, [
                    $value->device_name . ' (' . $value->device_family_name . ')' ?? '',
                    $value->serial_number,
                    $value->repair_reason,
                    $value->repair_details,
                    isset($zohoRepairStatusMap[$value->repair_status]) ? $zohoRepairStatusMap[$value->repair_status] : 'Unknown',
                    isset($userRepairStatusMap[$value->user_repair_status]) ? $userRepairStatusMap[$value->user_repair_status] : 'Unknown',
                    $this->dateFormat($value->status_updated_date),
                    $this->dateFormat($value->created_at),
                    $value->owner_first_name . ' ' . $value->owner_last_name,
                    $value->owner_email,
                    $value->owner_phone,
                    $value->org_name ?? '',
                    $value->sub_org_name ?? '',
                    isset($countryMap[$value->country]) ? $countryMap[$value->country] : 'Unknown',
                    isset($stateMap[$value->state]) ? $stateMap[$value->state] : 'Unknown',
                    isset($cityMap[$value->city]) ? $cityMap[$value->city] : 'Unknown',
                    $value->zip,
                    $value->street_address,
                    $value->address_line_2,
                    // $value->transaction_amount,
                    $value->repair_amount,
                    $value->transaction_id,
                    isset($value->transaction_status) ? $value->transaction_status : '',
                    $this->dateFormat($value->transaction_created_at),
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

    /** Total Claim Requests Count */
    // private function totalRepairRequests()
    // {
    //     $serviceProvider = $serviceProvider = Session::get('service_provider');
    //     $total = DeviceRepair::where('active', 1)->where('service_provider_id', $serviceProvider)->count();
    //     return $total;
    // }
}
