@extends('layout.main')
@include('layout.menu_logistica')
@section('cabecera')
    Dashboard Logística
@endsection
@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística</a></li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection
@section('content')

<div class="row">
    <div class="col-md-3">
        <!-- small box -->
        <div class="small-box bg-blue">
            <div class="icon">
                <i class="fas fa-file-prescription"></i>
                </div>
                <div class="inner">
                    <h3>{{$cantidad_requerimientos_elaborados}}</h3>
                    <p style="font-size:15px;display:flex;width:20px;">Requerimientos Elaborados</p>
                </div>
                @if(Auth::user()->tieneAplicacion(102))
                <a href="{{route('logistica.gestion-logistica.requerimiento.elaboracion.index')}}" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
                @else
                <a href="#" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
                @endif
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-orange">
            <div class="icon">
                <i class="fas fa-file-invoice"></i>
                </div>
                <div class="inner">
                    <h3>{{$cantidad_ordenes_pendientes}}</h3>
                    <p style="font-size:15px;display:flex;width:20px;">Compras Pendientes </p>
                </div>
                @if(Auth::user()->tieneAplicacion(108))
                <a href="{{route('logistica.gestion-logistica.orden.por-requerimiento.index')}}" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
                @else
                <a href="#" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
                @endif
            <!-- </div> -->
        </div>
        
    </div>
    <div class="col-md-3">
        <div class="small-box bg-teal">
            <div class="icon">
                <i class="fas fa-truck"></i>
                </div>
                <div class="inner">
                    <h3>{{$cantidad_despachos_pendientes}}</h3>
                    <p style="font-size:15px;display:flex;width:20px;">Despachos Pendientes</p>
                </div>
                @if(Auth::user()->tieneAplicacion(80))
                <a href="{{route('logistica.distribucion.despachos.index')}}" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
                @else
                <a href="#" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
                @endif
        </div>
        
    </div>

</div>
<div class="row">
    <div class="col-md-9">
        <div class="row">
            <div class="col-md-4">
                <div style="display:flex;">
                    <p>Seleccione el Filtro: </p>
                    <select name="filtro" onChange="mostrar_tabla();"
                        class="form-control" style="width:200px" required>
                        <!-- <option value="0">Elija una opción</option> -->
                        <option value="1" >Hoy</option>
                        <option value="2" >Semana</option>
                        <option value="3" selected>Mes</option>
                        <option value="4" >Año</option>
                    </select>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">Requerimientos por Estado</div>
                    <!-- <div class="panel-body">
                        <p>...</p>
                    </div> -->
                    <table id="listaEstadosRequerimientos" class="table">
                        <thead></thead>
                        <tbody></tbody>
                    </table>
                </div>
                <a href="{{route('almacen.reportes.saldos.index')}}" >
                    <button type="button" class="btn btn-success" style="display:block;">
                    <i class="fas fa-box-open"></i> Ver Saldos Actuales en Almacén</button>
                </a>
            </div>
            <div class="col-md-8">
                <canvas id="chartRequerimientos" width="600" height="300"></canvas>
            </div>
        </div>
    </div>
</div>
@include('almacen.verRequerimientoEstado')
@endsection
@section('scripts')
<script src="{{ asset('template/plugins/chartjs/Chart.min.js') }}"></script>
<script src="{{ asset('js/almacen/dashboardAlmacen.js')}}"></script>
<script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
</script>
@endsection