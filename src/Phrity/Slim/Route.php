<?php

/**
 * File for Slim OpenApi tests.
 * @package Phrity > Slim > OpenApi
 */

namespace Phrity\Slim;

use Slim\App;

/**
 * Slim OpenApi route instance
 */
class Route
{
    private $settings;
    private $path;
    private $method;
    private $operation;
    private $controller;

    /**
     * Constructor Route instance
     * @param object $settings  Optional settings
     * @param string $path      Route path
     * @param string $method    Route method
     * @param string $operation Route operation
     */
    public function __construct(object $settings, string $path, string $method, string $operation)
    {
        $this->settings = $settings;
        $this->path = $path;
        $this->method = $method;
        $this->operation = $operation;
        $this->buildController();
    }

    /**
     * Get string representation of route
     * @return string           String representation of route
     */
    public function __toString(): string
    {
        return "{$this->path}.{$this->method} > {$this->controller}";
    }

    /**
     * Register route on Slim
     * @param Slim\App          Slim instance to register routes on
     */
    public function route(App $slim): void
    {
        $slim_route = call_user_func([$slim, $this->method], $this->path, $this->controller);
        $slim_route->setName($this->operation);
    }

    /**
     * Build controller definition
     */
    private function buildController(): void
    {
        $op = "{$this->settings->controller_prefix}{$this->operation}";
        if ($this->settings->controller_method && preg_match('/(:[a-z]*)$/', $this->operation) == 0) {
            $op .= ":" . strtolower($this->method);
        }
        $this->controller = str_replace('/', "\\", $op);
    }
}
