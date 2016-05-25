<?php
namespace pistol88\filter;

use yii;

class Module extends \yii\base\Module
{
    public $relationFieldName = null;
    public $relationFieldValues = [];
    public $relationFieldValuesCallback = '';
    public $types = ['radio' => 'Один вариант', 'checkbox' => 'Много вариантов'];
    public $adminRoles = ['superadmin', 'admin'];

    public function init()
    {
        if(is_callable($this->relationFieldValues))
        {
            $values = $this->relationFieldValues;
            $this->relationFieldValues = $values();
        }
        parent::init();
    }
}
