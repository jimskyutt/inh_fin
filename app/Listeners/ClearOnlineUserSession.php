<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\OnlineUser;

class ClearOnlineUserSession
{
    public function handle(Logout $event)
    {
        if ($event->user) {
            OnlineUser::where('user_id', $event->user->id)->delete();
        }
    }
}