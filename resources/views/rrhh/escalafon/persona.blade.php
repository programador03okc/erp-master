@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="persona">
    <legend><h2>Persona</h2></legend>
    <form id="form-persona" type="register" form="formulario">
        <input type="hidden" name="id_persona" primary="ids">
        <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
        <div class="row">
            <div class="col-md-3">
                <h5>Tipo documento</h5>
                <select class="form-control activation" name="id_documento_identidad" disabled="true" onChange="valueLengthDoc(this.value);">
                    <option value="0" selected disabled>Elija una opción</option>
                    @foreach ($doc_identi as $doc_identi)
                        <option value="{{$doc_identi->id_doc_identidad}}">{{$doc_identi->descripcion}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <h5>N° documento</h5>
                <input type="text" class="form-control activation" name="nro_documento" disabled="true" maxlength="0">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <h5>Nombres</h5>
                <input type="text" class="form-control activation" name="nombres" disabled="true">
            </div>
            <div class="col-md-3">
                <h5>Apellido paterno</h5>
                <input type="text" class="form-control activation" name="apellido_paterno" disabled="true">
            </div>
            <div class="col-md-3">
                <h5>Apellido materno</h5>
                <input type="text" class="form-control activation" name="apellido_materno" disabled="true">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <h5>Sexo</h5>
                <select class="form-control activation" name="sexo" disabled="true">
                    <option value="0" selected disabled>Elija una opción</option>
                    <option value="F">Femenino</option>
                    <option value="M">Masculino</option>
                </select>
            </div>
            <div class="col-md-3">
                <h5>Fecha nacimiento</h5>
                <input type="date" class="form-control activation" name="fecha_nacimiento" disabled="true">
            </div>
            <div class="col-md-3">
                <h5>Estado civil</h5>
                <select class="form-control activation" name="id_estado_civil" disabled="true">
                    <option value="0" selected disabled>Elija una opción</option>
                    @foreach ($est_civil as $est_civil)
                        <option value="{{$est_civil->id_estado_civil}}">{{$est_civil->descripcion}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>
</div>
<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-persona">
    <div class="modal-dialog" style="width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Personas</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-striped table-condensed table-bordered" id="listaPersona">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Doc. Identidad</th>
                            <th>Apellidos y Nombres</th>
                            <th>Fecha Nacimiento</th>
                            <th>Edad</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <label style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectValue();">Aceptar</button>
            </div>
        </div>
    </div>
</div>

@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/escalafon/persona.js')}}"></script>
@include('layout.fin_html')