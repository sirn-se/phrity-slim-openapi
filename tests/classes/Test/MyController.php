<?php

namespace Test;

use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

class MyController
{
    public function put(Request $request, Response $response, array $arguments)
    {
        $response->getBody()->write("MyController::put");
        return $response;
    }

    public function __invoke(Request $request, Response $response, array $arguments)
    {
        $response->getBody()->write("MyController::__invoke");
        return $response;
    }

    public function argument(Request $request, Response $response, array $args)
    {
        $response->getBody()->write("MyController::argument={$args['arg_1']},{$args['arg_2']}");
        return $response;
    }

    public function arguments(Request $request, Response $response, string $arg_1, string $arg_2)
    {
        $response->getBody()->write("MyController::arguments={$arg_1},{$arg_2}");
        return $response;
    }
}
