<?php

namespace App\Action;

use App\Domain\User\Data\UserCreateData;
use App\Domain\User\Service\UserCreator;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class UserCreateAction
{
    private $userCreator;

    public function __construct(UserCreator $userCreator)
    {
        $this->userCreator = $userCreator;
    }

    public function __invoke(ServerRequest $request, Response $response): Response
    {
        // Collect input from the HTTP request
        $data = (array)$request->getParsedBody();

        // Mapping (should be done in a mapper class)
        $user = new UserCreateData();
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->level = $data['level'];
        
        

        // Invoke the Domain with inputs and retain the result
        $userId = $this->userCreator->createUser($user);


        $result = [
            'user_id' => $userId,
            'status' => "email is used",
        ];

        if (!$userId) {
            // Invalid authentication credentials
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401, 'Unauthorized')
                ->withJson($result);
                
        }
        // Transform the result into the JSON representation
        $result = [
            'user_id' => $userId,
            'status' => "user created",
        ];

        // Build the HTTP response
        return $response
        ->withHeader('Content-Type', 'application/json')
        ->withJson($result)->withStatus(201);
    }
}