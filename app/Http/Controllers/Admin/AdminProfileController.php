<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminUserRegistrationController;
use App\Http\Controllers\Admin\MediaLibraryController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\User\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AdminProfileController extends Controller
{
    /** Auth User Data  Get*/
    public function authUserData()
    {
        $userData = AdminUserRegistrationController::authUserDataList();
        return $userData;
    }

    /** Profile Page */
    public function index()
    {
        return view('admin.profile.index');
    }

    /** Profile Update */
    public function update(Request $request, string $id)
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
                'profile_img_media_id' => $mediaId,
            ];

            if ($passwordToUpdate !== null) {
                $userData['password'] = $passwordToUpdate;
            }

            $userUpdated = $user->update($userData);

            if (!empty($userUpdated)) {
                /** Updating data in shipping address table */
                $user_addressData = ShippingAddress::updateOrCreate([
                    'user_id'=> $id,
                ],[
                    'street_address'    => $request->streetAddress,
                    'address_line_2'    => $request->addressLine2 ?? null,
                    'country'           => $request->country,
                    'state'             => $request->state,
                    'city'              => $request->city,
                    'zip'               => $request->zipCode,
                    'service_provider_id' => $serviceProvider,
                ]);

                if (empty($user_addressData)) {
                    return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
                }

                return response()->json([
                    "msg" => "Profile Updated Successfully.",
                    "success" => true,
                ], 200);
            } else {
                return response()->json(["msg" => "Profile Updation failed.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }
}
