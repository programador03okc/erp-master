@extends('layout.main')
@include('layout.menu_cas')

@section('cabecera')
Productos
@endsection

@section('estilos')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
<style>
    .d-none{
        display: none;
    }
</style>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('cas.index')}}"><i class="fas fa-tachometer-alt"></i> Servicios CAS</a></li>
    <li>Garantías</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')

<div class="page-main" type="incidencia">

    {{-- <form id="form-incidencia"> --}}
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="box box-danger">
                    <div class="box-header with-border">

                        <h3 class="box-title">Listado de productos</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-success" data-button="nuevo" type="button"><i class="fa fa-plus"></i> Nuevo Modelo</button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="mytable table table-condensed table-bordered table-okc-view"
                                            id="lista-productos" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th hidden></th>
                                            <th>codigo</th>
                                            <th>descripcion</th>
                                            <th width="70px">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot></tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    {{-- </form> --}}
</div>
<div id="nuevo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('cas.garantias.producto.guardar')}}" method="POST" data-form="guardar" >
                <div class="modal-header">
                    <h5 class="modal-title" id="my-modal-title">Nuevo Producto</h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="descripcion_nuevo">Descripcion</label>
                                <input id="descripcion_nuevo" class="form-control" type="text" name="descripcion" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="editar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('cas.garantias.producto.actualizar')}}" method="POST" data-form="actualizar" >
                <input type="hidden" name="id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="my-modal-title">Editar Producto</h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="descripcion_editar">Descripcion</label>
                                <input id="descripcion_editar" class="form-control" type="text" name="descripcion" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@include('cas.incidencias.incidenciaModal')
@include('cas.incidencias.salidasVentaModal')
@include('cas.incidencias.seriesProductosModal')
@include('almacen.distribucion.ordenDespachoContacto')
@include('almacen.distribucion.agregarContacto')
@include('publico.ubigeoModal')
@endsection

@section('scripts')
<script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>
<script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
<script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>

<script src="{{ asset('js/cas/cas_producto/producto.js') }}"></script>


<script>
    const route_listar = "{{ route('cas.garantias.producto.listar') }}";
    const route_editar = "{{ route('cas.garantias.producto.editar') }}";
    const route_eliminar = "{{ route('cas.garantias.producto.eliminar') }}";
    $(document).ready(function() {

        seleccionarMenu(window.location);
        $(".select2").select2({
            tags: true
        });
    });
</script>
@endsection
