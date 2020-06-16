<?php

namespace App\Action;


use App\Domain\User\Service\UserCreator;
use Slim\Http\Response;
use Slim\Http\ServerRequest;


final class GetAdminsAction
{
    private $userCreator;

    public function __construct(UserCreator $userCreator)
    {
        $this->userCreator = $userCreator;
    }

    public function __invoke(ServerRequest $request, Response $response,$args): Response
    {
       

        // Invoke the Domain with inputs and retain the result
        //$users = $this->userCreator->getAdmin();

       $user =  $this->userCreator->getAdminLogs($args['id']);


       
       

        // Build the HTTP response
        return $response->withJson($user)->withStatus(201);
    }
}