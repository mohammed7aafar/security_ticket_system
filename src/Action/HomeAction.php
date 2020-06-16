<?php

namespace App\Action;

use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class HomeAction
{
    public function __invoke(ServerRequest $request, Response $response): Response
    {
        $result = ['error' => ['message' => 'Validation failed']];


        return $response->withJson($result)->withStatus(422);
        


       // return $response->withJson(['success' => true]);
    }
}