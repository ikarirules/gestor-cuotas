<?php

use common\models\Prestamo;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var common\models\Prestamo $model */
/** @var common\models\Prestamo[] $prestamos */
/** @var DateTimeImmutable $hoy */
/** @var array $calendario */
/** @var int $mesesAtras */
/** @var int $mesesAdelante */
/** @var float $totalPagado */
/** @var float $totalRestante */

$this->title = 'Mis cuotas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prestamo-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p class="text-muted">Hoy es <?= Html::encode(Prestamo::formatearMes($hoy)) ?> (día <?= $hoy->format('d') ?>)</p>

    <div class="card">
        <div class="card-body">
            <h2 class="h5 card-title">Agregar cuota</h2>
            <?= $this->render('_form', ['model' => $model, 'accion' => 'crear', 'textoBoton' => 'Agregar']) ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h2 class="h5 card-title">Cuotas cargadas</h2>

            <?php if (!$prestamos): ?>
                <p class="text-muted">Todavía no cargaste ninguna cuota.</p>
            <?php else: ?>
                <?php foreach ($prestamos as $p):
                    $pagadas = $p->contarCuotasPagadas($hoy);
                    $total = (int) $p->cantidad_cuotas;
                    $restantes = max(0, $total - $pagadas);
                    $porcentaje = $total > 0 ? min(100, (int) round($pagadas / $total * 100)) : 0;
                    $montoTotal = $total * (float) $p->monto_cuota;
                    $fechaInicio = new DateTimeImmutable($p->fecha_primera_cuota);
                ?>
                <div class="cuota-item">
                    <div class="cuota-item-header">
                        <div>
                            <span class="cuota-item-nombre"><?= Html::encode($p->nombre) ?></span>
                            <span class="cuota-item-sub">
                                desde <?= Html::encode(Prestamo::formatearMes($fechaInicio)) ?>
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
                        <div class="cuota-progress-fill" style="width: <?= $porcentaje ?>%"></div>
                    </div>
                    <div class="cuota-item-info">
                        <span><?= $pagadas ?> / <?= $total ?> cuotas pagadas (<?= $porcentaje ?>%)</span>
                        <span>Pagado: <?= Prestamo::formatearMoneda($pagadas * (float) $p->monto_cuota) ?></span>
                        <span>Restante: <?= Prestamo::formatearMoneda($restantes * (float) $p->monto_cuota) ?></span>
                        <span>Total: <?= Prestamo::formatearMoneda($montoTotal) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="cuota-totales">
                    <span>Total pagado hasta hoy: <strong><?= Prestamo::formatearMoneda($totalPagado) ?></strong></span>
                    <span>Total restante: <strong><?= Prestamo::formatearMoneda($totalRestante) ?></strong></span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h2 class="h5 card-title">Calendario de pagos por mes</h2>
            <p class="text-muted small">Últimos <?= $mesesAtras ?> meses y próximos <?= $mesesAdelante ?> meses</p>

            <div class="cuota-calendario-scroll">
                <table class="cuota-calendario">
                    <thead>
                        <tr>
                            <th>Mes</th>
                            <th>Detalle</th>
                            <th>Total del mes</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($calendario as $mes): ?>
                            <tr class="<?= $mes['es_actual'] ? 'mes-actual' : '' ?>">
                                <td><?= Html::encode(Prestamo::formatearMes($mes['fecha'])) ?></td>
                                <td>
                                    <?php if (!$mes['items']): ?>
                                        <span class="detalle-item">—</span>
                                    <?php else: ?>
                                        <?php foreach ($mes['items'] as $item): ?>
                                            <div class="detalle-item">
                                                <?= Html::encode($item['nombre']) ?>
                                                (cuota <?= $item['numero'] ?>/<?= $item['cantidad_cuotas'] ?>)
                                                — <?= Prestamo::formatearMoneda($item['monto']) ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="total-mes"><?= Prestamo::formatearMoneda($mes['total']) ?></td>
                                <td>
                                    <?php if ($mes['es_actual']): ?>
                                        <span class="badge text-bg-primary">Mes actual</span>
                                    <?php elseif ($mes['es_pasado']): ?>
                                        <span class="badge text-bg-success">Pagado</span>
                                    <?php else: ?>
                                        <span class="badge text-bg-warning">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
