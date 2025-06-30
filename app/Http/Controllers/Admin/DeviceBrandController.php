<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\DeviceFamilyController;
use App\Http\Controllers\Controller;
use App\Models\Admin\DeviceBrand;
use App\Models\Admin\DeviceFamily;
use App\Models\Admin\DeviceModel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DeviceBrandController extends Controller
{
    /** Display a listing of the resource. */
    public function index(Request $request)
    {
        $where = $this->getFilter($request);
        $brandData = self::getDeviceBrandsPaginate(20, $where);
        $familyData = DeviceFamilyController::getDeviceFamiliesDropdown();
        $pagination = [
            'total' => $brandData->total(),
            'per_page' => $brandData->perPage(),
            'current_page' => $brandData->currentPage(),
            'last_page' => $brandData->lastPage(),
            'from' => $brandData->firstItem(),
            'to' => $brandData->lastItem()
        ];
        if (!empty($request->page)) {
            return response()->json(["brandData" => $brandData, "pagination" => $pagination, "msg" => "Paginated records here.", "success" => true], 200);
        } else {
            return view('admin.devicebrandmaster.index', compact('brandData', 'pagination', 'familyData'));
        }
    }

    /** Store a newly created resource in storage. */
    public function store(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate request data */
            $validator = Validator::make($request->all(), [
                'name' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('device_brands', 'name')
                        ->where(function ($query) use ($request, $serviceProvider) {
                            $query->where('service_provider_id', $serviceProvider)->where('device_family_id', $request->deviceFamily)->where('active', 1);
                        })
                ],
                'deviceFamily' => 'required|exists:device_families,id,active,1,service_provider_id,' . $serviceProvider,
            ], [
                'name.required' => 'The device brand name field is required.',
                'name.string' => 'The device brand name must be a string.',
                'name.max' => 'The device brand name may not be greater than 50 characters.',
                'name.unique' => 'The device brand name has already been taken.',
                'deviceFamily.required' => 'The device family field is required.',
                'deviceFamily.exists' => 'The selected device family does not exist.',
            ]);
            /* Check if validation fails */
            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $validator->errors()
                ], 200);
            }
            /* Create a new DeviceBrand record */
            $deviceBrand = DeviceBrand::create([
                'name' => $request->name,
                'device_family_id' => $request->deviceFamily,
                'service_provider_id' => $serviceProvider,
            ]);
            /* If device brand created successfully */
            if ($deviceBrand->wasRecentlyCreated) {
                return response()->json(["msg" => "Device Brand created successfully.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Device Brand creation failed.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Show the form for editing the specified resource. */
    public function edit(string $id)
    {
        if (!empty($id)) {
            $where = [
                'where' => [
                    'device_brands.id' => $id,
                ]
            ];
            $editData = self::getDeviceBrandsQuery($where)->first();
            if (!empty($editData)) {
                return response()->json(["editData" => $editData, "msg" => "Edit Device Brand Data.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
            }
        } else {
            return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
        }
    }

    /** Update the specified resource in storage. */
    public function update(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate request data */
            $validator = Validator::make($request->all(), [
                'name' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('device_brands', 'name')
                        ->where(function ($query) use ($request, $serviceProvider) {
                            $query->where('service_provider_id', $serviceProvider)->where('device_family_id', $request->deviceFamily)->where('active', 1);
                        })
                        ->ignore($request->id, 'id'), /* To exclude the current record during update */
                ],
                'deviceFamily' => ['required', 'exists:device_families,id,active,1,service_provider_id,' . $serviceProvider],
            ], [
                'name.required' => 'The device brand name field is required.',
                'name.string' => 'The device brand name must be a string.',
                'name.max' => 'The device brand name may not be greater than 50 characters.',
                'name.unique' => 'The device brand name has already been taken.',
                'deviceFamily.required' => 'The device family field is required.',
                'deviceFamily.exists' => 'The selected device family does not exist.',
            ]);

            /* Check if validation fails */
            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $validator->errors()
                ], 200);
            }
            /* Find the device brand */
            $deviceBrand = DeviceBrand::where('id', $request->id)->where('service_provider_id', $serviceProvider)->where('active', 1)->first();
            /* Check if the device brand not found */
            if (empty($deviceBrand)) {
                return response()->json(["msg" => "Device Brand not found.", "success" => false], 200);
            }
            /* Update device brand data */
            $deviceBrand->name = $request->name;
            $deviceBrand->device_family_id = $request->deviceFamily;

            if ($deviceBrand->save()) {
                return response()->json(["msg" => "Device Brand updated successfully.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Device Brand update failed.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Remove the specified resource from storage. */
    public function destroy(string $id)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Find the device brand */
            $deviceBrand = DeviceBrand::where('id', $id)->where('service_provider_id', $serviceProvider)->first();

            if (!empty($deviceBrand)) {
                /* Toggle active status */
                $deviceBrand->active = $deviceBrand->active == 1 ? 0 : 1;
                $deviceBrand->save();
                /* Set the same active status for related device models */
                DeviceModel::where('device_brand_id', $id)->where('service_provider_id', $serviceProvider)->update(['active' => $deviceBrand->active]);
                /* Dynamic message based on new active status */
                $message = $deviceBrand->active == 1 ? 'Activated' : 'Deactivated';
                return response()->json(["msg" => "Device Brand {$message} successfully.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Device Brand not found.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Get device brands(query). */
    private static function getDeviceBrandsQuery($where = [], $fields = [])
    {
        $serviceProvider = Session::get('service_provider');
        $query = DB::table('device_brands')
            ->leftJoin('device_families as deviceFamily', 'device_brands.device_family_id', '=', 'deviceFamily.id')
            ->where('device_brands.service_provider_id', $serviceProvider)
            ->where('device_brands.active', 1);
          /*   ->where(function ($query) use ($serviceProvider) {
                $query->whereNull('device_brands.device_family_id')
                    ->orWhere(function ($query) use ($serviceProvider) {
                        $query->where('deviceFamily.active', 1)
                            ->where('deviceFamily.service_provider_id', $serviceProvider);
                    });
            }); */
        if (!empty($fields) && is_array($fields)) {

            $query->select($fields);
        } else {
            $query->select(
                'device_brands.id',
                'device_brands.name',
                'device_brands.device_family_id',
                'device_brands.active',
                'deviceFamily.name as device_family_name',
            );
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
        return $query->orderBy('device_brands.name', 'asc');
    }

    /** Get all the device brands(Paginate). */
    public static function getDeviceBrandsPaginate($limit = 20, $where = [], $fields = [])
    {
        $data = self::getDeviceBrandsQuery($where, $fields);
        $limit = (is_numeric($limit) && $limit > 0) ? $limit : 20;
        return $data->paginate($limit);
    }

    /** Get all the device brands. */
    public static function getDeviceBrands($where = [], $fields = [])
    {
        /** $where['where']['device_brands.active'] = 1; */
        $fields = [
            'device_brands.id',
            'device_brands.name',
            'deviceFamily.name as device_family_name',
        ];
        $data = self::getDeviceBrandsQuery($where, $fields);
        return $data->get();
    }

    /** Get Device brand according to family */
    public function getDeviceBrandsByFamily(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            $validator = Validator::make($request->all(), [
                'familyId' => 'required|exists:device_families,id,active,1,service_provider_id,' . $serviceProvider,
            ]);
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }

            if (!empty($request->familyId)) {
                $familyId = $request->familyId;
                if (!is_numeric($familyId)) {
                    return response()->json(['success' => false, 'msg' => "Invalid family ID"], 200);
                } else {
                    $where = [
                        'where' => [
                            'device_family_id' => $familyId,
                            'device_brands.active' => 1,
                        ]
                    ];
                    $brandData = self::getDeviceBrandsQuery($where, ['device_brands.id', 'device_brands.name'])->orderBy('device_brands.name', 'asc')->get();
                    if (empty($brandData)) {
                        return response()->json(['success' => false, 'msg' => "No Devcie Brand found in this family"], 200);
                    } else {
                        return response()->json(['success' => true, 'brandData' => $brandData], 200);
                    }
                }
            } else {
                return response()->json(['success' => false, 'msg' => "Family ID is required"], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => "Failed to get device brands."], 200);
        }
    }

    /** For Filter's */
    private function getFilter(Request $request)
    {
        $where = [];
        /** Search by device brand name, family, status */
        if (!empty($request->name) || !empty($request->familyId) || !is_null($request->status)) {
            $where['where'] = [];
            /* If name is not empty */
            if (!empty($request->name)) {
                $where['where']['device_brands.name'] = ['LIKE', "%{$request->name}%"];
            }
            /* If Device Family is not empty */
            if (!empty($request->familyId)) {
                $where['where']['device_brands.device_family_id'] = $request->familyId;
            }
            /**
             * If status is not empty
             * 1 = active
             * 0 = deactive
             * */
            if (!is_null($request->status)) {
                $where['where']['device_brands.active'] = ['=', $request->status];
            }
        }
        return $where;
    }
}
