<?php

use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use App\Core\ViewExtension;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Capsule\Manager;


define('CONFIG_PATH', __DIR__.'/settings.php');
$config = require CONFIG_PATH;
$app = new App($config);

// configure Eloquent
$capsule = new Manager();
$capsule->addConnection($config['settings']['db']);
$capsule->setEventDispatcher(new Dispatcher(new Container));
$capsule->setAsGlobal();
$capsule->bootEloquent();



// get container
$container = $app->getContainer();

// Twig template system configuration
$container['view'] = function ($container) use ($config) {
    $view = new Twig(__DIR__.'/views', [
        'cache' => $config['settings']['view']['cache']
    ]);

    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new TwigExtension($container['router'], $basePath));
    $view->addExtension(new ViewExtension(__DIR__.'/../public', $basePath));

    return $view;
};

require 'routes.php';

$app->run();
