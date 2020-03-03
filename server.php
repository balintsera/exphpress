<?php
require('vendor/autoload.php');
use \Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;

$app = new \Exphpress\App();
$app->get('/hello', function (ServerRequestInterface $request) {
    error_log("handler", 4);
     new Response(
        200,
        array(
            'Content-Type' => 'text/plain'
        ),
        'hello world'
    );
});
$app->use(function (ServerRequestInterface $request, callable $next) {
    error_log("first middleware");
    $next($request);
});

$app->listen(8080);