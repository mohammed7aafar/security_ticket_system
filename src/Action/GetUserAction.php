<?php

namespace App\Action;

use App\Domain\User\Data\UserCreateData;
use App\Domain\User\Service\UserCreator;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class GetUserAction
{
    private $userCreator;

    public function __construct(UserCreator $userCreator)
    {
        $this->userCreator = $userCreator;
    }

    public function __invoke(ServerRequest $request, Response $response): Response
    {
       

        // Invoke the Domain with inputs and retain the result
        $users = $this->userCreator->getusers();

        if (!$users) {
            // Invalid authentication credentials
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401, 'Unauthorized')
                ->withJson("not found");
                
        }


      

        // Build the HTTP response
        return $response
        ->withHeader('Content-Type', 'application/json')
        ->withJson($users)->withStatus(201);
    }
}