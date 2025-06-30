<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\DeviceImportCsv;
use App\Models\OrgRelationship;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use SebastianBergmann\CodeUnit\FunctionUnit;

class AdminImportDevicesController extends Controller
{
    /** Device list index function to get data */
    public function index(Request $request)
    {
        $where = $this->deviceImportListFilter($request);
        $deviceCsvData = self::getImportedDevicesCSVWithPaginate(20, $where);
        $pagination = [
            'total' => $deviceCsvData->total(),
            'per_page' => $deviceCsvData->perPage(),
            'current_page' => $deviceCsvData->currentPage(),
            'last_page' => $deviceCsvData->lastPage(),
            'from' => $deviceCsvData->firstItem(),
            'to' => $deviceCsvData->lastItem()
        ];
        if (!empty($request->page)) {
            return response()->json(["deviceCsvData" => $deviceCsvData, "pagination" => $pagination, "msg" => "Paginated records here.", "success" => true], 200);
        } else {
            return view('admin.importdevicemaster.index', compact('deviceCsvData', 'pagination'));
        }
    }

    /** Store Csv File in DB */
    public function store(Request $request)
    {
        try {
            $serviceProvider = Session::get('service_provider');

            $validator = Validator::make($request->all(), [
                'file' => [
                    'required',
                    // Custom validation rule to check file size and MIME type
                    function ($attribute, $value, $fail) {
                        // Check if the file is empty
                        if ($value->getSize() == 0) {
                            $fail('The uploaded CSV file is empty.');
                        }
                        // Check if the file is a valid CSV by MIME type
                        $mimeType = $value->getClientMimeType();
                        if ($mimeType !== 'text/csv' && $mimeType !== 'application/vnd.ms-excel') {
                            $fail('The selected file must be a CSV file.');
                        }
                    },
                ],
                'deviceExp' => 'required|date_format:Y-m-d',
                'users' => 'required|exists:users,id,active,1,service_provider_id,' . $serviceProvider,
                'selectedDeviceModel' => 'required|exists:device_models,id,active,1,service_provider_id,' . $serviceProvider,
                'organization' => 'required|exists:organizations,id,active,1,service_provider_id,' . $serviceProvider,
                'subOrgId' => 'nullable|exists:sub_organizations,id,org_id,' . $request->orgId . ',active,1,service_provider_id,' . $serviceProvider,
            ], [
                'file.required' => 'Please select a file to import.',
                'file.mimes' => 'The selected file must be a CSV file.',
                'selectedDeviceModel.required' => 'Please select a device model.',
                'organization.required' => 'Please select an organization.',
                'subOrganization.required' => 'Please select a sub-organization.',
                'subOrganization.exists' => 'The selected sub-organization is invalid.',
                'deviceExp.required' => 'The expiration date is required.',
                'deviceExp.date_format' => 'The expiration date must be in the format YYYY-MM-DD.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $validator->errors()
                ], 200);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('device_csv', $fileName, 'public');

            /* $orgRelQuery = OrgRelationship::select('user_id')->where('is_org_subscriber', 0)->where('active', 1)->where('service_provider_id', $serviceProvider);

            $user = isset($request->organization) && empty($request->subOrganization) ? $orgRelQuery->where('org_id', $request->organization)->whereNull('parent_org_id')->first() : $orgRelQuery->where('org_id', $request->subOrganization)->where('parent_org_id', $request->organization)->first();

            if (empty($user)) {
                return response()->json([
                    "msg" => "IT HOD or Director not found.",
                    "rcode" => 1,
                    "success" => false
                ], 200);
            } */

            $import = DeviceImportCsv::create([
                'file_name' => $fileName,
                'file_path' => $filePath,
                'user_id' => $request->users,
                'total' => 0,
                'inserted' => 0,
                'skipped' => 0,
                'updated' => 0,
                'active' => 1,
                'service_provider_id' => $serviceProvider
            ]);

            return response()->json([
                "msg" => "File uploaded successfully.",
                "success" => true,
                "import_id" => $import->id
            ], 200);
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong. Please try again.", "success" => false], 200);
        }
    }

    /** Live Progress and Processing */
    public function checkCsvImportProgress(Request $request, string $importId)
    {
        /* $where = [
            'where' => [
                'device_import_csv.id' => $importId
            ]
        ];
        $import = self::getDeviceCSVQuery($where)->first(); */
        $serviceProvider = Session::get('service_provider');
        $import = DeviceImportCsv::where('service_provider_id', $serviceProvider)->where('id', $importId)->where('active', 1)->first();
        if (empty($import)) {
            return response()->json(["msg" => "Import not found.", "success" => false], 200);
        }

        /** Get the File From storage  */
        $filePath = storage_path("app/public/" . $import->file_path);
        /* Check File Is exists or not */
        if (!file_exists($filePath)) {
            return response()->json(["msg" => "File not found.", "success" => false], 200);
        }
        /** Read the file */
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return response()->json(["msg" => "Unable to open file.", "success" => false], 200);
        }
        /** chunk size */
        $batchSize = 500;
        /** Getting Total Records from the file using countCsvRecords function */
        $totalRecords = $this->countCsvRecords($filePath);
        $currentLine = $import->total;
        for ($i = 0; $i <= $currentLine; $i++) {
            fgetcsv($handle);
        }

