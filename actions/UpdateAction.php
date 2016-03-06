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
            if(($behavior = $this->testBehavior(new NestedSetsBehavior())) !== false){
                if($model->hasAttribute($behavior->treeAttribute)){
                    $model->makeRoot();
                }else{
                    $modelName = $this->getModelName;
                    if($modelName::find()->roots()->count()<=0){
                        $root=new $modelName(['name'=>'Основная']);
                        $root->makeRoot();
                    }
                    $root = $modelName::find()->roots()->one();
                    $model->appendTo($root);
                }
            }
            else
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