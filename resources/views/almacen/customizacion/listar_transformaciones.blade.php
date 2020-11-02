@extends('layout.main')
@include('layout.menu_logistica')

@section('cabecera')
Gestión de Customizaciones
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Almacén</a></li>
  <li>Customización</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="transformaciones">

    <div class="box box-solid">
        <div class="box-body">
            <div class="col-md-12" id="tab-transformaciones" style="padding-left:0px;padding-right:0px;">

                <ul class="nav nav-tabs" id="myTab">
                    <!-- <li class="active"><a data-toggle="tab" href="#cc">Cuadros de Costos</a></li> -->
                    <li class="active"><a data-toggle="tab" href="#htm">Hojas de Transformación</a></li>
                    <!-- <li class=""><a data-toggle="tab" href="#hth">Hojas de Transformación Hijas</a></li> -->
                </ul>

                <div class="tab-content">
                    <!-- <div id="cc" class="tab-pane fade in active">
                        <br>
                        <form id="form-cc" type="register">
                            <div class="row">
                                <div class="col-md-12">

                                    <table class="mytable table table-condensed table-bordered table-okc-view" 
                                        id="listaCuadrosCostos">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th>Código</th>
                                                <th>Oportunidad</th>
                                                <th>Cliente</th>
                                                <th>Estado</th>
                                                <th>Prioridad</th>
                                                <th>Fecha Entrega</th>
                                                <th>Tipo Cuadro</th>
                                                <th>Responsable</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody style="font-size: 11px;"></tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div> -->
                    <div id="htm" class="tab-pane fade in active">
                        <br>
                        <form id="form-htm" type="register">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" 
                                        id="listaTransformacionesMadres">
                                        <thead>
                                            <tr>
                                                <th hidden></th>
                                                <th>Orden Elec.</th>
                                                <th>Cuadro Costo</th>
                                                <th>Oportunidad</th>
                                                <th>Entidad</th>
                                                <th>Fecha Registro</th>
                                                <th>Código</th>
                                                <th>Fecha Entrega</th>
                                                <th>Fecha Inicio</th>
                                                <th>Fecha Proceso</th>
                                                <th>Almacén</th>
                                                <th>Responsable</th>
                                                <th>Observación</th>
                                                <th>Estado</th>
                                                <th width="10%">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody style="font-size: 11px;"></tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@include('almacen.customizacion.transformacionCreate')
@include('almacen.producto.productoModal')
@include('logistica.servicioModal')
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
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>

    <script src="{{ asset('js/almacen/customizacion/listar_transformaciones.js')}}"></script>
    <script src="{{ asset('js/almacen/customizacion/transformacionCreate.js')}}"></script>
    <script src="{{ asset('js/almacen/ubicacion/almacenModal.js')}}"></script>
    <script src="{{ asset('/js/almacen/producto/productoModal.js')}}"></script>
    <script src="{{ asset('/js/logistica/servicioModal.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);

        let gestionCustomizacion = new GestionCustomizacion('{{Auth::user()->tieneAccion(125)}}');

        $('#btnActualizarTransformacionesMadres').on('click',()=>{
            gestionCustomizacion.listarTransformaciones();
        })
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            let tab = $(e.target).attr("href") // activated tab
            // if (tab=='#cc')
            // {
            //     $('#listaCuadrosCostos').DataTable().ajax.reload();
            // }
            // else 
            if (tab=='#htm')
            {
                if ($('#listaTransformacionesMadres tbody tr').length > 0){
                    $('#listaTransformacionesMadres').DataTable().ajax.reload();
                } else {
                    gestionCustomizacion.listarTransformaciones();
                }
            }
            else if (tab=='#hth')
            {
                $('#listaTransformacionesHijas').DataTable().ajax.reload();
            }
         });
    });
    </script>
@endsection