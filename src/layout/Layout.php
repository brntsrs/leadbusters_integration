<?php
namespace Leadbusters\layout;


use Leadbusters\processor\Controller;
use Leadbusters\processor\Debug;
use Leadbusters\render\Content;

abstract class Layout
{
    use TrackingUrl;
    use Pixels;

    private $view = 'template.html';
    private $isRenderDisabled = false;
    private $isAssetsDisabled = false;

    /**
     * @var Debug
     */
    protected $debug;

    /**
     * @var Content
     */
    protected $content;

    /**
     * @var Controller
     */
    public $controller;

    public function __construct(Controller $controller, $view = null)
    {
        $this->controller = $controller;
        $this->debug = $this->controller->debug;
        $this->content = new Content($this->debug);
        if (!empty($view)) {
            $this->view = $view;
        }

        ob_start();
        ob_implicit_flush(false);

        $this->debug->log('Layout start');
    }

    /**
     * End of layout, integration actions start
     */
    public function render()
    {
        $this->debug->log('Layout end: ' . get_class($this));

        if ($this->beforeRender()) {
            $this->appendPixelCode();

            if (!$this->isRenderDisabled) {
                if (file_exists($this->view)) {
                    require $this->view;
                } elseif (file_exists(__DIR__ . '/../../' . $this->view)) {
                    require __DIR__ . '/../../' . $this->view;
                }
                $this->content->set(ob_get_clean());
                if ($this->duringRender()) {
                    $this->debug->close('----------------------');

                    return $this->content->render($this->isAssetsDisabled);
                }
            } else {
                $this->debug->log('Render disabled');
            }
            $this->afterRender();
        }

        $this->debug->close('----------------------');
        return '';
    }

    /**
     * Disabling automatic assets including to layout
     *
     * @param bool $isDisabled
     * @return $this
     */
    public function noAssets($isDisabled = true)
    {
        $this->isAssetsDisabled = $isDisabled;

        return $this;
    }

    /**
     * Disabling page render, only code handler
     *
     * @return $this
     */
    public function noRender()
    {
        $this->isRenderDisabled = true;

        return $this;
    }

    /**
     * Event launching before HTML render
     */
    protected function beforeRender()
    {
        if ($this->controller->request->getParam('noRender')) {
            $this->noRender();
        }
        return true;
    }

    protected function duringRender()
    {
        return true;
    }

    /**
     * Evente launching after HTML render
     */
    protected function afterRender()
    {
        return true;
    }

    /**
     * @param $url
     */
    protected function redirect($url)
    {
        header('Location: ' . $url);
    }
}