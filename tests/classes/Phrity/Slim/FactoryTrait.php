<?php

/**
 * File for Slim OpenApi tests
 * @package Phrity > Slim > OpenApi
 */

declare(strict_types=1);

namespace Phrity\Slim;

use Psr\Http\Message\{
    ServerRequestInterface,
    StreamInterface
};
use Slim\Psr7\Factory\{
    ServerRequestFactory,
    StreamFactory,
};

/**
 * Slim OpenApi test helper
 */
trait FactoryTrait
{
    /**
     * Create a stream.
     * @param string $data                  Data to write on stream
     * @return StreamInterface              A stream
     */
    private static function createStream(string $data): StreamInterface
    {
        return (new StreamFactory())->createStream($data);
    }

    /**
     * Create a server request.
     * @param string $method                HTTP method
     * @param string $path                  Request path
     * @return StreamInterface              A server request
     */
    private static function createServerRequest(string $method, string $path): ServerRequestInterface
    {
        return (new ServerRequestFactory())->createServerRequest($method, $path);
    }
}
