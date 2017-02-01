<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 01.02.17
 * Time: 16:57
 */

namespace andrew72ru\user\controllers;

use andrew72ru\user\models\UserSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
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
}