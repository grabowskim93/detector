<?php

use Slim\Http\Request;
use Slim\Http\Response;


$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withAddedHeader('Access-Control-Allow-Origin', '*')
        ->withAddedHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, X-Authorization, X-Dataset-Id')
        ->withAddedHeader('Access-Control-Expose-Headers', 'Content-Disposition')
        ->withAddedHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->withAddedHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->withAddedHeader('Cache-Control', 'post-check=0, pre-check=0')
        ->withAddedHeader('Pragma', 'no-cache');
});

// Routes
$app->get('/', function (Request $request, Response $response, array $args) {
    // Render index view
    return $this->view->render($response, 'index.phtml', $args);
});

$app->get('/scan', 'App\Sensors\Controllers\SensorController:scan');
$app->get('/add', 'App\Sensors\Controllers\SensorController:addSensor');
$app->get('/list', 'App\Sensors\Controllers\SensorController:sensorsList');
$app->get('/data/{mac}', 'App\Sensors\Controllers\SensorController:sensorData');
$app->post('/edit/{id}', 'App\Sensors\Controllers\SensorController:updateSensor');
$app->get('/details/{id}', 'App\Sensors\Controllers\SensorController:getDetails');


$app->get('/add2', function (Request $request, Response $response, array $args) {
    // Render index view
    return $this->view->render($response, 'details.html.twig', $args);
});