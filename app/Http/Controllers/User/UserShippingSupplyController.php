<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Models\Admin\Organization;
use App\Models\Admin\SubOrganization;
use App\Models\Admin\SupplyBox;
use App\Models\OrgRelationship;
use App\Models\User;
use App\Models\User\ShippingAddress;
use App\Models\User\ShippingSupply;
use App\Models\User\ShippingSupplyBoxInfo;
use App\Models\UserMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UserShippingSupplyController extends Controller
{
     /**
     * Shipping supply List View
     */
    public function index(Request $request)
    {

        $where = $this->filterSupplyRequest($request);
        $shippingSupplyData = self::paginateshippingSupplies(20, $where);
        $serviceProvider = Session::get('service_provider');
        $boxTypes = SupplyBox::where('service_provider_id', $serviceProvider)->where('active', 1)->select('id', 'name')->get();
        $pagination = [
            'total' => $shippingSupplyData->total(),
            'per_page' => $shippingSupplyData->perPage(),
            'current_page' => $shippingSupplyData->currentPage(),
            'last_page' => $shippingSupplyData->lastPage(),
            'from' => $shippingSupplyData->firstItem(),
            'to' => $shippingSupplyData->lastItem()
        ];
        if (!empty($request->page)) {
            return response()->json(["shippingSupplyData" => $shippingSupplyData, "pagination" => $pagination, "msg" => "Paginated records here.", "success" => true], 200);
        } else {
            return view('user.shippingSupplies.shippingsupplied', compact('boxTypes', 'shippingSupplyData', 'pagination'));

        }
    }

    /** Store */
    public function store(Request $request){
        try{
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate to the requests */
            $validator = Validator::make($request->all(), [
                'streetAddress' => 'required|string',
                'addressLine2' => 'nullable|string',
                'city' => 'required',
                'state' => 'required',
                'zipCode' => 'required|regex:/^[0-9]{5}$/',
                'country' => 'required',
                'boxData' => 'required|array',
                'boxData.*.boxType' => 'required|distinct',
                'boxData.*.boxQuantity' => 'required|integer|min:1',
            ], [
                'streetAddress.required' => "Please enter the street address.",
                'city.required' => "Please enter the city name.",
                'state.required' => "Please enter the state name.",
                'zipCode.required' => "Please enter the zip code.",
                'zipCode.regex' => "Zip code must be a 5 digit number.",
                'country.required' => "Please enter the country name.",

                'boxData.*.boxType.required' => "Please select the box type.",
                'boxData.*.boxType.distinct' => "This box type is already selected.",
                'boxData.*.boxQuantity.required' => "Please enter the box quantity.",
                'boxData.*.boxQuantity.integer' => "Box quantity must be a valid number.",
                'boxData.*.boxQuantity.min' => "Box quantity must be at least 1.",

            ]);
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
             /** Getting user data to get the id of the user */
            $userData = Session::get('auth_user');
            $user_id = $userData->id;
            $user_fullName = $userData->full_name;
            $organdSubOrgId = $this->getAuthUserOrgSubOrg($user_id);
            if (empty($organdSubOrgId)) {
                return response()->json(['success' => false, 'msg' => "Something went wrong, Please try again later."], 200);
            }
            $org_id = null;
            $sub_org_id = null;
            if (isset($organdSubOrgId->org_id) && isset($organdSubOrgId->parent_org_id)) {
                $org_id = $organdSubOrgId->parent_org_id;
                $sub_org_id = $organdSubOrgId->org_id;
            }elseif (isset($organdSubOrgId->org_id) && is_null($organdSubOrgId->parent_org_id)) {
                $org_id = $organdSubOrgId->org_id;
                $sub_org_id = null;
            }else {
                $org_id = null;
                $sub_org_id = null;
            }
            if(!empty($request->boxData)){
                $shippingSupplyData = ShippingSupply::create([
                    'user_id' => $user_id,
                    'org_id' => $org_id,
                    'sub_org_id' => $sub_org_id,
                    /** Insert the address here in shipping supply table */
                    'street_address' => $request->streetAddress,
                    'address_line_2' => $request->addressLine2,
                    'country' => $request->country,
                    'city' => $request->city,
                    'state' => $request->state,
                    'zipcode' => $request->zipCode,
                    'service_provider_id' => $serviceProvider,
                ]);

                if (!empty($shippingSupplyData)) {
                    $shippingSupplyId = $shippingSupplyData->id;
                    foreach ($request->boxData as $key => $box) {
                        $supplyboxesData = ShippingSupplyBoxInfo::create([
                            'user_id' => $user_id,
                            'shipping_supply_id' => $shippingSupplyId,
                            'box_type' => $box['boxType'] ?? null,
                            'box_quantity' => $box['boxQuantity'] ?? null,
                        ]);
                        if (empty($supplyboxesData->id)) {
                            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
                        }
                    }
                    if($request->isAddressEdit == 1){
                        $shippingData = ShippingAddress::updateOrCreate(
                            [ 'user_id' => $user_id ],
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
                            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
                        }
                    }
                    /** Notifications */
                    $notificationRequest = [
                        'user_id' => $user_id,
                        'message' => "A new request for shipping supply boxes from: " . $user_fullName,
                        'notification_for' => 'shipping_supply',
                        'shipping_supply_id' => $shippingSupplyId,
                        'user_target_link' => null,
                        'admin_target_link' => 'smarttiusadmin/shipping-supplies',
                        'service_provider_id' => $serviceProvider,
                    ];
                    NotificationController::addNotification($notificationRequest);
                    return response()->json(["msg" => "Thanks For Shipping Supply request.", "success" => true], 200);
                } else {
                    return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
                }
            }else {
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }

        } catch (\Exception $e){
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** To get the user data(address and other) by using the user Id to show the data in shipping supplies request form */
    public function getUserdataById(){
        $authUser = Session::get('auth_user');
        $serviceProvider = Session::get('service_provider');

        try{
            /** Query to get the data */
            $user_shipping_data = ShippingAddress::select('street_address', 'address_line_2', 'city', 'state', 'zip', 'country')->where('user_id',$authUser->id)->where('active', 1)->where('service_provider_id', $serviceProvider)->latest()->first();
            if (empty($user_shipping_data)) {
                $streetAddress = '';
                $addressLine2 = '';
                $country = '';
                $state = '';
                $city = '';
                $zip = '';
            }
            $streetAddress = $user_shipping_data->street_address ?? '';
            $addressLine2 = $user_shipping_data->address_line_2 ?? '';
            $country = $user_shipping_data->country ?? '';
            $state = $user_shipping_data->state ?? '';
            $city = $user_shipping_data->city ?? '';
            $zip = $user_shipping_data->zip ?? '';
            $org_relationship = OrgRelationship::select('org_id', 'parent_org_id', 'user_id')->where('user_id',$authUser->id)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
            if(empty($org_relationship)) {
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }
            $orgId = null;
            $subOrgId = null;
            if (isset($org_relationship->org_id) && isset($org_relationship->parent_org_id) ) {
                $subOrgId = $org_relationship->org_id;
                $orgId = $org_relationship->parent_org_id;
            }elseif (isset($org_relationship->org_id) && is_null($org_relationship->parent_org_id)) {
                $orgId = $org_relationship->org_id;
            }
            if (is_null($org_relationship->org_id) && is_null($org_relationship->parent_org_id)) {
                $orgName = '';
                $subOrgName = '';
            }else {
                if(is_null($orgId)) {
                    $orgName = '';
                } else {
                    $orgNameData = Organization::select('name')->where('id',$orgId)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
                    if (empty($orgNameData)) {
                        $orgName = '';
                    } else {
                        $orgName = $orgNameData->name ?? '';
                    }

                }
                if (is_null($subOrgId)) {
                    $subOrgName = '';
                } else {
                    $subOrgNameData = SubOrganization::select('name')->where('id',$subOrgId)->where('org_id', $orgId)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
                    if (empty($subOrgNameData)) {
                        $subOrgName = '';
                    } else {
                        $subOrgName = $subOrgNameData->name ?? '';
                    }

                }
            }
            $userMeta = UserMeta::select('meta_key','meta_value')->where('user_id', $authUser->id)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
            if (empty($userMeta)) {
                $user_meta_key = '';
                $user_meta_key_value = '';
            }else {
                $user_meta_key = $userMeta->meta_key ?? '';
                $user_meta_key_value = $userMeta->meta_value ?? '';
            }
            $userDetails = (object) [
                'first_name' => $authUser->first_name ?? '',
                'last_name' => $authUser->last_name ?? '',
                'email' => $authUser->email ?? '',
                'orgName' => $orgName,
                'subOrgName' => $subOrgName,
                'userMetaKey' => $user_meta_key,
                'userMetaValue' => $user_meta_key_value,
                'streetAddress' => $streetAddress,
                'addressLine2' => $addressLine2,
                'country' => $country,
                'state' => $state,
                'city' => $city,
                'zip' => $zip,
            ];
            return response()->json(["msg" => "User Details.", "userDetails" => $userDetails, "success" => true], 200);

        } catch (\Exception $e){
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** To view shipping supply request */
    public function getShippingSupplyRequestById(Request $request){
        if (!empty($request->requestId)) {
            $where['where']['supplyBox.id'] = $request->requestId;
            $requestData = self::shippingSupplyQuery($where)->first();
            if (!empty($requestData)) {
                return response()->json(["viewData" => $requestData, "msg" => "Shipping Supply Request Data.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Shipping Supply Request not found.", "success" => false], 200);
            }
        } else {
            return response()->json(["msg" => "Shipping Supply Request not found.", "success" => false], 200);
        }
    }

    /** Shipping Supply Query */
    private static function shippingSupplyQuery($where = [], $fields = [])
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $query = DB::table('shipping_supplies')
                ->leftJoin('shipping_supply_boxes_info as supplyBox', 'shipping_supplies.id', '=', 'supplyBox.shipping_supply_id')
                ->leftJoin('users', 'users.id', '=', 'shipping_supplies.user_id')
                ->where('shipping_supplies.active', 1)
                ->where('shipping_supplies.service_provider_id', $serviceProvider);

        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select(
                'shipping_supplies.id',
                'shipping_supplies.status',
                'users.full_name',
                'shipping_supplies.street_address',
                'shipping_supplies.address_line_2',
                'shipping_supplies.country',
                'shipping_supplies.state',
                'shipping_supplies.city',
                'shipping_supplies.zipcode',
                'supplyBox.id as box_id',
                'supplyBox.box_type as box_type',
                'supplyBox.box_quantity as box_quantity',
                'shipping_supplies.created_at',
            );
        }
        /* Auth User */
        $authUser = Session::get('auth_user');

        /** hasRole
         * 1 = admin
         * 2 = user
         */
        if ($authUser && $authUser->hasRole(2)) {
            $org_relationship = OrgRelationship::select('user_id', 'org_id', 'parent_org_id', 'is_org_subscriber')->where('user_id', $authUser['id'])->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
            if (!empty($authUser['id']) && !empty($org_relationship->org_id) && empty($org_relationship->parent_org_id) && $org_relationship->is_org_subscriber == 0) {
                /** IT HOD */
                $where['where']['shipping_supplies.org_id'] = $org_relationship->org_id;
            } elseif (!empty($authUser['id']) && !empty($org_relationship->org_id) && !empty($org_relationship->parent_org_id) && $org_relationship->is_org_subscriber == 0) {
                /* IT Director */
                $where['where']['shipping_supplies.org_id'] = $org_relationship->parent_org_id;
                $where['where']['shipping_supplies.sub_org_id'] = $org_relationship->org_id;
            } elseif (!empty($authUser['id']) && !empty($org_relationship->org_id) && !empty($org_relationship->parent_org_id) && $org_relationship->is_org_subscriber == 1) {
                /* Organization Subscriber */
                $where['where']['shipping_supplies.user_id'] = $authUser['id'];
                /* $where['where']['shipping_supplies.org_id'] = $org_relationship->parent_org_id;
                $where['where']['shipping_supplies.sub_org_id'] = $org_relationship->org_id; */
            } else {
                /** Subscriber */
                $where['where']['shipping_supplies.user_id'] = $authUser['id'];
            }
        }
        if (!empty($where) && is_array($where)) {
            foreach ($where as $field => $value) {
                if (is_array($value)) {
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
        return $query->orderBy('shipping_supplies.created_at', 'desc');
    }

    /* Get All the Devices(paginate). */
    public static function paginateshippingSupplies($limit = 20, $where = [], $fields = [])
    {
        $fields = [
            'supplyBox.id as box_id',
            'users.full_name',
            'supplyBox.box_type as box_type',
            'supplyBox.box_quantity as box_quantity',
            'shipping_supplies.status',
            'shipping_supplies.created_at',
        ];
        if(empty($where) && empty($where['where']['shipping_supplies.status']))
        {
            $where['where']['shipping_supplies.status'] = 1;
        }
        $data = self::shippingSupplyQuery($where, $fields)->paginate($limit);
        return $data;
    }

    /* filter according to request date and box types */
    private function filterSupplyRequest(Request $request)
    {
        $where = [];
        if (!empty($request->selectedBoxType) || !empty($request->requestDate) || !empty($request->status)) {
            if (!empty($request->selectedBoxType)) {
                if ($request->selectedBoxType == 'all') {
                    $where = [];
                }else {
                    $where['where']['supplyBox.box_type'] = $request->selectedBoxType;
                }
            }
            if (!empty($request->requestDate)) {
                $where['whereDate']['shipping_supplies.created_at'] = $request->requestDate;
            }
            if (!empty($request->status)) {
                $where['where']['shipping_supplies.status'] = $request->status;
            }
        }
        return $where;
    }

    /** Auth User Organization and sub-organization id get */
    private function getAuthUserOrgSubOrg($userId){
        $serviceProvider = Session::get('service_provider');
        $orgRelationship = OrgRelationship::select('user_id','org_id', 'parent_org_id')->where('user_id', $userId)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
        return $orgRelationship;
    }

}
