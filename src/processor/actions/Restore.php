<?php
namespace Leadbusters\processor\actions;

use Leadbusters\provider\File;

class Restore extends Action
{
    public function run()
    {
        $this->restoreLeads(
            $this->controller->request->getParam('from'),
            $this->controller->request->getParam('to')
        );
    }

    /**
     * @param $timeFrom
     * @param $timeTo
     */
    private function restoreLeads($timeFrom, $timeTo)
    {
        if (isset($this->providers[File::class])) {
            $providerFrom = $this->controller->providers[File::class];
        } else {
            return;
        }
        if (isset($this->providers[File::class])) {
            $providerTo = $this->controller->providers[File::class];
        } else {
            return;
        }

        $result = $providerTo->restoreFromOtherProvider($providerFrom, $timeFrom, $timeTo);

        echo json_encode([
            'status' => is_array($result) && count($result) > 0 ? 'ask more' : 'stop, please',
            'data' => $result,
        ]);
    }
}