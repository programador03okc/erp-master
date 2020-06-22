@extends('layout.head')
@include('layout.menu_config')

@section('cabecera')
    Correos Coorporativos
@endsection

@section('content')
<div class="page-main" type="correo_coorporativo">
    <legend><h2>Correos Coorporativos</h2></legend>
    <div class="row">
        <div class="col-md-6">
            <fieldset class="group-table">
                <table class="mytable table table-hover table-condensed table-bordered table-result-form" id="listaCorreoCoorporativo">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Empresa</th>
                            <th>Correo</th>
                            <th>Servidor</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-6">
            <form id="form-correo_coorporativo" type="register" form="formulario">
                <input type="hidden" name="id_smtp_authentication" primary="ids">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Empresa</h5>
                        <select class="form-control activation" name="empresa" disabled="true" onchange="cambiarModulo(this.value);">
                            <option value="0" selected disabled>Elija una opción</option>
                            @foreach ($empresas as $empresa)
                                <option value="{{$empresa->id_empresa}}">{{$empresa->razon_social}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-7">
                        <h5>Servidor SMTP</h5>
                        <input type="text" class="form-control activation" name="smtp_server" disabled="true">
                    </div>
                    <div class="col-md-3">
                        <h5>Encriptación</h5>
                        <select class="form-control activation" name="encryption" disabled="true">
                            <option value="ssl" >SSL</option>
                            <option value="tls" >TLS</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <h5>Puerto</h5>
                        <input type="number" min="0" class="form-control activation" name="port" disabled="true" >
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Correo</h5>
                        <input type="text" class="form-control activation" name="email" disabled="true" >
                    </div>
                    <div class="col-md-6">
                        <h5>Contraseña</h5>
                        <input type="password" class="form-control activation" name="password" disabled="true" >
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h5>Estado</h5>
                        <select class="form-control activation" name="estado" disabled="true">
                            <option value="0" selected disabled>Elija una opción</option>
                            <option value="1" >Activo</option>
                            <option value="7" >Anulado</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script src="{{('/js/configuracion/correo_coorporativo.js')}}"></script>
@endsection
