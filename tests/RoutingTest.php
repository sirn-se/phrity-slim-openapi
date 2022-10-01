<?php

/**
 * File for Slim OpenApi tests
 * @package Phrity > Slim > OpenApi
 */

declare(strict_types=1);

namespace Phrity\Slim;

use DI\Container;
use PHPUnit\Framework\TestCase;
use Phrity\Slim\OpenApi;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;
use Slim\Handlers\Strategies\RequestResponseArgs;

/**
 * Slim OpenApi routing test class
 */
class RoutingTest extends TestCase
{
    use FactoryTrait;

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

        $request = self::createServerRequest('GET', '/test');
        $response = $slim->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("MyController::__invoke", $response->getBody()->__toString());

        $request = self::createServerRequest('PUT', '/test');
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
        $request = self::createServerRequest('GET', '/unexisting');
        $this->expectException('Slim\Exception\HttpNotFoundException');
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('Not found.');
        $response = $slim->handle($request);
    }

    public function testRequestResponse(): void
    {
        $slim = AppFactory::create();
        $routeCollector = $slim->getRouteCollector();
        $openapi = new OpenApi(__DIR__ . '/schemas/openapi-2.json', [
            'strict' => true,
            'controller_prefix' => 'Test/',
        ]);
        $openapi->route($slim);
        $request = self::createServerRequest('GET', '/argument/something/else');
        $response = $slim->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("MyController::argument=something,else", $response->getBody()->__toString());
    }

    public function testRequestResponseArgs(): void
    {
        $slim = AppFactory::create();
        $routeCollector = $slim->getRouteCollector();
        $routeCollector->setDefaultInvocationStrategy(new RequestResponseArgs());
        $openapi = new OpenApi(__DIR__ . '/schemas/openapi-2.json', [
            'strict' => true,
            'controller_prefix' => 'Test/',
        ]);
        $openapi->route($slim);
        $request = self::createServerRequest('GET', '/arguments/something/else');
        $response = $slim->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("MyController::arguments=something,else", $response->getBody()->__toString());
    }

    public function testContainer(): void
    {
        $container = new Container();
        $container->set('ContainerController', function (ContainerInterface $container) {
            return new \Test\ContainerController($container);
        });
        $slim = AppFactory::create(null, $container);
        $openapi = new OpenApi(__DIR__ . '/schemas/openapi-container.json', ['strict' => true]);
        $openapi->route($slim);
        $request = self::createServerRequest('GET', '/test');
        $response = $slim->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("ContainerController::byContainer", $response->getBody()->__toString());
    }

    public function testRouteBind(): void
    {
        $slim = AppFactory::create();
        $openapi = new OpenApi(__DIR__ . '/schemas/openapi-2.json', [
            'strict' => true,
            'controller_prefix' => 'Test/',
            'route_bind' => true,
        ]);
        $openapi->route($slim);

        $request = self::createServerRequest('GET', '/bind');
        $response = $slim->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            "MyController::bind=/bind.get > Test\MyController:bind",
            $response->getBody()->__toString()
        );
    }
}
