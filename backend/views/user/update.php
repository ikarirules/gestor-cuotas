<?php

use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var common\models\User $model */
/** @var string $password */

$this->title = 'Editar usuario: ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'password' => $password,
    ]) ?>

</div>
