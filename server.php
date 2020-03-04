<?php
require('vendor/autoload.php');
use \Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;

$app = new \Exphpress\App();
$port = 8080;
$app->get('/hello', function (ServerRequestInterface $request) {
    error_log("handler", 4);
    return new Response(
        200,
        array(
            'Content-Type' => 'text/plain'
        ),
        'Hello World!'
    );
});

$app->use(function (ServerRequestInterface $request, callable $next) {
    error_log("example middleware");
    return $next($request);
});

$app->listen($port);