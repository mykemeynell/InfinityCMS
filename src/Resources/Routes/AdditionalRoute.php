<?php

namespace Infinity\Resources\Routes;

use JetBrains\PhpStorm\Pure;

class AdditionalRoute
{
    private string $uri;
    private string $method = 'GET';
    private string $name;
    private string $action;

    public function __construct(string $uri)
    {
        $this->uri = $uri;
    }

    #[Pure] public static function make(string $uri): AdditionalRoute
    {
        return new self($uri);
    }

    /**
     * Get the URI.
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Set the method or HTTP verb of the route.
     *
     * @param string $method
     *
     * @return $this
     * @throws \Exception
     */
    public function setMethod(string $method): AdditionalRoute
    {
        if(!in_array($method, ['get', 'post', 'put', 'patch', 'delete', 'options'])) {
            throw new \Exception(sprintf("The method [%s] is not allowed when registering an additional route", $method));
        }

        $this->method = $method;
        return $this;
    }

    /**
     * Get the additional route method.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return strtolower($this->method);
    }

    /**
     * Set the route name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): AdditionalRoute
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the route name.
     *
     * @return string
     * @throws \Exception
     */
    public function getName(): string
    {
        if(!empty($this->name)) {
            return $this->name;
        }

        if(!empty($this->action)) {
            return $this->action;
        }

        throw new \Exception(sprintf("A name or action must be set for additional route [%s] when calling [%s]", $this->getUri(), __METHOD__));
    }

    /**
     * Set the route action.
     *
     * @param string $action
     *
     * @return $this
     */
    public function setAction(string $action): AdditionalRoute
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Get the action.
     *
     * @return string
     * @throws \Exception
     */
    public function getAction(): string
    {
        if(empty($this->action)) {
            throw new \Exception(sprintf("An action must be set for additional route [%s]", $this->getUri()));
        }

        return $this->action;
    }
}
