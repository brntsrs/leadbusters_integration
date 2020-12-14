<?php
namespace Leadbusters\provider;

use GuzzleHttp\Client;

class Leadbusters extends Provider
{
    /**
     * @var Client
     */
    private $transport;
    private $apiUrlDomain;
    private $apiUrlProtocol;

    const API_URL = '{PROTOCOL}://{DOMAIN}/tracking/external/lead';
    const API_DEFAULT_DOMAIN = 'leadbusters.network';
    const API_DEFAULT_PROTOCOL = 'https';

    public function __construct($target = null)
    {
        parent::__construct($target);

        $this->apiUrlDomain = self::API_DEFAULT_DOMAIN;
        $this->apiUrlProtocol = self::API_DEFAULT_PROTOCOL;

        if ($this->getTarget()) {
            $url = parse_url($this->getTarget());
            $this->apiUrlDomain = $url['host'];
            $this->apiUrlProtocol = $url['scheme'];
        }

        $this->createTransport();
    }

    /**
     * @return string
     */
    public function getApiDomain()
    {
        return $this->apiUrlDomain;
    }

    /**
     * @return string
     */
    public function getApiProtocol()
    {
        return $this->apiUrlProtocol;
    }

    /**
     * Send data to lead storage
     *
     * @param $data
     * @param bool $isCloseConnection
     * @return string
     */
    public function write($data, $isCloseConnection = true)
    {
        $response = $this->getTransport()->post(
            $this->createUrl(),
            ['form_params' => $data]
        );

        return (string)$response->getBody();
    }

    /**
     * Lead request from storage by provided ID
     *
     * @param Query $query
     * @return array
     */
    public function read($query = null)
    {
        //not implemented without api key
        return [];
    }

    /**
     * Request transport getter
     *
     * @return Client
     */
    private function getTransport()
    {
        if (empty($this->transport)) {
            $this->createTransport();
        }

        return $this->transport;
    }

    /**
     * Request transport create and set default params
     */
    private function createTransport()
    {
        $urlParts = parse_url($this->createUrl());

        $this->transport = new Client([
            'base_uri'    => (isset($urlParts['scheme']) ? $urlParts['scheme'] : 'http') . '://' . $urlParts['host'],
            'timeout'     => 30,
            'http_errors' => false,
            'verify' => false,
            'allow_redirects' => true,
            'headers'         => [
                'User-Agent' => 'API Integration | Client ' . $this->getTarget() . ' | ' . (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'console'),
            ],
        ]);
    }

    /**
     * Full server API url create
     *
     * @param $endpoint
     * @param $action
     * @return string
     */
    private function createUrl()
    {
        return str_replace(['{PROTOCOL}', '{DOMAIN}'], [$this->apiUrlProtocol, $this->apiUrlDomain], self::API_URL);
    }

    public function close()
    {
        //do nothing
    }
}