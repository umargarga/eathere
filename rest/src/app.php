<?php

/**
 * @var Silex\Application $app
 */
$app = require_once __DIR__.'/bootstrap.php';


/*

$app->get('/hello/world', function() {

    return 'Hello World!';
});

$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello ' .$app->escape($name);
});

$app->get('/hello/{name}', function ($name) use ($app) {
    $date = date('d/M/Y');
    return 'Today is ' .$app->escape($date);
});

*/


$app->get('/albums', function ($albums) use ($app) {
    $sql = 'SELECT * FROM albums';
    $albums = $app['db']->fetchAll($sql);
    return $app->json($albums);
});

/*
$app->get('/hello/{name}', function($name) use ($app) {

    return 'Hello World!';
});

jdbc:sqlite:/home/sjp986/workshop1/data.db
*/
$app->mount('/v1', include __DIR__.'/albums.php');

return $app;