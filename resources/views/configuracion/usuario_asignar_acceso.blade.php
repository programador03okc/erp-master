@extends('layout.main')
@include('layout.menu_config')

@section('cabecera')
    Gestión de Usuarios
@endsection
@section('content')
<div class="page-main" type="usuarios">
    <legend class="mylegend">
        <h2>Usuarios</h2>
        <ol class="breadcrumb">
            <li>
                <label data-name="name"></label>
            </li>
        </ol>
    </legend>
    <div class="container-fluid">
        <form action="" data-form="enviar-data">
            <input type="hidden" name="id_usuario" value="">
            <div class="row">
                <div class="col-md-12">
                    <ul class="nav nav-tabs" role="tablist" id="tab_modulos">
                        {{-- <li role="presentation" class="active"><a href="#modulo1" aria-controls="modulo1" role="tab" data-toggle="tab">modulo 1</a></li>
                        <li role="presentation" class=""><a href="#modulo2" onClick="vista_extendida();" aria-controls="modulo2" role="tab" data-toggle="tab">modulo 2</a></li> --}}
                    </ul>
                    <div class="tab-content" id="tabpanel_modulos">

                        {{-- <div role="tabpanel" class="tab-pane active" id="modulo1">
                            <div class="panel panel-default">
                                <div class="panel-body" style="overflow: scroll; height: 35vh;">
                                    <div class="row">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="modulo2">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row">

                                    </div>
                                </div>
                            </div>
                        </div> --}}

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-right">
                    <button class="btn btn-primary" type="submit"> Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Accesos -->

{{-- @include('proyectos.residentes.trabajadorModal')
@include('configuracion.modal_editar_usuario')
@include('configuracion.modal_asignar_accesos') --}}

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

    {{-- <script src="{{('/js/configuracion/usuario.js')}}"></script> --}}
    <script src="{{('/js/configuracion/usuario_asignar_accesos.js')}}"></script>
    <script src="{{('/js/configuracion/modal_asignar_accesos.js')}}"></script>
    <script src="{{ asset('js/proyectos/residentes/trabajadorModal.js')}}"></script>

@endsection
