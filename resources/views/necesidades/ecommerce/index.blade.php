@extends('layout.main')
@include('layout.menu_necesidades')

@section('option')
@endsection

@section('cabecera')
Lista de pedidos
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('css/usuario-accesos.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('necesidades.index')}}"><i class="fas fa-tachometer-alt"></i> Necesidades</a></li>
    <li class="active">Ecommerce</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="lista_requerimiento">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <div class="box-header">
                    {{-- <h2 class="box-title">Lista de pedidos
                      <small>Pedidos de la pagina </small>
                    </h2> --}}
                    <!-- tools box -->
                    <div class="pull-right box-tools">
                      <a href="{{route('necesidades.ecommerce.crear')}}" class="btn btn-success " title="Nuevo requerimiento">
                        <i class="fa fa-save"></i> Nuevo requerimiento</a>
                    </div>
                    <!-- /. tools -->
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="mytable table table-hover table-condensed table-bordered table-okc-view"" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center"></th>
                                    <th class="text-center" style="width:2%">Prio.</th>
                                    <th class="text-center" style="width:10%">Código</th>
                                    <th class="text-center" style="width:20%">Concepto</th>
                                    <th class="text-center" style="width:8%">Fecha registro</th>
                                    <th class="text-center">Fecha entrega</th>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-center" style="width:10%">Empresa - sede</th>
                                    <th class="text-center">Grupo</th>
                                    <th class="text-center">División</th>
                                    <th class="text-center">Proyecto/presupuesto</th>
                                    <th class="text-center">Monto Subtotal</th>
                                    <th class="text-center">Monto Total</th>
                                    <th class="text-center">Creado por</th>
                                    <th class="text-center" style="width:5%;">Estado</th>
                                    <th class="text-center" style="width:8%">Ordenes</th>
                                    <th class="text-center" style="width:8%">Acción</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


@endsection

@section('scripts')
<script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
<script src="{{ asset('js/util.js')}}"></script>
<script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>



@endsection
