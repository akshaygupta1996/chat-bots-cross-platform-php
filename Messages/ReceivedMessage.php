<?php

namespace ChatBot\Messages;


class ReceivedMessage
{
    /**
     * @var integer|null
     */
    protected $sender = null;

    /**
     * @var string
     */
    protected $text = null;

    /**
     * Message constructor.
     *
     * @param $sender
     * @param $text
     */
    public function __construct($sender, $text)
    {
        $this->sender = $sender;
        $this->text = $text;
    }

    public function getSender()
    {
        return $this->sender;
    }

    public function getText()
    {
        return $this->text;
    }
}