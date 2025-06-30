<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\DeviceBrandController;
use App\Http\Controllers\Admin\DeviceModelController;
use App\Http\Controllers\Admin\DevicePlanController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Models\Admin\Device;
use App\Models\Admin\OrgAllowedModel;
use App\Models\Admin\Role;
use App\Models\Notification;
use App\Models\OrgRelationship;
use App\Models\User;
use App\Models\UserMeta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Subscription;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\Invoice;

class AdminDeviceController extends Controller
{
    /** Device list index function to get data */
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
        /* Total Devices */
        $totalDevices = AdminDeviceController::totalDevices();

        if (!empty($request->page)) {
            return response()->json(["deviceData" => $deviceData, "pagination" => $pagination, "msg" => "Paginated records here.", "success" => true], 200);
        } else {
            return view('admin.devicemaster.index', compact('deviceData', 'pagination', 'deviceBrands', 'totalDevices'));
        }
    }

    /** Show the form for creating new device */
    public function create()
    {
        $orgData = OrganizationController::getAllOrganizations();
        return view('admin.devicemaster.create', compact('orgData'));
    }

    /** Function to create devices. */
    public function store(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate request data */
            $validator = Validator::make($request->all(), [
                'deviceOrg' => 'required_if:deviceType,2|exists:organizations,id,active,1,service_provider_id,' . $serviceProvider,
                'deviceUser' => 'required|exists:users,id,active,1,service_provider_id,' . $serviceProvider,
                'deviceSerialNumber' => 'required|string|unique:devices,serial_number,NULL,id,active,1',
                'deviceImei' => 'nullable|string|unique:devices,imei,NULL,id,active,1',
                'deviceType' => 'required|in:1,2',
                'deviceOwnerFirstName' => 'required_if:deviceType,1',
                'deviceOwnerLastName' => 'nullable|string|max:50',
                'deviceOwnerName' => 'required_if:deviceType,2',
                'deviceOrgUserName' => 'required_if:deviceType,2',
                'deviceModel' => 'required|exists:device_models,id,active,1,service_provider_id,' . $serviceProvider,
                'deviceCarrier' => 'nullable',
                'deviceCapacity' => 'nullable',
                'deviceColor' => 'nullable',
                'deviceCellService' => 'nullable|in:1,2',
                'deviceOrgUserDesig' => 'nullable|string|max:100',
                'deviceOrgAssetTag' => 'nullable',
                'deviceOrgUserId' => 'nullable',
                'deviceCoverageExDate' => 'required|date_format:Y-m-d',
                'deviceCoveragePlan' => 'required|integer',
                'deviceBillingCycle' => 'required|integer',
                'deviceSubOrg' => 'nullable',
            ], [
                'deviceOrg.required_if' => 'Organization is required',
                'deviceUser.required' => 'User is required',
                'deviceUser.email' => 'User email must be a valid email address',
                'deviceSerialNumber.required' => 'Serial number is required',
                'deviceSerialNumber.unique' => 'Serial number already exists',
                'deviceImei.required' => 'IMEI is required',
                'deviceImei.unique' => 'IMEI already exists',
                'deviceType.required' => 'Device type is required',
                'deviceType.in' => 'Device type must be either 1 or 2',
                'deviceOwnerFirstName.required_if' => 'Owner first name is required for Personal device',
                'deviceOwnerLastName.required_if' => 'Owner last name is required for Personal device',
                'deviceOwnerName.required_if' => 'Owner name is required for Business device',
                'deviceOrgUserName.required_if' => 'Organization user name is required for Business device',
                'deviceModel.required' => 'Device model is required',
                'deviceModel.exists' => 'Invalid device model',
                'deviceCarrier.nullable' => 'Carrier is optional',
                'deviceCapacity.nullable' => 'Capacity is optional',
                'deviceColor.nullable' => 'Color is optional',
                'deviceCellService.required' => 'Cellular service is required',
                'deviceCellService.in' => 'Cellular service must be either 1 or 2',
                'deviceOrgUserName.required_if' => 'Organization user name is required for Organization device',
                'deviceOrgAssetTag.required_if' => 'Organization asset tag is required for Organization device',
                'deviceOrgUserId.required_if' => 'Organization user ID is required for Organization device',
                'deviceCoverageExDate.required' => 'Coverage expiration date is required',
                'deviceCoverageExDate.date_format' => 'Coverage expiration date must be in YYYY-MM-DD format',
                'deviceCoveragePlan.required' => 'Coverage plan is required',
                'deviceCoveragePlan.integer' => 'Coverage plan must be an integer',
                'deviceBillingCycle.required' => 'Billing cycle is required',
                'deviceBillingCycle.integer' => 'Billing cycle must be an integer',
                'deviceSubOrg.required_if' => 'Sub organization is required',
            ]);
            /* Check if validation fails */
            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $validator->errors()
                ], 200);
            }

            /** creating device title with model name and serial number */
            $deviceTitle = $request->deviceModelTitle . ' (#' . $request->deviceSerialNumber . ')';
            /** UserMeta Key (parnet case)  */
            $userMetaKey = UserMeta::where('user_id', $request->deviceUser)->where("meta_key", "org_subscriber")->where('meta_value', 'yes')->where('active', 1)->where('service_provider_id', $serviceProvider)->exists();
            /* Create a new Device record */
            $device = Device::create([
                'device_title' => $deviceTitle,
                'device_model_id' => $request->deviceModel,
                'serial_number' => $request->deviceSerialNumber,
                'imei' => $request->deviceImei,
                'carrier' => $request->deviceCarrier,
                'capacity' => $request->deviceCapacity,
                'color' => $request->deviceColor,
                'cellular_service' => $request->deviceCellService,
                'device_type' => $request->deviceType,
                'user_id' => $request->deviceUser,
                'org_id' => $request->deviceOrg,
                'sub_org_id' => $request->deviceSubOrg,
                'is_org_subscriber_device' => $userMetaKey ? 1 : 0,


                'device_owner_name' => $request->deviceOwnerName,
                'org_user_first_name' => $request->deviceOwnerFirstName,
                'org_user_last_name' => $request->deviceOwnerLastName,
                'org_user_full_name' => isset($request->deviceOwnerFirstName) || isset($request->deviceOwnerLastName) ? $request->deviceOwnerFirstName . ' ' . $request->deviceOwnerLastName : $request->deviceOrgUserName,


                'org_user_designation' => $request->deviceOrgUserDesig,
                'org_user_id' => $request->deviceOrgUserId,
                'plan_id' => $request->deviceCoveragePlan,
                'expiration_date' => $request->deviceCoverageExDate,
                'billing_cycle_id' => $request->deviceBillingCycle,
                'asset_tag' => $request->deviceOrgAssetTag,
                'cover_img_url' => null,
                'subscription_id' => $request->deviceSubscriptionId ?? null,
                'service_provider_id' => $serviceProvider,
                'payment_added_date' => Carbon::now(),
            ]);
            /* Check If Device Created Successfully */
            if ($device->wasRecentlyCreated) {
                $notificationRequest = [
                    'user_id' => $device->user_id,
                    // 'message' => $device->device_type == 2  ? $device->org_user_full_name . " has registered a new device: " . $device->device_title : "Your  device has been registered: " . $device->device_title,
                    'message' => "A new Device is registered: " . $device->device_title,
                    'notification_for' => 'device_added',
                    'device_id' => $device->id,
                    /*  'org_id' => $device->org_id,
                    'sub_org_id' => $device->sub_org_id, */
                    'is_admin' => 2,
                    'user_target_link' => 'sdcsmuser/device-list',
                    'admin_target_link' => 'smarttiusadmin/devices',
                    'service_provider_id' => $device->service_provider_id,
                ];
                NotificationController::addNotification($notificationRequest);
                return response()->json([
                    "msg" => "Device created successfully.",
                    "success" => true,
                ], 200);
            } else {
                return response()->json(["msg" => "Device creation failed.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Display the specified device. */
    public function show(string $id)
    {
        if (!empty($id)) {
            $where = [
                'where' => [
                    'devices.id' => $id,
                ]
            ];
            $device = self::fulldeviceDetailsQuery($where)->first();

            if (!empty($device)) {
                if ($device->subscription_id) {
                    $subscription = Subscription::where('stripe_id', $device->subscription_id)->first();
                    $device->subscription_details = self::formatSubscriptionDetails($subscription, $device->service_provider_id);
                }
                return response()->json(["deviceData" => $device, "msg" => "Device fetched successfully.", "success" => true], 200);
            }
        }
        return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
    }

    /** Format the subscription details */
    public static function formatSubscriptionDetails($subscription, $serviceProvider)
    {
        if (!$subscription) return null;

        // Get Stripe secret key (multi-tenant support via options table)
        $stripeSecretKey = trim(SDCOptionController::getOption('sdcsm_stripe_secret_key', $serviceProvider), '"');

        if (empty($stripeSecretKey)) {
            return response()->json(["msg" => "Stripe API key not found.", "success" => false], 200);
        }
        config(['cashier.secret' => $stripeSecretKey]);

        // Set API key for direct Stripe SDK use (required for raw API calls)
        /* \Stripe\Stripe::setApiKey($stripeSecretKey); */

        // Get full Stripe subscription object
        $stripeSubscription = $subscription->asStripeSubscription();

        // Get price details (for amount + currency)
        $priceData = $stripeSubscription->items->data[0]->price ?? null;
        $amount = $priceData ? $priceData->unit_amount / 100 : null;
        $currency = strtoupper($priceData->currency ?? 'usd');

        // Get latest invoice from Stripe
        /* $latestInvoice = $stripeSubscription->latest_invoice
            ? Invoice::retrieve($stripeSubscription->latest_invoice)
            : null; */

        // Determine next payment attempt date
        /* $nextPaymentAttempt = $stripeSubscription->next_payment_attempt
            ? Carbon::createFromTimestamp($stripeSubscription->next_payment_attempt)->format('Y-m-d H:i:s')
            : null; */

        // Failure reason (if payment failed)
        /* $failureReason = optional($latestInvoice?->payment_intent)?->last_payment_error?->message; */

        return [
            // Basic subscription info
            'id' => $subscription->stripe_id,
            'status' => $subscription->stripe_status,
            /*  'price_id' => $subscription->stripe_price,
            'quantity' => $subscription->quantity, */

            // Dates
            'created_at' => $subscription->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $subscription->updated_at->format('Y-m-d H:i:s'),
            /*  'current_period_start' => Carbon::createFromTimestamp($stripeSubscription->current_period_start)->format('Y-m-d H:i:s'),
            'current_period_end' => Carbon::createFromTimestamp($stripeSubscription->current_period_end)->format('Y-m-d H:i:s'),
            'cancel_at_period_end' => $stripeSubscription->cancel_at_period_end,
            'canceled_at' => $stripeSubscription->canceled_at
                ? Carbon::createFromTimestamp($stripeSubscription->canceled_at)->format('Y-m-d H:i:s')
                : null,*/
            'ended_at' => $stripeSubscription->ended_at
                ? Carbon::createFromTimestamp($stripeSubscription->ended_at)->format('Y-m-d H:i:s')
                : null,

            // Trial info
            /* 'trial_ends_at' => $subscription->trial_ends_at?->format('Y-m-d H:i:s'), */

            // Payment info
            'amount' => $amount,
            'currency' => $currency,
            'next_payment_date' => $stripeSubscription->current_period_end
                ? Carbon::createFromTimestamp($stripeSubscription->current_period_end)->format('Y-m-d H:i:s')
                : null,
            /*  'next_payment_attempt' => $nextPaymentAttempt, */

            // Invoice info
            /*  'latest_invoice_id' => $latestInvoice?->id,
            'latest_invoice_status' => $latestInvoice?->status,
            'latest_invoice_payment_intent_status' => optional($latestInvoice?->payment_intent)?->status, */

            // Failure reason if any
            /* 'payment_failure_reason' => $failureReason, */

            // Cashier status checks
            /*  'active' => $subscription->active(),
            'on_grace_period' => $subscription->onGracePeriod(),
            'on_trial' => $subscription->onTrial(),
            'past_due' => $subscription->pastDue(),
            'incomplete' => in_array($stripeSubscription->status, ['incomplete', 'incomplete_expired']), */
        ];
    }

    /** To open the update form and show the relavent data */
    public function edit(string $id)
    {
        if (!empty($id)) {
            // $device = Device::findOrFail($id);


            $where = [
                'where' => [
                    'devices.id' => $id,
                ]
            ];
            $device = self::fulldeviceDetailsQuery($where)->first();

            if (!empty($device)) {
                $orgData = OrganizationController::getAllOrganizations();
                return view('admin.devicemaster.update', compact('device', 'orgData'));
            } else {
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }
        } else {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Update the specified device in storage. */
    public function update(Request $request, $id)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate request data */
            $validator = Validator::make($request->all(), [
                'deviceOrg' => 'required_if:deviceType,2|nullable|exists:organizations,id,active,1,service_provider_id,' . $serviceProvider,
                'deviceUser' => 'required|exists:users,id,active,1,service_provider_id,' . $serviceProvider,
                'deviceSerialNumber' => 'required|string|unique:devices,serial_number,' . $id . ',id,active,1',
                'deviceImei' => 'nullable|string|unique:devices,imei,' . $id . ',id,active,1',
                'deviceType' => 'required|in:1,2',
                'deviceOwnerFirstName' => 'nullable',
                'deviceOwnerLastName' => 'nullable',
                'deviceOwnerName' => 'nullable',
                'deviceOrgUserName' => 'nullable',
                'deviceModel' => 'required|exists:device_models,id,active,1,service_provider_id,' . $serviceProvider,
                'deviceCarrier' => 'nullable',
                'deviceCapacity' => 'nullable',
                'deviceColor' => 'nullable',
                'deviceCellService' => 'nullable|in:1,2',
                'deviceOrgUserDesig' => 'nullable',
                'deviceOrgAssetTag' => 'nullable',
                'deviceOrgUserId' => [
                    'nullable',
                    /* function ($attribute, $value, $fail) use ($request) {
                        if (request()->has('deviceType') && $request->deviceType == 2 && !is_numeric($value)) {
                            $fail('The ' . $attribute . ' must be a number.');
                        }
                    } */
                ],
                'deviceCoverageExDate' => 'required|date_format:Y-m-d',
                'deviceCoveragePlan' => 'required|integer',
                /* 'deviceBillingCycle' => 'required|integer', */
                'deviceSubOrg' => 'nullable',
            ], [
                'deviceOrg.required_if' => 'Organization is required',
                'deviceUser.required' => 'User is required',
                'deviceUser.email' => 'User email must be a valid email address',
                'deviceSerialNumber.required' => 'Serial number is required',
                'deviceSerialNumber.unique' => 'Serial number already exists',
                'deviceImei.required' => 'IMEI is required',
                'deviceImei.unique' => 'IMEI already exists',
                'deviceType.required' => 'Device type is required',
                'deviceType.in' => 'Device type must be either 1 or 2',
                'deviceOwnerFirstName.required_if' => 'Owner first name is required for Personal device',
                'deviceOwnerLastName.required_if' => 'Owner last name is required for Personal device',
                'deviceOwnerName.required_if' => 'Owner name is required for Business device',
                'deviceOrgUserName.required_if' => 'Organization user name is required for Business device',
                'deviceModel.required' => 'Device model is required',
                'deviceModel.exists' => 'Invalid device model',
                'deviceCarrier.nullable' => 'Carrier is optional',
                'deviceCapacity.nullable' => 'Capacity is optional',
                'deviceColor.nullable' => 'Color is optional',
                'deviceCellService.required' => 'Cellular service is required',
                'deviceCellService.in' => 'Cellular service must be either 1 or 2',
                'deviceOrgUserName.required_if' => 'Organization user name is required for Organization device',
                'deviceOrgAssetTag.required_if' => 'Organization asset tag is required for Organization device',
                'deviceOrgUserId.required_if' => 'Organization user ID is required for Organization device',
                'deviceCoverageExDate.required' => 'Coverage expiration date is required',
                'deviceCoverageExDate.date_format' => 'Coverage expiration date must be in YYYY-MM-DD format',
                'deviceCoveragePlan.required' => 'Coverage plan is required',
                'deviceCoveragePlan.integer' => 'Coverage plan must be an integer',
                /*
                'deviceBillingCycle.required' => 'Billing cycle is required',
                'deviceBillingCycle.integer' => 'Billing cycle must be an integer',
                */
                'deviceSubOrg.required_if' => 'Sub organization is required',
            ]);

            /* Check if validation fails */
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }

            /* Getting device data to update */
            $device = Device::where('id', $id)->where('service_provider_id', $serviceProvider)->where('active', 1)->first();
            if (empty($device)) {
                return response()->json(["msg" => "Device not found.", "success" => false], 200);
            }

            /** creating device title with model name and serial number */
            $deviceTitle = $request->deviceModelTitle . ' (#' . $request->deviceSerialNumber . ')';
            /** UserMeta Key (parnet case)  */
            $userMetaKey = UserMeta::where('user_id', $request->deviceUser)->where("meta_key", "org_subscriber")->where('meta_value', 'yes')->where('active', 1)->where('service_provider_id', $serviceProvider)->exists();
            $deviceUpdated = $device->update([
                'device_title' => $deviceTitle,
                'device_model_id' => $request->deviceModel,
                'serial_number' => $request->deviceSerialNumber,
                'imei' => $request->deviceImei,
                'carrier' => $request->deviceCarrier,
                'capacity' => $request->deviceCapacity,
                'color' => $request->deviceColor,
                'cellular_service' => $request->deviceCellService,
                'device_type' => $request->deviceType,
                'user_id' => $request->deviceUser,
                'org_id' => $request->deviceOrg,
                'sub_org_id' => $request->deviceSubOrg,
                'is_org_subscriber_device' => $userMetaKey ? 1 : 0,
                'device_owner_name' => $request->deviceOwnerName,
                'org_user_first_name' => $request->deviceOwnerFirstName,
                'org_user_last_name' => $request->deviceOwnerLastName,
                'org_user_full_name' => isset($request->deviceOwnerFirstName) || isset($request->deviceOwnerLastName) ? $request->deviceOwnerFirstName . '' . $request->deviceOwnerLastName : $request->deviceOrgUserName,
                'org_user_designation' => $request->deviceOrgUserDesig,
                'org_user_id' => $request->deviceOrgUserId,
                'plan_id' => $request->deviceCoveragePlan != 0 ? $request->deviceCoveragePlan : $device->plan_id,
                'expiration_date' => $request->deviceCoverageExDate,
                'billing_cycle_id' => $request->deviceBillingCycle,
                'asset_tag' => $request->deviceOrgAssetTag,
                'cover_img_url' => null,
                // 'subscription_id' => $request->deviceSubscriptionId ?? null,
            ]);

            if (!empty($deviceUpdated)) {
                return response()->json([
                    "msg" => "Device updated successfully.",
                    "success" => true,
                ], 200);
            } else {
                return response()->json(["msg" => "Device updation failed.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** To delete the device data (This will be a soft delete). */
    public function destroy(string $id)
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');

        /* Find the device */
        $device = Device::where('id', $id)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
        if (empty($device)) {
            return response()->json(["msg" => "Device not found.", "success" => false], 404);
        }

        /** Soft delete the device */
        $deviceDeleted =  $device->update([
            'active' => 0,
        ]);
        if ($deviceDeleted) {
            return response()->json(["msg" => "Device deleted successfully.", "success" => true], 200);
        } else {
            return response()->json(["msg" => "Device deletion failed.", "success" => false], 500);
        }
    }

    /** Get the devices(query). */
    public static function devicesQuery($where = [], $fields = [])
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $query = DB::table('devices')
            ->leftJoin('device_models as deviceModel', 'devices.device_model_id', '=', 'deviceModel.id')
            ->leftJoin('device_brands as deviceBrand', 'deviceModel.device_brand_id', '=', 'deviceBrand.id')
           /*  ->leftJoin('org_relationship as orgRelationship', 'devices.user_id', '=', 'orgRelationship.user_id') */

            // Join organizations dynamically depending on parent_org_id
           /*  ->leftJoin('organizations as org', function ($join) {
                $join->on('org.id', '=', DB::raw('IF(orgRelationship.parent_org_id IS NOT NULL, orgRelationship.parent_org_id, orgRelationship.org_id)'));
            }) */

            // Always join sub_organizations using org_id
            // ->leftJoin('sub_organizations as subOrg', 'orgRelationship.org_id', '=', 'subOrg.id')
            ->leftJoin('organizations as org', 'devices.org_id', '=', 'org.id')
            ->leftJoin('sub_organizations as subOrg', 'devices.sub_org_id', '=', 'subOrg.id')
            ->leftJoin('users as user', 'devices.user_id', '=', 'user.id')
            ->where('devices.active', 1)
            ->where('devices.service_provider_id', $serviceProvider);

        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select(
                'devices.id',
                'devices.active',
                'devices.device_title',
                'devices.serial_number',
                'devices.device_type',
                'devices.payment_added_date',
                'devices.expiration_date',
                'devices.device_type',
                'deviceModel.title as device_model_name',
                'org.name as org_name',
                'org.org_type as org_type',
                'subOrg.name as sub_org_name',
                'user.first_name as user_first_name',
                'user.last_name as user_last_name',
                'user.full_name as user_full_name',
                'user.email as user_email'
            );
        }
        if (!empty($where) && is_array($where)) {
            foreach ($where as $field => $value) {
                if (is_array($value)) {
                    /*  foreach ($value as $secondField => $secondValue) {
                        if (is_array($secondValue)) {
                            $query->$field($secondField, $secondValue[0], $secondValue[1]);
                        }else {
                            $query->$field($secondField, $secondValue);
                        }
                    } */
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
        return $query->orderBy('devices.created_at', 'desc');
    }

    /** Get all the devices(paginate). */
    public static function paginateDevices($limit = 20, $where = [], $fields = [])
    {
        $data = self::devicesQuery($where);
        $limit = (is_numeric($limit) && $limit > 0) ? $limit : 20;
        return $data->paginate($limit);
    }

    private static function fulldeviceDetailsQuery($where = [], $fields = [])
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $query = DB::table('devices')
            ->leftJoin('device_models as deviceModel', 'devices.device_model_id', '=', 'deviceModel.id')
            ->leftJoin('device_brands as deviceBrand', 'deviceModel.device_brand_id', '=', 'deviceBrand.id')
            /* ->leftJoin('org_relationship as orgRelationship', 'devices.user_id', '=', 'orgRelationship.user_id') */

            // Join organizations dynamically depending on parent_org_id
          /*   ->leftJoin('organizations as org', function ($join) {
                $join->on('org.id', '=', DB::raw('IF(orgRelationship.parent_org_id IS NOT NULL, orgRelationship.parent_org_id, orgRelationship.org_id)'));
            }) */

            // Always join sub_organizations using org_id
            /* ->leftJoin('sub_organizations as subOrg', 'orgRelationship.org_id', '=', 'subOrg.id') */
            ->leftJoin('organizations as org', 'devices.org_id', '=', 'org.id')
            ->leftJoin('sub_organizations as subOrg', 'devices.sub_org_id', '=', 'subOrg.id')
            ->leftJoin('users as user', 'devices.user_id', '=', 'user.id')
            ->leftJoin('device_plans as plan', 'devices.plan_id', '=', 'plan.id')
            ->leftJoin('transactions as transaction', 'devices.transaction_id', '=', 'transaction.stripe_transaction_id')
            ->leftJoin('subscriptions as subscription', 'devices.subscription_id', '=', 'subscription.stripe_id')
            ->where('devices.active', 1)
            ->where('devices.service_provider_id', $serviceProvider);

        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select(
                'devices.*',
                'deviceModel.title as device_model_name',
                'org.name as org_name',
                'org.org_type as org_type',
                'subOrg.name as sub_org_name',
                'user.first_name as user_first_name',
                'user.last_name as user_last_name',
                'user.full_name as user_full_name',
                'user.email as user_email',
                'user.created_at as user_registration_Date',
                'plan.plan_name as plan_name',
                'plan.freq_occurence as occurence',
                'transaction.amount as transaction_amount',
                'transaction.created_at as transaction_created_at',
                'transaction.status as transaction_status',
                /* 'transaction.stripe_transaction_id as transaction_stripe_transaction_id',
                'subscription.stripe_status as subscription_status',
                'subscription.created_at as subscription_start_date',
                'subscription.stripe_id as subscription_id',
                'subscription.ends_at as subscription_end_date',
                'subscription.created_at as subscription_created_at',
                'subscription.updated_at as subscription_updated_at', */
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
        return $query->orderBy('devices.created_at', 'desc');
    }

    /** Get all the devices. */
    public static function getDevices($where = [], $fields = [])
    {
        /** $where['where']['devices.active'] = 1; */
        $data = self::fulldeviceDetailsQuery($where, $fields);
        return $data->get();
    }

    /** Get users list of organizations and all */
    public function getUsersList(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');

            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'orgId' => 'nullable|exists:organizations,id,active,1,service_provider_id,' . $serviceProvider,
                'name' => 'nullable|string',
                'deviceUserId' => 'nullable',
                'subOrgId' => 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $fields = ['id'];
            $where = [
                'where' => [
                    'role_type' => 2,
                ]
            ];
            $roles = RoleController::getAllUserRoles($where, $fields);
            if ($roles->isEmpty()) {
                return response()->json(['success' => false, 'msg' => "Role not found"], 404);
            }
            /* Get all role IDs */
            $roleIds = [];
            foreach ($roles as $value) {
                $roleIds[] = $value->id;  // Collecting all role IDs
            }

            $users = User::query()
                ->select('id', 'first_name', 'last_name', 'full_name', 'email')
                ->whereIn('role_id', $roleIds)
                ->where('service_provider_id', $serviceProvider)
                ->where('active', 1);

            if (!empty($request->deviceUserId)) {
                $users->where('id', $request->deviceUserId);
            }

            /* Only apply whereAny if name is provided */
            if (!empty($request->name)) {
                $users->whereAny(['first_name', 'last_name', 'full_name', 'email'], 'LIKE', "%{$request->name}%");
            }


            if (!empty($request->orgId) && !empty($request->subOrgId)) {

                /* Sub-organization users */
                $org_relationship2 = OrgRelationship::select('user_id')->where('org_id', $request->subOrgId)->where('parent_org_id', $request->orgId)->pluck('user_id')
                    ->toArray();
                $users->whereIn('id', $org_relationship2);
            } elseif (!empty($request->orgId)) {
                /* Organization */
                $org_relationship = OrgRelationship::select('user_id')->where('org_id', $request->orgId)->whereNull('parent_org_id')->pluck('user_id')
                    ->toArray();
                $users->whereIn('id', $org_relationship);
            }

            $users = $users->get();
            if ($users->isEmpty()) {
                return response()->json(['users' => $users, 'success' => false, 'msg' => "No users found"], 200);
            }
            return response()->json(['success' => true, 'users' => $users], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => "Failed to get users." . $e->getMessage()], 200);
        }
    }

    /** Organization available device model or all device models */
    public function getOrgDeviceModels(Request $request)
    {
        try {

            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate Requests */
            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string',
                'orgId' => 'nullable|exists:organizations,id,active,1,service_provider_id,' . $serviceProvider,
                'deviceModelId' => 'nullable',
            ]);

            /* If Validation's fails */
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $where = [];

            if (!empty($request->deviceModelId)) {
                $where = [
                    'where' => [
                        'device_models.id' => $request->deviceModelId
                    ]
                ];
            }

            if (!empty($request->name)) {
                $where = [
                    'where' => [
                        'device_models.title' => ['LIKE', "%{$request->name}%"]
                    ]
                ];
            }

            $modelData = DeviceModelController::getDeviceModelDropdown($where);
            if (!empty($request->orgId)) {
                $orgId = $request->orgId;
                if (!is_numeric($orgId)) {
                    return response()->json(['success' => false, 'msg' => "Invalid Organization ID"], 200);
                } else {

                    /* Fetch Sub Organizations based on Organization ID and Service Provider ID */
                    $orgDeviceModels = OrgAllowedModel::select('id', 'model_id')->where('org_id', $orgId)->where('service_provider_id', $serviceProvider)->where('active', 1)->get();
                    if (empty($orgDeviceModels)) {
                        return response()->json(['success' => false, 'msg' => "No Device Models are found in this Organization"], 200);
                    } else {

                        /* Fetch Device Models based on Model IDs */
                        $deviceModels = $modelData->filter(function ($model) use ($orgDeviceModels) {
                            return in_array($model->id, $orgDeviceModels->pluck('model_id')->toArray());
                        })->values(); /* Resetting array keys */

                        if (empty($deviceModels)) {
                            return response()->json(['success' => false, 'msg' => "No Device Models are found for selected Organization."], 200);
                        } else {
                            return response()->json(['success' => true, 'deviceModels' => $deviceModels], 200);
                        }
                    }
                }
            } else {
                return response()->json(['success' => true, 'deviceModels' => $modelData], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => "Failed to get device models"], 200);
        }
    }

    /** Total Devices without role (Admin Dashboard) */
    public static function totalDevices()
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $totalDevices =  Device::where('active', 1)->where('service_provider_id', $serviceProvider)->count();
        return $totalDevices;
    }

    /* Total Covered Devices without role (Admin Dashboard) */
    public static function totalCoveredDevices()
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $totalDevices =  Device::where('expiration_date', '>=', date('Y-m-d'))->where('active', 1)->where('service_provider_id', $serviceProvider)->count();
        return $totalDevices;
    }

    /* Total Uncovered Devices without role (Admin Dashboard) */
    public static function totalUncoveredDevices()
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $totalDevices =  Device::where('expiration_date', '<', date('Y-m-d'))->where('active', 1)->where('service_provider_id', $serviceProvider)->count();
        return $totalDevices;
    }

    /* Recent 5 devices */
    public static function recentDevices($where = [], $fields = [])
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $query = DB::table('devices')
            ->leftJoin('device_models', 'devices.device_model_id', '=', 'device_models.id')
            ->leftJoin('device_families', 'device_models.device_family_id', '=', 'device_families.id')
            ->where('devices.is_imported_device', 0)
            ->where('devices.active', 1)
            ->where('devices.service_provider_id', $serviceProvider)
            ->where('device_models.service_provider_id', $serviceProvider)
            ->where('device_families.service_provider_id', $serviceProvider);

        /* Select Fields */
        if (!empty($fields)) {
            $query->select($fields);
        } else {
            $query->select(
                'devices.id',
                'devices.device_title',
                'devices.serial_number',
                'device_models.title as device_model_name',
                'device_families.name as device_family_name',
                'devices.created_at',
                'devices.payment_added_date',
                'devices.expiration_date'
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
        $query->orderBy('devices.created_at', 'desc')->limit(5);
        return $query->get();
    }

    /** Device List Filter */
    private function deviceListFilter(Request $request)
    {
        $where = [];
        if (!empty($request->search) || !empty($request->brand) || !empty($request->orgType) || !empty($request->coverage) || !empty($request->startDate) || !empty($request->endDate) || !empty($request->userId)) {
            if (!empty($request->search)) {
                $where = [
                    'orWhere' => [
                        'devices.device_title' => ['LIKE', "%{$request->search}%"],
                        'deviceModel.title' => ['LIKE', "%{$request->search}%"],
                        'devices.serial_number' => ['LIKE', "%{$request->search}%"],
                        'user.email' => ['LIKE', "%{$request->search}%"],
                        'org.name' => ['LIKE', "%{$request->search}%"],
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
            if (!empty($request->orgType)) {
                if ($request->orgType == 1) {
                    $where['where']['devices.device_type'] = $request->orgType;
                } elseif ($request->orgType == 2) {
                    $where['where']['devices.device_type'] = $request->orgType;
                } elseif ($request->orgType == 'all') {
                    // All Devices
                }
            }
            if (!empty($request->startDate) ||  !empty($request->endDate)) {
                $start = Carbon::parse($request->startDate)->startOfDay(); /* 00:00:00 */
                $end = Carbon::parse($request->endDate)->endOfDay();       /* 23:59:59 */
                $where['whereBetween']['devices.created_at'] = [$start, $end];
            }
            if (!empty($request->userId)) {
                $where['where']['devices.user_id'] = $request->userId;
            }
        }
        return $where;
    }

    /** Export Devices as csv */
    public function exportDevice(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate request data */
            $validator = Validator::make($request->all(), [
                'search' => 'nullable|string',
                'brand' => 'nullable|string',
                'coverage' => 'nullable|string',
                'startDate' => 'nullable|date',
                'endDate' => 'nullable|date',
            ], [
                'search.string' => 'Search must be a string',
                'brand.string' => 'Brand must be a string',
                'coverage.string' => 'Coverage must be a string',
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

            $where = $this->deviceListFilter($request);
            $deviceData = self::getDevices($where);
            $reportData =  $deviceData;
            if ($reportData->isEmpty()) {
                return response()->json(['success' => false, 'msg' => "No devices found."], 200);
            }
            // Set the CSV file name
            $fileName = 'device_report.csv';
            // Set the CSV column headers
            $headers = ['Name', 'Device Type', 'Device Model', 'Serial Number', 'IMEI', 'Cellular Service', 'Carrier', 'Capacity', 'Color', 'User Name', 'Organization Name', 'Sub Organization Name', 'Owner\'s Full Name', 'Org User Full Name', 'Org User Designation', 'Org User ID', 'Asset Tag', 'Plan Name', 'Coverage Start Date', 'Coverage Expiration Date', 'Transaction Id', 'Transaction Amount', 'Transaction Status', 'Transaction Date', 'Subscription Id', 'Subscription Amount', 'Subscription Status', 'Subscription Start Date', 'Subscription End Date', 'Subscription Created At', 'Subscription Updated At', 'Subscription Next Payment Date', 'Subscription Cancelled Date',];
            // Create a new CSV file in memory
            $output = fopen('php://memory', 'w');
            fputcsv($output, $headers);
            foreach ($reportData as $key => $value) {
                if (!empty($value)) {
                    if ($value->subscription_id) {
                        $subscriptionModel = Subscription::where('stripe_id', $value->subscription_id)->first();
                        $value->subscription_details = self::formatSubscriptionDetails($subscriptionModel, $value->service_provider_id);
                    }
                }

                $subscription = $value->subscription_details ?? [];
                // Write the device data to the CSV file
                fputcsv($output, [
                    $value->device_title ?? '',
                    $value->device_type == 1 ? 'Personal' : 'Organization',
                    $value->device_model_name,
                    $value->serial_number,
                    $value->imei,
                    $value->cellular_service == 1 ? 'Yes' : 'No',
                    $value->carrier,
                    $value->capacity,
                    $value->color,
                    $value->user_full_name,
                    $value->org_name,
                    $value->sub_org_name,
                    $value->device_owner_name,
                    $value->org_user_full_name,
                    $value->org_user_designation,
                    $value->org_user_id,
                    $value->asset_tag,
                    $value->plan_name,
                    $this->dateFormat($value->payment_added_date),
                    $this->dateFormat($value->expiration_date),
                    $value->transaction_id,
                    isset($value->transaction_amount) ? '$' . $value->transaction_amount : '',
                    $value->transaction_status,
                    $this->dateFormat($value->transaction_created_at),
                    $subscription['id'] ?? '',
                    isset($subscription['amount']) ? '$' . $subscription['amount'] : '',
                    $subscription['status'] ?? '',
                    $this->dateFormat($subscription['created_at'] ?? ''),
                    $this->dateFormat($subscription['updated_at'] ?? ''),
                    $this->dateFormat($subscription['next_payment_date'] ?? ''),
                    $this->dateFormat($subscription['ended_at'] ?? ''),
                ]);
            }
            // Rewind the memory stream to the beginning
            rewind($output);
            // Get the contents of the memory stream (CSV content)
            $csvContent = stream_get_contents($output);
            // Close the memory stream
            fclose($output);
            // Set the headers for the CSV download
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ];
            // Return the CSV content as a response
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
}
