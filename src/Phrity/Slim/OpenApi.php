<?php

/**
 * Route collector for Slim
 */

namespace Phrity\Slim;

use cebe\openapi\Reader;
use Slim\App;
use IteratorAggregate;
use RuntimeException;
use Traversable;

class OpenApi implements IteratorAggregate
{
    private $openapi;
    private $settings;
    private static $defaultsettings = [
        'validate' => false,
    ];

    public function __construct(string $file, array $settings = [])
    {
        $this->openapi = Reader::readFromJsonFile($file);
        $this->settings = (object)array_merge(self::$defaultsettings, $settings);
        if ($this->settings->validate && !$this->openapi->validate()) {
            throw new RuntimeException(implode(', ', $this->openapi->getErrors()));
        }
    }

    // Iterate possible routes
    public function getIterator(): Traversable
    {
        return (function () {
            if (empty($this->openapi->paths)) {
                return [];
            }
            foreach ($this->openapi->paths as $path => $pathItems) {
                foreach ($pathItems->getOperations() as $method => $operation) {
                    if (empty($operation->operationId)) {
                        if ($this->settings->validate) {
                            throw new RuntimeException("Route {$path}:{$method} is missing operationId");
                        }
                        continue; // Unusable
                    }
                    yield new Route($path, $method, $operation->operationId);
                }
            }
        })();
    }

    // Register route on Slim
    public function route(App $slim): void
    {
        foreach ($this->getIterator() as $route) {
            $controller = $this->getController($route->operation);
            $slim_route = call_user_func([$slim, $route->method], $route->path, $controller);
            $slim_route->setName($route->operation);
        }
    }

    // Format class name
    private function getController(string $operation): string
    {
        return str_replace('/', "\\", $operation);
    }
}
