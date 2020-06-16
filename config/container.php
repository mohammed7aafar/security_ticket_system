<?php

use Psr\Container\ContainerInterface;
use Selective\Config\Configuration;
use Slim\App;
use Slim\Factory\AppFactory;
use App\Auth\JwtAuth;
use App\Auth\JwtAuthUser;
use Psr\Http\Message\ResponseFactoryInterface;
use Illuminate\Container\Container as IlluminateContainer;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Connection;
use Illuminate\Database\Connectors\ConnectionFactory;

return [
    Configuration::class => function () {
        return new Configuration(require __DIR__ . '/settings.php');
    },

    App::class => function (ContainerInterface $container) {
        AppFactory::setContainer($container);

        
        $app = AppFactory::create();

        // Optional: Set the base path to run the app in a sub-directory
        // The public directory must not be part of the base path
       // $app->setBasePath('/ammen');

        return $app;
    },



    // PDO::class => function (ContainerInterface $container) {
    //     $config = $container->get(Configuration::class);
    
    //     $host = $config->getString('db.host');
    //     $dbname =  $config->getString('db.database');
    //     $username = $config->getString('db.username');
    //     $password = $config->getString('db.password');
    //     $charset = $config->getString('db.charset');
    //     $flags = $config->getArray('db.flags');
        
    //     $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    
    //     return new PDO($dsn, $username, $password, $flags);
    // },




     // Database connection
     Connection::class => function (ContainerInterface $container) {
        $factory = new ConnectionFactory(new IlluminateContainer());



        $connection = $factory->make($container->get(Configuration::class)->getArray('db'));

        // Disable the query log to prevent memory issues
        $connection->disableQueryLog();
        

        return $connection;
    },

    PDO::class => function (ContainerInterface $container) {
        return $container->get(Connection::class)->getPdo();
    },


       // Add this entry
       ResponseFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getResponseFactory();
    },

    JwtAuth::class => function (ContainerInterface $container) {
        $config = $container->get(Configuration::class);

        $issuer = $config->getString('jwt2.issuer');
        $lifetime = $config->getInt('jwt2.lifetime');
        $privateKey = $config->getString('jwt2.private_key');
        $publicKey = $config->getString('jwt2.public_key');

        return new JwtAuth($issuer, $lifetime, $privateKey, $publicKey);
    },

    JwtAuthUser::class => function (ContainerInterface $container) {
        $config = $container->get(Configuration::class);

        $issuer = $config->getString('jwt.issuer');
        $lifetime = $config->getInt('jwt.lifetime');
        $privateKey = $config->getString('jwt.private_key');
        $publicKey = $config->getString('jwt.public_key');

        return new JwtAuthUser($issuer, $lifetime, $privateKey, $publicKey);
    },



];