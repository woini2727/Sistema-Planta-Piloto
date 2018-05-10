<?php


namespace App;
use App\TipoMovimiento;
use App\Movimiento;
use App\DetalleSalida;
use App\Producto;
use InvalidArgumentException;

/**
 * @author brujua
 * @version 1.0
 * @created 22-abr.-2018 3:19:29 a. m.
 */

class GestorStock
{

    // MOVIMIENTOS DE ENTRADA
    //REALES
    /**
     *
     * @param string $idLote
     * @param int $idProducto
     * @param double $cantidad
     * @param string $fecha
     *
     */
    public static function entradaInsumoProducto(string $idLote, int $idProducto, double $cantidad, string $fecha)
    {
        $banderaRecalcular = false;
        $ultimoMovReal=Movimiento::ultimoRealProd($idProducto);
        $movAnterior = $ultimoMovReal;
        //Compruebo si estoy insertando antes del ultimo mov de ese producto
        if($ultimoMovReal->fecha > $fecha){
            //si es asi, recupero el mov anterior a este y deberé recalcular
            $movAnterior = Movimiento::getAnteriorProd($idProducto,$fecha);
            $banderaRecalcular=true;
        }
        $datosNuevoMov = [
            'producto_id'=>$idProducto,
            'fecha'=>$fecha,
            'tipo'=>TipoMovimiento::TIPO_MOV_ENTRADA_INSUMO,
            'idLoteConsumidor'=>$idLote,
            'idLoteIngrediente'=>$idLote,
            'debe'=>0,
            'haber'=>$cantidad,
            'saldoGlobal'=>($movAnterior->saldoGlobal+$cantidad), // cantidad nueva es la anterior mas lo que agrega la llegada
            'saldoLote'=>$cantidad
        ];

        $nuevoMov = Movimiento::create($datosNuevoMov); //puede que no ande, que haya que hacer ->get();

        if($banderaRecalcular){
            self::recalcularStockReal($nuevoMov);
        }
    }
    //PLANIFICADOS



    public static function entradaInsumoPlanificado(int $idProducto, double $cantidad, string $fecha)

    {
        //No deben existir mas de una entrada de insumo planificada para un mismo dia
        //debido a que los planificados se recalculan cada vez que se quiere saber algo de ellos,
        // simplemente inserto el mov sin calcular nada.
        $datosNuevoMov = [
            'producto_id'=>$idProducto,
            'fecha'=>$fecha,
            'tipo'=>TipoMovimiento::TIPO_MOV_ENTRADA_INSUMO_PLANIF,
            'idLoteConsumidor'=>null,
            'idLoteIngrediente'=>null,
            'debe'=>0,
            'haber'=>$cantidad,
            'saldoGlobal'=>null, // cantidad nueva es la anterior mas lo que agrega la llegada
            'saldoLote'=>$cantidad
        ];
        Movimiento::create($datosNuevoMov);
    }


    public static function eliminarEntradaInsumoPlanificado(int $idProducto, string $fecha)

    {
        //No deben existir mas de una entrada de insumo planificada para un mismo dia
        Movimiento::eliminarEntradaInsumoPlanif($idProducto,$fecha);
    }


    public static function entradaProductoPlanificado(string $idLote, int $idProducto, double $cantidad, string $fecha )

