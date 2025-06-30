<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\MediaLibraries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class MediaLibraryController extends Controller
{
    /* Common Function for create the new media library */
    public static function createMediaLibrary($mediaRequest)
    {
        /* Auth User Service Provider id*/
        $serviceProvider = Session::get('service_provider');
        /* Validate request data */
        $validator = Validator::make($mediaRequest, [
            'fileName' => 'required',
            'filePath' => 'required',
            'fileType' => 'required',
        ]);
        /**
         * Check if validation fails
         */
        if ($validator->fails()) {
            return response()->json([
                "msg" => "Validation errors",
                "success" => false,
                "errors" => $validator->errors()
            ], 200);
        }
        $mediaLibrary = MediaLibraries::create([
            'file_name' => $mediaRequest['fileName'],
            'file_path' => $mediaRequest['filePath'],
            'file_type' => $mediaRequest['fileType'],
            'service_provider_id' => $serviceProvider
        ]);
        if ($mediaLibrary->wasRecentlyCreated) {
            return $mediaLibrary->id;
        } else{
            return null;
        }
    }
}
