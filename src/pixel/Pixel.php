<?php
namespace Leadbusters\pixel;

use Leadbusters\processor\request\Request;
use Leadbusters\processor\Storage;
use Leadbusters\render\Content;

abstract class Pixel
{
    protected $PARAM = '';

    /**
     * @var Request
     */
    private $request;
    protected $event = 'PageView';
    protected $eventPurchase = 'Purchase';

    protected $id;

    public function __construct($id = null, $request = null)
    {
        if (!empty($id)) {
            $this->setId($id);
        }
        if (!empty($request)) {
            $this->setRequest($request);
        }
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
        if ($request->hasParam($this->PARAM) && empty($this->id)) {
            $this->setId($request->getParam($this->PARAM));
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        Storage::addParam(Storage::PIXELS, [
            'class' => get_class($this),
            'id' => $id,
        ]);
        $this->id = $id;
    }

    /**
     * Setting event name
     *
     * @param $name
     * @return $this
     */
    public function setEvent($name)
    {
        $this->event = $name;

        return $this;
    }

    public function setPurchaseEvent()
    {
        $this->setEvent($this->eventPurchase);
    }

    /**
     * @return mixed
     */
    abstract protected function render();

    /**
     * @return mixed
     */
    abstract protected function renderInit();

    /**
     * @return mixed
     */
    abstract protected function getSignature();

    private function isAttachedTo(Content $content)
    {
        return $content->isContains($this->getSignature());
    }

    public function attach(Content $content)
    {
        if (!empty($this->id)) {
            if (!$this->isAttachedTo($content)) {
                $content->addJs($this->renderInit());
            }
            $content->addJs($this->render());
        }
    }
}