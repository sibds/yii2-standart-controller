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

        return true;
    }
}
