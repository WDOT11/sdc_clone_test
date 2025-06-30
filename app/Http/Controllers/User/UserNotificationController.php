<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UserNotificationController extends Controller
{
    /** Get Unseen Notifications without pagination */
    public function getNotifications(Request $request)
    {
       $notifications =  NotificationController::getNotifications($request);
       return $notifications;
    }
     /** Get Seen Notifications without pagination */
    public function getSeenNotifications()
    {
       $notifications =  NotificationController::getSeenNotifications();
       return $notifications;
    }
    /** With Pagination */
    public function index(Request $request)
    {
        $notifications = NotificationController::paginateNotifications($request);
        if(!empty($request->page))
        {
            return $notifications;
        }else {
            $notificationsData = $notifications['notifications'];
            $pagination = $notifications['pagination'];

            return view('user.notifications.index', compact('notificationsData','pagination'));

        }
    }

    /** Mark  User Dashboard all Notifications of auth user as seen */
    public function markSeen()
    {
        $notificationsUpdate = NotificationController::markSeenNotification();
        return $notificationsUpdate;
    }
    /** Mark Single Notification of auth user as seen */
    public function markSingleSeen(Request $request)
    {
        $notificationsUpdate = NotificationController::markSeenSingleNotification($request);
        return $notificationsUpdate;
    }
    /** Unseen Notifications count */
    public function getNotificationsCount()
    {
       $notificationsCount =  NotificationController::unseenLatestNotificationsCount();
       return $notificationsCount;
    }
}
