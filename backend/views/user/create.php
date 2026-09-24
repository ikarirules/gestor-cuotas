<?php

use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var common\models\User $model */
/** @var string $password */

$this->title = 'Nuevo usuario';
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'password' => $password,
    ]) ?>

</div>
