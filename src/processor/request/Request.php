<?php
namespace Leadbusters\processor\request;

class Request
{
    private $method = 'GET';
    private $isSecureConnection = false;
    private $params = [];

    /**
     * @var Headers
     */
    private $headers;

    public function __construct()
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
        }
        $this->headers = new Headers();

        $this->isSecureConnection = $this->checkIsSecureConnection();

        $this->loadParams();
    }

    /**
     * Returns the method of the current request (e.g. GET, POST, HEAD, PUT, PATCH, DELETE).
     * @return string request method, such as GET, POST, HEAD, PUT, PATCH, DELETE.
     * The value returned is turned into upper case.
     */
    public function getMethod()
    {
        return $this->method;
    }

    public function getIsSecureConnection()
    {
        return $this->isSecureConnection;
    }

    public function checkIsSecureConnection()
    {
        return isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'],'on')===0 || $_SERVER['HTTPS']==1)
            || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'],'https')===0;
    }

    public function getServerUrl()
    {
        return 'http' . ($this->isSecureConnection ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
    }

    private function loadParams()
    {
        $this->params = $_REQUEST;
    }

    public function getParam($param, $defaultValue = null)
    {
        return $this->hasParam($param) ? $this->params[$param] : $defaultValue;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function hasParam($param)
    {
        return isset($this->params[$param]);
    }

    public function setParam($param, $value)
    {
        $this->params[$param] = $value;
    }

    public function getUrl()
    {
        if ($this->headers->has('X-Rewrite-Url')) { // IIS
            $requestUri = $this->headers->get('X-Rewrite-Url');
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
            if ($requestUri !== '' && $requestUri[0] !== '/') {
                $requestUri = preg_replace('/^(http|https):\/\/[^\/]+/i', '', $requestUri);
            }
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0 CGI
            $requestUri = $_SERVER['ORIG_PATH_INFO'];
            if (!empty($_SERVER['QUERY_STRING'])) {
                $requestUri .= '?' . $_SERVER['QUERY_STRING'];
            }
        } else {
            $requestUri = '';
        }

        return $requestUri;
    }

    public function getUserIp()
    {
        if ($this->headers->has('cf-connecting-ip')) {
            if (is_array($this->headers->get('cf-connecting-ip'))) {
                $ip = $this->headers->get('cf-connecting-ip')[0];
            } else {
                $ip = $this->headers->get('cf-connecting-ip');
            }
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = explode(',', getenv('HTTP_X_FORWARDED_FOR'))[0];
        } elseif (getenv('HTTP_X_REAL_IP')) {
            $ip = getenv('HTTP_X_REAL_IP');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ip = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ip = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $ip = getenv('HTTP_FORWARDED');
        } elseif (getenv('REMOTE_ADDR')) {
            $ip = getenv('REMOTE_ADDR');
        } else {
            $ip = getmygid();
        }

        if (preg_match("/([\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3})/si", $ip, $match)) {
            $ip = $match[1];
        } elseif (preg_match("/([\d\w]{1,4}:[\d\w]{1,4}:[\d\w]{1,4}:[\d\w]{1,4}:[\d\w]{1,4}:[\d\w]{1,4}:[\d\w]{1,4}:[\d\w]{1,4})/si", $ip, $match)) {
            $ip = $match[1];
        } else {
            $ip = '127.0.0.1';
        }

        return $ip;
    }

    public function getUserAgent()
    {
        return $this->headers->get('User-Agent');
    }
}