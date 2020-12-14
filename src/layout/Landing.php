<?php
namespace Leadbusters\layout;

use Leadbusters\processor\Controller;
use Leadbusters\processor\Storage;
use Leadbusters\render\Form;

class Landing extends Layout
{
    /**
     * @var Form
     */
    private $form;

    public function __construct(Controller $controller, $view = null)
    {
        parent::__construct($controller, $view);

        $this->form = new Form($this->debug);
    }

    public function setForm(Form $form)
    {
        $this->form = $form;
        return $this;
    }

    public function getForm()
    {
        return $this->form;
    }

    public function duringRender()
    {
        $this->form->fixInContent($this->content);
        if ($this->controller->track->getId(true) === null) {
            $this->debug->log('Need to add tracking script');
        } else {
            $this->controller->track->storeInCookies();
            $this->debug->log('TrackId exists, no need in tracking script');
        }
        $this->addTrackScript();

        return parent::duringRender();
    }

}