<?php

namespace pistol88\filter\models;

use yii;

class FilterVariant extends \yii\db\ActiveRecord
{
    function behaviors()
    {
        return [
            'images' => [
                'class' => 'pistol88\gallery\behaviors\AttachImages',
                'mode' => 'single',
            ],
        ];
    }
    
    public static function tableName()
    {
        return '{{%filter_variant}}';
    }

    public function rules()
    {
        return [
            [['filter_id'], 'required'],
            [['filter_id', 'numeric_value'], 'integer'],
            [['value'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filter_id' => 'Фильтр',
            'value' => 'Значение',
            'numeric_value' => 'Числовое значение',
            'image' => 'Картинка',
        ];
    }

    public static function saveEdit($id, $name, $value)
    {
        $setting = FilterVariant::findOne($id);
        $setting->$name = $value;
        $setting->save();
    }

    public function beforeValidate()
    {
        if(empty($this->numeric_value)) {
            $this->numeric_value = (int)$this->value;
        }
        
        return true;
    }
}
