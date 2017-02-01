<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 01.02.17
 * Time: 18:10
 *
 * @var \yii\web\View $this
 * @var \andrew72ru\user\models\User $model
 */

use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('user', 'Create a user account');
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('_menu')?>

<div class="row">
    <div class="col-md-3">
        <div class="panel panel-default">
            <div class="panel-body">
                <?= Nav::widget([
                    'options' => ['class' => 'nav-pills nav-stacked'],
                    'items' => [
                        ['label' => Yii::t('user', 'Account details'), 'url' => ['/user/admin/create']],
                        ['label' => Yii::t('user', 'Profile details'), 'options' => [
                            'class' => 'disabled',
                            'onclick' => 'return false;',
                        ]],
                        ['label' => Yii::t('user', 'Information'), 'options' => [
                            'class' => 'disabled',
                            'onclick' => 'return false;',
                        ]],
                    ]
                ])?>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="alert alert-info">
                    <?= Yii::t('user', 'Credentials will be sent to the user by email') ?>.
                    <?= Yii::t('user', 'A password will be generated automatically if not provided') ?>.
                </div>
                <?php $form = ActiveForm::begin([
                    'options' => ['class' => 'form-horizontal'],
                    'fieldConfig' => [
                        'template' => "{label}\n<div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
                        'labelOptions' => ['class' => 'col-sm-2 control-label']
                    ]
                ])?>

                <?= $this->render('_user', ['model' => $model, 'form' => $form])?>

                <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-10">
                        <?= Html::submitButton(Yii::t('user', 'Save'), [
                            'class' => 'btn btn-flat btn-success'
                        ])?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>