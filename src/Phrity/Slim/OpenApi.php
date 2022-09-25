<?php

/**
 * File for Slim OpenApi tests.
 * @package Phrity > Slim > OpenApi
 */

namespace Phrity\Slim;

use cebe\openapi\Reader;
use IteratorAggregate;
use RuntimeException;
use Slim\App;
use Traversable;

/**
 * Slim OpenApi route generator
 */
class OpenApi implements IteratorAggregate
{
    private $openapi;
    private $settings;
    private static $defaultsettings = [
        'strict' => false,
        'controller_prefix' => '',
        'controller_method' => false,
    ];

    /**
     * Constructor for Slim OpenApi route generator
     * @param string $file      File path to OpenApi schema
     * @param array  $settings  Optional settings
     */
    public function __construct(string $file, array $settings = [])
    {
        $this->openapi = Reader::readFromJsonFile($file);
        $this->settings = (object)array_merge(self::$defaultsettings, $settings);
        if ($this->settings->strict && !$this->openapi->validate()) {
            throw new RuntimeException(implode(', ', $this->openapi->getErrors()));
        }
    }

    /**
     * Iterate possible routes in OpenApi schema
     * @return Traversable      List of Phrity\Slim\Route instances
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
     * Register routes on Slim
     * @param Slim\App          Slim instance to register routes on
     */
    public function route(App $slim): void
    {
        foreach ($this->getIterator() as $route) {
            $route->route($slim);
        }
    }
}
