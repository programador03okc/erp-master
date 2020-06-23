@extends('layout.head')
@include('layout.menu_config')
@section('option')
    @include('layout.option')
@endsection
@section('cabecera')
    Lista de Usuarios
@endsection
@section('content')
<div class="page-main" type="usuarios">
    <legend class="mylegend">
        <h2>Lista de Usuarios</h2>
        <ol class="breadcrumb">
            <li>
                <button type="submit" class="btn btn-success" data-toggle="tooltip" data-placement="bottom" title="Nuevo Usuario" onClick="crear_usuario();">Nuevo Usuario</button>
            </li>
        </ol>
    </legend>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <table class="mytable table table-striped table-condensed table-bordered" id="listaUsuarios">
                    <thead>
                        <tr>
                            <th></th>
                            <th width="12%">N° Dni</th>
                            <th>Datos del Trabajador</th>
                            <th>Usuario</th>
                            <th width="10%">Acción</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-agregarUsuario">
    <div class="modal-dialog" style="width: 30%;">
        <div class="modal-content">
            <form class="formularioUsu" type="register" id="formPage">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Formulario de Usuarios</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Buscar DNI</h5>
                            <input type="hidden" class="form-control input-sm" name="id_trabajador">
                            <div class="input-group-okc">
                                <input type="text" class="form-control input-sm" name="trab" id="trab" disabled>
                                <div class="input-group-append">
                                    <button type="button" class="input-group-text" id="basic-addon1" onclick="modalTrabajadores();">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Usuario</h5>
                            <input type="text" class="form-control input-sm" name="usuario" id="usuario" required>
                        </div>
                        <div class="col-md-6">
                            <h5>Clave</h5>
                            <input type="password" class="form-control input-sm" name="clave" id="clave" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-trabajador">
    <div class="modal-dialog" style="width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Trabajadores</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-striped table-condensed table-bordered" id="listaTrabajadorUser">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Doc. Identidad</th>
                            <th>Apellidos y Nombres</th>
                            <th>Empresa</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label style="display: none;" id="idTr"></label>
                <label style="display: none;" id="nameTr"></label>
                <button class="btn btn-sm btn-success" onClick="selectValueTrab();">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<!-- Accesos -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-accesos">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <form id="formAccess">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Accesos por Usuario</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="id_acceso">
                        <div class="col-md-12">
                            <input type="hidden" name="id_usuario">
                            <div class="row">
                                <div class="col-md-3">
                                    <h5>Seleccione un rol</h5>
                                    <select class="form-control input-sm" name="role" id="role">
                                        <option value="0" selected disabled></option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <h5>Seleccione un módulo</h5>
                                    <select class="form-control input-sm" name="modulo" id="modulo" onchange="cargarAplicaciones(this.value);">
                                        <option value="0" selected disabled>Elija una opción</option>
                                        @foreach ($modulos as $modulos)
                                            <option value="{{$modulos->id_modulo}}">{{$modulos->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 text-right">
                                    <h5><i>Seleccionar todos</i></h5>
                                    <input type="checkbox" name="todos" id="todos">
                                </div>
                            </div>
                        </div>
                    </div><br>
                    <div class="row" id="domAccess"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-success" onClick="guardarAcceso();">Aceptar</button>
                </div>
            </form>
        </div>
    </div>
</div>
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
    <script src="{{('/js/configuracion/usuario.js')}}"></script>
@endsection
