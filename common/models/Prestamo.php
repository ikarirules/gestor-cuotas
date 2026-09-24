<?php

namespace common\models;

use DateTimeImmutable;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Modelo de una cuota (préstamo) perteneciente a un usuario.
 *
 * @property int $id
 * @property int $user_id
 * @property string $nombre
 * @property string $fecha_primera_cuota
 * @property string $monto_cuota
 * @property int $cantidad_cuotas
 * @property int $created_at
 * @property int $updated_at
 */
class Prestamo extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%prestamo}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['nombre', 'fecha_primera_cuota', 'monto_cuota', 'cantidad_cuotas'], 'required'],
            ['nombre', 'trim'],
            ['nombre', 'string', 'max' => 150],
            ['fecha_primera_cuota', 'date', 'format' => 'php:Y-m-d'],
            ['monto_cuota', 'number', 'min' => 0.01],
            ['cantidad_cuotas', 'integer', 'min' => 1],
            ['user_id', 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'nombre' => 'Descripción',
            'fecha_primera_cuota' => 'Fecha de la primera cuota',
            'monto_cuota' => 'Monto de cada cuota',
            'cantidad_cuotas' => 'Cantidad de cuotas',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Devuelve el detalle de cada cuota: mes de vencimiento y si ya está pagada
     * respecto al mes actual (el mes en curso se considera pagado).
     */
    public function generarDetalleCuotas(DateTimeImmutable $hoy): array
    {
        $inicio = new DateTimeImmutable($this->fecha_primera_cuota);
        $mesActual = $hoy->format('Y-m');
        $detalle = [];

        for ($i = 0; $i < (int) $this->cantidad_cuotas; $i++) {
            $fechaCuota = $inicio->modify("+{$i} months");
            $mesCuota = $fechaCuota->format('Y-m');
            $detalle[] = [
                'numero' => $i + 1,
                'mes' => $mesCuota,
                'monto' => (float) $this->monto_cuota,
                'pagada' => $mesCuota < $mesActual,
                'es_mes_actual' => $mesCuota === $mesActual,
            ];
        }

        return $detalle;
    }

    public function contarCuotasPagadas(DateTimeImmutable $hoy): int
    {
        $pagadas = 0;
        foreach ($this->generarDetalleCuotas($hoy) as $c) {
            if ($c['pagada'] || $c['es_mes_actual']) {
                $pagadas++;
            }
        }
        return $pagadas;
    }

    /**
     * Arma el calendario mensual sumando, para cada mes del rango, el total
     * a pagar entre todos los préstamos recibidos (ya filtrados por usuario).
     *
     * @param Prestamo[] $prestamos
     */
    public static function calcularCalendarioMensual(array $prestamos, DateTimeImmutable $hoy, int $mesesAtras, int $mesesAdelante): array
    {
        $mesActual = $hoy->format('Y-m');
        $meses = [];

        for ($i = -$mesesAtras; $i <= $mesesAdelante; $i++) {
            $fecha = $hoy->modify("{$i} months");
            $clave = $fecha->format('Y-m');
            $meses[$clave] = [
                'clave' => $clave,
                'fecha' => $fecha,
                'total' => 0.0,
                'items' => [],
                'es_pasado' => $clave < $mesActual,
                'es_actual' => $clave === $mesActual,
            ];
        }

        foreach ($prestamos as $prestamo) {
            foreach ($prestamo->generarDetalleCuotas($hoy) as $c) {
                if (isset($meses[$c['mes']])) {
                    $meses[$c['mes']]['total'] += $c['monto'];
                    $meses[$c['mes']]['items'][] = [
                        'nombre' => $prestamo->nombre,
                        'numero' => $c['numero'],
                        'cantidad_cuotas' => (int) $prestamo->cantidad_cuotas,
                        'monto' => $c['monto'],
                    ];
                }
            }
        }

        return $meses;
    }

    public static function formatearMoneda(float $monto): string
    {
        return '$' . number_format($monto, 2, ',', '.');
    }

    public static function formatearMes(DateTimeImmutable $fecha): string
    {
        static $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        return $meses[(int) $fecha->format('n')] . ' ' . $fecha->format('Y');
    }
}
