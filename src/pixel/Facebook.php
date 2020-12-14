<?php
namespace Leadbusters\pixel;

use Leadbusters\render\Content;

class Facebook extends Pixel
{
    protected $PARAM = 'facebook_id';

    private $event = 'PageView';
    private $price = null;
    private $currency = null;
    private $advancedMatchingParams = [];


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

    /**
     * Setting event price
     *
     * @param $price
     * @param $currency
     * @return $this
     */
    public function setPrice($price, $currency)
    {
        $this->price = floatval($price);
        $this->currency = $currency;

        return $this;
    }

    private function setAdvancedMatchingParams()
    {
        $params = [];
        foreach ($_GET as $param => $value) {
            if (strpos($param, 'fb_') !== false) {
                $params[str_replace('fb_', '', $param)] = $value;
            }
        }
        $this->advancedMatchingParams = $params;
    }

    /**
     * Generating html code
     *
     * @return string
     */
    public function jsImg()
    {
        if (empty($this->id)) {
            return false;
        }
        $params = [
            'id' => $this->id,
            'ev' => $this->event,
            'noscript' => 1,
        ];
        if (!empty($this->price) && !empty($this->currency)) {
            $params['cd'] = [
                'value' => $this->price,
                'currency' => $this->currency,
            ];
        }
        if (!empty($this->advancedMatchingParams)) {
            $params['ud'] = $this->advancedMatchingParams;
        }

        $url = 'https://www.facebook.com/tr?' . str_replace(['%5B', '%5D'], ['[', ']'], http_build_query($params));
        return '<img src="' . $url . '" />';
    }

    public function jsInit()
    {
        if (empty($this->id)) {
            return false;
        }
        $advancedMatchingParams = empty($this->advancedMatchingParams) ? null : (', ' . json_encode($this->advancedMatchingParams));
        return 'fbq(\'init\', \'' . $this->id . '\'' . $advancedMatchingParams . ');';
    }

    public function jsEvent()
    {
        if (empty($this->id)) {
            return false;
        }
        $code = [];
        if ($this->event != 'PageView') {
            $code[] = 'fbq(\'track\', \'PageView\');';
        }
        $code[] = 'fbq(\'track\', \'' . $this->event . '\');';
        return implode("\r\n", $code);
    }

    public function renderInit()
    {
        return <<<HTMLCODE
<!-- Facebook Pixel Code -->
<script>
 !function(f,b,e,v,n,t,s)
 {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
 n.callMethod.apply(n,arguments):n.queue.push(arguments)};
 if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
 n.queue=[];t=b.createElement(e);t.async=!0;
 t.src=v;s=b.getElementsByTagName(e)[0];
 s.parentNode.insertBefore(t,s)}(window, document,'script',
 'https://connect.facebook.net/en_US/fbevents.js');
</script>
<!-- End Facebook Pixel Code -->
HTMLCODE;
    }

    public function render()
    {
        $fbInits = $this->jsInit();
        $fbEvent = $this->jsEvent();
        $fbImgs = $this->jsImg();

        return <<<HTMLCODE
<!-- Facebook Pixel Code -->
<script>
 {$fbInits}
 {$fbEvent}
</script>
<noscript>{$fbImgs}</noscript>
<!-- End Facebook Pixel Code -->
HTMLCODE;
    }

    public function getSignature()
    {
        return 'connect.facebook.net';
    }
}