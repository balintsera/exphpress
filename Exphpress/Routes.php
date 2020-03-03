<?php


namespace Exphpress;


class Routes implements \Iterator, \Countable
{
    private array $routes;
    private int $position = 0;

    public function __construct() {
        $this->position = 0;
    }

    public function add(Route $r) :Routes {
        $this->routes[] = $r;
        $this->position++;
        return $this;
    }


    public function current()
    {
        return $this->routes[$this->position];
    }

    public function next()
    {
        ++$this->position;
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return isset($this->routes[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function count()
    {
        return count($this->routes);
    }
}