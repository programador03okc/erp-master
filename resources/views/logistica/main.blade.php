@extends('layout.main')
@include('layout.menu_logistica')
@section('cabecera')
    Dashboard Logística
@endsection
@section('content')

<!-- <div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fas fa-tachometer-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Requerimientos</span>
                <span class="info-box-text">Generados</span>
                <span class="info-box-number">{{$cantidad_requerimientos_generados}}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fas fa-tachometer-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Requerimientos</span>
                <span class="info-box-text">Aprobados</span>
                <span class="info-box-number">{{$cantidad_requerimientos_aprobados}}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fas fa-tachometer-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Requerimientos</span>
                <span class="info-box-text">Observados</span>
                <span class="info-box-number">{{$cantidad_requerimientos_observados}}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fas fa-tachometer-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Requerimientos</span>
                <span class="info-box-text">Anulados</span>
                <span class="info-box-number">{{$cantidad_requerimientos_anulados}}</span>
            </div>
        </div>
    </div>
</div> -->
<div class="row">
<div class="col-md-3">
        <!-- small box -->
        <div class="small-box bg-blue">
            <div class="icon">
                <i class="fas fa-file-prescription"></i>
                </div>
                <div class="inner">
                    <h3></h3>
                    <p style="font-size:15px;display:flex;width:20px;">Elaborar Requerimientos</p>
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
                        <h3></h3>
                        <p style="font-size:15px;display:flex;width:20px;">Generar Orden </p>
                    </div>
                    @if(Auth::user()->tieneAplicacion(104))
                    <a href="{{route('logistica.gestion-logistica.orden.por-requerimiento.index')}}" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
                    @else
                    <a href="#" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
                    @endif
                <!-- </div> -->
            </div>
        </div>
</div>
@endsection
@section('scripts')
<script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
</script>
@endsection