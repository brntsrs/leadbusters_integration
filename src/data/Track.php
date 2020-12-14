<?php
namespace Leadbusters\data;

use Leadbusters\processor\Controller;
use Leadbusters\processor\Debug;
use Leadbusters\processor\request\Cookie;
use Leadbusters\processor\Storage;

class Track
{
    private $paramName = 'track_id';

    /**
     * @var Debug
     */
    private $debug;

    /**
     * @var Controller
     */
    private $controller;

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
        $this->debug = $this->controller->debug;
    }


    /**
     * Setting track param name to value search
     *
     * @param $name
     * @return $this
     */
    public function setParam($name)
    {
        $this->paramName = $name;
        $this->debug->log('Set track param name = ' . $name);

        return $this;
    }

    public function getParam()
    {
        return $this->paramName;
    }

    /**
     * Getting track ID from request
     * @param $doNotUseStorage
     *
     * @return null
     */
    public function getId($doNotUseStorage = false)
    {
        if ($this->controller->request->hasParam($this->paramName)) {
            return $this->controller->request->getParam($this->paramName);
        } elseif (empty($doNotUseStorage) && Storage::restoreParam(Storage::TRACK_ID)) {
            return Storage::restoreParam(Storage::TRACK_ID);
        }
        return null;
    }

    /**
     * Save track ID in cookies to restore in thank-you page
     */
    public function storeInCookies()
    {
        Storage::saveParam(Storage::TRACK_ID, $this->getId());
        $this->debug->log('Saved cookies: ' . Storage::TRACK_ID . ' = ' . $this->getId());

        return $this;
    }

    public function getJsCode($trackingUrl)
    {
        $cookieTrakIdParam = Cookie::getPrefix() . Storage::TRACK_ID;

        return /** @lang JavaScript */ <<<JAVASCRIPT
(function() {
    fetch('{$trackingUrl}', {
        headers: {
            'Content-Type': 'application/json'
        },
        redirect: 'follow',
        referrerPolicy: 'no-referrer'
    })
  .then(response => response.json())
  .then(data => {
      if (data.track_id.length > 0) {
            days = 30;
            var date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
            
            document.cookie = "{$cookieTrakIdParam}=" + data.track_id + "; domain=." + window.location.hostname + "; path=/" + expires;
            
            if (typeof afterTrackFound === 'function') {
                afterTrackFound(data.track_id);
            }
        }
  });
})();
JAVASCRIPT;
    }
}