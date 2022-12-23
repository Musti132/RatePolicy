<?php

namespace Musti\RatePolicy;

use Illuminate\Routing\Route as IlluminateRoute;

class Route
{
    public function __construct(public IlluminateRoute $route)
    {
        $this->route = $route;
    }

    public function resolveRouteActionMethod()
    {
        return $this->route->getActionMethod();
    }

    public function resolveRouteActionClass()
    {
        return $this->route->getActionName();
    }

    public function resolveRouteAction()
    {
        return $this->route->getAction();
    }

    public function resolveRouteControllerName()
    {
        return class_basename($this->route->getControllerClass());
    }
}
