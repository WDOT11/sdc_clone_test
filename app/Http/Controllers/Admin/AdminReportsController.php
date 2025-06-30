<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Device;
use App\Models\User;
use App\Models\User\DeviceClaim;
use App\Models\User\DeviceRepair;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AdminReportsController extends Controller
{
    /** Display reports listing */
    public function index()
    {
        return view('admin.adminreports.index');
    }


    /** Device report (Month Chart) */
    public function deviceMonthReport(Request $request){
        try {

            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'year' => [
                    'required',
                    'date_format:Y',
                    'digits:4',
                    // function ($attribute, $value, $fail) {
                    //     $year = (int) $value;
                    //     $currentYear = now()->year;

                    //     if ($year < 2000 || $year > $currentYear) {
                    //         $fail("The $attribute must be between 2000 and $currentYear.");
                    //     }
                    // },
                ],
            ], [
                'year.required' => 'Year is required',
                'year.date_format' => 'Year must be in YYYY format',
                'year.digits' => 'Year must be 4 digits',
            ]);

            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $year = $request->year;
            $data = [];
            /* List of months (1 = January, 12 = December) */
            $months = $this->months();

            /* Loop through the 12 months*/
            for ($i = 1; $i <= count($months); $i++) {
                /* Get the month number (1 = January, 12 = December) */
                $monthNumber = $i;
                /* Get Total devices for the month (relative to the last 12 months)*/
                $totalDevices = Device::whereMonth('created_at', $monthNumber)->whereYear('created_at', $year)->where('active', 1)->where('service_provider_id', $serviceProvider)->count();
                /* If no devices are found for this month, set the count to 0 */
                if ($totalDevices == null || $totalDevices == 0) {
                    $totalDevices = 0;
                }
                /* Store data for the current month */
                $data[] = [
                    'month' => $months[$monthNumber], /* Get the month name from the months array */
                    'devices' => $totalDevices,
                ];
            }
            /* Return the response with the total devices data of 12 Months */
            return response()->json(['msg' => 'Total Devices of 12 Months','success' => true,'deviceData' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['msg' => 'Something went wrong, Please try again later.','success' => false], 200);
        }
    }
    /** Total Devices report (Pie Chart) */
    public function totalDevicesReport(Request $request){
        try {

            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'year' => [
                    'required',
                    'date_format:Y',
                    'digits:4',
                    // function ($attribute, $value, $fail) {
                    //     $year = (int) $value;
                    //     $currentYear = now()->year;

                    //     if ($year < 2000 || $year > $currentYear) {
                    //         $fail("The $attribute must be between 2000 and $currentYear.");
                    //     }
                    // },
                ],
            ], [
                'year.required' => 'Year is required',
                'year.date_format' => 'Year must be in YYYY format',
                'year.digits' => 'Year must be 4 digits',
            ]);

            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $year = $request->year;
            $data = [
                'totalDevices' => 0,
                'totalCoveredDevices' => 0,
                'totalUncoveredDevices' => 0,
            ];
            $deviceQuery = Device::whereYear('created_at', $year)->where('active', 1)->where('service_provider_id', $serviceProvider);
            $totalDevices = (clone $deviceQuery)->count();
            /** Total Uncovered Devices */
            $totalUncoveredDevices = (clone $deviceQuery)->where('expiration_date', '<', date('Y-m-d'))->count();
            /** Total Covered Devices */
            $totalCoveredDevices = (clone $deviceQuery)->where('expiration_date', '>=', date('Y-m-d'))->count();
            /* If no devices are found, set the count to 0 */
            if ($totalDevices == null || $totalDevices == 0) {
                $totalDevices = 0;
            }
            if ($totalUncoveredDevices == null || $totalUncoveredDevices == 0) {
                $totalUncoveredDevices = 0;
            }
            if ($totalCoveredDevices == null || $totalCoveredDevices == 0) {
                $totalCoveredDevices = 0;
            }
            /* Store data for the current month */
            $data['totalDevices'] = $totalDevices;
            $data['totalCoveredDevices'] = $totalCoveredDevices;
            $data['totalUncoveredDevices'] = $totalUncoveredDevices;

            /* Return the response with the total devices data   */
            return response()->json(['msg' => 'Total Devices','success' => true,'deviceData' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['msg' => 'Something went wrong, Please try again later.','success' => false], 200);
        }
    }
    /** Device Repair Request report (Month Chart) */
    public function deviceRepairRequestMonthReport(Request $request){
        try {

            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'year' => [
                    'required',
                    'date_format:Y',
                    'digits:4',
                    // function ($attribute, $value, $fail) {
                    //     $year = (int) $value;
                    //     $currentYear = now()->year;

                    //     if ($year < 2000 || $year > $currentYear) {
                    //         $fail("The $attribute must be between 2000 and $currentYear.");
                    //     }
                    // },
                ],
            ], [
                'year.required' => 'Year is required',
                'year.date_format' => 'Year must be in YYYY format',
                'year.digits' => 'Year must be 4 digits',
            ]);

            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $year = $request->year;
            $data = [];
            /* List of months (1 = January, 12 = December) */
            $months = $this->months();

            /* Loop through the 12 months*/
            for ($i = 1; $i <= count($months); $i++) {
                /* Get the month number (1 = January, 12 = December) */
                $monthNumber = $i;
                /* Get Total devices for the month (relative to the last 12 months)*/
                $totalDevices = DeviceRepair::whereMonth('created_at', $monthNumber)->whereYear('created_at', $year)->where('active', 1)->where('service_provider_id', $serviceProvider)->count();
                /* If no devices are found for this month, set the count to 0 */
                if ($totalDevices == null || $totalDevices == 0) {
                    $totalDevices = 0;
                }
                /* Store data for the current month */
                $data[] = [
                    'month' => $months[$monthNumber], /* Get the month name from the months array */
                    'devices' => $totalDevices,
                ];
            }
            /* Return the response with the total devices data of 12 Months */
            return response()->json(['msg' => 'Total Devices of 12 Months','success' => true,'deviceData' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['msg' => 'Something went wrong, Please try again later.','success' => false], 200);
        }
    }
    /** Total Repair Requests report (Pie Chart) */
    public function totalRepairRequestsReport(Request $request){
        try {

            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'year' => [
                    'required',
                    'date_format:Y',
                    'digits:4',
                    // function ($attribute, $value, $fail) {
                    //     $year = (int) $value;
                    //     $currentYear = now()->year;

                    //     if ($year < 2000 || $year > $currentYear) {
                    //         $fail("The $attribute must be between 2000 and $currentYear.");
                    //     }
                    // },
                ],
            ], [
                'year.required' => 'Year is required',
                'year.date_format' => 'Year must be in YYYY format',
                'year.digits' => 'Year must be 4 digits',
            ]);

            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $year = $request->year;
            $totaldeviceRepairRequests = 0;
            $totaldeviceRepairRequests = DeviceRepair::whereYear('created_at', $year)->where('active', 1)->where('service_provider_id', $serviceProvider)->count();

            /* If no device repair requests are found, set the count to 0 */
            if ($totaldeviceRepairRequests == null || $totaldeviceRepairRequests == 0) {
                $totaldeviceRepairRequests = 0;
            }

            /* Return the response with the total device repair requests data   */
            return response()->json(['msg' => 'Total Device Repair Requests','success' => true,'totaldeviceRepairRequests' => $totaldeviceRepairRequests], 200);
        } catch (\Exception $e) {
            return response()->json(['msg' => 'Something went wrong, Please try again later.','success' => false], 200);
        }
    }
    /** Device Claim Request report (Month Chart) */
    public function deviceClaimRequestMonthReport(Request $request){
        try {

            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'year' => [
                    'required',
                    'date_format:Y',
                    'digits:4',
                    // function ($attribute, $value, $fail) {
                    //     $year = (int) $value;
                    //     $currentYear = now()->year;

                    //     if ($year < 2000 || $year > $currentYear) {
                    //         $fail("The $attribute must be between 2000 and $currentYear.");
                    //     }
                    // },
                ],
            ], [
                'year.required' => 'Year is required',
                'year.date_format' => 'Year must be in YYYY format',
                'year.digits' => 'Year must be 4 digits',
            ]);

            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $year = $request->year;
            $data = [];
            /* List of months (1 = January, 12 = December) */
            $months = $this->months();

            /* Loop through the 12 months*/
            for ($i = 1; $i <= count($months); $i++) {
                /* Get the month number (1 = January, 12 = December) */
                $monthNumber = $i;
                /* Get Total devices for the month (relative to the last 12 months)*/
                $totalDevices = DeviceClaim::whereMonth('created_at', $monthNumber)->whereYear('created_at', $year)->where('active', 1)->where('service_provider_id', $serviceProvider)->count();
                /* If no devices are found for this month, set the count to 0 */
                if ($totalDevices == null || $totalDevices == 0) {
                    $totalDevices = 0;
                }
                /* Store data for the current month */
                $data[] = [
                    'month' => $months[$monthNumber], /* Get the month name from the months array */
                    'devices' => $totalDevices,
                ];
            }
            /* Return the response with the total devices data of 12 Months */
            return response()->json(['msg' => 'Total Device Claims of 12 Months','success' => true,'deviceData' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['msg' => 'Something went wrong, Please try again later.','success' => false], 200);
        }
    }
    /** Total Claim Requests report (Pie Chart) */
    public function totalClaimRequestsReport(Request $request){
        try {

            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'year' => [
                    'required',
                    'date_format:Y',
                    'digits:4',
                    // function ($attribute, $value, $fail) {
                    //     $year = (int) $value;
                    //     $currentYear = now()->year;

                    //     if ($year < 2000 || $year > $currentYear) {
                    //         $fail("The $attribute must be between 2000 and $currentYear.");
                    //     }
                    // },
                ],
            ], [
                'year.required' => 'Year is required',
                'year.date_format' => 'Year must be in YYYY format',
                'year.digits' => 'Year must be 4 digits',
            ]);

            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $year = $request->year;
            $totaldeviceClaimRequests = 0;
            $totaldeviceClaimRequests = DeviceClaim::whereYear('created_at', $year)->where('active', 1)->where('service_provider_id', $serviceProvider)->count();

            /* If no device claim requests are found, set the count to 0 */
            if ($totaldeviceClaimRequests == null || $totaldeviceClaimRequests == 0) {
                $totaldeviceClaimRequests = 0;
            }

            /* Return the response with the total device claim requests data   */
            return response()->json(['msg' => 'Total Device Claim Requests','success' => true,'totaldeviceClaimRequests' => $totaldeviceClaimRequests], 200);
        } catch (\Exception $e) {
            return response()->json(['msg' => 'Something went wrong, Please try again later.','success' => false], 200);
        }
    }
    /** Users report (Month Chart) */
    public function userMonthReport(Request $request){
        try {

            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'year' => [
                    'required',
                    'date_format:Y',
                    'digits:4',
                    // function ($attribute, $value, $fail) {
                    //     $year = (int) $value;
                    //     $currentYear = now()->year;

                    //     if ($year < 2000 || $year > $currentYear) {
                    //         $fail("The $attribute must be between 2000 and $currentYear.");
                    //     }
                    // },
                ],
            ], [
                'year.required' => 'Year is required',
                'year.date_format' => 'Year must be in YYYY format',
                'year.digits' => 'Year must be 4 digits',
            ]);

            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $year = $request->year;
            $data = [];
            /* List of months (1 = January, 12 = December) */
            $months = $this->months();

            /* Loop through the 12 months*/
            for ($i = 1; $i <= count($months); $i++) {
                /* Get the month number (1 = January, 12 = December) */
                $monthNumber = $i;
                /* Get Total users for the month (relative to the last 12 months)*/
                $totalUsers = User::whereMonth('created_at', $monthNumber)->whereYear('created_at', $year)->where('active', 1)->where('service_provider_id', $serviceProvider)->count();
                /* If no users are found for this month, set the count to 0 */
                if ($totalUsers == null || $totalUsers == 0) {
                    $totalUsers = 0;
                }
                /* Store data for the current month */
                $data[] = [
                    'month' => $months[$monthNumber], /* Get the month name from the months array */
                    'users' => $totalUsers,
                ];
            }
            /* Return the response with the total users data of 12 Months */
            return response()->json(['msg' => 'Total Users of 12 Months','success' => true,'userData' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['msg' => 'Something went wrong, Please try again later.','success' => false], 200);
        }
    }
    /** Total Users report (Pie Chart) */
    public function totalUsersReport(Request $request){
        try {

            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'year' => [
                    'required',
                    'date_format:Y',
                    'digits:4',
                    // function ($attribute, $value, $fail) {
                    //     $year = (int) $value;
                    //     $currentYear = now()->year;

                    //     if ($year < 2000 || $year > $currentYear) {
                    //         $fail("The $attribute must be between 2000 and $currentYear.");
                    //     }
                    // },
                ],
            ], [
                'year.required' => 'Year is required',
                'year.date_format' => 'Year must be in YYYY format',
                'year.digits' => 'Year must be 4 digits',
            ]);

            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $year = $request->year;
            $totalUsers = 0;
            $totalUsers = User::whereYear('created_at', $year)->where('active', 1)->where('service_provider_id', $serviceProvider)->count();

            /* If no users are found, set the count to 0 */
            if ($totalUsers == null || $totalUsers == 0) {
                $totalUsers = 0;
            }

            /* Return the response with the total users data   */
            return response()->json(['msg' => 'Total Users','success' => true,'totalUsers' => $totalUsers], 200);
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
}
