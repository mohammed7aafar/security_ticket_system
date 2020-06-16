<?php

namespace App\Action;

use App\Domain\User\Data\UserCreateData;
use App\Domain\User\Service\UserCreator;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class UpdateUserAction
{
    private $userCreator;

    public function __construct(UserCreator $userCreator)
    {
        $this->userCreator = $userCreator;
    }

    public function __invoke(ServerRequest $request, Response $response,$args): Response
    {
        // Collect input from the HTTP request
        $data = (array)$request->getParsedBody();

        // Mapping (should be done in a mapper class)
        $user = new UserCreateData();
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->level = $data['level'];
        
        

        // Invoke the Domain with inputs and retain the result
        $userId = $this->userCreator->updateUser($user,$args['id']);


        $result = [
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
            'status' => "user updated",
        ];

        // Build the HTTP response
        return $response->withJson($result)->withStatus(201);
    }
}