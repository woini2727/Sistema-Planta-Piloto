@extends('layouts.layoutPrincipal' )

@section('section')
		<?php 
		setlocale(LC_TIME, 'spanish');
		Carbon\Carbon::setUtf8(true);

		$fechaC = Carbon\Carbon::createFromFormat('Y-m-d',$fecha);
		$fechaActual=$fechaC->formatLocalized('%A %d de %B de %Y');
	?>
		@include('elementosComunes.aperturaTitulo')
			Stock <h4>Hasta el dia {{$fechaActual}}</h4>
		@include('elementosComunes.cierreTitulo')
		
		{{-- FORM PARA STOCK A FUTURO --}}
		@include('elementosComunes.aperturaFormInline')

        	<h6 >Fecha Hasta</h6>        
			<form class="form-inline" id="form" name="form" action="stock" method="POST" enctype="multipart/form-data">
				@csrf
	           <div class="input-group">
					<input type="date" class="form-control" placeholder="Fecha" value="{{$fecha}}" id='inputDate' name='fecha' required> 
					
				</div>
				<input  type="submit" class="btn btn-primary" value="Actualizar"> 
				<div class="input-group">
					@if($mostarPlanificados==true)
						<input type="checkbox" name="mostarPlanificados" checked>
					@else
						<input type="checkbox" name="mostarPlanificados">
					@endif
					<h6>Tener en cuenta productos planificados</h6>
				</div>
			</form>

	    @include('elementosComunes.cierreFormInline')

	    {{-- TABLA STOCK --}}
		@include('elementosComunes.aperturaTablaStock')
			<thead ><tr><th>Código</th> 
						<th>Insumo/Producto</th> 
						<th>Cantidad en Stock</th> 
						<th>Unidad</th> 
						<th>Lotes</th></tr>
			</thead>			
	        <tbody >
	        	@foreach ($stock as $s)	        	
		        	<tr data={{ $s['alarma'] }}>
		        		<td>{{ $s['codigo'] }}</td> 
		        		<td>{{ $s['nombre'] }}</td> 
		        		<td> {{ $s['stock'] }}</td> 
		        		<td> {{ $s['tipoUnidad'] }}</td>
		        		<td> <a href="verLotes?codigo={{ $s['codigo'] }}"><img src="{{asset('img/details.png')}}" style="height: 24px; width: 24px" alt=""></a></td>
		        	</tr> 
	        	@endforeach
	        </tbody>
	        
        @include('elementosComunes.cierreTabla')

		<input type="button" class="btn btn-secondary" value="Imprimir" onClick="window.print()">
		@include('layouts.errors')


@endsection
