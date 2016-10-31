<?php
/**
 * Created by PhpStorm.
 * User: vadim
 * Date: 14.02.16
 * Time: 17:03
 */

namespace sibds\controllers\actions;


class LockAction extends BaseAction
{
    public function run()
    {
        $this->getModel()->lock();

        if(!\Yii::$app->request->isAjax && (\Yii::$app->request->isGet || \Yii::$app->request->isPost))
            return $this->redirect();

        if(\Yii::$app->request->isAjax&&!\Yii::$app->request->isPjax)
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if(\Yii::$app->request->isPjax)
            return $this->controller->redirect(\Yii::$app->request->referrer);

        return true;
    }
}
