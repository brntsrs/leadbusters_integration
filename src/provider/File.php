<?php
namespace Leadbusters\provider;

class File extends Provider
{
    /**
     * @param Query $query
     * @return array
     */
    public function read($query = null)
    {
        $resource = fopen($this->getTarget(), 'r');
        $data = [];
        $lineNumber = 0;
        while ($line = fgets($resource)) {
            if (!empty($query) && $lineNumber++ < $query->offset) {
                continue;
            }
            $row = $this->decode($line);
            if (isset($row['data'])) {
                if ($this->checkConditions($row['data'], $query->conditions)) {
                    $data[] = $row['data'];
                }
            } else {
                if ($this->checkConditions($row, $query->conditions)) {
                    $data[] = $row;
                }
            }
        }
        fclose($resource);
        return $data;
    }

    private function checkConditions($row, $conditions)
    {
        $isMatch = true;
        foreach ($conditions as $condition) {
            if (isset($condition[0], $condition[1], $condition[2])) {
                $field = $condition[0];
                $sign = $condition[1];
                $value = $condition[2];
                $conditionApply = true;
                switch ($sign) {
                    case '=':
                        $conditionApply = isset($row[$field]) && $row[$field] == $value;
                        break;
                    case '>=':
                        $conditionApply = isset($row[$field]) && $row[$field] >= $value;
                        break;
                    case '>':
                        $conditionApply = isset($row[$field]) && $row[$field] > $value;
                        break;
                    case '<=':
                        $conditionApply = isset($row[$field]) && $row[$field] <= $value;
                        break;
                    case '<':
                        $conditionApply = isset($row[$field]) && $row[$field] < $value;
                        break;
                    case '!=':
                    case '<>':
                        $conditionApply = !isset($row[$field]) || $row[$field] != $value;
                        break;
                }

                if ($condition['type'] == 'and') {
                    $isMatch = $isMatch && $conditionApply;
                } elseif ($condition['type'] == 'or') {
                    $isMatch = $isMatch || $conditionApply;
                }
            }
        }

        return $isMatch;
    }

    private $resource;
    public function write($data, $isCloseConnection = true)
    {
        if (empty($this->resource)) {
            $this->resource = fopen($this->getTarget(), 'a');
        }
        $data['time'] = date('d.m H:i:s');

        foreach ($data as $param => $value) {
            $line[$param] = $value;
        }
        fwrite($this->resource, $this->encode($data) . "\r\n");
        if ($isCloseConnection) {
            fclose($this->resource);
        }

        return null;
    }

    public function close()
    {
        if (!empty($this->resource)) {
            fclose($this->resource);
            $this->resource = null;
        }
    }
}