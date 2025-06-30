<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\DeviceFamilyController;
use App\Http\Controllers\Controller;

use App\Models\Admin\Device;
use App\Models\Admin\DeviceModel;
use App\Models\Admin\DevicePlan;

use App\Models\Admin\OrgAllowedModel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;


class DeviceModelController extends Controller
{
    /* Display a listing of the resource. */
    public function index(Request $request)
    {
        $where = $this->getFilter($request);
        $modelData = self::getPaginatedDeviceModel(20, $where);
        $familyData = DeviceFamilyController::getDeviceFamiliesDropdown();
        $pagination = [
            'total' => $modelData->total(),
            'per_page' => $modelData->perPage(),
            'current_page' => $modelData->currentPage(),
            'last_page' => $modelData->lastPage(),
            'from' => $modelData->firstItem(),
            'to' => $modelData->lastItem()
        ];
        if (!empty($request->page)) {
            return response()->json(["modelData" => $modelData, "pagination" => $pagination, "msg" => "Paginated records here.", "success" => true], 200);
        } else {
            return view('admin.devicemodelmaster.index', compact('modelData', 'pagination', 'familyData'));
        }
    }

    /* Store a newly created resource in storage. */
    public function store(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate request data */
            $validator = Validator::make($request->all(), [
                'title' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('device_models', 'title')
                        ->where(function ($query) use ($request, $serviceProvider) {
                            $query->where('service_provider_id', $serviceProvider)
                                ->where('device_family_id', $request->deviceFamily)
                                ->where('device_brand_id', $request->deviceBrand);
                        }),
                ],
                'deviceFamily' => [
                    'required',
                    'exists:device_families,id,active,1,service_provider_id,' . $serviceProvider,
                ],
                'deviceBrand' => [
                    'required',
                    'exists:device_brands,id,active,1,service_provider_id,' . $serviceProvider,
                ],
                'showDeviceModel' => ['required', 'in:1,2,3'],
                'showDevicePublicly' => ['required', 'in:1,2']
            ], [
                'title.required' => 'Title is required.',
                'title.string' => 'Title must be a string.',
                'title.max' => 'Title should not exceed 50 characters.',
                'deviceFamily.required' => 'Device family is required.',
                'deviceFamily.exists' => 'Selected device family does not exist.',
                'deviceBrand.required' => 'Device brand is required.',
                'deviceBrand.exists' => 'Selected device brand does not exist.',
                'showDeviceModel.required' => 'Show device model is required.',
                'showDeviceModel.in' => 'Invalid show device model value.',
                'showDevicePublicly.required' => 'Select public presence.',
                'showDevicePublicly.in' => 'Invalid show device model value.',
            ]);