        $inserted = $import->inserted;
        $skipped = $import->skipped;
        $updated = $import->updated;
        $now = Carbon::now();
        /** User Full Name */
        $userFullName = User::select('full_name')->where('id', $import->user_id)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
        $deviceModelId = $request->selectedDeviceModel;
        $orgId = $request->organization;
        $subOrgId = $request->subOrganization;
        $deviceModelTitle = $request->deviceModelTitle;
        $deviceExp = $request->deviceExp;
        /** Get The Serial Numbers */
        $existingSerials = DB::table('devices')
            /*  ->where('device_model_id', $deviceModelId)
            ->where('user_id', $import->user_id)
            ->where('org_id', $orgId)
            ->orWhere('sub_org_id', $subOrgId) */
            ->where('service_provider_id', $serviceProvider)
            ->where('active', 1)
            ->pluck('id', 'serial_number')
            ->toArray();

        $processed = 0;


        while (($row = fgetcsv($handle, 0, ",")) !== false && $processed < $batchSize) {
            if (!is_array($row) || count(array_filter($row, fn($cell) => trim($cell) !== '')) == 0) {
                continue;
            }
            if (empty($row[0])) {
                $skipped++;
                $processed++;
                $currentLine++;
                continue;
            }

            $serial = trim($row[0]);
            $deviceTitle = $deviceModelTitle . ' (#' . $serial . ')';

            $deviceData = [
                'device_title' => $deviceTitle,
                'device_model_id' => $deviceModelId,
                'serial_number' => $serial,
                'device_type' => 2,
                'user_id' => $import->user_id,
                'org_id' => $orgId,
                'sub_org_id' => $subOrgId ?? null,
                'device_owner_name' => $userFullName->full_name ?? null,
                'is_imported_device' => 1,
                'service_provider_id' => $serviceProvider,
                'payment_added_date' => $now,
                'expiration_date' => Carbon::parse($deviceExp)->format('Y-m-d'),
                'updated_at' => $now
            ];

            if (isset($existingSerials[$serial])) {
                DB::table('devices')->where('id', $existingSerials[$serial])->update($deviceData);
                $updated++;
            } else {
                $deviceData['created_at'] = $now;
                DB::table('devices')->insert($deviceData);
                $inserted++;
            }

            $processed++;
            $currentLine++;
        }
        $done = feof($handle);
        fclose($handle);

        $import->update([
            'inserted' => $inserted,
            'skipped' => $skipped,
            'updated' => $updated,
            'total' => $currentLine,
        ]);

