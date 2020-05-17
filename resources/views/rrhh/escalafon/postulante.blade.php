@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="postulante">
    <legend><h2>Postulante</h2></legend>
    <div id="tab-postulante">
        <ul class="nav nav-tabs">
            <li class="active"><a type="#informacion">Información</a></li>
            <li><a type="#formacion">Formación Académica</a></li>
            <li><a type="#experiencia">Experiencia Laboral</a></li>
            <li><a type="#extras">Datos Extras</a></li>
            <li><a type="#observacion">Observaciones</a></li>
        </ul>
        <div class="content-tabs">
            <input type="hidden" name="id_postulante">
            <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
            <section id="informacion" hidden>
                <form id="form-informacion" type="register" form="formulario">
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Buscar DNI</h5>
                            <input type="hidden" class="form-control" name="id_persona">
                            <div class="input-group-okc">
                                <input type="text" class="form-control" name="nro_documento" placeholder="Ingrese DNI" aria-describedby="basic-addon1">
                                <div class="input-group-append">
                                    <button type="button" class="input-group-text" id="basic-addon1" onClick="buscarPersona();">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Nombre de la persona</h5>
                            <input type="text" class="form-control" name="datos_persona" disabled="true">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Teléfono</h5>
                            <input type="text" class="form-control activation" name="telefono" disabled="true" placeholder="Número telefónico">
                        </div>
                        <div class="col-md-6">
                            <h5>Dirección</h5>
                            <input type="text" class="form-control activation" name="direccion" disabled="true" placeholder="Dirección actual">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Brevette - Categoría</h5>
                            <input type="text" class="form-control activation" name="brevette" disabled="true" placeholder="Brevette y categoría">
                        </div>
                        <div class="col-md-6">
                            <h5>Correo</h5>
                            <input type="email" class="form-control activation" name="correo" disabled="true" placeholder="Correo electrónico">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>País</h5>
                            <select class="form-control activation" name="id_pais">
                                @foreach ($pais as $pais)
                                    <option value="{{$pais->id_pais}}" @if($pais->descripcion == 'PERU') selected="selected"@endif>{{$pais->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Ubigeo</h5>
                            <div class="input-group-okc">
                                <input type="text" class="form-control" name="ubigeo" readonly placeholder="Seleccione ubigeo">
                                <div class="input-group-append">
                                    <button type="button" class="input-group-text" onclick="cargarUbigeo();">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </section>
            <section id="formacion" hidden>
                <form id="form-formacion">
                    <div class="row">
                        <div class="col-md-5">
                            <h5>Nivel de Estudios</h5>
                            <input type="hidden" class="form-control" name="id_formacion">
                            <select class="form-control activation" name="id_nivel_estudio">
                                <option value="0" selected disabled>Elija una opción</option>
                                @foreach ($niv_est as $niv_est)
                                    <option value="{{$niv_est->id_nivel_estudio}}">{{$niv_est->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Fecha de Inicio</h5>
                            <input type="date" class="form-control activation" name="fecha_inicio">
                        </div>
                        <div class="col-md-3">
                            <h5>Fecha de Fin</h5>
                            <input type="date" class="form-control activation" name="fecha_fin">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <h5>Carrera/Profesión</h5>
                            <select class="form-control activation" name="id_carrera">
                                <option value="0" selected disabled>Elija una opción</option>
                                @foreach ($carrera as $carrera)
                                    <option value="{{$carrera->id_carrera}}">{{$carrera->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <h5>Institución</h5>
                            <input type="text" class="form-control activation" name="nombre_institucion" placeholder="Nombre de la institución">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>País</h5>
                            <select class="form-control activation" name="id_pais_frm">
                                @foreach ($pais_frm as $pais_frm)
                                    <option value="{{$pais_frm->id_pais}}" @if($pais_frm->descripcion == 'PERU') selected="selected"@endif>{{$pais_frm->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Ubigeo</h5>
                            <div class="input-group-okc">
                                <input type="text" class="form-control activation" name="ubigeo" placeholder="Seleccione ubigeo" aria-describedby="basic-addon2" readonly>
                                <div class="input-group-append">
                                    <button type="button" class="input-group-text" id="basic-addon2" onclick="cargarUbigeo();">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <h5>Tabla de Resultados</h5>
                            <table class="mytable table table-condensed table-bordered table-okc-view table-result-form" id="ListaFormacionAcad" width="100%">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>NIVEL</th>
                                        <th>PROFESION</th>
                                        <th>INSTITUCION</th>
                                        <th width="90">F. INICIO</th>
                                        <th width="90">F. FIN</th>
                                    </tr>
                                </thead>
                                <tbody id="postu-fa">
                                    <tr><td></td><td colspan="5"> No hay datos registrados</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </section>
            <section id="experiencia" hidden>
                <form id="form-experiencia">
                    <div class="row">
                        <div class="col-md-8">
                            <h5>Nombre Empresa</h5>
                            <input type="hidden" class="form-control activation" name="id_experiencia_laboral">
                            <input type="text" class="form-control activation" name="nombre_empresa" placeholder="Ingrese de la Empresa">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Cargo Ocupado</h5>
                            <input type="text" class="form-control activation" name="cargo_ocupado" placeholder="Cargo ejercido">
                        </div>
                        <div class="col-md-2">
                            <h5>Fecha Inicio</h5>
                            <input type="date" class="form-control activation" name="fecha_ingreso">
                        </div>
                        <div class="col-md-2">
                            <h5>Fecha Fin</h5>
                            <input type="date" class="form-control activation" name="fecha_cese">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-10">
                            <h5>Funciones Realizadas</h5>
                            <textarea class="form-control activation" name="funciones" placeholder="Escriba las funciones separadas por punto a parte (.)"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Datos de Contacto</h5>
                            <input type="text" class="form-control activation" name="datos_contacto" placeholder="Nombre del contacto">
                        </div>
                        <div class="col-md-4">
                            <h5>Cargo del Contacto</h5>
                            <input type="text" class="form-control activation" name="relacion_trab_contacto" placeholder="Relacion entre Contacto y Postulante">
                        </div>
                        <div class="col-md-2">
                            <h5>Tlf. de Contacto</h5>
                            <input type="text" class="form-control activation" name="telefono_contacto" placeholder="Tlf. del contacto">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Tabla de Resultados</h5>
                            <table class="table table-condensed table-bordered table-okc-view table-result-form" id="ListaExperienciaLab" width="100%">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>EMPRESA</th>
                                        <th>CARGO</th>
                                        <th>CONTACTO</th>
                                        <th width="90">F. INICIO</th>
                                        <th width="90">F. FIN</th>
                                    </tr>
                                </thead>
                                <tbody id="postu-el">
                                    <tr><td></td><td colspan="5"> No hay datos registrados</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </section>
            <section id="extras" hidden>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Tabla de Resultados</h5>
                        <table class="mytable table table-condensed table-bordered table-okc-view table-result-form" id="ListaArchivos" width="100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>TIPO ARCHIVO</th>
                                    <th>ARCHIVO</th>
                                </tr>
                            </thead>
                            <tbody id="postu-de">
                                <tr><td></td><td colspan="2"> No hay archivos registrados</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <form id="form-extras">
                            <input type="hidden" name="id_datos_extras">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5>Tipo de archivo</h5>
                                    <select class="form-control activation" name="id_tipo_archivo">
                                        <option value="0" selected disabled>Elija una opcion</option>
                                        @foreach ($tipo_archivo as $tipo_archivo)
                                            <option value="{{$tipo_archivo->id_tipo_archivo}}">{{$tipo_archivo->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-10">
                                    <h5>Cargar archivo</h5>
                                    <input type="file" name="archivo" class="filestyle" data-buttonName="btn-primary" data-buttonText="Seleccionar archivo" data-size="sm">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
            <section id="observacion" hidden>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Tabla de Resultados</h5>
                        <table class="mytable table table-condensed table-bordered table-okc-view table-result-form" id="ListaObs" width="100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>OBSERVACION</th>
                                </tr>
                            </thead>
                            <tbody id="postu-obs">
                                <tr><td></td><td> No hay archivos registrados</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <form id="form-observacion">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Observacion</h5>
                                    <input type="hidden" name="id_observacion">
                                    <textarea class="form-control activation" name="observacion"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-postulante">
    <div class="modal-dialog" style="width: 85%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Postulantes</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-striped table-condensed table-bordered" id="listaPostulante">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Doc. Identidad</th>
                            <th>Apellidos y Nombres</th>
                            <th>Dirección</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
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
@include('publico.ubigeo')

@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/escalafon/postulante.js')}}"></script>
<script src="{{('/js/publico/ubigeo.js')}}"></script>
@include('layout.fin_html')