<?php
/**
 * Project: yii2-mongodb-user
 * File: SecurityController.php
 * User: andrew
 * Date: 30.12.16
 * Time: 14:39
 */

namespace andrew72ru\user\controllers;


use andrew72ru\user\models\LoginForm;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;

class SecurityController extends Controller
{
    public function actionLogin()
    {
        if(!\Yii::$app->user->isGuest)
            return $this->goHome();

        $loginForm = $this->getModule()->modelMap['LoginForm'];
        /** @var \yii\base\Model|LoginForm $model */
        $model = new $loginForm;

        if(\Yii::$app->request->isAjax && $model->load(\Yii::$app->request->post()))
        {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if($model->load(\Yii::$app->request->post()) && $model->login())
            return $this->goBack();

        return $this->render('login', ['model' => $model]);
    }

    /**
     * @return \andrew72ru\user\Module
     */
    private function getModule()
    {
        return \Yii::$app->getModule('user');
    }
}
