<?php
namespace Musti\RatePolicy\Traits;

use Musti\RatePolicy\RatePolicy;

trait RateLimitRequests {

    public function apply($ratePolicy)
    {
        return app(RatePolicy::class)->apply($ratePolicy);
    }
}