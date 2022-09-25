<?php

/**
 * File for Slim OpenApi tests
 * @package Phrity > Slim > OpenApi
 */

declare(strict_types=1);

namespace Phrity\Slim;

use PHPUnit\Framework\TestCase;
use Phrity\Slim\OpenApi;

/**
 * Slim OpenApi controller test class
 */
class ControllerTest extends TestCase
{
    /**
     * Set up for all tests
     */
    public function setUp(): void
    {
        error_reporting(-1);
    }

    // Test a simple schema with paths
    /**
     *
     */
    public function testRoutesStrict(): void
    {
        $openapi = new OpenApi(__DIR__ . '/schemas/openapi-1.json', ['strict' => true]);
        $routes = [];
        foreach ($openapi as $route) {
            $routes[] = "{$route}";
        }
        $this->assertEquals([
            '/test.get > Test\\MyController',
            '/test.put > Test\\MyController:put',
            '/another.get > Test\\YourController',
            '/another.put > Test\\YourController',
        ], $routes);
    }

    /**
     * Test a schema without any paths defined
     */
    public function testEmptyNotStrict(): void
    {
        $openapi = new OpenApi(__DIR__ . '/schemas/openapi-empty.json');
        $routes = [];
        foreach ($openapi as $route) {
            $routes[] = "{$route}";
        }
        $this->assertEmpty($routes);
    }

    /**
     * Test a schema without any paths defined
     */
    public function testEmptyStrict(): void
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('[] OpenApi is missing required property: paths');
        $openapi = new OpenApi(__DIR__ . '/schemas/openapi-empty.json', ['strict' => true]);
    }

    /**
     * Test a schema with operationId missing
     */
    public function testNoopNotStrict(): void
    {
        $openapi = new OpenApi(__DIR__ . '/schemas/openapi-noop.json');
        $routes = [];
        foreach ($openapi as $route) {
            $routes[] = "{$route}";
        }
        $this->assertEquals([
            '/test.get > Test\\MyController',
        ], $routes);
    }

    /**
     * Test a schema with operationId missing
     */
    public function testNoopStrict(): void
    {
        $openapi = new OpenApi(__DIR__ . '/schemas/openapi-noop.json', ['strict' => true]);
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Route /test:put is missing operationId');
        foreach ($openapi as $route) {
            // Irrelevant
        }
    }

    /**
     * Test a schema with prefix and auto methods
     */
    public function testControllerMagic(): void
    {
        $openapi = new OpenApi(__DIR__ . '/schemas/openapi-2.json', [
            'strict' => true,
            'controller_prefix' => 'Test/',
            'controller_method' => true,
        ]);
        $routes = [];
        foreach ($openapi as $route) {
            $routes[] = "{$route}";
        }
        $this->assertEquals([
            '/test.get > Test\\MyController:get',
            '/test.put > Test\\MyController:custom',
            '/another.get > Test\\YourController:custom',
            '/another.put > Test\\YourController:put',
        ], $routes);
    }
}
