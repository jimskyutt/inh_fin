<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    /**
     * Display a listing of the notification.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Enable query logging
        \DB::enableQueryLog();
        
        $user = Auth::user();
        
        // Get notifications with related data
        $notifications = $user->notifications()
            ->with(['relatedUser', 'post.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        // Get the executed queries
        $queries = \DB::getQueryLog();
        
        // Output debug information
        $debugInfo = [
            'user_id' => $user->id,
            'notifications_count' => $notifications->count(),
            'queries' => $queries,
            'notifications' => $notifications->toArray(),
            'raw_notifications' => \DB::table('notifications')->where('user_id', $user->id)->get()->toArray()
        ];
        
        // Log the debug info
        \Log::info('Notifications Debug:', $debugInfo);
        
        // Output to browser console for debugging
        echo "<script>console.log('Debug Info: ", json_encode($debugInfo, JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE), "');</script>";
        
        // Mark all unread notifications as read
        if ($user->unreadNotifications()->exists()) {
            $user->unreadNotifications()->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
        
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        
        if ($notification) {
            $notification->markAsRead();
            
            // Redirect to the notification's URL if available
            if (isset($notification->data['link'])) {
                return redirect($notification->data['link']);
            }
        }
        
        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
