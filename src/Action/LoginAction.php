<?php

namespace App\Action;

use App\Domain\User\Data\UserCreateData;
use App\Domain\User\Service\UserCreator;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use \Sonata\GoogleAuthenticator\GoogleAuthenticator;


final class LoginAction
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
        
        $user->email= (string)($data['email'] ?? '');
        $user->password = (string)($data['password'] ?? '');
        
    
        $g = new GoogleAuthenticator();

        $secret = $this->userCreator->returnSalt($user);


        $isValidLogin = $this->userCreator->checkLogin($user);

        // Invoke the Domain with inputs and retain the result
       

        if (!$isValidLogin) {
            // Invalid authentication credentials
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401, 'Unauthorized')
                ->withJson("Unauthenticated");
                
        }

        $url = $g->getURL($user->email, 'ammen', $secret);


        // Transform the result into the JSON representation
        $result = [
    
            'status' => "login successful",
            'url' => $url
        ];

        // Build the HTTP response
        return $response
        ->withHeader('Content-Type', 'application/json')
        ->withJson($result)->withStatus(201);
    }
}