
@extends('layout.main')
@include('layout.menu_config')
@section('cabecera')
    Configuración
@endsection
@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('configuracion.index')}}"><i class="fas fa-tachometer-alt"></i> Configuración</a></li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection
@section('content')
@endsection