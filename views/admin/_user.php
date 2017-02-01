<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 01.02.17
 * Time: 18:22
 *
 * @var \andrew72ru\user\models\User $model
 * @var \yii\widgets\ActiveForm $form
 */

?>

<?= $form->field($model, 'email')->textInput(['maxlength' => 255])?>

<?= $form->field($model, 'username')->textInput(['maxlength' => 255])?>

<?= $form->field($model, 'password')->passwordInput()?>