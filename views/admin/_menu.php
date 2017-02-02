<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 01.02.17
 * Time: 17:45
 *
 * @var \yii\web\View $this
 */
use yii\bootstrap\Nav;

/** @var \yii\base\ViewContextInterface|\yii\web\Controller $context */
$context = $this->context;

echo Nav::widget([
    'options' => [
        'class' => 'nav-tabs',
        'style' => ['margin-bottom' => '1em']
    ],
    'items' => [
        [
            'label' => Yii::t('user', 'Users'),
            'url' => ['/user/admin'],
            'active' => ($context->id === 'admin' && $context->action->id === 'index'),
        ],
        [
            'label' => Yii::t('user', 'Roles'),
            'url' => ['/rbac/role'],
            'visible' => (Yii::$app->getModule('rbac')->className() == 'andrew72ru\\rbac\\RbacWebModule'),
            'active' => ($context->id === 'role' && $context->action->id === 'index'),
        ],
        [
            'label' => Yii::t('user', 'Permissions'),
            'url' => ['/rbac/permission/index'],
            'visible' => (Yii::$app->getModule('rbac')->className() == 'andrew72ru\\rbac\\RbacWebModule'),
            'active' => ($context->id === 'permission' && $context->action->id === 'index'),
        ],
        [
            'label' => \Yii::t('user', 'Rules'),
            'url'   => ['/rbac/rule/index'],
            'visible' => (Yii::$app->getModule('rbac')->className() == 'andrew72ru\\rbac\\RbacWebModule'),
            'active' => ($context->id === 'rule' && $context->action->id === 'index'),
        ],
        [
            'label' => Yii::t('user', 'Create'),
            'items' => [
                [
                    'label' => Yii::t('user', 'New user'),
                    'url' => ['/user/admin/create'],
                ],
                [
                    'label' => Yii::t('user', 'New role'),
                    'url' => ['/rbac/role/create'],
                    'visible' => (Yii::$app->getModule('rbac')->className() == 'andrew72ru\\rbac\\RbacWebModule'),
                ],
                [
                    'label' => Yii::t('user', 'New permission'),
                    'url' => ['/rbac/permission/create'],
                    'visible' => (Yii::$app->getModule('rbac')->className() == 'andrew72ru\\rbac\\RbacWebModule'),
                ],
                [
                    'label' => \Yii::t('user', 'New rule'),
                    'url'   => ['/rbac/rule/create'],
                    'visible' => (Yii::$app->getModule('rbac')->className() == 'andrew72ru\\rbac\\RbacWebModule'),
                ]
            ],
        ],
    ]
]);