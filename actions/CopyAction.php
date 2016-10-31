<?php
/**
 * Created by PhpStorm.
 * User: vadim
 * Date: 14.02.16
 * Time: 16:58
 */

namespace sibds\controllers\actions;


class CopyAction extends BaseAction
{
    public function run()
    {
        $this->getModel()->duplicate();

        if(!\Yii::$app->request->isAjax && (\Yii::$app->request->isGet || \Yii::$app->request->isPost))
            return $this->redirect();

        if(\Yii::$app->request->isAjax&&!\Yii::$app->request->isPjax)
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if(\Yii::$app->request->isPjax)
            return $this->controller->redirect(\Yii::$app->request->referrer);

        return true;
    }
}
