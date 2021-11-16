<?php
if (file_exists(dirname(__FILE__) . '/leadbusters')) {
    require_once dirname(__FILE__) . '/leadbusters/vendor/autoload.php';
} else {
    require_once 'phar://' . dirname(__FILE__) . '/leadbusters.phar/vendor/autoload.php';
}

//====================================================================================
// 1. Rename your index.html file into landing.html
// 2. Place this file into your landing directory as index.php
// 3. Replace your tracking URL from Leadbusters in setTrackingUrl() call below
// 4. Allow your directory to be written, so you could have leads.log file as backup
// 5. Open your landing with your browser and see the result, create test lead
//====================================================================================

(new Leadbusters\processor\Controller())
    ->setLayout(Leadbusters\layout\Landing::class, 'landing.html')
    //Specifying page type and filename of your html code
    ->addProvider(new Leadbusters\provider\Leadbusters())
    //Mandatory setting, adding Leadbusters integraion initiation
    ->setTrackingUrl('https://leadbusters.network/t/12345')
    //Get your tracking link from offer page by creating new flow
    ->addProvider(new Leadbusters\provider\File(__DIR__ . '/leads.log'))
    //Log file is optional, used in case of server failure, so we can restore leads
    ->addPixel(new Leadbusters\pixel\Facebook())
    //Pixel will catch any Fcaebook Pixel ID from URL parameter 'facebook_id'
    //Use URL look like https://example.com/landings/offer1/1/?facebook_id=1234567890
    //Or add specific pixel ID like next:
    //->addPixel(new Leadbusters\pixel\Facebook(1234567890))
    ->addPixel(new Leadbusters\pixel\Google())
    //Pixel will catch any Google Analytics ID from URL parameter 'google_id'
    //Use URL look like https://example.com/landings/offer1/1/?google_id=XYZ-12345678
    //Or add specific pixel ID like next:
    //->addPixel(new Leadbusters\pixel\Google('XYZ-12345678'))
    ->addPixel(new Leadbusters\pixel\TikTok())
    //Pixel will catch any TiKTok ID from URL parameter 'tiktok_id'
    //Use URL look like https://example.com/landings/offer1/1/?tiktok_id=12345678
    //Or add specific pixel ID like next:
    //->addPixel(new Leadbusters\pixel\TikTok('12345678'))
    ->run();