<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Admin\SDCOptionController;
use App\Http\Controllers\Controller;
use App\Models\Admin\SupportTicket;
use App\Services\Smtp2GoMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SupportTicketController extends Controller
{
    public function createForm()
    {
        return view('user.supportTickets.create');
    }

    public function store(Request $request)
    {
        try{
            /* Getting service provider id from session */
            $serviceProvider = Session::get('service_provider');

            /* Validate to the requests */
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => 'required|email|max:100',
                'phone' => 'required|regex:/^[0-9]{10}$/',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|max:5000',
            ], [
                'firstName.required' => "Please enter the first name.",
                'firstName.max' => "First name must not exceed 100 characters.",

                'lastName.required' => "Please enter the last name.",
                'lastName.max' => "Last name must not exceed 100 characters.",

                'email.required' => "Please enter the email address.",
                'email.email' => "Please enter a valid email address.",

                'phone.required' => "Please enter the phone number.",
                'phone.regex' => "Phone Number must be numeric.",

                'subject.required' => "Please enter the subject.",
                'subject.max' => "Subject must not exceed 255 characters.",

                'message.required' => "Please enter the message.",
                'message.max' => "Message must not exceed 5000 characters.",

            ]);
            if ($validator->fails()) {
                return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
            }

            /** Getting current logged in user id */
            $userData = Session::get('auth_user');
            $user_id = $userData->id;

            $replyTo = $request->email;
            $replyToUserName = $request->first_name . ' ' . $request->last_name;

            $supportTicket = SupportTicket::create([
                'user_id' => $user_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'active' => 1,
                'service_provider_id' => $serviceProvider
            ]);

            if($supportTicket->wasRecentlyCreated){
                /** Send Mail using the Smtp2GoMail **/
                $senderEmail = SDCOptionController::getOption('sdcsm_smtp2Go_sender_email', $serviceProvider);
                Smtp2GoMail::to($senderEmail)->replyTo($replyTo)->subject($request->subject)->view('emails.support_ticket_message_mail', ['userName'=> $replyToUserName, 'message'=> $request->message])->send();
                return response()->json(["msg" => "Support ticket raised successfully.", "success" => true], 200);
            }else {
                return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /** Function to list the support tickets */
    public function adminSupportTicketIndex(Request $request)
    {
        $where = [];
        $supportTicket = self::getPaginatedSupportTickets(20, $where);
        $pagination = [
            'total' => $supportTicket->total(),
            'per_page' => $supportTicket->perPage(),
            'current_page' => $supportTicket->currentPage(),
            'last_page' => $supportTicket->lastPage(),
            'from' => $supportTicket->firstItem(),
            'to' => $supportTicket->lastItem()
        ];

        /** If the page parameter is not empty */
        if (!empty($request->page)) {
            return response()->json(["ticketData" => $supportTicket, "pagination" => $pagination, "msg" => "Support Tickets Fetched.", "success" => true], 200);
        }
        else {
            return view('admin.supporttickets.index', compact('supportTicket', 'pagination'));
        }
    }

    /* Get all the support tickets. */
    public static function getPaginatedSupportTickets($limit = 20, $where = [], $fields = [])
    {
        $data = self::getSupportTicketsQuery($where, $fields);
        $limit = (is_numeric($limit) && $limit > 0) ? $limit : 20;
        return $data->paginate($limit);
    }

    /* Get Support Tickets Query */
    private static function getSupportTicketsQuery($where = [], $fields = [])
    {
        /* Get service provider is using session */
        $serviceProvider = Session::get('service_provider');
        $query = SupportTicket::where('service_provider_id', $serviceProvider)->where('active', 1);
        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select('id', 'first_name', 'last_name', 'phone', 'email', 'subject', 'message', 'created_at');
        }
        if (!empty($where) && is_array($where)) {
            foreach ($where as $field => $value) {
                if (is_array($value)) {
                    foreach ($value as $secondField => $secondValue) {
                        if ($field == 'where' && is_array($secondValue)) {
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

    /** TO get the data by ticket id */
    public function getTicketDataById(Request $request){

        /* Auth User */
        $where = [];
        $ticket = null;
        if (!empty($request->ticketId)) {
            $where['where']['id'] = $request->ticketId;
            $fields = [];

            $ticket = self::getSupportTicketsQuery($where, $fields)->first();
        }

        if (!empty($ticket)) {
            return response()->json(["viewData" => $ticket, "msg" => "Ticket data found.", "success" => true], 200);
        } else {
            return response()->json(["msg" => "Ticket data not found.", "success" => false], 200);
        }
    }

    public static function recentSupportTickets($where = []){
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');

        /* Get the recent support tickets */
        $fields = ['id', 'first_name', 'last_name', 'phone', 'email', 'subject', 'message', 'created_at'];

        return $recentTickets = self::getSupportTicketsQuery($where, $fields)->take(5)->get();
    }
}