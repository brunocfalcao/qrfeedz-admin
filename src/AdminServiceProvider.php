<?php

namespace QRFeedz\Admin;

use Laravel\Nova\Nova;
use Illuminate\Support\ServiceProvider;
use QRFeedz\Admin\Resources\User;

class AdminServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Nova::resources([
            User::class,
        ]);
    }

    public function register()
    {
        //
    }
}
