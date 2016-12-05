<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
/** @var Silex\Application */
$app;
/** @var \Silex\ControllerCollection $api */

$api = $app['controllers_factory'];

$api->before(
    function (Request $request) {

        if (0 === strpos($request->headers->get('Content-Type'),
                'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
        }
    }
);

$api->get('/albums', function () use ($app) {
    $sql = 'SELECT * FROM albums';
    $albums = $app['db']->fetchAll($sql);
    return $app->json($albums);
});

$api->post('/albums', function(Request $request) use ($app) {
    $data = $request->request->all();

    $albumValidator = new Assert\Collection([
        'artist' => [new Assert\NotBlank(), new Assert\Length(['max' => 255])],
        'title' => [new Assert\NotBlank(), new Assert\Length(['max' => 255])]
    ]);
    $errors = $app['validator']->validateValue($data, $albumValidator);

    if(count($errors) > 0) {
        $errorList = [];
        /** @var Symfony\Component\Validator\ConstraintViolation $error */
        foreach($errors as $error) {
            $errorList[$error->getPropertyPath()] = $error->getMessage();
        }
        return $app->json($errorList, 400);
    }
    else {
        $app['db']->insert('albums',
            ['artist' => $data['artist'], 'title' => $data['title']]);
        $id = $app['db']->lastInsertId();
        return new Response(null, 201, ['Location' => '/api/albums/'.$id]);
    }
});

$api->post('/users',function(Request $request) use ($app) {
//    $user = $app['user'];
//    if($user === null) {
//        return new Response(null, 401);
//    }
    /** @var \SimpleUser\UserManager £userManager */
    $userManager = $app['user.manager'];
    $user = $userManager->createUser($request->request->get('email'),
        $request->request->get('password'));
    $userManager->insert($user);
    return new Response(null, 201);
});
$api->get('/users', function () use ($app) {
    $sql = 'SELECT * FROM users';
    $albums = $app['db']->fetchAll($sql);
    return $app->json($albums);
});
//$api->get('/users/{value}', function (Request $value) use ($app) {
//    $sql = 'SELECT * FROM users';
//    $albums = $app['db']->fetchAll($sql);
//    return $app->json($albums);
//});

return $api;