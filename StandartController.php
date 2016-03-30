<?php
/**
 * Created by PhpStorm.
 * User: vadim
 * Date: 14.02.16
 * Time: 14:36
 */

namespace sibds\controllers;


use creocoder\nestedsets\NestedSetsBehavior;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class StandartController extends Controller
{
    public $model = null;
    public $searchModel = null;

    public $defaultAction = 'list';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['list', 'trash', 'copy', 'update', 'unlock', 'lock', 'delete', 'nodeMove'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actions()
    {
        $actions = [
            'list' => ['class' => 'sibds\controllers\actions\ListAction'],
            'trash' => ['class' => 'sibds\controllers\actions\TrashAction'],
            'copy' => ['class' => 'sibds\controllers\actions\CopyAction'],
            'update' => ['class' => 'sibds\controllers\actions\UpdateAction'],
            'unlock' => ['class' => 'sibds\controllers\actions\UnlockAction'],
            'lock' => ['class' => 'sibds\controllers\actions\LockAction'],
            'delete' => ['class' => 'sibds\controllers\actions\DeleteAction'],
        ];

        if($this->testBehavior(new NestedSetsBehavior())){
            $actions['nodeMove'] = [
                'class' => 'sibds\controllers\actions\NodeMoveAction',
            ];
        }

        return ArrayHelper::merge($actions, parent::actions()); // TODO: Change the autogenerated stub
    }

    public function getLayouts()
    {
        $layouts = scandir(\Yii::getAlias('@app/views/layouts'));

        if (file_exists(\Yii::getAlias('@app/modules/' . $this->module->id . '/views/layouts'))) {
            $layoutsModule = scandir(\Yii::getAlias('@app/modules/' . $this->module->id . '/views/layouts'));
            $layouts = ArrayHelper::merge($layoutsModule, $layouts);
        }

        foreach ($layouts as $key => $value) {
            if (in_array($value, ['.', '..']))
                unset($layouts[$key]);
            else {
                if ($key == '')
                    continue;
                $layouts[$value] = $value;
                unset($layouts[$key]);
            }
        }

        return $layouts;
    }

    public function getModel()
    {
        if (is_null($this->model)) {
            $nameModel = str_replace(['\\controllers\\', 'Controller'], ['\\models\\', ''],
                $this->className());
        }else
            return $this->model;

        if (($id = \Yii::$app->request->get('id')) === null)
            $model = new $nameModel;
        else if (($model = $nameModel::findOne($id)) === null)
            throw new NotFoundHttpException('The specified record cannot be found.');

        $this->model = $model;

        return $model;
    }

    public function testBehavior($b)
    {
        if(is_null($this->model))
            $this->getModel();

        foreach($this->model->behaviors as $behavior)
            if($behavior instanceof $b)
                return true;

        return false;
    }
}
