<?php
namespace Leadbusters\processor;

use Leadbusters\processor\request\Request;

abstract class Application
{
    /**
     * @var Debug
     */
    public $debug;

    /**
     * @var Request
     */
    public $request;

    public function __construct($debug = null)
    {
        if (empty($debug)) {
            $this->debug = new Debug();
        } else {
            $this->debug = $debug;
        }
        $this->request = new Request();

        $this->init();
    }

    abstract protected function init();
}