<?php
/**
 * Project: yii2-mongodb-user
 * File: SettingsController.php
 * User: andrew
 * Date: 09.01.17
 * Time: 12:38
 */

namespace andrew72ru\user\controllers;

use andrew72ru\user\models\User;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Контроллер для установки свойств пользователя самим пользователем
 *
 * Class SettingsController
 * @package andrew72ru\user\controllers
 */
class SettingsController extends Controller
{
    /** @inheritdoc */
    public $defaultAction = 'profile';

    /**
     * @inheritdoc
     * @return array
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post']
                ]
            ]
        ];
    }

    /**
     * Редактирование профиля пользователя
     *
     * @return array|string|Response
     * @throws NotFoundHttpException
     */
    public function actionProfile()
    {
        $user = $this->findUser();
        $model = $user->getProfile();

        if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()))
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if($model->load(Yii::$app->request->post()) && $model->validate())
        {
            if($user->avatarModel->load(Yii::$app->request->post()))
                $user->avatarModel->upload();

            if($user->profile_data == $model->attributes)
                return $this->refresh();

            if(!$user->updateAttributes(['profile_data' => $model->attributes]))
                Yii::$app->session->addFlash('error', Html::errorSummary([$user, $model], ['header' => null]));
            else
                return $this->refresh();
        }

        return $this->render('profile', [
            'model' => $model
        ]);
    }

    /**
     * @return \yii\web\IdentityInterface|User
     * @throws NotFoundHttpException
     */
    private function findUser()
    {
        $user = Yii::$app->user->identity;
        if($user === null)
            throw new NotFoundHttpException(Yii::t('user', 'User not found'));
        return $user;
    }
}
