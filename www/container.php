<?php
require 'vendor/autoload.php';

use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Container;
use App\Db\SettingsDB;

$container = new Container([
    App::class => function (ContainerInterface $c) {
        $app = new App($c);

        $app->add(function ($req, $res, $next) {
                $response = $next($req, $res);
                return $response
                        ->withHeader('Access-Control-Allow-Origin', '*')
                        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            });

        $app->post('/api/createuser', '\App\Controllers\UserController:create');
        $app->post('/api/login', '\App\Controllers\UserController:login');
        $app->post('/api/logout', '\App\Controllers\UserController:logout');
        $app->put('/api/files_put', '\App\Controllers\FilesController:fileUpload');
        $app->get('/api/files_list', '\App\Controllers\FilesController:filesList');
        $app->get('/api/get_file', '\App\Controllers\FilesController:getFile');
        $app->put('/api/file_update', '\App\Controllers\FilesController:updateFile');

        return $app;
    }
    ]);
return $container;
