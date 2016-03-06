<?php
/**
 * Created by PhpStorm.
 * User: vadim
 * Date: 07.03.16
 * Time: 0:55
 */

namespace sibds\controllers\actions;


class TrashAction extends BaseAction
{
    public function run()
    {
        //set view
        $this->setView('list');

        $searchModel = $this->getSearchModelName();
        $searchModel = new $searchModel;
        $dataProvider = $searchModel->trashSearch(\Yii::$app->request->queryParams);

        return $this->render([
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}