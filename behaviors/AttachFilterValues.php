<?php
namespace pistol88\filter\behaviors;

use yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use pistol88\filter\models\Filter;
use pistol88\filter\models\FilterValue;

class AttachFilterValues extends Behavior
{
    private $filterVariants = null;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_DELETE => 'deleteValues',
        ];
    }
    
    public function filterVariants()
    {
        if(!$this->owner->isNewRecord) {
            if(is_array($this->filterVariants)) {
                return $this->filterVariants;
            }

            $values = FilterValue::findAll(['item_id' => $this->owner->id]);

            $this->filterVariants = [];

            foreach($values as $value) {
                $this->filterVariants[$value->variant_id] = $value->variant_id;
            }

            return $this->filterVariants;
        }
        else {
            return [];
        }
    }

    public function getFilters()
    {
        $model = $this->owner;
        $return = [];
        $filters = Filter::find()->where(['model_name' => $model::className()])->all();
        foreach($filters as $filter) {
            $field = $filter->relation_field_name;
            $show = false;
            if (empty($filter->relation_field_value)) {
                $show = true;
            }
            else {
                foreach ($filter->relation_field_value as $rfv) {
                    if ($model->{$field} == $rfv) {
                        $show = true;
                    }
                }
            }

            if ($show == true) {
                $return[] = $filter;
            }
        }

        return $return;
    }

    public function getSelectedFilters()
    {
        $return = [];
        $variantFilters = $this->owner->filterVariants();
        foreach($this->owner->getFilters() as $filter) {
            foreach($filter->getVariants() as $variant) {
                if(isset($variantFilters[$variant->id])) {
                    $return[$filter->name][] = $variant->value;
                }
            }
        }

        return $return;
    }
    
    public function getFilterData()
    {
        return "data-filter-variants='".json_encode($this->filterVariants())."'";
    }

    public function deleteValues()
    {
        foreach(FilterValue::find()->where(['item_id' => $this->owner->id])->all() as $value) {
            $value->delete();
        }
        
        return true;
    }
}
