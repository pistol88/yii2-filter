<?php
namespace pistol88\filter;

use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $app->get('assetManager')->bundles['yii2mod\slider\IonSliderAsset'] =
                [
                    'css' => [
                        'css/normalize.css',
                        'css/ion.rangeSlider.css',
                        'css/ion.rangeSlider.skinNice.css'
                     ]
                ];
    }
}