<?php
namespace pistol88\filter\controllers;

use yii;
use pistol88\filter\models\FilterValue;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;


class FilterValueController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => $this->module->adminRoles,
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'create' => ['post'],
                ],
            ],
        ];
    }

    public function actionCreate()
    {
        $model = new FilterValue();

        $json = [];

        if ($model->load(yii::$app->request->post()) && $model->save()) {
            $json['result'] = 'success';
        } else {
            $json['result'] = 'fail';
        }

        return json_encode($json);
    }

    public function actionDelete()
    {
        $itemId = yii::$app->request->post('item_id');
        $variantId = yii::$app->request->post('variant_id');

        FilterValue::find()->where(['item_id' => $itemId, 'variant_id' => $variantId])->one()->delete();

        return json_encode(['result' => 'success']);
    }

}
