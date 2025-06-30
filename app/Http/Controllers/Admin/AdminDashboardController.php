<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminDeviceController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\User\InsuredClaimsController;
use App\Http\Controllers\User\UninsuredClaimsController;
use App\Http\Controllers\User\UserDeviceController;
use App\Http\Controllers\User\SupportTicketController;
use App\Models\Admin\MediaLibraries;
use App\Models\User;
use Carbon\Carbon;

use function PHPUnit\Framework\isEmpty;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AdminDashboardController extends Controller
{
    

    /** To upload the service agreement from the wordpress database */
    public function uploadServiceAgreement(Request $request)
    {
        // dd('file upload route hit');
        try {
            if ($request->org_service_agreement == 'pdf') {
                if ($request->hasFile('org_agreement_pdf')) {
                    $serviceAgreePdf = $request->file('org_agreement_pdf');
                    /* Check if the image is valid */
                    if (empty($serviceAgreePdf)) {
                        return response()->json(["msg" => "No PDF provided.", "success" => false], 200);
                    }
                    /* Generate a unique file(service agreement) name */
                    $serviceAgreeExtension = $serviceAgreePdf->getClientOriginalExtension();
                    $serviceAgreeName = pathinfo($serviceAgreePdf->getClientOriginalName(), PATHINFO_FILENAME) . '-' . substr(str_shuffle("0123456789"), 0, 4) . '.' . $serviceAgreeExtension;
                    /* Failed to generate unique file(service agreement) name.*/
                    if (empty($serviceAgreeName)) {
                        return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                    }
                    /* Store the file in storage/app/public/organizations/service_agreement */
                    $serviceAgreePdfPath = $serviceAgreePdf->storeAs('organizations/service_agreement', $serviceAgreeName, 'public');
                    if (empty($serviceAgreePdfPath)) {
                        return response()->json(["msg" => "Failed to upload pdf.", "success" => false], 200);
                    }
                    /* create and get (id) media library for service agreement */
                    $serviceAgreeMediaRequest = [
                        'fileName' => $serviceAgreeName,
                        'filePath' => '/storage/organizations/service_agreement/',
                        'fileType' => $serviceAgreeExtension,

                    ];

                    $serviceAgreeMediaLibrary = MediaLibraries::create([
                        'file_name' => $serviceAgreeMediaRequest['fileName'],
                        'file_path' => $serviceAgreeMediaRequest['filePath'],
                        'file_type' => $serviceAgreeMediaRequest['fileType'],
                        'service_provider_id' => 1
                    ]);
                    if ($serviceAgreeMediaLibrary->wasRecentlyCreated) {
                        $serviceAgreeId = $serviceAgreeMediaLibrary->id;
                    }
                    /* Failed to create media library record for service agreement */
                    if (empty($serviceAgreeId)) {
                        return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                    }
                }
            }
            /* Save the file path to the database or perform any other necessary actions */
            return response()->json(["msg" => "Service Agreement uploaded successfully.", "success" => true, "service_agreement_id" => $serviceAgreeId], 200);
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later." . $e->getMessage(), "success" => false], 200);
        }
    }

    /* Admin Dashboard  View*/
    public function index()
    {
        /* Total Devices */
        $totalDevices = AdminDeviceController::totalDevices();
        /* Total Covered Devices */
        $totalCoveredDevices =  AdminDeviceController::totalCoveredDevices();
        /* Total Uncovered Devices */
        $totalUncoveredDevices = AdminDeviceController::totalUncoveredDevices();
        /* Total Insured Claims File*/
        $totalInsuredClaims = InsuredClaimsController::totalInsuredClaims();
        /* Total Uninsured Claims File */
        $totalUninsuredClaims = UninsuredClaimsController::totalUninsuredClaims();
        /* Call Dashboard File */
        return view('admin.home.index', compact('totalDevices', 'totalCoveredDevices', 'totalUncoveredDevices', 'totalInsuredClaims', 'totalUninsuredClaims'));
    }

    /* Recent Insured Claims */
    public function recentClaims()
    {
        try {
            $orderBy = 'device_claims.created_at';
            $claimDevicesData = InsuredClaimsController::recentFiledClaims($orderBy);
            if ($claimDevicesData->isEmpty()) {
                return response()->json(['msg' => 'No Records Fond', "success" => true], 200);
            } else {
                return response()->json(['msg' => 'Recent Insured Claims', "success" => true, 'claimDevicesData' => $claimDevicesData], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /* Most recent claim status changed devices */
    public function recentClaimStatusChangedDevices()
    {
        try {
            $orderBy = 'device_claims.status_updated_date';
            $fields = [
                'device_claims.id',
                'device_claims.claim_status',
                'device_claims.user_claim_status',
                'devices.device_title',
                'devices.serial_number',
                'device_models.title as device_model_name',
                // 'device_families.name as device_family_name',
                'users.full_name',
                $orderBy
            ];
            $claimDevicesData = InsuredClaimsController::recentFiledClaims($orderBy, $fields);
            if ($claimDevicesData->isEmpty()) {
                return response()->json(['msg' => 'No Records Fond', "success" => true], 200);
            } else {
                return response()->json(['msg' => 'Most Recent Status Changed Devices', "success" => true, 'claimDevicesData' => $claimDevicesData], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /* Recent Uninsured Claims */
    public function recentUninsuredClaims()
    {
        try {
            $orderBy = 'device_repairs.created_at';
            $claimDevicesData = UninsuredClaimsController::recentUninsuredClaims($orderBy);
            if ($claimDevicesData->isEmpty()) {
                return response()->json(['msg' => 'No Records Fond', "success" => true], 200);
            } else {
                return response()->json(['msg' => 'Recent Uninsured Claims', "success" => true, 'claimDevicesData' => $claimDevicesData], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /* Recent Devices */
    public function recentDevices()
    {
        try {
            $devicesData = AdminDeviceController::recentDevices();
            if ($devicesData->isEmpty()) {
                return response()->json(['msg' => 'No Records Fond', "success" => true], 200);
            } else {
                return response()->json(['msg' => 'Recent Devices', "success" => true, 'devicesData' => $devicesData], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /* For Total Insured and Uninsured Claims according to the month's */
    public function chartInsuredClaims()
    {
        try {
            $data = [];
            /* Get the current date */
            $currentDate = Carbon::now();
            /* List of months (1 = January, 12 = December) */
            $months = $this->months();

            /* Loop through the last 12 months including the current month */
            for ($i = 11; $i >= 0; $i--) {
                /* Get the month for the current iteration (current month - i) */
                $date = $currentDate->copy()->subMonths($i);

                /* Get the month number (1 = January, 12 = December) */
                $monthNumber = $date->month;

                /* Prepare where condition for the current month (relative to the last 12 months) */
                $whereConditions = [
                    'whereMonth' => [
                        'created_at' => $monthNumber /* Apply month (1-12) for created_at */
                    ],
                    'whereYear' => [
                        'created_at' => $date->year /* Apply the year relative to the current date */
                    ]
                ];

                /* Get Total Insured Claims for the month */
                $totalInsuredClaims = InsuredClaimsController::totalInsuredClaims($whereConditions);
                /* Get Total Uninsured Claims for the month */
                $totalUninsuredClaims = UninsuredClaimsController::totalUninsuredClaims($whereConditions);

                /* If no claims are found for this month, set the count to 0 */
                if ($totalInsuredClaims == null || $totalInsuredClaims == 0) {
                    $totalInsuredClaims = 0;
                }
                if ($totalUninsuredClaims == null || $totalUninsuredClaims == 0) {
                    $totalUninsuredClaims = 0;
                }

                /* Store data for the current month */
                $data[] = [
                    'month' => $months[$monthNumber], /* Get the month name from the months array */
                    'totalInsuredClaims' => $totalInsuredClaims,
                    'totalUninsuredClaims' => $totalUninsuredClaims,
                ];
            }
            /* Return the response with the total claims data for the last 12 months */
            return response()->json([
                'msg' => 'Total Insured and Uninsured Claims for the Last 12 Months',
                'success' => true,
                'claimsData' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'msg' => 'Something went wrong, Please try again later.',
                'success' => false
            ], 200);
        }
    }

    /* For Total Users according to the month's*/
    public function chartUsers() {
        try {
                /* Getting service provider id from session */
                $serviceProvider = Session::get('service_provider');
                $data = [];
                /* Get the current date */
                $currentDate = Carbon::now();
                /* List of months (1 = January, 12 = December) */
                $months = $this->months();

                /* Loop through the last 12 months including the current month */
                for ($i = 11; $i >= 0; $i--) {
                /* Get the month for the current iteration (current month - i) */
                $date = $currentDate->copy()->subMonths($i);

                /* Get the month number (1 = January, 12 = December) */
                $monthNumber = $date->month;
                /* Get Total users for the month (relative to the last 12 months)*/
                $totalUsers = User::whereMonth('created_at', $monthNumber)->whereYear('created_at', $date->year)->where('active', 1)->where('service_provider_id', $serviceProvider)->count();
                /* If no claims are found for this month, set the count to 0 */
                if ($totalUsers == null || $totalUsers == 0) {
                    $totalUsers = 0;
                }

                /* Store data for the current month */
                $data[] = [
                    'month' => $months[$monthNumber], /* Get the month name from the months array */
                    'users' => $totalUsers,
                ];
                }
                /* Return the response with the total users data for the last 12 months */
                return response()->json(['msg' => 'Total Users for the Last 12 Months','success' => true,'usersData' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['msg' => 'Something went wrong, Please try again later.','success' => false], 200);
        }
    }

    /* Month's */
    private function months()
    {
        return [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec'
        ];
    }

    /** Check Device Coverage */
    public function verifyCoverage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'serialNumber' => 'nullable|string',
                'name' => 'nullable|string',
            ]);
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $where = [];
            $fields = [
                'devices.id',
                'devices.device_title',
                'devices.serial_number',
                'devices.payment_added_date',
                'devices.expiration_date',
                'deviceModel.title as device_model_name',
                'devices.org_id',
                'devices.sub_org_id',
            ];
            if (!empty($request->name) || !empty($request->serialNumber)) {
                if (!empty($request->serialNumber)) {
                    $where = [
                        'where' => [
                            'devices.serial_number' => ['LIKE', "%{$request->serialNumber}%"],
                        ]
                    ];
                }
                if (!empty($request->name)) {
                    $orWhere = [
                        'orWhere' => [
                            'devices.device_title' => ['LIKE', "%{$request->name}%"],
                            'deviceModel.title' => ['LIKE', "%{$request->name}%"],
                        ]
                    ];
                    $where = array_merge($where, $orWhere);
                }
            }
            $device = AdminDeviceController::devicesQuery($where, $fields)->orderBy('deviceModel.title')->get();
            return response()->json(["deviceData" => $device, "msg" => "Device Data.", "success" => true], 200);
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Recent Support Tickets */
    public function recentSupportTickets(){
        try {
            $ticketData = SupportTicketController::recentSupportTickets();
            if ($ticketData->isEmpty()) {
                return response()->json(['msg' => 'No Records Fond', "success" => true], 200);
            } else {
                return response()->json(['msg' => 'Recent Support Tickets', "success" => true, 'ticketData' => $ticketData], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

}
