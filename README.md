# RatePolicy
[![Generic badge](https://img.shields.io/badge/PHP-8.0%2B-green.svg)](#)

### Description
This package is made for fun, it doesn't really have any useability, as this was more for learning how to create packages for Laravel specifically.
It creates Rate limits for controllers like you would make Policies in laravel, it does this by User IP.
Also makes it easier to handle ratelimitting for controllers.

### Installation
Install via composer 

```bash
 composer require musti/rate-policy
 ```

### Usage


First you need to add the RateLimitRequests trait to base Controller
```php
use Musti\RatePolicy\Traits\RateLimitRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, RateLimitRequests;
}

```

Then create a policy via the command 
```bash
 php artisan make:rate-policy {name}
```
This will create a RatePolicy under app/RatePolicies

Remember to change `protected $controller = Controller::class;` to desired controller

In any controller add this to the constructor

```php
use Musti\RatePolicy\RatePolicy;

class ChannelController extends Controller
{
    public function __construct(Request $request)
    {
        $this->applyRatePolicy(ChannelRatePolicy::class);
    }
}
```

In the RatePolicy that you have created you can add a method for the controller index method like so

```php
<?php
namespace App\RatePolicies;

use App\Http\Controllers\ChannelController;
use Musti\RatePolicy\RateLimits;

class ChannelRatePolicy extends RateLimits
{
    protected $controller = ChannelController::class;

    protected function message() {
        //Do something

        return response()->json([
            'message' => 'Too many requests',
        ], 429);
    }
}

```
The method has to return a response.

By default the max attempts is set to 10

To change the max attempts, simply add a $maxAttempts property to the RatePolicy

```php
protected $maxAttempts = 10;
```

You can also change the max attempts for a specific method by providing a $rateLimitForMethods property

```php
    protected $rateLimitForMethods = [
        'index' => 15, //index method is automatically translated to viewAny
        'store' => 5,
    ];
```

RatePolicy will only work if it can find a corresponding method from the controller.


### Resource
Just like Laravel Policies, Rate Policies will be mapped to corresponding controller methods

| Controller Method  | Rate Policy Method  |
| ------------ | ------------ |
|  index |  viewAny |
| show  |  view |
| create  |  create |
|  store | create  |
|  edit |  update |
|  destroy |  delete |
