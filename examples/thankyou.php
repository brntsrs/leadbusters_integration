<?php
if (file_exists(dirname(__FILE__) . '/leadbusters')) {
    require_once dirname(__FILE__) . '/leadbusters/vendor/autoload.php';
} else {
    require_once 'phar://' . dirname(__FILE__) . '/leadbusters.phar/vendor/autoload.php';
}

//====================================================================================
// 1. Place your html file into thankyou.html
// 2. Place yout thankyou.html file into your landing directory
// 3. Place this file into your landing directory as thankyou.php
// 4. Open your thank-you page with your browser and see the result, create test lead
//====================================================================================

(new Leadbusters\processor\Controller())
    ->setLayout(Leadbusters\layout\ThankYou::class, 'thankyou.html')
    //Specifying page type and filename of your html code
    //->addPixel(new Leadbusters\pixel\Facebook())
    //No need to add extra Pixel attachments if your thankyou.php will be in the landing folder
    //Landing integration will store your pixel IDs in cookies and automatically add them on this page
    ->run();