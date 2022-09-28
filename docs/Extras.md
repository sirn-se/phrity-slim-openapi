# Extras

[Basics](docs/Basics.md) • [Settings](docs/Settings.md) • Extras

## Using Slim with Container

If a Container has been added to Slim, it can be used to resolve mappings to controllers.
First, Slim will check the Container if it has an item with key matching `operationId` in OpenApi schema.
If so, it expects the Container to return a controller class to be used.
If no match it will attempt to resolve as normal.

If a Container is used, it will always be passed to controller constructor.

`openapi.json` source
```json
{
    "openapi": "3.0.0",
    "paths": {
        "/test": {
            "get": {
                "operationId": "MyContainerController"
            },
            "put": {
                "operationId": "test/MyController"
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
$container->set('MyContainerController', function (ContainerInterface $container) {
    return new \Test\ContainerController($container);
});
$slim = AppFactory::create(null, $container);
$openapi = new OpenApi('openapi.json');
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

foreach ($openapiopenapi as $route) {
    // $my_callable may be a function or Slim compatible class/method reference
    call_user_func([$slim, $route->method], $route->path, $my_callable);
}
```

