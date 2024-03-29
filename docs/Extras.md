# Extras

[Basics](Basics.md) • [Settings](Settings.md) • [Validation](Validation.md) • Extras

## Using Slim with Container

If a Container has been added to Slim, it can be used to resolve mappings to controllers.
First, Slim will check the Container if it has an item with key matching `operationId` in OpenApi schema.
If so, it expects the Container to return a controller class to be used.
If no match it will attempt to resolve as usual.

If a Container is used, it will always be passed to controller constructor.

`openapi.json` source
```json
{
    "openapi": "3.0.0",
    "paths": {
        "/test": {
            "get": {
                "operationId": "MyContainerKey1"
            },
            "put": {
                "operationId": "MyContainerKey2"
            }
        }
    }
}
```

PHP code
```php
use DI\Container; // Or any other ContainerInterface implementation
use Phrity\Slim\OpenApi;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;

$container = new Container();
$container->set('MyContainerKey1', function (ContainerInterface $container) {
    return new \Test\MyController($container);
});

$slim = AppFactory::create(null, $container);
$openapi = new OpenApi('openapi.json');
$openapi->route($slim);
$slim->run();
```

Call results per operation
```
GET /test
```
```php
(new Test\\ContainerController(ContainerInterface $container))->__invoke(ServerRequestInterface $request, ResponseInterface $response, array $attributes): ResponseInterface
```
```
PUT /test
```
```php
(new Test\\MyController(ContainerInterface $container))->__invoke(ServerRequestInterface $request, ResponseInterface $response, array $attributes): ResponseInterface
```

## DIY

If you want to add routes to Slim yourself, you can traverse OpenApi to get all routes defined in schema.
```php
use Phrity\Slim\OpenApi;
use Slim\Factory\AppFactory;

$slim = AppFactory::create();
$openapi = new OpenApi('openapi.json');

foreach ($openapi as $route) {
    // $my_callable may be a function or Slim compatible class/method reference
    $slim_route = call_user_func([$slim, $route->method], $route->path, $my_callable);
}
$slim->run();
```

Or you can use the returned Route instance to attach it to Slim.
```php
use Phrity\Slim\OpenApi;
use Slim\Factory\AppFactory;

$slim = AppFactory::create();
$openapi = new OpenApi('openapi.json');

foreach ($openapi as $route) {
    $route->route($slim);
}
$slim->run();
```