    {
        //debido a que los planificados se recalculan cada vez que se quiere saber algo de ellos,
        // simplemente inserto el mov sin calcular nada.
        $datosNuevoMov = [
            'producto_id'=>$idProducto,
            'fecha'=>$fecha,
            'tipo'=>TipoMovimiento::TIPO_MOV_ENTRADA_INSUMO_PLANIF,
            'idLoteConsumidor'=>$idLote,
            'idLoteIngrediente'=>$idLote,
            'debe'=>0,
            'haber'=>$cantidad,
            'saldoGlobal'=>null, //
            'saldoLote'=>$cantidad
        ];
       Movimiento::create($datosNuevoMov);
    }
    public static function eliminarEntradaProductoPlanificado(string $idLote, string $fecha)
    {
        Movimiento::eliminarEntradaProductoPlanif($idLote,$fecha);
    }
    /**
     *
     * @param string $idLote
     * @param int $idProducto
     * @param double $cantidadObsrv
     * @param string $fecha
     */
    public static function controlarExistencia(string $idLote, int $idProducto, double $cantidadObsrv, string $fecha)
    {
        $banderaRecalcular = false;
        $ultimoMovReal=Movimiento::ultimoRealProd($idProducto);
        $ultiMovLote=Movimiento::ultimoRealLote($idLote);
        $movAnteriorProd = $ultimoMovReal;
        //Compruebo si estoy insertando antes del ultimo mov de ese producto
        if($ultimoMovReal->fecha>$fecha){
            //si es asi, recupero el mov anterior a este y deberé recalcular
            $movAnteriorProd = Movimiento::getAnteriorProd($idProducto,$fecha);
            $banderaRecalcular=true;
        }
        //Compruebo si estoy insertando antes del ultimo mov de ese lote
        if($ultiMovLote->fecha>$fecha){
            //si es asi, deberé recalcular
            $banderaRecalcular=true;
        }
        // Ajusto el saldo global la diferencia entre la cantidad anterior y la observada
        $diferencia = $cantidadObsrv - $ultiMovLote->saldoLote;
        //calculo debe y haber
        if($diferencia>0){
            $haber = $diferencia;
            $debe = 0;
        } else {
            $debe = abs($diferencia);
            $haber = 0;
        }

        $nuevoSaldoGlobal = $movAnteriorProd->saldoGlobal+ $debe - $haber;



        $datosNuevoMov = [
            'producto_id'=>$idProducto,
            'fecha'=>$fecha,
            'tipo'=>TipoMovimiento::TIPO_MOV_CONTROL_EXISTENCIAS,
            'idLoteConsumidor'=>$idLote,
            'idLoteIngrediente'=>$idLote,
            'debe'=>$debe,
            'haber'=>$haber,
            'saldoGlobal'=>$nuevoSaldoGlobal,
            'saldoLote'=>$cantidadObsrv
        ];
        $nuevoMov = Movimiento::create($datosNuevoMov);
        if($banderaRecalcular){
            self::recalcularStockReal($nuevoMov);
        }
        //TODO
    }
    //MOVIMIENTOS DE SALIDA
    //REALES
    /**
     *
     * @param string $idLoteConsumidor
     * @param string $idLoteIngrediente
     * @param int $idProductoIng
     * @param double $cantidad
     * @param string $fecha
     */
    public static function altaConsumo(string $idLoteConsumidor, string $idLoteIngrediente, int $idProductoIng, double $cantidad, string $fecha)
    {
        $banderaRecalcular = false;
        $ultimoMovRealProd=Movimiento::ultimoRealProd($idProductoIng);
        $ultimoMovRealLote = Movimiento::ultimoRealLote($idLoteIngrediente);
        $movAnteriorProd = $ultimoMovRealProd;
        $movAnteriorLote = $ultimoMovRealLote;
        //Compruebo si estoy insertando antes del ultimo mov de ese producto
        if($ultimoMovRealProd->fecha>$fecha){
            //si es asi, recupero el mov anterior a este y deberé recalcular
            $movAnteriorProd = Movimiento::getAnteriorProd($idProductoIng,$fecha);
            $banderaRecalcular=true;
        }
        //Compruebo si estoy insertando antes del ultimo mov de ese lote
        if($ultimoMovRealLote->fecha>$fecha){
            //si es asi, recupero el mov anterior a este y deberé recalcular
            $movAnteriorLote = Movimiento::getAnteriorLote($idLoteIngrediente,$fecha);
            $banderaRecalcular=true;
        }
        $datosNuevoMov = [
            'producto_id'=>$idProductoIng,
            'fecha'=>$fecha,
            'tipo'=>TipoMovimiento::TIPO_MOV_CONSUMO,
            'idLoteConsumidor'=>$idLoteConsumidor,
            'idLoteIngrediente'=>$idLoteIngrediente,
            'debe'=>$cantidad,
            'haber'=>0,
            'saldoGlobal'=>$movAnteriorProd->saldoGlobal - $cantidad, // cantidad nueva es la anterior menos consumo
            'saldoLote'=>$movAnteriorLote->saldoLote - $cantidad
        ];
        $nuevoMov = Movimiento::create($datosNuevoMov);
        if($banderaRecalcular){
            self::recalcularStockReal($nuevoMov);
        }
    }
    /**
     *
     * @param string $idLote
     * @param int $idProducto
     * @param double $cantidad
     * @param string $fecha
     * @param String $motivo
     * @param string $detalle
     * @parm string tipo
     */
    public static function salidaExcepcional(string $idLote, int $idProducto, double $cantidad, string $fecha, String $motivo, String $detalle )
    {
        $banderaRecalcular = false;
        $ultimoMovRealProd=Movimiento::ultimoRealProd($idProducto);
        $ultimoMovRealLote=Movimiento::ultimoRealLote($idLote);
        if(is_null($ultimoMovRealLote)||is_null($ultimoMovRealProd)){
            throw new InvalidArgumentException("No hay movimientos anteriores para ese lote o producto");
        }
        $movAnteriorProd = $ultimoMovRealProd;
        $movAnteriorLote = $ultimoMovRealLote;
        //Compruebo si estoy insertando antes del ultimo mov de ese producto
        if($ultimoMovRealProd->fecha>$fecha){
            //si es asi, recupero el mov anterior a este y deberé recalcular
            $movAnteriorProd = Movimiento::getAnteriorProd($idProducto,$fecha);
            $banderaRecalcular=true;
        }
        //Compruebo si estoy insertando antes del ultimo mov de ese lote
        if($ultimoMovRealLote->fecha>$fecha){
            //si es asi, recupero el mov anterior a este y deberé recalcular
            $movAnteriorLote = Movimiento::getAnteriorLote($idLote,$fecha);
            $banderaRecalcular=true;
        }
        $datosNuevoMov = [
            'producto_id'=>$idProducto,
            'fecha'=>$fecha,
            'tipo'=>TipoMovimiento::TIPO_MOV_SALIDA_EXCEP,
            'idLoteConsumidor'=>$idLote,
            'idLoteIngrediente'=>$idLote,
            'debe'=>$cantidad,
            'haber'=>0,
            // cantidad nueva es la anterior menos la salida
            'saldoglobal'=>($movAnteriorProd->saldoGlobal - $cantidad),
            // descuento el saldo del lote restando del saldo anterior
            'saldolote'=>($movAnteriorLote->saldoLote - $cantidad)
        ];
        $nuevoMov = Movimiento::create($datosNuevoMov);
        //agrego la entrada en salida detalle
        $datosSalidaExcep=[
            'movimiento_id'=>$nuevoMov->id,
            'lote_id'=>$idLote,
            'fecha'=>$fecha,
            'motivo'=>$motivo,
            'detalle'=>$detalle,
            'cantidad'=>$cantidad
        ];
        $salida = DetalleSalida::create($datosSalidaExcep);
        //De ser necesario recalculo.
        if($banderaRecalcular){
            self::recalcularStockReal($nuevoMov);
        }
    }
    /**
     *
     * @param string $idLote
     * @param int $idProducto
     * @param double $cantidad
     * @param string $fecha
     */
    public static function salidaVentas(string $idLote, int $idProducto, double $cantidad, string $fecha)
    {
        $banderaRecalcular = false;
        $ultimoMovRealProd=Movimiento::ultimoRealProd($idProducto);
        $ultimoMovRealLote=Movimiento::ultimoRealLote($idLote);
        if(is_null($ultimoMovRealLote)||is_null($ultimoMovRealProd)){
            throw new InvalidArgumentException("No hay movimientos anteriores para ese lote o producto");
        }
        $movAnteriorProd = $ultimoMovRealProd;
        $movAnteriorLote = $ultimoMovRealLote;
        //Compruebo si estoy insertando antes del ultimo mov de ese producto
        if($ultimoMovRealProd->fecha>$fecha){
            //si es asi, recupero el mov anterior a este y deberé recalcular
            $movAnteriorProd = Movimiento::getAnteriorProd($idProducto,$fecha);
            $banderaRecalcular=true;
        }
        //Compruebo si estoy insertando antes del ultimo mov de ese lote
        if($ultimoMovRealLote->fecha>$fecha){
            //si es asi, recupero el mov anterior a este y deberé recalcular
            $movAnteriorLote = Movimiento::getAnteriorLote($idLote,$fecha);
            $banderaRecalcular=true;
        }
        $datosNuevoMov = [
            'producto_id'=>$idProducto,
            'fecha'=>$fecha,
            'tipo'=>TipoMovimiento::TIPO_MOV_SALIDA_VENTAS,
            'idLoteConsumidor'=>$idLote,
            'idLoteIngrediente'=>$idLote,
            'debe'=>$cantidad,
            'haber'=>0,
            // cantidad nueva es la anterior menos la salida
            'saldoglobal'=>($movAnteriorProd->saldoGlobal - $cantidad),
            // descuento el saldo del lote restando del saldo anterior
            'saldolote'=>($movAnteriorLote->saldoLote - $cantidad)
        ];
        $nuevoMov = Movimiento::create($datosNuevoMov);
        //De ser necesario recalculo.
        if($banderaRecalcular){
            self::recalcularStockReal($nuevoMov);
        }
    }
    /**
     *
     * @param string $idLote
     * @param int $idProducto
     * @param double $cantidad
     * @param String $detalle
     * @param string $fecha
     *
     */
    public static function decomisar(string $idLote, int $idProducto, double $cantidad, String $detalle, string $fecha)
    {
        $banderaRecalcular = false;
        $ultimoMovRealProd=Movimiento::ultimoRealProd($idProducto);
        $ultimoMovRealLote=Movimiento::ultimoRealLote($idLote);
        if(is_null($ultimoMovRealLote)||is_null($ultimoMovRealProd)){
            throw new InvalidArgumentException("No hay movimientos anteriores para ese lote o producto");
        }
        $movAnteriorProd = $ultimoMovRealProd;
        $movAnteriorLote = $ultimoMovRealLote;
        //Compruebo si estoy insertando antes del ultimo mov de ese producto
        if($ultimoMovRealProd->fecha>$fecha){
            //si es asi, recupero el mov anterior a este y deberé recalcular
            $movAnteriorProd = Movimiento::getAnteriorProd($idProducto,$fecha);
            $banderaRecalcular=true;
        }
        //Compruebo si estoy insertando antes del ultimo mov de ese lote
        if($ultimoMovRealLote->fecha>$fecha){
            //si es asi, recupero el mov anterior a este y deberé recalcular
            $movAnteriorLote = Movimiento::getAnteriorLote($idLote,$fecha);
            $banderaRecalcular=true;
        }
        $datosNuevoMov = [
            'producto_id'=>$idProducto,
            'fecha'=>$fecha,
            'tipo'=>TipoMovimiento::TIPO_MOV_SALIDA_DECOMISO,
            'idLoteConsumidor'=>$idLote,
            'idLoteIngrediente'=>$idLote,
            'debe'=>$cantidad,
            'haber'=>0,
            // cantidad nueva es la anterior menos la salida
            'saldoglobal'=>($movAnteriorProd->saldoGlobal - $cantidad),
            // descuento el saldo del lote restando del saldo anterior
            'saldolote'=>($movAnteriorLote->saldoLote - $cantidad)
        ];
        $nuevoMov = Movimiento::create($datosNuevoMov);
        //agrego la entrada en detalleSalida
        $datosSalidaExcep=[
            'movimiento_id'=>$nuevoMov->id,
            'lote_id'=>$idLote,
            'fecha'=>$fecha,
            'motivo'=>DetalleSalida::MOTIVO_DECOMISO,
            'detalle'=>$detalle,
            'cantidad'=>$cantidad
        ];
        $salida = DetalleSalida::create($datosSalidaExcep);
        //De ser necesario recalculo.
        if($banderaRecalcular){
            self::recalcularStockReal($nuevoMov);
        }
    }
    //PLANIFICADOS
    /**
     * @param string $idLoteConsumidor
     * @param string $idProdIng
     * @param double $cantidad
     * @param string $fecha
     */
    public static function altaConsumoPlanificado(string $idLoteConsumidor, string $idProdIng, double $cantidad, string $fecha)
    {
        $datosNuevoMov = [
            'producto_id'=>$idProdIng,
            'fecha'=>$fecha,
            'tipo'=>TipoMovimiento::TIPO_MOV_CONSUMO_PLANIF,
            'idLoteConsumidor'=>$idLoteConsumidor,
            'idLoteIngrediente'=>null,
            'debe'=>$cantidad,
            'haber'=>0,
            'saldoGlobal'=>null,
            'saldoLote'=>null
        ];
        $nuevoMov = Movimiento::create($datosNuevoMov);
    }
    public static function eliminarConsumosPlanificados(string $idLoteConsumidor, string $fecha)
    {
        Movimiento::eliminarConsumosPlanificados($idLoteConsumidor, $fecha);
    }
    //INFORMES
    public static function getSaldoLote(string $idLote)
    {
        $ultMov = Movimiento::ultimoRealLote($idLote);
        return $ultMov->saldoLote;
    }
    /**
     *
     * @param string $idLote
     * @return int[] $idsLotesTrazabilidad // devolvera array asociativo  donde la key será el id de lote y el valor la cantidad usada
     */
    public static function Trazabilidad(string $idLote)
    {
        $arrayReturn = [];
        $movimientos= Movimiento::getTrazabilidadLote($idLote);
        foreach ($movimientos as $mov){
            $arrayAux=[];
            $arrayAux['idLote']=$mov->idLoteIngrediente;
            $arrayAux['cantidad']=$mov->debe;
            array_push($arrayReturn,$arrayAux);
        }
        return $arrayReturn;
    }
    /**
     * @param string $fechaDesde
     * @param string $fechaHasta
     * @return DetalleSalida[]
     */
    public static function getSalidasExcpYdecomisos(string $fechaDesde, string $fechaHasta)
    {
        return DetalleSalida::getSalidas($fechaDesde, $fechaHasta);
    }
    /**
     * @param string $fechaDesde
     * @param string $fechaHasta
     * @return DetalleSalida[]
     */
    public static function getSalidasVentas(string $fechaDesde, string $fechaHasta)
    {
        $result = [];
        $movimientos= Movimiento::getSalidasVenta($fechaDesde, $fechaHasta);
        foreach ($movimientos as $movimiento){
            $datosSalida=[
                'movimiento_id'=>$movimiento->id,
                'lote_id'=>$movimiento->idLoteIngrediente,
                'fecha'=>$movimiento->fecha,
                'motivo'=>DetalleSalida::MOTIVO_VENTAS,
                'detalle'=>'Salida a ventas',
                'cantidad'=>$movimiento->debe
            ];
            $salida = new DetalleSalida($datosSalida);
            array_push($result,$salida);
        }
        return $result;
    }
    /**
     * @param string $fechaHasta
     * @return array [['nombre'=>,'codigo'=>, 'tu'=>, 'alarma'=>, 'stock'=>, 'producto_id'=>, ]...] hashmap key: idProducto, value: cantidad
     * Hay que evaluar si con esta funcion no alcanza ya para desde afuera calcular getNecesidadInsumos y otras por el estilo
     */
    public static function getStockPorProd(string $fechaHasta)
    {
        $result=[];
        self::recalcularPlanificados($fechaHasta);
        $movimientos =Movimiento::ultimoStockProdTodos($fechaHasta);
        foreach ($movimientos as $movimiento){
            $arrAux=[];
            $producto=Producto::find($movimiento->producto_id)->get();
            $stock=$movimiento->salgoGlobal;
            $arrAux['alarma']='normal';
            $arrAux['nombre']=$producto->nombre;
            $arrAux['codigo']=$producto->codigoProducto;
            $arrAux['tu']=$producto->tipoUnidad;
            $arrAux['stock']=$stock;
            $arrAux['producto_id']=$movimiento->producto_id;
            if($producto->alarmaActiva){
                if($stock<$producto->alarmaAmarilla){
                    $arrAux['alarma']='amarilla';
                }
                if($stock<$producto->alarmaRoja){
                    $arrAux['alarma']='roja';
                }
            }

            array_push($result, $arrAux);

        }
        return $result;
    }

