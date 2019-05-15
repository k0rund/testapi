<?php

use Slim\App;

$container = require __DIR__ . '/../container.php';
$container->get(App::class)->run();
