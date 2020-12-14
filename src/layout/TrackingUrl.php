<?php

namespace Leadbusters\layout;

use Leadbusters\data\Url;
use Leadbusters\processor\Controller;
use Leadbusters\processor\Storage;

/**
 * Trait TrackingUrl
 * @package Leadbusters\layout
 * @property Controller $controller
 */
trait TrackingUrl
{
    protected $urlParamName = 'track_url';
    protected $trackingUrl = false;

    protected function addTrackScript()
    {
        $this->prepareTrackingUrl();

        if (!empty($this->trackingUrl)) {
            $trackId = $this->controller->track->getId(true);
            parse_str(parse_url($this->trackingUrl, PHP_URL_QUERY), $params);
            $params['is_prelanding'] = $this->controller->layout instanceof PreLanding ? 1 : 0;
            $params['domain'] = $this->controller->request->getServerUrl();
            if (!empty($trackId)) {
                $params['track_id'] = $trackId;
            }

            foreach (['s1', 's2', 's3', 's4', 's5', 'c'] as $subid) {
                $params[$subid] = $this->controller->request->getParam($subid, isset($params[$subid]) ? $params[$subid] : null);
            }

            $this->debug->log('TrackId script added');

            $this->controller->track->storeInCookies();
            $params[$this->controller->track->getParam()] = $trackId;

            $url = (new Url())->createLinkFromPart($this->trackingUrl);
            if (strpos($url, '?') !== false) {
                $url = substr($url, 0, strpos($url, '?'));
            }
            $url .= '?' . http_build_query($params);

            $jsCode = $this->controller->track->getJsCode($url);
            if (!empty($jsCode)) {
                $this->content->addJs($jsCode);
            }
        } else {
            $this->debug->log('No webmaster link, can not add tracking script');
        }
    }

    private function prepareTrackingUrl()
    {
        if ($this->controller->request->hasParam($this->urlParamName)) {
            $url = $this->controller->request->getParam($this->urlParamName);
            $this->settrackingUrl((new Url())->createLinkFromPart(urldecode($url)));
        }
    }

    /**
     * Reading URL param to set extra click
     *
     * @param $param
     * @return $this
     */
    public function setTrackingUrlFromParam($param)
    {
        $this->urlParamName = $param;

        return $this;
    }

    /**
     * Adding extra js ajax click to create track ID
     * Will add one more js click to landing to store track ID in cookies
     *
     * @param $url
     * @return $this
     */
    public function setTrackingUrl($url)
    {
        $this->trackingUrl = $url;

        return $this;
    }

    public function getTrackingUrl()
    {
        return $this->trackingUrl;
    }
}