        return response()->json([
            "success" => true,
            "progress" => [
                'totalRecords' => $totalRecords,
                'total' => $currentLine,
                'inserted' => $inserted,
                'skipped' => $skipped,
                'updated' => $updated,
                'completed' => $done

            ]
        ], 200);
    }
    /**
     * Validate CSV Expiration Date
     */
    private function isValidExpDate($date, $format = 'Y-m-d')
    {
        $dt = \DateTime::createFromFormat($date, $format = 'Y-m-d');
        return $dt && $dt->format($format) == $date;
    }
    private function countCsvRecords($filePath)
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return 0;
        }
        $lineCount = 0;
        /* Assuming the first row is the header */
        fgetcsv($handle); /* Skip header */
        while (fgetcsv($handle) !== false) {
            $lineCount++;
        }
        fclose($handle);
        return $lineCount;
    }


    /** Device CSV Get Query */
    private static function getDeviceCSVQuery($where = [], $fields = [])
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $query = DB::table('device_import_csv')
            ->leftJoin('users as user', 'device_import_csv.user_id', '=', 'user.id')
            ->where('device_import_csv.service_provider_id', $serviceProvider)->where('device_import_csv.active', 1);
        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select(
                'device_import_csv.id',
                'device_import_csv.file_name',
                'device_import_csv.file_path',
                'device_import_csv.user_id',
                'device_import_csv.total',
                'device_import_csv.inserted',
                'device_import_csv.skipped',
                'device_import_csv.updated',
                'device_import_csv.created_at',
                'user.full_name as user_name',
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
                    } else {
                        /* For 'where' */
                        foreach ($value as $secondField => $secondValue) {
                            $query->$field($secondField, $secondValue);
                        }
                    }
                }
            }
        }
        $query->orderBy('created_at', 'desc');
        return $query;
    }

    /** Get Imported Devices CSV With Paginate  */
    public function getImportedDevicesCSVWithPaginate($limit = 20, $where = [], $fields = [])
    {
        $data = self::getDeviceCSVQuery($where, $fields);
        $limit = (is_numeric($limit) && $limit > 0) ? $limit : 20;
        return $data->paginate($limit);
    }


    /** Get Users for import devices */
    public function getUsers(Request $request)
    {
        try {
            $serviceProvider = Session::get('service_provider');
            $validator = Validator::make($request->all(), [
                'orgId' => 'required|exists:organizations,id,active,1,service_provider_id,' . $serviceProvider,
                'subOrgId' => 'nullable|exists:sub_organizations,id,org_id,' . $request->orgId . ',active,1,service_provider_id,' . $serviceProvider,
            ], [
                'orgId.required' => 'Please select an organization.',
                'orgId.exists' => 'The selected organization is invalid.',
                'subOrgId.required' => 'Please select a sub-organization.',
                'subOrgId.exists' => 'The selected sub-organization is invalid.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $validator->errors()
                ], 200);
            }

            /** Get Users */
            $fields = ['id'];
            $where = [
                'where' => [
                    'role_type' => 2,
                ],
                'whereIn' => [
                    'role_for' => ['is_org_it_hod', 'is_org_it_director'],
                ]
            ];
            $roles = RoleController::getAllUserRoles($where, $fields);
            if ($roles->isEmpty()) {
                return response()->json(['success' => false, 'msg' => "Role not found"], 404);
            }
            /* Get all role IDs */
            $roleIds = [];
            foreach ($roles as $value) {
                $roleIds[] = $value->id;  /* Collecting all role IDs */
            }
            $users = User::query()->select('id', 'full_name', 'email')->whereIn('role_id', $roleIds)->where('service_provider_id', $serviceProvider)->where('active', 1);
            if (!empty($request->orgId) && !empty($request->subOrgId)) {
                /* Sub-organization users */
                $org_relationship2 = OrgRelationship::select('user_id')->where('org_id', $request->subOrgId)->where('parent_org_id', $request->orgId)->where('is_org_subscriber', 0)->orWhere(
                    function ($query) use ($request) {
                        $query->where('org_id', $request->orgId)->whereNull('parent_org_id');
                    }
                )->pluck('user_id')->toArray();
                $users->whereIn('id', $org_relationship2);
            } else {
                /* Organization */
                $org_relationship = OrgRelationship::select('user_id')->where('org_id', $request->orgId)->whereNull('parent_org_id')->where('is_org_subscriber', 0)->pluck('user_id')->toArray();
                $users->whereIn('id', $org_relationship);
            }
            $users = $users->get();
            if ($users->isEmpty()) {
                return response()->json(['users' => $users, 'success' => false, 'msg' => "No users found"], 200);
            }
            $users = $users->map(function ($user) {
                $user->full_name = "{$user->full_name} ({$user->email})";  /* Combine full_name and email */
                return $user;
            });
            return response()->json(['success' => true, 'users' => $users], 200);
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong. Please try again.", "success" => false], 200);
        }
    }

    /** Filter */
    private function deviceImportListFilter(Request $request)
    {
        $where = [];
        if (!empty($request->search)) {
            if (!empty($request->search)) {
                $where = [
                    'orWhere' => [
                        'user.first_name' => ['LIKE', "%{$request->search}%"],
                        'user.last_name' => ['LIKE', "%{$request->search}%"],
                        'user.full_name' => ['LIKE', "%{$request->search}%"]
                    ]
                ];
            }
        }
        return $where;
    }

    /** Download Import Devices Csv */
    public function downloadImportDevicescsv(string $id)
    {
        try {
            if (!empty($id)) {
                $serviceProvider = Session::get('service_provider');
                $csvRecord = DeviceImportCsv::where('id', $id)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
                if (empty($csvRecord)) {
                    return response()->json([
                        "msg" => "Record not found",
                        "success" => false
                    ], 200);
                } else {

                    $relativePath = 'storage/' . $csvRecord->file_path;
                    /** File Path */
                    $fullPath = public_path($relativePath);
                    /** Full File Path */
                    if (!file_exists($fullPath)) {
                        return response()->json([
                            "msg" => "File not found.",
                            "success" => false
                        ], 200);
                    }
                    /* Return the file as a download */
                    return response()->download($fullPath, 'imported_devices.csv');
                }
            } else {
                return response()->json(["msg" => "Something went wrong. Please try again.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong. Please try again.", "success" => false], 200);
        }
    }
}
