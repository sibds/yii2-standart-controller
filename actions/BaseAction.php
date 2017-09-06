<?php

namespace sibds\controllers\actions;


/**
 * Created by PhpStorm.
 * User: vadim
 * Date: 14.02.16
 * Time: 14:43
 */
class BaseAction extends \yii\base\Action
{
    use ActionTrait;
    /**
     * @var string
     */
    private $_view;

    /**
     * Упрощенная переадресация по действиям контроллера
     * По-умолчанию переходим на основное действие контроллера
     */
    public function redirect($actionId = null)
    {
        if (is_null($actionId)) {
            if ($this->controller->defaultAction === null) {
                $this->controller->redirect(\Yii::$app->user->returnUrl);
            } else {
                $actionId = $this->controller->defaultAction;
            }
        }

        if (is_array($actionId)) {
            $this->controller->redirect($actionId);
        } else {
            $this->controller->redirect(array($actionId));
        }
    }

    /**
     * Рендер представление.
     * По-умолчанию рендерим одноименное представление
     */
    public function render($data)
    {
        if ($this->_view === null)
            $this->_view = $this->id;
        
        if (\Yii::$app->request->isAjax) {
            return $this->controller->renderPartial($this->_view, $data);
        }

        return $this->controller->render($this->_view, $data);
    }


    public function setView($value)
    {
        $this->_view = $value;
    }
}
