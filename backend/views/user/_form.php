<?php

use common\models\User;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var common\models\User $model */
/** @var yii\widgets\ActiveForm $form */
/** @var string $password */

$esNuevo = $model->isNewRecord;
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'autofocus' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput(['value' => $password])->hint(
        $esNuevo ? 'Mínimo 6 caracteres.' : 'Dejar en blanco para no cambiar la contraseña actual.'
    ) ?>

    <?= $form->field($model, 'status')->dropDownList([
        User::STATUS_ACTIVE => 'Activo',
        User::STATUS_INACTIVE => 'Inactivo',
        User::STATUS_DELETED => 'Eliminado',
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton($esNuevo ? 'Crear' : 'Guardar', ['class' => $esNuevo ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
