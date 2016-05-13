<?php
namespace pistol88\filter\widgets;

use pistol88\filter\models\Filter;
use pistol88\filter\models\FilterVariants;
use yii\helpers\Html;
use yii\helpers\Url;
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
            $row = [];

            $row[] = Html::tag('div', Html::tag('strong', $filter->name), ['class' => 'col-lg-2']);

            $variants = [];
            if($variantsList = $filter->getVariants()) {
                $variants[] = Html::ul($variantsList, ['item' => function($item, $index) {
                    return $this->variant($item);
                }, 'class' => 'filter-variants', 'data-item-id' => $model->id, 'data-id' => $filter->id, 'data-delete-action' => Url::toRoute(['/filter/filter-value/delete']), 'data-create-action' => Url::toRoute(['/filter/filter-value/create'])]);
            }

            $new = [];
            $new[] = Html::input('text', 'variant_value', '', ['data-filter-id' => $filter->id, 'data-create-action' => Url::toRoute(['/filter/filter-variant/create']), 'class' => ' form-control', 'placeholder' => '']);
            $new[] = Html::button(Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['class' => 'btn btn-success']);
            $variants[] = Html::tag('div', implode('', $new), ['class' => 'new-variant']);

            $row[] = Html::tag('div', implode('', $variants), ['class' => 'col-lg-10']);

            $return[] = Html::tag('div', implode('', $row), ['class' => 'row']);
        }

        return Html::tag('div', implode('<hr>', $return), ['class' => 'pistol88-filter']);
    }

    private function variant($item)
    {
        $return = [];

        $variantFilters = $this->model->filterVariants();
        if(isset($variantFilters[$item->id])) {
            $checked = true;
        }
        else {
            $checked = false;
        }
        $return[] = Html::checkbox('variant', $checked, ['id' => 'filtervariant'.$item->id, 'data-id' => $item->id]);
        $return[] = ' ';
        $return[] = Html::label($item->value, 'filtervariant'.$item->id);
        return Html::tag('li', implode('', $return));
    }
}
