<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductoController extends Controller
{
    

	public function administracionProducto(){
		$insumoProducto = 'producto';
		return view('administracion.buscarInsumoProducto')->with(compact('insumoProducto'));
	}


	public function administracionInsumo(){
		$insumoProducto = 'insumo';
		return view('administracion.buscarInsumoProducto')->with(compact('insumoProducto'));
	}

	public static function  search($busqueda)
	{
		 $isumoProducto = 'producto';	
		 $codigo=request()->input('codigo');
		 $nombre=request()->input('nombre');
		 $categoria=request()->input('categoria');
		 $alarma=request()->input('alarma');
		 
		$productos= (Producto::filterRAW($codigo,$nombre,$categoria,$alarma))->toArray();
		var_dump($producto);
		return  response()->json($productos);





	}



	 


}
/*

//filtro producto por codigo
     public function filterByCodigo (int $codigo)
     {
        if ($codigo != null) {
          return $this->where('codigo','=',$codigo);
        }else{
          return $this;
        }
     }
       //filtro producto por nombre
       public function filterByName ($nombre)
       {
        if ($nombre != null) {
          return $this->where('nombre','=',$nombre);
        }else{
          return $this;
        }
       }   
     //filtro producto por categoria
         public function filterByCategoria ($categoria)
              {
          if ($categoria != null) {
            return $this->where('categoria','=',$categoria);
          }else{
            return $this;
          }
         }
     //filtro producto por alarma
       public function filterByAlarma ($alarma)
            {
        if ($alarma != null) {
          return $this->where('alarmaActiva','=',$alarma);
        }else{
          return $this;
        }
       }
*/