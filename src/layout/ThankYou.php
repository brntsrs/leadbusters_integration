<?php
namespace Leadbusters\layout;

use Leadbusters\pixel\Pixel;
use Leadbusters\processor\Storage;

class ThankYou extends Layout
{
    public function beforeRender()
    {
        $pixels = Storage::restoreParam(Storage::PIXELS);
        if (!empty($pixels)) {
            foreach (json_decode($pixels, true) as $pixelData) {
                $pixelClass = $pixelData['class'];
                /**
                 * @var Pixel $pixel
                 */
                $pixel = new $pixelClass($pixelData['id']);
                $pixel->setEvent('Purchase');

                $this->addPixel($pixel);
            }
        }
        return parent::beforeRender();
    }
}