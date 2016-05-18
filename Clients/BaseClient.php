<?php

namespace ChatBot\Clients;

/**
 * Class BaseClient
 * @package ChatBot\Clients
 */
abstract class BaseClient
{
    /**
     * Request type GET
     */
    const TYPE_GET = "get";

    /**
     * Request type POST
     */
    const TYPE_POST = "post";

    /**
     * Application config
     *
     * @var array
     */
    protected $config = [];

    protected $handlers = [];

    abstract public function send($message);

    /**
     * BaseClient constructor.
     *
     * @param array $config
     * @param array $handlers
     */
    public function __construct($config, $handlers)
    {
        $this->config = $config;
        $this->handlers = $handlers;
    }

    /**
     * Get config var
     *
     * @param $key Param alias
     * @return mixed|bool
     */
    public function getConfigValue($key)
    {
        if (!empty($this->config[$key])) {
            return $this->config[$key];
        }

        return false;
    }
}