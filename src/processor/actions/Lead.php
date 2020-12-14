<?php
namespace Leadbusters\processor\actions;

use Leadbusters\layout\Landing;
use Leadbusters\processor\Storage;

class Lead extends Action
{
    protected $thankyouUrl = 'thankyou.php';

    public function run()
    {
        $this->saveLead();
        if (!empty($this->thankyouUrl)) {
            $this->controller->redirect($this->thankyouUrl);
        }
    }

    private function saveLead()
    {
        $this->debug->log('Creating new lead');

        $lead = new \Leadbusters\data\Lead(Storage::restoreParam(Storage::TRACK_ID));
        $lead->setReferrer($this->controller->request->getUrl());
        $lead->setBrowserParams($this->controller->request->getUserIP(), $this->controller->request->getUserAgent());
        $this->debug->log('Lead data: ' . json_encode($lead->getFullData()));

        foreach ($this->controller->providers as $provider) {
            $this->debug->log('Adding provider to lead: ' . get_class($provider));
            $lead->addProvider($provider);
        }

        /**
         * @var Landing $landing
         */
        $landing = $this->controller->layout;

        foreach ($landing->getForm()->getFields() as $param => $field) {
            $lead->setParam($param, $this->controller->request->getParam($field));
        }
        $this->parseProviderResponse($lead->save());
    }

    /**
     * Applying response settings to user further actions
     *
     * @param $response
     */
    protected function parseProviderResponse($response)
    {
        $this->debug->log('Lead save response: ' . json_encode($response));

        if (!empty($response[Storage::PIXELS])) {
            Storage::addParams(Storage::PIXELS, $response[Storage::PIXELS]);
        }
        if (!empty($response['thankyou_url'])) {
            $this->thankyouUrl = $response['thankyou_url'];
        }
    }
}