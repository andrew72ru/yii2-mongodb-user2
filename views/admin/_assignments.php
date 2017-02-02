<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 02.02.17
 * Time: 11:15
 *
 * @var \yii\web\View $this
 * @var \andrew72ru\user\models\User $model
 */

use andrew72ru\rbac\widgets\Assignments;
use yii\bootstrap\Alert;

$this->beginContent('@andrew72ru/user/views/admin/update.php', ['model' => $model]);
?>

<?= Alert::widget([
    'options' => ['class' => 'alert-info alert-dismissible'],
    'body' => Yii::t('user', 'You can assign multiple roles or permissions to user by using the form below')
])?>

<?= Assignments::widget(['userId' => $model->_id])?>

<?php $this->endContent() ?>

