<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\ShippingOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AdminRepairsShippingOptionController extends Controller
{
    /** Index function to call the view file */
    public function index(Request $request)
    {
        $where = [];
        $shippingOptions = self::getPaginatedShippingOptions(20, $where);
        $pagination = [
            'total' => $shippingOptions->total(),
            'per_page' => $shippingOptions->perPage(),
            'current_page' => $shippingOptions->currentPage(),
            'last_page' => $shippingOptions->lastPage(),
            'from' => $shippingOptions->firstItem(),
            'to' => $shippingOptions->lastItem()
        ];

        if (!empty($request->page)) {
            return response()->json(["shippingOptionsData" => $shippingOptions, "pagination" => $pagination, "msg" => "Shipping Options For Repairs Fetched.", "success" => true], 200);
        } else {
            return view('admin.adminrepairshippingoptions.index', compact('shippingOptions', 'pagination'));
        }
    }

    /** To store the option data */
    public function store(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate to the requests */
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50|unique:shipping_options,name,Null,id,active,1,option_for,2,service_provider_id,' . $serviceProvider,
                'price' => 'required|numeric|min:0',
            ], [
                'name.required' => "Shipping option name can't be empty.",
                'name.unique' => "Option name must be unique",
                'price.required' => "Shipping option price can't be empty.",
                'price.numeric' => "Shipping option price must be a number.",
            ]);

            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }

            /* Creating Shipping option */
            $shippingOption = ShippingOption::create([
                'name' => $request->name,
                'price' => $request->price,
                'option_for' => 2,
                'service_provider_id' => $serviceProvider,
            ]);
            if ($shippingOption->wasRecentlyCreated) {
                return response()->json(["msg" => "Shipping option for repairs added successfully.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Shipping option for repairs creation failed.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** To get the edit data */
    public function edit(string $id)
    {
        if (!empty($id)) {
            $where['where']['id'] = $id;
            $shippingOptions = self::getShippingOptionsQuery($where)->first();
            if (!empty($shippingOptions)) {
                return response()->json(["editData" => $shippingOptions, "msg" => "Edit Shipping Option Data For Repairs.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }
        } else {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** To update the shipping options */
    public function update(Request $request, $id)
    {
        try {
            /* Getting service provider id using session */
            $serviceProvider = Session::get('service_provider');
            /* Validate to the requests */
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50|unique:shipping_options,name,' . $id . ',id,active,1,option_for,2,service_provider_id,' . $serviceProvider,
                'price' => 'required|numeric|min:0',
            ], [
                'name.required' => "Name can't be empty.",
                'name.unique' => "Name must be unique",
                'price.required' => "Price can't be empty.",
                'price.numeric' => "Price must be a number.",
            ]);
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            /* Updating shipping options */
            $shippingOption = ShippingOption::where('id', $id)->where('option_for', 2)->where('service_provider_id', $serviceProvider)->where('active', 1)->first();
            if (!empty($shippingOption)) {
                $shippingOptionUpdate = $shippingOption->update([
                    'name' => $request->name,
                    'price' => $request->price,
                ]);

                if (!empty($shippingOptionUpdate)) {
                    return response()->json(["msg" => "Shipping option for repairs updated successfully.", "success" => true], 200);
                } else {
                    return response()->json(["msg" => "Shipping option for repairs updation failed.", "success" => false], 200);
                }
            } else {
                return response()->json(["msg" => "Shipping option not found.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** To get the paginated records */
    private static function getPaginatedShippingOptions($limit = 20, $where = [], $fields = [])
    {
        $data = self::getShippingOptionsQuery($where, $fields);
        $limit = (is_numeric($limit) && $limit > 0) ? $limit : 20;
        return $data->paginate($limit);
    }

    /** To get the query for shipping options */
    private static function getShippingOptionsQuery($where = [], $fields = [])
    {
        /* Get service provider is using session */
        $serviceProvider = Session::get('service_provider');
        $query = ShippingOption::where('service_provider_id', $serviceProvider)->where('option_for', 2)->where('active', 1);
        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select('id', 'name', 'price', 'active', 'created_at');
        }
        if (!empty($where) && is_array($where)) {
            foreach ($where as $field => $value) {
                if (is_array($value)) {
                    foreach ($value as $secondField => $secondValue) {
                        if (is_array($secondValue)) {
                            /* Correct handling for [operator, value] format */
                            $query->$field($secondField, $secondValue[0], $secondValue[1]);
                        } else {
                            $query->$field($secondField, $secondValue);
                        }
                    }
                }
            }
        }
        $query->orderBy('created_at', 'desc');
        return $query;
    }

    /** Shipping Options Get for drop-down */
    public static function getShippingOptionsDropDown($where = [], $fields = [])
    {
        $data =  self::getShippingOptionsQuery($where, $fields)->get();
        return $data;
    }
}
