<?php
namespace Leadbusters\pixel;

class Google extends Pixel
{
    protected $PARAM = 'google_id';

    protected function renderInit()
    {
        $id = $this->getId();
        return <<<CODE
<script async src="https://www.googletagmanager.com/gtag/js?id=$id"> </script>
<script>
    window.dataLayer = window.dataLayer || [ ] ;
    function gtag ( ) {dataLayer.push (arguments ) ; }
</script>
CODE;
    }

    protected function render()
    {
        $id = $this->getId();
        if ($this->event == $this->eventPurchase) {
            $addEvent = <<<CODE
ga('send', 'event', {
  'eventCategory': 'Products',
  'eventAction': '{$this->event}'
});
CODE;
        } else {
            $addEvent = '';
        }
        return <<<CODE
<script>
gtag ('js' , new Date ( ) ) ;
gtag ('config', '$id') ;
$addEvent
</script>
CODE;
    }

    protected function getSignature()
    {
        return 'https://www.googletagmanager.com/gtag/js';
    }
}