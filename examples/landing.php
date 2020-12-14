<?php

use Leadbusters\layout\Landing;
use Leadbusters\pixel\Facebook;
use Leadbusters\pixel\Google;
use Leadbusters\processor\Controller;
use Leadbusters\provider\File;
use Leadbusters\provider\Leadbusters;

(new Controller(new \Leadbusters\processor\Debug(new File(__DIR__ . '/debug.log'))))
    ->setLayout(Landing::class, 'template.html')
    ->addProvider(new Leadbusters())
    ->setTrackingUrl('https://leadbusters.network/t/12321')
    ->addProvider(new File(__DIR__ . '/leads.log'))
    ->addPixel(new Facebook())
    ->addPixel(new Google())
    ->addPixel(new Facebook('1231231312312'))
    ->addPixel(new Google('XX-1232131'))
    ->run();