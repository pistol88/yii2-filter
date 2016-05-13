<?php
namespace pistol88\filter\assets;

use yii\web\AssetBundle;

class FrontendAsset extends AssetBundle
{
    public $depends = [
        'pistol88\filter\assets\Asset'
    ];

    public $js = [
        'js/frontend.js',
    ];

    public $css = [
        'css/styles.css',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/../web';
        parent::init();
    }

}
