<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 01.02.17
 * Time: 16:57
 */

namespace andrew72ru\user\controllers;

use andrew72ru\user\models\User;
use andrew72ru\user\models\UserSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use Yii;

/**
 * Управление пользователями
 *
 * Class AdminController
 * @package andrew72ru\user\controllers
 *
 * @property \andrew72ru\user\Module $module
 */
class AdminController extends Controller
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'confirm' => ['post'],
                    'resend-password' => ['post'],
                    'block' => ['post'],
                    'switch' => ['post']
                ]
            ],
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => \andrew72ru\user\filters\AccessRule::className()
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['switch'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['admin']
                    ]
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        Url::remember('', 'actions-redirect');
        $searchModelClass = $this->module->modelMap['UserSearch'];
        /** @var UserSearch $searchModel */
        $searchModel = new $searchModelClass;

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    /**
     * @return array|string|Response
     */
    public function actionCreate()
    {
        /** @var User $model */
        $model = new $this->module->modelMap['User'];

        if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()))
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->addFlash('success', Yii::t('user', 'User has been created'));
            return $this->redirect(['update', 'id' => (string) $model->_id]);
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * @param $id
     * @return array|string|Response
     */
    public function actionUpdate($id)
    {
        Url::remember('', 'actions-redirect');
        $model = $this->findModel($id);

        if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()))
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->addFlash('success', \Yii::t('user', 'Account details have been updated'));
            return $this->refresh();
        }

        return $this->render('_account', [
            'model' => $model
        ]);
    }

    /**
     * @param $id
     * @return User
     * @throws NotFoundHttpException
     */
    private function findModel($id)
    {
        /** @var User $class */
        $class = $this->module->modelMap['User'];
        if(($model = $class::findOne($id)) === null)
            throw new NotFoundHttpException(Yii::t('user', 'User not found'));

        return $model;
    }
}