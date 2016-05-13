<?php

namespace pistol88\filter\models;

use yii;

class FilterValue extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%filter_value}}';
    }

    public function rules()
    {
        return [
            [['filter_id', 'item_id', 'variant_id'], 'required'],
            [['filter_id', 'item_id', 'variant_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filter_id' => 'Фильтр',
            'item_id' => 'Элемент',
            'variant_id' => 'Вариант'
        ];
    }
}
