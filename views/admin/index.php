<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 01.02.17
 * Time: 17:15
 *
 * @var \yii\web\View $this
 * @var \andrew72ru\user\models\UserSearch $searchModel
 * @var \yii\data\ActiveDataProvider $dataProvider
 */

use andrew72ru\user\models\User;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = Yii::t('user', 'Manage users');
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('/admin/_menu')?>

<?php Pjax::begin(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'layout' => "{items}\n{pager}",
    'columns' => [
        'username',
        'email:email',
        [
            'attribute' => 'created_at',
            'value' => function(User $model)
            {
                return Yii::$app->formatter->asDatetime($model->created_at->toDateTime());
            },
        ],
        [
            'header' => Yii::t('user', 'Block status'),
            'value' => function(User $model)
            {
                if($model->isBlocked)
                {
                    return Html::a(Yii::t('user', 'Unblock'), ['block', 'id' => (string) $model->_id], [
                        'class' => 'btn btn-xs btn-success',
                        'data' => [
                            'method' => 'post',
                            'confirm' => Yii::t('user', 'Are you sure you want to unblock this user?'),
                        ]
                    ]);
                }

                return Html::a(Yii::t('user', 'Block'), ['block', 'id' => $model->id], [
                    'class' => 'btn btn-xs btn-danger',
                    'data-method' => 'post',
                    'data-confirm' => Yii::t('user', 'Are you sure you want to block this user?'),
                ]);
            },
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
        ],
        [
            'class' => \yii\grid\ActionColumn::className(),
            'contentOptions' => ['class' => 'text-right text-nowrap', 'style' => 'width: 15px'],
            'buttonOptions' => ['class' => 'btn btn-xs btn-flat', 'data-toggle' => 'tooltip'],
            'template' => '{switch} {resend_password} {update} {delete}',
            'buttons' => [
                'resend_password' => function($url, User $model)
                {
                    if(!$model->isAdmin)
                    {
                        return Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-envelope']), ['resend-password', 'id' => (string) $model->_id], [
                            'title' => Yii::t('user', 'Generate and send new password to user'),
                            'class' => 'btn btn-xs btn-flat',
                            'data' => [
                                'method' => 'post',
                                'confirm' => Yii::t('user', 'Are you sure?'),
                                'toggle' => 'tooltip'
                            ]
                        ]);
                    }
                    return null;
                },
                'switch' => function($url, User $model)
                {
                    if((string) $model->_id !== Yii::$app->user->id && Yii::$app->getModule('user')->enableImpersonateUser)
                    {
                        return Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-user']), ['switch', 'id' => (string) $model->_id], [
                            'title' => Yii::t('user', 'Become this user'),
                            'class' => 'btn btn-xs btn-flat',
                            'data' => [
                                'confirm' => Yii::t('user', 'Are you sure you want to switch to this user for the rest of this Session?'),
                                'method' => 'post',
                                'toggle' => 'tooltip'
                            ]
                        ]);
                    }

                    return null;
                }
            ]
        ]
    ]
])?>

<?php Pjax::end(); ?>
