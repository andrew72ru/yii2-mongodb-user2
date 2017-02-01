<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 01.02.17
 * Time: 17:45
 */

echo \yii\bootstrap\Nav::widget([
    'options' => [
        'class' => 'nav-tabs'
    ],
    'items' => [
        [
            'label' => Yii::t('user', 'Users'),
            'url' => ['/user/admin'],
        ],
        [
            'label' => Yii::t('user', 'Roles'),
            'url' => ['/rbac/role'],
            'visible' => (Yii::$app->getModule('rbac')->className() == 'andrew72ru\\rbac\\RbacWebModule'),
        ],
        [
            'label' => Yii::t('user', 'Permissions'),
            'url' => ['/rbac/permission/index'],
            'visible' => (Yii::$app->getModule('rbac')->className() == 'andrew72ru\\rbac\\RbacWebModule'),
        ],
        [
            'label' => \Yii::t('user', 'Rules'),
            'url'   => ['/rbac/rule/index'],
            'visible' => (Yii::$app->getModule('rbac')->className() == 'andrew72ru\\rbac\\RbacWebModule'),
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