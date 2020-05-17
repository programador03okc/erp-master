@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="salida">
    <legend><h2>Permisos y Salidas del Personal</h2></legend>
    <form id="form-salida" type="register" form="formulario">
        <input type="hidden" name="id_permiso" primary="ids">
        <div class="row">
            <div class="col-md-3">
                <h5>Buscar DNI</h5>
                <input type="hidden" class="form-control" name="id_trabajador">
                <div class="input-group-okc">
                    <input type="text" class="form-control" name="nro_documento" placeholder="Ingrese DNI" aria-describedby="basic-addon1">
                    <div class="input-group-append">
                        <button type="button" class="input-group-text" id="basic-addon1" onClick="buscarPersona();">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <h5>Nombre del trabajador</h5>
                <input type="text" class="form-control" name="datos_trabajador" disabled="true">
            </div>
            <div class="col-md-3">
                <h5>Permiso / Salida</h5>
                <select class="form-control activation" name="tipo" disabled="true">
                    <option value="0" selected disabled>Elija una opción</option>
                    <option value="1">Permisos</option>
                    <option value="2">Comisión de Salida</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <h5>Tipo</h5>
                <select class="form-control activation" name="id_tipo_permiso" disabled="true">
                    <option value="0" selected disabled>Elija una opción</option>
                    @foreach ($salidas as $salidas)
                        <option value="{{$salidas->id_tipo_permiso}}">{{$salidas->descripcion}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Fecha Inicio</h5>
                        <input type="date" class="form-control activation" name="fecha_inicio_permiso" disabled="true">
                    </div>
                    <div class="col-md-6">
                        <h5>Fecha Fin</h5>
                        <input type="date" class="form-control activation" name="fecha_fin_permiso" disabled="true">
                    </div>
                </div>
            </div>
            <div class="col-md-3">
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
            <div class="col-md-2">
                <h5>Hora Inicio</h5>
                <input type="time" class="form-control activation" name="hora_inicio" disabled="true">
            </div>
            <div class="col-md-2">
                <h5>Hora Fin</h5>
                <input type="time" class="form-control activation" name="hora_fin" disabled="true">
            </div>
            <div class="col-md-7">
                <h5>Motivo</h5>
                <textarea class="form-control activation" name="motivo" disabled="true"></textarea>
            </div>
            
        </div>
        <div class="row">
            <div class="col-sm-10">
                <h5>Tabla de Resultados</h5>
                <table class="mytable table table-condensed table-bordered table-okc-view table-result-form" id="ListaSalidas" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th class="hidden"></th>
                            <th class="hidden">MES</th>
                            <th>TIPO SALIDA</th>
                            <th>FECHA SALIDA</th>
                            <th>HORA SALIDA</th>
                            <th>AUTORIZADO POR</th>
                        </tr>
                    </thead>
                    <tbody>
                    	<tr>
                    		<td colspan="4">No hay registros</td>
                    	</tr>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/control/salidas.js')}}"></script>
@include('layout.fin_html')