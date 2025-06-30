<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TransactionController extends Controller
{

    /** Admin Dashboard Transaction List */
    public function adminindex(Request $request)
    {
        $where = self::filterTransactions($request);
        $transactionData = self::paginateTransaction(20, $where);
        $pagination = [
            'total' => $transactionData->total(),
            'per_page' => $transactionData->perPage(),
            'current_page' => $transactionData->currentPage(),
            'last_page' => $transactionData->lastPage(),
            'from' => $transactionData->firstItem(),
            'to' => $transactionData->lastItem()
        ];
        if (!empty($request->page)) {
            return response()->json(["transactionData" => $transactionData, "pagination" => $pagination, "msg" => "Paginated records here.", "success" => true], 200);
        } else {
            return view('admin.transactionmaster.index', compact('transactionData', 'pagination'));
        }
    }
    /** User Dashboard Transaction List */
    public function userindex(Request $request)
    {
        /* Auth User */
        $authUser = Session::get('auth_user');
        $where = [
            'where' => [
                'transactions.user_id' => $authUser->id,
            ]
        ];

        $filters = self::filterTransactions($request);

        /* Merge 'where' conditions if present */
        if (!empty($filters['where'])) {
            $where['where'] = array_merge($where['where'], $filters['where']);
        }

        /* Merge other filters like 'whereBetween', etc. */
        foreach ($filters as $key => $value) {
            if ($key !== 'where') {
                $where[$key] = $value;
            }
        }

        $transactionData = self::paginateTransaction(20, $where);
        $pagination = [
            'total' => $transactionData->total(),
            'per_page' => $transactionData->perPage(),
            'current_page' => $transactionData->currentPage(),
            'last_page' => $transactionData->lastPage(),
            'from' => $transactionData->firstItem(),
            'to' => $transactionData->lastItem()
        ];
        if (!empty($request->page)) {
            return response()->json(["transactionData" => $transactionData, "pagination" => $pagination, "msg" => "Paginated records here.", "success" => true], 200);
        } else {
            return view('user.transactions.index', compact('transactionData', 'pagination'));
        }
    }

    /** Transaction Query */
    private static function transactionQuery($where = [], $fields = [])
    {
        /* Getting service provider id from session */
        $serviceProvider = Session::get('service_provider');
        $query = DB::table('transactions')
            ->leftJoin('users as user', 'transactions.user_id', '=', 'user.id')
            ->where('transactions.service_provider_id', $serviceProvider);
        if (!empty($fields) && is_array($fields)) {
            $query->select($fields);
        } else {
            $query->select(
                'transactions.id',
                'transactions.amount',
                'transactions.stripe_transaction_id',
                'transactions.payment_method',
                'transactions.payment_for',
                'transactions.user_id',
                'transactions.device_id',
                'transactions.device_claim_id',
                'transactions.device_repair_id',
                'transactions.status',
                'transactions.description',
                'transactions.created_at',
                'user.full_name as user_name',
                'user.email as user_email',
                'user.pm_last_four as card_number',
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
                    } elseif ($field == 'whereBetween') {
                        /* For 'whereBetween', handle differently */
                        $query->whereBetween(key($value), $value[key($value)]);
                    } else {
                        /* For 'where', 'whereIn', etc., handle normally */
                        foreach ($value as $secondField => $secondValue) {
                            if (is_array($secondValue)) {
                                if ($field == 'whereIn') {
                                    $query->$field($secondField, $secondValue);
                                } else {
                                    $query->$field($secondField, $secondValue[0], $secondValue[1]);
                                }
                            } else {
                                $query->$field($secondField, $secondValue);
                            }
                        }
                    }
                }
            }
        }
        return $query->orderBy('transactions.created_at', 'desc');
    }


    /** With Pagination */
    public static function paginateTransaction($limit = 20,$where = [], $fields = []){
        $data = self::transactionQuery($where, $fields);
        $limit = (is_numeric($limit) && $limit > 0) ? $limit : 20;
        return $data->paginate($limit);
    }

    /** Filter Function */
    public static function filterTransactions(Request $request)
    {
        /* Auth User */
        $where = [];
        if (!empty($request->search) ||  !empty($request->paymentFor) || !empty($request->startDate) ||  !empty($request->endDate) || !empty($request->userId)) {
            /* Search by user email */
            if (!empty($request->search)) {
                $where = [
                    'where' => [
                        'user.email' => ['LIKE', "%{$request->search}%"],
                    ]
                ];
            }
            if (!empty($request->paymentFor)) {
                if ($request->paymentFor == 'all') {

                } else {
                    $where['where']['transactions.payment_for'] = $request->paymentFor;
                }
            }
            if (!empty($request->startDate) ||  !empty($request->endDate)) {
                $start = Carbon::parse($request->startDate)->startOfDay(); /* 00:00:00 */
                $end = Carbon::parse($request->endDate)->endOfDay();       /* 23:59:59 */
                $where['whereBetween']['transactions.created_at'] = [$start, $end];
            }
            if (!empty($request->userId)) {
                $where['where']['transactions.user_id'] = $request->userId;
            }
        }
        return $where;
    }
}
