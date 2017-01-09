<?php
/**
 * Project: yii2-mongodb-user
 * File: _menu.php
 * User: andrew
 * Date: 09.01.17
 * Time: 13:02
 *
 * @var \yii\web\View $this
 */

/** @var \andrew72ru\user\models\User $user */
$user = Yii::$app->user->identity;
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <strong class="panel-title b">
            <?= \yii\helpers\Html::img($user->getAvatar(24), [
                'class' => 'img-circle',
                'alt' => $user->fullName
            ])?>
            <?= $user->fullName?>
        </strong>
    </div>
    <div class="panel-body">
        <?= \yii\widgets\Menu::widget([
            'options' => [
                'class' => 'nav nav-pills nav-stacked',
            ],
            'items' => [
                [
                    'label' => Yii::t('user', 'Profile'),
                    'url' => ['/user/settings/profile'],
                ],
                [
                    'label' => Yii::t('user', 'Account'),
                    'url' => ['/user/settings/account']
                ]
            ]
        ])?>
    </div>
</div>
