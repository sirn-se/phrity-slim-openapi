<?php

namespace Phrity\Slim;

use Slim\Psr7\Factory\{
    ServerRequestFactory,
    StreamFactory,
};

trait FactoryTrait
{
    private static function createStream(string $data)
    {
        return (new StreamFactory())->createStream($data);
    }

    private static function createServerRequest(string $method, string $path)
    {
        return (new ServerRequestFactory())->createServerRequest($method, $path);
    }
}
