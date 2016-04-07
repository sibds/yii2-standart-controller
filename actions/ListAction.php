<?php
/**
 * Created by PhpStorm.
 * User: vadim
 * Date: 14.02.16
 * Time: 16:30
 */

namespace sibds\controllers\actions;


use yii\helpers\Url;

class ListAction extends BaseAction
{

    public function run()
    {
        $searchModel = $this->getSearchModelName();
        $searchModel = new $searchModel;
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        \Yii::$app->user->returnUrl = Url::current();

        return $this->render([
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

}
