<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Admin\AdminDeviceController;
use App\Http\Controllers\Admin\DeviceBrandController;

use App\Http\Controllers\Controller;
use App\Models\Admin\Device;

use App\Models\OrgRelationship;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Laravel\Cashier\Subscription;

class UserDeviceController extends Controller
{

    /**
     * Device List View
     */
    public function index(Request $request)
    {
        $where = $this->deviceListFilter($request);
        $deviceData = self::paginateDevices(20, $where);
        $pagination = [
            'total' => $deviceData->total(),
            'per_page' => $deviceData->perPage(),
            'current_page' => $deviceData->currentPage(),
            'last_page' => $deviceData->lastPage(),
            'from' => $deviceData->firstItem(),
            'to' => $deviceData->lastItem()
        ];
        $deviceBrands = DeviceBrandController::getDeviceBrands();
        /** Total Devices */
       /*  $totalDevices = self::getDeviceQuery()->count(); */
        /* Total Covered Devices */
       /*  $whereCovered['where']['devices.expiration_date'] = ['>=', date('Y-m-d')];
        $totalCoveredDevices = self::getDeviceQuery($whereCovered)->count(); */
        /* Total Uncovered Devices */
       /*  $whereUncovered['where']['devices.expiration_date'] = ['<', date('Y-m-d')];
        $totalUncoveredDevices = self::getDeviceQuery($whereUncovered)->count(); */

        /* Total Devices */
        $totalDevices = self::totalUserDevices();
        /* Total Covered Devices */
        $totalCoveredDevices =  self::totalUserCoveredDevices();
        /* Total Uncovered Devices */
        $totalUncoveredDevices = self::totalUserUncoveredDevices();
        if (!empty($request->page)) {
            return response()->json(["deviceData" => $deviceData, "pagination" => $pagination, "msg" => "Paginated records here.", "success" => true], 200);
        } else {
            return view('user.devices.device_list', compact('deviceData', 'pagination', 'deviceBrands', 'totalDevices', 'totalCoveredDevices', 'totalUncoveredDevices'));
        }
    }



    /* Device-List get Query */
    public static function getDeviceQuery($where = [], $fields = [])
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $query = DB::table('devices')
            ->leftJoin('device_models as deviceModel', 'devices.device_model_id', '=', 'deviceModel.id')
            ->leftJoin('device_brands as deviceBrand', 'deviceModel.device_brand_id', '=', 'deviceBrand.id')
             ->leftJoin('org_relationship as orgRelationship', 'devices.user_id', '=', 'orgRelationship.user_id')

            /* Join organizations dynamically depending on parent_org_id */
            ->leftJoin('organizations as org', function ($join) {
                $join->on('org.id', '=', DB::raw('IF(orgRelationship.parent_org_id IS NOT NULL, orgRelationship.parent_org_id, orgRelationship.org_id)'));
            })

