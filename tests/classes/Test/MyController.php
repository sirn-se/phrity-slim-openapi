<?php

namespace Test;

use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

class MyController
{
    private $environment;

    public function __construct($environment = null)
    {
        $this->environment = $environment;
    }

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
}
