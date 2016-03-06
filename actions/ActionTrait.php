<?php
namespace sibds\controllers\actions;

use sibds\components\ActiveRecord;
use yii\base\Exception;

/**
 * Created by PhpStorm.
 * User: vadim
 * Date: 14.02.16
 * Time: 21:27
 */
trait ActionTrait
{
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
        $nameModel = $this->getModelName();

        if (($id = \Yii::$app->request->get('id')) === null)
            $model = new $nameModel;
        else if (($model = $nameModel::findOne($id)) === null)
            throw new NotFoundHttpException('The specified record cannot be found.');

        return $model;
    }

    public function testBehavior($b)
    {
        foreach($this->getModel()->behaviors as $behavior)
            if($behavior instanceof $b)
                return true;

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
                $this->_modelName = $this->controller->model;
            else {

                $this->_modelName = str_replace(['\\controllers\\', 'Controller'], ['\\models\\', ''],
                    $this->controller->className());
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