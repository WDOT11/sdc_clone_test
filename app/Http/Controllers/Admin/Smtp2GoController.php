<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\SDCOptionController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class Smtp2GoController extends Controller
{
    /** Function to call the stripe setting form*/
    public function index(){
        /** $smtpData */
        $smtpData = $this->getSmtpData();
        return view('admin.smtp2gosettings.index', compact('smtpData'));
    }

    /** Function to store stripe settings */
    public function store(Request $request) {
        try {
             /* Getting service provider id from session */
             $serviceProvider = Session::get('service_provider');
             /* Validate request data */
             $validator = Validator::make($request->all(), [
                'apiKey' => 'required|string',
                'senderEmail' => 'required|email',
             ],[
                'apiKey.required' => 'API key is required.',
                'apiKey.string' => 'API key must be an string.',
                'senderEmail.required' => 'Sender Email is required.',
                'senderEmail.email' => 'Please enter a valid email.',
             ]);
             /* Check if validation fails */
             if ($validator->fails()) {
                 return response()->json([
                     "msg" => "Validation errors",
                     "success" => false,
                     "errors" => $validator->errors()
                 ], 200);
             }
             /* Insert SMTP Records record */
             $apiKey = SDCOptionController::updateOption('sdcsm_smtp2Go_api_key', $request->apiKey);
             $senderEmail = SDCOptionController::updateOption('sdcsm_smtp2Go_sender_email', $request->senderEmail);
             /* If SMTP data updated successfully */
             if ($apiKey && $senderEmail) {
                $apiKey = SDCOptionController::getOption('sdcsm_smtp2Go_api_key', $serviceProvider);
                $senderEmail = SDCOptionController::getOption('sdcsm_smtp2Go_sender_email', $serviceProvider);
                return response()->json(["msg" => "SMTP settings updated successfully.", "success" => true, 'apiKey' => $apiKey, 'senderEmail' => $senderEmail], 200);
             } else {
                /* If SMTP data not updated successfully */
                $apiKey = '';
                $senderEmail = '';
                return response()->json(["msg" => "SMTP settings update failed.", "success" => false, "apiKey" => $apiKey, 'senderEmail' => $senderEmail], 200);
             }

        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Fetch SMTP settings  Data*/
    private function getSmtpData() {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        /* Fetch SMTP settings data */
        $apiKey = SDCOptionController::getOption('sdcsm_smtp2Go_api_key', $serviceProvider);
        $senderEmail = SDCOptionController::getOption('sdcsm_smtp2Go_sender_email', $serviceProvider);

        return [
            'apiKey' => $apiKey,
            'senderEmail' => $senderEmail,
        ];
    }

    /** Send Email */
    // public static function sendEmail($subject, $recipientEmail)
    // {
    //      /* Getting service provider id from session */
    //     $serviceProvider = Session::get('service_provider');
    //     /* Fetch Stripe settings data */
    //     $apiKey = SDCOptionController::getOption('sdcsm_smtp2Go_api_key', $serviceProvider);
    //     $senderEmail = SDCOptionController::getOption('sdcsm_smtp2Go_sender_email', $serviceProvider);
    //     $response = Http::post('https://api.smtp2go.com/v3/email/send', [
    //         'api_key' => $apiKey,
    //         'to' => $recipientEmail,
    //         'sender' => $senderEmail,
    //         'subject' => $subject,
    //         'text_body' => 'This is a plain text body',
    //         'html_body' => '<p>This is an HTML body</p>',
    //     ]);

    // }
}
