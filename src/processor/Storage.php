<?php
namespace Leadbusters\processor;


use Leadbusters\processor\request\Cookie;

class Storage
{
    const TRACK_ID = 'track_id';
    const PIXELS = 'pixels';

    private static $isSessionStarted = false;

    /**
     * Store param value in all available storage
     *
     * @param $param
     * @param $value
     */
    public static function saveParam($param, $value)
    {
        Cookie::init();
        Cookie::set($param, $value, Cookie::PERMANENT);

        self::startSession();
        $_SESSION[$param] = $value;
    }

    /**
     * Restore param value from storage using prioritising conditions
     *
     * @param $param
     * @return mixed
     */
    public static function restoreParam($param)
    {
        Cookie::init();
        $id = Cookie::get($param);

        if (empty($id)) {
            self::startSession();
            $id = isset($_SESSION[$param]) ? $_SESSION[$param] : null;
        }

        return $id;
    }

    public static function clearAll()
    {
        $removeParams = [self::TRACK_ID, self::PIXELS];
        foreach ($removeParams as $removeParam) {
            self::clearParam($removeParam);
        }
    }

    /**
     * Clear param from cookie and session
     *
     * @param $param
     */
    public static function clearParam($param)
    {
        Cookie::clear($param);
        Cookie::clear(Cookie::getPrefix() . $param);

        self::startSession();
        $_SESSION[$param] = null;
    }

    public static function addParam($param, $value)
    {
        $allValues = self::restoreParam($param);
        if (empty($allValues)) {
            $allValues = [];
        } else {
            $allValues = @json_decode($allValues, true);
        }

        $allValues[] = $value;

        self::saveParam($param, json_encode($allValues));
    }

    public static function addParams($param, $values)
    {
        foreach ($values as $value) {
            self::addParam($param, $value);
        }
    }

    /**
     * Session starter
     */
    private static function startSession()
    {
        if (!self::$isSessionStarted) {
            session_start();
            self::$isSessionStarted = true;
        }
    }
}