            /* Check if validation fails */
            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $validator->errors()
                ], 200);
            }
            /* Create a new DeviceModel record */
            /*  Show Device Model
                1 = Show under uninsured devices only,
                2 = Show under insured devices only,
                3 = Show everywhere
            */
            $deviceModel = DeviceModel::create([
                'title' => $request->title,
                'device_family_id' => $request->deviceFamily,
                'device_brand_id' => $request->deviceBrand,
                'show_device_model' => $request->showDeviceModel,
                'show_device_publicly' => $request->showDevicePublicly,
                'service_provider_id' => $serviceProvider,
            ]);
            /* Check if device model created successfully */
            if ($deviceModel->wasRecentlyCreated) {
                return response()->json([
                    "msg" => "Device Model created successfully.",
                    "success" => true,
                ], 200);
            } else {
                return response()->json(["msg" => "Device Model creation failed.", "success" => false], 200);
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
                    'device_models.id' => $id,
                ],
            ];
            $editData = self::getDeviceModelsQuery($where)->first();
            if (!empty($editData)) {
                return response()->json(["editData" => $editData, "msg" => "Edit Device Model Data.", "success" => true], 200);
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
            $validator = Validator::make($request->all(), [
                'title' => [
                    'required',
                    'string',
                    'max:50',
                    'unique:device_models,title,' . $request->id . ',id,active,1,service_provider_id,' . $serviceProvider .
                        ',device_family_id,' . $request->deviceFamily .
                        ',device_brand_id,' . $request->deviceBrand,
                ],
                'deviceFamily' => [
                    'required',
                    'exists:device_families,id,active,1,service_provider_id,' . $serviceProvider,
                ],
                'deviceBrand' => [
                    'required',
                    'exists:device_brands,id,active,1,service_provider_id,' . $serviceProvider,
                ],
                'showDeviceModel' => ['required', 'in:1,2,3'],
                'showDevicePublicly' => ['required', 'in:1,2'],
            ], [
                'title.required' => 'Title is required.',
                'title.string' => 'Title must be a string.',
                'title.max' => 'Title should not exceed 50 characters.',
                'deviceFamily.required' => 'Device family is required.',
                'deviceFamily.exists' => 'Selected device family does not exist.',
                'deviceBrand.required' => 'Device brand is required.',
                'deviceBrand.exists' => 'Selected device brand does not exist.',
                'showDeviceModel.required' => 'Show device model is required.',
                'showDeviceModel.in' => 'Invalid show device model value.',
                'showDeviceModel.required' => 'Show device model is required.',
                'showDeviceModel.in' => 'Invalid show device model value.',
                'showDevicePublicly.required' => 'Select public presence.',
                'showDevicePublicly.in' => 'Invalid show device model value.',
            ]);

            /* Check if validation fails */
            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $validator->errors()
                ], 200);
            }
            /* Find the device model */
            $deviceModel = DeviceModel::where('id', $request->id)
                ->where('active', 1)
                ->where('service_provider_id', $serviceProvider)
                ->first();
            if (empty($deviceModel)) {
                return response()->json(["msg" => "Device model not found.", "success" => false], 200);
            }
            /* Update device model data */
            /*  Show Device Model
                1 = Show under uninsured devices only,
                2 = Show under insured devices only,
                3 = Show everywhere
            */
            $deviceModel->title = $request->title;
            $deviceModel->device_family_id = $request->deviceFamily;
            $deviceModel->device_brand_id = $request->deviceBrand;
            $deviceModel->show_device_model = $request->showDeviceModel;
            $deviceModel->show_device_publicly = $request->showDevicePublicly;

            if ($deviceModel->save()) {
                return response()->json(["msg" => "Device model updated successfully.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Device model update failed.", "success" => false], 200);
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
            /* Find the device model */
            $deviceModel = DeviceModel::where('id', $id)->where('service_provider_id', $serviceProvider)->first();
            /* Check If Device Model is not Empty */
            if (!empty($deviceModel)) {
                /* Toggle active status */
                $deviceModel->active = $deviceModel->active == 1 ? 0 : 1;
                $deviceModel->save();
                /* Set the same active status for related device plans and device */
                DevicePlan::where('device_model_id', $id)->where('service_provider_id', $serviceProvider)->update(['active' => $deviceModel->active]);
                Device::where('device_model_id', $id)->where('service_provider_id', $serviceProvider)->update(['active' => $deviceModel->active]);
                /* Dynamic message based on new active status */
                $message = $deviceModel->active == 1 ? 'Activated' : 'Deactivated';
                return response()->json(["msg" => "Device Model {$message} successfully.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Device Model not found.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Get all the device models (query). */
    private static function getDeviceModelsQuery($where = [], $fields = [])
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $query = DB::table('device_models')
            ->leftJoin('device_families as deviceFamily', 'device_models.device_family_id', '=', 'deviceFamily.id')
            ->leftJoin('device_brands as deviceBrand', 'device_models.device_brand_id', '=', 'deviceBrand.id')
            ->where('device_models.service_provider_id', $serviceProvider)
            ->where('device_models.active', 1);
        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select(
                'device_models.id',
                'device_models.title',
                'device_models.device_family_id',
                'device_models.device_brand_id',
                'device_models.show_device_model',
                'device_models.active',
                'deviceFamily.name as device_family_name',
                'deviceBrand.name as device_brand_name',
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
        return $query;
    }
    /** Get device model list with pagination */
    public static function getPaginatedDeviceModel($limit = 20, $where = [], $fields = [])
    {
        $data = self::getDeviceModelsQuery($where, $fields)->orderBy('device_models.created_at', 'desc');
        $limit = (is_numeric($limit) && $limit > 0) ? $limit : 20;
        return $data->paginate($limit);
    }

    /** Get all device models without pagination */
    public static function getAllDeviceModel($where = [], $fields = [])
    {
        /** $where['where']['device_models.active'] = 1; */
        $data = self::getDeviceModelsQuery($where, $fields)->orderBy('device_models.title', 'asc');
        return $data->get();
    }


    /** Get all device models for dropdown(query) */
    private static function getDeviceModelsQueryForDropdown($where = [], $fields = [])
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $query = DB::table('device_models')
            ->leftJoin('device_families as deviceFamily', 'device_models.device_family_id', '=', 'deviceFamily.id')
            ->where('device_models.service_provider_id', $serviceProvider)
            ->where('device_models.active', 1);

        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select(
                'device_models.id',
                'device_models.title',
                'deviceFamily.name as device_family_name',
            );
        }
        if (!empty($where) && is_array($where)) {
            foreach ($where as $conditionType => $conditions) {
                if (!is_array($conditions)) continue;

                foreach ($conditions as $column => $value) {
                    switch ($conditionType) {
                        case 'where':
                            if (is_array($value)) {
                                $query->where($column, $value[0], $value[1]);
                            } else {
                                $query->where($column, $value);
                            }
                            break;

                        case 'whereIn':
                            $query->whereIn($column, array_unique($value)); // Remove duplicates
                            break;

                        case 'whereNot':
                            $query->where($column, '!=', $value);
                            break;

                        case 'whereNotIn':
                            $query->whereNotIn($column, $value);
                            break;
                    }
                }
            }
        }
        return $query;
    }


    /* Get Device Model (dropdown) */
    public static function getDeviceModelDropdown($where = [], $fields = [], $limit = null)
    {
        $fields = [
            'device_models.id',
            'device_models.title',
        ];
        /* Show Device Model
            1 = Show under uninsured devices only,
            2 = Show under insured devices only,
            3 = Show everywhere
        */
        /* Not in uninsured devices */
        $where['whereNot']['device_models.show_device_model'] = 1;
        /** $where['where']['device_models.active'] = 1; */
        $data = self::getDeviceModelsQueryForDropdown($where, $fields)->orderBy('device_models.title', 'asc');
        /* If a numeric limit is provided and > 0, apply it */
        if (is_numeric($limit) && $limit > 0) {
            $data->limit($limit);
        }
        return $data->get();
    }
    /* get only uninsured and show everywhere devices  */
    public static function getUninsuredAndShowDevices($limit = null,$where = [], $fields = [])
    {
        /* Show Device Model
            1 = Show under uninsured devices only,
            2 = Show under insured devices only,
            3 = Show everywhere
        */
        $where['whereNot']['device_models.show_device_model'] = 2;
        $data = self::getDeviceModelsQueryForDropdown($where, $fields)->orderBy('device_models.title', 'asc');
        /* If a numeric limit is provided and > 0, apply it */
        if (is_numeric($limit) && $limit > 0) {
            $data->limit($limit);
        }
        return $data->get();
    }
    /** For Filter's */
    private function getFilter(Request $request)
    {
        $where = [];
        /** Search by device model title, family, brand , status and show model */
        if (!empty($request->title) || !empty($request->familyId) || !empty($request->brandId) || !empty($request->showModel)) {
            $where['where'] = [];

            /* If title is not empty */
            if (!empty($request->title)) {
                $where['where']['device_models.title'] = ['LIKE', "%{$request->title}%"];
            }
            /* If Device Family is not empty */
            if (!empty($request->familyId)) {
                $where['where']['device_models.device_family_id'] = $request->familyId;
            }
            /* If Device Brand is not empty */
            if (!empty($request->brandId)) {
                $where['where']['device_models.device_brand_id'] = $request->brandId;
            }
            /*  Show Device Model
                1 = Show under uninsured devices only,
                2 = Show under insured devices only,
                3 = Show everywhere
            */
            if (!empty($request->showModel)) {
                $where['where']['device_models.show_device_model'] = $request->showModel;
            }
        }
        return $where;
    }
    /**
     * Get Device Model (dropdown) according to organization & all
     */
    public function fetchDeviceModels(Request $request)
    {
        try {
            $serviceProvider = Session::get('service_provider');

            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string',
                'orgId' => 'nullable|integer|exists:organizations,id,active,1,service_provider_id,' . $serviceProvider,
                'deviceModelId' => 'nullable|integer|exists:device_models,id,active,1,service_provider_id,' . $serviceProvider,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $validator->errors()
                ], 200);
            }
            $baseFilter = [];
            /* Apply organization filter if orgId is provided */
            if (!empty($request->orgId)) {
                $allowedModelIds = OrgAllowedModel::where([
                    ['org_id', $request->orgId],
                    ['service_provider_id', $serviceProvider],
                    ['active', 1]
                ])->pluck('model_id')->unique()->toArray();

                if (empty($allowedModelIds)) {
                    return response()->json([
                        'success' => false,
                        'msg' => "No Device Models found for selected Organization.",
                        'modelTotal' => 0,
                        'deviceModels' => []
                    ], 200);
                }

                $baseFilter['whereIn']['device_models.id'] = $allowedModelIds;
            }
            /* Get total count (without name filter) */
            $totalModels = self::countDeviceModels($baseFilter);
            $deviceModels = collect();
            if ($totalModels == 0) {
                return response()->json(['success' => false, 'msg' => "No device models found.", 'modelTotal' => 0, 'deviceModels' => []], 200);
            } elseif ($totalModels <= 100) {
                $deviceModels = self::getDeviceModelDropdown($baseFilter, [], null);
            } else {
                // totalModels > 100 — only return 20 results max

                // 1. Specific model requested
                if (!empty($request->deviceModelId)) {
                    $baseFilter['where']['device_models.id'] = $request->deviceModelId;

                    // Get the requested model
                    $modelSelected = self::getDeviceModelDropdown($baseFilter, [], null);

                    // Remove that filter
                    unset($baseFilter['where']['device_models.id']);

                    // Get 19 more models (excluding the one above)
                    if (!$modelSelected->isEmpty()) {
                        $excludedId = $modelSelected->first()->id;
                        $baseFilter['whereNot']['device_models.id'] = $excludedId;
                    }

                    $additionalModels = self::getDeviceModelDropdown($baseFilter, [], 19);

                    $deviceModels = $modelSelected->merge($additionalModels);
                }
                // 2. Name filter applied (overrides everything)
                elseif (!empty($request->name)) {
                    $baseFilter['where']['device_models.title'] = ['LIKE', "%{$request->name}%"];
                    $deviceModels = self::getDeviceModelDropdown($baseFilter, [], 20);
                }
                // 3. No filters — just return 20
                else {
                    $deviceModels = self::getDeviceModelDropdown($baseFilter, [], 20);
                }
            }
            if ($deviceModels->isEmpty()) {
                return response()->json(['success' => false, 'msg' => "No Device Models found.", 'modelTotal' => $totalModels, 'deviceModels' => []], 200);
            }

            return response()->json(['success' => true, 'msg' => "Device Models.", 'deviceModels' => $deviceModels, 'modelTotal' => $totalModels], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => "Failed to get device models"], 200);
        }
    }
    /**
     *  count of insured and show everywhere device models
     */
    public static function countDeviceModels($where = [])
    {
        $where['whereNot']['device_models.show_device_model'] = 1;
        return self::getDeviceModelsQueryForDropdown($where, ['device_models.id'])->count();
    }

     /**
     * Get Device Model (dropdown) of uninsured and show everywhere type
     */
    public static function fetchUninsuredDeviceModels(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $validator->errors()
                ], 200);
            }
            /* Get total count (without name filter) */
            $totalModels = self::countUninsuredDeviceModels();
            if ($totalModels == 0) {
                return response()->json(['success' => false, 'msg' => "No device models found.", 'modelTotal' => 0, 'deviceModels' => []], 200);
            } elseif ($totalModels <= 100) {
                $deviceModels = self::getUninsuredAndShowDevices();
            } else {
                // totalModels > 100 — only return 20 results max
                $baseFilter = [];
                // 1. Name filter applied
                if (!empty($request->name)) {
                    $baseFilter['where']['device_models.title'] = ['LIKE', "%{$request->name}%"];
                }
                $deviceModels = self::getUninsuredAndShowDevices(20,$baseFilter);
            }
            if ($deviceModels->isEmpty()) {
                return response()->json(['success' => false, 'msg' => "No Device Models found.", 'modelTotal' => $totalModels, 'deviceModels' => []], 200);
            }

            return response()->json(['success' => true, 'msg' => "Device Models.", 'deviceModels' => $deviceModels, 'modelTotal' => $totalModels], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => "Failed to get device models"], 200);
        }
    }
    /**
     *  count of uninsured and show everywhere device models
     */
    public static function countUninsuredDeviceModels($where = [])
    {
        $where['whereNot']['device_models.show_device_model'] = 2;
        return self::getDeviceModelsQueryForDropdown($where)->count();
    }
}
