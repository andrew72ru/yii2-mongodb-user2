<?php
/**
 * Project: yii2-mongodb-user
 * File: profile.php
 * User: andrew
 * Date: 09.01.17
 * Time: 12:56
 *
 * @var \yii\web\View $this
 * @var \andrew72ru\user\models\Profile $model
 * @var \andrew72ru\user\models\User $user
 */

use andrew72ru\flowjs\widgets\UploadImageWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('user', 'Profile settings');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-xs-12 col-sm-3">
        <?= $this->render('_menu')?>
    </div>
    <div class="col-xs-12 col-sm-9">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong class="panel-title"><?= Html::encode($this->title) ?></strong>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'user-profile-form',
                    'enableAjaxValidation' => true,
                    'options' => ['class' => 'form-horizontal'],
                    'fieldConfig' => [
                        'template' => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
                        'labelOptions' => ['class' => 'col-lg-3 control-label'],
                    ],
                ])?>

                <?= $form->field($model, 'first_name')->textInput()?>

                <?= $form->field($model, 'last_name')->textInput()?>

                <?= $form->field($user->getAvatarModel(), 'uploadedFile')->widget(UploadImageWidget::className(), [
                    'preview' => $user->getAvatar(300),
                    'callbackClass' => $user->getAvatarModel()->className(),
                    'callbackMethod' => 'setFromFile'
                ])->label(Yii::t('user', 'Avatar'))?>

                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                        <?= Html::submitButton(Yii::t('user', 'Save'), [
                            'class' => 'btn btn-flat btn-primary'
                        ])?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
