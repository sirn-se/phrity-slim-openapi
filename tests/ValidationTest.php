<?php

/**
 * File for Slim OpenApi tests
 * @package Phrity > Slim > OpenApi
 */

declare(strict_types=1);

namespace Phrity\Slim;

use PHPUnit\Framework\TestCase;
use Phrity\Slim\OpenApi;
use Slim\Factory\AppFactory;

/**
 * Slim OpenApi validation test class
 */
class ValidationTest extends TestCase
{
    use FactoryTrait;

    private array $testdata = [
        'propA' => 1234,
        'propB' => "A property"
    ];

    /**
     * Set up for all tests
     */
    public function setUp(): void
    {
        error_reporting(-1);
    }

    /**
     * Test manual validation
     */
    public function testManualRequestValidation(): void
    {
        $slim = AppFactory::create();
        $openapi = new OpenApi(__DIR__ . '/schemas/validations.yaml', [
            'strict' => true,
            'route_bind' => true,
        ]);
        $openapi->route($slim);
        $stream = self::createStream(json_encode($this->testdata));
        $request = self::createServerRequest('POST', '/complete/test/1234');
        $request = $request->withHeader('X-RequestId', 'abcd');
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withCookieParams(['session_id' => 12345678]);
        $request = $request->withQueryParams(['limit' => 10, 'filtering' => 'yes']);
        $request = $request->withBody($stream);
        $response = $slim->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $body = $this->testdata;
        $body['route'] = "ValidatingController::post";
        $this->assertEquals(json_encode($body), $response->getBody()->__toString());
    }

    /**
     * Test manual request validation failure
     */
    public function testManualRequestValidationFailure(): void
    {
        $slim = AppFactory::create();
        $openapi = new OpenApi(__DIR__ . '/schemas/validations.yaml', [
            'strict' => true,
            'route_bind' => true,
        ]);
        $openapi->route($slim);
        $stream = self::createStream(json_encode($this->testdata));
        $request = self::createServerRequest('POST', '/complete/test/1234');
        $request = $request->withBody($stream);
        // Validation order may change, check for any validation exception
        $this->expectException('League\OpenAPIValidation\PSR7\Exception\ValidationFailed');
        $response = $slim->handle($request);
    }

    /**
     * Test manual response validation failure
     */
    public function testManualResponseValidationFailure(): void
    {
        $slim = AppFactory::create();
        $openapi = new OpenApi(__DIR__ . '/schemas/validations.yaml', [
            'strict' => true,
            'route_bind' => true,
        ]);
        $openapi->route($slim);
        $stream = self::createStream(json_encode(['invalid' => 'body']));
        $request = self::createServerRequest('POST', '/complete/test/1234');
        $request = $request->withHeader('X-RequestId', 'abcd');
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withCookieParams(['session_id' => 12345678]);
        $request = $request->withQueryParams(['limit' => 10, 'filtering' => 'yes']);
        $request = $request->withBody($stream);
        // Validation order may change, check for any validation exception
        $this->expectException('League\OpenAPIValidation\PSR7\Exception\ValidationFailed');
        $response = $slim->handle($request);
    }

    /**
     * Test middleware validation
     */
    public function testMiddlewareRequestValidation(): void
    {
        $slim = AppFactory::create();
        $openapi = new OpenApi(__DIR__ . '/schemas/validations.yaml', [
            'strict' => true,
            'validate_request' => true,
            'validate_response' => true,
        ]);
        $openapi->route($slim);
        $stream = self::createStream(json_encode($this->testdata));
        $request = self::createServerRequest('PUT', '/complete/test/1234');
        $request = $request->withHeader('X-RequestId', 'abcd');
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withCookieParams(['session_id' => 12345678]);
        $request = $request->withQueryParams(['limit' => 10, 'filtering' => 'yes']);
        $request = $request->withBody($stream);
        $response = $slim->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $body = $this->testdata;
        $body['route'] = "ValidatingController::put";
        $this->assertEquals(json_encode($body), $response->getBody()->__toString());
    }

    /**
     * Test middleware request validation failure
     */
    public function testMiddlewareRequestValidationFailure(): void
    {
        $slim = AppFactory::create();
        $openapi = new OpenApi(__DIR__ . '/schemas/validations.yaml', [
            'strict' => true,
            'validate_request' => true,
            'validate_response' => true,
        ]);
        $openapi->route($slim);
        $stream = self::createStream(json_encode($this->testdata));
        $request = self::createServerRequest('PUT', '/complete/test/1234');
        $request = $request->withBody($stream);
        // Validation order may change, check for any validation exception
        $this->expectException('League\OpenAPIValidation\PSR7\Exception\ValidationFailed');
        $response = $slim->handle($request);
    }

    /**
     * Test middleware response validation failure
     */
    public function testMiddlewareResponseValidationFailure(): void
    {
        $slim = AppFactory::create();
        $openapi = new OpenApi(__DIR__ . '/schemas/validations.yaml', [
            'strict' => true,
            'validate_request' => true,
            'validate_response' => true,
        ]);
        $openapi->route($slim);
        $stream = self::createStream(json_encode(['invalid' => 'body']));
        $request = self::createServerRequest('PUT', '/complete/test/1234');
        $request = $request->withHeader('X-RequestId', 'abcd');
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withCookieParams(['session_id' => 12345678]);
        $request = $request->withQueryParams(['limit' => 10, 'filtering' => 'yes']);
        $request = $request->withBody($stream);
        // Validation order may change, check for any validation exception
        $this->expectException('League\OpenAPIValidation\PSR7\Exception\ValidationFailed');
        $response = $slim->handle($request);
    }
}
