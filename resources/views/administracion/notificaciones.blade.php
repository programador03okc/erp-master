@extends('layout.main')
@include('layout.menu_logistica')
@section('option')
@endsection

@section('cabecera')
    Notificaciones
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('administracion.index')}}"><i class="fas fa-tachometer-alt"></i> Logistica</a></li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="orden-requerimiento">
        <div class="row">
            <div class="col-md-12">
                <div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#bandejaNotificaciones" onClick="vista_extendida();" aria-controls="bandejaNotificaciones" role="tab" data-toggle="tab">Bandeja Entrada</a></li>
                        <li role="presentation"><a href="#notificacionesLeidas" onClick="vista_extendida();" aria-controls="notificacionesLeidas" role="tab" data-toggle="tab">Leidas</a></li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="bandejaNotificaciones">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <form id="form-bandejaNotificaciones" type="register">
                                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                            id="listaNotificacionesNoLeidas">
                                                <thead>
                                                    <tr>
                                                    <th width="5%">#</th>
                                                    <th width="70%">Mensaje</th>
                                                    <th width="20%">Fecha</th>
                                                    <th width="5%">ACCIONES</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </form>
                                    </div>
                                </div>
                        </div>

                        <div role="tabpanel" class="tab-pane" id="notificacionesLeidas">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <form id="form-notificacionesLeidas" type="register">
                                            <table class="mytable table table-condensed table-bordered table-okc-view" 
                                            id="listaNotificacionesLeidas">
                                                <thead>
                                                    <tr>
                                                    <th width="5%">#</th>
                                                    <th width="70%">Mensaje</th>
                                                    <th width="20%">Fecha</th>
                                                    <th width="5%">ACCIONES</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </form>
                                    </div>
                                </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
 </div>
@endsection

@section('scripts')
    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script>
    <script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
    <script src="{{('/js/administracion/notificaciones.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
        inicializar(
        //     "{{route('logistica.gestion-logistica.orden.por-requerimiento.requerimientos-pendientes')}}",
        //     "{{route('logistica.gestion-logistica.orden.por-requerimiento.requerimientos-atendidos')}}",
        //     "{{route('logistica.gestion-logistica.orden.por-requerimiento.requerimiento-orden')}}",
        //     "{{route('logistica.gestion-logistica.orden.por-requerimiento.guardar')}}",
        //     "{{route('logistica.gestion-logistica.orden.por-requerimiento.revertir')}}"
        );
        //     tieneAccion('{{Auth::user()->tieneAccion(114)}}','{{Auth::user()->tieneAccion(115)}}');
    });
    </script>
@endsection