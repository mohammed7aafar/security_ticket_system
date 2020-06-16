<?php

namespace App\Middleware;

use App\Auth\JwtAuthUser;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * JWT middleware.
 */
final class JwtMiddlewareUser implements MiddlewareInterface
{
    /**
     * @var JwtAuthUser
     */
    private $jwtAuth;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    public function __construct(JwtAuthUser $jwtAuth, ResponseFactoryInterface $responseFactory)
    {
        $this->jwtAuth = $jwtAuth;
        $this->responseFactory = $responseFactory;
    }

    /**
     * Invoke middleware.
     *
     * @param ServerRequestInterface $request The request
     * @param RequestHandlerInterface $handler The handler
     *
     * @return ResponseInterface The response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authorization = explode(' ', (string)$request->getHeaderLine('Authorization'));
        $token = $authorization[1] ?? '';

        if (!$token || !$this->jwtAuth->validateToken($token)) {
            return $this->responseFactory->createResponse()
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401, 'Unauthorized');
        }

        // Append valid token
        $parsedToken = $this->jwtAuth->createParsedToken($token); 
        $request = $request->withAttribute('token', $parsedToken);
       

        // Append the user id as request attribute
        $request = $request->withAttribute('uid', $parsedToken->getClaim('uid'));

        


        return $handler->handle($request);
    }
}