# Settings

[Basics](Basics.md) • Settings • [Extras](docs/Extras.md)


## Available settings

| Setting | Type | Default |
| --- | --- | --- |
| `strict` | `boolean` | `false` |
| If `true`, will validate OpenApi schema and throw exception on error |
| `controller_prefix` | `string` | `""`
| Prefix `operationId` when creating controller class name |
| `controller_method` | `boolean` | `false` |
Add current HTTP method (get, put, etc) if not specified in schema |

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

## `strict` setting

Will validate OpenApi schema when loaded, and throw `RuntimeException` if schema is invalid.

## `controller_prefix` setting

Will prefix `operationId` when mapping container classes. Typically used to hide namespace paths from OpenApi schema.

See [example](Basics.md#using-class-prefix).

## `controller_method` setting

If set to `true` and method is not defined in `operationId`, requests will be mapped to methods that correspond to HTTP methods.
Standard HTTP methods are `get`, `post`, `put`, `delete`, `head`, `patch` and `options`.
If method is defined in schema, this setting will have no effect.

See [example](Basics.md#automatic-method-mapping).

