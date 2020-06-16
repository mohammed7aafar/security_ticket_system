<?php

namespace App\Action;


use App\Domain\User\Service\UserCreator;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use App\Domain\User\Data\UserData;
final class UpdateUserNameAction
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
        $user = new UserData();
        $user->username = $data['username'];
        $user->password = $data['password'];
        $user->profile_name = $data['profile_name'];
        $user->profile_photo = $data['profile_photo'];
        $user->birthdate = $data['birthdate'];
        $user->status = $data['user_status'];
        
        

        // Invoke the Domain with inputs and retain the result
        $userId = $this->userCreator->updateUserName($user,$args['id']);


        $result = [
            'user_id' => $userId,
            'status' => "username is used",
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
            'status' => "user updated",
        ];

        // Build the HTTP response
        return $response->withJson($result)->withStatus(201);
    }
}