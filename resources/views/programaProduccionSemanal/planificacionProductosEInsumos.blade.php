@extends('layouts.layoutPrincipal' )
@section('section')

    
    <?php $fecha=$planificaciones[0]['fecha'];?>

    @include('elementosComunes.aperturaTitulo')

        Planificación Productos e Insumos
    
    @include('elementosComunes.cierreTitulo')
    @include('elementosComunes.aperturaTitulo')
    <h4 style="text-align: center">
    <b>Fecha Actual: <?= date("d-m-Y",strtotime($fecha)); ?></b>
    </h4>
    @include('elementosComunes.cierreTitulo')
    @include('elementosComunes.aperturaTitulo')
    <h4>
        Productos
    </h4>
    @include('elementosComunes.cierreTitulo')
    @include('elementosComunes.aperturaTabla')
    <thead>
    <tr>
        <th>Código</th>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>TP</th>
        <th></th>
        <th></th>
    </tr>
    </thead>
    <tbody class="tbodyPlanif">
    @foreach($planificaciones as $value)
        @if($value['fecha']==$fecha)
            @foreach($value["productos"] as $v)
                <?php
                $codigo[]=$v['codigo'];
                $nombre[]=$v['nombre'];
                $cantidad[]=$v['cantidad'].$v['tipoUnidad'];
                $id[]=$v["movimiento_id"];
                ?>
            @endforeach
        @endif
    @endforeach
    @if(isset($codigo))
        @foreach($codigo as $k=>$a)

            <tr>

                <td><?=$codigo[$k];?></td>
                <td><?=$nombre[$k];?></td>
                <td><?=$cantidad[$k];?></td>
                <td></td>
                <td><img  src="{{asset('img/modificar.png') }}" width="20" height="20" style="cursor: pointer;"  class="modificar" /></td>
                <td><img src="{{asset('img/borrar.png') }}" width="30" height="30" style="cursor: pointer;" class="borrar" /></td>
            </tr>
        @endforeach
        
    @endif
    <tr class="nuevaLineaProducto"> </tr>
     <tr><td><img src="{{asset('img/agregar.png') }}" width="30" height="30" style="cursor: pointer;"class="agregarProducto"/></td></tr>
    </tbody>
    @include('elementosComunes.cierreTabla')
    @include('elementosComunes.aperturaTitulo')
    <h3>
        Llegada de Insumos
    </h3>
    @include('elementosComunes.cierreTitulo')
    @include('elementosComunes.aperturaTabla')
    <thead>
    <tr>
        <th>Código</th>
        <th>Insumo</th>
        <th>Cantidad</th>
        <th></th>
        <th></th>

    </tr>
    <tr></tr>
    </thead>
    <tbody>
    <?php unset($codigo);unset($nombre);unset($cantidad);?>
    @foreach($planificaciones as $value)
        @if($value['fecha']==$fecha)
            @foreach($value["insumos"] as $v)
                <?php
                $codigo[]=$v['codigo'];
                $nombre[]=$v['nombre'];
                $cantidad[]=$v['cantidad'].$v['tipoUnidad'];
                $id[]=$v["movimiento_id"];
                ?>
            @endforeach
        @endif
    @endforeach
    @if(isset($codigo))
        @foreach($codigo as $k=>$a)

            <tr>

                <td><?=$codigo[$k];?></td>
                <td><?=$nombre[$k];?></td>
                <td><?=$cantidad[$k];?></td>
               
                 <td><img src="{{asset('img/modificar.png') }}" width="20" height="20" style="cursor: pointer;" /></td>
                <td><img src="{{asset('img/borrar.png') }}" width="30" height="30" style="cursor: pointer;"/></td>
            </tr>

        @endforeach

    @endif
    <tr><td><img  src="{{asset('img/agregar.png') }}" width="30" height="30" style="cursor: pointer;" class="agregarInsumo" /></td></tr>
     <tr class="nuevaLineaInsumo">
           
        </tr>
    </tbody>
    @include('elementosComunes.cierreTabla')
    <div id="imgmodificar">
    <img src="{{asset('img/modificar.png') }}" width="20" height="20" style="cursor: pointer;" hidden="true"  />
    </div>
     <div id="imgborrar">
    <img src="{{asset('img/borrar.png') }}" width="30" height="30" style="cursor: pointer;" hidden="true" />
    </div>
    

    
    <form id="formProduccion">
        <button class="btn btn-primary"> Guardar </button>
    </form>
    </body>
@endsection
@section('script')
<script type="text/javascript" src="{{asset('js/planificacion/addPlanificacion.js')}}"></script>
      <script type="text/javascript" src="{{asset('js/planificacion/guardarPlanificacion.js')}}"></script>
       <script type="text/javascript" src="{{asset('js/planificacion/modificarPlanificacion.js')}}"></script>
       <script type="text/javascript" src="{{asset('js/planificacion/borrarPlanificacion.js')}}"></script>
@endsection