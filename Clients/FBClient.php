<?php

namespace ChatBot\Clients;


use ChatBot\BotApplication;
use ChatBot\Profile\User;

class FBClient extends BaseClient
{
    /**
     * FB Messenger API Url
     *
     * @var string
     */
    protected $apiUrl = 'https://graph.facebook.com/v2.6/';

    /**
     * Send message to user
     *
     * @param $message Message Object
     * @return array
     */
    public function send($message)
    {
        return $this->call('me/messages', $message->getData());
    }

    /**
     * Get User Profile
     *
     * @param $id
     * @return User
     */
    public function userProfile($id)
    {
        return new User($this->call($id, [
            'fields' => 'first_name,last_name,profile_pic'
        ], self::TYPE_GET));
    }

    /**
     * Run Application
     * 
     * @param $data
     */
    public function run($data)
    {
        // Verification
        if (!empty($data['hub_mode']) && $data['hub_mode'] == 'subscribe') {
            if ($data['hub_verify_token'] == $this->getConfigValue("verify_token")) {
                echo $_REQUEST['hub_challenge'];
            }
        } else {

            // Message received

            $data = json_decode(file_get_contents("php://input"), true, 512, JSON_BIGINT_AS_STRING);
            if (!empty($data['entry'][0]['messaging']))
            {
                foreach ($data['entry'][0]['messaging'] as $message)
                {
                    if (is_callable($this->handlers['receive'])) {
                        call_user_func($this->handlers['receive'], $message);
                    }
                }
            }
        }
    }

    /**
     * Request to API
     *
     * @param $url Url
     * @param $data Data
     * @param string $type Type of request (GET|POST)
     * @return array
     */
    protected function call($url, $data, $type = self::TYPE_POST)
    {
        $data['access_token'] = $this->getConfigValue('token');
        $headers = [
            'Content-Type: application/json',
        ];
        if ($type == BotApplication::TYPE_GET) {
            $url .= '?'.http_build_query($data);
        }
        $process = curl_init($this->apiUrl.$url);
        curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($process, CURLOPT_HEADER, false);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);

        if($type == self::TYPE_POST) {
            curl_setopt($process, CURLOPT_POST, 1);
            curl_setopt($process, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
        $return = curl_exec($process);
        curl_close($process);
        return json_decode($return, true);
    }
}