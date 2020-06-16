
<?php

use DI\ContainerBuilder;
use Slim\App;
use Selective\Config\Configuration;

require_once __DIR__ . '/../vendor/autoload.php';

// only included when you use them
require_once __DIR__ . '/../vendor/ezyang/htmlpurifier/library/HTMLPurifier.auto.php';


$containerBuilder = new ContainerBuilder();

// Set up settings
$containerBuilder->addDefinitions(__DIR__ . '/container.php');





// $capsule = new Illuminate\Database\Capsule\Manager;
// $capsule->addConnection($container['settings']['db']);
// $capsule->setAsGlobal();
// $capsule->bootEloquent();



// Build PHP-DI Container instance
$container = $containerBuilder->build();



// Create App instance
$app = $container->get(App::class);


$container = $app->getContainer();
    
    
$settings = $container->get(Configuration::class)->getArray('db');

$capsule = new Illuminate\Database\Capsule\Manager;
$capsule->addConnection($settings);
$capsule->bootEloquent();
$capsule->setAsGlobal();

// Register routes
(require __DIR__ . '/routes.php')($app);




// Register middleware
(require __DIR__ . '/middleware.php')($app);




/// setting up cors to prevent blocking from web browser

if (isset($_SERVER['HTTP_ORIGIN'])) {
    // should do a check here to match $_SERVER['HTTP_ORIGIN'] to a
    // whitelist of safe domains
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400000'); 
    header("Content-Type: application/json");
    
    // cache for 1 day
}
// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");         
        header("Content-Type: application/json");
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        header("Content-Type: application/json");
}




return $app;