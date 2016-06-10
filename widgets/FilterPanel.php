<?php
namespace pistol88\filter\widgets;

use yii\helpers\Html;
use yii\helpers\Url;
use pistol88\filter\models\Filter;
use pistol88\filter\models\FieldRelationValue;
use yii;

class FilterPanel extends \yii\base\Widget
{
    public $itemId = NULL;
    public $filterId = NULL;
    public $itemCssClass = 'item';
    public $fieldName = 'filter';
    public $blockCssClass = 'block';
    public $findModel = false; //::find() модели, по которой будем искать соответствия
    public $ajaxLoad = false; //Ajax подгрузка результатов
    public $resultHtmlSelector = null; //CSS селектор, который хранит результаты
    public $submitButtonValue = 'Показать';

    public function init()
    {
        parent::init();

        if($this->ajaxLoad) {
            \pistol88\filter\assets\FrontendAjaxAsset::register($this->getView());
        } else {
            \pistol88\filter\assets\FrontendAsset::register($this->getView());
        }
    }

    public function run()
    {
        $params = ['is_filter' => 'yes'];
        
        if($this->filterId) {
            $params['id'] = $this->filterId;
        }

        $filters = Filter::find()->andWhere($params)->all();
        
        $return = [];
        $haveVariants = false;
        
        foreach($filters as $filter) {
            if(in_array($this->itemId, $filter->selected)) {
                $block = '';
                $title = Html::tag('p', $filter->name, ['class' => 'heading']);
                
                if($this->findModel) {
                    $variants = $filter->getVariantsByFindModel($this->findModel)->all();
                } else {
                    $variants = $filter->variants;
                }

                foreach($variants as $variant) {
                    $haveVariants = true;
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

        if(!$haveVariants) {
            return null;
        }
        
        if($return) $return[] = Html::input('submit', '', $this->submitButtonValue, ['class' => 'btn btn-submit']);
        
        return Html::tag('form', implode('', $return), ['data-resulthtmlselector' => $this->resultHtmlSelector, 'name' => 'pistol88-filter', 'action' => '', 'class' => 'pistol88-filter']);
    }
}
