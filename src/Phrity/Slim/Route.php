<?php

/**
 * File for Slim OpenApi tests.
 * @package Phrity > Slim > OpenApi
 */

namespace Phrity\Slim;

use cebe\openapi\spec\Operation;
use League\OpenAPIValidation\PSR7\OperationAddress;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\App;

/**
 * Slim OpenApi route instance
 */
class Route
{
    private OpenApi $openapi;
    private string $path;
    private string $method;
    private Operation $operation;
    private string $controller;

    /**
     * Constructor Route instance.
     * @param OpenApi $openapi      Backlink to OpenApi instance
     * @param string $path          Route path
     * @param string $method        Route method
     * @param Operation $operation  Route operation
     */
    public function __construct(OpenApi $openapi, string $path, string $method, Operation $operation)
    {
        $this->openapi = $openapi;
        $this->path = $path;
        $this->method = $method;
        $this->operation = $operation;
        $this->buildController();
    }

    /**
     * Get string representation of route.
     * @return string           String representation of route
     */
    public function __toString(): string
    {
        return "{$this->path}.{$this->method} > {$this->controller}";
    }

    /**
     * Register route on Slim App.
     * @param \Slim\App $app    Slim App instance to register routes on
     */
    public function route(App $app): void
    {
        $slim_route = call_user_func([$app, $this->method], $this->path, $this->controller);
        $slim_route->setName($this->operation->operationId);
        if ($this->openapi->getSetting('route_bind')) {
            $slim_route->add(function (Request $request, RequestHandler $handler) {
                return $handler->handle($request->withAttribute('openapi-route', $this));
            });
        }
    }

    public function validateRequest(Request $request): void
    {
        $this->openapi->getRequestValidator()->validate(
            new OperationAddress($this->path, $this->method),
            $request
        );
    }

    /**
     * Build controller definition.
     */
    private function buildController(): void
    {
        $controller_prefix = $this->openapi->getSetting('controller_prefix');
        $controller_method = $this->openapi->getSetting('controller_method');
        $op = "{$controller_prefix}{$this->operation->operationId}";
        if ($controller_method && preg_match('/(:[a-z]*)$/', $this->operation->operationId) == 0) {
            $op .= ":" . strtolower($this->method);
        }
        $this->controller = str_replace('/', "\\", $op);
    }
}
