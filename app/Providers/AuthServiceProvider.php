<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        'App\Models\Conversation' => 'App\Policies\ConversationPolicy',
        'App\Models\Post' => 'App\Policies\PostPolicy',
        'App\Models\Comment' => 'App\Policies\CommentPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Define a gate for accessing the homeowner dashboard
        \Gate::define('access-homeowner-dashboard', function ($user) {
            return in_array($user->role, ['Homeowner', 'Admin']);
        });
    }
}
