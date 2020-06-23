@extends('layout.head')
@include('layout.menu_logistica')
@section('cabecera')
    Dashboard Logística
@endsection
@section('content')

<div class="row">

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
</div>
@endsection
@section('scripts')
@endsection