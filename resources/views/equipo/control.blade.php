@extends('layout.head')
@include('layout.menu_logistica')
@section('option')
@endsection

@section('cabecera')
    Registro de Bitácora
@endsection

@section('content')
<div class="page-main" type="control">
    <legend class="mylegend">
        <h2>Registro de Bitácora</h2>
        <ol class="breadcrumb">
            <li>
                {{-- <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                data-placement="bottom" title="Generar Ingreso a Almacén" 
                onClick="generar_ingreso();">Generar Ingreso </button>
                <a onClick="generar_factura();">
                    <input type="button" class="btn btn-primary" data-toggle="tooltip" 
                    data-placement="bottom" title="Generar Factura de Compra" 
                    value="Generar Factura"/>
                </a> --}}
                <button type="button" class="btn btn-danger" data-toggle="tooltip" 
                data-placement="bottom" title="Ver PDF" 
                onClick="open_ver();">Ver <i class="fas fa-file-pdf"></i></button>
                <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                    data-placement="bottom" title="Descargar Excel" 
                    onClick="downloadControlBitacora();">Descargar <i class="fas fa-file-excel"></i></button>
            </li>
        </ol>
    </legend>
    <form id="form-control" type="register" form="formulario">
        <input class="oculto" name="id_control" primary="ids">
        <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Equipo Asignado</h5>
                        <div class="input-group-okc">
                            <input type="text" name="id_asignacion" class="oculto">
                            <input type="text" name="id_solicitud" class="oculto">
                            <input type="text" name="id_equipo" class="oculto">
                            <input type="text" class="form-control" aria-describedby="basic-addon2" 
                                readonly name="equipo_descripcion" disabled="true">
                            <div class="input-group-append">
                                <button type="button" class="input-group-text" id="basic-addon2"
                                    onClick="asignacionModal();">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>              
                    <div class="col-md-4">
                        <h5>Area</h5>
                        <input type="text" class="form-control activation" name="area_descripcion" disabled="true">
                    </div>
                    <div class="col-md-2">
                        <h5>Fecha de Asignación</h5>
                        <input type="date" class="form-control activation" name="fecha_asignacion" disabled="true">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Solicitado por</h5>
                        <input type="text" name="id_trabajador" class="oculto"/>
                        <input type="text" name="trabajador" readOnly class="form-control">
                    </div>
                    <div class="col-md-4">
                        <h5>Fecha Inicio / Fecha Fin</h5>
                        <div style="display:flex;">
                            <input type="date" name="fecha_inicio" class="form-control" disabled="true"/>
                            <input type="date" name="fecha_fin" class="form-control" disabled="true"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <fieldset class="group-importes"><legend><h6>Recorrido Realizado</h6></legend>
                    <table id="detalle" class="table-group">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Fecha</th>
                                <th>Kil.Inicio</th>
                                <th>Kil.Fin</th>
                                <th>Recorrido (Km)</th>
                                <th>Hora Inicio</th>
                                <th>Hora Fin</th>
                                <th>Chofer</th>
                                <th>Descripción del Recorrido</th>
                                <th>Monto (S/)</th>
                                <th>Galones</th>
                                <th>Observaciones</th>
                                <th>
                                    <i class="fas fa-plus-square icon-tabla green boton" 
                                    data-toggle="tooltip" data-placement="bottom" 
                                    title="Agregar" onClick="controlModal();"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </fieldset>
            </div>
        </div>
    </form>
</div>
@include('equipo.controlModal')
@include('equipo.asignacionModal')
@include('publico.fechas')
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
    <script src="{{('/js/equipo/control.js')}}"></script>
    <script src="{{('/js/equipo/asignacionModal.js')}}"></script>
@endsection