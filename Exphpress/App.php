<?php


namespace Exphpress;

use \Psr\Http\Message\ServerRequestInterface;
use \React\Http\{ Server, Response };
use \FastRoute;
class App
{
    private int $port;
    private Server $server;
    private Routes $routes;

    public function __construct() {
        $this->routes = new Routes();
    }


    // $handler function handler(\Psr\Http\Message\ServerRequestInterface, RingCentral\Psr7\Response)
    public function get(string $path, callable $handler) {
        error_log("adding to app routes " . $path);
        $this->routes->add(new Route('GET', $path, $handler));
    }

    public function listen(int $port) {
        $this->port = $port;
        $loop = \React\EventLoop\Factory::create();


        $socket = new \React\Socket\Server('0.0.0.0:'.$port, $loop);
        $this->server = new Server(function(ServerRequestInterface $request) {
            return $this->handler($request);
        });
        $this->server->listen($socket);



        $loop->run();
        error_log('Exphpress running on ' . $port);
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return \React\Http\Server
     */
    public function getServer(): \React\Http\Server
    {
        return $this->server;
    }


    private function handler(ServerRequestInterface $request) {
        $uri = $request->getUri();
        $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
            error_log("dispatching", 4);
            foreach ($this->routes as $route) {
                error_log("adding route: " . $route->getPath());
                $r->addRoute($route->getMethod(), $route->getPath(), $route->getCb());
            }
        });

        $routeInfo = $dispatcher->dispatch($request->getMethod(), $uri->getPath());

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

    public function helloRouteHandler() {
        error_log("hello route handler", 4);
        return new Response(
            200,
            array(
                'Content-Type' => 'text/plain'
            ),
            "Hello World!\n"
        );

    }
}