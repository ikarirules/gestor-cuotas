<?php

use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var common\models\Prestamo $model */

$this->title = 'Editar cuota: ' . $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Mis cuotas', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="prestamo-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="card">
        <div class="card-body">
            <?= $this->render('_form', ['model' => $model, 'accion' => 'actualizar', 'textoBoton' => 'Guardar cambios']) ?>
        </div>
    </div>

    <p class="mt-3">
        <?= Html::a('Volver a mis cuotas', ['index'], ['class' => 'btn btn-link']) ?>
    </p>

</div>
