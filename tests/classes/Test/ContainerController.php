<?php

namespace Test;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

class ContainerController
{
    private $container;

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function byContainer(Request $request, Response $response, array $arguments)
    {
        $response->getBody()->write("ContainerController::byContainer");
        return $response;
    }
}
