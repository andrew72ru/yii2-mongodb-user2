<?php
/**
 * Project: yii2-mongodb-user
 * File: ModuleTrait.php
 * User: andrew
 * Date: 09.01.17
 * Time: 13:42
 */

namespace andrew72ru\user\traits;


trait ModuleTrait
{
    /**
     * @return \andrew72ru\user\Module|\yii\base\Module
     */
    public function getModule()
    {
        return \Yii::$app->getModule('user');
    }
}
