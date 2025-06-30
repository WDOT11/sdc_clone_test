<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\SDCOptionController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class StripeController extends Controller
{
    /** Function to call the stripe setting form*/
    public function index(){
        /** $stripeData */
        $stripeData = $this->getStripeData();
        return view('admin.stripesettings.index', compact('stripeData'));
    }

    /** Function to store stripe settings */
    public function store(Request $request) {
        try {
             /* Getting service provider id from session */
             $serviceProvider = Session::get('service_provider');
             /* Validate request data */
             $validator = Validator::make($request->all(), [
                'publishableKey' => 'required|string',
                'secretKey' => 'required|string',
                'webhookUrl' => 'required|url',
                'webhookSecret' => 'required|string',
             ],[
                'publishableKey.required' => 'Publishable key is required',
                'secretKey.required' => 'Secret key is required',
                'webhookSecret.required' => 'Webhook secret is required',
                'webhookUrl.required' => 'Webhook URL is required',
             ]);
             /* Check if validation fails */
             if ($validator->fails()) {
                 return response()->json([
                     "msg" => "Validation errors",
                     "success" => false,
                     "errors" => $validator->errors()
                 ], 200);
             }
             /* Insert Stripe Records record */
             $stripePublishKey = SDCOptionController::updateOption('sdcsm_stripe_publishable_key', $request->publishableKey);
             $stripeSecretKey = SDCOptionController::updateOption('sdcsm_stripe_secret_key', $request->secretKey);
             $stripeWebhookSecret = SDCOptionController::updateOption('sdcsm_stripe_webhook_secret', $request->webhookSecret);
             $stripeWebhookUrl = SDCOptionController::updateOption('sdcsm_stripe_webhook_url', $request->webhookUrl);
             /* If stripe data updated successfully */
             if ($stripePublishKey && $stripeSecretKey && $stripeWebhookSecret && $stripeWebhookUrl) {
                $publishKey = SDCOptionController::getOption('sdcsm_stripe_publishable_key', $serviceProvider);
                $secretKey = SDCOptionController::getOption('sdcsm_stripe_secret_key', $serviceProvider);
                $webhookSecret = SDCOptionController::getOption('sdcsm_stripe_webhook_secret', $serviceProvider);
                $webhookUrl = SDCOptionController::getOption('sdcsm_stripe_webhook_url', $serviceProvider);

                return response()->json(["msg" => "Stripe settings updated successfully.", "success" => true, 'publishableKey' => $publishKey, 'secretKey' => $secretKey, 'webhookSecret' => $webhookSecret, 'webhookUrl' => $webhookUrl], 200);
             } else {
                /* If stripe data not updated successfully */
                $publishKey = '';
                $secretKey = '';
                $webhookSecret = '';
                $webhookUrl = '';
                return response()->json(["msg" => "Stripe settings update failed.", "success" => false, "publishableKey" => $publishKey, 'secretKey' => $secretKey, 'webhookSecret' => $webhookSecret, 'webhookUrl' => $webhookUrl], 200);
             }

        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Fetch Stripe settings  Data*/
    private function getStripeData () {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        /* Fetch Stripe settings data */
        $stripePublishKey = SDCOptionController::getOption('sdcsm_stripe_publishable_key', $serviceProvider);
        $stripeSecretKey = SDCOptionController::getOption('sdcsm_stripe_secret_key', $serviceProvider);
        $stripeWebhookUrl = SDCOptionController::getOption('sdcsm_stripe_webhook_url', $serviceProvider);
        $stripeWebhookSecret = SDCOptionController::getOption('sdcsm_stripe_webhook_secret', $serviceProvider);

        return [
            'publishableKey' => $stripePublishKey,
            'secretKey' => $stripeSecretKey,
            'webhookUrl' => $stripeWebhookUrl,
            'webhookSecret' => $stripeWebhookSecret,
        ];
    }

}
