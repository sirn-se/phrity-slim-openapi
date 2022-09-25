<?php

/**
 * File for Slim OpenApi tests
 * @package Phrity > Slim > OpenApi
 */

declare(strict_types=1);

namespace Phrity\Slim;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Phrity\Slim\OpenApi;
use Slim\Factory\AppFactory;

/**
 * Slim OpenApi routing test class
 */
class RoutingTest extends TestCase
{
    /**
     * Set up for all tests
     */
    public function setUp(): void
    {
        error_reporting(-1);
    }

    /**
     * Test Slim routing
     */
    public function testRouting(): void
    {
        $slim = AppFactory::create();
        $openapi = new OpenApi(__DIR__ . '/schemas/openapi-1.json', ['strict' => true]);
        $openapi->route($slim);

        $request = new ServerRequest('GET', '/test');
        $response = $slim->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("MyController::__invoke", $response->getBody()->__toString());

        $request = new ServerRequest('PUT', '/test');
        $response = $slim->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("MyController::put", $response->getBody()->__toString());
    }

    /**
     * Test Slim routing failure with invalid OpenApi schema
     */
    public function testRoutingEmpty(): void
    {
        $slim = AppFactory::create();
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('[] OpenApi is missing required property: paths');
        $openapi = new OpenApi(__DIR__ . '/schemas/openapi-empty.json', ['strict' => true]);
    }

    /**
     * Test Slim routing failure with missing operationId
     */
    public function testRoutingNoop(): void
    {
        $slim = AppFactory::create();
        $openapi = new OpenApi(__DIR__ . '/schemas/openapi-noop.json', ['strict' => true]);
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Route /test:put is missing operationId');
        $openapi->route($slim);
    }

    /**
     * Test Slim routing failure on unrouted request path
     */
    public function testRoutingNoRoute(): void
    {
        $slim = AppFactory::create();
        $openapi = new OpenApi(__DIR__ . '/schemas/openapi-1.json', ['strict' => true]);
        $openapi->route($slim);
        $request = new ServerRequest('GET', '/unexisting');
        $this->expectException('Slim\Exception\HttpNotFoundException');
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('Not found.');
        $response = $slim->handle($request);
    }
}
