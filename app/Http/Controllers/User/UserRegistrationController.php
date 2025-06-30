<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Admin\MediaLibraryController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Controller;
use App\Models\OrgRelationship;
use App\Models\User;
use App\Models\User\ShippingAddress;
use App\Models\UserMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UserRegistrationController extends Controller
{
    
    /* User Registration List */
    public function index(Request $request)
    {
        $where = $this->userListFilter($request);
        $userData = self::paginateGetUsers(20, $where);
        $pagination = [
            'total' => $userData->total(),
            'per_page' => $userData->perPage(),
            'current_page' => $userData->currentPage(),
            'last_page' => $userData->lastPage(),
            'from' => $userData->firstItem(),
            'to' => $userData->lastItem()
        ];
        $serviceProvider = Session::get('service_provider');
        /* Auth User */
        $authUser = auth()->user();
        if(auth()->user()->role_id){
            $authUser['role_for'] = auth()->user()->roleFor();
        }
        $where = [];
        $org = null;
        $subOrg = null;
        $orgRelationship = DB::table('org_relationship')
            ->select('org_id', 'parent_org_id')
            ->where('user_id', $authUser['id'])
            ->where('service_provider_id', $serviceProvider)
            ->first();
        if ($authUser['role_for'] == 'is_org_it_hod') {
            $where = [
                'whereIn' => [
                    'roles.role_for' => ['is_org_it_hod','is_org_it_director']
                ],
            ];
            $orgData = DB::table('organizations')->select('id', 'name')
                ->where('id', $orgRelationship->org_id)
                ->where('active', 1)
                ->where('service_provider_id', $serviceProvider)
                ->first();
        }
        if ($authUser['role_for'] == 'is_org_it_director') {
            $where['where']['roles.role_for'] = 'is_org_it_director';
            if (isset($orgRelationship->parent_org_id)) {
                $orgData = DB::table('organizations')->select('id', 'name')
                    ->where('id', $orgRelationship->parent_org_id)
                    ->where('active', 1)
                    ->where('service_provider_id', $serviceProvider)
                    ->first();
            } else {
                $orgData = DB::table('organizations')->select('id', 'name')->where('id', $orgRelationship->org_id)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();

            }
        }

        $roles = RoleController::getRolesForDropdown($where);

        if (!empty($request->page)) {
            return response()->json(["userData" => $userData, "pagination" => $pagination, "msg" => "User Data", "success" => true], 200);
        } else {
            return view('user.userRegistration.index', compact('userData', 'pagination', 'roles', 'orgData'));
        }
    }

    /** Check Email */
    public function checkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,NULL,id,active,1,service_provider_id,' . Session::get('service_provider'),
        ], [
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'Email Address already exist, Please try with different Email Address.',
        ]);

        if ($validator->fails()) {
            return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
        }

        return response()->json(["msg" => "Email verified", "success" => true], 200);
    }

    public function store(Request $request)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');
            /* Validate request data */
            $validator = Validator::make($request->all(), [
                'firstName' => 'required|string|max:50',
                'lastName' => 'nullable|string|max:50',
                'email' => 'required|string|email|max:255|unique:users,email,NULL,id,active,1,service_provider_id,' . $serviceProvider,
                'phone' => 'required|regex:/^[0-9]{10}$/|unique:users,phone,NULL,id,active,1,service_provider_id,' . $serviceProvider,
                'password' => 'required|string|min:8',
                'confirmPassword' => 'required|string|min:8|same:password',
                'userRole' => 'required|exists:roles,id,active,1,service_provider_id,' . $serviceProvider,
                'organization' => 'required_if:userRoleFor,is_org_it_hod, is_org_it_director|exists:organizations,id,active,1,service_provider_id,' . $serviceProvider,
                'subOrganization' => 'nullable|exists:sub_organizations,id,active,1,service_provider_id,' . $serviceProvider,
                'streetAddress' => 'required|string|max:100',
                'addressLine2' => 'nullable|string|max:100',
                'country' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'city' => 'required|string|max:100',
                'zipCode' => 'required|string|regex:/^[0-9]{5}$/',
                'profileImage' => 'nullable|image|mimes:jpg,png,jpeg,svg|max:2048',
            ], [
                'firstName.required' => 'First name is required.',
                'firstName.string' => 'First name must be a string.',
                'firstName.max' => 'First name should not exceed 50 characters.',
                // 'firstName.unique' => 'First name already exists.',
                'lastName.required' => 'Last name is required.',
                'lastName.string' => 'Last name must be a string.',
                'lastName.max' => 'Last name should not exceed 50 characters.',
                'email.required' => 'Email is required.',
                'email.string' => 'Email must be a string.',
                'email.email' => 'Email must be a valid email.',
                'email.max' => 'Email should not exceed 255 characters.',
                'email.unique' => 'Email already exists.',
                'phone.required' => 'Phone number is required.',
                'phone.string' => 'Phone number must be a string.',
                'phone.regex' => 'Phone number must be a 10-digit number.',
                'phone.unique' => 'Phone number already exists.',
                'password.required' => 'Password is required.',
                'password.string' => 'Password must be a string.',
                'password.min' => 'Password must be at least 8 characters long.',
                'password.confirmed' => 'Password and confirmation do not match.',
                'confirmPassword.required' => 'Confirm password is required.',
                'confirmPassword.string' => 'Confirm password must be a string.',
                'confirmPassword.min' => 'Confirm password must be at least 8 characters long.',
                'confirmPassword.same' => 'Password and confirmation do not match.',
                'userRole.required' => 'User role is required.',
                'userRole.exists' => 'User role does not exist.',
                'organization.required_if' => 'Organization is required.',
                'organization.exists' => 'Organization does not exist.',
                'userRole.active' => 'User role is not active.',
                'subOrganization.exists' => 'Sub organization does not exist.',
                'streetAddress.required' => 'Street address is required.',
                'streetAddress.string' => 'Street address must be a string.',
                'streetAddress.max' => 'Street address should not exceed 100 characters.',
                'addressLine2.string' => 'Address line 2 must be a string.',
                'addressLine2.max' => 'Address line 2 should not exceed 100 characters.',
                'country.required' => 'Country is required.',
                'country.string' => 'Country must be a string.',
                'country.max' => 'Country should not exceed 100 characters.',
                'state.required' => 'State is required.',
                'state.string' => 'State must be a string.',
                'city.required' => 'City is required.',
                'city.string' => 'City must be a string.',
                'city.max' => 'City should not exceed 100 characters.',
                'zipCode.required' => 'Zip code is required.',
                'zipCode.string' => 'Zip code must be a string.',
                'zipCode.regex' => 'Zip code must be a 5-digit number.',
                'profileImage.image' => 'Profile image must be an image.',
                'profileImage.mimes' => 'Profile image must be in jpg, png, jpeg or svg format.',
                'profileImage.max' => 'Profile image should not exceed 2MB.',
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
            if ($request->hasFile('profileImage')) {
                $image = $request->file('profileImage');
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
                /* Store the file in storage/app/public/images/users_profile_image */
                // $imagePath = $image->storeAs('device_families', $imageName, 'public');
                $imagePath = $image->move(public_path('images/users_profile_image/'), $imageName);
                if (empty($imagePath)) {
                    return response()->json(["msg" => "Failed to upload image.", "success" => false], 200);
                }
                /* create and get (id) media library */
                $mediaRequest = [
                    'fileName' => $imageName,
                    'filePath' => '/images/users_profile_image/',
                    'fileType' => $imageExtension,

                ];
                $mediaLibrary = MediaLibraryController::createMediaLibrary($mediaRequest);
                /* Failed to create media library record */
                if (empty($mediaLibrary)) {
                    return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                }
                $mediaId = $mediaLibrary;
            }
            $fullName =  $request->firstName . ' ' . $request->lastName;
            /** Create a new User record */
            $user = User::create([
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'full_name' => $fullName,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role_id' => $request->userRole,
                'profile_img_media_id' => $mediaId,
                'service_provider_id' => $serviceProvider,
            ]);
            if ($user->wasRecentlyCreated) {
                $metaKey = '';
                $is_org_subscriber = 0;
                $roleWhere = [
                    'where' => [
                        'id' => $user->role_id
                    ]
                ];
                $roleData =  RoleController::firstRole($roleWhere);
                if (empty($roleData)) {
                    return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                }
                if ($roleData->role_for == 'is_org_it_hod') {
                    $metaKey = 'org_it_hod';
                } elseif ($roleData->role_for == 'is_org_it_director') {
                    $metaKey = 'org_it_director';
                } else {
                    $metaKey = '';
                }
                $userMetaData = UserMeta::create([
                    'user_id' => $user->id,
                    'meta_key' => $metaKey,
                    'meta_value' => 'yes',
                    'service_provider_id' => $serviceProvider,
                ]);
                $userRoleFor = $request->userRoleFor;
                if ($userRoleFor == 'is_org_it_hod' || $userRoleFor == 'is_org_it_director') {
                    $orgRelationData = OrgRelationship::create([
                        'user_id' => $user->id,
                        'org_id' => isset($request->organization) && isset($request->subOrganization) ? $request->subOrganization : $request->organization,
                        'parent_org_id' => isset($request->organization) && isset($request->subOrganization) ? $request->organization : null,
                        'is_org_subscriber' => $is_org_subscriber,
                        'service_provider_id' => $serviceProvider,
                    ]);
                }
                $user_addressData = ShippingAddress::create([
                    'user_id'           => $user->id,
                    'phone'             => $user->phone,
                    'street_address'    => $request->streetAddress,
                    'address_line_2'    => $request->addressLine2 ?? null,
                    'country'           => $request->country,
                    'state'             => $request->state,
                    'city'              => $request->city,
                    'zip'               => $request->zipCode,
                    'service_provider_id' => $serviceProvider,
                ]);
                return response()->json([
                    "msg" => "User Registered successfully.",
                    "success" => true,
                ], 200);
            } else {
                return response()->json(["msg" => "User Registration failed.", "success" => false], 200);
            }
        } catch (\Exception $e) {

            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    public function getUserDataById(Request $request)
    {
        /* Auth User */
        $where = [];
        $user = null;
        if (!empty($request->userId)) {
            $where['where']['users.id'] = $request->userId;
            // $fields = [];
            $fields = [
                'users.id',
                'users.full_name',
                'users.email',
                'users.phone',
                'users.created_at',
                'userRole.name as user_role_name',
                'userAddress.street_address as street_address',
                'userAddress.address_line_2 as address_line_2',
                'userAddress.city as city',
                'userAddress.state as state',
                'userAddress.zip as zip',
                'userAddress.country as country',
                'userMeta.meta_key',
                'orgRelationship.org_id as org',
                'orgRelationship.parent_org_id as parent_org',
            ];
            $user = self::getFullUserDetailsQuery($where, $fields)->first();
            if ($user->org && $user->parent_org) {
                $user->org = DB::table('sub_organizations')->where('id', $user->org)->value('name');
                $user->parent_org = DB::table('organizations')->where('id', $user->parent_org)->value('name');
            } else if ($user->org && !$user->parent_org) {
                $user->org = DB::table('organizations')->where('id', $user->org)->value('name');
            }
        } else {
            $user = self::getUsersQuery($where)->latest()->first();
        }

        if (!empty($user)) {
            return response()->json(["viewData" => $user, "msg" => "User Data.", "success" => true], 200);
        } else {
            return response()->json(["msg" => "User not found.", "success" => false], 200);
        }
    }

    /** Show the form for editing the specified resource. */
    public function edit(string $id)
    {
        if (!empty($id)) {
            $where = [
                'where' => [
                    'users.id' => $id,
                ]
            ];
            $fields = [
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.phone as phone',
                'users.password',
                'users.role_id',
                // 'users.profile_img_media_id',
                'media.file_name as profile_image_name',
                'media.file_path as profile_image_path',
                'userAddress.street_address',
                'userAddress.address_line_2',
                'userAddress.country',
                'userAddress.state',
                'userAddress.city',
                'userAddress.zip',
                'orgRelationship.org_id',
                'orgRelationship.parent_org_id',
                'orgRelationship.is_org_subscriber',
                // 'userMeta.meta_key',
            ];
            $editData = self::getFullUserDetailsQuery($where, $fields)
                // ->leftJoin('shipping_address as userAddress', 'users.id', '=', 'userAddress.user_id')
                ->leftJoin('media_libraries as media', 'users.profile_img_media_id', '=', 'media.id')
                // ->leftJoin('user_meta as userMeta', 'users.id', '=', 'userMeta.user_id')
                // ->leftJoin('org_relationship as orgRelationship', 'users.id', '=', 'orgRelationship.user_id')
                ->first();
            if (!empty($editData)) {
                $roles = RoleController::getRolesForDropdown();
                return view('admin.userregistrationmaster.update', compact('editData', 'roles'));
            } else {
                return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
            }
        } else {
            return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
        }
    }

    /** Get Users Query */
    private static function getUsersQuery($where = [], $fields = [])
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $query = DB::table('users')
            ->leftJoin('roles as userRole', 'users.role_id', '=', 'userRole.id')
            ->where('users.service_provider_id', $serviceProvider)
            ->where('users.active', 1);
        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.full_name',
                'users.email',
                'users.phone',
                'userRole.name as user_role_name',
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
        return $query->orderBy('users.created_at', 'desc');
    }

    /** Get Users with Pagination */
    public static function paginateGetUsers($limit = 20, $where = [], $fields = [])
    {
        $data = self::getUsersQuery($where, $fields);
        $limit = (is_numeric($limit) && $limit > 0) ? $limit : 20;
        return $data->paginate($limit);
    }

    /** Chcek the user email(existance) */
    public function checkUserEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
        ], [
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'Email Address already exist, Please try with different Email Address.',
        ]);

        if ($validator->fails()) {
            return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
        }

        return response()->json(["msg" => "Email verified", "success" => true], 200);
    }

    private static function getFullUserDetailsQuery($where = [], $fields = [])
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $query = DB::table('users')
            ->leftJoin('roles as userRole', 'users.role_id', '=', 'userRole.id')
            ->leftJoin('user_meta as userMeta', 'users.id', '=', 'userMeta.user_id')
            ->leftJoin('shipping_address as userAddress', 'users.id', '=', 'userAddress.user_id')
            ->leftJoin('org_relationship as orgRelationship', 'users.id', '=', 'orgRelationship.user_id')
            ->where('users.service_provider_id', $serviceProvider)
            ->where('users.active', 1);
        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.full_name',
                'users.email',
                'users.phone',
                'userRole.name as user_role_name',
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
        return $query->orderBy('users.created_at', 'desc');
    }

    /** To update the user */
    public function update(Request $request, $id)
    {
        try {
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');

            /* Validate request data */
            $validator = Validator::make($request->all(), [
                'firstName' => 'required|string|max:50',
                'lastName' => 'nullable|string|max:50',
                'email' => 'required|string|email|max:255|unique:users,email,' . $id . ',id,active,1,service_provider_id,' . $serviceProvider,
                'phone' => 'required|regex:/^[0-9]{10}$/|unique:users,phone,' . $id . ',id,active,1,service_provider_id,' . $serviceProvider,
                'password' => 'nullable|string|min:8',
                'confirmPassword' => 'nullable|required_with:password|string|min:8|same:password',
                'userRole' => 'required|exists:roles,id,active,1,service_provider_id,' . $serviceProvider,
                'organization' => 'required_if:userRoleFor,is_org_it_hod, is_org_it_director, is_org_subscriber|exists:organizations,id,active,1,service_provider_id,' . $serviceProvider,
                // 'subOrganization' => 'required_if:userRoleFor,is_org_it_director, is_org_subscriber|exists:sub_organizations,id,active,1,service_provider_id,' . $serviceProvider,
                'subOrganization' => 'nullable|exists:sub_organizations,id,active,1,service_provider_id,' . $serviceProvider,
                'streetAddress' => 'required|string|max:100',
                'addressLine2' => 'nullable|string|max:100',
                'country' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'city' => 'required|string|max:100',
                'zipCode' => 'required|string|regex:/^[0-9]{5}$/',
                'profileImage' => 'nullable|image|mimes:jpg,png,jpeg,svg|max:2048',
            ], [
                'firstName.required' => 'First name is required.',
                'firstName.string' => 'First name must be a string.',
                'firstName.max' => 'First name should not exceed 50 characters.',
                'lastName.required' => 'Last name is required.',
                'lastName.string' => 'Last name must be a string.',
                'lastName.max' => 'Last name should not exceed 50 characters.',
                'email.required' => 'Email is required.',
                'email.string' => 'Email must be a string.',
                'email.email' => 'Email must be a valid email.',
                'email.max' => 'Email should not exceed 255 characters.',
                'email.unique' => 'Email already exists.',
                'phone.required' => 'Phone number is required.',
                'phone.regex' => 'Phone number must be a 10-digit number.',
                'phone.unique' => 'Phone number already exists.',
                'password.required' => 'Password is required.',
                'password.string' => 'Password must be a string.',
                'password.min' => 'Password must be at least 8 characters long.',
                'password.confirmed' => 'Password and confirmation do not match.',
                'confirmPassword.required' => 'Confirm password is required.',
                'confirmPassword.string' => 'Confirm password must be a string.',
                'confirmPassword.min' => 'Confirm password must be at least 8 characters long.',
                'confirmPassword.same' => 'Password and confirmation do not match.',
                'userRole.required' => 'User role is required.',
                'userRole.exists' => 'User role does not exist.',
                'organization.required_if' => 'Organization is required.',
                'organization.exists' => 'Organization does not exist.',
                'userRole.active' => 'User role is not active.',
                'subOrganization.exists' => 'Sub organization does not exist.',
                'subOrganization.required_if' => 'Sub organization is required.',
                'streetAddress.required' => 'Street address is required.',
                'streetAddress.string' => 'Street address must be a string.',
                'streetAddress.max' => 'Street address should not exceed 100 characters.',
                'addressLine2.string' => 'Address line 2 must be a string.',
                'addressLine2.max' => 'Address line 2 should not exceed 100 characters.',
                'country.required' => 'Country is required.',
                'country.string' => 'Country must be a string.',
                'country.max' => 'Country should not exceed 100 characters.',
                'state.required' => 'State is required.',
                'state.string' => 'State must be a string.',
                'city.required' => 'City is required.',
                'city.string' => 'City must be a string.',
                'city.max' => 'City should not exceed 100 characters.',
                'zipCode.required' => 'Zip code is required.',
                'zipCode.string' => 'Zip code must be a string.',
                'zipCode.regex' => 'Zip code must be a 5-digit number.',
                'profileImage.image' => 'Profile image must be an image.',
                'profileImage.mimes' => 'Profile image must be in jpg, png, jpeg or svg format.',
                'profileImage.max' => 'Profile image should not exceed 2MB.',
            ]);

            /* Check if validation fails */
            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validation errors",
                    "success" => false,
                    "errors" => $validator->errors()
                ], 200);
            }

            /* Getting user data to update */
            $user = User::where('id', $id)->where('service_provider_id', $serviceProvider)->where('active', 1)->first();
            if (empty($user)) {
                return response()->json(["msg" => "User not found.", "success" => false], 200);
            }

            /** Need to update the image also */
            $imageName = null;
            $mediaId = $user->profile_img_media_id ?? null;
            if ($request->hasFile('profileImage')) {
                $image = $request->file('profileImage');
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
                /* Store the file in public/images/users_profile_image */
                $imagePath = $image->move(public_path('images/users_profile_image/'), $imageName);
                if (empty($imagePath)) {
                    return response()->json(["msg" => "Failed to upload image.", "success" => false], 200);
                }
                /* create and get (id) media library */
                $mediaRequest = [
                    'fileName' => $imageName,
                    'filePath' => '/images/users_profile_image/',
                    'fileType' => $imageExtension,

                ];
                $mediaLibrary = MediaLibraryController::createMediaLibrary($mediaRequest);
                /* Failed to create media library record */
                if (empty($mediaLibrary)) {
                    return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                }
                $mediaId = $mediaLibrary;
            }

            $passwordToUpdate = null;

            if (!empty($request->password)) {
                if (Hash::check($request->password, $user->password)) {
                    $validator->getMessageBag()->add('password', 'New password cannot be the same as the current password.');

                    return response()->json([
                        "msg" => "Validation errors",
                        "success" => false,
                        "errors" => $validator->errors()
                    ], 200);
                }

                $passwordToUpdate = Hash::make($request->password);
            }

            /** User full name */
            $fullName =  $request->firstName . ' ' . $request->lastName;
            $userData = [
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'full_name' => $fullName,
                'email' => $request->email,
                'phone' => $request->phone,
                'role_id' => $request->userRole,
                'profile_img_media_id' => $mediaId,
            ];

            if ($passwordToUpdate !== null) {
                $userData['password'] = $passwordToUpdate;
            }

            $userUpdated = $user->update($userData);

            if (!empty($userUpdated)) {

                $userRoleFor = $request->userRoleFor;
                $is_org_subscriber = 0;

                if (empty($userRoleFor)) {
                    return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                }

                if ($userRoleFor == 'is_admin') {
                    $metaKey = 'admin';
                } elseif ($userRoleFor == 'is_org_it_hod') {
                    $metaKey = 'org_it_hod';
                } elseif ($userRoleFor == 'is_org_it_director') {
                    $metaKey = 'org_it_director';
                } elseif ($userRoleFor == 'is_org_subscriber') {
                    $metaKey = 'org_subscriber';
                    $is_org_subscriber = 1;
                } elseif ($userRoleFor == 'is_subscriber') {
                    $metaKey = 'subscriber';
                } else {
                    $metaKey = '';
                }

                /** Updating data in user meta table */
                $userMetaData = UserMeta::where('user_id', $id)->update([
                    'meta_key' => $metaKey,
                    'meta_value' => 'yes',
                    'service_provider_id' => $serviceProvider,
                ]);

                if (empty($userMetaData)) {
                    return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                }

                /** updating data in org-relationship table */
                if ($userRoleFor == 'is_org_it_hod' || $userRoleFor == 'is_org_it_director' || $userRoleFor == 'is_org_subscriber') {
                    $orgRelationData = OrgRelationship::where('user_id', $id)->updateOrCreate([
                        'user_id' => $id,
                    ], [
                        'org_id' => isset($request->organization) && isset($request->subOrganization) ? $request->subOrganization : $request->organization,
                        'parent_org_id' => isset($request->organization) && isset($request->subOrganization) ? $request->organization : null,
                        'is_org_subscriber' => $is_org_subscriber,
                        'service_provider_id' => $serviceProvider,
                    ]);

                    if (empty($orgRelationData)) {
                        return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                    }
                }

                /** Updating data in shipping address table */
                $user_addressData = ShippingAddress::where('user_id', $id)->updateOrCreate([
                    'phone'             => $user->phone,
                    'street_address'    => $request->streetAddress,
                    'address_line_2'    => $request->addressLine2 ?? null,
                    'country'           => $request->country,
                    'state'             => $request->state,
                    'city'              => $request->city,
                    'zip'               => $request->zipCode,
                    'service_provider_id' => $serviceProvider,
                ]);

                if (empty($user_addressData)) {
                    return response()->json(["msg" => "Something went wrong.", "success" => false], 200);
                }

                return response()->json([
                    "msg" => "User Updated successfully.",
                    "success" => true,
                ], 200);
            } else {
                return response()->json(["msg" => "User Registration failed.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** User List Filter */
    private function userListFilter(Request $request)
    {
        $where = [];
        if (!empty($request->search)) {
            if (!empty($request->search)) {
                $where = [
                    'orWhere' => [
                        'users.first_name' => ['LIKE', "%{$request->search}%"],
                        'users.last_name' => ['LIKE', "%{$request->search}%"],
                        'users.full_name' => ['LIKE', "%{$request->search}%"],
                        'users.email' => ['LIKE', "%{$request->search}%"],
                    ]
                ];
            }
        }
        return $where;
    }

    /** Auth User Data get */
    public static function authUserDataList()
    {
        try {
            $serviceProvider = Session::get('service_provider');
            /** Getting current user details */
            $userDetails = Session::get('auth_user');
            $userMeta =  UserMeta::select('meta_key', 'meta_value')->where('user_id', $userDetails->id)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
            $user_shipping_data = ShippingAddress::select('phone', 'street_address', 'address_line_2', 'city', 'state', 'zip', 'country')->where('user_id', $userDetails->id)->where('active', 1)->where('service_provider_id', $serviceProvider)->first();
            /** Creating an to store the required data */
            $userData = [
                'id' => $userDetails->id,
                'first_name' => $userDetails->first_name,
                'last_name' => $userDetails->last_name,
                'email' => $userDetails->email,
                'phone' => $userDetails->phone,
                'meta_key' => $userMeta->meta_key,
                'street_address' => $user_shipping_data->street_address,
                'address_line_2' => $user_shipping_data->address_line_2,
                'city' => $user_shipping_data->city,
                'state' => $user_shipping_data->state,
                'country' => $user_shipping_data->country,
                'zip' => $user_shipping_data->zip,
            ];
            /** Query to get the data */

            if (!empty($userData)) {
                return response()->json(["user_Details" => $userData, "msg" => "User details", "success" => true], 200);
            } else {
                return response()->json(["msg" => "User details not found.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }


    /** Total Users Count */
    private function totalUsers()
    {
        $serviceProvider = $serviceProvider = Session::get('service_provider');
        $total = User::where('active', 1)->where('service_provider_id', $serviceProvider)->count();
        return $total;
    }
}
