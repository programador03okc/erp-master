@extends('layout.main')
@include('layout.menu_necesidades')

@section('option')
@endsection

@section('cabecera')
Revisar/aprobar
@endsection

@section('estilos')
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('necesidades.index')}}"><i class="fas fa-tachometer-alt"></i> Necesidades</a></li>
    <li class="active">Revisar/aprobar</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="lista_documentos_para_revisar_aprobar">
    <div class="row">
        <div class="col-md-12">
            <fieldset class="group-table">
                <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="listaDocumetosParaRevisarAprobar" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th class="text-center">Prio.</th>
                            <th class="text-center" >Tipo doc.</th>
                            <th class="text-center">Código</th>
                            <th class="text-center" >Concepto</th>
                            <th class="text-center" >Tipo Req.</th>
                            <th class="text-center">Fecha registro</th>
                            <th class="text-center" >Empresa</th>
                            <th class="text-center">Sede</th>
                            <th class="text-center">Grupo</th>
                            <th class="text-center">División</th>
                            <th class="text-center">Monto Total</th>
                            <th class="text-center">Creado por</th>
                            <th class="text-center">Estado / Aprob.</th>
                            <th class="text-center" style="width:10%">Acción</th>
                        </tr>
                    </thead>
                </table>
            </fieldset>
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

<script src="{{ asset('js/necesidades/RevisarAprobarDocumento.js')}}?v={{filemtime(public_path('js/necesidades/RevisarAprobarDocumento.js'))}}"></script>


<script>
    function updateUM(val) {
        val.options[val.selectedIndex].setAttribute("selected", "");
    }

    var gruposUsuario = JSON.parse('{!!$gruposUsuario!!}');

    $(document).ready(function() {
        seleccionarMenu(window.location);

        const revisarAprobarDocumentoView= new RevisarAprobarDocumentoView();

        revisarAprobarDocumentoView.listarDocumentosPendientesParaRevisarAprobar();
        revisarAprobarDocumentoView.initializeEventHandler();
    });

</script>
@endsection