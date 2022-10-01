# Basics

Basics • [Settings](Settings.md) • [Validation](Validation.md) • [Extras](Extras.md)


## Basic operation

The `operationId` may or may not define method to call on class. If not specified, `__invoke()` will be called.

`openapi.json` source
```json
{
    "openapi": "3.0.0",
    "paths": {
        "/test": {
            "get": {
                "operationId": "Test/MyController"
            },
            "put": {
                "operationId": "Test/MyController:put"
            }
        }
    }
}
```

PHP code
```php
use Phrity\Slim\OpenApi;
use Slim\Factory\AppFactory;

$slim = AppFactory::create();
$openapi = new OpenApi('openapi.json');
$openapi->route($slim);
$slim->run();
```

Constructor accepts OpenApi spcification as JSON, YAML or a [pre-parsed schema](https://github.com/cebe/php-openapi#reading-api-description-files).
```php
$openapi = new OpenApi('openapi.json');
```
```php
$openapi = new OpenApi('openapi.yaml');
```
```php
$schema = cebe\openapi\Reader::readFromJsonFile('openapi.json');
$openapi = new OpenApi($schema);
```

Call results per operation
```
GET /test
```
```php
(new Test\\MyController())->__invoke(ServerRequestInterface $request, ResponseInterface $response, array $attributes): ResponseInterface
```
```
PUT /test
```
```php
(new Test\\MyController())->put(ServerRequestInterface $request, ResponseInterface $response, array $attributes): ResponseInterface
```


## Using class prefix

By defining `controller_prefix` setting, all specified `operationId` will be prefixed.
This allow OpenApi schema to "hide" class namespaces.

`openapi.json` source
```json
{
    "openapi": "3.0.0",
    "paths": {
        "/test": {
            "get": {
                "operationId": "MyController"
            },
            "put": {
                "operationId": "MyController:put"
            }
        }
    }
}
```

PHP code
```php
use Phrity\Slim\OpenApi;
use Slim\Factory\AppFactory;

$slim = AppFactory::create();
$openapi = new OpenApi('openapi.json', [
    'controller_prefix' => 'Test/',
]);
$openapi->route($slim);
$slim->run();
```

Call results per operation
```
GET /test
```
```php
(new Test\\MyController())->__invoke(ServerRequestInterface $request, ResponseInterface $response, array $attributes): ResponseInterface
```
```
PUT /test
```
```php
(new Test\\MyController())->put(ServerRequestInterface $request, ResponseInterface $response, array $attributes): ResponseInterface
```

## Automatic method mapping

By setting `controller_method` to true, `operationId` specified without a method will be called using current HTTP method.
If method is specified, it will be used even if this setting is true.

`openapi.json` source
```json
{
    "openapi": "3.0.0",
    "paths": {
        "/test": {
            "get": {
                "operationId": "MyController"
            },
            "put": {
                "operationId": "MyController:custom"
            }
        }
    }
}
```

PHP code
```php
use Phrity\Slim\OpenApi;
use Slim\Factory\AppFactory;

$slim = AppFactory::create();
$openapi = new OpenApi('openapi.json', [
    'controller_method' => true,
]);
$openapi->route($slim);
$slim->run();
```

Call results per operation
```
GET /test
```
```php
(new Test\\MyController())->get(ServerRequestInterface $request, ResponseInterface $response, array $attributes): ResponseInterface
```
```
PUT /test
```
```php
(new Test\\MyController())->custom(ServerRequestInterface $request, ResponseInterface $response, array $attributes): ResponseInterface
```
