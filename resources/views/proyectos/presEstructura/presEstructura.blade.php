@extends('layout.head')
@include('layout.menu_proyectos')
@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
Estructura del Presupuesto
@endsection

@section('content')
<div class="page-main" type="presEstructura">
    <legend class="mylegend">
        <h2>Estructura del Presupuesto</h2>
        <ol class="breadcrumb">
            <li><label id="codigo"></label></li>
        </ol>
    </legend>
    <form id="form-presEstructura" type="register" form="formulario">
        <div class="row">
            <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
            <input type="text" class="oculto" name="id_presup" primary="ids">
            <div class="col-md-2">
                <h5>Fecha Emisión</h5>
                <input type="date" class="form-control activation" name="fecha_emision" value="<?=date('Y-m-d');?>"  disabled="true"/>
            </div>
            <div class="col-md-10">  
                <h5>Descripción</h5>
                <input type="text" class="form-control activation" name="descripcion"/>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <h5 id="fecha_registro">Fecha Registro: <label></label></h5>
            </div>
            <div class="col-md-5">
                <h5 id="responsable">Registrado por: <label></label></h5>
            </div>
            <div class="col-md-3">
                <input type="text" name="id_estado" hidden/>
                <h5 id="des_estado">Estado: <label></label></h5>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%"
                id="listaPresupuesto">
                <thead>
                    <tr>
                        <th></th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Relacionado</th>
                        <th width="15%">
                            <i class="fas fa-plus-square icon-tabla green boton" 
                            data-toggle="tooltip" data-placement="bottom" 
                            title="Agregar Título" onClick="agregar_primer_titulo();"></i>
                            {{-- <i class="fas fa-archive icon-tabla orange boton" 
                            data-toggle="tooltip" data-placement="bottom" 
                            title="Agregar ACU" onClick="agregar_acus_cd();"></i> --}}
                        </th>
                        <th hidden>padre</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@include('proyectos.presEstructura.presEstructuraModal')
@include('proyectos.presEstructura.partidaEstructura')
@include('proyectos.presEstructura.pardetModal')
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

    <script src="{{('/js/proyectos/presupuesto/presEstructura.js')}}"></script>
    <script src="{{('/js/proyectos/presupuesto/presEstructuraModal.js')}}"></script>
    <script src="{{('/js/proyectos/presupuesto/partidaEstructura.js')}}"></script>
    <script src="{{('/js/proyectos/presupuesto/pardetModal.js')}}"></script>
@endsection