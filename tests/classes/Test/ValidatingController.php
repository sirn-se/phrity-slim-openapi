<?php

namespace Test;

use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

class ValidatingController
{
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

        $response->getBody()->write("ValidatingController::get={$route}");
        return $response;
    }
}
