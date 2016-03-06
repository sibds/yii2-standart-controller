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
        if ($actionId === null)
            $actionId = $this->controller->defaultAction;

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

        return $this->controller->render($this->_view, $data);
    }


    public function setView($value)
    {
        $this->_view = $value;
    }
}