    /**
     * @param $fechaHasta
     * @return array   de la forma
     * [
     *  'fecha'=>,
     * 'necesidades'=>[
     *      ['codigo'=>,'insumo'=>, 'NecesidadFinal'=>, 'fechaAgotamiento'=> ],
     *      [...]
     *  ],
     * 'alarmas'=>[
     *      ['codigo'=>,'insumo'=>,'cantidad'=>, 'color'=>],
     *      [...]
     *  ],
     * ]
     */
    public static function getNecesidadInsumos($fechaHasta){
        $arrResult=[];
        $necesidades = [];
        $alarmas =[];
        //TODO



    }
    //PRIVADOS
    /**
     * @param Movimiento $movimientoDesde
     */
    private static function recalcularStockReal(Movimiento $movimientoDesde)
    {

        $movimientos = Movimiento::getMovimientosProdDespuesDe($movimientoDesde->producto_id,$movimientoDesde->fecha);

        $movAnteriorProd = $movimientoDesde;
        $movAnteriorLote= $movimientoDesde;
        foreach ($movimientos as $movimiento) {

            $debe = $movimiento->debe;
            $haber = $movimiento->haber;


            //si es movimiento del lote recalculo saldoLote
            if ($movimiento->idLoteIngrediente == $movimientoDesde->idLoteIngrediente) {

                $nuevoSaldoLote = $movAnteriorLote->saldoLote + $haber - $debe;

                $movimiento->saldoLote = $nuevoSaldoLote;
                //actualizo para la próxima iteración
                $movAnteriorLote = $movimiento;
            }


            $nuevoSaldoGlobal = $movAnteriorProd->saldoGlobal + $haber - $debe;

            $movimiento->saldoGlobal = $nuevoSaldoGlobal;
            //guardo
            $movimiento->save();
            //actualizo para la próxima iteración
            $movAnteriorProd = $movimiento;
        }
    }
    private static function recalcularPlanificados($fechaHasta)
    {
        //Guardo el ultimo mov de cada producto, ya que el recalculo se hará por cada producto
        $movimientosInicialesProducto = Movimiento::ultimoStockProdTodos($fechaHasta);
        //Por cada producto
        foreach ($movimientosInicialesProducto as $ultMovRealProd){
            //Tomo el ultimo movimiento
            $producto = $ultMovRealProd->producto_id;
            $movAnteriorProd =$ultMovRealProd;
            $planificacionesProd = Movimiento::getPlanificadosProd($producto,$fechaHasta);
            //itero para todas las planificaciones de este producto
            foreach ($planificacionesProd as $planif){

                //si la planificacion es anterior al ultimo mov debo darla como Incumplido
                if($planif->fecha<$movAnteriorProd->fecha) {
                    $planif->tipo = TipoMovimiento::incumplidoDe($planif->tipo);
                } else {
                    //sino recalculo
                    $debe = $planif->debe;
                    $haber = $planif->haber;
                    $nuevoSaldoG = $movAnteriorProd->saldoGlobal + $haber - $debe;
                    $planif->saldoGlobal = $nuevoSaldoG;
                }
                $planif->save();
                //actualizo para la proxima iteracion
                $movAnteriorProd = $planif;
            }
        }
    }
}