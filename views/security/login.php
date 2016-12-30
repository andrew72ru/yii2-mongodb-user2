<?php
/**
 * Project: yii2-mongodb-user
 * File: login.php
 * User: andrew
 * Date: 30.12.16
 * Time: 15:39
 *
 * @var \yii\web\View $this
 * @var \andrew72ru\user\models\LoginForm $model
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="login-box">
    <div class="login-logo"><?= Yii::t('user', 'Sign In')?></div>
    <div class="login-box-body">
        <p class="login-box-msg"><?= Yii::t('user', 'Sign in to start your session')?></p>
        <?php $form = ActiveForm::begin([
            'id'                     => 'login-form',
            'enableAjaxValidation'   => true,
            'enableClientValidation' => false,
            'validateOnBlur'         => false,
            'validateOnType'         => false,
            'validateOnChange'       => false,
        ]); ?>

        <?= $form->field($model, 'login', [
            'template' => '{input}<span class="glyphicon glyphicon-envelope form-control-feedback"></span>',
            'inputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('login'), 'autofocus' => 'autofocus', 'tabindex' => '1'],
            'options' => ['class' => 'form-group has-feedback']
        ])->textInput()?>

        <?= $form->field($model, 'password', [
            'template' => '{input}<span class="glyphicon glyphicon-lock form-control-feedback"></span>',
            'inputOptions' => ['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('password'), 'tabindex' => '2'],
            'options' => ['class' => 'form-group has-feedback']
        ])->passwordInput()?>

        <div class="checkbox icheck">
            <?= $form->field($model, 'rememberMe')->checkbox([
                'tabindex' => '4',
            ])?>
        </div>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('user', 'Sign In'), [
                'class' => 'btn btn-flat btn-block btn-primary',
                'tabindex' => '3'
            ])?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
