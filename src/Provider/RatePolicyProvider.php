<?php

namespace Musti\RatePolicy\Provider;

use Illuminate\Support\ServiceProvider;
use Musti\RatePolicy\Console\Commands\RatePolicyCommand;

class RatePolicyProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([
            RatePolicyCommand::class,
        ]);

    }
}
