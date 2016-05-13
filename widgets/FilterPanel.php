<?php
namespace pistol88\filter\widgets;

use yii\helpers\Html;
use yii\helpers\Url;
use pistol88\filter\models\Filter;
use pistol88\filter\models\FieldRelationValue;
use yii;

class FilterPanel extends \yii\base\Widget
{
    public $modelName = NULL;
    public $itemId = NULL;
    public $filterId = NULL;
    public $itemCssClass = 'item';
    public $fieldName = 'filter';
    public $blockCssClass = 'block';
    public $submitButtonValue = 'Показать';

    public function init()
    {
        parent::init();

        \pistol88\filter\assets\FrontendAsset::register($this->getView());
    }

    public function run()
    {
        $params = ['model_name' => $this->modelName];
        if($this->filterId) {
            $params['id'] = $this->filterId;
        }

        $filters = Filter::find()->andWhere($params)->all();
        
        $return = [];
        foreach($filters as $filter) {
            if(in_array($this->itemId, $filter->selected)) {
                $block = '';
                $title = Html::tag('p', $filter->name, ['class' => 'heading']);
                foreach($filter->getVariants() as $variant) {
                    $checked = false;
                    if($filterData = yii::$app->request->get('filter')) {
                        if(isset($filterData[$filter->id]) && (isset($filterData[$filter->id][$variant->id]) |  $filterData[$filter->id] == $variant->id)) {
                            $checked = true;
                        }
                    }

					if(!in_array($filter->type, array('radio', 'checkbox'))) {
						$filter->type = 'checkbox';
					}
                    
                    if($filter->type == 'radio') {
                        $fieldName = $this->fieldName.'['.$filter->id.']';
                    } else {
                        $fieldName = $this->fieldName.'['.$filter->id.']['.$variant->id.']';
                    }

                    $field = Html::input($filter->type, $fieldName, $variant->id, ['checked' => $checked, 'data-item-css-class' => $this->itemCssClass, 'id' => "variant{$variant->id}"]);
                    $field .= Html::label($variant->value, "variant{$variant->id}");
                    $block .= Html::tag('div', $field);
                }
                $return[] = Html::tag('div', $title.$block, ['class' => $this->blockCssClass]);
            }
            
        }

        if($return) $return[] = Html::input('submit', 'submit', $this->submitButtonValue, ['class' => 'btn btn-submit']);
        
        return Html::tag('form', implode('', $return), ['name' => 'pistol88-filter', 'action' => '', 'class' => 'pistol88-filter']);
    }
}
