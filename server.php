<?php
require('vendor/autoload.php');
use \Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;

$app = new \Exphpress\App();
$app->get('/hello', function (ServerRequestInterface $request) {
    error_log("handler", 4);
    return new Response(
        200,
        array(
            'Content-Type' => 'text/plain'
        ),
        'hello world'
    );
});
$app->listen(8080);