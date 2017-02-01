<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 01.02.17
 * Time: 18:46
 *
 * @var \yii\web\View $this
 * @var \andrew72ru\user\models\User $model
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<?php $this->beginContent('@andrew72ru/user/views/admin/update.php', ['model' => $model]); ?>

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
        <?= Html::submitButton(Yii::t('user', 'Update'), ['class' => 'btn btn-flat btn-success']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php $this->endContent(); ?>


