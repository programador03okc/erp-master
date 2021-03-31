

@extends('layout.main')
@include('layout.menu_tesoreria')
@section('cabecera')
    Dashboard Tesoreria
@endsection
@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('tesoreria.index')}}"><i class="fas fa-tachometer-alt"></i> Logística y Almacenes</a></li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection
@section('content')

<div class="row">

    <div class="col-md-3">
        <!-- small box -->
        <div class="small-box bg-orange">
            <div class="icon">
                <i class="fas fa-file-prescription"></i>
                </div>
                <div class="inner">
                    <h3>{{ $pagos_pendientes }}</h3>
                    <p style="font-size:15px;display:flex;width:20px;">Pagos pendientes</p>
                </div>
                <!-- @if(Auth::user()->tieneAplicacion(102)) -->
                <a href="{{route('tesoreria.pagos.index')}}" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
                <!-- @else
                <a href="#" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
                @endif -->
        </div>
    </div>

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


