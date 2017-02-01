<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 01.02.17
 * Time: 18:37
 *
 * @var \yii\web\View $this
 * @var \andrew72ru\user\models\User $model
 * @var string $content
 */

$this->title = Yii::t('user', 'Update user account');
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('_menu')?>

<div class="row">
    <div class="col-md-3">
        <div class="panel panel-default">
            <div class="panel-body">
                <?= \yii\bootstrap\Nav::widget([
                    'options' => ['class' => 'nav-pills nav-stacked'],
                    'items' => [
                        [
                            'label' => Yii::t('user', 'Account details'),
                            'url' => ['/user/admin/update', 'id' => (string) $model->_id]
                        ],
                        [
                            'label' => Yii::t('user', 'Profile details'),
                            'url' => ['/user/admin/update-profile', 'id' => (string) $model->_id]
                        ],
                        [
                            'label' => Yii::t('user', 'Information'),
                            'url' => ['/user/admin/info', 'id' => (string) $model->_id]
                        ],
                        [
                            'label' => Yii::t('user', 'Assignments'),
                            'url' => ['/user/admin/assignments', 'id' => (string) $model->_id],
                            'visible' => (Yii::$app->getModule('rbac')->className() == 'andrew72ru\\rbac\\RbacWebModule'),
                        ],
                        '<hr>',
                        [
                            'label' => Yii::t('user', 'Block'),
                            'url' => ['/user/admin/block', 'id' => (string) $model->_id],
                            'visible' => !$model->isBlocked,
                            'linkOptions' => [
                                'class' => 'text-danger',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('user', 'Are you sure you want to block this user?'),
                            ],
                        ],
                        [
                            'label' => Yii::t('user', 'Unblock'),
                            'url' => ['/user/admin/block', 'id' => (string) $model->_id],
                            'visible' => $model->isBlocked,
                            'linkOptions' => [
                                'class' => 'text-success',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('user', 'Are you sure you want to unblock this user?'),
                            ],
                        ],
                        [
                            'label' => Yii::t('user', 'Delete'),
                            'url' => ['/user/admin/delete', 'id' => (string) $model->_id],
                            'linkOptions' => [
                                'class' => 'text-danger',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('user', 'Are you sure you want to delete this user?'),
                            ],
                        ],
                    ]
                ])?>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-body">
                <?= $content ?>
            </div>
        </div>
    </div>
</div>
