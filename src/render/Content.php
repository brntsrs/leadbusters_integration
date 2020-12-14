<?php
namespace Leadbusters\render;

class Content
{
    private $content;
    private $debugProvider;
    public function __construct($debugProvider = false)
    {
        $this->debugProvider = $debugProvider;
    }

    public function set($content)
    {
        $this->content = $content;
        return $this;
    }

    public function get()
    {
        return $this->content;
    }

    private $cssFiles = [];
    private $jsFiles = [];
    private $css = [];
    private $js = [];

    public function addCssFile($file)
    {
        $this->cssFiles[] = $file;
        return $this;
    }

    public function addJsFile($file)
    {
        $this->jsFiles[] = $file;
        return $this;
    }

    public function addCss($text)
    {
        if (!empty($text)) {
            $this->css[] = $text;
        }
        return $this;
    }

    public function addJs($text)
    {
        if (!empty($text)) {
            $this->js[] = $text;
        }
        return $this;
    }

    public function render($noAssets = false)
    {
        if (!$noAssets) {
            foreach ($this->cssFiles as $file) {
                $this->addHeader($this->renderCssFile($file));
            }
            foreach ($this->css as $text) {
                $this->addHeader($this->renderCss($text));
            }

            foreach ($this->jsFiles as $file) {
                $this->addHeader($this->renderJsFile($file));
            }
            foreach ($this->js as $text) {
                $this->addFooter($this->renderJs($text));
            }
        }

        return $this->content;
    }

    private function renderCssFile($file)
    {
        return '<link rel="stylesheet" type="text/css" href="' . $file . '">';
    }

    private function renderJsFile($file)
    {
        return '<script src="' . $file . '"></script>';
    }

    private function renderJs($text)
    {
        if (strpos($text, '<script') === false) {
            return '<script>' . $text . '</script>';
        }
        return $text;
    }

    private function renderCss($text)
    {
        if (strpos($text, '<style') === false) {
            return '<style>' . $text . '</style>';
        }
        return $text;
    }

    /**
     * @param string $header
     * @return string
     */
    public function addHeader($header)
    {
        if ($header !== '') {
            $count = 0;
            $output = preg_replace('/(<title\b[^>]*>|<\\/head\s*>)/is', '<###head###>$1', $this->content, 1, $count);
            if ($count) {
                $this->content = str_replace('<###head###>', $header, $output);
            } else {
                $this->content = $header . $output;
            }
        }
        return $this;
    }

    /**
     * @param string $footer
     * @return string
     */
    public function addFooter($footer)
    {
        if ($footer !== '') {
            $this->content = str_replace('</body>', $footer . "\r\n" . '</body>', $this->content);
        }
        return $this;
    }

    public function isContains($text)
    {
        foreach ($this->jsFiles as $file) {
            if (strpos($file, $text)) {
                return true;
            }
        }
        foreach ($this->cssFiles as $file) {
            if (strpos($file, $text)) {
                return true;
            }
        }
        foreach ($this->js as $code) {
            if (strpos($code, $text)) {
                return true;
            }
        }
        foreach ($this->css as $code) {
            if (strpos($code, $text)) {
                return true;
            }
        }

        return strpos($this->content, $text) !== false;
    }
}