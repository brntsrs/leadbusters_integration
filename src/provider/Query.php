<?php

namespace Leadbusters\provider;

class Query
{
    private $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    public $conditions = [];
    public $limit = 10;
    public $offset = 0;

    /**
     * @param $condition
     * @return self
     */
    public function andWhere($condition)
    {
        $this->conditions[] = [
            'type' => 'and',
            'condition' => $condition,
        ];
        return $this;
    }

    /**
     * @param $condition
     * @return $this
     */
    public function orWhere($condition)
    {
        $this->conditions[] = [
            'type' => 'or',
            'condition' => $condition,
        ];
        return $this;
    }

    /**
     * @param $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    public function one()
    {
        $data = $this->provider->read(1);
        return isset($data[0]) ? $data[0] : null;
    }

    public function all()
    {
        return $this->provider->read($this);
    }
}