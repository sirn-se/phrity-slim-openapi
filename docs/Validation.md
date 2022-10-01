# Validation

[Basics](Basics.md) • [Settings](Settings.md) • Validation • [Extras](Extras.md)

The OpenApi Slim adapter supports request and response validation through [Leauge OpenAPI PSR-7 Message Validator](https://github.com/thephpleague/openapi-psr7-validator).

When used, it will validate a request and/or response against the OpenApi specification.


## Manual validation

To expose the validators, create the OpenApi adapter with setting `route_bind` set to true.
This will enable a Controller to access functions on the adapter framework.

PHP router code
```php
use Phrity\Slim\OpenApi;
use Slim\Factory\AppFactory;

$slim = AppFactory::create();
$openapi = new OpenApi('openapi.json', ['route_bind' => true]);
$openapi->route($slim);
$slim->run();
```

### Request validation

PHP controller code
```php
class MyController
{
    public function post(Request $request, Response $response, array $arguments)
    {
        // Get the OpenApi route instance bound to this request
        $route = $request->getAttribute('openapi-route');

        // Call the validator - will throw exception on validation failure
        $route->validateRequest($request);

        // Do things with $response

        return $response;
    }
}
```

### Response validation

PHP controller code
```php
class MyController
{
    public function post(Request $request, Response $response, array $arguments)
    {
        // Get the OpenApi route instance bound to this request
        $route = $request->getAttribute('openapi-route');

        // Do things with $response

        // Call the validator - will throw exception on validation failure
        $route->validateResponse($response);

        return $response;
    }
}
```

