<?php

namespace App\Action;

use App\Auth\JwtAuthUser;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use App\Domain\User\Service\UserCreator;
use App\Domain\User\Data\UserCreateData;
use App\Domain\User\Data\UserData;
use App\User;

final class UserTokenCreateAction
{
    private $jwtAuth;
    private $userCreator;

    public function __construct(JwtAuthUser $jwtAuth,UserCreator $userCreator)
    {
        $this->jwtAuth = $jwtAuth;
        $this->userCreator = $userCreator;
      
    }

    public function __invoke(ServerRequest $request, Response $response): Response
    {
        $data = (array)$request->getParsedBody();



        $user = new UserData();

        $user->username= (string)($data['username'] ?? '');
        $user->password = (string)($data['password'] ?? '');
        
        

       //print_r(json_encode($url));



       $isValidLogin = $this->userCreator->checkUserLogin($user);
      

        if (!$isValidLogin) {
            // Invalid authentication credentials
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401, 'Unauthorized')
                ->withJson("Unauthenticated");
                
        }

        $row = User::select('user_status','id')->where('username',$user->username)->get();
        
        
        //return $response->withJson($row[0]["status"])->withStatus(201);
        

      

        // Create a fresh token
        $token = $this->jwtAuth->createJwt($row[0]["user_status"],$row[0]["id"]);
        $lifetime = $this->jwtAuth->getLifetime();

     

        // Transform the result into a OAuh 2.0 Access Token Response
        // https://www.oauth.com/oauth2-servers/access-tokens/access-token-response/
        

        $result = [
            'access_token' => $token,
            'expires_in' => $lifetime,
        ];


        // Build the HTTP response
      
       
        return $response->withJson($result)->withStatus(201);
        

   
    }

}