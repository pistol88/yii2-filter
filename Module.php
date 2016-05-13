<?php
namespace pistol88\filter;

use yii;

class Module extends \yii\base\Module
{
    public $relationModel = null;
    public $relationFieldName = null;
    public $relationFieldValues = [];
    public $relationFieldValuesCallback = '';
    public $types = ['checkbox' => 'Галочка', 'radio' => 'Радиокнопка', 'slider' => 'Слайдер'];
    public $adminRoles = ['superadmin', 'admin'];

    public function init()
    {
        if(is_callable($this->relationFieldValues)) {
            $values = $this->relationFieldValues;
            $this->relationFieldValues = $values();
        }
        parent::init();
    }
}
