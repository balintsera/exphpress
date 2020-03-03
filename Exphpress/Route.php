<?php


namespace Exphpress;

class Route
{
    private string $method;
    private string $path;
    private $cb;

    public function __construct(string $method, string $path, callable $cb)
    {
        $this->path = $path;
        $this->method = $method;
        $this->cb = $cb;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return Route
     */
    public function setMethod(string $method): Route
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return Route
     */
    public function setPath(string $path): Route
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return callable
     */
    public function getCb(): callable
    {
        return $this->cb;
    }

    /**
     * @param callable $cb
     * @return Route
     */
    public function setCb(callable $cb): Route
    {
        $this->cb = $cb;
        return $this;
    }



}