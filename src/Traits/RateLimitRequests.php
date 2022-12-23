<?php
namespace Musti\RatePolicy\Traits;

use Musti\RatePolicy\RatePolicy;

trait RateLimitRequests {
    public $ratePolicy;

    public function applyRatePolicy($ratePolicy) : void
    {
        $instance = app(RatePolicy::class);
        
        $instance->apply($ratePolicy);

        $this->ratePolicy = $instance->ratePolicy;
    }

    public function getRatePolicy() : RatePolicy
    {
        return app(RatePolicy::class);
    }

    public function isRateLimitted(): bool
    {
        return $this->ratePolicy->remaining() <= 0;
    }

    public function getMaxAttempts(string $method): int
    {
        return $this->ratePolicy->rateLimitForMethods[$method] ?? $this->maxAttempts;
    }

    public function getRatePolicyKey(): string
    {
        return $this->ratePolicy->getRatePolicyKey();
    }
}