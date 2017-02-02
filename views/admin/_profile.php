<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 02.02.17
 * Time: 9:21
 *
 * @var \yii\web\View $this
 * @var \andrew72ru\user\models\Profile $model
 * @var \andrew72ru\user\models\User $user
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->beginContent('@andrew72ru/user/views/admin/update.php', ['model' => $user]);

?>

<?php $form = ActiveForm::begin([
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{label}\n<div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
        'labelOptions' => ['class' => 'col-sm-2 control-label']
    ]
])?>

<?= $form->field($model, 'first_name')->textInput()?>

<?= $form->field($model, 'last_name')->textInput()?>

<div class="form-group">
    <div class="col-lg-offset-2 col-lg-10">
        <?= Html::submitButton(Yii::t('user', 'Update'), ['class' => 'btn btn-flat btn-success']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php $this->endContent(); ?>
