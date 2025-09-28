<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OnlineUser;

class CleanupOnlineUsers extends Command
{
    protected $signature = 'online-users:cleanup';
    protected $description = 'Remove old online user sessions';

    public function handle()
    {
        // Delete sessions older than 5 minutes
        $cutoff = now()->subMinutes(5);
        OnlineUser::where('updated_at', '<', $cutoff)->delete();
        
        $this->info('Old online user sessions cleaned up.');
    }
}