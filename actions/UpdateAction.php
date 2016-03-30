<?php

namespace sibds\controllers\actions;

use creocoder\nestedsets\NestedSetsBehavior;

/**
 * Created by PhpStorm.
 * User: vadim
 * Date: 14.02.16
 * Time: 14:41
 */
class UpdateAction extends BaseAction
{
    public function run()
    {
        $model = $this->getModel();

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            if (($behavior = $this->testBehavior(new NestedSetsBehavior())) !== false) {
                if ($model->hasAttribute($behavior->treeAttribute)) {
                    $modelName = $this->getModelName();

                    if (($parent = \Yii::$app->request->get('parent')) === null) {
                        $model->makeRoot();
                    } else if (($root = $modelName::findOne(['id' => $parent])) === null)
                        throw new NotFoundHttpException('The specified record cannot be found.');

                    if ($model->isNewRecord) {
                        $model->appendTo($root);
                    }
                } else {
                    $modelName = $this->getModelName();
                    if ($modelName::find()->roots()->count() <= 0) {
                        $root = new $modelName(['name' => 'Основная']);
                        $root->makeRoot();
                    }
                    if (($parent = \Yii::$app->request->get('parent')) === null)
                        $root = $modelName::find()->roots()->one();
                    else if (($root = $modelName::findOne(['id' => $parent])) === null)
                        throw new NotFoundHttpException('The specified record cannot be found.');

                    $root = $modelName::find()->roots()->one();
                    $model->appendTo($root);
                }
            } else
                $model->save();

            if (isset($_GET['close']))
                return $this->redirect();

            return $this->redirect([$this->id, 'id' => $model->id]);
        } else {
            return $this->render([
                'model' => $model,
            ]);
        }
    }
}
