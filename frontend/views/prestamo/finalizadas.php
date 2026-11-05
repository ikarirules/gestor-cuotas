<?php

use common\models\Prestamo;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var common\models\Prestamo[] $prestamos */
/** @var DateTimeImmutable $hoy */
/** @var float $totalPagado */

$this->title = 'Cuotas finalizadas';
$this->params['breadcrumbs'][] = ['label' => 'Mis cuotas', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Finalizadas';
?>
<div class="prestamo-finalizadas">

    <h1><?= Html::encode($this->title) ?></h1>
    <p class="text-muted">Cuotas cuya última cuota venció antes de <?= Html::encode(Prestamo::formatearMes($hoy)) ?></p>

    <div class="card">
        <div class="card-body">
            <?php if (!$prestamos): ?>
                <p class="text-muted">Todavía no tenés cuotas finalizadas.</p>
            <?php else: ?>
                <?php foreach ($prestamos as $p):
                    $total = (int) $p->cantidad_cuotas;
                    $fechaInicio = new DateTimeImmutable($p->fecha_primera_cuota);
                ?>
                <div class="cuota-item">
                    <div class="cuota-item-header">
                        <div>
                            <span class="cuota-item-nombre"><?= Html::encode($p->nombre) ?></span>
                            <span class="badge text-bg-success">Finalizada</span>
                            <span class="cuota-item-sub">
                                <?= Html::encode(Prestamo::formatearMes($fechaInicio)) ?>
                                – <?= Html::encode(Prestamo::formatearMes($p->getFechaUltimaCuota())) ?>
                                · <?= Prestamo::formatearMoneda((float) $p->monto_cuota) ?> x <?= $total ?> cuotas
                            </span>
                        </div>
                        <div class="cuota-item-acciones">
                            <?= Html::a('Modificar', ['update', 'id' => $p->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                            <?= Html::a('Eliminar', ['eliminar', 'id' => $p->id], [
                                'class' => 'btn btn-sm btn-outline-danger',
                                'data' => [
                                    'confirm' => '¿Eliminar esta cuota?',
                                    'method' => 'post',
                                ],
                            ]) ?>
                        </div>
                    </div>
                    <div class="cuota-progress">
                        <div class="cuota-progress-fill" style="width: 100%"></div>
                    </div>
                    <div class="cuota-item-info">
                        <span><?= $total ?> / <?= $total ?> cuotas pagadas (100%)</span>
                        <span>Total pagado: <?= Prestamo::formatearMoneda($total * (float) $p->monto_cuota) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="cuota-totales">
                    <span>Cuotas finalizadas: <strong><?= count($prestamos) ?></strong></span>
                    <span>Total pagado: <strong><?= Prestamo::formatearMoneda($totalPagado) ?></strong></span>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
