<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\MediaLibraryController;
use App\Http\Controllers\Controller;

use App\Models\Admin\DeviceBrand;
use App\Models\Admin\DeviceFamily;
use App\Models\Admin\DeviceModel;
use App\Models\Admin\MediaLibraries;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DeviceFamilyController extends Controller
{
    /** Display a listing of the resource. */
    public function index(Request $request)
    {
        $where = $this->getFilter($request);
        $familyData = self::paginateDeviceFamilies(20, $where);
        $pagination = [
            'total' => $familyData->total(),
            'per_page' => $familyData->perPage(),
            'current_page' => $familyData->currentPage(),
            'last_page' => $familyData->lastPage(),
            'from' => $familyData->firstItem(),
            'to' => $familyData->lastItem()
        ];
        if (!empty($request->page)) {
            return response()->json(["familyData" => $familyData, "pagination" => $pagination, "msg" => " ", "success" => true], 200);
        } else {
            return view('admin.devicefamilymaster.index', compact('familyData', 'pagination'));
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
                'name' => 'required|string|max:50|unique:device_families,name,Null,id,active,1,service_provider_id,' . $serviceProvider,
                'image' => 'nullable|image|mimes:jpg,png,jpeg,svg|max:2048',
                'description' => 'nullable|string|max:150',
                'remove_image' => 'nullable|boolean', /* New field to handle image removal */
            ], [
                'name.required' => "Device family name can't be empty.",
                'name.unique' => "Device family name must be unique",
                'image.image' => "The selected file must be an image.",
                'image.mimes' => "The selected file must be a JPG, PNG, JPEG, or SVG image.",
                'image.max' => "The image file size must not exceed 2MB.",
                'description.max' => "Description must not exceed 150 characters.",
            ]);
            /** Check if validation fails */
            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $validator->errors()
                ], 200);
            }
            $imageName = null;
            $mediaId = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                /* Check if the image is valid */
                if (empty($image)) {
                    return response()->json(["msg" => "No image provided.", "success" => false], 200);
                }
                /* Generate a unique file name */
                $imageExtension = $image->getClientOriginalExtension();
                $imageName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '-' . substr(str_shuffle("0123456789"), 0, 4) . '.' . $imageExtension;
                /* Failed to generate unique file name.*/
                if (empty($imageName)) {
                    return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                }
                /* Store the file in storage/app/public/device_families */
                $imagePath = $image->storeAs('device_families', $imageName, 'public');
                if (empty($imagePath)) {
                    return response()->json(["msg" => "Failed to upload image.", "success" => false], 200);
                }
                /* create and get (id) media library */
                $mediaRequest = [
                    'fileName' => $imageName,
                    'filePath' => '/storage/device_families/',
                    'fileType' => $imageExtension,

                ];
                $mediaLibrary = MediaLibraryController::createMediaLibrary($mediaRequest);
                /* Failed to create media library record */
                if (empty($mediaLibrary)) {
                    return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                }
                $mediaId = $mediaLibrary;
            } elseif ($request->has('remove_image') && $request->remove_image) {
                /* If the 'remove_image' flag is set to true, remove the image */
                $imageName = null;
            }

            /** Create a new DeviceFamily record */
            $deviceFamily = DeviceFamily::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name, '-'),
                'description' => $request->description ?? null,
                'media_id' => $mediaId,
                'service_provider_id' => $serviceProvider,
            ]);
            if ($deviceFamily->wasRecentlyCreated) {
                return response()->json([
                    "msg" => "Device family created successfully.",
                    "success" => true,
                ], 200);
            } else {
                return response()->json(["msg" => "Device family creation failed.", "success" => false], 200);
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
                    'device_families.id' => $id,
                ]
            ];
            $editData = self::getDeviceFamilyQuery($where)->first();
            if (!empty($editData)) {
                return response()->json(["editData" => $editData, "msg" => "Edit Device Family Data.", "success" => true], 200);
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
                'id' => 'required|exists:device_families,id,active,1,service_provider_id,' . $serviceProvider,
                'name' => 'required|string|max:50|unique:device_families,name,' . $request->id . ',id,active,1,service_provider_id,' . $serviceProvider,
                'image' => 'nullable|image|mimes:jpg,png,jpeg,svg|max:2048',
                'description' => 'nullable|string|max:150',
                'remove_image' => 'nullable|boolean', /* New field to handle image removal */
            ], [
                'id.required' => "Device family ID can't be empty.",
                'id.exists' => "Device family not found.",
                'name.required' => "Device family name can't be empty.",
                'name.unique' => "Device family name must be unique",
                'image.image' => "The selected file must be an image.",
                'image.mimes' => "The selected file must be a JPG, PNG, JPEG, or SVG image.",
                'image.max' => "The image file size must not exceed 2MB.",
                'description.max' => "Description must not exceed 150 characters.",
            ]);
            /* Check if validation fails */
            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $validator->errors()
                ], 200);
            }
            $mediaId = null;
            /* Find the device family */
            $deviceFamily = DeviceFamily::where('id', $request->id)->where('service_provider_id', $serviceProvider)->where('active', 1)->first();
            /* Device Family data not found */
            if (empty($deviceFamily)) {
                return response()->json(["msg" => "Device family not found.", "success" => false], 200);
            }
            /* Handle image upload */
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                if (empty($image)) {
                    return response()->json(["msg" => "No image provided.", "success" => false], 200);
                }
                $imageExtension = $image->getClientOriginalExtension();
                $newImageName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '-' . substr(str_shuffle("0123456789"), 0, 4) . '.' .  $imageExtension; /* Keep original file name */
                if (empty($newImageName)) {
                    return response()->json(["msg" => "Failed to upload image.", "success" => false], 200);
                }
                /* create and get(id) media library */
                $mediaRequest = [
                    'fileName' => $newImageName,
                    'filePath' => '/storage/device_families/',
                    'fileType' => $imageExtension,

                ];
                $mediaLibrary = MediaLibraryController::createMediaLibrary($mediaRequest);
                /* Failed to create media library record */
                if (empty($mediaLibrary)) {
                    return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                }
                $mediaId = $mediaLibrary;
                /* Store the new image using storeAs() */
                $image->storeAs('device_families', $newImageName, 'public');
                /* Update database record with new media id */
                $deviceFamily->media_id = $mediaId;
            } elseif ($request->has('remove_image') && $request->remove_image) {
                /* If the 'remove_image' flag is set to true, remove the image */
                $deviceFamily->media_id = null;
            }
            /* Update device family data */
            $deviceFamily->name = $request->name;
            $deviceFamily->slug = Str::slug($request->name, '-');
            $deviceFamily->description = $request->description;

            if ($deviceFamily->save()) {
                return response()->json(["msg" => "Device family updated successfully.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Device family update failed.", "success" => false], 200);
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
            /* Find the device family */
            $deviceFamily = DeviceFamily::where('id', $id)->where('service_provider_id', $serviceProvider)->first();
            if (!empty($deviceFamily)) {
                /* Toggle active status */
                $deviceFamily->active = $deviceFamily->active == 1 ? 0 : 1;
                $deviceFamily->save();

                /* Set the same active status for related device brands and models */
                DeviceBrand::where('device_family_id', $id)
                    ->where('service_provider_id', $serviceProvider)
                    ->update(['active' => $deviceFamily->active]);

                DeviceModel::where('device_family_id', $id)
                    ->where('service_provider_id', $serviceProvider)
                    ->update(['active' => $deviceFamily->active]);

                /* Dynamic message based on new active status */
                $message = $deviceFamily->active == 1 ? 'Activated' : 'Deactivated';
                return response()->json(["msg" => "Device Family {$message} successfully.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Device Family not found.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Get device family query */
    private static function getDeviceFamilyQuery($where = [], $fields = [])
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $query = DB::table('device_families')
            ->leftJoin('media_libraries as media', 'device_families.media_id', '=', 'media.id')
            ->where('device_families.service_provider_id', $serviceProvider)
            ->where('device_families.active', 1);
            /* ->where(function ($query) use ($serviceProvider) {
                $query->whereNull('device_families.media_id')
                    ->orWhere(function ($query) use ($serviceProvider) {
                        $query->orWhere('media.active', 1)->orWhere('media.service_provider_id', $serviceProvider);
                    });
            }); */
        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select(
                'device_families.id',
                'device_families.name',
                'device_families.slug',
                'device_families.description',
                'device_families.media_id',
                'device_families.active',
                'media.file_name',
                'media.file_path'
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

    /** Get all the device family(paginate). */
    public static function paginateDeviceFamilies($limit = 20, $where = [], $fields = [],)
    {
        $data = self::getDeviceFamilyQuery($where, $fields)->orderBy('device_families.name', 'asc');
        $limit = (is_numeric($limit) && $limit > 0) ? $limit : 20;
        return $data->paginate($limit);
    }

    /** Get all device family */
    public static function getAllDeviceFamilies($where = [], $fields = [])
    {
        $query = self::getDeviceFamilyQuery($where, $fields)->orderBy('device_families.name', 'asc');
        return $query->get();
    }
    /** Get Device Families for drop-down */
    public static function getDeviceFamiliesDropdown($where = [], $fields = [])
    {
        $fields = ['device_families.id', 'device_families.name'];
        $query = self::getDeviceFamilyQuery($where, $fields)->orderBy('device_families.name', 'asc');
        return $query->get();
    }
    /** For Filter's */
    private function getFilter(Request $request)
    {
        $where = [];
        /** Search by device family name and status */
        if (!empty($request->name) || !is_null($request->status)) {
            $where['where'] = [];

            /* If name is not empty */
            if (!empty($request->name)) {
                $where['where']['device_families.name'] = ['LIKE', "%{$request->name}%"];
            }
            /**
             * If status is not empty
             * 1 = active
             * 0 = deactive
             * */
            if (!is_null($request->status)) {
                $where['where']['device_families.active'] = ['=', $request->status];
            }
        }
        return $where;
    }
}
