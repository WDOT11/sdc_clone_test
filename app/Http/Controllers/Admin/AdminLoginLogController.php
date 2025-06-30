<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminLoginLogController extends Controller
{
    public function index(Request $request)
    {
        $where = $this->getFilter($request);
        $fields = [];
        $logData = self::getLoginLogQuery($where, $fields);

        $pagination = [
            'total' => $logData->total(),
            'per_page' => $logData->perPage(),
            'current_page' => $logData->currentPage(),
            'last_page' => $logData->lastPage(),
            'from' => $logData->firstItem(),
            'to' => $logData->lastItem()
        ];

        if (!empty($request->page)) {
            return response()->json(["logData" => $logData, "pagination" => $pagination, "msg" => "Paginated records here.", "success" => true], 200);
        } else {
            return view('admin.adminloginlogs.index', compact('logData', 'pagination'));
        }
    }

    private function getLoginLogQuery($where = [], $fields = [])
    {
        $query = LoginLog::query();

        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select('ip_address', 'browser', 'user_id', 'date', 'success', 'user_email', 'password_attempt', 'panel');
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

        $query->orderBy('date', 'desc');
        return $query->paginate(20);
    }

    /** For Filter's */
    private function getFilter(Request $request)
    {
        $where = [];
        if (!empty($request->email) || !empty($request->search_ip) || isset($request->success) || !empty($request->date) || !empty($request->panel)) {
            $where['where'] = [];

            /* If email is not empty */
            if (!empty($request->email)) {
                $where['where']['user_email'] = ['LIKE', "%{$request->email}%"];
            }

            /* If ip is not empty */
            if (!empty($request->search_ip)) {
                $where['where']['ip_address'] = ['LIKE', "%{$request->search_ip}%"];
            }
            /* If success input is not empty */
            if(isset($request->success)){
                $where['where']['success'] = ['=', "{$request->success}"];
            }
            /* If panel input is not empty */
            if(isset($request->panel)){
                $where['where']['panel'] = ['LIKE', "%{$request->panel}%"];
            }

            /** If the date input is not empty */
            if(!empty($request->date)){
                $where['whereDate']['date'] = Carbon::parse($request->date)->format('Y-m-d');
            }
        }
        return $where;
    }
}
