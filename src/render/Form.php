<?php
namespace Leadbusters\render;


use Leadbusters\processor\Debug;

class Form
{
    /**
     * @var Debug
     */
    private $debug;

    private $fields = [
        'user_phone' => 'phone',
        'user_name' => 'name',
        'other' => 'other',
    ];

    public function __construct($debug = null)
    {
        if (empty($debug)) {
            $this->debug = new Debug();
        } else {
            $this->debug = $debug;
        }
    }

    /**
     * Link form data to lead params
     *
     * @param $paramName
     * @param $fieldName
     * @return $this
     */
    public function setField($paramName, $fieldName)
    {
        $this->fields[$paramName] = $fieldName;
        $this->debug->log('Set form field name ' . $paramName . ' = ' . $fieldName);

        return $this;
    }

    /**
     * Link form data to lead params
     *
     * @param array $params
     * @return $this
     */
    public function setFields($params)
    {
        foreach ($params as $paramName => $fieldName) {
            $this->setField($paramName, $fieldName);
        }

        return $this;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function fixInContent(Content $content)
    {
        $pattern = '/<form(.+)action="([^"]+)"/';
        $replace = '<form$1action=""';

        return $content->set(preg_replace($pattern, $replace, $content->get()));
    }
}