<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\User\UserShippingSupplyController;
use App\Http\Controllers\Controller;
use App\Models\Admin\SupplyBox;
use App\Models\User\ShippingSupply;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminShippingSuppliesController extends Controller
{

    /** Supply boxes CRUD */
    /** List out the box listing */
    public function boxList(Request $request)
    {
        $where = [];
        if (!empty($request->search)) {
            $where = [
                'where' => [
                    'name' => ['LIKE', "%{$request->search}%"],
                ]
            ];
        }
        $shippingSupplyBoxData = $this->paginateShippingSupplyBoxes(20, $where);

        $pagination = [
            'total' => $shippingSupplyBoxData->total(),
            'per_page' => $shippingSupplyBoxData->perPage(),
            'current_page' => $shippingSupplyBoxData->currentPage(),
            'last_page' => $shippingSupplyBoxData->lastPage(),
            'from' => $shippingSupplyBoxData->firstItem(),
            'to' => $shippingSupplyBoxData->lastItem()
        ];
        if (!empty($request->page)) {
            return response()->json(["shippingSupplyBoxData" => $shippingSupplyBoxData, "pagination" => $pagination, "msg" => "Paginated records here.", "success" => true], 200);
        } else {
            return view('admin.adminshippingsupplyboxes.index', compact('shippingSupplyBoxData', 'pagination'));
        }
    }

    /** Create the supply box */
    public function storeBox(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');

            /* Validate to the requests */
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50|unique:supply_boxes,name,Null,id,active,1,service_provider_id,' . $serviceProvider,
            ], [
                'name.required' => "Box name can't be empty.",
                'name.unique' => "Box name must be unique.",
            ]);

            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }

            $SupplyBox = SupplyBox::create([
                'name' => $request->name,
            ]);
            if ($SupplyBox->wasRecentlyCreated) {
                return response()->json(["msg" => "Supply box added successfully.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** To edit the box(get data and show in update form) */
    public function editBox()
    {
        try {
            $supplyBoxId = request()->id;
            if (!empty($supplyBoxId)) {
                $SupplyBox = SupplyBox::where('id', $supplyBoxId)->where('active', 1)->where('service_provider_id', Session::get('service_provider'))->first();
                if (!empty($SupplyBox)) {
                    return response()->json(["supplyBox" => $SupplyBox, "msg" => "Supply box data found.", "success" => true], 200);
                } else {
                    return response()->json(["msg" => "Supply box not found.", "success" => false], 200);
                }
            } else {
                return response()->json(["msg" => "Supply box not found.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Edit the box name */
    public function updateBox(Request $request)
    {
        try {
            /* Validate to the requests */
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50|unique:supply_boxes,name,' . $request->id . ',id,active,1,service_provider_id,' . Session::get('service_provider'),
            ], [
                'name.required' => "Box name can't be empty.",
                'name.unique' => "Box name must be unique.",
            ]);

            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }

            $SupplyBox = SupplyBox::find($request->id);
            if (!empty($SupplyBox)) {
                $SupplyBox->name = $request->name;
                if ($SupplyBox->save()) {
                    return response()->json(["msg" => "Supply box updated successfully.", "success" => true], 200);
                } else {
                    return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
                }
            } else {
                return response()->json(["msg" => "Supply box not found.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Function to get the paginated records of the supply boxes */
    private function paginateShippingSupplyBoxes($limit = 20, $where = [], $fields = [])
    {
        $data = $this->shippingSupplyBoxQuery($where, $fields)->paginate($limit);
        return $data;
    }

    /** Common function to create the query for supply boxes as needed */
    private function shippingSupplyBoxQuery($where = [], $fields = [])
    {
        /* Get service provider is using session */
        $serviceProvider = Session::get('service_provider');
        $query = SupplyBox::where('service_provider_id', $serviceProvider)->where('active', 1);
        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select('id', 'name');
        }
        if (!empty($where) && is_array($where)) {
            foreach ($where as $field => $value) {
                if (is_array($value)) {
                    foreach ($value as $secondField => $secondValue) {
                        if ($field == 'where' && is_array($secondValue)) {
                            /* Correct handling for [operator, value] format */
                            $query->$field($secondField, $secondValue[0], $secondValue[1]);
                        } else {
                            $query->$field($secondField, $secondValue);
                        }
                    }
                }
            }
        }
        $query->orderBy('name', 'asc');
        return $query;
    }

    /** Shipping supply request data function*/
    /** Index function to show all records */
    public function index(Request $request)
    {
        // $where = [];
        $where = $this->filterSupplyRequest($request);
        $shippingSupplyData = self::paginateshippingSupplies(20, $where);
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
            return view('admin.adminshippingsupplies.index', compact('shippingSupplyData', 'pagination'));
        }
    }

    /** Shipping supply List Filter */
    private function filterSupplyRequest(Request $request)
    {
        $where = [];
        if (!empty($request->search) || !empty($request->status)) {
            $where = [
                'where' => [
                    'shipping_supplies.status' => $request->status,
                ],
                'orWhere' => [
                    'users.email' => ['LIKE', "%{$request->search}%"],
                    'organizations.name' => ['LIKE', "%{$request->search}%"],
                ]
            ];
        }
        return $where;
    }

    public function getShippingSupplyDetailById(Request $request)
    {
        if (!empty($request->requestId)) {
            $where['where']['shipping_supplies.id'] = $request->requestId;
            $fields = [
                'shipping_supplies.id',
                'shipping_supplies.status',
                'users.full_name',
                'users.email',
                'users.id as user_id',
                'users.phone',
                'organizations.name as organization_name',
                'sub_organizations.name as sub_organization_name',
                'shipping_supplies.street_address',
                'shipping_supplies.address_line_2',
                'shipping_supplies.country',
                'shipping_supplies.state',
                'shipping_supplies.city',
                'shipping_supplies.zipcode',
                'shipping_supplies.created_at',
            ];
            /** Getting the User data from main supply table */
            $supplyData = self::shippingSupplyQuery($where, $fields)->get();

            /** Getting the box details for the id */
            $supplyBoxData = self::shippingBoxDetailQuery($request->requestId);

            if (!empty($supplyData) && !empty($supplyBoxData)) {
                return response()->json(["viewSupplyData" => $supplyData, "viewBoxData" => $supplyBoxData, "msg" => "Shipping Supply Request Data.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Shipping Supply Request not found.", "success" => false], 200);
            }
        } else {
            return response()->json(["msg" => "Shipping Supply Request not found.", "success" => false], 200);
        }
    }


    /** Update Status */
    public function updateShippingSupplyStatus(Request $request)
    {
        try {

            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate request data */
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:shipping_supplies,id,active,1,service_provider_id,' . $serviceProvider,
                'status' => 'required|in:1,2', // Assuming 1 is for 'Pendding' and 2 is for 'Completed'
            ], [
                'status.required' => "Please select the status.",
                'status.in' => "Status must be either 'Pendding' or 'Completed'.",
                'id.required' => "Shipping Supply Request ID is required.",
                'id.exists' => "It must be a valid ID.",
            ]);

            /* Check if validation fails */
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $shippingSupply = ShippingSupply::where('id', $request->id)->where('service_provider_id', $serviceProvider)->where('active', 1)->first();
            if (!empty($shippingSupply)) {
                $shippingSupply->status = $request->status;
                if ($shippingSupply->save()) {
                    return response()->json(["msg" => "Shipping Supply Request status updated successfully.", "success" => true, 'status' => $shippingSupply->status], 200);
                } else {
                    return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
                }
            } else {
                return response()->json(["msg" => "Shipping Supply Request not found.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /* Get All the Shipping Supplies. */
    public static function paginateshippingSupplies($limit = 20, $where = [], $fields = [])
    {
        if (empty($where))
        {
            $where = [
                'where' => [
                    'shipping_supplies.status' => 1,
                ]
            ];

        }
        $data = self::shippingSupplyQuery($where, $fields)->paginate($limit);
        return $data;
    }

    /** Get box details by supply id */
    public function shippingBoxDetailQuery($id)
    {
        $query = DB::table('shipping_supply_boxes_info')->where('shipping_supply_id', $id);
        $query->select('box_type', 'box_quantity');
        return $query->get();
    }

    /** Shipping Supply Query */
    private static function shippingSupplyQuery($where = [], $fields = [])
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $query = DB::table('shipping_supplies')
            // ->leftJoin('shipping_supply_boxes_info as supplyBox', 'shipping_supplies.id', '=', 'supplyBox.shipping_supply_id')
            ->leftJoin('users', 'users.id', '=', 'shipping_supplies.user_id')
            ->leftJoin('organizations', 'organizations.id', '=', 'shipping_supplies.org_id')
            ->leftJoin('sub_organizations', 'sub_organizations.id', '=', 'shipping_supplies.sub_org_id')
            ->where('shipping_supplies.active', 1)
            ->where('shipping_supplies.service_provider_id', $serviceProvider);

        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select(
                'shipping_supplies.id',
                'shipping_supplies.status',
                'users.full_name',
                'users.email',
                'organizations.name as organization_name',
                'sub_organizations.name as sub_organization_name',
                'shipping_supplies.created_at',
            );
        }
        /* Auth User */
        $authUser = Session::get('auth_user');

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
        return $query->orderBy('shipping_supplies.created_at', 'desc');
    }
}
