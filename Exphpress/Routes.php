<?php


namespace Exphpress;


class Routes implements \Iterator
{
    private array $routes;

    public function add(Route $r) :Routes {
        $this->routes[] = $r;

        return $this;
    }


    public function current()
    {
        current($this->routes);
    }

    public function next()
    {
        next($this->routes);
    }

    public function key()
    {
        key($this->routes);
    }

    public function valid()
    {
        if (key($this->routes) > count($this->routes)) {
            return false;
        }

        return true;
    }

    public function rewind()
    {
        prev($this->routes);
    }

}