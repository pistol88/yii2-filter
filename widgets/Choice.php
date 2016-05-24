<?php
namespace pistol88\filter\widgets;

use pistol88\filter\models\Filter;
use pistol88\filter\models\FilterVariants;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\select2\Select2;
use Yii;

class Choice extends \yii\base\Widget
{
    public $model = NULL;

    public function init()
    {
        \pistol88\filter\assets\BackendAsset::register($this->getView());
        
        parent::init();
    }

    public function run()
    {
        $return = [];
        $model = $this->model;

        foreach($model->getFilters() as $filter) {
            $row = $this->renderFilter($filter);
            $return[] = Html::tag('div', implode('', $row), ['class' => 'row']);
        }

        if(empty($return)) {
            return null;
        }
        
        return Html::tag('div', implode('<hr>', $return), ['class' => 'pistol88-filter']);
    }
    
    private function renderFilter($filter)
    {
        $model = $this->model;
        
        $row = [];

        $row[] = Html::tag('div', Html::tag('strong', $filter->name . ': '), ['class' => 'col-lg-3']);

        $variants = [];

        $variantsList = $filter->variants;
        $options = [
            'class' => 'form-group option-variants filter-data-container',
            'data-item-id' => $model->id,
            'data-id' => $filter->id,
            'data-delete-action' => Url::toRoute(['/filter/filter-value/delete']),
            'data-create-action' => Url::toRoute(['/filter/filter-value/create']),
            'data-update-action' => Url::toRoute(['/filter/filter-value/update']),
        ];
        if($filter->type == 'radio') {
            $variantsList = ArrayHelper::map($variantsList, 'id', 'value');
            $variantsList[0] = '-';
            ksort($variantsList);

            $checked = false;
            foreach($variantsList as $variantId => $value) {
                if($this->model->checkedId($variantId)) {
                    $checked = $variantId;
                    break;
                }
            }

            $select = Select2::widget([
                'name' => 'choise-option',
                'value' => $checked,
                'data' => $variantsList,
                'language' => 'ru',
                'options' => ['placeholder' => 'Выберите значение ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            $variants[] = Html::tag('div', $select, $options);
        } else {
            $options['class'] .= ' filter-variants';
            $options['item'] = function($item, $index) {
                return $this->variant($item);
            };
            $variants[] = Html::ul($variantsList, $options);
        }

        $new = [];
        $new[] = Html::input('text', 'variant_value', '', ['data-filter-id' => $filter->id, 'data-create-action' => Url::toRoute(['/filter/filter-variant/create']), 'class' => ' form-control', 'placeholder' => '']);
        $new[] = Html::button(Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['class' => 'btn btn-success']);
        
        $variants[] = Html::tag('div', implode('', $new), ['class' => 'new-variant']);

        $row[] = Html::tag('div', implode('', $variants), ['class' => 'col-lg-9']);
            
        return $row;
    }
    
    private function variant($item)
    {
        $return = [];

        $checked = $this->model->checkedId($item->id);

        $return[] = Html::checkbox('variant', $checked, ['id' => 'filtervariant'.$item->id, 'data-id' => $item->id]);
        $return[] = ' ';
        $return[] = Html::label($item->value, 'filtervariant'.$item->id);
        return Html::tag('li', implode('', $return));
    }
}
