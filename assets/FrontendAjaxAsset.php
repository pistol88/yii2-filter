<?php
namespace pistol88\filter\assets;

use yii\web\AssetBundle;

class FrontendAjaxAsset extends AssetBundle
{
    public $depends = [
        'pistol88\filter\assets\FrontendAsset'
    ];

    public $js = [
        'js/frontend_ajax.js',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/../web';
        parent::init();
    }

}
