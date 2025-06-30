<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\ClaimReasonController;
use App\Http\Controllers\Admin\DeviceModelController;
use App\Http\Controllers\Admin\DevicePlanController;
use App\Http\Controllers\Admin\MediaLibraryController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SDCOptionController;
use App\Http\Controllers\Controller;

use App\Models\Admin\Device;
use App\Models\Admin\OrgAllowedModel;
use App\Models\Admin\OrgAllowedRenewalModel;
use App\Models\Admin\Organization;
use App\Models\Admin\OrganizationClaimReason;
use App\Models\Admin\SubOrganization;
use App\Models\OrgRelationship;
use App\Models\User\DeviceClaim;
use App\Models\User\DeviceRepair;
use App\Models\User\ShippingSupply;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;

class OrganizationController extends Controller
{
    /** Display a listing of the organizations. */
    public function index(Request $request)
    {
        /** Filter Organization */
        $where = $this->getFilter($request);
        $orgData = self::getPaginatedOrganizations(20, $where);
        $totalOrgs = $this->totalOrgs();
        $pagination = [
            'total' => $orgData->total(),
            'per_page' => $orgData->perPage(),
            'current_page' => $orgData->currentPage(),
            'last_page' => $orgData->lastPage(),
            'from' => $orgData->firstItem(),
            'to' => $orgData->lastItem()
        ];
        if (!empty($request->page)) {
            return response()->json(["organizations" => $orgData, "pagination" => $pagination, "msg" => "Paginated records here.", "success" => true], 200);
        } else {
            return view('admin.organizationmaster.index', compact('orgData', 'pagination', 'totalOrgs'));
        }
    }

    /** Show the form for creating a new oerganization. */
    public function createOrganization()
    {
        /** Calling common function from claim reason controller to get reasons */
        $claimReasons = ClaimReasonController::getClaimReasonsDropdown();
        /** Calling common function from device model controller to get the model list with selected data */
        $deviceModels = DeviceModelController::getDeviceModelDropdown();
        return view('admin.organizationmaster.createorganization', compact('claimReasons', 'deviceModels'));
    }

