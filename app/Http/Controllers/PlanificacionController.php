<?php

namespace App\Http\Controllers;

use App\GestorStock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Planificacion;
use Mockery\Exception;

class PlanificacionController extends Controller
{
    public static function index()
    {
        $planificaciones = Planificacion::getSemana(Carbon::now()->format('Y-m-d'));
        return view('programaProduccionSemanal.programaProduccionSemanal',['planificaciones'=>$planificaciones]);
    	
    }

    public static function calendarioSig(){
        $fechaC = Carbon::createFromFormat('Y-m-d',request()->input('fecha'));
        $fechaC = $fechaC->startOfWeek();
        $fechaC = $fechaC->addWeek();
        $fecha = $fechaC->format('Y-m-d');
        $planificaciones = Planificacion::getSemana($fecha);
        return view('programaProduccionSemanal.programaProduccionSemanal',['planificaciones'=>$planificaciones]);
    }

    public static function calendarioAnt(){

        $fechaC = Carbon::createFromFormat('Y-m-d',request()->input('fecha'));
        $fechaC = $fechaC->startOfWeek();
        $fechaC = $fechaC->subWeek();
        $fecha = $fechaC->format('Y-m-d');
        $planificaciones = Planificacion::getSemana($fecha);
        return view('programaProduccionSemanal.programaProduccionSemanal',['planificaciones'=>$planificaciones]);
    }

    public static function show(){
        $planificaciones = [];
        //agarro la fecha
        $fechaC = Carbon::createFromFormat('Y-m-d',request()->input('fecha'));
        $fechaC = $fechaC->startOfWeek();

        // la paso a formato yyyy-mm-dd
        $fecha = $fechaC->format('Y-m-d');

        $planificaciones = Planificacion::getSemana($fecha);

        return  view('programaProduccionSemanal.programaProduccionSemanal', ['planificaciones'=>$planificaciones]);

    }

    public static function planificacionDia(){
        $fecha =request()->input('fecha');
        if($fecha==null){
            throw new Exception('Fecha inválida');
        }
        $planificacion=Planificacion::where('fecha','=',$fecha)->first();

        $planificaciones = [];
        array_push($planificaciones,$planificacion->toArray());
        return view('programaProduccionSemanal.planificacionProductosEInsumos',compact('planificaciones'));
    }

    public static function verNecesidadInsumos(){
        $fechaHasta = request()->input('fecha');
        if($fechaHasta==null)
            throw new Exception('Fecha inválida');
        $fecha = Carbon::createFromFormat('Y-m-d',$fechaHasta);
        $necesidad =GestorStock::getNecesidadInsumos($fecha->format('Y-m-d H:i:s'));
        return view('informes.sumatoriaDeNecesidadDeInsumos',compact('necesidad'));

    }

    /**
     * Funcion que agrega un producto o insumo a una planificacion
     */
    public static function agregarProducto ()
    {
        if(($fecha  = request()->input('fecha'))==null){
            throw new Exception("Error: fecha igual a null");
        }
        $Planificacion = Planificacion::where('fecha','=',$fecha)->first();
        $movimiento_id = $Planificacion->agregarProducto(request()->input('codigo'),
                                                        request()->input('cantidad'),
                                                        request()->input('tipoTP'),
                                                        request()->input('asignatura'),
                                                        request('fecha'));
        if($movimiento_id==null){
            return response()->json('error');
        } else {
            return response()->json($movimiento_id);
        }
    }

    public static function agregarInsumo()
    {
        if(($fecha  = request()->input('fecha'))==null){
            throw new Exception("Error: Error: fecha igual a null");
        }
        $Planificacion = Planificacion::where('fecha','=',$fecha)->first();
        $movimiento_id = $Planificacion->agregarInsumo(request()->input('codigo'),
                                                        request()->input('cantidad'),
                                                        request('fecha'));
        if($movimiento_id==null){
            return response()->json('error');
        } else {
            return response()->json($movimiento_id);
        }
    }

    public static function eliminar()
    {
        if(($fecha  = request()->input('fecha'))==null){
            throw new Exception("Error: Error: fecha igual a null");
        }
        $planificacion = Planificacion::where('fecha','=',$fecha)->first();
        $planificacion->eliminarInsPro(request()->all());
        return response('OK');

    }

    public static function modificar()
    {
        if(($fecha  = request()->input('fecha'))==null){
            throw new Exception("Error: agregar prod movimiento_id null");
        }
        $Planificacion = Planificacion::where('fecha','=',$fecha)->first();
        $movimiento_id = $Planificacion->agregarProducto(request()->all());
        if($movimiento_id==null){
            return response()->json('error');
        } else {
            return response()->json($movimiento_id);
        }
    }

}
