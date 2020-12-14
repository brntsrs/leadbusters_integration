<?php
namespace Leadbusters\layout;

use Leadbusters\data\Url;
use Leadbusters\processor\Storage;

class PreLanding extends Landing
{
    const DAMAGED_URL = 'https://google.com';

    private $urlForward;

    public function setUrlForward($url)
    {
        $this->urlForward = $url;
        return $this;
    }

    /**
     * Building URL to replace links
     *
     * @return string
     */
    private function createFullUrl()
    {
        return $this->createUrl() . '&' . http_build_query([Storage::TRACK_ID => Storage::restoreParam(Storage::TRACK_ID)]);
    }

    private function createUrl()
    {
        $url = !empty($this->urlForward) ? $this->urlForward : $this->getTrackingUrl();
        if (empty($url)) {
            $url = self::DAMAGED_URL;
        }
        $url .= (strpos($url, '?') ? '&' : '?') . http_build_query($this->controller->request->getParams());

        return $url;
    }

    /**
     * Integration run
     *
     * @return mixed
     */
    public function duringRender()
    {
        if ($this->controller->track->getId(true) === null) {
            $this->debug->log('Need to add tracking script');
            $this->addTrackScript();

            $url = $this->createUrl();
            $this->content->addJs(<<<JAVASCRIPT
function afterTrackFound(trackId) {
    const url = '{$url}';
    var aElements = document.getElementsByTagName("a");
    for (var i = 0; i < aElements.length; i++) {
        aElements[i].href = url + '&track_id=' + trackId;
    }
}
JAVASCRIPT
);
        } else {
            $this->controller->track->storeInCookies();
            $this->debug->log('Replacing links to ' . $this->createFullUrl());

            $pattern = '/<a(.+)href="([^"]+)?"/im';
            $replace = '<a$1href="' . $this->createFullUrl() . '"';

            $this->content->set(preg_replace($pattern, $replace, $this->content->get()));

            $this->debug->log('TrackId exists, no need in tracking script');
        }

        return true;
    }
}