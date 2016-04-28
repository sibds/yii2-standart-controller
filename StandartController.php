<?php
/**
 * Created by PhpStorm.
 * User: vadim
 * Date: 14.02.16
 * Time: 14:36
 */

namespace sibds\controllers;


use creocoder\nestedsets\NestedSetsBehavior;
use sibds\components\ActiveRecord;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class StandartController extends Controller
{
    use ModelHelper;

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
                        'actions' => ['list', 'trash', 'copy', 'update', 'unlock', 'lock', 'delete', 'nodeMove', 'galleryApi'],
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
    
    /**
     * Creates an action based on the given action ID.
     * The method first checks if the action ID has been declared in [[actions()]]. If so,
     * it will use the configuration declared there to create the action object.
     * If not, it will look for a controller method whose name is in the format of `actionXyz`
     * where `Xyz` stands for the action ID. If found, an [[InlineAction]] representing that
     * method will be created and returned.
     * @param string $id the action ID.
     * @return Action the newly created action instance. Null if the ID doesn't resolve into any action.
     */
    public function createAction($id)
    {
        if ($id === '') {
            $id = $this->defaultAction;
        }
        $actionMap = $this->actions();
        if (preg_match('/^[a-z0-9\\-_]+$/', $id) && strpos($id, '--') === false && trim($id, '-') === $id) {
            $methodName = 'action' . str_replace(' ', '', ucwords(implode(' ', explode('-', $id))));
            if (method_exists($this, $methodName)) {
                $method = new \ReflectionMethod($this, $methodName);
                if ($method->isPublic() && $method->getName() === $methodName) {
                    return new InlineAction($id, $this, $methodName);
                }
            }
        } elseif (isset($actionMap[$id])) {
            return Yii::createObject($actionMap[$id], [$id, $this]);
        } 
        return null;
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
        
        if($this->testBehavior(new GalleryBehavior())){
            $actions['galleryApi'] = [
                'class' => 'zxbodya\yii2\galleryManager\GalleryManagerAction',
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
            $nameModel = $this->getModelName();
        }else
            return $this->model;

        if (($id = \Yii::$app->request->get('id')) === null)
            $model = new $nameModel;
        else if (($model = $this->modelFind($nameModel, $id)) === null)
            throw new NotFoundHttpException('The specified record cannot be found.');

        $this->model = $model;

        return $model;
    }

    public function getModelName()
    {
        return str_replace(['\\controllers\\', 'Controller'], ['\\models\\', ''],
            $this->className());
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
