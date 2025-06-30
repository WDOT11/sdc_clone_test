<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\OrgRelationship;
use App\Models\UserMeta;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /** Get Unseen(Latest 10) Notifications (without paginations) */

    public static function getNotifications()
    {
        $serviceProvider = Session::get('service_provider');
        $userData = Session::get('auth_user');

        // Step 1: Get all unseen notification IDs for this user
        /* $notificationUserQuery = DB::table('notification_users')
            ->where('user_id', $userData->id)
            // ->where('is_seen', 2)
            ->where('active', 1)
            ->where('service_provider_id', $serviceProvider)->pluck('notification_id');
        $notifications = Notification::whereIn('id', $notificationUserQuery)->where('active', 1)->where('service_provider_id', $serviceProvider)->orderBy('created_at', 'desc')->take(10)->get(); */

        $notifications = DB::table('notifications')
                        ->join('notification_users', 'notifications.id', '=', 'notification_users.notification_id')
                        ->where('notification_users.user_id', $userData->id)
                        // ->where('notification_users.is_seen', 2)
                        ->where('notifications.active', 1)
                        ->where('notification_users.active', 1)
                        ->where('notifications.service_provider_id', $serviceProvider)
                        ->where('notification_users.service_provider_id', $serviceProvider)
                        ->orderBy('notifications.created_at', 'desc')
                        ->select(
                            'notifications.*',
                            'notification_users.is_seen'
                        )
                        ->take(10)
                        ->get();

        return response()->json([
            'success' => true,
            /* 'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'message' => $notification->message,
                    'notification_for' => $notification->notification_for,
                    'user_id' => $notification->user_id,
                    'device_id' => $notification->device_id,
                    'shipping_supply_id' => $notification->shipping_supply_id,
                    'device_claim_id' => $notification->device_claim_id,
                    'device_repair_id' => $notification->device_repair_id,
                    'is_seen' => $notification->is_seen,
                    'is_claim_status_changed' => $notification->is_claim_status_changed,
                    'is_repair_status_changed' => $notification->is_repair_status_changed,
                    'user_target_link' => $notification->user_target_link,
                    'admin_target_link' => $notification->admin_target_link,
                    'created_at' => $notification->created_at,
                ];
            }) */
           'notifications' => $notifications
        ]);
    }
    /** Get Seen(Latest 10) Notifications (without paginations) */

    public static function getSeenNotifications()
    {
        $serviceProvider = Session::get('service_provider');
        $userData = Session::get('auth_user');

        /* Get all seen notification IDs for this user */
        $notificationUserQuery = DB::table('notification_users')->where('user_id', $userData->id)->where('is_seen', 1)->where('active', 1)->where('service_provider_id', $serviceProvider)->pluck('notification_id');
        $notifications = Notification::whereIn('id', $notificationUserQuery)->where('active', 1)->where('service_provider_id', $serviceProvider)->orderBy('created_at', 'desc')->take(10)->get();
        return response()->json([
            'success' => true,
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'message' => $notification->message,
                    'created_at' => $notification->created_at,
                ];
            })
        ]);
    }




    /** Get Notification with pagination */
    public static function paginateNotifications(Request $request)
    {
        $serviceProvider = Session::get('service_provider');
        $userData = Session::get('auth_user');
        if (!$userData) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ]);
        }

        // Step 1: Get all unseen notification IDs for this user
        $unseenIdsQuery = DB::table('notification_users')->where('user_id', $userData->id)->where('active', 1)->where('service_provider_id', $serviceProvider)->pluck('notification_id');

        $notifications = Notification::whereIn('id', $unseenIdsQuery)->where('active', 1)->where('service_provider_id', $serviceProvider)->orderBy('created_at', 'desc')->paginate(20);

        $formatted = collect($notifications->items())->map(function ($notification) {
            return [
                'id' => $notification->id,
                'message' => $notification->message,
                'notification_for' => $notification->notification_for,
                'user_id' => $notification->user_id,
                'device_id' => $notification->device_id,
                'shipping_supply_id' => $notification->shipping_supply_id,
                'device_claim_id' => $notification->device_claim_id,
                'device_repair_id' => $notification->device_repair_id,
                'user_target_link' => $notification->user_target_link,
                'admin_target_link' => $notification->admin_target_link,
                'created_at' => $notification->created_at,
            ];
        });

        $pagination = [
            'total' => $notifications->total(),
            'per_page' => $notifications->perPage(),
            'current_page' => $notifications->currentPage(),
            'last_page' => $notifications->lastPage(),
            'from' => $notifications->firstItem(),
            'to' => $notifications->lastItem(),
        ];

        return $request->page
            ? response()->json([
                'success' => true,
                'notifications' => $formatted,
                'pagination' => $pagination,
            ])
            : [
                'notifications' => $formatted,
                'pagination' => $pagination,
            ];
    }


    /** Store notifications */
    public static function addNotification($notificationRequest)
    {
        /* Auth User Service Provider id*/
        // $serviceProvider = Session::get('service_provider');
        $serviceProvider = $notificationRequest['service_provider_id'];
        /* Validate request data */
        $validator = Validator::make($notificationRequest, [
            'user_id' => 'required|exists:users,id,active,1,service_provider_id,' . $serviceProvider,
            'message' => 'required|string|max:255',
            'notification_for' => 'required|string|max:255',
            'device_id' => 'nullable|exists:devices,id,active,1,service_provider_id,' . $serviceProvider,
            'device_model_id' => 'nullable|exists:device_models,id,active,1,service_provider_id,' . $serviceProvider,
            'org_id' => 'nullable|exists:organizations,id,active,1,service_provider_id,' . $serviceProvider,
            'sub_org_id' => 'nullable|exists:sub_organizations,id,active,1,service_provider_id,' . $serviceProvider,
            'device_claim_id' => 'nullable|exists:device_claims,id,active,1,service_provider_id,' . $serviceProvider,
            'device_repair_id' => 'nullable|exists:device_repairs,id,active,1,service_provider_id,' . $serviceProvider,
            'shipping_supply_id' => 'nullable|exists:shipping_supplies,id,active,1,service_provider_id,' . $serviceProvider,
            /*   'is_claim_status_changed' => 'nullable',
            'is_repair_status_changed' => 'nullable', */
            'user_target_link' => 'nullable',
            'admin_target_link' => 'required',
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
        $notification = Notification::create([
            'user_id' => $notificationRequest['user_id'],
            'message' => $notificationRequest['message'],
            'notification_for' => $notificationRequest['notification_for'],
            'device_id' => $notificationRequest['device_id'] ?? null,
            'shipping_supply_id' => $notificationRequest['shipping_supply_id'] ?? null,
            'device_model_id' => $notificationRequest['device_model_id'] ?? null,
            'device_claim_id' => $notificationRequest['device_claim_id'] ?? null,
            'is_claim_status_changed' => $notificationRequest['is_claim_status_changed'] ?? null,
            'device_repair_id' => $notificationRequest['device_repair_id'] ?? null,
            'is_repair_status_changed' => $notificationRequest['is_repair_status_changed'] ?? null,
            'org_id' => $notificationRequest['org_id'] ?? null,
            'sub_org_id' => $notificationRequest['sub_org_id'] ?? null,
            'user_target_link' => $notificationRequest['user_target_link'] ?? null,
            'admin_target_link' => $notificationRequest['admin_target_link'] ?? null,
            'is_admin' => $notificationRequest['is_admin'] ?? 1, /* 1=from user dashboard, 2=from admin dashboard*/
            'service_provider_id' => $serviceProvider
        ]);
        if ($notification->wasRecentlyCreated) {
            /** Auth User */
            $userData = Session::get('auth_user');
            $creatorId = $userData->id ?? $notificationRequest['user_id'];
            $isStatusChange = !empty($notificationRequest['is_claim_status_changed']) && $notificationRequest['is_claim_status_changed'] == 2 || !empty($notificationRequest['is_repair_status_changed']) && $notificationRequest['is_repair_status_changed'] == 2;

            // Step 1: Get creator's role(s)
            $creatorRoles = UserMeta::where('user_id', $creatorId)
                ->where('active', 1)
                ->where('service_provider_id', $serviceProvider)
                ->pluck('meta_key')
                ->toArray();

            // Step 2: All potential users
            $allRoleUsers = UserMeta::select('user_id', 'meta_key')
                ->whereIn('meta_key', ['admin', 'org_it_hod', 'org_it_director', 'org_subscriber', 'subscriber'])
                ->where('active', 1)
                ->where('service_provider_id', $serviceProvider)
                ->get()
                ->groupBy('meta_key');

            // Step 3: Build recipient list
            $targetUserIds = collect();
            $orgId = $notificationRequest['org_id'] ?? null;
            $subOrgId = $notificationRequest['sub_org_id'] ?? null;
            if (in_array('admin', $creatorRoles)) {
                if ($isStatusChange) {
                    $targetUserIds = $allRoleUsers->flatten()->pluck('user_id');
                } else {

                    if (!empty($notificationRequest['is_admin']) && $notificationRequest['is_admin'] == 2) {
                        $targetUserId = $notificationRequest['user_id'];
                        $targetUserRole = UserMeta::where('user_id', $targetUserId)
                            ->where('active', 1)
                            ->where('service_provider_id', $serviceProvider)
                            ->value('meta_key');



                        if ($targetUserRole == 'org_subscriber') {
                            if (!empty($orgId) && !empty($subOrgId)) {
                                $relatedHod = OrgRelationship::where('org_id', $orgId)->whereNull('parent_org_id')->pluck('user_id')->toArray();
                                $relatedDirectorOrgSubs = OrgRelationship::where('org_id', $subOrgId)->where('parent_org_id', $orgId)->pluck('user_id')->toArray();
                                $relatedUserIds = array_merge($relatedHod, $relatedDirectorOrgSubs);
                                $targetUserIds = UserMeta::whereIn('user_id', $relatedUserIds)
                                    ->whereIn('meta_key', ['org_it_hod', 'org_it_director', 'org_subscriber'])
                                    ->where('active', 1)
                                    ->where('service_provider_id', $serviceProvider)
                                    ->pluck('user_id');

                                $targetUserIds->push($targetUserId);
                            } elseif (empty($orgId) && empty($subOrgId)) {
                                $targetUserIds = collect([$targetUserId]);
                            }
                        } elseif ($targetUserRole == 'org_it_director') {

                            if (!empty($orgId) && !empty($subOrgId)) {
                               $relatedHod = OrgRelationship::select('user_id')->where('org_id', $orgId)->whereNull('parent_org_id')->first();

                                $targetUserIds = collect([$targetUserId]);

                                if (!empty($relatedHod) && !empty($relatedHod->user_id)) {
                                    $hodUser = UserMeta::where('user_id', $relatedHod->user_id)
                                        ->where('meta_key', 'org_it_hod')
                                        ->where('active', 1)
                                        ->where('service_provider_id', $serviceProvider)
                                        ->first();

                                    if (!empty($hodUser)) {
                                        $targetUserIds->push($relatedHod->user_id);
                                    }
                                }
                            } elseif (empty($orgId) && empty($subOrgId)) {
                                $targetUserIds = collect([$targetUserId]);
                            }
                        } elseif ($targetUserRole == 'org_it_hod' || $targetUserRole == 'subscriber') {
                            $targetUserIds = collect([$targetUserId]);
                        }
                    } else {
                        // Admin doesn't notify anyone else unless status change
                        $targetUserIds = collect();
                    }
                }
            } elseif (in_array('org_it_hod', $creatorRoles)) {
                $targetUserIds = collect([
                    ...($allRoleUsers['admin'] ?? collect())->pluck('user_id')->toArray(),
                ]);
            } elseif (in_array('org_it_director', $creatorRoles)) {
                $relatedHod = OrgRelationship::select('user_id')->where('org_id', $orgId)->whereNull('parent_org_id')->first();
                $targetUserIds = collect([
                    ...($allRoleUsers['admin'] ?? collect())->pluck('user_id')->toArray(),
                ]);
                $hodUser = UserMeta::where('user_id', $relatedHod->user_id)
                ->where('meta_key', 'org_it_hod')
                ->where('active', 1)
                ->where('service_provider_id', $serviceProvider)
                ->first();
                if (!empty($relatedHod) && !empty($relatedHod->user_id)) {
                    $hodUser = UserMeta::where('user_id', $relatedHod->user_id)
                        ->where('meta_key', 'org_it_hod')
                        ->where('active', 1)
                        ->where('service_provider_id', $serviceProvider)
                        ->first();

                    if (!empty($hodUser)) {
                        $targetUserIds->push($relatedHod->user_id);
                    }
                }
            } elseif (in_array('org_subscriber', $creatorRoles)) {
                $targetUserIds = collect([...($allRoleUsers['admin'] ?? collect())->pluck('user_id')->toArray(),]);
                $relatedHod = OrgRelationship::where('org_id', $orgId)->whereNull('parent_org_id')->pluck('user_id')->toArray();
                $relatedDirector = OrgRelationship::where('org_id', $subOrgId)->where('parent_org_id', $orgId)->where('is_org_subscriber', 0)->pluck('user_id')->toArray();
                $relatedUserIds = array_merge($relatedHod, $relatedDirector);
                if (!empty($relatedHod) || !empty($relatedDirector)) {
                    $userIds = UserMeta::whereIn('user_id', $relatedUserIds)
                                    ->whereIn('meta_key', ['org_it_hod', 'org_it_director'])
                                    ->where('active', 1)
                                    ->where('service_provider_id', $serviceProvider)
                                    ->pluck('user_id');

                    $targetUserIds = $targetUserIds->merge($userIds);
                }
            } elseif (in_array('subscriber', $creatorRoles)) {
                $targetUserIds = collect([
                    ...($allRoleUsers['admin'] ?? collect())->pluck('user_id')->toArray()
                ]);
            }

            // Step 4: Include creator only if it's a status change
            if ($isStatusChange) {
                $targetUserIds->push($creatorId);
            }

            // Remove duplicates and finalize
            $finalUserIds = $targetUserIds->unique()->toArray();

            $now = now();

            $notificationUsers = array_map(function ($userId) use ($notification, $serviceProvider, $now) {
                return [
                    'notification_id' => $notification->id,
                    'user_id' => $userId,
                    'is_seen' => 2,
                    'service_provider_id' => $serviceProvider,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }, $finalUserIds);

            DB::table('notification_users')->insert($notificationUsers);
            return true;
        } else {
            return false;
        }
    }


    /** Mark all notifications as seen for the logged-in user */
    public static function markSeenNotification()
    {
        $serviceProvider = Session::get('service_provider');
        $userData = Session::get('auth_user');
        $updated = DB::table('notification_users')
            ->where('user_id', $userData->id)
            ->where('service_provider_id', $serviceProvider)
            ->update([
                'is_seen' => 1, // 1 = seen, 2 = unseen
                'seen_at' => now(),
            ]);
        if ($updated > 0) {
            return true;
        } else {
            return false;
        }
    }

    /** Mark Single Notification as seen for logged-user*/
    public static function markSeenSingleNotification($request)
    {
        $serviceProvider = Session::get('service_provider');
        /* Validate Requests */
        $validator = Validator::make($request->all(), [
            'notificationId' => 'required|exists:notifications,id,active,1,service_provider_id,' . $serviceProvider,
        ]);

        if ($validator->fails()) {
            return response()->json(["msg" => "Validation errors", "success" => false, "errors" => $validator->errors()], 200);
        }
        $userData = Session::get('auth_user');
        $updated = DB::table('notification_users')
                   ->where('notification_id', $request->notificationId)
                   ->where('user_id', $userData->id)
                   ->where('service_provider_id', $serviceProvider)
                   ->update([
                       'is_seen' => 1, // 1 = seen , 2 = unseen
                       'seen_at' => now(),
                   ]);
        if ($updated > 0) {
            return true;
        } else {
            return false;
        }
    }

    /** Unseen Notifications count (latest 10) */
    public static function unseenLatestNotificationsCount()
    {
        $serviceProvider = Session::get('service_provider');
        $userData = Session::get('auth_user');

        // Step 1: Get the latest 10 notification IDs (for that user)
        $latestNotificationIds = Notification::where('active', 1)
            ->where('service_provider_id', $serviceProvider)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->pluck('id');

        // Step 2: Count unseen among those 10
        $count = DB::table('notification_users')
            ->where('user_id', $userData->id)
            ->where('is_seen', 2)
            ->where('active', 1)
            ->where('service_provider_id', $serviceProvider)
            ->whereIn('notification_id', $latestNotificationIds)
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ], 200);
    }

    /** Seen Notifications count (latest 10) */
    public static function seenLatestNotificationsCount()
    {
        $serviceProvider = Session::get('service_provider');
        $userData = Session::get('auth_user');

        // Step 1: Get the latest 10 notification IDs (for that user)
        $latestNotificationIds = Notification::where('active', 1)
            ->where('service_provider_id', $serviceProvider)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->pluck('id');

        // Step 2: Count unseen among those 10
        $count = DB::table('notification_users')
            ->where('user_id', $userData->id)
            ->where('is_seen', 1)
            ->where('active', 1)
            ->where('service_provider_id', $serviceProvider)
            ->whereIn('notification_id', $latestNotificationIds)
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ], 200);
    }
    /** All Notifications count*/
    public static function AllNotificationsCount()
    {
        $serviceProvider = Session::get('service_provider');
        $userData = Session::get('auth_user');
        $count = DB::table('notification_users')
            ->where('user_id', $userData->id)
            ->where('active', 1)
            ->where('service_provider_id', $serviceProvider)
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ], 200);
    }
}
