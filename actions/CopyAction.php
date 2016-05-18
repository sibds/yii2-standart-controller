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

        return true;
    }
}
