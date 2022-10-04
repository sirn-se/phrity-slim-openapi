<?php

/**
 * File for Slim OpenApi tests.
 * @package Phrity > Slim > OpenApi
 */

namespace Phrity\Slim;

use cebe\openapi\spec\OpenApi as OpenApiSpec;
use cebe\openapi\Reader;
use IteratorAggregate;
use League\OpenAPIValidation\PSR7\{
    ValidatorBuilder,
    ResponseValidator,
    RoutedServerRequestValidator
};
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
    private ValidatorBuilder $validation_builder;
    private object $settings;
    private static array $defaultsettings = [
        'controller_prefix' => '',
        'controller_method' => false,
        'route_bind'        => false,
        'strict'            => false,
        'validate_request'  => false,
        'validate_response' => false,
    ];

    /**
     * Constructor for Slim OpenApi route generator.
     * @param OpenApiSpec|string $source    File path to OpenApi schema or OpenApi instance
     * @param array<string,mixed> $settings Optional settings
     */
    public function __construct($source, array $settings = [])
    {
        $this->openapi = $this->readSpec($source);
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
                    yield new Route($this, $path, $method, $operation);
                }
            }
        })();
    }

    /**
     * Register routes on Slim App.
     * @param App $app                      Slim App instance to register routes on
     */
    public function route(App $app): void
    {
        foreach ($this->getIterator() as $route) {
            $route->route($app);
        }
    }

    /**
     * Get a setting.
     * @param string $key                   Setting key
     * @return mixed                        Setting value
     */
    public function getSetting(string $key)
    {
        return isset($this->settings->$key) ? $this->settings->$key : null;
    }

    /**
     * Get request validator.
     * @return RoutedServerRequestValidator Validator
     */
    public function getRequestValidator(): RoutedServerRequestValidator
    {
        return $this->getValidatorBuilder()->getRoutedRequestValidator();
    }

    /**
     * Get response validator.
     * @return ResponseValidator            Validator
     */
    public function getResponseValidator(): ResponseValidator
    {
        return $this->getValidatorBuilder()->getResponseValidator();
    }

    /**
     * Get validator builder.
     * @return ValidatorBuilder             Builder
     */
    private function getValidatorBuilder(): ValidatorBuilder
    {
        if (empty($this->validation_builder)) {
            $this->validation_builder = (new ValidatorBuilder())->fromSchema($this->openapi);
        }
        return $this->validation_builder;
    }

    /**
     * Read OpenApi source from json or yaml file.
     * @param OpenApiSpec|string $source    File path to OpenApi schema or OpenApi instance
     * @return OpenApiSpec                  OpenApi specification
     */
    private function readSpec($source): OpenApiSpec
    {
        if ($source instanceof OpenApiSpec) {
            return $source; // Already parsed
        }
        if (is_string($source)) {
            $source = realpath($source) ?: $source;
            if (!is_readable($source)) {
                throw new RuntimeException("Source file {$source} do not exist or is not readable");
            }
            switch (pathinfo($source, PATHINFO_EXTENSION)) {
                case 'json':
                    return Reader::readFromJsonFile($source);
                case 'yml':
                case 'yaml':
                    return Reader::readFromYamlFile($source);
            }
        }
        throw new RuntimeException("Could not parse {$source}, invalid file format");
    }
}
