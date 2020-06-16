<?php

namespace App\Action;

use App\Auth\JwtAuth;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use App\Domain\User\Service\UserCreator;
use App\Domain\User\Data\UserCreateData;

use App\Admin;

final class TokenCreateAction
{
    private $jwtAuth;
    private $userCreator;

    public function __construct(JwtAuth $jwtAuth,UserCreator $userCreator)
    {
        $this->jwtAuth = $jwtAuth;
        $this->userCreator = $userCreator;
      
    }

    public function __invoke(ServerRequest $request, Response $response): Response
    {
        $data = (array)$request->getParsedBody();



        $user = new UserCreateData();

        $user->email= (string)($data['email'] ?? '');
        $user->password = (string)($data['password'] ?? '');
        $code = (string)$data['code'] ?? '';
        

       //print_r(json_encode($url));

       $isValidLogin2 = $this->userCreator->checkLogin($user);

       // Invoke the Domain with inputs and retain the result
      

       if (!$isValidLogin2) {
           // Invalid authentication credentials
           return $response
               ->withHeader('Content-Type', 'application/json')
               ->withStatus(401, 'Unauthorized')
               ->withJson("Unauthenticated");
               
       }


       $isValidLogin = $this->userCreator->verifyCode($user,$code);
      

        if (!$isValidLogin) {
            // Invalid authentication credentials
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401, 'Unauthorized')
                ->withJson("Unverified");
                
        }

        $row = Admin::select('level','id')->where('email',$user->email)->get();
        
        
        //return $response->withJson($row[0]['level'])->withStatus(201);
        

      

        // Create a fresh token
        $token = $this->jwtAuth->createJwt($row[0]["level"],$row[0]["id"]);
        $lifetime = $this->jwtAuth->getLifetime();

     

        // Transform the result into a OAuh 2.0 Access Token Response
        // https://www.oauth.com/oauth2-servers/access-tokens/access-token-response/
        

        $result = [
            'access_token' => $token,
            'expires_in' => $lifetime,
        ];


        // Build the HTTP response
      
       
        return $response
        ->withHeader('Content-Type', 'application/json')
        ->withJson($result)->withStatus(201);
        

   
    }

}