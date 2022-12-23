<?php

namespace Musti\RatePolicy;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;

class RatePolicy
{
    public $ratePolicy;
    public $route;

    public $methodMapping = [
        'index' => 'viewAny',
        'show' => 'view',
        'store' => 'create',
        'update' => 'update',
        'destroy' => 'delete',
    ];

    public function apply($ratePolicy)
    {
        $this->ratePolicy = new $ratePolicy;
        $this->route = new Route(request()->route());

        if($this->ratePolicyHasActionMethod($this->currentAction())){
            return $this->callActionMethod($this->currentAction());
        }

        if($this->doesCurrentActionMatchAnyMappedMethods($this->currentAction())){

            if($this->ratePolicyHasActionMethod($this->methodMapping[$this->currentAction()])){
                return $this->callActionMethod($this->methodMapping[$this->currentAction()]);
            }
        }
    }

    public function callRateLimitMethod($method)
    {
        return $this->ratePolicy->$method();
    }

    public function doesCurrentActionMatchAnyMappedMethods($action)
    {
        return array_key_exists($action, $this->methodMapping);
    }

    public function getRatePolicy()
    {
        return $this->ratePolicy;
    }

    public function currentAction()
    {
        return $this->route->resolveRouteActionMethod();
    }

    public function currentController()
    {
        return $this->route->resolveRouteControllerName();
    }

    public function doesCurrentActionMatch($action)
    {
        return $this->currentAction() === $action;
    }

    public function ratePolicyHasActionMethod($method)
    {
        return method_exists($this->getRatePolicy(), $method);
    }

    public function callActionMethod(string $method)
    {
        return $this->getRatePolicy()->call($method);
    }
}
