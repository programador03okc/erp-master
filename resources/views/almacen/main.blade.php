@extends('layout.main')
@include('layout.menu_logistica')
@section('cabecera')
    Dashboard Almacén
@endsection
@section('content')
<!-- <section class="content"> -->

    <div class="row">
        <div class="col-md-3">
              <!-- small box -->
            <div class="small-box bg-blue">
                <div class="icon">
                    <i class="fas fa-truck"></i>
                    </div>
                    <div class="inner">
                        <h3>{{$cantidad_despachos_pendientes}}</h3>
                        <p style="font-size:15px;display:flex;width:20px;">Despachos Pendientes</p>
                    </div>
                    @if(Auth::user()->tieneAplicacion(80))
                    <a href="{{route('logistica.almacen.distribucion.despachos.index')}}" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
                    @else
                    <a href="#" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
                    @endif
            </div>
        </div>
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
                    <a href="{{route('logistica.almacen.movimientos.pendientes-ingreso.index')}}" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
                    @else
                    <a href="#" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
                    @endif
                <!-- </div> -->
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-teal">
                <div class="icon">
                    <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <div class="inner">
                        <h3>{{$cantidad_salidas_pendientes}}</h3>
                        <p style="font-size:15px;display:flex;width:20px;">Salidas Pendientes</p>
                    </div>
                    @if(Auth::user()->tieneAplicacion(83))
                    <a href="{{route('logistica.almacen.movimientos.pendientes-salida.index')}}" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
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
                <a href="{{route('logistica.almacen.transferencias.gestion-transferencias.index')}}" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
                @else
                <a href="#" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
                @endif
                <!-- </div> -->
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
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
        </div>
        <div class="col-md-6">
            <canvas id="chartRequerimientos" width="600" height="300"></canvas>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-purple">
                <div class="icon">
                    <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div class="inner">
                        <h3>{{$cantidad_pagos_pendientes}}</h3>
                        <p style="font-size:15px;display:flex;width:20px;">Confirmaciones de Pago</p>
                    </div>
                    @if(Auth::user()->tieneAplicacion(79))
                    <a href="{{route('logistica.almacen.pagos.confirmacion-pagos.index')}}" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
                    @else
                    <a href="#" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
                    @endif
            </div>
        </div>
    </div>
<!-- </section> -->
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