    /** Show organization details */
    public function showOrganization(string $id)
    {
        $request_id = $id;
        if (!empty($request_id)) {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            $orgFields = [
                'org.id',
                'org.name',
                'org.repair_enabled',
                'org.can_edit_device',
                'org.agreement_link',
                'org.allow_parents_claim',
                'org.portal_status',
                'org.close_portal_message',
                'org.enable_multiple_sub_org',
                'org.additional_instructions',
                'org.org_type',
                'logoMedia.file_name as logo_file_name',
                'logoMedia.file_path as logo_file_path',
                'coverMedia.file_name as cover_file_name',
                'coverMedia.file_path as cover_file_path',
                'pdfMedia.file_name as pdf_file_name',
                'pdfMedia.file_path as pdf_file_path',
            ];
            $subOrgs = null;
            $claims = null;
            $query = DB::table('organizations as org')
                ->leftJoin('media_libraries as logoMedia', 'org.org_logo_media_id', '=', 'logoMedia.id')
                ->leftJoin('media_libraries as coverMedia', 'org.cover_image_media_id', '=', 'coverMedia.id')
                ->leftJoin('media_libraries as pdfMedia', 'org.service_agreement_media_id', '=', 'pdfMedia.id')
                ->select($orgFields)
                ->where('org.id', $request_id)
                ->where('org.active', 1)
                ->where('org.service_provider_id', $serviceProvider)
                ->where(function ($query) use ($serviceProvider) {
                    $query->whereNull('org.org_logo_media_id')
                        ->orWhere(function ($query) use ($serviceProvider) {
                            $query->where('logoMedia.service_provider_id', $serviceProvider);
                        });
                })
                ->where(function ($query) use ($serviceProvider) {
                    $query->whereNull('org.cover_image_media_id')
                        ->orWhere(function ($query) use ($serviceProvider) {
                            $query->where('coverMedia.service_provider_id', $serviceProvider);
                        });
                })
                ->where(function ($query) use ($serviceProvider) {
                    $query->whereNull('org.service_agreement_media_id')
                        ->orWhere(function ($query) use ($serviceProvider) {
                            $query->where('pdfMedia.service_provider_id', $serviceProvider);
                        });
                })
                ->first();
            if (!empty($query)) {
                $subOrgWhere['where']['org_id'] = $query->id;
                $subOrgData = self::getSubOrganizationQuery($subOrgWhere)->pluck('name');
                $subOrgs = $subOrgData;
                /** Allowed Models */
                $allowedModelFields = [
                    'orgAllowedModels.id',
                    'orgAllowedModels.coverage_price as allowed_coverage_price',
                    'orgAllowedModels.deductible as allowed_deductible_price',
                    'orgAllowedModels.expiration_date as allowed_expiration_date',
                    'allowedModels.title as allowed_model_name',
                    'allowedPlan.plan_name as allowed_plan_name',
                ];
                $allowedModels = DB::table('org_allowed_models as orgAllowedModels')
                    ->leftJoin('device_models as allowedModels', 'orgAllowedModels.model_id', '=', 'allowedModels.id')
                    ->leftJoin('device_plans as allowedPlan', 'orgAllowedModels.device_plan_id', '=', 'allowedPlan.id')
                    ->select($allowedModelFields)
                    ->where('orgAllowedModels.org_id', $query->id)
                    ->where('orgAllowedModels.service_provider_id', $serviceProvider)
                    ->where('allowedModels.service_provider_id', $serviceProvider)
                    ->where('allowedPlan.service_provider_id', $serviceProvider)
                    ->get()->toArray();
                /** Allowed Renewal Models */
                $allowedRenewalModelFields = [
                    'orgAllowedRenewalModels.id',
                    'orgAllowedRenewalModels.coverage_price as allowed_renewal_coverage_price',
                    'orgAllowedRenewalModels.deductible as allowed_renewal_deductible_price',
                    'orgAllowedRenewalModels.expiration_date as allowed_renewal_expiration_date',
                    'allowedRenewalModels.title as allowed_renewal_model_name',
                    'allowedRenewalPlan.plan_name as allowed_renewal_plan_name',
                ];
                $allowedRenewalModels = DB::table('org_allowed_renewal_models as orgAllowedRenewalModels')
                    ->leftJoin('device_models as allowedRenewalModels', 'orgAllowedRenewalModels.model_id', '=', 'allowedRenewalModels.id')
                    ->leftJoin('device_plans as allowedRenewalPlan', 'orgAllowedRenewalModels.device_plan_id', '=', 'allowedRenewalPlan.id')
                    ->select($allowedRenewalModelFields)
                    ->where('orgAllowedRenewalModels.org_id', $query->id)
                    ->where('orgAllowedRenewalModels.service_provider_id', $serviceProvider)
                    ->where('allowedRenewalModels.service_provider_id', $serviceProvider)
                    ->where('allowedRenewalPlan.service_provider_id', $serviceProvider)
                    ->get()->toArray();
                $orgClaims = DB::table('organization_claim_reasons as orgClaimReason')
                    ->leftJoin('claim_reasons as claimReason', 'orgClaimReason.claim_reason_id', '=', 'claimReason.id')
                    ->select('claimReason.claim_reason_name')
                    ->where('orgClaimReason.org_id', $query->id)
                    ->where('orgClaimReason.service_provider_id', $serviceProvider)
                    ->where('claimReason.service_provider_id', $serviceProvider)
                    ->pluck('claimReason.claim_reason_name')
                    ->join(', ');
                $claims = $orgClaims;
                $viewData = (object) [
                    'id' => $query->id,
                    'org_name' => $query->name,
                    'logo_file_name' => optional($query)->logo_file_name,
                    'logo_file_path' => optional($query)->logo_file_path,
                    'cover_file_name' => optional($query)->cover_file_name,
                    'cover_file_path' => optional($query)->cover_file_path,
                    'pdf_file_name' => optional($query)->pdf_file_name,
                    'pdf_file_path' => optional($query)->pdf_file_path,
                    'repair_enabled' => $query->repair_enabled,
                    'can_edit_device' => $query->can_edit_device,
                    'agreement_link' => $query->agreement_link,
                    'subOrg' => $subOrgs,
                    'allow_parents_claim' => $query->allow_parents_claim,
                    'org_type' => $query->org_type,
                    'portal_status' => $query->portal_status,
                    'close_portal_message' => $query->close_portal_message,
                    'enable_multiple_sub_org' => $query->enable_multiple_sub_org,
                    'additional_instructions' => $query->additional_instructions,
                    'allowed_models' => $allowedModels,
                    'allowed_renewal_models' => $allowedRenewalModels,
                    'claim_reasons' => $claims
                ];
                return response()->json(['viewData' => $viewData, "msg" => "Organization Data.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "No organization found.", "success" => false], 200);
            }
        } else {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Store a newly created resource in storage. */
    public function storeOrgData(Request $request)
    {
        try {
            $org_id = $request->org_id;
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate to the requests */
            $validator = Validator::make($request->all(), [
                'org_name' => 'required_if:form_step,1|max:50|unique:organizations,name,' . $org_id . ',id,active,1,service_provider_id,' . $serviceProvider,
                'org_logo' => 'image|mimes:jpg,png,jpeg,svg|max:2048',
                'org_cover_image' => 'image|mimes:jpg,png,jpeg,svg|max:2048',
                'org_service_agreement' => 'required_if:form_step,1|in:pdf,link,none',
                'org_agreement_pdf' => 'required_if:org_service_agreement,pdf|mimes:pdf|max:5120',
                'org_agreement_link' => 'required_if:org_service_agreement,link|nullable',
                'enable_multiple_sub_org' => 'required_if:form_step,2|in:0,1',
                'organiations_sub_org' => 'required_if:enable_multiple_sub_org,1|array',

                // 'organiations_sub_org.*.subOrgName' => 'required|string|max:50|unique:sub_organizations,name,Null,id,active,1,service_provider_id,' . $serviceProvider . ',org_id,' . $org_id,
                'organiations_sub_org.*.subOrgName' => 'required|string|max:50',

                // 'organiation_allowed_devices' => 'required_if:form_step,3|array',
                'organiation_allowed_devices' => 'nullable|array',

                'organiation_allowed_devices.*.deviceModelId' => 'required|integer|exists:device_models,id,active,1,service_provider_id,' . $serviceProvider,
                'organiation_allowed_devices.*.planId' => 'required|integer|exists:device_plans,id,active,1,service_provider_id,' . $serviceProvider,
                'organiation_allowed_devices.*.coverage_price' => 'required|numeric|min:0',
                'organiation_allowed_devices.*.deductible' => 'nullable|numeric|min:0',
                'organiation_allowed_devices.*.expiration_date' => 'required|date|date_format:Y-m-d',

                'organiation_allowed_renewal_devices' => 'nullable|array',

                'organiation_allowed_renewal_devices.*.deviceModelId' => 'required|integer|exists:device_models,id,active,1,service_provider_id,' . $serviceProvider,
                'organiation_allowed_renewal_devices.*.planId' => 'required|integer|exists:device_plans,id,device_model_id,organiations_allowed_renewal_devices.*.deviceModelId,active,1,service_provider_id,' . $serviceProvider,
                'organiation_allowed_renewal_devices.*.coverage_price' => 'required|numeric|min:0',
                'organiation_allowed_renewal_devices.*.deductible' => 'nullable|numeric|min:0',
                'organiation_allowed_renewal_devices.*.expiration_date' => 'required|date|date_format:Y-m-d',

                'org_claim_reasons' => 'required_if:form_step,4|array',
                'org_claim_reasons.*.reasonId' => 'required|integer|exists:claim_reasons,id,active,1,service_provider_id,' . $serviceProvider,

                'org_type' => 'required_if:form_step,4|integer|in:1,2',

                'org_portal_status' => 'required_if:form_step,4|in:0,1',
                'org_close_portal_message' => 'required_if:org_portal_status,0|string|max:250',

                'repair_enabled' => 'required_if:form_step,5|in:0,1',
                'allow_parents_claim' => 'required_if:form_step,5|in:0,1',
                'can_edit_device' => 'required_if:form_step,5|in:0,1',
                'additional_instructions' => 'nullable|string|max:500',

            ], [
                'org_name.required_if' => "Organization name can't be empty.",
                'org_name.max' => "Organization name should not exceed 50 characters.",
                'org_name.unique' => "Organization name already exists.",
                'org_logo.image' => "Please select a valid image file (jpg, png, jpeg, svg).",
                'org_logo.mimes' => "Only jpg, png, jpeg, svg files are allowed.",
                'org_logo.max' => "Logo file size should not exceed 2MB.",
                'org_cover_image.image' => "Please select a valid image file (jpg, png, jpeg, svg).",
                'org_cover_image.mimes' => "Only jpg, png, jpeg, svg files are allowed.",
                'org_cover_image.max' => "Cover image file size should not exceed 2MB.",

                'org_service_agreement.required_if' => "Service agreement is required.",
                'org_service_agreement.in' => "Service agreement must be link or pdf.",

                'org_agreement_pdf.required_if' => "Service agreement pdf is required.",
                'org_agreement_pdf.mimes' => "Only pdf files are allowed.",
                'org_agreement_pdf.max' => "Service agreement pdf file size should not exceed 5MB.",

                'org_agreement_link.required_if' => "Service agreement link is required.",
                'org_agreement_link.url' => "Please enter a valid URL for agreement link.",

                'enable_multiple_sub_org.required_if' => "Enable multiple sub-organization is required.",
                'enable_multiple_sub_org.required' => "Enable multiple sub-organization is required.",
                'enable_multiple_sub_org.in' => "Enable multiple sub-organization must be 0 or 1.",

                'organiations_sub_org.required_if' => "Sub-organization data is required.",
                'organiations_sub_org.*.subOrgName.required' => "Sub-organization name is required.",
                'organiations_sub_org.*.subOrgName.max' => "Sub-organization name should not exceed 50 characters.",
                'organiations_sub_org.*.subOrgName.unique' => "Sub-organization name already exists.",

                'organiation_allowed_devices.required_if' => "Enter at least one allowed device info.",
                'organiation_allowed_devices.*.deviceModelId.required' => "Device model is required.",
                'organiation_allowed_devices.*.deviceModelId.integer' => "Device model must be an integer.",
                'organiation_allowed_devices.*.deviceModelId.exists' => "Device model not found.",
                'organiation_allowed_devices.*.planId.required' => "Plan is required.",
                'organiation_allowed_devices.*.planId.integer' => "Plan must be an integer.",
                'organiation_allowed_devices.*.planId.exists' => "Plan not found.",
                'organiation_allowed_devices.*.coverage_price.required' => "Coverage price is required.",
                'organiation_allowed_devices.*.coverage_price.numeric' => "Coverage price must be a number.",
                'organiation_allowed_devices.*.deductible.numeric' => "Deductible must be a number.",
                'organiation_allowed_devices.*.expiration_date.required' => "Expiration date is required.",
                'organiation_allowed_devices.*.expiration_date.date' => "Expiration date must be a valid date.",

                'organiation_allowed_renewal_devices.*.deviceModelId.exists' => "Device model not found.",
                'organiation_allowed_renewal_devices.*.planId.exists' => "Plan not found.",
                'organiation_allowed_renewal_devices.*.coverage_price.required' => "Coverage price is required.",
                'organiation_allowed_renewal_devices.*.coverage_price.numeric' => "Coverage price must be a number.",
                'organiation_allowed_renewal_devices.*.deductible.numeric' => "Deductible must be a number.",
                'organiation_allowed_renewal_devices.*.expiration_date.required' => "Expiration date is required.",
                'organiation_allowed_renewal_devices.*.expiration_date.date' => "Expiration date must be a valid date.",

                'org_claim_reasons.required_if' => "Claim reason is required.",
                'org_claim_reasons.*.reasonId.required' => "Claim reason is required.",
                'org_claim_reasons.*.reasonId.integer' => "Claim reason must be an number.",
                'org_claim_reasons.*.reasonId.exists' => "Claim reason not found.",

                'org_type.required' => "Please select organization type.",
                'org_portal_status.required_if' => "Portal status is required.",
                'org_portal_status.in' => "Portal status must be 0 or 1.",
                'org_close_portal_message.required_if' => "Closed portal message is required.",
                'org_close_portal_message.string' => "Closed portal message must be a string.",
                'org_close_portal_message.max' => "Closed portal message should not exceed 250 characters.",
                'repair_enabled.required_if' => "Repair enable status is required.",
                'repair_enabled.in' => "Repair enable status must be 0 or 1.",
                'allow_parents_claim.required_if' => "Allow parent claim status is required.",
                'allow_parents_claim.in' => "Allow parent claim status must be 0 or 1.",
                'can_edit_device.required_if' => "Edit device permission status is required.",
                'can_edit_device.in' => "Edit device permission status must be 0 or 1.",
                'additional_instructions.string' => "Additional instruction must be a string.",
                'additional_instructions.max' => "Additional instruction should not exceed 500 characters.",

            ]);
            /* If Validation Fails */
            $errors = [];
            $allowedDeviceCombos = [];
            foreach ($request->input('organiation_allowed_devices', []) as $index => $device) {
                $comboKey = $device['deviceModelId'] . '_' . $device['planId'];
                if (isset($allowedDeviceCombos[$comboKey])) {
                    $errors["organiation_allowed_devices.$index.planId"] =
                        "This plan has already been assigned to this device.";
                } else {
                    $allowedDeviceCombos[$comboKey] = true;
                }
            }

            $renewalDeviceCombos = [];
            foreach ($request->input('organiation_allowed_renewal_devices', []) as $index => $device) {
                $comboKey = $device['deviceModelId'] . '_' . $device['planId'];
                if (isset($renewalDeviceCombos[$comboKey])) {
                    $errors["organiation_allowed_renewal_devices.$index.planId"] =
                        "This plan has already been assigned to this device for renewal.";
                } else {
                    $renewalDeviceCombos[$comboKey] = true;
                }
            }

            if ($validator->fails() || !empty($errors)) {
                $allErrors = $validator->errors()->toArray();
                foreach ($errors as $key => $error) {
                    $allErrors[$key] = [$error];
                }
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $allErrors
                ], 200);
            }
            /** The form steps */
            if ($request->form_step) {
                /** Inserting form step value in $current_step */
                $current_step = $request->form_step;

                /** Executing first step */
                $org_slug = '';
                if ($current_step == 1) {

                    $org_name = $request->org_name;
                    $org_agreement_link = $request->org_agreement_link;
                    /* Organization Logo */
                    $logoImageName = null;
                    $logoMediaId = null;
                    if ($request->hasFile('org_logo')) {
                        $logoImage = $request->file('org_logo');
                        /* Check if the image is valid */
                        if (empty($logoImage)) {
                            return response()->json(["msg" => "No image provided.", "success" => false], 200);
                        }
                        /* Generate a unique file(logo) name */
                        $logoImageExtension = $logoImage->getClientOriginalExtension();
                        $logoImageName = pathinfo($logoImage->getClientOriginalName(), PATHINFO_FILENAME) . '-' . substr(str_shuffle("0123456789"), 0, 4) . '.' . $logoImageExtension;
                        /* Failed to generate unique file(logo) name.*/
                        if (empty($logoImageName)) {
                            return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                        }
                        /* Store the file in storage/app/public/organizations/logo */
                        $logoImagePath = $logoImage->storeAs('organizations/logo', $logoImageName, 'public');
                        if (empty($logoImagePath)) {
                            return response()->json(["msg" => "Failed to upload image.", "success" => false], 200);
                        }
                        /* create and get (id) media library for logo */
                        $mediaRequest = [
                            'fileName' => $logoImageName,
                            'filePath' => '/storage/organizations/logo/',
                            'fileType' => $logoImageExtension,

                        ];
                        $mediaLibrary = MediaLibraryController::createMediaLibrary($mediaRequest);
                        /* Failed to create media library record for logo */
                        if (empty($mediaLibrary)) {
                            return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                        }
                        $logoMediaId = $mediaLibrary;
                    }
                    /* Organization Logo end */

                    /* Cover Image */
                    $coverImageName = null;
                    $coverMediaId = null;
                    if ($request->hasFile('org_cover_image')) {
                        $coverageImage = $request->file('org_cover_image');
                        /* Check if the image is valid */
                        if (empty($coverageImage)) {
                            return response()->json(["msg" => "No image provided.", "success" => false], 200);
                        }
                        /* Generate a unique file(cover image) name */
                        $coverImageExtension = $coverageImage->getClientOriginalExtension();
                        $coverImageName = pathinfo($coverageImage->getClientOriginalName(), PATHINFO_FILENAME) . '-' . substr(str_shuffle("0123456789"), 0, 4) . '.' . $coverImageExtension;
                        /* Failed to generate unique file(cover image) name.*/
                        if (empty($coverImageName)) {
                            return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                        }
                        /* Store the file in storage/app/public/organizations/cover_image */
                        $coverageImagePath = $coverageImage->storeAs('organizations/cover_image', $coverImageName, 'public');
                        if (empty($coverageImagePath)) {
                            return response()->json(["msg" => "Failed to upload image.", "success" => false], 200);
                        }
                        /* create and get (id) media library for cover image */
                        $coverMediaRequest = [
                            'fileName' => $coverImageName,
                            'filePath' => '/storage/organizations/cover_image/',
                            'fileType' => $coverImageExtension,

                        ];
                        $coverMediaLibrary = MediaLibraryController::createMediaLibrary($coverMediaRequest);
                        /* Failed to create media library record for cover image */
                        if (empty($coverMediaLibrary)) {
                            return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                        }
                        $coverMediaId = $coverMediaLibrary;
                    }
                    /* Cover Image end */

                    /* Service Agreement */
                    $serviceAgreeName = null;
                    $serviceAgreeId = null;
                    if ($request->org_service_agreement == 'pdf') {
                        if ($request->hasFile('org_agreement_pdf')) {
                            $serviceAgreePdf = $request->file('org_agreement_pdf');
                            /* Check if the image is valid */
                            if (empty($serviceAgreePdf)) {
                                return response()->json(["msg" => "No PDF provided.", "success" => false], 200);
                            }
                            /* Generate a unique file(service agreement) name */
                            $serviceAgreeExtension = $serviceAgreePdf->getClientOriginalExtension();
                            $serviceAgreeName = pathinfo($serviceAgreePdf->getClientOriginalName(), PATHINFO_FILENAME) . '-' . substr(str_shuffle("0123456789"), 0, 4) . '.' . $serviceAgreeExtension;
                            /* Failed to generate unique file(service agreement) name.*/
                            if (empty($serviceAgreeName)) {
                                return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                            }
                            /* Store the file in storage/app/public/organizations/service_agreement */
                            $serviceAgreePdfPath = $serviceAgreePdf->storeAs('organizations/service_agreement', $serviceAgreeName, 'public');
                            if (empty($serviceAgreePdfPath)) {
                                return response()->json(["msg" => "Failed to upload pdf.", "success" => false], 200);
                            }
                            /* create and get (id) media library for service agreement */
                            $serviceAgreeMediaRequest = [
                                'fileName' => $serviceAgreeName,
                                'filePath' => '/storage/organizations/service_agreement/',
                                'fileType' => $serviceAgreeExtension,

                            ];
                            $serviceAgreeMediaLibrary = MediaLibraryController::createMediaLibrary($serviceAgreeMediaRequest);
                            /* Failed to create media library record for service agreement */
                            if (empty($serviceAgreeMediaLibrary)) {
                                return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                            }
                            $serviceAgreeId = $serviceAgreeMediaLibrary;
                        }
                    }
                    /* Service Agreement end */
                    $org_slug = Str::slug($org_name, '-');
                    if (!empty($org_slug)) {
                        $organization = Organization::updateOrCreate([
                            'id' => $org_id
                        ], [
                            'name' => $org_name,
                            'org_slug' => $org_slug,
                            'org_link' => $org_slug,
                            'cover_image_media_id' => $coverMediaId,
                            'org_logo_media_id' => $logoMediaId,
                            'service_agreement_media_id' => $serviceAgreeId,
                            'agreement_link' => $request->org_service_agreement == 'link' ? $org_agreement_link : null,
                            'service_provider_id' => $serviceProvider,
                            'setup_steps' => $current_step
                        ]);
                        $org_id = $organization->id;
                        if ($org_id) {
                            return response()->json(["msg" => "Organization created successfully.", "success" => true, 'org_id' => $org_id], 200);
                        }
                    } else {
                        return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                    }
                }
                /* For Steps 2, 3, 4, 5 */ else if ($current_step == 2 || $current_step == 3 || $current_step == 4 || $current_step == 5) {
                    $org_id = $request->org_id;

                    if (!empty($org_id)) {
                        /* Get Organization Data using organization id */
                        $where['where']['id'] = $org_id;
                        $org_data = self::getOrganizations($where)->first();
                        if (!empty($org_data)) {
                            /** Get Organization Name and Slug */
                            $org_name = $org_data->name;
                            $org_slug = $org_data->org_slug;
                            $fields = [
                                'device_plans.id',
                                'device_plans.plan_name',
                                'device_plans.freq_occurence',
                                'deviceModel.title as device_model_name',
                            ];
                            /* Executing second step */
                            if ($current_step == 2) {
                                $enable_multiple_sub_org = $request->enable_multiple_sub_org;
                                /** If organization have not sub-organizations */
                                if ($enable_multiple_sub_org == 0) {
                                    $org_data_update = $org_data->update([
                                        'enable_multiple_sub_org' => $enable_multiple_sub_org,
                                        'setup_steps' => $current_step,
                                    ]);
                                    if ($org_data_update == true) {
                                        return response()->json(["msg" => "Sub-organization updated successfully.", "success" => true], 200);
                                    } else {
                                        return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                                    }
                                }
                                /** If organization have sub-organizations */
                                else if ($enable_multiple_sub_org == 1) {
                                    $sub_orgs = request()->input('organiations_sub_org', []);
                                    if (!empty($sub_orgs)) {
                                        /* Delete existing SubOrganization before adding new ones */
                                        SubOrganization::where('org_id', $org_id)
                                            ->where('active', 1)
                                            ->where('service_provider_id', $serviceProvider)
                                            ->delete();
                                        foreach ($sub_orgs as $sub_org) {
                                            $subOrgName = $sub_org['subOrgName'];
                                            $sub_organizations = SubOrganization::create([
                                                'org_id' => $org_id,
                                                'name' => $subOrgName,
                                                'service_provider_id' => $serviceProvider,
                                            ]);

                                            if (empty($sub_organizations->org_id)) {
                                                return response()->json(["msg" => "Sub-organization not created, something went wrong.", "success" => false], 200);
                                            }
                                        }

                                        $org_data_update = $org_data->update([
                                            'enable_multiple_sub_org' => $enable_multiple_sub_org,
                                            'setup_steps' => $current_step,
                                        ]);
                                        if ($org_data_update == true) {
                                            return response()->json(["msg" => "Sub-organization updated successfully.", "success" => true], 200);
                                        } else {
                                            return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                                        }
                                    } else {
                                        return response()->json(["msg" => "Sub-organizations not found.", "success" => false], 200);
                                    }
                                } else {
                                    return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                                }
                            }

                            /* Executing third step */ else if ($current_step == 3) {
                                $org_allowed_devices = request()->input('organiation_allowed_devices', []);
                                $org_allowed_renewal_devices = request()->input('organiations_allowed_renewal_devices', []);
                                if (!empty($org_allowed_devices) || !empty($org_allowed_renewal_devices)) {

                                    /* If Organization allowed devices */
                                    if (!empty($org_allowed_devices)) {
                                        /* Delete existing OrgAllowedModel before adding new ones */
                                        OrgAllowedModel::where('org_id', $org_id)->where('active', 1)->where('service_provider_id', $serviceProvider)->delete();

                                        foreach ($org_allowed_devices as $org_allowed_device) {


                                            $device_id = $org_allowed_device['deviceModelId'];
                                            $device_plan_id = $org_allowed_device['planId'];
                                            $coverage_price = $org_allowed_device['coverage_price'];
                                            $deductible = $org_allowed_device['deductible'];
                                            // $expiration_days = $org_allowed_device['expiration_days'];
                                            /* $freqOccurence = (int) $org_allowed_device['freqOccurence'];
                                            $expiration_days = $freqOccurence == 1 ? $org_allowed_device['expiration_days'] : match ($freqOccurence)
                                                {
                                                    2 => 30,
                                                    3 => 90,
                                                    4 => 180,
                                                    5 => 365,
                                                    default => 30,
                                                }; */

                                            /* Add to processed array or save to DB */
                                            $expiration_date =  $org_allowed_device['expiration_date'];
                                            $organizational_devices = OrgAllowedModel::create([
                                                'org_id' => $org_id,
                                                'model_id' => $device_id,
                                                'device_plan_id' => $device_plan_id,
                                                'coverage_price' => $coverage_price,
                                                'deductible' => $deductible,
                                                'expiration_date' => $expiration_date,
                                                'service_provider_id' => $serviceProvider,
                                            ]);

                                            if (empty($organizational_devices)) {
                                                return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                                            }
                                        }
                                    }

                                    /* If Organization allowed renewal devices */
                                    if (!empty($org_allowed_renewal_devices)) {

                                        /* Delete existing OrgAllowedRenewalModel before adding new ones */
                                        OrgAllowedRenewalModel::where('org_id', $org_id)
                                            ->where('active', 1)
                                            ->where('service_provider_id', $serviceProvider)
                                            ->delete();

                                        foreach ($org_allowed_renewal_devices as $org_allowed_renewal_device) {
                                            $device_model_id = $org_allowed_renewal_device['deviceModelId'];
                                            $device_plan_id = $org_allowed_renewal_device['planId'];
                                            $coverage_price = $org_allowed_renewal_device['coverage_price'];
                                            $deductible = $org_allowed_renewal_device['deductible'];
                                            // $expiration_days = $org_allowed_renewal_device['expiration_days'];
                                            /* $freqOccurence = (int) $org_allowed_renewal_device['freqOccurence'];
                                            $expiration_days = $freqOccurence == 1
                                                ? $org_allowed_renewal_device['expiration_days']
                                                : match ($freqOccurence) {
                                                    2 => 30,
                                                    3 => 90,
                                                    4 => 180,
                                                    5 => 365,
                                                    default => 30,
                                                }; */
                                            $expiration_date = $org_allowed_renewal_device['expiration_date'];
                                            if (!empty($device_model_id) && !empty($device_plan_id) && !empty($coverage_price) && !empty($expiration_date)) {
                                                $organizational_renewal_devices = OrgAllowedRenewalModel::create([
                                                    'org_id' => $org_id,
                                                    'model_id' => $device_model_id,
                                                    'device_plan_id' => $device_plan_id,
                                                    'coverage_price' => $coverage_price,
                                                    'deductible' => $deductible,
                                                    'expiration_date' => $expiration_date,
                                                    'service_provider_id' => $serviceProvider,
                                                ]);
                                                if (empty($organizational_renewal_devices)) {
                                                    return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                                                }
                                            }
                                        }
                                    }
                                }

                                $org_data_update = $org_data->update([
                                    'setup_steps' => $current_step,
                                ]);
                                if ($org_data_update == true) {
                                    return response()->json(["msg" => "Organization devices added successfully.", "success" => true], 200);
                                } else {
                                    return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                                }
                            }

                            /* Executing fourth step */ else if ($current_step == 4) {
                                $org_type = $request->org_type;
                                $org_portal_status = $request->org_portal_status;
                                $org_closed_portal_msg = $request->org_portal_status == 0 ? $request->org_close_portal_message : null;
                                $org_claim_reasons = request()->input('org_claim_reasons', []);
                                if (!empty($org_claim_reasons)) {
                                    /* Delete existing OrganizationClaimReason before adding new ones */
                                    OrganizationClaimReason::where('org_id', $org_id)
                                        ->where('active', 1)
                                        ->where('service_provider_id', $serviceProvider)
                                        ->delete();
                                    foreach ($org_claim_reasons as $org_claim_reason) {
                                        $reasonId = $org_claim_reason['reasonId'];
                                        $claimReason = OrganizationClaimReason::create([
                                            'claim_reason_id' => $reasonId,
                                            'org_id' => $org_id,
                                            'service_provider_id' => $serviceProvider,
                                        ]);
                                        if (empty($claimReason)) {
                                            return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                                        }
                                    }
                                    $org_portal_status = $org_data->update([
                                        'org_type' => $org_type,
                                        'portal_status' => $org_portal_status,
                                        'close_portal_message' => $org_closed_portal_msg,
                                        'setup_steps' => $current_step,
                                    ]);

                                    if ($org_portal_status == true) {
                                        return response()->json(["msg" => "Claim reasons and Portal status updated successfully.", "success" => true], 200);
                                    } else {
                                        return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                                    }
                                } else {
                                    return response()->json(["msg" => "Claim reasons not found.", "success" => false], 200);
                                }
                            }

                            /* Executing fifth step */ else if ($current_step == 5) {
                                $org_repiar_enable_status = $request->repair_enabled ?? '';
                                $org_edit_device_permission = $request->can_edit_device ?? '';
                                $org_allow_parent_claim = $request->allow_parents_claim ?? '';
                                $org_additional_instruction = $request->additional_instructions ?? '';
                                $update_org_data = $org_data->update([
                                    'repair_enabled' => $org_repiar_enable_status,
                                    'can_edit_device' => $org_edit_device_permission,
                                    'allow_parents_claim' => $org_allow_parent_claim,
                                    'additional_instructions' => $org_additional_instruction,
                                    'setup_steps' => $current_step,
                                ]);

                                if ($update_org_data == true) {
                                    /** Stripe Secret key */
                                    /* $stripeSecretKey = trim(SDCOptionController::getOption('sdcsm_stripe_secret_key', $serviceProvider), '"');

                                    if (empty($stripeSecretKey)) {
                                        return response()->json(["msg" => "Stripe secret key missing.", "success" => false], 200);
                                    } */
                                    /* Dynamically override the Cashier key */
                                    // config(['cashier.secret' => $stripeSecretKey]);
                                    // Stripe::setApiKey(config('cashier.secret'));
                                    /* Fetch all existing OrgAllowedModels for this org */
                                    // $existingOrgDevices = OrgAllowedModel::where('org_id', $org_id)->where('active', 1)->where('service_provider_id', $serviceProvider)->get();
                                    // if ($existingOrgDevices->isNotEmpty()) {
                                    //     /** allowed */

                                    //     foreach ($existingOrgDevices as $existingOrgDevice) {
                                    //         $planData = DevicePlanController::getSingleDevicePlanById($existingOrgDevice->device_plan_id, $fields);
                                    //         $freqOccurence = (int) $planData->freq_occurence;
                                    //         if ($freqOccurence != 1) {
                                    //             $stripeProductId = null;
                                    //             $stripePriceId = null;
                                    //             $stripeProduct = Product::create([
                                    //                 'name' => $org_slug . '_' . $planData->plan_name,
                                    //                 'description' => 'Coverage Plan for Device model ' . ($planData->device_model_name ?? '') . ' of ' . ($org_name ?? '') . ' organization.',
                                    //             ]);
                                    //             $stripeProductId = $stripeProduct->id;
                                    //             if (!$stripePriceId && $stripeProductId) {
                                    //                 [$interval, $intervalCount] = match ($freqOccurence) {
                                    //                     2 => ['month', 1],
                                    //                     3 => ['month', 3],
                                    //                     4 => ['month', 6],
                                    //                     5 => ['year', 1],
                                    //                     default => ['month', 1],
                                    //                 };

                                    //                 $stripePrice = Price::create([
                                    //                     'product' => $stripeProductId,
                                    //                     'unit_amount' => round($existingOrgDevice->coverage_price * 100),
                                    //                     'currency' => 'usd',
                                    //                     'recurring' => [
                                    //                         'interval' => $interval,
                                    //                         'interval_count' => $intervalCount,
                                    //                     ],
                                    //                 ]);

                                    //                 $stripePriceId = $stripePrice->id;
                                    //             }
                                    //             /** Update Fields */
                                    //             OrgAllowedModel::where('id', $existingOrgDevice->id)->update([
                                    //                 'stripe_product_id' => $stripeProductId,
                                    //                 'stripe_price_id' => $stripePriceId,
                                    //             ]);
                                    //         }
                                    //     }
                                    // }
                                    /* Fetch all existing OrgAllowedModels for this org */
                                    // $existingOrgRenewalDevices = OrgAllowedRenewalModel::where('org_id', $org_id)->where('active', 1)->where('service_provider_id', $serviceProvider)->get();
                                    // if ($existingOrgRenewalDevices->isNotEmpty()) {
                                    //     foreach ($existingOrgRenewalDevices as $existingOrgRenewalDevice) {
                                    //         $renewalPlanData = DevicePlanController::getSingleDevicePlanById($existingOrgRenewalDevice->device_plan_id, $fields);
                                    //         $freqOccurence = (int) $renewalPlanData->freq_occurence;
                                    //         $coverage_price = $existingOrgRenewalDevice->coverage_price;
                                    //         if ($freqOccurence != 1) {
                                    //             $stripeProductId = null;
                                    //             $stripePriceId = null;
                                    //             $stripeProduct = Product::create([
                                    //                 'name' =>  $org_slug . '_' . $renewalPlanData->plan_name,
                                    //                 'description' => 'Renewal Plan for Device model ' . ($renewalPlanData->device_model_name ?? '') . ' of ' . ($org_name ?? '') . ' organization.',
                                    //             ]);
                                    //             $stripeProductId = $stripeProduct->id;
                                    //             if (!$stripePriceId && $stripeProductId) {
                                    //                 [$interval, $intervalCount] = match ($freqOccurence) {
                                    //                     2 => ['month', 1],
                                    //                     3 => ['month', 3],
                                    //                     4 => ['month', 6],
                                    //                     5 => ['year', 1],
                                    //                     default => ['month', 1],
                                    //                 };

                                    //                 $stripePrice = Price::create([
                                    //                     'product' => $stripeProductId,
                                    //                     'unit_amount' => round($coverage_price * 100),
                                    //                     'currency' => 'usd',
                                    //                     'recurring' => [
                                    //                         'interval' => $interval,
                                    //                         'interval_count' => $intervalCount,
                                    //                     ],
                                    //                 ]);

                                    //                 $stripePriceId = $stripePrice->id;
                                    //             }
                                    //             /** Update Fields */
                                    //             OrgAllowedRenewalModel::where('id', $existingOrgRenewalDevice->id)->update([
                                    //                 'stripe_product_id' => $stripeProductId,
                                    //                 'stripe_price_id' => $stripePriceId,
                                    //             ]);
                                    //         }
                                    //     }
                                    // }
                                    return response()->json(["msg" => "Organization created successfully.", "success" => true], 200);
                                } else {
                                    return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                                }
                            }
                        } else {
                            return response()->json(["msg" => "Organization not found.", "success" => false], 200);
                        }
                    } else {
                        return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                    }
                }
            } else {
                return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }



    /** Update Org Link */
    public function updateOrgLink(Request $request, string $id)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            $appUrl = preg_quote(parse_url(env('APP_URL'), PHP_URL_HOST), '/');

            $validator = Validator::make($request->all(), [
                'org_link' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:organizations,org_link,' . $id . ',id,active,1,service_provider_id,' . $serviceProvider,
                    'regex:/^[a-zA-Z0-9]/', // Starts with letter/number
                    'not_regex:/^(https?:\/\/|www\.)/', // Not full URL
                    "not_regex:/$appUrl/i", // Doesn't contain APP_URL
                    'not_regex:/^org/i',
                ],
            ], [
                'org_link.required' => 'Organization link is required.',
                'org_link.string' => 'Organization link must be a string.',
                'org_link.max' => 'Organization link must not exceed 255 characters.',
                'org_link.unique' => 'Organization link has already been taken.',
                'org_link.regex' => 'Organization link must start with a letter or number.',
                'org_link.not_regex' => 'Organization link must not start with "http://", "https://", "www.", "org", or contain the application domain.',
            ]);

            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }

            $org = Organization::find($id);
            if (empty($org)) {
                return response()->json(["msg" => "Organization not found.", "success" => false], 200);
            }

            $org->org_link = $request->org_link;
            $org->save();

            return response()->json(["msg" => "Organization link updated successfully.", "success" => true], 200);
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Edit organization form */
    public function editOrg(string $id)
    {
        try {
            $request_id = $id;
            if (!empty($request_id)) {
                /* Getting service provider id from session */
                $serviceProvider = Session::get('service_provider');
                /** Calling common function from claim reason controller to get reasons */
                $claimReasons = ClaimReasonController::getClaimReasonsDropdown();
                $orgFields = [
                    'org.id',
                    'org.name',
                    'org.repair_enabled',
                    'org.can_edit_device',
                    'org.agreement_link',
                    'org.allow_parents_claim',
                    'org.org_type',
                    'org.portal_status',
                    'org.close_portal_message',
                    'org.enable_multiple_sub_org',
                    'org.additional_instructions',
                    'logoMedia.file_name as logo_file_name',
                    'logoMedia.file_path as logo_file_path',
                    'coverMedia.file_name as cover_file_name',
                    'coverMedia.file_path as cover_file_path',
                    'pdfMedia.file_name as pdf_file_name',
                    'pdfMedia.file_path as pdf_file_path',
                ];
                $subOrgs = null;
                $claims = null;
                $query = DB::table('organizations as org')
                    ->leftJoin('media_libraries as logoMedia', 'org.org_logo_media_id', '=', 'logoMedia.id')
                    ->leftJoin('media_libraries as coverMedia', 'org.cover_image_media_id', '=', 'coverMedia.id')
                    ->leftJoin('media_libraries as pdfMedia', 'org.service_agreement_media_id', '=', 'pdfMedia.id')
                    ->select($orgFields)
                    ->where('org.id', $request_id)
                    ->where('org.active', 1)
                    ->where('org.service_provider_id', $serviceProvider)
                    ->where(function ($query) use ($serviceProvider) {
                        $query->whereNull('org.org_logo_media_id')
                            ->orWhere(function ($query) use ($serviceProvider) {
                                $query->where('logoMedia.service_provider_id', $serviceProvider);
                            });
                    })
                    ->where(function ($query) use ($serviceProvider) {
                        $query->whereNull('org.cover_image_media_id')
                            ->orWhere(function ($query) use ($serviceProvider) {
                                $query->where('coverMedia.service_provider_id', $serviceProvider);
                            });
                    })
                    ->where(function ($query) use ($serviceProvider) {
                        $query->whereNull('org.service_agreement_media_id')
                            ->orWhere(function ($query) use ($serviceProvider) {
                                $query->where('pdfMedia.service_provider_id', $serviceProvider);
                            });
                    })
                    ->first();
                if (!empty($query)) {
                    $subOrgs = $this->getSubOrgWithDeletedAttr($query->id, $serviceProvider);
                    /** Allowed Models */
                    $allowedModels = $this->getOrgAllowedDevicesWithDeletedAttr($query->id, $serviceProvider);
                    /** Allowed Renewal Models */
                    $allowedRenewalModels = $this->getOrgRenewalDevicesWithDeletedAttr($query->id, $serviceProvider);
                    $claimIds = $this->getOrgClaimReasons($query->id, $serviceProvider);
                    $viewData = (object) [
                        'id' => $query->id,
                        'org_name' => $query->name,
                        'logo_file_name' => optional($query)->logo_file_name,
                        'logo_file_path' => optional($query)->logo_file_path,
                        'cover_file_name' => optional($query)->cover_file_name,
                        'cover_file_path' => optional($query)->cover_file_path,
                        'pdf_file_name' => optional($query)->pdf_file_name,
                        'pdf_file_path' => optional($query)->pdf_file_path,
                        'repair_enabled' => $query->repair_enabled,
                        'can_edit_device' => $query->can_edit_device,
                        'agreement_link' => $query->agreement_link,
                        'subOrg' => $subOrgs,
                        'allow_parents_claim' => $query->allow_parents_claim,
                        'org_type' => $query->org_type,
                        'portal_status' => $query->portal_status,
                        'close_portal_message' => $query->close_portal_message,
                        'enable_multiple_sub_org' => $query->enable_multiple_sub_org,
                        'additional_instructions' => $query->additional_instructions,
                        'allowed_models' => $allowedModels,
                        'allowed_renewal_models' => $allowedRenewalModels,
                        'claim_reasons_id' => $claimIds,
                    ];
                    return response()->json(['viewData' => $viewData, 'claimReasonsData' => $claimReasons, "msg" => "Organization Data.", "success" => true], 200);
                } else {
                    return response()->json(["msg" => "No organization found.", "success" => false], 200);
                }
            } else {
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Update Organization Details Only  */
    public function updateOrgDetails(Request $request, $id)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate the request */
            $validator = Validator::make($request->all(), [
                'org_name' => 'required|string|max:50',
                'org_logo' => 'nullable|image|mimes:jpg,png,jpeg,svg|max:2048',
                'org_cover_image' => 'nullable|image|mimes:jpg,png,jpeg,svg|max:2048',
                'org_service_agreement' => 'required|in:pdf,link,none',

                'org_agreement_pdf' => 'required_if:org_service_agreement,pdf|mimes:pdf|max:5120',
                'org_agreement_link' => 'required_if:org_service_agreement,link|nullable',
            ], [
                'org_name.required' => 'Organization name is required.',
                'org_name.string' => 'Organization name must be a string.',
                'org_name.max' => 'Organization name must not exceed 50 characters.',
                'org_logo.image' => 'Organization logo must be an image.',
                'org_service_agreement.required' => 'Service agreement type is required.',
                'org_agreement_pdf.required_if' => 'PDF agreement is required when service agreement type is PDF.',
                'org_agreement_link.required_if' => 'Agreement link is required when service agreement type is link.',
                'org_agreement_pdf.mimes' => 'PDF agreement must be a PDF file.',
                'org_agreement_pdf.max' => 'PDF agreement must not exceed 5MB.',
            ]);

            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $organization = Organization::where('id', $id)->where('service_provider_id', $serviceProvider)->where('active', 1)->first();
            if (empty($organization)) {
                return response()->json(["msg" => "Organization not found.", "success" => false], 200);
            }
            $org_slug = Str::slug($request->org_name, '-');

            /* Organization Logo */
            $logoImageName = null;
            $logoMediaId = null;
            if ($request->hasFile('org_logo')) {
                $logoImage = $request->file('org_logo');
                /* Check if the image is valid */
                if (empty($logoImage)) {
                    return response()->json(["msg" => "No image provided.", "success" => false], 200);
                }
                /* Generate a unique file(logo) name */
                $logoImageExtension = $logoImage->getClientOriginalExtension();
                $logoImageName = pathinfo($logoImage->getClientOriginalName(), PATHINFO_FILENAME) . '-' . substr(str_shuffle("0123456789"), 0, 4) . '.' . $logoImageExtension;
                /* Failed to generate unique file(logo) name.*/
                if (empty($logoImageName)) {
                    return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                }
                /* Store the file in storage/app/public/organizations/logo */
                $logoImagePath = $logoImage->storeAs('organizations/logo', $logoImageName, 'public');
                if (empty($logoImagePath)) {
                    return response()->json(["msg" => "Failed to upload image.", "success" => false], 200);
                }
                /* create and get (id) media library for logo */
                $mediaRequest = [
                    'fileName' => $logoImageName,
                    'filePath' => '/storage/organizations/logo/',
                    'fileType' => $logoImageExtension,

                ];
                $mediaLibrary = MediaLibraryController::createMediaLibrary($mediaRequest);
                /* Failed to create media library record for logo */
                if (empty($mediaLibrary)) {
                    return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                }
                $logoMediaId = $mediaLibrary;
            } else {
                $logoMediaId = $organization->org_logo_media_id;
            }
            /* Organization Logo end */
            /* Cover Image */
            $coverImageName = null;
            $coverMediaId = null;
            if ($request->hasFile('org_cover_image')) {
                $coverageImage = $request->file('org_cover_image');
                /* Check if the image is valid */
                if (empty($coverageImage)) {
                    return response()->json(["msg" => "No image provided.", "success" => false], 200);
                }
                /* Generate a unique file(cover image) name */
                $coverImageExtension = $coverageImage->getClientOriginalExtension();
                $coverImageName = pathinfo($coverageImage->getClientOriginalName(), PATHINFO_FILENAME) . '-' . substr(str_shuffle("0123456789"), 0, 4) . '.' . $coverImageExtension;
                /* Failed to generate unique file(cover image) name.*/
                if (empty($coverImageName)) {
                    return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                }
                /* Store the file in storage/app/public/organizations/cover_image */
                $coverageImagePath = $coverageImage->storeAs('organizations/cover_image', $coverImageName, 'public');
                if (empty($coverageImagePath)) {
                    return response()->json(["msg" => "Failed to upload image.", "success" => false], 200);
                }
                /* create and get (id) media library for cover image */
                $coverMediaRequest = [
                    'fileName' => $coverImageName,
                    'filePath' => '/storage/organizations/cover_image/',
                    'fileType' => $coverImageExtension,

                ];
                $coverMediaLibrary = MediaLibraryController::createMediaLibrary($coverMediaRequest);
                /* Failed to create media library record for cover image */
                if (empty($coverMediaLibrary)) {
                    return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                }
                $coverMediaId = $coverMediaLibrary;
            } else {
                $coverMediaId = $organization->cover_image_media_id;
            }
            /* Cover Image end */

            /* Service Agreement */
            $serviceAgreeName = null;
            $serviceAgreeId = null;
            if ($request->org_service_agreement == 'pdf') {
                if ($request->hasFile('org_agreement_pdf')) {
                    $serviceAgreePdf = $request->file('org_agreement_pdf');
                    /* Check if the image is valid */
                    if (empty($serviceAgreePdf)) {
                        return response()->json(["msg" => "No PDF provided.", "success" => false], 200);
                    }
                    /* Generate a unique file(service agreement) name */
                    $serviceAgreeExtension = $serviceAgreePdf->getClientOriginalExtension();
                    $serviceAgreeName = pathinfo($serviceAgreePdf->getClientOriginalName(), PATHINFO_FILENAME) . '-' . substr(str_shuffle("0123456789"), 0, 4) . '.' . $serviceAgreeExtension;
                    /* Failed to generate unique file(service agreement) name.*/
                    if (empty($serviceAgreeName)) {
                        return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                    }
                    /* Store the file in storage/app/public/organizations/service_agreement */
                    $serviceAgreePdfPath = $serviceAgreePdf->storeAs('organizations/service_agreement', $serviceAgreeName, 'public');
                    if (empty($serviceAgreePdfPath)) {
                        return response()->json(["msg" => "Failed to upload pdf.", "success" => false], 200);
                    }
                    /* create and get (id) media library for service agreement */
                    $serviceAgreeMediaRequest = [
                        'fileName' => $serviceAgreeName,
                        'filePath' => '/storage/organizations/service_agreement/',
                        'fileType' => $serviceAgreeExtension,

                    ];
                    $serviceAgreeMediaLibrary = MediaLibraryController::createMediaLibrary($serviceAgreeMediaRequest);
                    /* Failed to create media library record for service agreement */
                    if (empty($serviceAgreeMediaLibrary)) {
                        return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                    }
                    $serviceAgreeId = $serviceAgreeMediaLibrary;
                }
            } else {
                $serviceAgreeId = $organization->service_agreement_media_id;
            }
            /* Service Agreement end */
            $organization->update([
                'name' => $request->org_name,
                'org_slug' => $org_slug,
                'org_link' => $org_slug,
                'org_logo_media_id' => $logoMediaId,
                'cover_image_media_id' => $coverMediaId,
                'service_agreement_media_id' => $serviceAgreeId,
                'agreement_link' => $request->org_service_agreement == 'link' ? $request->org_agreement_link : null,
            ]);
            $orgData = [
                'name' => $organization->name,
                'org_logo_media_id' => $organization->org_logo_media_id,
                'cover_image_media_id' => $organization->cover_image_media_id,
                'service_agreement_media_id' => $organization->service_agreement_media_id,
                'agreement_link' => $organization->agreement_link,
                'logo_file_name' => $logoImageName,
                'logo_file_path' => '/storage/organizations/logo/',
                'cover_file_name' => $coverImageName,
                'cover_file_path' => '/storage/organizations/cover_image/',
                'pdf_file_name' => $serviceAgreeName,
                'pdf_file_path' => '/storage/organizations/service_agreement/',
            ];
            return response()->json(["msg" => "Organization updated successfully.", 'orgData' => $orgData, "success" => true], 200);
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Organization sub-organization details update */
    public function updateSubOrg(Request $request, $id)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate the request */
            $validator = Validator::make($request->all(), [
                'sub_org_data' => 'required|array',
                'sub_org_data.*.name' => 'required|string|max:50',
                'sub_org_data.*.active' => 'required|in:0,1',
                'sub_org_data.*.id' => [
                    'nullable',
                    Rule::exists('sub_organizations', 'id')
                        ->where('org_id', $id)
                        ->where('service_provider_id', $serviceProvider),
                ],
            ], [
                'sub_org_data.required' => 'Sub-organization data is required.',
                'sub_org_data.array' => 'Sub-organization data must be an array.',
                'sub_org_data.*.name.required' => 'Sub-organization must have a name.',
                'sub_org_data.*.name.string' => 'Sub-organization name must be a valid string.',
                'sub_org_data.*.name.max' => 'Sub-organization name must not exceed 50 characters.',
                'sub_org_data.*.active.required' => 'Sub-organization must have an active status.',
                'sub_org_data.*.active.in' => 'Active status must be either 0 (inactive) or 1 (active).',
                'sub_org_data.*.id.exists' => 'sub-organization ID is invalid.',
            ]);
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $organization = Organization::where('id', $id)->where('service_provider_id', $serviceProvider)->where('active', 1)->first();
            if (empty($organization)) {
                return response()->json(["msg" => "Organization not found.", "success" => false], 200);
            }
            $organization->update([
                'enable_multiple_sub_org' => 1,
            ]);
            /* Loop through sub-org data */
            foreach ($request->sub_org_data as $subOrgItem) {
                $data = [
                    'name' => $subOrgItem['name'],
                    'active' => (int)$subOrgItem['active'], /* Force integer conversion */
                ];
                if (!empty($subOrgItem['id'])) {
                    /* Update existing sub-org */
                    $subOrg = SubOrganization::where('id', $subOrgItem['id'])
                        ->where('org_id', $organization->id)
                        ->where('service_provider_id', $serviceProvider)
                        ->first();

                    if ($subOrg) {
                        $subOrg->update($data);
                    }
                } else {
                    /* Create new sub-org */
                    $data['org_id'] = $organization->id;
                    $data['service_provider_id'] = $serviceProvider;
                    $subOrg = SubOrganization::create($data);
                }
            }
            $updatedSubOrgs = $this->getSubOrgWithDeletedAttr($organization->id, $serviceProvider);
            return response()->json(["msg" => "Sub-organization updated successfully.", "success" => true, "sub_organizations" => $updatedSubOrgs], 200);
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Hard Delete Sub-organization */
    public function subOrgDelete(Request $request, $id)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate the request */
            $validator = Validator::make($request->all(), [
                'orgId' => 'required|exists:organizations,id,active,1,service_provider_id,' . $serviceProvider,
            ], [
                'orgId.required' => 'Organization is required.',
                'orgId.exists' => 'Organization is invalid.',
            ]);
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $subOrg = SubOrganization::where('id', $id)->where('org_id', $request->orgId)->where('service_provider_id', $serviceProvider)->first();
            if (!empty($subOrg)) {
                $subOrg->delete();
            }
            $subOrgData = $this->getSubOrgWithDeletedAttr($request->orgId, $serviceProvider);
            return response()->json(["msg" => "Sub-Orgnization Deleted Successfully.", 'success' => true, 'sub_organizations' => $subOrgData], 200);
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Organization Allowed Devices Update */
    public function updateAllowedDevices(Request $request, $id)
    {
        try {
            $serviceProvider = Session::get('service_provider');
            // Validate request
            $validator = Validator::make($request->all(), [
                /* 'allowed_devices_data' => 'required|array', */
                'deviceModelId' => 'required|exists:device_models,id,active,1,service_provider_id,' . $serviceProvider,
                'planId' => 'required|exists:device_plans,id,active,1,service_provider_id,' . $serviceProvider,
                'coverage_price' => 'required|numeric',
                'deductible' => 'required|numeric',
                'expiration_date' => 'required|date|date_format:Y-m-d',
                'active' => 'required|in:1,0',
            ], [
                'deviceModelId.exists' => 'Device model ID does not exist.',
                'planId.exists' => 'Device plan ID does not exist.',
                'coverage_price.required' => 'Coverage price is required.',
                'deductible.required' => 'Deductible is required.',
                'expiration_date.required' => 'Expiration date is required.',
                'expiration_date.date' => 'Expiration date must be a date.',
                'expiration_date.date_format' => 'Expiration date must be a date.',
                'active.in' => 'Active status must be either 0 or 1.',
                /* 'allowed_devices_data.required' => 'Allowed devices data is required.', */
            ]);
            // Continue only if base validation passed
            if (!$validator->fails()) {
                $deviceModelId = $request->deviceModelId;
                $planId = $request->planId;
                $currentId = $request->deviceId ?? null;

                // Get the existing record to compare
                $existingRecord = DB::table('org_allowed_models')->where('id', $currentId)->where('org_id', $id)->where('service_provider_id', $serviceProvider)->first();

                // Only check for duplicate if the model-plan combo is different
                $comboChanged = !$existingRecord ||
                    $existingRecord->model_id != $deviceModelId ||
                    $existingRecord->device_plan_id != $planId;

                if ($comboChanged) {
                    $exists = DB::table('org_allowed_models')->where('org_id', $id)->where('model_id', $deviceModelId)->where('device_plan_id', $planId)->where('service_provider_id', $serviceProvider)->exists();

                    if ($exists) {
                        $validator->errors()->add("planId", 'This plan is already assigned to the selected device model.');
                    }
                }

                if ($validator->errors()->any()) {
                    return response()->json([
                        "msg" => "Validation errors",
                        "success" => false,
                        "errors" => $validator->errors(),
                    ], 200);
                }
            }
            // Get organization
            $organization = Organization::where([
                'id' => $id,
                'service_provider_id' => $serviceProvider,
                'active' => 1
            ])->first();

            if (!$organization) {
                return response()->json(["msg" => "Organization not found.", "success" => false], 200);
            }
            /* $org_name = $organization->name;
            $org_slug = $organization->org_slug; */

            /* Stripe setup */
            /*  $stripeSecretKey = trim(SDCOptionController::getOption('sdcsm_stripe_secret_key', $serviceProvider), '"');
            if (!$stripeSecretKey) {
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }

            config(['cashier.secret' => $stripeSecretKey]);
            Stripe::setApiKey($stripeSecretKey); */

            /* Fields to fetch from plan */
            /* $fields = [
                'device_plans.id',
                'device_plans.plan_name',
                'device_plans.freq_occurence',
                'deviceModel.title as device_model_name',
            ]; */

            /* foreach ($request->allowed_devices_data as $deviceItem) { */
            $isUpdate = !empty($request->deviceId);

            /* Fetch or initialize model */
            $orgAllowed = $isUpdate ? OrgAllowedModel::where('id', $request->deviceId)->where('org_id', $organization->id)->where('service_provider_id', $serviceProvider)->first() : new OrgAllowedModel();

            /* if ($isUpdate && !$orgAllowed) continue; */

            /* Plan info */
            /* $planData = DevicePlanController::getSingleDevicePlanById($request->planId, $fields); */
            /* if (!$planData) continue; */

            /*  $freqOccurence = (int) $planData->freq_occurence;
            $planName = $planData->plan_name;
            $modelName = $planData->device_model_name ?? ''; */

            /* Assign fields */
            $orgAllowed->org_id = $organization->id;
            $orgAllowed->model_id = $request->deviceModelId;
            $orgAllowed->device_plan_id = $request->planId;
            $orgAllowed->coverage_price = $request->coverage_price;
            $orgAllowed->deductible = $request->deductible;
            $orgAllowed->expiration_date = $request->expiration_date;
            $orgAllowed->active = $request->active;
            $orgAllowed->service_provider_id = $serviceProvider;
            /*  $orgAllowed->expiration_days = $freqOccurence == 1
                ? $request->expiration_days
                : match ($freqOccurence) {
                    2 => 30,
                    3 => 90,
                    4 => 180,
                    5 => 365,
                    default => 30,
                }; */

            $orgAllowed->save();

            // if ($freqOccurence != 1) {
            //     /* Stripe Product creation or retrievaln */
            //     if (!$orgAllowed->stripe_product_id) {
            //         $product = Product::create([
            //             'name' => $org_slug . '_' . $planName,
            //             'description' => "Coverage Plan for Device model {$modelName} of " . ($org_name ?? '') . ' organization.',
            //         ]);
            //         $orgAllowed->stripe_product_id = $product->id;
            //     }

            //     [$interval, $intervalCount] = match ($freqOccurence) {
            //         2 => ['month', 1],
            //         3 => ['month', 3],
            //         4 => ['month', 6],
            //         5 => ['year', 1],
            //         default => ['month', 1],
            //     };

            //     /* Always create a new price (Stripe best practice) */
            //     $price = Price::create([
            //         'product' => $orgAllowed->stripe_product_id,
            //         'unit_amount' => round($request->coverage_price * 100),
            //         'currency' => 'usd',
            //         'recurring' => [
            //             'interval' => $interval,
            //             'interval_count' => $intervalCount,
            //         ],
            //     ]);

            //     $orgAllowed->stripe_price_id = $price->id;
            //     $orgAllowed->save();
            // }

            /* Return updated data */
            $updatedAllowedDevicesData = $this->getOrgAllowedDevicesWithDeletedAttr($organization->id, $serviceProvider);
            return response()->json([
                "msg" => "Allowed devices updated successfully.",
                "updatedAllowedDevicesData" => $updatedAllowedDevicesData,
                "success" => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later." . $e->getMessage(), "success" => false,], 200);
        }
    }
    /** Org Allowed Device Delete */
    public function orgAllowedDevicesDelete(Request $request, $id)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate the request */
            $validator = Validator::make($request->all(), [
                'orgId' => 'required|exists:organizations,id,active,1,service_provider_id,' . $serviceProvider,
            ], [
                'orgId.required' => 'Organization is required.',
                'orgId.exists' => 'Organization is invalid.',
            ]);
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $orgAllowedModel = OrgAllowedModel::where('id', $id)->where('org_id', $request->orgId)->where('service_provider_id', $serviceProvider)->first();
            if (!empty($orgAllowedModel)) {
                /** Delete */
                $orgAllowedModel->delete();
            }
            /* Stripe setup */
            /* $stripeSecretKey = trim(SDCOptionController::getOption('sdcsm_stripe_secret_key', $serviceProvider), '"');
            if (!$stripeSecretKey) {
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }
            config(['cashier.secret' => $stripeSecretKey]);
            $this->cleanUpStripeProducts($orgAllowedModel, $stripeSecretKey); */
            $orgAllowedModelData = $this->getOrgAllowedDevicesWithDeletedAttr($request->orgId, $serviceProvider);
            return response()->json(["msg" => "Device Plan Deleted Successfully.", 'success' => true, 'orgAllowedModelData' => $orgAllowedModelData], 200);
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Organization Renewal Devices Update */
    public function updateRenewalDevices(Request $request, $id)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate the request */
            $validator = Validator::make($request->all(), [
                /* 'allowed_renewal_devices_data' => 'required|array', */
                'deviceModelId' => 'required|exists:device_models,id,active,1,service_provider_id,' . $serviceProvider,
                'planId' => 'required|exists:device_plans,id,active,1,service_provider_id,' . $serviceProvider,
                'coverage_price' => 'required|numeric',
                'deductible' => 'required|numeric',
                'expiration_date' => 'required|date|date_format:Y-m-d',
                'active' => 'required|in:1,0',
            ], [
                'deviceModelId.exists' => 'Device model ID does not exist.',
                'planId.exists' => 'Device plan ID does not exist.',
                'coverage_price.required' => 'Coverage price is required.',
                'deductible.required' => 'Deductible is required.',
                'expiration_date.required' => 'Expiration date is required.',
                'expiration_date.date' => 'Expiration date must be a date.',
                'expiration_date.date_format' => 'Expiration date must be a date.',
                'active.in' => 'Active status must be either 0 or 1.',
            ]);

            /*   if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            } */

            if (!$validator->fails()) {
                $deviceModelId = $request->deviceModelId;
                $planId = $request->planId;
                $currentId = $request->deviceId ?? null;

                // Get the existing record to compare
                $existingRecord = DB::table('org_allowed_renewal_models')->where('id', $currentId)->where('org_id', $id)->where('service_provider_id', $serviceProvider)->first();

                // Only check for duplicate if the model-plan combo is different
                $comboChanged = !$existingRecord || $existingRecord->model_id != $deviceModelId || $existingRecord->device_plan_id != $planId;

                if ($comboChanged) {
                    $exists = DB::table('org_allowed_renewal_models')->where('org_id', $id)->where('model_id', $deviceModelId)->where('device_plan_id', $planId)->where('service_provider_id', $serviceProvider)->exists();
                    if ($exists) {
                        $validator->errors()->add("planId", 'This plan is already assigned to the selected device model.');
                    }
                }

                if ($validator->errors()->any()) {
                    return response()->json([
                        "msg" => "Validation errors",
                        "success" => false,
                        "errors" => $validator->errors(),
                    ], 200);
                }
            }

            $organization = Organization::where('id', $id)->where('service_provider_id', $serviceProvider)->where('active', 1)->first();
            if (empty($organization)) {
                return response()->json(["msg" => "Organization not found.", "success" => false], 200);
            }

            /*   $org_name = $organization->name;
            $org_slug = $organization->org_slug; */
            // Stripe setup
            /* $stripeSecretKey = trim(SDCOptionController::getOption('sdcsm_stripe_secret_key', $serviceProvider), '"');
            if (!$stripeSecretKey) {
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }

            config(['cashier.secret' => $stripeSecretKey]);
            Stripe::setApiKey($stripeSecretKey);

            // Fields to fetch from plan
            $fields = [
                'device_plans.id',
                'device_plans.plan_name',
                'device_plans.freq_occurence',
                'deviceModel.title as device_model_name',
            ]; */

            /* foreach ($request->allowed_renewal_devices_data as $deviceItem) { */
            $isUpdate = !empty($request->deviceId);

            // Fetch or initialize model
            $orgAllowedRenewal = $isUpdate ? OrgAllowedRenewalModel::where('id', $request->deviceId)->where('org_id', $organization->id)->where('service_provider_id', $serviceProvider)->first() : new OrgAllowedRenewalModel();

            /* if ($isUpdate && !$orgAllowedRenewal) continue; */

            // Plan info
            /*  $planData = DevicePlanController::getSingleDevicePlanById($request->planId, $fields); */
            /* if (!$planData) continue; */

            /*  $freqOccurence = (int) $planData->freq_occurence;
                $planName = $planData->plan_name;
                $modelName = $planData->device_model_name ?? ''; */

            // Assign fields
            $orgAllowedRenewal->org_id = $organization->id;
            $orgAllowedRenewal->model_id = $request->deviceModelId;
            $orgAllowedRenewal->device_plan_id = $request->planId;
            $orgAllowedRenewal->coverage_price = $request->coverage_price;
            $orgAllowedRenewal->deductible = $request->deductible;
            $orgAllowedRenewal->expiration_date = $request->expiration_date;
            $orgAllowedRenewal->active = $request->active;
            $orgAllowedRenewal->service_provider_id = $serviceProvider;
            /*  $orgAllowedRenewal->expiration_days = $freqOccurence == 1
                    ? $request->expiration_days
                    : match ($freqOccurence) {
                        2 => 30,
                        3 => 90,
                        4 => 180,
                        5 => 365,
                        default => 30,
                    }; */

            $orgAllowedRenewal->save();

            /*                 if ($freqOccurence != 1) {
                    // Stripe Product creation or retrieval
                    if (!$orgAllowedRenewal->stripe_product_id) {
                        $product = Product::create([
                            'name' => $org_slug . '_' . $planName,
                            'description' => "Coverage Plan for Device model {$modelName} of " . ($org_name ?? '') . ' organization.',
                        ]);
                        $orgAllowedRenewal->stripe_product_id = $product->id;
                    }

                    [$interval, $intervalCount] = match ($freqOccurence) {
                        2 => ['month', 1],
                        3 => ['month', 3],
                        4 => ['month', 6],
                        5 => ['year', 1],
                        default => ['month', 1],
                    };

                    // Always create a new price (Stripe best practice)
                    $price = Price::create([
                        'product' => $orgAllowedRenewal->stripe_product_id,
                        'unit_amount' => round($request->coverage_price * 100),
                        'currency' => 'usd',
                        'recurring' => [
                            'interval' => $interval,
                            'interval_count' => $intervalCount,
                        ],
                    ]);

                    $orgAllowedRenewal->stripe_price_id = $price->id;
                    $orgAllowedRenewal->save();
                } */
            /* Return updated data */
            $updatedRenewalDevicesData = $this->getOrgRenewalDevicesWithDeletedAttr($organization->id, $serviceProvider);
            return response()->json(["msg" => "Renewal Allowed devices updated successfully.", 'updatedRenewalDevicesData' => $updatedRenewalDevicesData, "success" => true], 200);
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Org Renewal Device Delete */
    public function orgRenewalDevicesDelete(Request $request, $id)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate the request */
            $validator = Validator::make($request->all(), [
                'orgId' => 'required|exists:organizations,id,service_provider_id,' . $serviceProvider,
            ], [
                'orgId.required' => 'Organization is required.',
                'orgId.exists' => 'Organization is invalid.',
            ]);
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $orgRenewalModel = OrgAllowedRenewalModel::where('id', $id)->where('org_id', $request->orgId)->where('service_provider_id', $serviceProvider)->first();
            if (!empty($orgRenewalModel)) {
                $orgRenewalModel->delete();
            }
            /* Stripe setup */
            /* $stripeSecretKey = trim(SDCOptionController::getOption('sdcsm_stripe_secret_key', $serviceProvider), '"');
            if (!$stripeSecretKey) {
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }
            config(['cashier.secret' => $stripeSecretKey]);
            $this->cleanUpStripeProducts($orgRenewalModel, $stripeSecretKey); */
            $orgRenewalModelData = $this->getOrgRenewalDevicesWithDeletedAttr($request->orgId, $serviceProvider);
            return response()->json(["msg" => "Device Plan Deleted Successfully.", 'success' => true, 'orgRenewalModelData' => $orgRenewalModelData], 200);
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }


    /** Organization Claims Reasons & portal Message Update */
    public function updateClaimReasonsPortalMsg(Request $request, $id)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate the request */
            $validator = Validator::make($request->all(), [
                'claim_reasons_data' => 'required|array',
                // 'claim_reasons_data.*.claim_reason_id' => 'required|exists:claim_reasons,id,active,1,service_provider_id,' . $serviceProvider,
                'org_type' => 'required|in:1,2',
                'portalStatus' => 'required|in:0,1',
                'portalMsg' => 'nullable|string|max:500',
            ], [
                'portalStatus.in' => 'Portal status must be either 0 or 1.',
                'portalMsg.string' => 'Portal message must be a string.',
                'portalMsg.max' => 'Portal message may not be greater than 500 characters.',
                'org_type.required' => 'Please select organization type.',
                // 'claim_reasons_data.*.claim_reason_id.exists' => 'Claim reason ID does not exist.',
                // 'claim_reasons_data.*.claim_reason_id.unique' => 'Please provide a unique claim reason.',
                // 'claim_reasons_data.required' => 'Claim reasons data is required.',
            ]);
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            /* Now check for duplicates with index info */
            /* $ids = [];
            $duplicateErrors = [];

            foreach ($request->claim_reasons_data as $index => $item) {
                $claimReasonId = $item['claim_reason_id'];
                if (in_array($claimReasonId, $ids)) {
                    $duplicateErrors["claim_reasons_data.$index.claim_reason_id"] = ["Duplicate claim reason is not allowed."];
                } else {
                    $ids[] = $claimReasonId;
                }
            }

            if (!empty($duplicateErrors)) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $duplicateErrors
                ], 200);
            } */
            $organization = Organization::where('id', $id)
                ->where('service_provider_id', $serviceProvider)
                ->where('active', 1)
                ->first();
            if (empty($organization)) {
                return response()->json(["msg" => "Organization not found.", "success" => false], 200);
            }
            $organization->update([
                'org_type' => $request->org_type,
                'portal_status' => $request->portalStatus,
                'close_portal_message' => $request->portalMsg,
            ]);
            if (!empty($request->claim_reasons_data)) {
                /* Delete existing Claim Reasons before adding new ones */
                OrganizationClaimReason::where('org_id', $organization->id)->where('active', 1)->where('service_provider_id', $serviceProvider)->delete();
                /* Loop through claim_reasons_data */
                foreach ($request->claim_reasons_data as $reasonId) {

                    OrganizationClaimReason::create([
                        'org_id' => $organization->id,
                        'service_provider_id' => $serviceProvider,
                        'claim_reason_id' => $reasonId,
                    ]);


                    // $data = [
                    //     'claim_reason_id' => $reasonId['claim_reason_id'],
                    //     'active' => (int)$reasonId['active'], /* Force integer conversion */
                    // ];
                    // if (!empty($reasonId['id'])) {
                    //     /* Update existing org-claim-reason */
                    //     $orgClaimReason = OrganizationClaimReason::where('id', $reasonId['id'])
                    //         ->where('org_id', $organization->id)
                    //         ->where('service_provider_id', $serviceProvider)
                    //         ->first();

                    //     if ($orgClaimReason) {
                    //         $orgClaimReason->update($data);
                    //     }
                    // } else {
                    //     /* Create new sub-org */
                    //     $data['org_id'] = $organization->id;
                    //     $data['service_provider_id'] = $serviceProvider;
                    //     $orgClaimReason = OrganizationClaimReason::create($data);
                    // }
                }
            }
            $updatedClaimIds = $this->getOrgClaimReasons($organization->id, $serviceProvider);
            $organizationData = [
                'org_type' => $organization->org_type,
                'portal_status' => $organization->portal_status,
                'close_portal_message' => $organization->close_portal_message,
            ];
            return response()->json(["msg" => "Claim & Additional Details updated successfully.", 'updatedClaimIds' => $updatedClaimIds, 'organizationData' => $organizationData, "success" => true], 200);
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Hard Delete Org Claim Reasons */
    public function orgClaimReasonDelete(Request $request, $id)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate the request */
            $validator = Validator::make($request->all(), [
                'orgId' => 'required|exists:organizations,id,service_provider_id,' . $serviceProvider,
            ], [
                'orgId.required' => 'Organization is required.',
                'orgId.exists' => 'Organization is invalid.',
            ]);
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $orgClaimReason = OrganizationClaimReason::findOrFail($id);
            $orgClaimReason->delete();
            $orgClaimReasonData = $this->getOrgClaimReasons($request->orgId, $serviceProvider);
            return response()->json(["msg" => "Claim Reason Deleted Successfully.", 'success' => true, 'orgClaimReasonData' => $orgClaimReasonData], 200);
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Organization additional details update */
    public function updateAdditionalDetails(Request $request, $id)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate the request */
            $validator = Validator::make($request->all(), [
                'repair_enabled' => 'required|in:0,1',
                'can_edit_device' => 'required|in:0,1',
                'allow_parents_claim' => 'required|in:0,1',
                'additional_instructions' => 'nullable|string|max:500',
            ], [
                'repair_enabled.in' => 'Repair enabled must be either 0 or 1.',
                'can_edit_device.in' => 'Can edit device must be either 0 or 1.',
                'allow_parents_claim.in' => 'Allow parents claim must be either 0 or 1.',
                'additional_instructions.string' => 'Additional instructions must be a string.',
                'additional_instructions.max' => 'Additional instructions may not be greater than 500 characters.',
            ]);
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }
            $organization = Organization::where('id', $id)
                ->where('service_provider_id', $serviceProvider)
                ->where('active', 1)
                ->first();
            if (empty($organization)) {
                return response()->json(["msg" => "Organization not found.", "success" => false], 200);
            }
            $organization->update([
                'repair_enabled' => $request->repair_enabled,
                'can_edit_device' => $request->can_edit_device,
                'allow_parents_claim' => $request->allow_parents_claim,
                'additional_instructions' => $request->additional_instructions,
            ]);
            $orgData = [
                'repair_enabled' => $organization->repair_enabled,
                'can_edit_device' => $organization->can_edit_device,
                'allow_parents_claim' => $organization->allow_parents_claim,
                'additional_instructions' => $organization->additional_instructions,
            ];
            return response()->json(["msg" => "Organization additional details updated successfully.", 'orgData' => $orgData, "success" => true], 200);
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /**
     * Cancel Routes
     */

    /** Cancel Allowed Devices */
    public function cancelAllowedDevices($id)
    {
        try {
            $serviceProvider = Session::get('service_provider');
            /* Get organization */
            $organization = Organization::where([
                'id' => $id,
                'service_provider_id' => $serviceProvider,
                'active' => 1
            ])->first();
            /* Return data */
            $orgAllowedModelData = $this->getOrgAllowedDevicesWithDeletedAttr($organization->id, $serviceProvider);
            return response()->json(["msg" => "Device Allowed Plans Data.", 'success' => true, 'orgAllowedModelData' => $orgAllowedModelData], 200);
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }
    /** Cancel Renewal Devices */
    public function cancelRenewalDevices($id)
    {
        try {
            $serviceProvider = Session::get('service_provider');
            // Get organization
            $organization = Organization::where([
                'id' => $id,
                'service_provider_id' => $serviceProvider,
                'active' => 1
            ])->first();
            /* Return data */
            $orgRenewalModelData = $this->getOrgRenewalDevicesWithDeletedAttr($organization->id, $serviceProvider);
            return response()->json(["msg" => "Device Renewal Plans Data.", 'success' => true, 'orgRenewalModelData' => $orgRenewalModelData], 200);
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }


    /** Cancel SubOrg */
    public function cancelSubOrg($id)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            $organization = Organization::where('id', $id)->where('service_provider_id', $serviceProvider)->where('active', 1)->first();
            if (empty($organization)) {
                return response()->json(["msg" => "Organization not found.", "success" => false], 200);
            }
            $updatedSubOrgs = $this->getSubOrgWithDeletedAttr($organization->id, $serviceProvider);
            return response()->json(["msg" => "Sub-Organization Details.", "sub_organizations" => $updatedSubOrgs, "success" => true], 200);
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }
    /** Cancel ClaimReasons PortalMsg */
    public function cancelClaimReasonsPortalMsg($id)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            $organization = Organization::where('id', $id)->where('service_provider_id', $serviceProvider)->where('active', 1)->first();
            if (empty($organization)) {
                return response()->json(["msg" => "Organization not found.", "success" => false], 200);
            }
            $updatedClaimIds = $this->getOrgClaimReasons($organization->id, $serviceProvider);
            $organizationData = [
                'org_type' => $organization->org_type,
                'portal_status' => $organization->portal_status,
                'close_portal_message' => $organization->close_portal_message,
            ];
            return response()->json(["msg" => "Claim & Additional Details.", 'updatedClaimIds' => $updatedClaimIds, 'organizationData' => $organizationData, "success" => true], 200);
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }
    /** Cancel Additional Details */
    public function cancelAdditionalDetails($id)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            $organization = Organization::where('id', $id)->where('service_provider_id', $serviceProvider)->where('active', 1)->first();
            if (empty($organization)) {
                return response()->json(["msg" => "Organization not found.", "success" => false], 200);
            }
            $orgData = [
                'repair_enabled' => $organization->repair_enabled,
                'can_edit_device' => $organization->can_edit_device,
                'allow_parents_claim' => $organization->allow_parents_claim,
                'additional_instructions' => $organization->additional_instructions,
            ];
            return response()->json(["msg" => "Organization additional details.", 'orgData' => $orgData, "success" => true], 200);
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }



    /** Sub Organizations With deleted attribute  */
    private function getSubOrgWithDeletedAttr($id, $serviceProvider)
    {
        $subOrgData = SubOrganization::select('name', 'id', 'active')->where('org_id', $id)->where('service_provider_id', $serviceProvider)->get();
        /* Loop through sub-orgs to check if they exist in org_relationship, device_claims, device_repairs, devices, shipping_supplies,  tables  */
        foreach ($subOrgData as $subOrg) {
            $relationshipExists = OrgRelationship::where('org_id', $subOrg->id)
                ->where('service_provider_id', $serviceProvider)
                ->exists();
            /** Devices */
            $devicesExists =  Device::where('sub_org_id', $subOrg->id)->where('service_provider_id', $serviceProvider)->exists();
            /** Device Claims */
            $deviceClaimsExists = DeviceClaim::where('sub_org_id', $subOrg->id)->where('service_provider_id', $serviceProvider)->exists();
            /** Device Repairs */
            $deviceRepairsExists = DeviceRepair::where('sub_org_id', $subOrg->id)->where('service_provider_id', $serviceProvider)->exists();
            /** Shipping Supplies */
            $shippingSuppliesExists = ShippingSupply::where('sub_org_id', $subOrg->id)->where('service_provider_id', $serviceProvider)->exists();
            /* Add isDeletable attribute */
            if ($relationshipExists || $devicesExists || $deviceClaimsExists || $deviceRepairsExists || $shippingSuppliesExists) {
                $subOrg->isDeletable = false;
            } else {
                $subOrg->isDeletable = true;
            }
        }
        return $subOrgData;
    }

    /** Allowed Devices Data with deleted attribute */
    private function getOrgAllowedDevicesWithDeletedAttr($id, $serviceProvider)
    {
        /** Allowed Models */
        $allowedModelFields = [
            'orgAllowedModels.id',
            'orgAllowedModels.model_id',
            'orgAllowedModels.device_plan_id',
            'orgAllowedModels.coverage_price as allowed_coverage_price',
            'orgAllowedModels.deductible as allowed_deductible_price',
            'orgAllowedModels.expiration_date as allowed_expiration_date',
            'allowedModels.title as allowed_model_name',
            'allowedPlan.plan_name as allowed_plan_name',
            'orgAllowedModels.active',
        ];
        $allowedModels = DB::table('org_allowed_models as orgAllowedModels')
            ->leftJoin('device_models as allowedModels', 'orgAllowedModels.model_id', '=', 'allowedModels.id')
            ->leftJoin('device_plans as allowedPlan', 'orgAllowedModels.device_plan_id', '=', 'allowedPlan.id')
            ->select($allowedModelFields)
            ->where('orgAllowedModels.org_id', $id)
            ->where('orgAllowedModels.service_provider_id', $serviceProvider)
            ->where('allowedModels.service_provider_id', $serviceProvider)
            ->where('allowedPlan.service_provider_id', $serviceProvider)
            ->get();
        /** Loop through allowedModels to check if they exist in device table  */
        foreach ($allowedModels as $model) {
            $devicesExists =  Device::where('org_id', $id)->where('device_model_id', $model->model_id)->where('service_provider_id', $serviceProvider)->exists();
            $model->isDeletable = !$devicesExists;
        }
        return $allowedModels;
    }

    /** Renewal Devices Data with deleted attribute */
    private function getOrgRenewalDevicesWithDeletedAttr($id, $serviceProvider)
    {
        /** Allowed Renewal Models */
        $allowedRenewalModelFields = [
            'orgAllowedRenewalModels.id',
            'orgAllowedRenewalModels.model_id',
            'orgAllowedRenewalModels.device_plan_id',
            'orgAllowedRenewalModels.coverage_price as allowed_renewal_coverage_price',
            'orgAllowedRenewalModels.deductible as allowed_renewal_deductible_price',
            'orgAllowedRenewalModels.expiration_date as allowed_renewal_expiration_date',
            'allowedRenewalModels.title as allowed_renewal_model_name',
            'allowedRenewalPlan.plan_name as allowed_renewal_plan_name',
            'orgAllowedRenewalModels.active',
        ];
        $allowedRenewalModels = DB::table('org_allowed_renewal_models as orgAllowedRenewalModels')
            ->leftJoin('device_models as allowedRenewalModels', 'orgAllowedRenewalModels.model_id', '=', 'allowedRenewalModels.id')
            ->leftJoin('device_plans as allowedRenewalPlan', 'orgAllowedRenewalModels.device_plan_id', '=', 'allowedRenewalPlan.id')
            ->select($allowedRenewalModelFields)
            ->where('orgAllowedRenewalModels.org_id', $id)
            ->where('orgAllowedRenewalModels.service_provider_id', $serviceProvider)
            ->where('allowedRenewalModels.service_provider_id', $serviceProvider)
            ->where('allowedRenewalPlan.service_provider_id', $serviceProvider)
            ->get();
        /** Loop through allowedRenewalModels to check if they exist in device table  */
        foreach ($allowedRenewalModels as $model) {
            $devicesExists =  Device::where('org_id', $id)->where('device_model_id', $model->model_id)->where('service_provider_id', $serviceProvider)->exists();
            $model->isDeletable = !$devicesExists;
        }
        return $allowedRenewalModels;
    }

    /** Organization Claim Reasons With deleted attribute */
    private function getOrgClaimReasons($id, $serviceProvider)
    {
        /*  $orgClaims = DB::table('organization_claim_reasons as orgClaimReason')
            ->leftJoin('claim_reasons as claimReason', 'orgClaimReason.claim_reason_id', '=', 'claimReason.id')
            ->select('orgClaimReason.id', 'claimReason.claim_reason_name', 'orgClaimReason.active', 'claimReason.id as claim_reason_id')
            ->where('orgClaimReason.org_id', $id)
            ->where('orgClaimReason.service_provider_id', $serviceProvider)
            ->where('claimReason.service_provider_id', $serviceProvider)->get(); */
        /** Loop through orgClaims to check if they exist in organization_claim_reasons table  */
        /*   foreach ($orgClaims as $orgClaim) {
            $organizationClaimReasonsExists =  OrganizationClaimReason::where('org_id', $id)->where('claim_reason_id', $orgClaim->id)->exists();
            $orgClaim->isDeletable = !$organizationClaimReasonsExists;
        }
        return $orgClaims; */
        $orgClaims = DB::table('organization_claim_reasons as orgClaimReason')
            ->leftJoin('claim_reasons as claimReason', 'orgClaimReason.claim_reason_id', '=', 'claimReason.id')
            ->select('claimReason.claim_reason_name', 'claimReason.id as claim_reason_id')
            ->where('orgClaimReason.org_id', $id)
            ->where('orgClaimReason.service_provider_id', $serviceProvider)
            ->where('claimReason.service_provider_id', $serviceProvider)->get();
        return $orgClaims;
    }


    /** Get Sub Organization Query */
    private static function getSubOrganizationQuery($where = [], $fields = [])
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $query = SubOrganization::where('service_provider_id', $serviceProvider)->where('active', 1);
        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select('id', 'name');
        }
        if (!empty($where) && is_array($where)) {
            foreach ($where as $field => $value) {
                if (is_array($value)) {
                    foreach ($value as $secondField => $secondValue) {
                        $query->$field($secondField, $secondValue);
                    }
                }
            }
        }
        $query->orderBy('name', 'asc');
        return $query;
    }

    /** Get Sub Organizations */
    public function getSubOrganizations(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            $validator = Validator::make($request->all(), [
                'orgId' => 'required|exists:organizations,id,active,1,service_provider_id,' . $serviceProvider,
            ]);
            /* If validation fails */
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }

            if (!empty($request->orgId)) {
                $orgId = $request->orgId;
                if (!is_numeric($orgId)) {
                    return response()->json(['success' => false, 'msg' => "Invalid Organization ID"], 200);
                } else {

                    /* Getting service provider id from session */
                    $serviceProvider = Session::get('service_provider');
                    /** Fetch Sub Organizations based on Organization ID and Service Provider ID */
                    // $subOrgData = SubOrganization::select('id', 'name')->where('org_id', $orgId)->where('service_provider_id', $serviceProvider)->where('active', 1)->get();
                    $subOrgWhere['where']['org_id'] = $orgId;
                    $subOrgData = self::getSubOrganizationQuery($subOrgWhere)->get();
                    if (empty($subOrgData)) {
                        return response()->json([
                            'success' => false,
                            'msg' => "No Sub Organizations are found in this Organization"
                        ], 200);
                    } else {
                        return response()->json(['success' => true, 'subOrgData' => $subOrgData], 200);
                    }
                }
            } else {
                return response()->json(['success' => false, 'msg' => "Organization ID is required"], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => "Failed to get sub organizations"], 200);
        }
    }

