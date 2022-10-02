<?php

/**
 * File for Slim OpenApi tests
 * @package Phrity > Slim > OpenApi
 */

declare(strict_types=1);

namespace Test;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

/**
 * Slim OpenApi test controller
 */
class ContainerController
{
    private ContainerInterface $container;

    /**
     * Constructor for test controller.
     * @param ContainerInterface|null $container Container passed by Slim
     */
    public function __construct(?ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Request method called by Slim.
     * @param Request $request
     * @param Response $response
     * @param array<string,mixed> $arguments
     * @return Response
     */
    public function byContainer(Request $request, Response $response, array $arguments): Response
    {
        $response->getBody()->write("ContainerController::byContainer");
        return $response;
    }
}
