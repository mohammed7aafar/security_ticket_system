<?php

use Selective\Config\Configuration;
use Slim\App;
use Selective\BasePath\BasePathMiddleware;

return function (App $app) {

   // 

//     $app->add(new Tuupola\Middleware\CorsMiddleware
// (
//     [
//         "origin" => ["localhost"],
//         "methods" => ["GET", "POST", "PUT", "PATCH", "DELETE"],
//         "headers.allow" => [],
//         "headers.expose" => [],
//         "credentials" => false,
//         "cache" => 0,
//     ]
// )

// );

    // The RoutingMiddleware should be added after our CORS middleware so routing is performed first// The RoutingMiddleware should be added after our CORS middleware so routing is performed first
    // Parse json, form data and xml
    $app->addBodyParsingMiddleware();


    // $app->add(\App\Middleware\CorsMiddleware::class);
    $app->addRoutingMiddleware();

    $app->add(new BasePathMiddleware($app));
    // Add routing middleware
    

    $container = $app->getContainer();
    
    // Add error handler middleware
    $settings = $container->get(Configuration::class)->getArray('error_handler_middleware');
    $displayErrorDetails = (bool)$settings['display_error_details'];
    $logErrors = (bool)$settings['log_errors'];
    $logErrorDetails = (bool)$settings['log_error_details'];

    $app->addErrorMiddleware($displayErrorDetails, $logErrors, $logErrorDetails);
};