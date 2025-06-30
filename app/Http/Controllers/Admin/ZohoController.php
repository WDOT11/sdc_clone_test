<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\ClaimReasonController;
use App\Http\Controllers\Admin\SDCOptionController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\User\UserDeviceController;
use App\Models\Admin\ClaimRepairStatus;
use App\Models\Admin\Device;
use App\Models\Admin\DeviceModel;
use App\Models\Admin\Organization;
use App\Models\Admin\RepairReason;
use App\Models\Admin\ShippingOption;
use App\Models\User;
use App\Models\User\DeviceClaim;
use App\Models\User\DeviceRepair;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ZohoController extends Controller
{
    /** Function to call the form */
    public function index(){
        return view('admin.zohosettings.index');
    }

    /** Function to refresh the access token (from zoho setting backend)*/
    public function refreshToken(){
        $access_token = self::sdcsmRefreshAccessTokenZoho();
        if(!empty($access_token)){
            return response()->json(["msg" => "Access token is refreshed successfully.", "success" => true], 200);
        }
    }

    /**Common function to regenerate the refresh token if expired */
    private static function sdcsmRefreshAccessTokenZoho(){

        $current_timestamp          =   time();
        $token_created_timestamp    =   SDCOptionController::getOption('sdcsm_token_timestamp');
        $token_expires_in_seconds   =   SDCOptionController::getOption('sdcsm_tech_expires_in');

        /** To regenerate the token if it is expired */
        if(($current_timestamp - $token_created_timestamp) >= $token_expires_in_seconds){
            $zcrm_client_id           =   SDCOptionController::getOption("sdcsm_client_id");
            $zcrm_client_secret       =   SDCOptionController::getOption("sdcsm_client_secret");
            $zcrm_domain_name         =   SDCOptionController::getOption("sdcsm_domain_name");
            $zcrm_authorization_uri   =   SDCOptionController::getOption("sdcsm_authorization_uri");
            $refresh_token            =   SDCOptionController::getOption("sdcsm_refresh_token");

            $client_id          =   $zcrm_client_id;
            $client_secret_key  =   $zcrm_client_secret;
            $redirect_uri       =   $zcrm_authorization_uri;
            $domain_setup       =   "https://accounts.".$zcrm_domain_name;

            $url = $domain_setup."/oauth/v2/token?refresh_token=$refresh_token&client_id=$client_id&client_secret=$client_secret_key&grant_type=refresh_token";

            $client = new Client();

            try{
                $response = $client->post($url);
                $body = $response->getBody()->getContents();
                $data = json_decode($body, true);

                if(!empty($data)){
                    /** Store the access and refresh token with the expiration time */
                    SDCOptionController::updateOption("sdcsm_access_token", $data['access_token']);
                    SDCOptionController::updateOption("sdcsm_api_domain", $data['api_domain']);
                    SDCOptionController::updateOption("sdcsm_token_type", $data['token_type']);
                    SDCOptionController::updateOption("sdcsm_tech_expires_in", $data['expires_in']);
                    SDCOptionController::updateOption("sdcsm_token_timestamp", time());
                }
                else {
                    return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
                }
            } catch (Exception $e){
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }
        }

        $access_token   =   SDCOptionController::getOption("sdcsm_access_token");
        return $access_token;
    }

    /** Function to generate the access token (ONE TIME)*/
    public function sdcsm_generateAccessToken_func(Request $request)
    {
        $zcrm_client_id           =   SDCOptionController::getOption("sdcsm_client_id");
        $zcrm_client_secret       =   SDCOptionController::getOption("sdcsm_client_secret");
        $zcrm_domain_name         =   SDCOptionController::getOption("sdcsm_domain_name");
        $zcrm_authorization_uri   =   SDCOptionController::getOption("sdcsm_authorization_uri");

        $client_id          =   $zcrm_client_id;
        $client_secret_key  =   $zcrm_client_secret;
        $redirect_uri       =   $zcrm_authorization_uri;
        $domain_setup       =   "https://accounts.".$zcrm_domain_name;

        /** Getting the code value from the parameters of get access token URL */
        $code = $request->code;

        $url = $domain_setup."/oauth/v2/token?client_id=$client_id&client_secret=$client_secret_key&grant_type=authorization_code&redirect_uri=$redirect_uri&access_type=offline&code=".$code;

        $client = new Client();

        try{
            $response = $client->post($url);
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

            if(!empty($data)){
                /** Store the access and refresh token with the expiration time */
                SDCOptionController::updateOption("sdcsm_access_token", $data['access_token']);
                SDCOptionController::updateOption("sdcsm_refresh_token", $data['refresh_token']);
                SDCOptionController::updateOption("sdcsm_api_domain", $data['api_domain']);
                SDCOptionController::updateOption("sdcsm_token_type", $data['token_type']);
                SDCOptionController::updateOption("sdcsm_tech_expires_in", $data['expires_in']);
                SDCOptionController::updateOption("sdcsm_token_timestamp", time());

                return response()->json(["msg" => "Access token generated successfully.", "success" => true], 200);
            } else {
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }
        } catch (Exception $e){
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Function to add claims as a deal in the zoho */
    public static function sdcsmInsertInsuredClaimsInZoho($claimData){
         /** Getting the user details*/
        $userId = $claimData->user_id;
        $userData = User::where('id', $userId)->where('active', 1)->first();

        /** Claim id containg here to use it further */
        $claim_id = $claimData->id;

        /** Creating array to check the contact or create if does not exist */
        $contactData = [
            'First_Name'        =>   !empty($userData['first_name']) ? $userData['first_name'] : '',
            'Full_Name'         =>   !empty($userData['full_name']) ? $userData['full_name'] : '',
            'Email'             =>   !empty($userData['email']) ? $userData['email'] : '',
            'Last_Name'         =>   !empty($userData['last_name']) ? $userData['last_name'] : '',
            'Phone'             =>   !empty($userData['phone']) ? $userData['phone'] : '',
            // 'Organization_ID'   =>   !empty($userData['organization_id']) ? $userData['organization_id'] : '',
        ];

        /* call the create contact function here */
        $contactZohoCrmId = self::sdcsmCreateContactInZoho($contactData);

        /** User full address */
        $street_address = !empty($claimData->street_address) ? $claimData->street_address : '';
        $address_line_2 = !empty($claimData->address_line_2) ? $claimData->address_line_2 : '';
        $city = !empty($claimData->city) ? $claimData->city : '';
        $state = !empty($claimData->state) ? $claimData->state : '';
        $zip_postal_code = !empty($claimData->zip) ? $claimData->zip : '';
        $country = !empty($claimData->country) ? $claimData->country : '';
        $full_address = $street_address.', '.$address_line_2.', '.$city.', '.$state.', '.$zip_postal_code.', '.$country;

        /** Getting device details by id */
        $fields = [
            'device_title',
            'serial_number',
            'carrier',
            'color',
            'capacity',
            'org_user_full_name',
            'org_user_id'
        ];
        $deviceDetails = UserDeviceController::getDeviceDetailsById($claimData->device_id, $fields);

        /** Getting organization name */
        $org_name = '';
        if(!empty($claimData->org_id)){
            $org_data = Organization::where('id', $claimData->org_id)->where('active', 1)->select('name')->first();
            $org_name = $org_data['name'];
        }

        $zipCode = (string) $claimData->zip;
        /** Getting claim reason name by id */
        $claimReason = ClaimReasonController::getReasonById($claimData->claim_reason_id);
        $claim_reason_name = $claimReason['claim_reason_name'] ? $claimReason['claim_reason_name'] : '';
        /** Getting the shipping option name */
        /** Getting the shipping option name */
        $shippingOptionName = '';
        if($claimData->shipping_option){
            $shippingOption = ShippingOption::where('id', $claimData->shipping_option)->where('active', 1)->select('name')->first();
            $shippingOptionName = $shippingOption['name'];
        }
        $full_Name = $claimData['first_name'].' '.$claimData['last_name'];

        /** Creating array for ZOHO */
        $DeviceClaimData = [
            'Last_Name'					=> 	!empty($full_Name) ? $full_Name : '',
            'Email'						=>	!empty($claimData['email']) ? $claimData['email'] : '',
            'Full_Name'					=>	!empty($full_Name) ? $full_Name : '',
            'Carrier'					=>	$deviceDetails['carrier'] ? $deviceDetails['carrier'] : '',
            'Color'						=>	$deviceDetails['color'] ? $deviceDetails['color'] : '',
            'Capacity'					=>	$deviceDetails['capacity'] ? $deviceDetails['capacity'] : '',
            'Device_Type'				=>	$deviceDetails['device_title'] ? $deviceDetails['device_title'] : '',
            'Case_Reason'				=>	$claim_reason_name ? $claim_reason_name : '',
            'Description'				=>	$claimData->claim_details ? $claimData->claim_details : '',
            'Subject'					=>	!empty($org_name) ? $org_name : $full_Name,
            'Case_Origin'				=>	"Web",
            'Shipping_Address'			=>	$full_address ? $full_address : '',
            'Zip_Code1'					=>	$zipCode ? $zipCode : '',
            'State'						=>	$claimData->state ? $claimData->state : '',
            'City_State_Zip_Code'		=>	$zip_postal_code ? $zip_postal_code : '',
            'Shipping_Options1'			=>	(string) $shippingOptionName,
            'Serial_Number'				=>	$deviceDetails['serial_number'] ? $deviceDetails['serial_number'] : '',
            'Account_Contact_for_Case'	=>	$contactZohoCrmId ? $contactZohoCrmId : '',
            'Status'					=>	'Claim Received',
            'Claim_Type'				=>	'Insured Claim',
            'School_System'				=>	!empty($org_name) ? $org_name : '',
            'Student_Name'				=>	$claimData->org_user_full_name ? $claimData->org_user_full_name : '',
            'Student_ID_if_applicable'	=>	$claimData->org_user_id ? $claimData->org_user_id : '',
            'Phone_Number'				=>	!empty($claimData['phone']) ? $claimData['phone'] : '',
        ];

        if(!empty($DeviceClaimData)){
            /** Format the payload according to Zoho CRM requirements */
            $payload = ['data' => [$DeviceClaimData]];
            $claimData = json_encode($payload);

            $recordLead = self::zcfFormDatainsert('Cases', $claimData);

            if(!empty($recordLead['data'][0]['details']['id']))
            {
                /** Update the current entry with user email, device name, device serial number etc. */
                $zoho_claims_id = $recordLead['data'][0]['details']['id'];
                $zoho_ticket_number = self::sdcsmGetRecordFromZoho('Cases', $zoho_claims_id);

                if(!empty($zoho_ticket_number['data'][0]['Ticket_Number']))
                {
                    $zohoTicketNumber	=	$zoho_ticket_number['data'][0]['Ticket_Number'];
                    $zohoClaimId 	=	$zoho_claims_id;
                    $zohoContactId 	=	$contactZohoCrmId;

                    /** Update the data in the user and claim details table */
                    $user = User::where('id', $userId)->where('active', 1)->first();
                    $userUpdated = $user->update(['zoho_contact_id' => $zohoContactId]);

                    $deviceClaim = DeviceClaim::where('id', $claim_id)->where('active', 1)->first();
                    $claimUpdated = $deviceClaim->update(['zoho_claim_id' => $zohoClaimId, 'zoho_ticket_number' => $zohoTicketNumber]);

                    if($userUpdated == true && $claimUpdated == true){
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        }
    }

    /** Function to add repairs as a deal in the zoho */
    public static function sdcsmInsertUninsuredRepairsInZoho($repaiarData){

        /** Getting the user details*/
        $userId = $repaiarData->user_id;
        $userData = User::where('id', $userId)->where('active', 1)->first();

        /** repair id containg here to use it further */
        $repiar_id = $repaiarData->id;
        /** Creating array to check the contact or create if does not exist */
        $contactData = [
            'First_Name'        =>   !empty($userData['first_name']) ? $userData['first_name'] : '',
            'Full_Name'         =>   !empty($userData['full_name']) ? $userData['full_name'] : '',
            'Email'             =>   !empty($userData['email']) ? $userData['email'] : '',
            'Last_Name'         =>   !empty($userData['last_name']) ? $userData['last_name'] : '',
            'Phone'             =>   !empty($userData['phone']) ? $userData['phone'] : '',
            // 'Organization_ID'   =>   !empty($userData['organization_id']) ? $userData['organization_id'] : '',
        ];

        /* call the create contact function here */
        $contactZohoCrmId = self::sdcsmCreateContactInZoho($contactData);

        // dd($repaiarData);

        $first_name = !empty($userData['first_name']) ? $userData['first_name'] : '';
        $last_name = !empty($userData['last_name']) ? $userData['last_name'] : '';
        $phone = !empty($userData['phone']) ? $userData['phone'] : '';
        $useremail = !empty($userData['email']) ? $userData['email'] : '';
        /** User address */
        $address_street = !empty($repaiarData->street_address) ? $repaiarData->street_address : '';
        $address_2 = !empty($repaiarData->address_line_2) ? $repaiarData->address_line_2 : '';
        $address_city = !empty($repaiarData->city) ? $repaiarData->city : '';
        $address_state = !empty($repaiarData->state) ? $repaiarData->state : '';
        $address_zip = !empty($repaiarData->zip) ? $repaiarData->zip : '';
        $address_country = !empty($repaiarData->country) ? $repaiarData->country : '';
        /** User full address */
        $full_address = $address_street.', '.$address_2.', '.$address_city.', '.$address_state.', '.$address_zip.', '.$address_country;

        /** Getting repair reason */
        $repair_reason_data = RepairReason::where('id', $repaiarData->repair_reason_id)->where('active', 1)->select('repair_reason_name')->first();
        $repair_reason_name = $repair_reason_data['repair_reason_name'] ? $repair_reason_data['repair_reason_name'] : '';
        /** Getting organization name */
        $org_name = '';
        if(!empty($repaiarData->org_id)){
            $org_data = Organization::where('id', $repaiarData->org_id)->where('active', 1)->select('name')->first();
            $org_name = $org_data['name'];
        }

        /** Getting device type */
        // $deviceType = Organization::where('id', $repaiarData->org_id)->where('active', 1)->select('device_type')->first();
        $address_zip = (string) $address_zip;

        /** Create array for ZOHO. */
        $data_to_send_to_zoho = [
            'Full_Name'					=> 	$first_name,
            'Last_Name'					=>	$last_name,
            'Phone_Number'				=>	$phone,
            'Email'						=>	$useremail,
            'Description'				=>	!empty($repaiarData->repair_details) ? $repaiarData->repair_details : '',
            'Subject'					=>	!empty($org_name) ? $org_name : $first_name,
            'Claim_Type'				=>	'Uninsured Claim',
            'Status'					=>	'Claim Received',
            'Case_Origin'				=>	'Web',
            'Serial_Number'				=>	!empty($repaiarData->device_serial_number) ? $repaiarData->device_serial_number : '',
            'Shipping_Address'			=>	$full_address,
            'Zip_Code1'					=>	$address_zip,
            'State'						=>	$address_state,
            'City_State_Zip_Code'		=>	$address_city,
            'Case_Reason'				=>	$repair_reason_name,
            // 'Device_Type'				=>	rgar($entry, $device[0]),
            'School_System'				=>	!empty($org_name) ? $org_name : '',
            'Account_Contact_for_Case'	=>	$contactZohoCrmId
            // 'Gravity_Forms_Entry_ID'	=>	(string) $result,
        ];

        if($contactZohoCrmId > 0)
		{
			/** Loop through all the collected data. */
			if(!empty($data_to_send_to_zoho))
			{
                $postFieldsLeadArray['data'] = [];
                $postFieldsLeadArray['data'][] = $data_to_send_to_zoho;
                $responseLeads = json_encode($postFieldsLeadArray, true);

                $recordLead = self::zcfFormDatainsert('Cases', $responseLeads);

                if(!empty($recordLead['data'][0]['details']['id']))
                {
                    /** Get current entry. */
                    $zoho_repair_id = $recordLead['data'][0]['details']['id'];
                    $zoho_repair_data = self::sdcsmGetRecordFromZoho('Cases', $zoho_repair_id);
                    $zoho_ticket_number = $zoho_repair_data['data'][0]['Ticket_Number'];

                    /** Update the data in the user and claim details table */
                    $user = User::where('id', $userId)->where('active', 1)->first();
                    $userUpdated = $user->update(['zoho_contact_id' => $contactZohoCrmId]);

                    $deviceRepair = DeviceRepair::where('id', $repiar_id)->where('active', 1)->first();
                    $repairUpdated = $deviceRepair->update(['zoho_repair_id' => $zoho_repair_id, 'zoho_ticket_number' => $zoho_ticket_number]);

                    if($userUpdated == true && $repairUpdated == true){
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        }
    }

    /** To create and update the contact */
        /** Function to add contact in the zoho */
        public static function sdcsmCreateContactInZoho($contactData){

            $contactZohoCrmId = 0;
            /** First check if Contact current contact already exist in ZOHO. */
            $contactRecord = self::sdcsmSearchContactByEmailInZoho('Contacts', $contactData['Email']);

            if (!empty($contactRecord['data']) && !empty($contactRecord['data'][0]))
            {
                if (!empty($contactRecord['data'][0]['id'])) {
                    $contactZohoCrmId = $contactRecord['data'][0]['id'];
                }
            }

            $contactFields = [
                'First_Name'        =>   !empty($contactData['First_Name']) ? $contactData['First_Name'] : '',
                'Last_Name'         =>   !empty($contactData['Last_Name']) ? $contactData['Last_Name'] : '',
                'Full_Name'         =>   !empty($contactData['Full_Name']) ? $contactData['Full_Name'] : '',
                'Email'             =>   $contactData['Email'],
                'Phone'             =>   !empty($contactData['Phone']) ? $contactData['Phone'] : '',
                'Organization_ID'   =>   !empty($contactData['Organization_ID']) ? $contactData['Organization_ID'] : '',
            ];

            /** Update or Add condition as per needed */
            if ($contactZohoCrmId > 0) {
                $contactFields['id'] = $contactZohoCrmId;
                $contactFieldsArray['data'][] = $contactFields;
                $response = json_encode($contactFieldsArray, true);

                $recordContact = self::sdcsmFormDataUpdateInZoho('Contacts', $response);
            } else {
                $contactFieldsArray['data'][] = $contactFields;
                $response = json_encode($contactFieldsArray, true);
                $recordContact = self::sdcsmFormDataInsertInZoho('Contacts', $response);
            }

            if (!empty($recordContact['data']) && !empty($recordContact['data'][0])) {
                    if (!empty($recordContact['data'][0]['details']) && !empty($recordContact['data'][0]['details']['id'])) {
                    return $recordContact['data'][0]['details']['id'];
                }
            }
            return $contactZohoCrmId;
        }

        /** Function to fetch the contact in zoho by using email */
        private static function sdcsmSearchContactByEmailInZoho($module, $email){
            /** Need to update the code as needed to hit the url and check the existance of the contact */
            $domain_setup       = "https://www.zohoapis.com";
            $apiurl = $domain_setup;

            $zohoContactSearchUrl = $apiurl . "/crm/v2/" . $module."/search?email=".$email;

            /** Get access token */
            $access_token = self::sdcsmRefreshAccessTokenZoho();

            $client = new Client();
            try {
                $response = $client->request('GET', $zohoContactSearchUrl, [
                    'timeout' => 5,
                    'allow_redirects' => [
                        'max' => 5
                    ],
                    'http_version' => 1.0,
                    'headers' => [
                        'Authorization' => 'Zoho-oauthtoken ' . $access_token
                    ],
                    'cookies' => false
                ]);

                $body = $response->getBody()->getContents();
                $data = json_decode($body, true);
                return $data;
            } catch (Exception $e){
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }
        }

        /** Function to insert the form data in zoho */
        private static function sdcsmFormDataInsertInZoho($modulename, $xmlData = "", $extraParams = ""){
            /** Check the access token (Update if expierd else use the same) */
            $accessToken = self::sdcsmRefreshAccessTokenZoho();

            $domain_setup       = "https://www.zohoapis.com";
            $apiurl = $domain_setup;
            $zohoFormBaseUrl = $apiurl . "/crm/v2/" . $modulename;

            /** Get access token */
            $access_token = self::sdcsmRefreshAccessTokenZoho();

            $client = new Client();
            try {

                $response = $client->post($zohoFormBaseUrl, [
                    'headers' => [
                        'Authorization' => 'Zoho-oauthtoken ' . $access_token,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'body' => $xmlData
                ]);

                /* Get and return the response */
                $responseBody = $response->getBody()->getContents();
                $data = json_decode($responseBody, true);
                return $data;
            } catch (Exception $e){
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }
        }

        /** Function to update the form data in zoho */
        private static function sdcsmFormDataUpdateInZoho($modulename, $xmlData = "", $extraParams = ""){
            /** Check the access token (Update if expierd else use the same) */
            $access_token = self::sdcsmRefreshAccessTokenZoho();

            $domain_setup       = "https://www.zohoapis.com";
            $apiurl = $domain_setup;
            $zohoFormBaseUrl = $apiurl . "/crm/v2/" . $modulename;

            $client = new Client();
            try {
                $response = $client->put($zohoFormBaseUrl, [
                    'headers' => [
                        'Authorization' => 'Zoho-oauthtoken ' . $access_token,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'body' => $xmlData
                ]);

                /* Get and return the response */
                $responseBody = $response->getBody()->getContents();
                $data = json_decode($responseBody, true);
                return $data;
            } catch (Exception $e){
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }
        }

        /** Function to insert the form data in Zoho*/
        private static function zcfFormDatainsert($modulename, $xmlData = "", $extraParams = "")
        {
            $access_token = self::sdcsmRefreshAccessTokenZoho();

            $domain_setup       = "https://www.zohoapis.com";
            $apiurl = $domain_setup;
            $zohoFormBaseUrl = $apiurl . "/crm/v2/" . $modulename;

            $client = new Client();
            try {
                $response = $client->post($zohoFormBaseUrl, [
                    'headers' => [
                        'Authorization' => 'Zoho-oauthtoken ' . $access_token,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'body' => $xmlData
                ]);

                /* Get and return the response */
                $responseBody = $response->getBody()->getContents();
                $data = json_decode($responseBody, true);
                return $data;
            } catch (Exception $e){
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }
        }

        /** Function to get the data from zoho after storing the data */
        public static function sdcsmGetRecordFromZoho($module, $itemId) {
            $access_token = self::sdcsmRefreshAccessTokenZoho();
            $domain_setup       = "https://www.zohoapis.com";
            $apiurl = $domain_setup;
            $zohoFormBaseUrl = $apiurl . "/crm/v2/" . $module."/".$itemId;

            $client = new Client();
            try {
                $response = $client->request('GET', $zohoFormBaseUrl, [
                    'timeout' => 5,
                    'allow_redirects' => [
                        'max' => 5
                    ],
                    'http_version' => 1.0,
                    'headers' => [
                        'Authorization' => 'Zoho-oauthtoken ' . $access_token
                    ],
                    'cookies' => false
                ]);

                /* Get and return the response */
                $responseBody = $response->getBody()->getContents();
                $data = json_decode($responseBody, true);
                return $data;
            } catch (Exception $e) {
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }
        }

    /** Function to handel the response from zoho */
    public function sdcsm_handle_webhook_response_func(Request $request){
        $req = $request->all();
        if(!empty($req['verifyifcorrectaction']) && $req['verifyifcorrectaction'] == 'aspirewebhookactionnew')
        {
            $claim_status = !empty($req['claim_status']) ? $req['claim_status'] : '';
            $claim_id = !empty($req['claim_id']) ? $req['claim_id'] : '';
            $customer_id = !empty($req['customer_id']) ? $req['customer_id'] : '';
            $insured_uninsured_claim = !empty($req['insured_uninsured_claim']) ? $req['insured_uninsured_claim'] : '';


            /** Need to manipulate the code to save the updated data in the database */
            if(!empty($claim_id) && !empty($claim_status) && !empty($insured_uninsured_claim))
            {

                /* getting claim record as per insurance type */
                if (strtolower($insured_uninsured_claim) == "insured claim")
                {
                    /** Preparing the claim status id by using status name */
                    $adminStatus = ClaimRepairStatus::select('id', 'name')->get();
                    /** Preparing the repair status id for admin*/
                    $adminStatusMap = [];
                    foreach ($adminStatus as $status) {
                        $adminStatusMap[$status['name']] = $status['id'];
                    }
                    $claim_status_id = isset($adminStatusMap[$claim_status]) ? $adminStatusMap[$claim_status] : null;
                    if(empty($claim_status_id) || $claim_status_id == null){
                        /** Insert the status in the DB considering as new status */
                        $newClaimStatus = ClaimRepairStatus::create(['name' => $claim_status, 'active' => 1]);
                        $claim_status_id = $newClaimStatus->id;
                    }

                    $userClaimStatus = config('dashboard.userClaimRepairStatus');
                    $userClaimStatusMap = [];
                    foreach ($userClaimStatus as $status) {
                        $userClaimStatusMap[$status['status']] = $status['id'];
                    }
                    $user_claim_status_id = isset($userClaimStatusMap[$claim_status]) ? $userClaimStatusMap[$claim_status] : 18;

                    /** Getting record by zoho claim id */
                    $claimData = DeviceClaim::where('zoho_claim_id', $claim_id)->where('active', 1)->first();

                    /** need to add the condition for not empty claimData */

                    $device = Device::select('device_title')->where('id', $claimData->device_id)->where('active', 1)->where('service_provider_id', $claimData->service_provider_id)->first();
                    $claimUpdated = $claimData->update(['claim_status' => $claim_status_id, 'user_claim_status' => $user_claim_status_id, 'status_updated_date' => date('Y-m-d h-i-s')]);
                    /** Notifications */
                    $notificationRequest = [
                            'user_id' => $claimData->user_id,
                            'message' => "Claim status changed for the device: " . $device->device_title,
                            'notification_for' => 'claim_raised',
                            'is_claim_status_changed' => 2,
                            'device_claim_id' => $claimData->id,
                            'org_id' => $claimData->org_id,
                            'sub_org_id' => $claimData->sub_org_id,
                            'user_target_link' => 'sdcsmuser/user-track-claims',
                            'admin_target_link' => 'smarttiusadmin/track-claims',
                            'service_provider_id' => $claimData->service_provider_id,
                        ];
                    NotificationController::addNotification($notificationRequest);
                    if($claimUpdated == true){
                        return response()->json([
                            'status' => 'success',
                            'message' => 'Data processed successfully',
                        ], 200);
                    }
                }

                if (strtolower($insured_uninsured_claim) == "uninsured claim")
                {
                    /** Preparing the claim status id by using status name */
                    $repairStatus = ClaimRepairStatus::select('id', 'name')->get();

                    /** Preparing the repair status id for admin*/
                    $repairStatusMap = [];
                    foreach ($repairStatus as $status) {
                        $repairStatusMap[$status['name']] = $status['id'];
                    }
                    $repair_status_id = isset($repairStatusMap[$claim_status]) ? $repairStatusMap[$claim_status] : null;
                    if(empty($repair_status_id) || $repair_status_id == null){
                        /** Insert the status in the DB considering as new status */
                        $newRepairStatus = ClaimRepairStatus::create(['name' => $claim_status, 'active' => 1]);
                        $repair_status_id = $newRepairStatus->id;
                    }

                    /** Preparing the repair status id for user */
                    $userRepairStatus = config('dashboard.userClaimRepairStatus');
                    $repairStatusUserMap = [];
                    foreach ($userRepairStatus as $status) {
                        $repairStatusUserMap[$status['status']] = $status['id'];
                    }
                    $user_repair_status_id = isset($repairStatusUserMap[$claim_status]) ? $repairStatusUserMap[$claim_status] : 18;

                    /** Getting record by zoho claim id */
                    $deviceRepair = DeviceRepair::where('zoho_repair_id', $claim_id)->where('active', 1)->first();
                    /** need to add the condition for not empty deviceRepair */
                    $deviceModel = DeviceModel::select('title')->where('id', $deviceRepair->device_model_id)->where('active', 1)->where('service_provider_id', $deviceRepair->service_provider_id)->first();
                    $repairUpdated = $deviceRepair->update(['repair_status' => $repair_status_id, 'user_repair_status' => $user_repair_status_id, 'status_updated_date' => date('Y-m-d h-i-s')]);
                    /** Notifications */
                    $notificationRequest = [
                        'user_id' => $deviceRepair->user_id,
                        'message' => "Repair status changed for the device model: " . $deviceModel->title,
                        'notification_for' => 'repair_request',
                        'device_repair_id' => $deviceRepair->id,
                        'is_repair_status_changed' => 2,
                        'org_id' => $deviceRepair->org_id,
                        'sub_org_id' => $deviceRepair->sub_org_id,
                        'user_target_link' => 'sdcsmuser/user-track-repairs',
                        'admin_target_link' => 'smarttiusadmin/track-repairs',
                        'service_provider_id' => $deviceRepair->service_provider_id,
                    ];
                    NotificationController::addNotification($notificationRequest);
                    if($repairUpdated == true){
                        return response()->json([
                            'status' => 'success',
                            'message' => 'Data processed successfully',
                        ], 200);
                    }
                }
            }
        }
    }
}