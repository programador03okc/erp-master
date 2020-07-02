@extends('layout.main')
@include('layout.menu_logistica')
@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
    Solicitud de Equipo
@endsection

@section('content')
<div class="page-main" type="equi_sol">
    <legend><h2>Solicitud de Equipo</h2></legend>
    <form id="form-equi_sol" type="register" form="formulario">
        <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
        <input class="oculto" name="id_solicitud" primary="ids">
        <div class="row">
            <div class="col-md-2">
                <h5>Código</h5>
                <input type="text" class="form-control" name="codigo" disabled="true">
            </div>
            <div class="col-md-2">
                <h5>Fecha Solicitud</h5>
                <input type="date" class="form-control activation" name="fecha_solicitud" value="<?=date('Y-m-d');?>" disabled="true">
            </div>
            <div class="col-md-4">
                <h5>Fecha Inicio / Fecha Fin</h5>
                <div style="display:flex;">
                    <input type="date" name="fecha_inicio" class="form-control activation" value="<?=date('Y-m-d');?>" disabled="true"/>
                    <input type="date" name="fecha_fin" class="form-control activation" value="<?=date('Y-m-d');?>" onBlur="valida_fecha(this.value);" disabled="true"/>
                </div>
            </div>
            <div class="col-md-4">
                <h5>Solicitado por</h5>
                <select class="form-control activation js-example-basic-single" name="id_trabajador" disabled="true">
                    <option value="0">Elija una opción</option>
                    @foreach ($trabajadores as $det)
                        <option value="{{$det->id_trabajador}}">{{$det->nombre_trabajador}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <h5>Empresa</h5>
                <select name="empresa" id="empresa" class="form-control activation"
                    onChange="change_empresa();" required>
                    <option value="">Elija una opción</option>
                    @foreach ($empresa as $emp)
                        <option value="{{$emp->id_empresa}}">{{ $emp->razon_social}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <h5>Area</h5>
                <input type="hidden" class="form-control" name="id_grupo">
                <input type="hidden" class="form-control" name="id_area" >
                <div class="input-group-okc">
                    <input type="text" class="form-control" name="nombre_area" onChange="cambiarArea();" readOnly>
                    <div class="input-group-append">
                        <button type="button" class="input-group-text activation" onclick="modal_area();">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <h5>Concepto</h5>
                <input type="text" class="form-control activation" name="observaciones" disabled="true">
            </div>
            {{-- <div class="col-md-2">
                <h5>Empresa</h5>
                <select class="form-control activation" name="id_empresa" onBlur="cambiarEmpresa(this.value);" disabled="true">
                    <option value="0">Elija una opción</option>
                    @foreach ($empresa as $empresa)
                        <option value="{{$empresa->id_empresa}}">{{$empresa->razon_social}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <h5>Sede</h5>
                <select class="form-control activation" name="id_sede" onBlur="cambiarSede(this.value);" disabled="true">
                    <option value="0" disabled>Elija una opción</option>
                </select>
            </div>
            <div class="col-md-4">
                <h5>Grupo</h5>
                <select class="form-control activation" name="id_grupo" onBlur="cambiarGrupo(this.value);" disabled="true">
                    <option value="0" disabled>Elija una opción</option>
                </select>
            </div>
            <div class="col-md-4">
                <h5>Área</h5>
                <select class="form-control activation" name="id_area" onBlur="cambiarArea(this.value);" disabled="true">
                    <option value="0" disabled>Elija una opción</option>
                </select>
            </div> --}}
        </div>
        <div class="row">
            <div class="col-md-2">
                <h5>Categoría de Equipo</h5>
                <select class="form-control activation" name="id_categoria" disabled="true">
                    <option value="0">Elija una opción</option>
                    @foreach ($categorias as $cat)
                        <option value="{{$cat->id_categoria}}">{{$cat->descripcion}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <h5>Cantidad</h5>
                <input type="number" class="form-control activation" name="cantidad" disabled="true"/>
            </div>
            <div class="col-md-8 proyecto oculto">
                <h5>Proyecto</h5>
                <select class="form-control js-example-basic-single activation" name="id_proyecto" disabled="true">
                    <option value="0">Elija una opción</option>
                    @foreach ($proyectos as $proy)
                        <option value="{{$proy->id_proyecto}}">{{$proy->descripcion}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row" style="margin-bottom:0;">
            <div class="col-md-3">
                <input type="text" name="estado" class="oculto">
                <h5 id="estado_doc">Estado: <label></label></h5>
            </div>
            <div class="col-md-3">
                <h5 id="fecha_registro">Fecha Registro: <label></label></h5>
            </div>
            <div class="col-md-3">
                <h5 id="registrado_por">Registrado por: <label></label></h5>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
            {{-- <strong><strong>Flujos de Aprobación</strong></strong> --}}
                <fieldset class="group-importes"><legend><h6>Flujos de Aprobación</h6></legend>
                    <input type="text" name="id_doc_aprob" class="oculto">
                    <table class="mytable table table-condensed table-bordered table-group" 
                        id="listaSolFlujos" style="margin-bottom:0px;"  width="100%">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Fecha VoBo</th>
                                <th>VoBo</th>
                                <th>Rol</th>
                                <th>Usuario</th>
                                <th width="300px">Observación</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </fieldset>
            </div>
        </div>
    </form>
</div>
@include('equipo.equi_solModal')
@include('publico.modal_area')
@include('publico.obs')

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
    <script src="{{('/js/equipo/equi_sol.js')}}"></script>
    <script src="{{('/js/equipo/equi_solModal.js')}}"></script>
    <script src="{{('/js/publico/modal_area.js')}}"></script>
    <script src="{{('/js/publico/obs.js')}}"></script>
@endsection