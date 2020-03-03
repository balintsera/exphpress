<?php


namespace Exphpress;

use \Psr\Http\Message\ServerRequestInterface;
use \React\Http\{ Server, Response };

class App
{
    private $port;
    private $server;

    public function __construct() {
    }


    // $handler function handler(\Psr\Http\Message\ServerRequestInterface, RingCentral\Psr7\Response)
    public function get(string $path, callable $handler) {

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
        var_dump($uri);
        // add routing here (on every request? seems totally resource wasting
        return new Response(
            200,
            array(
                'Content-Type' => 'text/plain'
            ),
            "Hello World!\n"
        );
    }
}