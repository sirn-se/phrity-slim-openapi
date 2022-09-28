# Settings

[Basics](Basics.md) • Settings • [Extras](Extras.md)


## Available settings

| Setting | Type | Default |
| --- | --- | --- |
| `strict` | `boolean` | `false` |
| `controller_prefix` | `string` | `""` |
| `controller_method` | `boolean` | `false` |

## How to use

OpenApi constructor takes array of settings as second aargument.

```php
use Phrity\Slim\OpenApi;
use Slim\Factory\AppFactory;

$slim = AppFactory::create();
$openapi = new OpenApi('openapi.json', [
    'strict' => true,
    'controller_prefix' => "Test/",
    'controller_method' => true,
]);
```

## Description

### `strict`

Will validate OpenApi schema when loaded, and throw `RuntimeException` if schema is invalid.

### `controller_prefix`

Will prefix `operationId` when mapping container classes. Typically used to hide namespace paths from OpenApi schema.

See [example](Basics.md#using-class-prefix).

### `controller_method`

If set to `true` and method is not defined in `operationId`, requests will be mapped to methods that correspond to HTTP methods.
Standard HTTP methods are `get`, `post`, `put`, `delete`, `head`, `patch` and `options`.
If method is defined in schema, this setting will have no effect.

See [example](Basics.md#automatic-method-mapping).

