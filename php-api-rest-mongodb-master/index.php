<?php

require_once('./vendor/autoload.php');

$config = require_once('config.php');

$dbClient = new \MongoDB\Client("mongodb://localhost", $config['db'] );

// Homemade dependency injector...
$playlistRepository = new App\Repositories\PlaylistRepository();
$playlistRepository->setDbClient($dbClient);
$playlistRepository->setDbName($config['db']['name']);
$playlistService = new App\Services\PlaylistService();
$playlistService->setRepository($playlistRepository);

$dependencies = [
    'App\Controllers\PlaylistController' => [
        $playlistService
    ]
];

// Homemade router which handles basic REST routes...
$router = new App\RestRouter($_SERVER, $_POST);
$router->setControllerNamespace('App\\Controllers');
$router->setDependencies($dependencies);

try{
    $router->handleRequest();
}catch(Exception $e){
    if($e instanceof App\Exceptions\CustomException){
        $msg = $e->getMessage();
        $code = $e->getCode();
    }else{
        $msg = 'An internal error has occurred';
        $code = 500;
    }

    http_response_code($code);
    header('Content-type: application/json');

    echo json_encode(['msg' => $msg]); exit;
}
