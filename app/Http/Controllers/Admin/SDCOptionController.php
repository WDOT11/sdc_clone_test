<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\SDCOption;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class SDCOptionController extends Controller
{

    /** To get the option data */
    public static function getOption($optionKey, $serviceProviderId = null)
    {
        /** Getting service provider id from the session */
        /* $serviceProviderId = Session::get('service_provider'); */
        $default = null;
        if ($serviceProviderId == null) {
            $option = SDCOption::where('option_key', $optionKey)->first();
            return $option ? $option->option_value : $default;
        } else {
            $option = SDCOption::where('option_key', $optionKey)->where('service_provider_id', $serviceProviderId)->first();
            return $option ? $option->option_value : $default;

        }
    }

    /** To add or update the option */
    public static function updateOption($optionKey, $optionValue)
    {
        /** Getting service provider id from the session */
        $serviceProviderId = Session::get('service_provider');
        try{
            /** Add OR Update */
            $optionData = SDCOption::updateOrCreate(
                [
                    'option_key' => $optionKey,
                    'service_provider_id' => $serviceProviderId,
                ],
                [
                    'option_value' => $optionValue,
                    'service_provider_id' => $serviceProviderId,
                ]
            );

            if(!empty($optionData) && $optionData->option_key == $optionKey)
            {
                return $optionData;
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }
}
