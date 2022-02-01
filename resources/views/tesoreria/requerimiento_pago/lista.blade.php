@extends('layout.main')
@include('layout.menu_necesidades')

@section('option')
@endsection

@section('cabecera')
Listado de requerimientos de pago
@endsection

@section('estilos')
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('necesidades.index')}}"><i class="fas fa-tachometer-alt"></i> Necesidades</a></li>
    <li>Requerimientos de pago</li>
    <li class="active">Listado</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="lista_requerimiento_pago">
    <div class="row">
        <div class="col-md-12">
            <fieldset class="group-table">
                    <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="ListaRequerimientoPago" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center"></th>
                                <th class="text-center" style="width:2%">Prio.</th>
                                <th class="text-center" style="width:8%">Código</th>
                                <th class="text-center" style="width:20%">Concepto</th>
                                <th class="text-center" style="width:15%">Tipo Req.</th>
                                <th class="text-center" style="width:8%">Fecha registro</th>
                                <th class="text-center" style="width:10%">Empresa</th>
                                <th class="text-center" style="width:10%">Sede</th>
                                <th class="text-center">Grupo</th>
                                <th class="text-center">División</th>
                                <th class="text-center">Monto Total</th>
                                <th class="text-center">Creado por</th>
                                <th class="text-center" style="width:5%;">Estado</th>
                                <th class="text-center" style="width:10%">Acción</th>
                            </tr>
                        </thead>
                    </table>


            </fieldset>
        </div>
    </div>
</div>


<div class="hidden" id="divOculto">
    <select id="selectUnidadMedida"  onchange="updateUM(this);">
        @foreach ($unidadesMedida as $unidad)
        <option value="{{$unidad->id_unidad_medida}}">{{$unidad->descripcion}}</option>
        @endforeach
    </select>
</div>

@include('tesoreria.requerimiento_pago.modal_requerimiento_pago')
@include('tesoreria.requerimiento_pago.modal_lista_cuadro_presupuesto')
@include('logistica.requerimientos.modal_partidas')
@include('logistica.requerimientos.modal_centro_costos')
@include('tesoreria.requerimiento_pago.modal_adjuntar_archivos_requerimiento_pago')
@include('tesoreria.requerimiento_pago.modal_adjuntar_archivos_requerimiento_pago_detalle')
@include('logistica.gestion_logistica.proveedores.modal_lista_proveedores')
@include('logistica.gestion_logistica.proveedores.modal_cuentas_bancarias_proveedor')
@include('logistica.gestion_logistica.proveedores.modal_agregar_cuenta_bancaria_proveedor')

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

<script src="{{ asset('js/Tesoreria/ListarRequerimientoPagoView.js')}}?v={{filemtime(public_path('js/Tesoreria/ListarRequerimientoPagoView.js'))}}"></script>
<script src="{{('/js/logistica/proveedores/listaProveedoresModal.js')}}?v={{filemtime(public_path('/js/logistica/proveedores/listaProveedoresModal.js'))}}"></script>
<script src="{{('/js/logistica/proveedores/cuentasBancariasProveedor.js')}}?v={{filemtime(public_path('/js/logistica/proveedores/cuentasBancariasProveedor.js'))}}"></script>


<script>
function updateUM(val){
  val.options[val.selectedIndex].setAttribute("selected","");
  
}
    var gruposUsuario = JSON.parse('{!!$gruposUsuario!!}');

    $(document).ready(function() {
        seleccionarMenu(window.location);

        const listarRequerimientoPagoView = new ListarRequerimientoPagoView();
        
        listarRequerimientoPagoView.mostrarListaRequerimientoPago('ALL');
        
        listarRequerimientoPagoView.initializeEventHandler();
 
    });


    // window.onload = function() {
    //     listarRequerimientoView.mostrar('ALL');
    // };

</script>
@endsection