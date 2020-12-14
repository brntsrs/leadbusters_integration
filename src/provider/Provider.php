<?php

namespace Leadbusters\provider;

abstract class Provider
{
    private $target;
    public function __construct($target = null)
    {
        $this->target = $target;
    }

    /**
     * @param $data
     * @param bool $isCloseConnection
     * @return string
     */
    abstract public function write($data, $isCloseConnection = true);

    abstract public function close();

    /**
     * @package Query $query
     * @return array
     */
    abstract public function read($query = null);

    protected function getTarget()
    {
        return $this->target;
    }

    public function encode($data)
    {
        return json_encode($data);
    }

    public function decode($data)
    {
        return json_decode($data, true);
    }

    /**
     * Save data to provider
     *
     * @param $data
     */
    public function log($data)
    {
        $this->write([
            'time' => date('Y-m-d H:i:s'),
            'data' => $data,
        ], false);
    }

    /**
     * Restore lead data from one Provider to another
     *
     * @param Provider $provider
     * @param null $timeFrom
     * @param null $timeTo
     * @return array
     */
    public function restoreFromOtherProvider(Provider $provider, $timeFrom = null, $timeTo = null, $limit = null, $offset = 0)
    {
        $data = (new Query($provider))
            ->andWhere(['time', '>=', $timeFrom])
            ->andWhere(['time', '<=', $timeTo])
            ->limit($limit)
            ->offset($offset)
            ->all();

        $result = [];
        foreach ($data as $row) {
            if (isset($data['data'])) {
                $result[] = $this->write($data['data']);
            } else {
                $result[] = $this->write($data);
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public static function getClassName()
    {
        return static::class;
    }
}