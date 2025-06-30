<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Admin\AdminDeviceController;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\User\InsuredClaimsController;
use App\Http\Controllers\User\UninsuredClaimsController;
use App\Http\Controllers\User\UserDeviceController;
use App\Models\OrgRelationship;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{

    /**
     * Return Home View
     */
    public function index()
    {
        /* Total Devices */
        $totalDevices = UserDeviceController::totalUserDevices();
        /* Total Covered Devices */
        $totalCoveredDevices =  UserDeviceController::totalUserCoveredDevices();
        /* Total Uncovered Devices */
        $totalUncoveredDevices = UserDeviceController::totalUserUncoveredDevices();
        /* Total Insured Claims File*/
        $totalInsuredClaims = $this->totalFiledInsuredClaims();
        /* Total Uninsured Claims File */
        $totalUninsuredClaims = $this->totalFiledUninsuredClaims();
        /* Call Dashboard File */
        return view('user.home.index', compact('totalDevices', 'totalCoveredDevices', 'totalUncoveredDevices', 'totalInsuredClaims', 'totalUninsuredClaims'));
    }

    /* Total Insured Claims File count*/
    private function totalFiledInsuredClaims($where = [], $secondWhere = [])
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        /* Auth User */
        $authUser = Session::get('auth_user');
        $totalFiledInsuredClaims = 0;

        $org_relationship = UserDeviceController::getOrgSubOrg($authUser->id);
        // $org_relationship = OrgRelationship::select('user_id', 'org_id', 'parent_org_id', 'is_org_subscriber')->where('user_id', $authUser['id'])->where('active', 1)->where('service_provider_id', $serviceProvider)->first();

        /* Find Org IT HOD, Org IT Director */
        if (!empty($org_relationship) && !empty($org_relationship->org_id) && empty($org_relationship->parent_org_id)) {
            /** IT HOD */
            $where = [
                'where' => [
                    'org_id' => $org_relationship->org_id
                ],
                'whereNot' => [
                    'device_claims.is_org_subsciber_claim' => 1
                ]
            ];
        } elseif (!empty($org_relationship) && !empty($org_relationship->org_id) && !empty($org_relationship->parent_org_id)) {
            /* IT Director */
            $where = [
                'where' => [
                    'org_id' => $org_relationship->parent_org_id,
                    'sub_org_id' => $org_relationship->org_id,
                ],
                'whereNot' => [
                    'device_claims.is_org_subsciber_claim' => 1
                ]
            ];
        } else {
            /** Subscriber  or Organization Subscriber*/
            $where = [
                'where' => [
                    'user_id' => $authUser->id
                ],
            ];
        }
        if (is_array($secondWhere) && !empty($secondWhere)) {
            $where = array_merge($where, $secondWhere);
        }
        $totalFiledInsuredClaims = InsuredClaimsController::totalInsuredClaims($where);

        return $totalFiledInsuredClaims;
    }

    /* Total Uninsured Claims File count*/
    private function totalFiledUninsuredClaims($where = [], $secondWhere = [])
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        /* Auth User */
        $authUser = Session::get('auth_user');
        $totalFiledUninsuredClaims = 0;

        /* Find Org IT HOD, Org IT Director */
        $org_relationship = UserDeviceController::getOrgSubOrg($authUser->id);
        // $org_relationship = OrgRelationship::select('user_id', 'org_id', 'parent_org_id', 'is_org_subscriber')->where('user_id', $authUser['id'])->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
        if (!empty($org_relationship) && !empty($org_relationship->org_id) && empty($org_relationship->parent_org_id)) {
            /** IT HOD */
            $where = [
                'where' => [
                    'org_id' => $org_relationship->org_id
                ]
            ];
        } elseif (!empty($org_relationship) && !empty($org_relationship->org_id) && !empty($org_relationship->parent_org_id)) {
            /* IT Director */
            $where = [
                'where' => [
                    'org_id' => $org_relationship->parent_org_id,
                    'sub_org_id' => $org_relationship->org_id
                ]
            ];
        } else {
            /** Subscriber or Organization Subscriber */
            $where = [
                'where' => [
                    'user_id' => $authUser->id
                ]
            ];
        }
        if (is_array($secondWhere) && !empty($secondWhere)) {
            $where = array_merge($where, $secondWhere);
        }
        $totalFiledUninsuredClaims = UninsuredClaimsController::totalUninsuredClaims($where);

        return $totalFiledUninsuredClaims;
    }

    /* For Total Insured and Uninsured Claims according to the month's */
    public function chartClaimsRepair()
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
                $totalInsuredClaims = $this->totalFiledInsuredClaims([], $whereConditions);
                /* Get Total Uninsured Claims for the month */
                $totalUninsuredClaims = $this->totalFiledUninsuredClaims([], $whereConditions);

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

    /* Recent Insured Claims */
    public function recentClaims()
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Auth User */
            $authUser = Session::get('auth_user');
            $orderBy = 'device_claims.created_at';

            /* Find Org IT HOD, Org IT Director */
            // $org_relationship = OrgRelationship::select('user_id', 'org_id', 'parent_org_id', 'is_org_subscriber')->where('user_id', $authUser['id'])->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
            $org_relationship = UserDeviceController::getOrgSubOrg($authUser->id);
            if (!empty($org_relationship) && !empty($org_relationship->org_id) && empty($org_relationship->parent_org_id)) {
                /** IT HOD */
                $where = [
                    'where' => [
                        'device_claims.org_id' => $org_relationship->org_id
                    ],
                    'whereNot' => [
                        'device_claims.is_org_subsciber_claim' => 1
                    ]
                ];
            } elseif (!empty($org_relationship) && !empty($org_relationship->org_id) && !empty($org_relationship->parent_org_id)) {
                /* IT Director */
                $where = [
                    'where' => [
                        'device_claims.org_id' => $org_relationship->parent_org_id,
                        'device_claims.sub_org_id' => $org_relationship->org_id
                    ],
                    'whereNot' => [
                        'device_claims.is_org_subsciber_claim' => 1
                    ]
                ];
            } else {
                /** Subscriber, Organization Subscriber */
                $where = [
                    'where' => [
                        'device_claims.user_id' => $authUser->id
                    ]
                ];
            }
            $claimDevicesData = InsuredClaimsController::recentFiledClaims($orderBy, [], $where);
            /* Check if the claimDevicesData is empty */
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
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Auth User */
            $authUser = Session::get('auth_user');
            $orderBy = 'device_claims.status_updated_date';
            $fields = [
                'device_claims.id',
                'device_claims.user_claim_status',
                'devices.device_title',
                'devices.serial_number',
                'device_models.title as device_model_name',
                // 'device_families.name as device_family_name',
                'users.full_name',
                $orderBy
            ];

            /* Find Org IT HOD, Org IT Director and Org Subscriber */
            // $org_relationship = OrgRelationship::select('user_id', 'org_id', 'parent_org_id', 'is_org_subscriber')->where('user_id', $authUser['id'])->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
            $org_relationship = UserDeviceController::getOrgSubOrg($authUser->id);
            if (!empty($org_relationship) && !empty($org_relationship->org_id) && empty($org_relationship->parent_org_id)) {
                /** IT HOD */
                $where = [
                    'where' => [
                        'device_claims.org_id' => $org_relationship->org_id
                    ],
                    'whereNot' => [
                        'device_claims.is_org_subsciber_claim' => 1
                    ]
                ];
            } elseif (!empty($org_relationship) && !empty($org_relationship->org_id) && !empty($org_relationship->parent_org_id)) {
                /* IT Director */
                $where = [
                    'where' => [
                        'device_claims.org_id' => $org_relationship->parent_org_id,
                        'device_claims.sub_org_id' => $org_relationship->org_id
                    ],
                    'whereNot' => [
                        'device_claims.is_org_subsciber_claim' => 1
                    ]
                ];
            } else {
                /** Subscriber or Organization Subscriber */
                $where = [
                    'where' => [
                        'device_claims.user_id' => $authUser->id
                    ]
                ];
            }
            $claimDevicesData = InsuredClaimsController::recentFiledClaims($orderBy, $fields, $where);
            /* Check if the claimDevicesData is empty */
            if ($claimDevicesData->isEmpty()) {
                return response()->json(['msg' => 'No Records Fond', "success" => true], 200);
            } else {
                return response()->json(['msg' => 'Recent Status Changed Insured Claims', "success" => true, 'claimDevicesData' => $claimDevicesData], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /* Recent Uninsured Claims */
    public function recentUninsuredClaims()
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Auth User */
            $authUser = Session::get('auth_user');
            $orderBy = 'device_repairs.created_at';


            /* Find Org IT HOD, Org IT Director and Org Subscriber */
            // $org_relationship = OrgRelationship::select('user_id', 'org_id', 'parent_org_id', 'is_org_subscriber')->where('user_id', $authUser['id'])->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
            $org_relationship = UserDeviceController::getOrgSubOrg($authUser->id);
            if (!empty($org_relationship) && !empty($org_relationship->org_id) && empty($org_relationship->parent_org_id) && $org_relationship->is_org_subscriber == 0) {
                /** IT HOD */
                $where = [
                    'where' => [
                        'device_repairs.org_id' => $org_relationship->org_id
                    ]
                ];
            } elseif (!empty($org_relationship) && !empty($org_relationship->org_id) && !empty($org_relationship->parent_org_id) && $org_relationship->is_org_subscriber == 0) {
                /* IT Director */
                $where = [
                    'where' => [
                        'device_repairs.org_id' => $org_relationship->parent_org_id,
                        'device_repairs.sub_org_id' => $org_relationship->org_id
                    ]
                ];
            } else {
                /** Subscriber */
                $where = [
                    'where' => [
                        'device_repairs.user_id' => $authUser->id
                    ]
                ];
            }
            $claimDevicesData = UninsuredClaimsController::recentUninsuredClaims($orderBy, [], $where);
            if ($claimDevicesData->isEmpty()) {
                return response()->json(['msg' => 'No Records Fond', "success" => true], 200);
            } else {
                return response()->json(['msg' => 'Recent Uninsured Claims', "success" => true, 'claimDevicesData' => $claimDevicesData], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /* Recent Support Tickets */
    public function recentSupportTickets()
    {
        try {
            $where = [];
            $user_id = Session::get('auth_user')->id;
            if (!empty($user_id)) {
                $where = [
                    'where' => [
                        'user_id' => $user_id
                    ]
                ];
            }
            $supportTicketsData = SupportTicketController::recentSupportTickets($where);
            if ($supportTicketsData->isEmpty()) {
                return response()->json(['msg' => 'No Records Fond', "success" => true], 200);
            } else {
                return response()->json(['msg' => 'Recent Support Tickets', "success" => true, 'ticketData' => $supportTicketsData], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /* Recent Devices */
    public function recentDevices()
    {
        try {
            $devicesData = UserDeviceController::recentDevices();
            if ($devicesData->isEmpty()) {
                return response()->json(['msg' => 'No Records Fond', "success" => true], 200);
            } else {
                return response()->json(['msg' => 'Recent Devices', "success" => true, 'devicesData' => $devicesData], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    public function userdashboard()
    {
        return view('user.user-dashboard');
    }

    /** To switch back */
    public function switchBackToAdmin()
    {
        try {
            /* Check if the user is authenticated */
            if (Auth::check()) {
                /* Get the auth user from session */
                $authUser = Session::get('admin_user_id');

                /* Getadmin user details */
                $user = User::where('id', $authUser)->where('active', 1)->first();
                if (empty($user)) {
                    return response()->json(["msg" => "User not found.", "success" => false], 200);
                }

                /** Remove the admin id if switched back */
                session()->forget('admin_user_id');
                Auth::login($user);
                return response()->json(["msg" => "User switched successfully.", "success" => true], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }
}
