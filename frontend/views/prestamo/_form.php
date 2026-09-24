<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var common\models\Prestamo $model */
/** @var string $accion 'crear' o 'actualizar' */
/** @var string $textoBoton */

$accion = $accion ?? 'crear';
$textoBoton = $textoBoton ?? 'Agregar';
?>
<div class="prestamo-form">

    <?php $form = ActiveForm::begin(); ?>

    <input type="hidden" name="accion" value="<?= Html::encode($accion) ?>">

    <div class="cuota-form-grid">
        <?= $form->field($model, 'nombre')->textInput(['maxlength' => true, 'placeholder' => 'Ej: Moto Honda CB1']) ?>
        <?= $form->field($model, 'fecha_primera_cuota')->input('date') ?>
        <?= $form->field($model, 'monto_cuota')->textInput(['type' => 'number', 'step' => '0.01', 'min' => '0.01']) ?>
        <?= $form->field($model, 'cantidad_cuotas')->textInput(['type' => 'number', 'step' => '1', 'min' => '1']) ?>

        <div class="cuota-form-acciones">
            <?= Html::submitButton($textoBoton, ['class' => $accion === 'crear' ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
