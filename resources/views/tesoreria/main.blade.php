

@extends('layout.main')
@include('layout.menu_tesoreria')
@section('cabecera')
    Dashboard Tesoreria
@endsection
@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('tesoreria.index')}}"><i class="fas fa-tachometer-alt"></i> Tesorería</a></li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection
@section('content')

<div class="row">

</div>


@endsection
@section('scripts')
<script src="{{ asset('template/plugins/chartjs/Chart.min.js') }}"></script>
 <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
</script>
@endsection


