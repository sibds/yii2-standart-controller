<?php
/**
 * Created by PhpStorm.
 * User: vadim
 * Date: 02.04.16
 * Time: 20:05
 */

namespace sibds\controllers;


use sibds\components\ActiveRecord;

trait ModelHelper
{
    private function modelFind(ActiveRecord $nameModel, $id){
        $obj = $nameModel::find();
        $primareKey = $nameModel::primaryKey()[0];
        return $obj->hasAttribute($obj->removedAttribute)?
            $obj->withRemoved()->andWhere([$primareKey => $id])->one():
            $nameModel::findOne($id);
    }
}
