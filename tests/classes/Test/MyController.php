<?php

/**
 * File for Slim OpenApi tests
 * @package Phrity > Slim > OpenApi
 */

declare(strict_types=1);

namespace Test;

use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

/**
 * Slim OpenApi test controller
 */
class MyController
{
    /**
     * Request method called by Slim.
     * @param Request $request
     * @param Response $response
     * @param array<string,mixed> $arguments
     * @return Response
     */
    public function put(Request $request, Response $response, array $arguments): Response
    {
        $response->getBody()->write("MyController::put");
        return $response;
    }

    /**
     * Request method called by Slim.
     * @param Request $request
     * @param Response $response
     * @param array<string,mixed> $arguments
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $arguments): Response
    {
        $response->getBody()->write("MyController::__invoke");
        return $response;
    }

    /**
     * Request method called by Slim.
     * @param Request $request
     * @param Response $response
     * @param array<string,string> $arguments
     * @return Response
     */
    public function argument(Request $request, Response $response, array $arguments): Response
    {
        $response->getBody()->write("MyController::argument={$arguments['arg_1']},{$arguments['arg_2']}");
        return $response;
    }

    /**
     * Request method called by Slim.
     * @param Request $request
     * @param Response $response
     * @param string $arg_1
     * @param string $arg_2
     * @return Response
     */
    public function arguments(Request $request, Response $response, string $arg_1, string $arg_2): Response
    {
        $response->getBody()->write("MyController::arguments={$arg_1},{$arg_2}");
        return $response;
    }

    /**
     * Request method called by Slim.
     * @param Request $request
     * @param Response $response
     * @param array<string,string> $arguments
     * @return Response
     */
    public function bind(Request $request, Response $response, array $arguments): Response
    {
        $route = $request->getAttribute('openapi-route');
        $response->getBody()->write("MyController::bind={$route}");
        return $response;
    }
}
