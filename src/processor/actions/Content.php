<?php
namespace Leadbusters\processor\actions;

use Leadbusters\layout\Landing;
use Leadbusters\processor\Controller;
use Leadbusters\processor\Storage;

class Content extends Action
{
    public function __construct(Controller $controller)
    {
        parent::__construct($controller);

        if ($controller->layout instanceof Landing) {
            Storage::clearAll();
            $controller->layout->storePixels();
        }
    }

    public function run()
    {
        if ($this->beforeRender()) {
            echo $this->controller->layout->render();
            $this->afterRender();
        }
    }

    private function beforeRender()
    {
        return true;
    }

    private function afterRender()
    {
        $this->controller->closeProviders();
    }
}