    /** Get Organization by name for multi-select */
    public function getOrgByName(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string',
                'orgId' => 'nullable',
            ]);
            /* If validation fails */
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }

            $name = $request->name;
            $org =  $request->orgId;
            $orgData = [];
            if (!empty($org) && !empty($name)) {
                $orgWhere['where']['name'] = ['LIKE', "%{$name}%"];
                $orgData = self::getAllOrganizations($orgWhere);
            } elseif (!empty($org)) {
                $orgWhere['where']['id'] = $org;
                $orgData = self::getAllOrganizations($orgWhere);
            } elseif (!empty($name)) {
                /** Fetch Organization based on Organization name */
                $orgWhere['where']['name'] = ['LIKE', "%{$name}%"];
                $orgData = self::getAllOrganizations($orgWhere);
            }
            if (empty($orgData)) {
                return response()->json(['success' => false, 'msg' => "No Organizations are found"], 200);
            } else {
                return response()->json(['success' => true, 'orgData' => $orgData], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => "Failed to get organizations"], 200);
        }
    }

    /** Common private function to get the organization data */
    private static function getOrganizations($where = [], $fields = [])
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $query = Organization::where('active', 1)->where('service_provider_id', $serviceProvider);
        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select('id', 'name', 'created_at', 'org_type', 'org_slug', 'org_link');
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

    /**get all organizations without pagination */
    public static function getAllOrganizations($where = [], $fields = [])
    {
        $data = self::getOrganizations($where, $fields)->orderBy('name', 'asc');
        return $data->get();
    }

    /** Get all organization with pagination. */
    public static function getPaginatedOrganizations($limit = 20, $where = [], $fields = [])
    {
        $data = self::getOrganizations($where, $fields);
        $limit = (is_numeric($limit) && $limit > 0) ? $limit : 20;
        return $data->paginate($limit);
    }


    /* For Filter's */
    private function getFilter(Request $request)
    {
        $where = [];
        /* Search By Org Name*/
        if (!empty($request->name)) {
            $where = [
                'where' => [
                    'name' => ['LIKE', "%{$request->name}%"],
                ]
            ];
        }
        return $where;
    }

    private function cleanUpStripeProducts($model, $stripeApiKey)
    {
        if (!empty($model->stripe_product_id)) {
            /* Set the Stripe secret key */
            // Stripe::setApiKey(config('cashier.secret'));
            Stripe::setApiKey($stripeApiKey);
            /* Step 1: Deactivate all prices for the product */
            $hasMore = true;
            $startingAfter = null;
            while ($hasMore) {
                $params = [
                    'product' => $model->stripe_product_id,
                    'limit' => 100,
                ];
                if ($startingAfter) {
                    $params['starting_after'] = $startingAfter;
                }
                $prices = Price::all($params);
                foreach ($prices->data as $price) {
                    /* Deactivate the price */
                    Price::update($price->id, ['active' => false]);
                }
                $hasMore = $prices->has_more;
                $startingAfter = end($prices->data)?->id;
            }
            /* Step 2: Deactivate the product itself */
            Product::update($model->stripe_product_id, ['active' => false]);
            /* Step 3: Delete the model from your DB */
            $model->delete();
        } else {
            $model->delete();
        }
    }

    /** Total Org Count */
    private function totalOrgs()
    {
        $serviceProvider = $serviceProvider = Session::get('service_provider');
        $total = Organization::where('active', 1)->where('service_provider_id', $serviceProvider)->count();
        return $total;
    }
}
