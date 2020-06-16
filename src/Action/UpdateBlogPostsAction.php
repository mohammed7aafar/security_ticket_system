<?php

namespace App\Action;

use App\Domain\User\Data\BlogPostsData;
use App\Domain\User\Service\UserCreator;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class UpdateBlogPostsAction
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
        $post = new BlogPostsData();
        $post->title = $data['title'];
        $post->text = $data['text'];
        $post->date = $data['date'];
        $post->views = $data['views'];
        $post->admin_id = $data['admin_id'];
        $post->category_id = $data['category_id'];




        // Invoke the Domain with inputs and retain the result
        $userId = $this->userCreator->updatePost($post,$args['id']);


        $result = [
            'post_id' => $userId,
            'status' => "post not updated",
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
            'status' => "post updated",
        ];

        // Build the HTTP response
        return $response->withJson($result)->withStatus(201);
    }
}