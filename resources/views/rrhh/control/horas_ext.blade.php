@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="horas_ext">
    <legend><h2>Horas Extras Autorizadas</h2></legend>
    <form id="form-horas_ext" type="register" form="formulario">
        <input type="hidden" name="id_hora_extra" primary="ids">
        <div class="row">
            <div class="col-md-3">
                <h5>Buscar DNI</h5>
                <input type="hidden" class="form-control input-sm" name="id_trabajador">
                <div class="input-group-okc">
                    <input type="text" class="form-control input-sm" name="nro_documento" placeholder="Ingrese DNI" aria-describedby="basic-addon1">
                    <div class="input-group-append">
                        <button type="button" class="input-group-text" id="basic-addon1" onClick="buscarPersona();">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <h5>Nombre del trabajador</h5>
                <input type="text" class="form-control input-sm" name="datos_trabajador" disabled="true">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-7">
                        <h5>Fecha</h5>
                        <input type="date" class="form-control input-sm activation" name="fecha" disabled="true">
                    </div>
                    <div class="col-md-5">
                        <h5>H. Extras</h5>
                        <input type="text" class="form-control input-sm activation" name="horas" disabled="true">
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <h5>Autorizado por</h5>
                <select class="form-control activation js-example-basic-single" name="id_trabajador_autoriza" disabled="true">
                    <option value="0" selected disabled>Elija una opción</option>
                    @foreach ($usuario as $usuario)
                        <option value="{{$usuario->id_usuario}}">{{$usuario->apellido_paterno}} {{$usuario->apellido_materno}} {{$usuario->nombres}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-7">
                <h5>Motivo</h5>
                <textarea class="form-control activation" name="motivo" disabled="true"></textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-8">
                <h5>Tabla de Resultados</h5>
                <table class="mytable table table-condensed table-bordered table-okc-view table-result-form" id="ListaHorasExt" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th width="120">FECHA</th>
                            <th width="100">HE. 25</th>
                            <th width="100">HE. 35</th>
                            <th width="100">HE. 100</th>
                            <th>TOTAL HE.</th>
                            <th>AUTORIZA</th>
                        </tr>
                    </thead>
                    <tbody id="trab-horas-ext">
                        <tr><td></td><td colspan="6"> No hay datos registrados</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/control/horas_ext.js')}}"></script>
@include('layout.fin_html')