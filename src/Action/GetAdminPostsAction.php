<?php

namespace App\Action;


use App\Domain\User\Service\UserCreator;
use Slim\Http\Response;
use Slim\Http\ServerRequest;


final class GetAdminPostsAction
{
    private $userCreator;

    public function __construct(UserCreator $userCreator)
    {
        $this->userCreator = $userCreator;
    }

    public function __invoke(ServerRequest $request, Response $response,$args): Response
    {
       

       
       $user =  $this->userCreator->getAdminPosts($args['id']);
       

        // Build the HTTP response
        return $response->withJson($user)->withStatus(201);
    }
}