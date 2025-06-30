<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminNotificationController extends Controller
{
     /** Get Unseen Notifications without pagination */
     public function getNotifications()
     {
        $notifications =  NotificationController::getNotifications();
        return $notifications;
     }

    /** Get Seen Notifications without pagination */
    public function getSeenNotifications()
    {
       $notifications =  NotificationController::getSeenNotifications();
       return $notifications;
    }
     /** With Pagination (All Notifications)*/
     public function index(Request $request)
     {
         $notifications = NotificationController::paginateNotifications($request);
         if(!empty($request->page))
         {
             return $notifications;
         }else {
             $notificationsData = $notifications['notifications'];
             $pagination = $notifications['pagination'];

             return view('admin.notifications.index', compact('notificationsData','pagination'));

         }
     }

     /** Mark Notifications(All) of auth user as seen */
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

    /** Unseen Notifications count(latest 10) */
    public function getNotificationsCount()
    {
       $notificationsCount =  NotificationController::unseenLatestNotificationsCount();
       return $notificationsCount;
    }
    /** Seen Notifications count(latest 10) */
    public function getSeenNotificationsCount()
    {
        $notificationsCount = NotificationController::seenLatestNotificationsCount();
        return $notificationsCount;
    }
}
