<?php

use common\models\User;
use yii\bootstrap5\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Usuarios';
$this->params['breadcrumbs'][] = $this->title;

$estados = [
    User::STATUS_ACTIVE => 'Activo',
    User::STATUS_INACTIVE => 'Inactivo',
    User::STATUS_DELETED => 'Eliminado',
];
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Html::a('Nuevo usuario', ['create'], ['class' => 'btn btn-success']) ?></p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'username',
            'email:email',
            [
                'attribute' => 'status',
                'value' => function (User $model) use ($estados) {
                    return $estados[$model->status] ?? $model->status;
                },
                'filter' => $estados,
            ],
            [
                'attribute' => 'created_at',
                'value' => function (User $model) {
                    return Yii::$app->formatter->asDatetime($model->created_at);
                },
                'filter' => false,
            ],
            [
                'class' => ActionColumn::class,
            ],
        ],
    ]); ?>
</div>
