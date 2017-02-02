<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 02.02.17
 * Time: 9:53
 *
 * @var \andrew72ru\user\models\User $model
 * @var \yii\web\View $this
 */

use yii\widgets\DetailView;

$this->beginContent('@andrew72ru/user/views/admin/update.php', ['model' => $model]);

?>

<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'attribute' => 'created_at',
            'value' => Yii::$app->formatter->asDatetime($model->updated_at->toDateTime()),
        ],
        [
            'label' => Yii::t('user', 'Block status'),
            'value' => $model->isBlocked
                ? Yii::t('user', 'Blocked at {0, date, MMMM dd, YYYY HH:mm}', [$model->blocked_at->toDateTime()])
                : Yii::t('user', 'Not blocked'),
            'contentOptions' => ['class' => $model->isBlocked ? 'text-danger' : 'text-success']
        ]
    ]
])?>

<?php $this->endContent(); ?>
