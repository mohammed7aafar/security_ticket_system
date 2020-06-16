<?php

namespace App\Action;

use App\Domain\User\Data\AdminLogsData;
use App\Domain\User\Data\UserCreateData;
use App\Domain\User\Service\UserCreator;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class postAdminLogsAction
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
        $user = new AdminLogsData();
        $user->url = $data['url'];
        $user->admin_id = $data['admin_id'];
       
        
        

        // Invoke the Domain with inputs and retain the result
        $userId = $this->userCreator->createAdminLogs($user);



        // Transform the result into the JSON representation
        $result = [
            'user_id' => $userId,
            'status' => "log created",
        ];

        // Build the HTTP response
        return $response->withJson($result)->withStatus(201);
    }
}