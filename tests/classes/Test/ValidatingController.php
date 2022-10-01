<?php

namespace Test;

use Phrity\Slim\FactoryTrait;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

class ValidatingController
{
    use FactoryTrait;

    /**
     * Request method called by Slim.
     * @param Request $request
     * @param Response $response
     * @param array<string,string> $arguments
     * @return Response
     */
    public function post(Request $request, Response $response, array $arguments)
    {
        $route = $request->getAttribute('openapi-route');
        $route->validateRequest($request);
        $data = json_decode($request->getBody()->__toString());
        $data->route = "ValidatingController::post";
        $response->getBody()->write(json_encode($data));
        $response = $response->withHeader('Content-Type', 'application/json');
        $route->validateResponse($response);
        return $response;
    }

    public function put(Request $request, Response $response, array $arguments)
    {
        $route = $request->getAttribute('openapi-route');
        $data = json_decode($request->getBody()->__toString());
        $data->route = "ValidatingController::put";
        $response->getBody()->write(json_encode($data));
        $response = $response->withHeader('Content-Type', 'application/json');
        return $response;
    }
}
