[![Build Status](https://github.com/sirn-se/phrity-slim-openapi/actions/workflows/acceptance.yml/badge.svg)](https://github.com/sirn-se/phrity-slim-openapi/actions)
[![Coverage Status](https://coveralls.io/repos/github/sirn-se/phrity-slim-openapi/badge.svg?branch=master)](https://coveralls.io/github/sirn-se/phrity-slim-openapi?branch=master)

# OpenApi for Slim v4

Adapter that reads [OpenApi](https://spec.openapis.org) schema and add included routes to [Slim](https://www.slimframework.com).
By defining `operationId` in OpenApi schema, the adapter will automatically instanciate and call referenced controller class.

## Installation

Install with [Composer](https://getcomposer.org/);
```
composer require phrity/slim-openapi
```

## How to use

```php
use Phrity\Slim\OpenApi;
use Slim\Factory\AppFactory;

// Create Slim App as you normally would
$slim = AppFactory::create();

// Create OpenApi adapter with OpenApi source
$openapi = new OpenApi('openapi.json');

// Push all routes from OpenApi to Slim
$openapi->route($slim);

// Run Slim
$slim->run();
```

## How to define controllers

In order for automatic mapping to work, the `operationId` must be set on all defined routes in OpenApi source.
If no method is specified, class method `__invoke()` will be called on class.

| With invoke | With method |
| --- | --- |
| `Classname` | `Classname:method` |
| `Namespace/Classname` | `Namespace/Classname:method` |
| `Namespace\\Classname` | `Namespace\\Classname:method` |

### Example

```json
{
    "openapi": "3.0.0",
    "paths": {
        "/test": {
            "get": {
                "operationId": "Test/MyController",
                "description": "Will invoke on class Test\\MyController"
            },
            "put": {
                "operationId": "Test\\MyController:put",
                "description": "Will call method put() on class Test\\MyController"
            }
        }
    }
}
```

## Settings

| Setting | Default | Description |
| --- | --- | --- |
| `strict` | `false` | If `true`, will validate OpenApi schema and throw exception on error |
| `controller_prefix` | `""` | Prefix `operationId` when creating controller class name |
| `controller_method` | `false` | Add current HTTP method (get, put, etc) if not specified in schema |


# Documentation

[Basics](docs/Basics.md)


## Versions

| Version | PHP | |
| --- | --- | --- |
| `1.1` | `^7.4\|^8.0` | Settings & helpers |
| `1.0` | `^7.4\|^8.0` | Route registry |
