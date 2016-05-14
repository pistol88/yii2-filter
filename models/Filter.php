<?php
namespace pistol88\filter\models;

use yii;
use pistol88\filter\models\FilterVariant;
use pistol88\filter\models\FieldRelationValue;
use yii\helpers\ArrayHelper;

class Filter extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return '{{%filter}}';
    }

    public function rules() {
        return [
            [['name'], 'required'],
            [['name', 'type', 'relation_field_name', 'model_name', 'description'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'description' => 'Описание',
            'type' => 'Тип',
            'model_name' => 'Группа',
            'relation_field_name' => 'Название поля',
            'relation_field_value' => 'Привязать к'
        ];
    }

    public function getVariants()
    {
        return $this->hasMany(FilterVariant::className(), ['filter_id' => 'id'])->all();
    }

    public function getSelected()
    {
        return ArrayHelper::map($this->hasMany(FieldRelationValue::className(), ['filter_id' => 'id'])->all(), 'value', 'value');
    }
    
    public static function saveEdit($id, $name, $value)
    {
        $setting = Filter::findOne($id);
        $setting->$name = $value;
        $setting->save();
    }

    public function beforeDelete()
    {
        foreach ($this->hasMany(FieldRelationValue::className(), ['filter_id' => 'id'])->all() as $frv) {
            $frv->delete();
        }
        foreach ($this->hasMany(FilterVariant::className(), ['filter_id' => 'id'])->all() as $fv) {
            $fv->delete();
        }
		
		return true;
    }
    
    public function beforeValidate()
    {
        $values = yii::$app->request->post('Filter')['relation_field_value'];
        
        if(is_array($values)) {
            FieldRelationValue::deleteAll(['filter_id' => $this->id]);
            foreach($values as $value) {
                $filterRelationValue = new FieldRelationValue;
                $filterRelationValue->filter_id = $this->id;
                $filterRelationValue->value = $value;
                $filterRelationValue->save();
            }

            $this->relation_field_value = serialize($values);
        }
        
        return true;
    }
    
    public function afterFind()
    {
        if(empty($this->relation_field_value)) {
            $this->relation_field_value = array();
        } elseif(!is_array($this->relation_field_value)) {
            $this->relation_field_value = unserialize($this->relation_field_value);
        }
        
        return true;
    }
}
