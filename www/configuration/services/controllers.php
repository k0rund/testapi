<?php
/** @var \Interop\Container\ContainerInterface $container */
$container = $app->getContainer();

$container['DefaultController'] = function($container) {
    return new \App\Controller\DefaultController();
};
