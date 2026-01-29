<?php

namespace App\Providers;

use App\Services\AuthResolver;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Broadcast::routes(['middleware' => ['web', 'auth.any']]);
        
        Broadcast::resolveAuthenticatedUserUsing(function () {
            return AuthResolver::resolve();
        });
    }
}
