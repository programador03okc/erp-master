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
                <form id="form-listaProveedores" type="register">
                    <button type="button" id="btnCrearProveedor" class="btn btn-success pull-right" ><i class="fas fa-new"></i>Nuevo</button>
                    <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="listarProveedores" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:2%">Código</th>
                                <th class="text-center" style="width:20%">Razon Social</th>
                                <th class="text-center" style="width:5%">Documento</th>
                                <th class="text-center" style="width:10%">Condición</th>
                                <th class="text-center" style="width:10%">Tipo empresa</th>
                                <th class="text-center" style="width:10%">Ubigeo</th>
                                <th class="text-center">Dirección</th>
                                <th class="text-center" style="width:10%">Contacto</th>
                                <th class="text-center">Teléfono</th>
                                <th class="text-center">Cuenta bancaria</th>
                                <th class="text-center" style="width:5%;">Estado</th>
                                <th class="text-center" style="width:8%">Acción</th>
                            </tr>
                        </thead>
                    </table>
                </form>
            </fieldset>
        </div>
    </div>
</div>


 

@endsection

@section('scripts')
    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
 
    <script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script>
    <script src="{{('/js/logistica/proveedores/modal_lista_proveedores.js')}}"></script>
    <script src="{{('/js/logistica/proveedores/tab_archivos_adjuntos_proveedor.js')}}"></script>
    <script src="{{('/js/logistica/proveedores/tab_contactos.js')}}"></script>
    <script src="{{('/js/logistica/proveedores/tab_cuentas_bancarias.js')}}"></script>
    <script src="{{('/js/logistica/proveedores/tab_establecimientos.js')}}"></script>
    <script src="{{('/js/logistica/proveedores/tab_proveedor.js')}}"></script>
    <script src="{{('/js/logistica/proveedores/gestionar_proveedores.js')}}"></script>
    <script src="{{('/js/publico/consulta_sunat.js')}}"></script>

@endsection