            /* Always join sub_organizations using org_id */
            ->leftJoin('sub_organizations as subOrg', 'orgRelationship.org_id', '=', 'subOrg.id')
            ->where('devices.active', 1)
            ->where('devices.service_provider_id', $serviceProvider);

        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select(
                'devices.*',
                'deviceModel.title as device_model_name',
            );
        }
        /* Auth User */
        $authUser = Session::get('auth_user');

        /** hasRole
         * 1 = admin
         * 2 = user
         */
        $org_relationship = self::getOrgSubOrg($authUser->id);
        if (!empty($org_relationship) && !empty($org_relationship->org_id) && empty($org_relationship->parent_org_id)) {
            /** IT HOD */
            $where['where']['devices.org_id'] = $org_relationship->org_id;
            $where['whereNot']['devices.is_org_subscriber_device'] = 1;
        } elseif (!empty($org_relationship) && !empty($org_relationship->org_id) && !empty($org_relationship->parent_org_id)) {
            /* IT Director */
            $where['where']['devices.org_id'] = $org_relationship->parent_org_id;
            $where['where']['devices.sub_org_id'] = $org_relationship->org_id;
            $where['whereNot']['devices.is_org_subscriber_device'] = 1;
        } else {
            /** Subscriber or Organization Subscriber */
            $where['where']['devices.user_id'] = $authUser->id;
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
        return $query->orderBy('devices.created_at', 'desc');
    }

    /* Get All the Devices(paginate). */
    public static function paginateDevices($limit = 10, $where = [], $fields = [])
    {
        $fields = [
            'devices.id',
            'devices.device_title',
            'devices.serial_number',
            'devices.device_type',
            'devices.payment_added_date',
            'devices.expiration_date',
            'devices.created_at',
            'deviceModel.title as device_model_name',
            'devices.org_id',
            'devices.sub_org_id',
            'org.org_type',

        ];
        $data = self::getDeviceQuery($where, $fields)->paginate($limit);
        return $data;
    }



    /** Function to get the devices without pagination */
    public static function getAllDevices($where = [], $fields = [])
    {
        $data = self::getDeviceQuery($where, $fields)->get();
        return $data;
    }

    /** Function to get devices for dropdown */
    public static function getDevicesForDropDown($where = [], $fields = [])
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $query = DB::table('devices')
            ->leftJoin('device_models as deviceModel', 'devices.device_model_id', '=', 'deviceModel.id')
            ->leftJoin('device_plans as devicePlan', 'devices.plan_id', '=', 'devicePlan.id')
            ->where('devices.active', 1)
            ->where('devices.service_provider_id', $serviceProvider);

        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select(
                'devices.id',
                'devices.device_title',
            );
        }
        /* Auth User */
        $authUser = Session::get('auth_user');
        $org_relationship = self::getOrgSubOrg($authUser->id);
        if (!empty($org_relationship->org_id) && empty($org_relationship->parent_org_id)) {
            /** IT HOD */
            $where['where']['devices.org_id'] = $org_relationship->org_id;
            $where['whereNot']['devices.is_org_subscriber_device'] = 1;
        } elseif (!empty($org_relationship->org_id) && !empty($org_relationship->parent_org_id)) {
            /* IT Director */
            $where['where']['devices.org_id'] = $org_relationship->parent_org_id;
            $where['where']['devices.sub_org_id'] = $org_relationship->org_id;
            $where['whereNot']['devices.is_org_subscriber_device'] = 1;
        } else {
            /** Subscriber or Organization Subscriber */
            $where['where']['devices.user_id'] = $authUser->id;
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
        return $query->orderBy('devices.device_title', 'asc');
    }

    /* Get device according to device id*/
    public function getDeviceById(string $id)
    {
        if (!empty($id)) {
            $serviceProvider = Session::get('service_provider');
            $authUser = Session::get('auth_user');

            // $org_relationship = OrgRelationship::select('user_id', 'org_id', 'parent_org_id', 'is_org_subscriber')->where('user_id', $authUser->id)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();

            $query = DB::table('devices')
                ->leftJoin('device_models as deviceModel', 'devices.device_model_id', '=', 'deviceModel.id')
                ->leftJoin('device_brands as deviceBrand', 'deviceModel.device_brand_id', '=', 'deviceBrand.id')
                ->leftJoin('device_families as deviceFamily', 'deviceBrand.device_family_id', '=', 'deviceFamily.id')
                ->leftJoin('organizations as org', 'devices.org_id', '=', 'org.id')
                ->leftJoin('sub_organizations as subOrg', 'devices.sub_org_id', '=', 'subOrg.id')
                ->leftJoin('users as user', 'devices.user_id', '=', 'user.id')
                ->leftJoin('device_plans as plan', 'devices.plan_id', '=', 'plan.id')
                ->leftJoin('transactions as transaction', 'devices.transaction_id', '=', 'transaction.stripe_transaction_id')
                ->leftJoin('subscriptions as subscription', 'devices.subscription_id', '=', 'subscription.stripe_id')
                ->select(
                    'devices.*',
                    'org.name as org_name',
                    'subOrg.name as sub_org_name',
                    'org.org_type as org_type',
                    'deviceModel.title as device_model_name',
                    'user.first_name as user_first_name',
                    'user.last_name as user_last_name',
                    'user.full_name as user_full_name',
                    'user.email as user_email',
                    'user.created_at as user_created_at',
                    'plan.plan_name as plan_name',
                    'plan.freq_occurence as occurence',
                    'transaction.amount as transaction_amount',
                    'transaction.created_at as transaction_created_at',
                    'transaction.status as transaction_status',
                    /* 'transaction.stripe_transaction_id as transaction_stripe_transaction_id',
                     'subscription.stripe_status as subscription_status',
                     'subscription.created_at as subscription_start_date',
                     'subscription.ends_at as subscription_end_date',
                     'subscription.stripe_id as subscription_id',
                     'subscription.created_at as subscription_created_at',
                     'subscription.updated_at as subscription_updated_at', */
                )
                ->where('devices.active', 1)
                ->where('devices.service_provider_id', $serviceProvider);

            $isSubscriber = false;
            $org_relationship = self::getOrgSubOrg($authUser->id);
            if (!empty($org_relationship) && !empty($org_relationship->org_id) && empty($org_relationship->parent_org_id)) {
                // IT HOD
               /*  $query->leftJoin('organizations as org', 'devices.org_id', '=', 'org.id')
                    ->leftJoin('sub_organizations as subOrg', 'devices.sub_org_id', '=', 'subOrg.id')
                    ->addSelect('org.name as org_name', 'subOrg.name as sub_org_name'); */
                $query->where('devices.org_id', $org_relationship->org_id)->whereNot('devices.is_org_subscriber_device', 1);
            } elseif (!empty($org_relationship) && !empty($org_relationship->org_id) && !empty($org_relationship->parent_org_id)) {
                // IT Director
               /*  $query->leftJoin('organizations as org', 'devices.org_id', '=', 'org.id')
                    ->leftJoin('sub_organizations as subOrg', 'devices.sub_org_id', '=', 'subOrg.id')
                    ->addSelect(
                        'org.name as org_name',
                        'org.org_type as org_type',
                        'subOrg.name as sub_org_name'
                    ); */
                $query->where('devices.org_id', $org_relationship->parent_org_id)->where('devices.sub_org_id', $org_relationship->org_id)->whereNot('devices.is_org_subscriber_device', 1);
            }
           /*  elseif (!empty($org_relationship) && $org_relationship->is_org_subscriber == 1) {
                // Organization Subscriber
                $query->leftJoin('org_relationship as orgRelationship', 'devices.user_id', '=', 'orgRelationship.user_id')->leftJoin('organizations as org', 'orgRelationship.parent_org_id', '=', 'org.id')
                    ->leftJoin('sub_organizations as subOrg', 'orgRelationship.org_id', '=', 'subOrg.id')
                    ->addSelect(
                        'org.name as org_name',
                        'org.org_type as org_type',
                        'subOrg.name as sub_org_name'
                    );
                $query->where('devices.user_id', $authUser->id);
                $isSubscriber = true;
            } */
            else {
                // Subscriber or Organization Subscriber
                $query->where('devices.user_id', $authUser->id);
                $isSubscriber = true;
            }
            $device = $query->where('devices.id', $id)->first();

            if (!empty($device)) {
                if ($device->subscription_id && $isSubscriber == true) {
                    $subscription = Subscription::where('stripe_id', $device->subscription_id)->first();
                    $device->subscription_details = AdminDeviceController::formatSubscriptionDetails($subscription, $device->service_provider_id);
                }
                return response()->json(["viewData" => $device, "msg" => "Device Data.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Device not found.", "success" => false], 200);
            }
        } else {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Check Device Coverage */
    public function checkDeviceCoverage(Request $request)
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
            $device = self::getDeviceQuery($where, $fields)->orderBy('deviceModel.title')->get();
            return response()->json(["deviceData" => $device, "msg" => "Device Data.", "success" => true], 200);
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Device List Filter */
    private function deviceListFilter(Request $request)
    {
        $where = [];
        if (!empty($request->search) || !empty($request->brand) || !empty($request->coverage) || !empty($request->startDate) || !empty($request->endDate)) {
            if (!empty($request->search)) {
                $where = [
                    'orWhere' => [
                        'devices.device_title' => ['LIKE', "%{$request->search}%"],
                        'deviceModel.title' => ['LIKE', "%{$request->search}%"],
                        'devices.serial_number' => ['LIKE', "%{$request->search}%"],
                    ]
                ];
            }
            if (!empty($request->brand)) {
                if ($request->brand == 'all') {
                    // All Brands
                } else {
                    $where['where']['deviceBrand.id'] = $request->brand;
                }
            }
            if (!empty($request->coverage)) {
                if ($request->coverage == 'uncovered') {
                    $where['where']['devices.expiration_date'] = ['<', date('Y-m-d')];
                } elseif ($request->coverage == 'covered') {
                    $where['where']['devices.expiration_date'] = ['>=', date('Y-m-d')];
                } elseif ($request->coverage == 'all') {
                    // All Devices
                }
            }
            if (!empty($request->startDate) ||  !empty($request->endDate)) {
                $start = Carbon::parse($request->startDate)->startOfDay(); /* 00:00:00 */
                $end = Carbon::parse($request->endDate)->endOfDay();       /* 23:59:59 */
                $where['whereBetween']['devices.created_at'] = [$start, $end];
            }
        }
        return $where;
    }

    /** Total Devices according to role (User Dashboard) */
    public static function totalUserDevices()
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        /* Auth User */
        $authUser = Session::get('auth_user');
        $totaldevices = 0;
        $deviceQuery =  Device::query()->where('active', 1)->where('service_provider_id', $serviceProvider);
        /** hasRole
         * 1 = admin
         * 2 = user
         */

        /* Find Org IT HOD, Org IT Director*/
        $org_relationship = self::getOrgSubOrg($authUser->id);
        // $org_relationship = OrgRelationship::select('user_id', 'org_id', 'parent_org_id', 'is_org_subscriber')->where('user_id', $authUser['id'])->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
        if (!empty($org_relationship) && !empty($org_relationship->org_id) && empty($org_relationship->parent_org_id)) {
            /** IT HOD */
            $deviceQuery->where('org_id', $org_relationship->org_id)->whereNot('is_org_subscriber_device', 1);
        } elseif (!empty($org_relationship) && !empty($org_relationship->org_id) && !empty($org_relationship->parent_org_id)) {
            /* IT Director */
            $deviceQuery->where('org_id', $org_relationship->parent_org_id)->where('sub_org_id', $org_relationship->org_id)->whereNot('is_org_subscriber_device', 1);
        } else {
            /** Subscriber Or Organization Subscriber */
            $deviceQuery->where('user_id', $authUser->id);
        }
        $totaldevices = $deviceQuery->count();

        return $totaldevices;
    }

    /** Total Covered Devices according to role (User Dashboard) */
    public static function totalUserCoveredDevices()
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        /* Auth User */
        $authUser = Session::get('auth_user');
        $totaldevices = 0;
        $deviceQuery =  Device::query()->where('expiration_date', '>=', date('Y-m-d'))->where('active', 1)->where('service_provider_id', $serviceProvider);

        /* Find Org IT HOD, Org IT Director and Org Subscriber */
        $org_relationship = self::getOrgSubOrg($authUser->id);
        if (!empty($org_relationship) && !empty($org_relationship->org_id) && empty($org_relationship->parent_org_id)) {
            /** IT HOD */
            $deviceQuery->where('org_id', $org_relationship->org_id)->whereNot('is_org_subscriber_device', 1);
        } elseif (!empty($org_relationship) && !empty($org_relationship->org_id) && !empty($org_relationship->parent_org_id)) {
            /* IT Director */
            $deviceQuery->where('org_id', $org_relationship->parent_org_id)->where('sub_org_id', $org_relationship->org_id)->whereNot('is_org_subscriber_device', 1);
        } else {
            /** Subscriber Or Organization Subscriber */
            $deviceQuery->where('user_id', $authUser->id);
        }
        $totaldevices = $deviceQuery->count();

        return $totaldevices;
    }

    /** Total Uncovered Devices according to role (User Dashboard) */
    public static function totalUserUncoveredDevices()
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        /* Auth User */
        $authUser = Session::get('auth_user');
        $totaldevices = 0;
        $deviceQuery =  Device::query()->where('expiration_date', '<', date('Y-m-d'))->where('active', 1)->where('service_provider_id', $serviceProvider);

        /* Find Org IT HOD, Org IT Director and Org Subscriber */
        $org_relationship = self::getOrgSubOrg($authUser->id);
        if (!empty($org_relationship) && !empty($org_relationship->org_id) && empty($org_relationship->parent_org_id)) {
            /** IT HOD */
            $deviceQuery->where('org_id', $org_relationship->org_id)->whereNot('is_org_subscriber_device', 1);
        } elseif (!empty($org_relationship) && !empty($org_relationship->org_id) && !empty($org_relationship->parent_org_id)) {
            /* IT Director */
            $deviceQuery->where('org_id', $org_relationship->parent_org_id)->where('sub_org_id', $org_relationship->org_id)->whereNot('is_org_subscriber_device', 1);
        } else {
            /** Subscriber or Organization Subscriber */
            $deviceQuery->where('user_id', $authUser->id);
        }
        $totaldevices = $deviceQuery->count();

        return $totaldevices;
    }

    /* Recent 5 devices */
    // public static function recentDevices($where = [], $fields = [])
    // {
    //     /* Getting service provider id from session */
    //     $serviceProvider = Session::get('service_provider');
    //     /* Auth User */
    //     $authUser = Session::get('auth_user');
    //     $query = DB::table('devices')
    //         ->leftJoin('device_models', 'devices.device_model_id', '=', 'device_models.id')
    //         ->leftJoin('device_families', 'device_models.device_family_id', '=', 'device_families.id')
    //         ->where('devices.active', 1)
    //         ->where('devices.service_provider_id', $serviceProvider)
    //         ->where('device_models.service_provider_id', $serviceProvider)
    //         ->where('device_families.service_provider_id', $serviceProvider);

    //     /* Select Fields */
    //     if (!empty($fields)) {
    //         $query->select($fields);
    //     } else {
    //         $query->select(
    //             'devices.id',
    //             'devices.device_title',
    //             'device_families.name as device_family_name',
    //             'devices.created_at'
    //         );
    //     }
    //     $org_relationship = self::getOrgSubOrg($authUser->id);
    //     if (!empty($org_relationship->org_id) && empty($org_relationship->parent_org_id)) {
    //         /** IT HOD */
    //         $where['where']['devices.org_id'] = $org_relationship->org_id;
    //     } elseif (!empty($org_relationship->org_id) && !empty($org_relationship->parent_org_id)) {
    //         /* IT Director */
    //         $where['where']['devices.org_id'] = $org_relationship->parent_org_id;
    //         $where['where']['devices.sub_org_id'] = $org_relationship->org_id;
    //     } else {
    //         /** Subscriber or Organization Subscriber */
    //         $where['where']['devices.user_id'] = $authUser->id;
    //     }

    //     /* Where */
    //     if (!empty($where) && is_array($where)) {
    //         foreach ($where as $field => $value) {
    //             if (is_array($value)) {
    //                 foreach ($value as $secondField => $secondValue) {
    //                     $query->$field($secondField, $secondValue);
    //                 }
    //             }
    //         }
    //     }
    //     $query->orderBy('devices.created_at', 'desc')->limit(5);
    //     return $query->get();
    // }

    public static function recentDevices()
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        /* Auth User */
        $authUser = Session::get('auth_user');
        $query = DB::table('devices')
            ->leftJoin('device_models', 'devices.device_model_id', '=', 'device_models.id')
            ->leftJoin('device_families', 'device_models.device_family_id', '=', 'device_families.id')
            ->select('devices.id', 'devices.device_title', 'devices.serial_number', 'device_models.title as device_model_name', 'device_families.name as device_family_name', 'devices.created_at', 'devices.payment_added_date', 'devices.expiration_date')
            ->where('devices.active', 1)
            ->where('devices.service_provider_id', $serviceProvider);
        $org_relationship = self::getOrgSubOrg($authUser->id);
        if (!empty($org_relationship) && !empty($org_relationship->org_id) && empty($org_relationship->parent_org_id)) {
            /** IT HOD */
            $query->where('devices.org_id', $org_relationship->org_id)->whereNot('is_org_subscriber_device', 1);
        } elseif (!empty($org_relationship) && !empty($org_relationship->org_id) && !empty($org_relationship->parent_org_id)) {
            /* IT Director */
            $query->where('devices.org_id', $org_relationship->parent_org_id)->where('devices.sub_org_id', $org_relationship->org_id)->whereNot('is_org_subscriber_device', 1);
            // $query->where('devices.sub_org_id', $org_relationship->org_id);
        } else {
            /** Subscriber or Organization Subscriber */
            $query->where('devices.user_id', $authUser->id);
        }
        $query->orderBy('devices.created_at', 'desc')->limit(5);
        return $query->get();
    }

    /** Get device details by id to send on Zoho */
    public static function getDeviceDetailsById($deviceId, $fields)
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $device = Device::select($fields)->where('id', $deviceId)->where('service_provider_id', $serviceProvider)->first();
        return $device;
    }

    /** Organization Relationship (In case Hod , Director)*/
    public static function getOrgSubOrg($userId)
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $org_relationship = OrgRelationship::select('user_id', 'org_id', 'parent_org_id')->where('user_id', $userId)->where('is_org_subscriber', 0)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
        return $org_relationship;
    }
}
