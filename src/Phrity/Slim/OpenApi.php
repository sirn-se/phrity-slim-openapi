<?php

/**
 * File for Slim OpenApi tests.
 * @package Phrity > Slim > OpenApi
 */

namespace Phrity\Slim;

use cebe\openapi\spec\OpenApi as OpenApiSpec;
use cebe\openapi\Reader;
use IteratorAggregate;
use RuntimeException;
use Slim\App;
use Traversable;

/**
 * Slim OpenApi route generator.
 * @implements IteratorAggregate<Route>
 */
class OpenApi implements IteratorAggregate
{
    private OpenApiSpec $openapi;
    private object $settings;
    private static array $defaultsettings = [
        'strict' => false,
        'controller_prefix' => '',
        'controller_method' => false,
    ];

    /**
     * Constructor for Slim OpenApi route generator.
     * @param string $file                  File path to OpenApi schema
     * @param array<string,mixed> $settings Optional settings
     */
    public function __construct(string $file, array $settings = [])
    {
        $this->openapi = $this->readFile($file);
        $this->settings = (object)array_merge(self::$defaultsettings, $settings);
        if ($this->settings->strict && !$this->openapi->validate()) {
            throw new RuntimeException(implode(', ', $this->openapi->getErrors()));
        }
    }

    /**
     * Iterate possible routes in OpenApi schema.
     * @return Traversable<Route>           List of Phrity\Slim\Route instances
     */
    public function getIterator(): Traversable
    {
        return (function () {
            if (empty($this->openapi->paths)) {
                return [];
            }
            foreach ($this->openapi->paths as $path => $pathItems) {
                foreach ($pathItems->getOperations() as $method => $operation) {
                    if (empty($operation->operationId)) {
                        if ($this->settings->strict) {
                            throw new RuntimeException("Route {$path}:{$method} is missing operationId");
                        }
                        continue; // Unusable
                    }
                    yield new Route($this->settings, $path, $method, $operation->operationId);
                }
            }
        })();
    }

    /**
     * Register routes on Slim App.
     * @param \Slim\App $app                Slim App instance to register routes on
     */
    public function route(App $app): void
    {
        foreach ($this->getIterator() as $route) {
            $route->route($app);
        }
    }

    /**
     * Read OpenApi source from json or yaml file.
     * @param string $file                  File path to OpenApi schema
     * @return OpenApiSpec                  OpenApi specification
     */
    private function readFile(string $file): OpenApiSpec
    {
        if (!is_readable($file)) {
            throw new RuntimeException("Source file {$file} do not exist or is not readable");
        }
        switch (pathinfo($file, PATHINFO_EXTENSION)) {
            case 'json':
                return Reader::readFromJsonFile($file);
            case 'yml':
            case 'yaml':
                return Reader::readFromYamlFile($file);
        }
        throw new RuntimeException("Could not parse {$file}, invalid file format");
    }
}
