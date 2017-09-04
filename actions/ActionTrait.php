<?php
namespace sibds\controllers\actions;

use sibds\components\ActiveRecord;
use sibds\controllers\ModelHelper;
use yii\base\Exception;
use yii\web\NotFoundHttpException;

/**
 * Created by PhpStorm.
 * User: vadim
 * Date: 14.02.16
 * Time: 21:27
 */
trait ActionTrait
{
    use ModelHelper;

    /**
     * @var string
     */
    private $_modelName = null;

    /**
     * @var string
     */
    private $_searchModelName = null;

    /**
     * Возвращаем новую модель или пытаемся найти ранее
     * созданную запись, если известен id
     *
     * @return ActiveRecord
     * @throws Exception
     */
    public function getModel()
    {
        if (!is_null($this->controller->model))
            if(is_object($this->controller->model))
                return $this->controller->model;
        
        $nameModel = $this->getModelName();

        if (($id = \Yii::$app->request->get('id')) === null)
            $model = new $nameModel;
        else if (($model = $this->modelFind($nameModel, $id)) === null)
            throw new NotFoundHttpException('The specified record cannot be found.');

        return $model;
    }

    public function testBehavior($b)
    {
        foreach($this->getModel()->behaviors as $behavior)
            if($behavior instanceof $b)
                return $behavior;

        return false;
    }

    /**
     * Возвращает имя модели, с которой работает контроллер
     * По-умолчанию имя модели совпадает с именем контроллера
     */
    public function getModelName()
    {
        if ($this->_modelName === null) {
            if (!is_null($this->controller->model))
                if(is_object($this->controller->model))
                    $this->_modelName = $this->controller->model->className();
                else
                    $this->_modelName = $this->controller->model;
            else {
                $this->_modelName = preg_replace(["/\\\controllers\\\/", "/Controller$/"], ['\\models\\', ''],
                    $this->controller->className(), 1);
            }
        }

        return $this->_modelName;
    }

    public function getSearchModelName()
    {
        if ($this->_searchModelName === null) {
            if (!is_null($this->controller->searchModel))
                $this->_searchModelName = $this->controller->searchModel;
            else {
                $this->_searchModelName = $this->getModelName() . 'Search';
            }
        }

        return $this->_searchModelName;
    }

    public function setModelName($value)
    {
        $this->_modelName = $value;
    }

    public function setSearchModelName($value)
    {
        $this->_searchModelName = $value;
    }
}
