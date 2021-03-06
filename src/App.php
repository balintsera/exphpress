<?php


namespace Exphpress;

use \Psr\Http\Message\ServerRequestInterface;
use \React\Http\{ StreamingServer, Response };
use \React\Promise\Promise;
use \FastRoute;
use \FastRoute\Dispatcher;

define('SIGINT', 2);

class App
{
    private int $port;
    private StreamingServer $server;
    private Routes $routes;
    private array $middlewares;


    // deliberately missing type
    private $dispatcher;

    public function __construct() {
        $this->routes = new Routes();
        $this->middlewares = [];
    }


    // $handler function handler(\Psr\Http\Message\ServerRequestInterface, RingCentral\Psr7\Response)
    public function get(string $path, callable $handler) {
        $this->routes->add(new Route('GET', $path, $handler));
    }

    // callable(req, res, next)
    public function use(callable $middleware) {
        $this->middlewares[] = $middleware;
    }


    public function listen(int $port) {
        $this->port = $port;
        $loop = \React\EventLoop\Factory::create();

        $loop->addSignal(SIGINT, function (int $signal) {
            echo 'Caught user interrupt signal' . PHP_EOL;
            exit;
        });
        $socket = new \React\Socket\Server('0.0.0.0:'.$port, $loop);

        // add the last handler to the middleware chain
        $this->middlewares[] = function(ServerRequestInterface $request) {
            return new Promise(function ($resolve, $reject) use ($request) {
                $contentLength = 0;
                $request->getBody()->on('data', function ($data) use (&$contentLength) {
                    $contentLength += strlen($data);
                });

                $request->getBody()->on('end', function () use ($resolve, &$contentLength, $request){
                    $resolve($this->handler($request));
                });

                // an error occures e.g. on invalid chunked encoded data or an unexpected 'end' event
                $request->getBody()->on('error', function (\Exception $exception) use ($resolve, &$contentLength) {
                    $response = new Response(
                        400,
                        array(
                            'Content-Type' => 'text/plain'
                        ),
                        "An error occured while reading at length: " . $contentLength
                    );
                    $resolve($response);
                });
            });
        };

        // start
        $this->server = new StreamingServer($this->middlewares);
        $this->server->listen($socket);


        error_log('Exphpress app running on ' . $port);
        $loop->run();
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }



    private function handler(ServerRequestInterface $request) {
        $uri = $request->getUri();

        // run only once
        // can't find any other way to initialize the dispatcher first in the constructor
        // and add the routes in the last possible time (before the first request)
        if ($this->dispatcher == null) {
            $this->dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
                //error_log("dispatching, number of routes: " . count($this->routes), 4);
                foreach ($this->routes as $route) {
                    error_log("adding route: " . $route->getPath());
                    $r->addRoute($route->getMethod(), $route->getPath(), $route->getCb());
                }
            });
        }


        $routeInfo = $this->dispatcher->dispatch($request->getMethod(), $uri->getPath());

        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                return new Response(
                    404,
                    array(
                        'Content-Type' => 'text/plain'
                    ),
                );
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                return new Response(
                    405,
                    array(
                        'Content-Type' => 'text/plain'
                    ),
                );
                break;
            case FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                return $handler($request);
                break;
        }
        return new Response(
            500,
            array(
                'Content-Type' => 'text/plain'
            ),
        );
    }
}