@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="derecho_hab">
    <legend><h2>Derecho Habientes</h2></legend>
    <div class="row">
        <div class="col-md-12">
            <form id="form-derecho_hab" type="register" form="formulario">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <input type="hidden" name="id_derecho_habiente" primary="ids">
                <div class="row">
                    <div class="col-md-3">
                        <h5>DNI Trabajador</h5>
                        <input type="hidden" class="form-control" name="id_trabajador">
                        <div class="input-group-okc">
                            <input type="text" class="form-control" name="dni_trab" placeholder="Ingrese DNI" aria-describedby="basic-addon1">
                            <div class="input-group-append">
                                <button type="button" class="input-group-text" id="basic-addon1" onClick="buscarPersona(1);">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <h5>Nombres y Apellidos del trabajador</h5>
                        <input type="text" class="form-control" name="descripcion_trab" disabled="true" placeholder="Datos del trabajador">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h5>DNI Persona</h5>
                        <input type="hidden" class="form-control" name="id_persona">
                        <div class="input-group-okc">
                            <input type="text" class="form-control" name="dni_per" placeholder="Ingrese DNI" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button type="button" class="input-group-text" id="basic-addon2" onClick="buscarPersona(2);">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <h5>Nombres y Apellidos de la persona</h5>
                        <input type="text" class="form-control" name="descripcion_pers" disabled="true" placeholder="Datos del pariente">
                    </div>
                    <div class="col-md-3">
                        <h5>Descripción</h5>
                        <select class="form-control activation" name="id_condicion_dh" disabled="true">
                            <option value="0" selected disabled>Elija una opción</option>
                            @foreach ($condi as $condi)
                                <option value="{{$condi->id_condicion_dh}}">{{$condi->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10">
            <table class="table table-condensed table-bordered table-okc-view table-result-form" id="ListaDerechoHab">
                <caption>Tabla de resultados</caption>
                <thead>
                    <tr>
                        <th></th>
                        <th width="120">DNI</th>
                        <th>NOMBRES Y APELIDOS</th>
                        <th width="150">FEC. NACIMIENTO</th>
                        <th width="60">EDAD</th>
                        <th>CONDICION</th>
                    </tr>
                </thead>
                <tbody id="dhab">
                    <tr><td></td><td colspan="5">No hay datos registrados</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/escalafon/derecho_hab.js')}}"></script>
@include('layout.fin_html')