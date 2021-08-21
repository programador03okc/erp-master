@extends('layout.main')
@include('layout.menu_cas')
@section('cabecera')
Dashboard Servicios CAS
@endsection
@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('cas.index')}}"><i class="fas fa-tachometer-alt"></i> Servicios CAS</a></li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection
@section('content')

<div class="row">

    <div class="col-md-3">
        <div class="small-box bg-blue">
            <div class="icon">
                <i class="fas fa-file-prescription"></i>
            </div>
            <div class="inner">
                <h3>0</h3>
                <p style="font-size:15px;display:flex;width:20px;">Transformaciones pendientes</p>
            </div>
            <a href="{{route('cas.customizacion.gestion-customizaciones.index')}}" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>

</div>

@endsection