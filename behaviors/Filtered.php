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
    
    public function option($key, $value) {
        if(!is_array($value)) {
            $value = [$value];
        }
        
        $filter = Filter::findOne(['slug' => $key]);

        $variants = ArrayHelper::map(FilterVariant::findAll(['filter_id' => $filter->id, 'value' => $value]), 'value', 'id');

        $filterIds = [];
        foreach($value as $fv) {
            if(isset($variants[$fv])) {
                $filterIds[$filter->id] = $variants[$fv];
            }
        }

        if(empty($filterIds)) {
            return $this->owner->andWhere(['id' => 0]);
        }

        return $this->filtered($filterIds);
    }
    
    public function filtered($filterIds = false)
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
            $variantCount += count($variantIds);
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
