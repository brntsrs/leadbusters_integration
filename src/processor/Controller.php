<?php
namespace Leadbusters\processor;

use Leadbusters\data\Track;
use Leadbusters\layout\Layout;
use Leadbusters\layout\PreLanding;
use Leadbusters\pixel\Pixel;
use Leadbusters\processor\actions\Content;
use Leadbusters\processor\actions\Lead;
use Leadbusters\processor\actions\Restore;
use Leadbusters\processor\actions\SelfUpdate;
use Leadbusters\provider\Provider;

class Controller extends Application
{
    /**
     * @var Layout
     */
    public $layout;

    /**
     * @var Provider[]
     */
    public $providers = [];

    /**
     * @var Track
     */
    public $track;

    /**
     * Adding extra js ajax click to create track ID
     * Will add one more js click to landing to store track ID in cookies
     *
     * @param $url
     * @return $this
     */
    public function setTrackingUrl($url)
    {
        $this->layout->setTrackingUrl($url);

        return $this;
    }

    public function setUrlForward($url)
    {
        $this->layout->setUrlForward($url);

        return $this;
    }

    public function setLayout($layoutClass, $view = 'template.html')
    {
        $this->layout = new $layoutClass($this, $view);
        return $this;
    }

    public function setTrackParam($name)
    {
        $this->track->setParam($name);
        return $this;
    }

    protected function init()
    {
        $this->track = new Track($this);
    }

    public function run()
    {
        $actionClass = Content::class;
        if ($this->request->getParam('restore') == 'true') {
            $actionClass = Restore::class;
        } elseif ($this->request->getMethod() == 'POST') {
            $actionClass = Lead::class;
        }
        (new $actionClass($this))->run();
    }

    /**
     * @param Provider $provider
     * @return $this
     */
    public function addProvider(Provider $provider)
    {
        $this->providers[$provider::getClassName()] = $provider;
        $this->debug->log('Added lead provider: ' . get_class($provider));

        return $this;
    }

    public function addPixel(Pixel $pixel)
    {
        $pixel->setRequest($this->request);
        $this->layout->addPixel($pixel);

        return $this;
    }

    public function closeProviders()
    {
        foreach ($this->providers as $provider) {
            $provider->close();
        }
    }

    /**
     * @param $url
     */
    public function redirect($url)
    {
        header('Location: ' . $url);
    }
}