<!DOCTYPE html>
<html>

<head>
<title>Planta Piloto</title>
  <meta charset="utf-8">

  <meta name="viewport" content="width=device-width, initial-scale=1">

<script src="{{ asset('jquery/jquery-3.3.1.min.js') }}"></script>
<script src="{{ asset('jquery/datatables.min.js') }}"></script> 



<link rel="stylesheet" type="text/css" href="{{ asset('css/dataTables.bootstrap4.css') }}">
<script src="{{ asset('js/dataTable.js') }}"></script>

  <script type="text/javascript" src="{{asset('ajax/sendNombreProducto.js')}}"></script>
  @yield('script')
 {{--  <link rel="stylesheet" type="text/css" href="{{ asset('css/dataTables.bootstrap4.css') }}"> 
 <script type="text/javascript" src="{{asset('js/nav/navbar.js')}}"></script>--}} 
  <link href="{{ asset('css/programa.produccion.semanal.css') }}" rel="stylesheet" type="text/css">


  <!--
    estilos para navBar
  -->
  

  <!--

  -->
  <link rel="stylesheet" href="{{ asset('css/stock.css') }}" type="text/css">


  <div class="fixed-top">
@include('nav.navbar')

  </div>

   </head>
@include('layouts.alerts')
@include('layouts.errors')
<main role="main">
<body class="container jumbotron">



	
  @yield('section')

 
  <!--REFERENCIA A ARCHIVO CON SCRIPTS JS-->
  
  
  
  <!--<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>-->
  <script src="{{asset('jquery/popper.min.js')}}" ></script>
  <script src="{{asset('jquery/bootstrap.min.js')}}" ></script>
 {{--<script type="text/javascript" src="{{asset('js/nav/navbar.js')}}"></script>--}}
</body>
</main>
</html>