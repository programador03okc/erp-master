@extends('layout.main')
@include('layout.menu_almacen')
@section('cabecera')
Dashboard Almacén
@endsection
@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('almacen.index')}}"><i class="fas fa-tachometer-alt"></i> Almacenes</a></li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection
@section('content')

<div class="row">
    <div class="col-md-3">
        <div class="small-box bg-orange">
            <div class="icon">
                <i class="fas fa-sign-in-alt"></i>
            </div>
            <div class="inner">
                <h3>{{$cantidad_ingresos_pendientes}}</h3>
                <p style="font-size:15px;display:flex;width:20px;">Ingresos Pendientes</p>
            </div>
            @if(Auth::user()->tieneAplicacion(82))
            <a href="{{route('almacen.movimientos.pendientes-ingreso.index')}}" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
            @else
            <a href="#" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
            @endif
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-blue">
            <div class="icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <div class="inner">
                <h3>{{$cantidad_salidas_pendientes}}</h3>
                <p style="font-size:15px;display:flex;width:20px;">Salidas Pendientes</p>
            </div>
            @if(Auth::user()->tieneAplicacion(83))
            <a href="{{route('almacen.movimientos.pendientes-salida.index')}}" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
            @else
            <a href="#" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
            @endif
            <!-- </div> -->
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-green">
            <div class="icon">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="inner">
                <h3>{{$cantidad_transferencias_pendientes}}</h3>
                <p style="font-size:15px;display:flex;width:20px;">Transferencias Pendientes</p>
            </div>
            @if(Auth::user()->tieneAplicacion(86))
            <a href="{{route('almacen.transferencias.gestion-transferencias.index')}}" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
            @else
            <a href="#" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
            @endif
            <!-- </div> -->
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        {{-- <div style="display:flex;">
            <p>Seleccione el Filtro: </p>
            <select name="filtro" onChange="mostrar_tabla();" class="form-control" style="width:200px" required>
                <!-- <option value="0">Elija una opción</option> -->
                <option value="1">Hoy</option>
                <option value="2">Semana</option>
                <option value="3" selected>Mes</option>
                <option value="4">Año</option>
            </select>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">Requerimientos por Estado</div>
            <table id="listaEstadosRequerimientos" class="table">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div> --}}
        <a href="{{route('almacen.reportes.saldos.index')}}">
            <button type="button" class="btn btn-success" style="display:block;">
                <i class="fas fa-box-open"></i> Ver Saldos Actuales en Almacén</button>
        </a>
    </div>
    <div class="col-md-6">
        <canvas id="chartRequerimientos" width="600" height="300"></canvas>
    </div>

</div>
<!-- </section> -->
@include('almacen.verRequerimientoEstado')
@endsection
@section('scripts')
{{-- <script src="{{ asset('template/plugins/chartjs/Chart.min.js') }}"></script> --}}
{{-- <script src="{{ asset('js/almacen/dashboardAlmacen.js')}}"></script> --}}
<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
    });
</script>
@endsection