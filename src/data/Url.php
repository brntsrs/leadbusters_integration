<?php
namespace Leadbusters\data;


class Url
{
    const DEFAULT_API_DOMAIN = 'http://leadbusters.network';

    /**
     * Creating full tracking URL according to Leadbusters API
     *
     * @param $url
     * @return string
     */
    public function createLinkFromPart($url)
    {
        if (stripos($url, '/t/') === 0) {
            $apiDomain = str_replace(['http://', 'https://'], '', self::DEFAULT_API_DOMAIN);
            $apiProtocol = strpos(self::DEFAULT_API_DOMAIN, 'https') === false ? 'http' : 'https';
            $url = $apiProtocol . '://' . $apiDomain . '/' . $url;
        }

        if (!isset(parse_url($url)['scheme']) && strpos($url, '//') !== 0) {
            return $this->createLinkFromPart('/t/' . $url);
        }

        return $this->appendSubIdsToUrl($url);
    }

    /**
     * @param $url
     * @return string
     */
    public function appendSubIdsToUrl($url)
    {
        $subIds = $this->getSubIds();
        if (!empty($subIds)) {
            if (strpos($url, '?') === false) {
                $url .= '?' . http_build_query($subIds);
            } else {
                $urlParts = parse_url($url);
                if (!empty($urlParts['query'])) {
                    parse_str($urlParts['query'], $extraParams);
                    foreach ($extraParams as $param => $value) {
                        $subIds[$param] = $value;
                    }
                    $urlParts['query'] = http_build_query($subIds);
                    $url = (strpos($url, '//') === 0 ? '//' : '') . $this->urlCombine($urlParts);
                }
            }
        }
        return $url;
    }

    /**
     * @return array
     */
    public function getSubIds()
    {
        $subIds = [];
        foreach ($_GET as $param => $value) {
            if (strpos($param, 'sub') === 0) {
                $subIds[$param] = $value;
            }
        }
        return $subIds;
    }

    /**
     * Fixing URL if webmaster used damaged or direct link for integration
     *
     * @param $url
     * @return string
     */
    public function convertAndFixWebmasterLink($url)
    {
        if (strpos($url, '?') !== false) {
            parse_str(parse_url($url, PHP_URL_QUERY), $urlParams);
            if (!empty($urlParams)) {
                foreach ($urlParams as $param => $value) {
                    if (stripos($value, '/t/') === 0) {
                        return $this->createLinkFromPart($value);
                    }
                }
            }
        }
        return $url;
    }

    /**
     * Combining URL from parts used in parse_url
     *
     * @param $parsedUrl
     * @return string
     */
    public function urlCombine($parsedUrl)
    {
        $scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
        $host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
        $port = isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';
        $user = isset($parsedUrl['user']) ? $parsedUrl['user'] : '';
        $pass = isset($parsedUrl['pass']) ? ':' . $parsedUrl['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
        $query = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
        $fragment = isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';

        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    public function urlOrigin($useForwardedHost = false)
    {
        $ssl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on');
        $sp = strtolower($_SERVER['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port = $_SERVER['SERVER_PORT'];
        $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : (':' . $port);
        $host = ($useForwardedHost && isset($_SERVER['HTTP_X_FORWARDED_HOST'])) ?
            $_SERVER['HTTP_X_FORWARDED_HOST'] :
            (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null);
        $host = isset($host) ? $host : $_SERVER['SERVER_NAME'] . $port;

        return $protocol . '://' . $host;
    }

    public function fullUrl($useForwardedHost = false)
    {
        return $this->urlOrigin($useForwardedHost) . $_SERVER['REQUEST_URI'];
    }
}