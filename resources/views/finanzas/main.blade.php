@extends('layout.main')
@include('layout.menu_finanzas')
@section('cabecera')
    Dashboard Finanzas
@endsection
@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('finanzas.index')}}"><i class="fas fa-tachometer-alt"></i> Finanzas</a></li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection
@section('content')

<div class="row">
    
</div>
@endsection
@section('scripts')
<script src="{{ asset('template/plugins/chartjs/Chart.min.js') }}"></script>
<!-- <script src="{{ asset('js/almacen/dashboardAlmacen.js')}}"></script> -->
<script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
</script>
@endsection