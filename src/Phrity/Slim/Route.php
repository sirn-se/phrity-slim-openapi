<?php

/**
 * Route for Slim
 */

namespace Phrity\Slim;

class Route
{
    private $path;
    private $method;
    private $operation;

    public function __construct(string $path, string $method, string $operation)
    {
        $this->path = $path;
        $this->method = $method;
        $this->operation = $operation;
    }

    public function __get(string $key): string
    {
        return property_exists($this, $key) ? $this->$key : null;
    }

    public function __toString(): string
    {
        return "{$this->path}.{$this->method} > {$this->operation}";
    }
}
