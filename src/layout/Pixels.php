<?php
namespace Leadbusters\layout;

use Leadbusters\pixel\Pixel;
use Leadbusters\processor\Storage;
use Leadbusters\render\Content;

/**
 * Trait Pixels
 * @package Leadbusters\layout
 * @property Content $content
 */
trait Pixels
{
    /**
     * @var Pixel[]
     */
    private $pixels = [];

    public function addPixel(Pixel $pixel)
    {
        $this->pixels[] = $pixel;
        $this->debug->log('Added pixel: ' . $pixel->getId() . ', ' . get_class($pixel));

        return $this;
    }

    private function appendPixelCode()
    {
        foreach ($this->pixels as $pixel) {
            $pixel->attach($this->content);
        }
    }

    public function storePixels()
    {
        Storage::clearParam(Storage::PIXELS);
        foreach ($this->pixels as $pixel) {
            Storage::addParam(Storage::PIXELS,
                [
                    'class' => get_class($pixel),
                    'id'    => $pixel->getId(),
                ]);
        }
    }
}