<?php

namespace ChatBot;

use ChatBot\Clients\FBClient;
use ChatBot\Clients\TelegramClient;
use ChatBot\Messages\Message;

/**
 * Class BotApplication
 *
 * @package ChatBot
 */
abstract class BotApplication
{
    /**
     * FB Messenger
     */
    const PLATFORM_FB = "fb";

    /**
     * Telegram
     */
    const PLATFORM_TELEGRAM = "telegram";

    /**
     * Application config
     *
     * @var array
     */
    protected $config = [];

    /**
     * Client Object
     * 
     * @var null|BaseClient
     */
    protected $client = null;

    /**
     * Method will be executed when user start chatting with bot
     *
     * @param $platform Platform alias from config file
     * @param array $data Input data
     * @return mixed
     */
    abstract public function install($platform, $data = []);

    /**
     * Method will be executed when user delete bot
     *
     * @param $platform Platform alias from config file
     * @param array $data Input data
     * @return mixed
     */
    abstract public function uninstall($platform, $data = []);

    /**
     * Method will be executed when bot received message from user
     *
     * @param $platform Platform alias from config file
     * @param array $data Input data
     * @return mixed
     */
    abstract public function receive($platform, $data = []);

    /**
     * BotApplication constructor.
     *
     * @param array $config Application config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Run Application
     */
    public function run()
    {
        $platform = str_replace("/", "", $_SERVER["REQUEST_URI"]);
        
        switch ($platform) 
        {
            case 'fb':
                $this->client = new FBClient($this->config);
            break;
            
            case 'telegram':
                $this->client = new TelegramClient($this->config);
            break;
        }

        $this->client->run($_REQUEST, [
            'install' => [$this, 'install'],
            'uninstall' => [$this, 'uninstall'],
            'receive' => [$this, 'receive']
        ]);
    }

    /**
     * Get All config vars for platform
     *
     * @param $platform Platform alias from config file
     * @return array
     */
    public function getConfigPlatform($platform)
    {
        if (!empty($this->config[$platform])) {
            return $this->config[$platform];
        }

        return [];
    }

    /**
     * Get config var for platform
     *
     * @param $platform Platform alias from config file
     * @param $key Param alias
     * @return mixed|bool
     */
    public function getConfigValue($platform, $key)
    {
        if (!empty($this->config[$platform][$key])) {
            return $this->config[$platform][$key];
        }

        return false;
    }

    /**
     * Send message to user
     *
     * @param Message $message Message object
     * @return mixed
     */
    protected function send(Message $message)
    {
        return $this->client->send($message);
    }

    /**
     * Get User Profile
     *
     * @param $user
     * @return mixed
     */
    protected function userProfile($user)
    {
        return $this->client->userProfile($user);
    }
}