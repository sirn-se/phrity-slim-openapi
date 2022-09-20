<?php

/**
 * File for Slim OpenApi tests.
 * @package Phrity > Slim > OpenApi
 */

declare(strict_types=1);

namespace Phrity\Slim;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Phrity\Slim\OpenApi;
use Slim\Factory\AppFactory;

/**
 * Numerics ceil test class.
 */
class OpenApiTest extends TestCase
{
    /**
     * Set up for all tests
     */
    public function setUp(): void
    {
        error_reporting(-1);
    }

    // Test a simple schema with paths
    public function testRoutesWithValidation(): void
    {
        $openapi = new OpenApi(__DIR__ . '/schemas/openapi-1.json', ['validate' => true]);
        $routes = [];
        foreach ($openapi as $route) {
            $routes[] = "{$route}";
        }
        $this->assertEquals([
            '/test.get > Test/MyController',
            '/test.put > Test/MyController:put',
            '/another.get > Test\\YourController',
            '/another.put > Test\\YourController',
        ], $routes);
    }

    // Test a schema without any paths defined
    public function testEmptyWithoutValidation(): void
    {
        $openapi = new OpenApi(__DIR__ . '/schemas/openapi-empty.json');
        $routes = [];
        foreach ($openapi as $route) {
            $routes[] = "{$route}";
        }
        $this->assertEmpty($routes);
    }

    // Test a schema without any paths defined
    public function testEmptyWithValidation(): void
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('[] OpenApi is missing required property: paths');
        $openapi = new OpenApi(__DIR__ . '/schemas/openapi-empty.json', ['validate' => true]);
    }

    // Test a schema with operationId missing
    public function testNoopWithoutValidation(): void
    {
        $openapi = new OpenApi(__DIR__ . '/schemas/openapi-noop.json');
        $routes = [];
        foreach ($openapi as $route) {
            $routes[] = "{$route}";
        }
        $this->assertEquals([
            '/test.get > Test/MyController',
        ], $routes);
    }

    // Test a schema with operationId missing
    public function testNoopWithValidation(): void
    {
        $openapi = new OpenApi(__DIR__ . '/schemas/openapi-noop.json', ['validate' => true]);
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Route /test:put is missing operationId');
        foreach ($openapi as $route) {}
    }

    // Test Slim routing
    public function testRouting(): void
    {
        $slim = AppFactory::create();
        $openapi = new OpenApi(__DIR__ . '/schemas/openapi-1.json', ['validate' => true]);
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
}