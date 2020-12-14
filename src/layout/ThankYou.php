<?php
namespace Leadbusters\layout;

use Leadbusters\processor\Storage;

class ThankYou extends Layout
{
    public function beforeRender()
    {
        $pixels = Storage::restoreParam(Storage::PIXELS);
        if (!empty($pixels)) {
            foreach (json_decode($pixels, true) as $pixelData) {
                $pixelClass = $pixelData['class'];

                $this->addPixel(new $pixelClass($pixelData['id']));
            }
        }
        return parent::beforeRender();
    }
}