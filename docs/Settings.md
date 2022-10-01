# Settings

[Basics](Basics.md) • Settings • [Validation](Validation.md) • [Extras](Extras.md)


## Available settings

| Setting | Type | Default |
| --- | --- | --- |
| `controller_method` | `boolean` | `false` |
| `controller_prefix` | `string` | `""` |
| `route_bind` | `boolean` | `false` |
| `strict` | `boolean` | `false` |

## How to use

OpenApi constructor takes array of settings as second aargument.

```php
use Phrity\Slim\OpenApi;
use Slim\Factory\AppFactory;

$slim = AppFactory::create();
$openapi = new OpenApi('openapi.json', [
    'controller_method' => true,
    'controller_prefix' => "Test/",
    'route_bind' => true,
    'strict' => true,
]);
$openapi->route($slim);
$slim->run();
```

## Descriptions

### `controller_method`

If set to `true` and method is not defined in `operationId`, requests will be mapped to methods that correspond to HTTP methods.
Standard HTTP methods are `get`, `post`, `put`, `delete`, `head`, `patch` and `options`.
If method is defined in schema, this setting will have no effect.

See [example](Basics.md#automatic-method-mapping).


### `controller_prefix`

Will prefix `operationId` when mapping container classes. Typically used to hide namespace paths from OpenApi schema.

See [example](Basics.md#using-class-prefix).


### `route_bind`

Will attach corresponding OpenApi route as a request attribute.
It can then be accessed in your controller as `openapi-route`.

```php
class MyController
{
    public function get($request, $response, array $arguments)
    {
        $route = $request->getAttribute('openapi-route');
        return $response;
    }
}
```


### `strict`

Will validate OpenApi schema when loaded, and throw `RuntimeException` if schema is invalid.

