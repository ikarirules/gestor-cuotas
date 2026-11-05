<?php

namespace frontend\controllers;

use common\models\Prestamo;
use DateTimeImmutable;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Gestiona las cuotas del usuario logueado. Cada usuario ve y modifica
 * únicamente sus propios préstamos/cuotas.
 */
class PrestamoController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'eliminar' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Dashboard principal: alta de cuota, listado de las propias y calendario mensual.
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        $model = new Prestamo();
        $model->fecha_primera_cuota = date('Y-m-d');

        if ($this->request->isPost && $this->request->post('accion') === 'crear') {
            $model->load($this->request->post());
            $model->user_id = $userId;
            if ($model->validate() && $model->save(false)) {
                Yii::$app->session->setFlash('success', 'Cuota agregada correctamente.');
                return $this->redirect(['index']);
            }
        }

        $prestamos = Prestamo::find()
            ->where(['user_id' => $userId])
            ->orderBy(['fecha_primera_cuota' => SORT_DESC, 'id' => SORT_DESC])
            ->all();

        $hoy = new DateTimeImmutable('today');
        $mesesAtras = 6;
        $mesesAdelante = 12;
        // El calendario usa todas las cuotas para que los meses pasados muestren lo que se pagó.
        $calendario = Prestamo::calcularCalendarioMensual($prestamos, $hoy, $mesesAtras, $mesesAdelante);

        $activos = array_values(array_filter($prestamos, fn(Prestamo $p) => !$p->estaFinalizada($hoy)));
        $cantidadFinalizadas = count($prestamos) - count($activos);

        $totalPagado = 0.0;
        $totalRestante = 0.0;
        foreach ($activos as $p) {
            $pagadas = $p->contarCuotasPagadas($hoy);
            $totalPagado += $pagadas * (float) $p->monto_cuota;
            $totalRestante += (max(0, (int) $p->cantidad_cuotas - $pagadas)) * (float) $p->monto_cuota;
        }

        return $this->render('index', [
            'model' => $model,
            'prestamos' => $activos,
            'cantidadFinalizadas' => $cantidadFinalizadas,
            'hoy' => $hoy,
            'calendario' => $calendario,
            'mesesAtras' => $mesesAtras,
            'mesesAdelante' => $mesesAdelante,
            'totalPagado' => $totalPagado,
            'totalRestante' => $totalRestante,
        ]);
    }

    /**
     * Listado de las cuotas cuya última cuota venció en un mes anterior al actual.
     */
    public function actionFinalizadas()
    {
        $hoy = new DateTimeImmutable('today');

        $prestamos = array_filter(
            Prestamo::find()->where(['user_id' => Yii::$app->user->id])->all(),
            fn(Prestamo $p) => $p->estaFinalizada($hoy)
        );

        // Las que terminaron más recientemente primero.
        usort($prestamos, fn(Prestamo $a, Prestamo $b) => $b->getFechaUltimaCuota() <=> $a->getFechaUltimaCuota());

        $totalPagado = 0.0;
        foreach ($prestamos as $p) {
            $totalPagado += (int) $p->cantidad_cuotas * (float) $p->monto_cuota;
        }

        return $this->render('finalizadas', [
            'prestamos' => $prestamos,
            'hoy' => $hoy,
            'totalPagado' => $totalPagado,
        ]);
    }

    /**
     * Edita una cuota propia.
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost) {
            $model->load($this->request->post());
            if ($model->validate() && $model->save(false)) {
                Yii::$app->session->setFlash('success', 'Cuota actualizada correctamente.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Elimina una cuota propia.
     */
    public function actionEliminar($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        Yii::$app->session->setFlash('success', 'Cuota eliminada.');

        return $this->redirect($this->request->referrer ?: ['index']);
    }

    /**
     * Busca la cuota por id verificando que pertenezca al usuario logueado.
     *
     * @throws NotFoundHttpException si no existe o no es del usuario actual
     */
    protected function findModel($id): Prestamo
    {
        $model = Prestamo::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);

        if ($model === null) {
            throw new NotFoundHttpException('La cuota solicitada no existe.');
        }

        return $model;
    }
}
