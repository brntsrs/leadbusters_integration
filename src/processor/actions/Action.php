<?php
namespace Leadbusters\processor\actions;

use Leadbusters\processor\Controller;
use Leadbusters\processor\Debug;

abstract class Action
{
    /**
     * @var Debug $debug
     */
    protected $debug;
    /**
     * @var Controller $controller
     */
    protected $controller;

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
        $this->debug = $controller->debug;
    }

    abstract public function run();
}