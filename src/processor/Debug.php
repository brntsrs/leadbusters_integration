<?php
namespace Leadbusters\processor;

class Debug
{
    private $provider;

    public function __construct($provider = null)
    {
        $this->provider = $provider;
    }

    public function log($text)
    {
        if (!empty($this->provider) && !empty($text)) {
            $this->provider->log($text);
        }
    }

    public function close($text = null)
    {
        if (!empty($this->provider)) {
            $this->log($text);
            $this->provider->close();
        }
    }
}