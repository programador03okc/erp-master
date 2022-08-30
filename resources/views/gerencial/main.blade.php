@extends('layout.main')
@include('layout.menu_gerencial')
@section('cabecera')
    Dashboard Gerencial
@endsection
@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('gerencial.index')}}"><i class="fas fa-tachometer-alt"></i> Gerencial</a></li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection
@section('content')

    <div class="row">
    </div>
@endsection
@section('scripts')
<script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
</script>
@endsection
