@extends('layouts.layoutPrincipal' )

@section('section')
		@include('elementosComunes.aperturaTitulo')
			@if ($insumoProducto=='producto')			
				Administración de Productos
			@elseif ($insumoProducto=='insumo')
				Administración de Insumos
			@endif
		@include('elementosComunes.cierreTitulo')
			<form>
				<div class="col-md-6">
					<div class="form-group">
			        	<label>Código</label>
			           	<input type="text" class="form-control" id="codigo"> 
			        </div>
			        <div class="form-group">
			            <label>Nombre</label>
			            <input type="text" class="form-control" id="nombre"> 
			        </div>
		    	</div>
		        <div class="col-md-6">
			        <div class="form-group">
			            <label>Categoría</label>
			            <input type="text" class="form-control"" id="categoria"> 
			        </div>
			        <div class="form-group">
			            <label>Alarma</label>
			            <select name="alarma" id="alarma">
	  						<option value="activa">Activa</option>
	  						<option value="inactiva">Inactiva</option>  						
						</select>
			        </div>
		    	</div>

		    	@include('elementosComunes.aperturaBoton')
		    		<a href="" class="btn btn-primary" id="btnBuscar">Buscar</a>
	          	@include('elementosComunes.cierreBoton')
		    </form>
	    @include('elementosComunes.aperturaTabla')
	    	<thead>
	    		<tr>
	    			<th>Código</th> <th>Nombre</th> <th>Descripción</th>
	    			<th>Tipo de Unidad</th><th>Alarma</th><th>Cant. Aviso Stock</th>
	    			<th>Cant. Aviso Stock Crítico</th><th>Categoría</th><th>Estado</th>
	    		</tr>
	    	</thead>
	    	<tbody id="tbodyResultados">	    		
	    	</tbody>		
        @include('elementosComunes.cierreTabla')

        @if ($insumoProducto=='producto')
        	@include('elementosComunes.aperturaBoton')        		
				<a href="#popAgregar" href="#myPopup" data-rel="popup">Agregar Producto</a>
				<div data-role="popup" id="popAgregar" class="ui-content">
					@include('elementosComunes.aperturaTitulo')
					Agregar Producto
					@include('elementosComunes.cierreTitulo')

					    <form class="">
					    	<div class="col-md-6">
					            <div class="form-group">
					              <label>Nombre</label>
					              <input type="email" class="form-control"> </div>
					            <div class="form-group">
					              <label>Descripcion</label>
					              <input type="password" class="form-control"> </div>
					            <div class="form-group">
					              <label for="exampleInputEmail1">Categoría</label>
					              <input type="text" class="form-control" id="inlineFormInput"> </div>
					            <div class="form-group">
					              <label for="exampleInputEmail1">Estado</label>
					              <input type="text" class="form-control" id="inlineFormInput"> </div>
					    
					        </div>
					        <div class="col-md-6">
					            <div class="form-group">
					              <label for="exampleInputEmail1">Tipo de Unidad</label>
					              <input type="text" class="form-control" id="inlineFormInput"> </div>
					            <div class="form-group">
					              <label for="exampleInputEmail1">Alarma (Activa o Inactiva)</label>
					              <input type="text" class="form-control" id="inlineFormInput"> </div>
					            <div class="form-group">
					              <label for="exampleInputEmail1"> Cantidad para aviso de falta de stock</label>
					              <input type="text" class="form-control" id="inlineFormInput"> </div>
					            <div class="form-group">
					              <label for="exampleInputEmail1"> Cantidad para aviso de falta de stock crítica</label>
					              <input type="text" class="form-control" id="inlineFormInput"> </div>


					        </div>
						    @include('elementosComunes.aperturaBoton')
							          <a class="btn btn-primary" href="#">Guardar</a>
							@include('elementosComunes.cierreBoton')

					              
						</form>
	        @include('elementosComunes.cierreBoton')
	        @include('elementosComunes.aperturaBoton')
					<a href="">Modificar Producto</a>
	        @include('elementosComunes.cierreBoton')
	        @include('elementosComunes.aperturaBoton')
					<a href="">Eliminar Producto</a>
	        @include('elementosComunes.cierreBoton')
	    @elseif ($insumoProducto=='insumo')
	    	@include('elementosComunes.aperturaBoton')
					<a href="">Agregar Insumo</a>
	        @include('elementosComunes.cierreBoton')
	        @include('elementosComunes.aperturaBoton')
					<a href="">Modificar Insumo</a>
	        @include('elementosComunes.cierreBoton')
	        @include('elementosComunes.aperturaBoton')
					<a href="">Eliminar Insumo</a>
	        @include('elementosComunes.cierreBoton')
        @endif

@endsection

 @section('script')
	 <script type="text/javascript" src="{{asset('js/buscarInsumoProducto.js')}}"></script>
 @endsection