<?php
namespace Leadbusters\data;

use Leadbusters\provider\Provider;

class Lead
{
    const STATUS_HOLD = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;
    const STATUS_TRASH = 3;

    private $trackId;
    private $status;
    private $params = [];

    /**
     * @var string URL referrer to store as lead source
     */
    private $referrer = '';
    private $userIp = '';
    private $userAgent = '';

    /**
     * @var Provider[]
     */
    private $providers = [];

    /**
     * Lead constructor.
     * @param $trackId
     */
    public function __construct($trackId)
    {
        $this->trackId = $trackId;
        $this->status = self::STATUS_HOLD;
    }

    /**
     * Setting params from input form
     *
     * @param $param
     * @param $value
     * @return $this
     */
    public function setParam($param, $value)
    {
        $this->params[$param] = $value;

        return $this;
    }

    /**
     * @param $param
     * @return mixed|null
     */
    public function getParam($param)
    {
        return isset($this->params[$param]) ? $this->params[$param] : null;
    }

    /**
     * @param $ip
     * @param $userAgent
     */
    public function setBrowserParams($ip, $userAgent)
    {
        $this->userIp = $ip;
        $this->userAgent = $userAgent;
    }

    /**
     * @return string
     */
    public function getUserIp()
    {
        return $this->userIp;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Add saving provider to store lead
     *
     * @param Provider $provider
     */
    public function addProvider(Provider $provider)
    {
        $this->providers[] = $provider;
    }

    /**
     * Save lead into assigned providers
     * @return array
     */
    public function save()
    {
        $response = [];
        foreach ($this->providers as $provider) {
            $result = $provider->write($this->getFullData());
            if (!empty($result)) {
                $response = $result;
            }
        }

        return $response;
    }

    /**
     * Data combine to save in Provider
     *
     * @return array
     */
    public function getFullData()
    {
        return array_merge([
            'follow' => 1,
            'trackid' => $this->trackId,
            'referrer' => $this->referrer,
            'user_ip' => $this->userIp,
            'user_agent' => $this->userAgent,
        ], $this->params);
    }

    /**
     * Load Lead from data
     *
     * @param $data
     * @return $this
     */
    public function restoreFromData($data)
    {
        $this->trackId = isset($data['track_id']) ? $data['track_id'] : null;
        $this->status = isset($data['status']) ? $data['status'] : self::STATUS_HOLD;
        $this->params = isset($data['params']) ? $data['params'] : [];
        $this->referrer = isset($data['referrer']) ? $data['referrer'] : null;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferrer()
    {
        return $this->referrer;
    }

    /**
     * @param $referrer
     */
    public function setReferrer($referrer)
    {
        $this->referrer = $referrer;
    }
}