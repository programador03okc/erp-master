@extends('layout.main')
@include('layout.menu_logistica')


@section('cabecera')
    Listado de proveedores
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística</a></li>
    <li>Proveedores</li>
    <li class="active">Listado</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="lista_proveedores">
    <div class="row">
        <div class="col-md-12">
            <fieldset class="group-table">
                <div id="form-listaProveedores">
                    <button type="button" id="btnCrearProveedor" class="btn btn-sm btn-success pull-right handleClickNuevoProveedor"  style="margin-left:5px;" ><i class="fas fa-new"></i>Nuevo</button>
                    <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="listaProveedores" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:5%">Tipo doc.</th>
                                <th class="text-center" style="width:5%">Doc. identidad</th>
                                <th class="text-center" style="width:20%">Razon social</th>
                                <th class="text-center" style="width:10%">Tipo empresa</th>
                                <th class="text-center" style="width:8%">País</th>
                                <th class="text-center" style="width:10%">Ubigeo</th>
                                <th class="text-center" style="width:20%">Dirección</th>
                                <th class="text-center" style="width:8%">Teléfono</th>
                                <th class="text-center" style="width:8%">Estado</th>
                                <th class="text-center" style="width:8%">Acción</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </fieldset>
        </div>
    </div>
</div>


@include('logistica.gestion_logistica.proveedores.modal_ver_proveedor')
@include('logistica.gestion_logistica.proveedores.modal_proveedor')
@include('logistica.gestion_logistica.proveedores.modal_agregar_cuenta_bancaria')
@include('logistica.gestion_logistica.proveedores.modal_agregar_adjunto_proveedor')
@include('logistica.gestion_logistica.proveedores.modal_agregar_contacto')
@include('logistica.gestion_logistica.proveedores.modal_agregar_establecimiento')
@include('publico.ubigeoModal')


@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
 
    <script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script>
    <script src="{{('/js/logistica/proveedores/listarProveedorView.js?v=3')}}"></script>
    <script src="{{('/js/logistica/proveedores/ProveedorController.js?v=3')}}"></script>
    <script src="{{('/js/logistica/proveedores/ProveedorModel.js?v=3')}}"></script>
    <script src="{{ asset('js/publico/ubigeoModal.js?v=3')}}"></script>



    <script>
        $(document).ready(function() {
            seleccionarMenu(window.location);
            const proveedorModel = new ProveedorModel();
            const proveedorController = new ProveedorCtrl(proveedorModel);
            const listarProveedorView = new ListarProveedorView(proveedorController);
            
            listarProveedorView.mostrar();
            listarProveedorView.initializeEventHandler();
                
        });
    </script>
@endsection