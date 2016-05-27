<?php
namespace pistol88\filter\behaviors;

use yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use pistol88\filter\models\FilterValue;
use pistol88\filter\models\FilterVariant;
use pistol88\filter\models\Filter;

class Filtered extends Behavior
{
    public $fieldName = 'filter';
    
    public function option($key, $value, $sign = '=')
    {
        if(!is_array($value)) {
            $value = [$value];
        }

        $filter = Filter::findOne(['slug' => $key]);

        $numeric_value = (int)current($value);
        
        if($sign == '=') {
            $variants = FilterVariant::findAll(['filter_id' => $filter->id, 'value' => $value]);
        } elseif($sign == '>') {
            $variants = FilterVariant::find()->where('filter_id = :filter_id AND numeric_value > :value', [':filter_id' => $filter->id, ':value' => $numeric_value])->all();
        } else {
            $variants = FilterVariant::find()->where('filter_id = :filter_id AND numeric_value < :value', [':filter_id' => $filter->id, ':value' => $numeric_value])->all();
        }

        $filterIds = [];

        foreach($variants as $variant) {
            $filterIds[$filter->id][] = $variant->id;
        }

        if(empty($filterIds)) {
            return $this->owner->andWhere(['id' => 0]);
        }

        return $this->filtered($filterIds, 2);
    }
    
    public function filtered($filterIds = false, $mode = 1)
    {
        if(!$filterIds) {
            $filterIds = Yii::$app->request->get($this->fieldName);
        }

        if(empty($filterIds)) {
            return $this->owner;
        }

        $condition = ['OR'];
        $variantCount = 0;
        $filterCount = count($filterIds);

        foreach($filterIds as $filterId => $variantIds) {
            $condition[] = ['filter_id' => $filterId, 'variant_id' => $variantIds];
            if($mode == 1) {
                $variantCount += count($variantIds);
            }
            else {
                $variantCount++;
            }
            
        }

        $filtered = FilterValue::find()->select('item_id')->groupBy('item_id')->andHaving("COUNT(DISTINCT `filter_id`) = $variantCount")->andFilterWhere($condition);

        if($filtered->count() > 0) {
            $this->owner->andWhere(['id' => $filtered]);
        } else {
            $this->owner->andWhere(['id' => 0]);
        }

        return $this->owner;
    }
}
