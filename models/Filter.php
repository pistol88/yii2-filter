<?php
namespace pistol88\filter\models;

use yii;
use yii\helpers\ArrayHelper;

class Filter extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%filter}}';
    }

    public function rules()
    {
        return [
            [['name', 'slug'], 'required'],
            [['sort'], 'integer'],
            [['name', 'type', 'relation_field_name', 'description', 'slug', 'is_filter'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'slug' => 'Идентификатор',
            'sort' => 'Сортировка',
            'description' => 'Описание',
            'is_filter' => 'Фильтр',
            'type' => 'Тип',
            'relation_field_name' => 'Название поля',
            'relation_field_value' => 'Привязать к'
        ];
    }

    public function getVariants()
    {
        return $this->hasMany(FilterVariant::className(), ['filter_id' => 'id']);
    }

    public function getVariantsByFindModel($findModel)
    {
        $variantIds = FilterValue::find()->select('variant_id')->distinct()->where(['item_id' => $findModel->select('id')]);

        return $this->hasMany(FilterVariant::className(), ['filter_id' => 'id'])->where(['id' => $variantIds]);
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
        } else {
            $this->relation_field_value = serialize([]);
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
