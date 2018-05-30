<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use App\Producto;
use Exception;

/**
 * Lote
 * @mixin Eloquent
 *
 * */

class Lote extends Model
{

    protected $guarded=[];

    /**
     * @param array $datos
     * @return $this|Model
     */



    public function producto(){
		return $this->belongsTo('App\Producto','producto_id')->first();
	}


    public function getNameProd()
    {
        return (Producto::find($this->producto_id))->nombre;
    }
	public function toArray()
	{

        $producto = Producto::find($this->producto_id);
		return[
        'numeroLote'=>$this->id,//
        'cantidad'=>GestorStock::getSaldoLote($this->id), //
        'vencimiento'=>$this->fechaVencimiento, //
        'fechaInicio'=>$this->fechaInicio,//
        'nombreProducto'=>$producto->nombre,//
        'tipoUnidad'=>$producto->tipoUnidad,//
        'cantidadElaborada'=>$this->cantidadElaborada,//
        'costoUnitario'=>$this->costoUnitario,//
        'inicioMaduracion'=>$this->fechaInicioMaduracion,//
        'finalizacion'=>$this->fechaFinalizacion,//
        'cantidadFinal'=>$this->cantidadFinal,//
        'proveedor'=>null,//
        'tipoTp'=>$this->tipoTP,//
        'codigo'=>$producto->codigo,//
        'asignatura'=>null//
        ]	;

	}

    public static function crearLoteNoPlanificado(array $datos)
    {
        $datosLote=[
            'producto_id'=>$datos['producto_id'],
            'tipoLote'=>TipoLote::INICIADO,
            'fechaInicio'=>$datos['fechaInicio'],
            'cantidadElaborada'=>$datos['cantidadElaborada'],
            'tipoTP'=>$datos['tipoTP'],
        ];
        if($datos['tipoTP']){
            $datosLote['asignatura'] = $datos['asignatura'];
        }
        return Lote::create($datosLote);
    }

    /**
     * Elimina un lote
     * @param int $idLote
     */
    public static function eliminarLote(int $idLote)
    {
        $lote=null;
        if(($lote=Lote::find($idLote))!=null){
            $lote->delete();
        }

    }

    public function modificarLote($consumos, $fecha){


        switch ($this->tipoLote) {
            case TipoLote::INICIADO: {
                //actualizar datos del lote
                //TODO
                //actualizo los consumos
                $producto=Producto::find($this->producto_id);
                $trazabilidad = GestorLote::getTrazabilidadLote($this->id);
                //por cada consumo de la modificacion
                foreach ($consumos as $consumo){
                    $banderaMatch = false;
                    $consumoViejoMatched = null;
                    //busco si ya fue cargado el consumo de ese producto
                    foreach ($trazabilidad as $consumoViejo) {
                        if ($consumo['producto_id'] === $consumoViejo['producto_id']) {
                            $banderaMatch = true;
                            $consumoViejoMatched = $consumoViejo;
                            break;
                        }
                    }
                    if($banderaMatch){ // si matcheo hay que actualizar, modificando lo que estaba previamente cargado
                        //GestorStock::modificarConsumo($this->id,$);
                        //TODO hay que resolver el problema de cargar consumos de n lotes diferentes de un mismo producto.
                    } else { //sino se da de alta un nuevo consumo
                        GestorStock::altaConsumo($this->id,$consumo['idLote'],$consumo['producto_id'],$consumo['cantidad'],$fecha);

                    }

                    //guardo los cambios
                    $this->save();
                }

                break;
            }

            default : {

                return null;
            }
        }
    }

    public function registrarMaduracion(string $fechaInicioMaduracion){
        if($this->tipoLote !=TipoLote::INICIADO){
            throw new Exception('Solo se puede pasar a maduracion un lote que este en estado INICIADO');
        }
        $this->tipoLote = TipoLote::MADURACION;
        $this->fechaInicioMaduracion = $fechaInicioMaduracion;
        $this->save();

    }


}
