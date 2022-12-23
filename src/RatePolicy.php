<?php

namespace Musti\RatePolicy;

use Illuminate\Http\Request;

class RatePolicy
{
    public $ratePolicy;
    public $route;

    public function apply($ratePolicy)
    {
        $this->ratePolicy = new $ratePolicy;
        $this->route = new Route(request()->route());
        
        if ($this->doesRatePolicyHaveActionMethod($this->currentAction())) {
            return $this->runActionMethod();
        }
    }

    public function getRatePolicy()
    {
        return $this->ratePolicy;
    }

    public function currentAction()
    {
        return $this->route->resolveRouteActionMethod();
    }

    public function doesCurrentActionMatch($action)
    {
        return $this->currentAction() === $action;
    }

    public function doesRatePolicyHaveActionMethod($method)
    {
        return method_exists($this->getRatePolicy(), $method);
    }

    public function runActionMethod()
    {
        return $this->getRatePolicy()->{$this->currentAction()}();
    }


    
}
