<?php

namespace Musti\RatePolicy;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\RateLimiter;

class RateLimits
{
    protected $controller = Controller::class;

    protected $maxAttempts = 10;

    protected $rateLimitForMethods = [];

    public function call($name)
    {
        // Check if method is rate limited
        if($this->isRateLimitted()){
            throw new HttpResponseException($this->{$name}());
        }

        $this->hit();
    }

    public function isRateLimitted(): bool
    {
        return $this->remaining() <= 0;
    }

    public function getMaxAttempts(string $method): int
    {
        return $this->rateLimitForMethods[$method] ?? $this->maxAttempts;
    }

    public function getRatePolicyKey(): string
    {
        //Create a key based on the controller name, action and request ip
        return $this->getControllerName() . '.' . $this->getControllerAction() . ':' . request()->ip();
    }

    public function getControllerName(): string
    {
        return (new \ReflectionClass($this->controller))->getShortName();
    }

    public function getControllerAction(): string
    {
        return request()->route()->getActionMethod();
    }

    public function hit(): bool
    {
        return RateLimiter::hit($this->getRatePolicyKey());
    }

    public function remaining(): int
    {
        return RateLimiter::remaining($this->getRatePolicyKey(), $this->getMaxAttempts($this->getControllerAction()));
    }

    public function attempts(): int
    {
        return RateLimiter::attempts($this->getRatePolicyKey());
    }

    public function clear(): void
    {
        RateLimiter::clear($this->getRatePolicyKey());
    }
}
