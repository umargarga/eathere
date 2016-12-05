<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app->register(new \Silex\Provider\DoctrineServiceProvider(),[
    'db.options' => ['driver' => 'pdo_mysql',
        'password' => 'root', 'port' => '3306', 'dbname' => 'db'
    ]
]);

$app->register(new \Silex\Provider\ValidatorServiceProvider());
$app->register(new \Silex\Provider\SecurityServiceProvider());
$app->register(new \SimpleUser\UserServiceProvider());

$app['security.firewalls'] = [
    'secured_area' => [
        'pattern' => '^/v1/albums',
        'anonymous' => true,
        'http' => true,
        'users' => $app->share(function($app){ return $app['user.manager']; })
    ]
];

return $app;