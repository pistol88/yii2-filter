<?php
namespace pistol88\filter\behaviors;

use yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use pistol88\filter\models\FilterValue;

class Filtered extends Behavior
{
    public $fieldName = 'filter';
    public function filtered()
    {
        $filterIds = Yii::$app->request->get($this->fieldName);

        $condition = ['OR'];
        $variantCount = 0;
        $filterCount = count($filterIds);

        foreach($filterIds as $filterId => $variantIds) {
            $condition[] = ['filter_id' => $filterId, 'variant_id' => $variantIds];
            $variantCount += count($variantIds);
        }

        $filtered = FilterValue::find()->select('item_id')->groupBy('`item_id`')->andHaving("COUNT(DISTINCT `filter_id`) = $variantCount")->andFilterWhere($condition);

        if($filtered->count() > 0) {
            $this->owner->andWhere(['id' => $filtered]);
        } else {
            $this->owner->andWhere(['id' => 0]);
        }

        return $this->owner;
    }
}
