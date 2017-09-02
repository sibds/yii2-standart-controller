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
            if($model->hasMethod('files')){
                $itemFiles = $model->files();
                foreach ($itemFiles as $key => $value){
                    $file = UploadedFile::getInstance($model, $key);

                    if(!empty($file)){
                        $oldName = $model->$value;

                        // store the source file name
                        $model->$value = $file->name;
                        $a = explode(".", $file->name);
                        $ext = end($a);

                        // generate a unique file name
                        $model->$value = \Yii::$app->security->generateRandomString().".{$ext}";

                        // the path to save file, you can set an uploadPath
                        // in Yii::$app->params (as used in example below)
                        $path = \Yii::$app->params['uploadPath']() . $model->$value;

                        if($oldName!=''){
                            unlink(\Yii::$app->params['uploadPath']() . $oldName);
                        }
                        $file->saveAs($path);
                    }
                }
            }
            if (($behavior = $this->testBehavior(new NestedSetsBehavior())) !== false && $model->isNewRecord) {
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
                
            \Yii::$app->session->setFlash('update-success', 'Record saved!', false);    

            return $this->redirect([$this->id, 'id' => $model->id]);
        } else {
            return $this->render([
                'model' => $model,
            ]);
        }
    }
}
