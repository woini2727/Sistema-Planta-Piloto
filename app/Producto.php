<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use App\Lote;

/**
 * Producto
 * @mixin Eloquent
 *
 * */
class Producto extends Model
{


    protected $guarded=[];
    const TIPO_UNIDADES=[
        'Kg',
        'Gr',
        'Mg',
        'L',
        'Ml',
        'U'
    ];
    public static function tipoUnidadesTodas()
    {
        return self::TIPO_UNIDADES;
    }


#addformulacion agrega las ttablas pivot al producto que le paso
    public function formulacion ()
    {
    	 # return $this->belongsToMany('Producto', 'producto_productoi', 'producto_id', 'ingrediente_id');
    	 return $this->belongsToMany('App\Producto','producto_productoi')
    	 	->withPivot('producto_id','ingrediente_id','cantidad','cantidadProducto');
    	 ;
        //App\Producto::find(1)->formulacion()->attach('6',['cantidad'=>2,'cantidadProducto'=>10,'ingrediente_id'=>6])

    }

#getIngredientesById devuelce una lista de los id de ingredientes para un prosucto

    	public static function showLotesByProd (string $codigo)
      {
        $lotesReturn=[];
        $lotes = GestorLote::getLotesPorProd($codigo);
        foreach ($lotes as $lote) {
            array_push($lotesReturn,
              [
                  'numeroLote'=>$lote->id,
                  'fechaInicio'=>$lote->fechaInicio,
                  'vencimiento'=>$lote->fechaVencimiento,
                  'cantidad'=>GestorStock::getSaldoLote($lote->id)
              ]);
        }
        return $lotesReturn;
      }

    public static function showLotesSinPlanifByProd(string $codigo)
    {
        $lotesReturn=[];
        $lotes = GestorLote::getLotesPorProd($codigo);
        foreach ($lotes as $lote) {
            if($lote->tipoLote != TipoLote::PLANIFICACION){
                array_push($lotesReturn,
                    [
                        'numeroLote'=>$lote->id,
                        'fechaInicio'=>$lote->fechaInicio,
                        'vencimiento'=>$lote->fechaVencimiento,
                        'cantidad'=>GestorStock::getSaldoLote($lote->id)
                    ]);
            }
        }
        return $lotesReturn;
    }

    /**
     * @return array [ ['id'=>, 'cantidad'=>, 'cantidadProducto'=> ] .. ]
     */
    public function getIngredientes(){
           $ingredientes = $this->formulacion()->get();
           $arrayResult = [];
           foreach ($ingredientes as $ing){
               array_push($arrayResult,['id'=>$ing->pivot->ingrediente_id,'cantidad'=>$ing->pivot->cantidad, 'cantidadProducto'=>$ing->pivot->cantidadProducto]);
           }
           return $arrayResult;
        }   

     public function lotes ()
     {
        return $this->hasMany('App\Lote')->get()
        ;
     } 

     public function getArrayLotes()
     {

      return ['producto'=>$this->nombre,'tu'=>$this->tipoUnidad,'codigo'=>$this->codigo,'lotes'=>$this->lotes()];
      /*
        $arrayResult = [];
        $lotes = $this->lotes();
        foreach ($lotes as $lote) {
          array_push($arrayResult,$lote->toArray());
        }
        return $arrayResult;*/
     }

        public function productoToArray()
     {
        return ['nombre'=>$this->nombre,
              'tipoUnidad'=>$this->tipoUnidad,
              'codigo'=>$this->codigo,
              ];
     }

    /**
     * @param int $cantidad a realizar
     * @return array =  [ ['codigo
     */
    public function getFormulacion(int $cantidad)
     {
         $formulacion = [];
         $ingredientes = $this->getIngredientes();
         foreach ($ingredientes as $ing){
            $arrAux=[];
            $arrayLotes=[];
            $productoAux = Producto::find($ing['id']);
            $arrAux['id']=$ing['id'];
            $arrAux['codigo']=$productoAux->codigo;
            $arrAux['nombre']=$productoAux->nombre;
            $arrAux['tipoUnidad']=$productoAux->tipoUnidad;
            $arrAux['cantidad'] = $cantidad * $ing['cantidad'] / $ing ['cantidadProducto'];
            //Agrego además los lotes, accion altamente cuestionable
             $lotes = Lote::where('producto_id','=',$ing['id'])->get();
             foreach($lotes as $lote){
                 $arrAuxL =[];
                 $arrAuxL['id']=$lote->id;
                 $arrAuxL['stock']=GestorStock::getSaldoLote($lote->id);
                 if($arrAuxL['stock']>0){
                     array_push($arrayLotes,$arrAuxL);
                 }
             }
             $arrAux['lotes']=$arrayLotes;
            array_push($formulacion,$arrAux);
        }
        return $formulacion;
     }


     public function agregarIngrediente($cantidadProducto,$cantidad,$ingrediente_id)
     {

       return $this->formulacion()->attach($ingrediente_id,['cantidad'=>$cantidad,'cantidadProducto'=>$cantidadProducto,'ingrediente_id'=>$ingrediente_id]);
        //App\Producto::find(1)->formulacion()->attach('6',['cantidad'=>2,'cantidadProducto'=>10,'ingrediente_id'=>6])
     }


     //filtro producto por codigo
    public static function filterRAW($codigo ,$nombre, $categoria,$alarma){


      $query=null;
      
     if($codigo!=null){
        $query = 'codigo='."'$codigo'";
     }; 
     
     if($nombre!=null){
        if ($query==null) {
            $query='nombre='."'$nombre'";
        }else{
            $query=$query.'and nombre='."'$nombre'";
        }
     };

     if($categoria!=null){
      if ($query==null) {
            $query='categoria='."'$categoria'";
        }else{
          $query=$query.'and categoria='."'$categoria'";
        }
     };

     if($alarma!=null){

       if ($query==null) {
              $query=$query.'"alarmaActiva"='."'$alarma'";
          }else{
            $query=$query.'and "alarmaActiva"='."'$alarma'";
          }
     };
     if ($query==null) {
       return Producto::all();
     }else{
     return Producto::whereRAW($query)->get();
     }
                
    }

    public static function getProductosSinInsumosArr(){
        $productosAux = Producto::all();
        $productos =[];
        foreach ($productosAux as $producto){
            $arrAux=[];
            //chequeo que el producto no sea un insumo
            if(!empty($producto->getIngredientes())){
                $arrAux = $producto->toArray();
                array_push($productos,$arrAux);
            }
        }
        return $productos;
    }






    public static function test($inspro,$codigo,$nombre,$categoria,$alarma){
        if($inspro=='insumo'){
        $productos=[];
        
        $insumos= (Producto::filterRAW($codigo,$nombre,$categoria,$alarma));
        //var_dump($insumos);
        foreach ($insumos as $insumo) {
        
          if (empty($insumo->getIngredientes())) {
           array_push($productos, $insumo);
          }
          
        }
        $productos=compact('productos');
         //var_dump($productos);
      }

    return  $productos;

}
}


