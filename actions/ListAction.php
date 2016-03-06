<?php
/**
 * Created by PhpStorm.
 * User: vadim
 * Date: 14.02.16
 * Time: 16:30
 */

namespace sibds\controllers\actions;


class ListAction extends BaseAction
{

    public function run()
    {
        $searchModel = $this->getSearchModelName();
        $searchModel = new $searchModel;
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render([
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

}