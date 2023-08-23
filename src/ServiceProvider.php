<?php

namespace Mactape\IsDayOff;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/logging.php', 'logging'
        );
    }

    public function boot()
    {
    }
}
