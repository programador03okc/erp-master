@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="trabajador">
    <legend><h2>Trabajadores</h2></legend>
    <div id="tab-trabajador">
        <ul class="nav nav-tabs">
            <li class="active"><a type="#alta">Alta del Trabajador</a></li>
            <li><a type="#contrato">Contratos</a></li>
            <li><a type="#rol">Roles</a></li>
            <li><a type="#cuentas">Ctas. Bancarias</a></li>
        </ul>
        <div class="content-tabs">
            <input type="hidden" name="id_trabajador">
            <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
            <section id="alta" hidden>
                <form id="form-alta" type="register" form="formulario">
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Buscar DNI</h5>
                            <input type="hidden" class="form-control" name="id_postulante">
                            <div class="input-group-okc">
                                <input type="text" class="form-control" name="nro_documento" placeholder="Ingrese DNI" aria-describedby="basic-addon1">
                                <div class="input-group-append">
                                    <button type="button" class="input-group-text" id="basic-addon1" onClick="buscarPostulante();">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Nombre del postulante</h5>
                            <input type="text" class="form-control" name="datos_postulante" disabled="true">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Tipo Planilla</h5>
                            <select class="form-control activation" name="id_tipo_planilla">
                                <option value="0" selected disabled>Elija una opción</option>
                                @foreach ($plani as $plani)
                                    <option value="{{$plani->id_tipo_planilla}}">{{$plani->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Tipo Empleado</h5>
                            <select class="form-control activation" name="id_tipo_trabajador">
                                <option value="0" selected disabled>Elija una opción</option>
                                @foreach ($tpemp as $tpemp)
                                    <option value="{{$tpemp->id_tipo_trabajador}}">{{$tpemp->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Categoría Ocupacional</h5>
                            <select class="form-control activation" name="id_categoria_ocupacional">
                                <option value="0" selected disabled>Elija una opción</option>
                                @foreach ($categ as $categ)
                                    <option value="{{$categ->id_categoria_ocupacional}}">{{$categ->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Fondo de Pensión</h5>
                            <select class="form-control activation" name="id_pension">
                                <option value="0" selected disabled>Elija una opción</option>
                                @foreach ($pensi as $pensi)
                                    <option value="{{$pensi->id_pension}}">{{$pensi->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>CUSPP</h5>
                            <input type="text" class="form-control activation" name="cuspp" placeholder="Número CUSPP">
                        </div>
                        <div class="col-md-3">
                            <h5>Hijos</h5>
                            <select class="form-control activation" name="hijos">
                                <option value="0" selected disabled>Elija una opcion</option>
                                <option value="1">SI</option>
                                <option value="2">NO</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Seguro</h5>
                            <select class="form-control activation" name="seguro">
                                <option value="0" selected disabled>Elija una opcion</option>
                                <option value="1">SEGURO REGULAR EXC.</option>
                                <option value="2">EPS</option>
                                <option value="3">AMBAS</option>
                                <option value="4">NINGUNA</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Personal de confianza</h5>
                            <select class="form-control activation" name="confianza">
                                <option value="0" selected disabled>Elija una opcion</option>
                                <option value="1">SI</option>
                                <option value="2">NO</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Marca tareo</h5>
                            <select class="form-control activation" name="marcaje">
                                <option value="0" selected disabled>Elija una opcion</option>
                                <option value="1">SI</option>
                                <option value="2">NO</option>
                            </select>
                        </div>
                    </div>
                </form>
            </section>
            <section id="contrato" hidden>
                <form id="form-contrato">
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Motivo</h5>
                            <input type="hidden" class="form-control" name="id_contrato">
                            <select class="form-control activation" name="motivo">
                                <option value="0" selected disabled>Elija una opción</option>
                                <option value="ALTA">ALTA</option>
                                <option value="RENOVACION">RENOVACION</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Tipo Contrato</h5>
                            <select class="form-control activation" name="id_tipo_contrato">
                                <option value="0" selected disabled>Elija una opción</option>
                                @foreach ($contra as $contra)
                                    <option value="{{$contra->id_tipo_contrato}}">{{$contra->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Modalidad</h5>
                            <select class="form-control activation" name="id_modalidad">
                                <option value="0" selected disabled>Elija una opción</option>
                                @foreach ($modali as $modali)
                                    <option value="{{$modali->id_modalidad}}">{{$modali->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Fecha de Inicio</h5>
                            <input type="date" class="form-control activation" name="fecha_inicio">
                        </div>
                        <div class="col-md-3">
                            <h5>Fecha de Fin</h5>
                            <input type="date" class="form-control activation" name="fecha_fin">
                        </div>
                        <div class="col-md-3">
                            <h5>Horario</h5>
                            <select class="form-control activation" name="id_horario">
                                <option value="0" selected disabled>Elija una opción</option>
                                @foreach ($horar as $horar)
                                    <option value="{{$horar->id_horario}}">{{$horar->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Centro Costos</h5>
                            <select class="form-control activation js-example-basic-single" name="id_centro_costo">
                                <option value="0" selected disabled>Elija una opción</option>
                                @foreach ($cc as $cc)
                                    <option value="{{$cc->id_grupo}}">{{$cc->nombre_sede}} - {{$cc->nombre_grupo}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Tipo de C. Costos</h5>
                            <select class="form-control activation" name="tipo_centro_costo">
                                <option value="0" selected disabled>Elija una opción</option>
                                <option value="1">FIJO</option>
                                <option value="2">VARIABLE</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <h5>Tabla de Resultados</h5>
                            <table class="mytable table table-condensed table-bordered table-okc-view table-result-form" id="ListaContratoTrab" width="100%">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>MOTIVO</th>
                                        <th>TIPO</th>
                                        <th>MODALIDAD</th>
                                        <th width="90">F. INICIO</th>
                                        <th width="90">F. FIN</th>
                                    </tr>
                                </thead>
                                <tbody id="trab-ctt">
                                    <tr><td></td><td colspan="5"> No hay datos registrados</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </section>
            <section id="rol" hidden>
                <form id="form-rol">
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Empresa</h5>
                            <input type="hidden" class="form-control activation" name="id_rol">
                            <select class="form-control activation" name="id_empresa">
                                <option value="0" selected disabled>Elija una opción</option>
                                @foreach ($empresa as $empresa)
                                    <option value="{{$empresa->id_empresa}}">{{$empresa->razon_social}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <h5>Area</h5>
                            <input type="hidden" class="form-control" name="id_area">
                            <div class="input-group-okc">
                                <input type="text" class="form-control" name="nombre_area" placeholder="Seleccionar area" aria-describedby="basic-addon-area" disabled="true">
                                <div class="input-group-append">
                                    <button type="button" class="input-group-text" id="basic-addon-area" onClick="modal_area();">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5>Tipo Planilla</h5>
                            <select class="form-control activation" name="rol_id_tipo_planilla">
                                <option value="0" selected disabled>Elija una opción</option>
                                @foreach ($planil as $planil)
                                    <option value="{{$planil->id_tipo_planilla}}">{{$planil->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <h5>Cargo</h5>
                            <select class="form-control activation js-example-basic-single" name="id_cargo">
                                <option value="0" selected disabled>Elija una opción</option>
                                @foreach ($cargo as $cargo)
                                    <option value="{{$cargo->id_cargo}}">{{$cargo->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <h5>Rol Concepto</h5>
                            <select class="form-control activation js-example-basic-single" name="id_rol_concepto">
                                <option value="0" selected disabled>Elija una opción</option>
                                @foreach ($rol_conc as $rol_conc)
                                    <option value="{{$rol_conc->id_rol_concepto}}">{{$rol_conc->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <h5>Salario</h5>
                            <input type="number" class="form-control activation" name="salario">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Fecha Inicio</h5>
                                    <input type="date" class="form-control activation" name="fecha_ingreso">
                                </div>
                                <div class="col-md-6">
                                    <h5>Fecha Fin</h5>
                                    <input type="date" class="form-control activation" name="fecha_cese">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5>Responsabilidad</h5>
                            <select class="form-control activation" name="responsabilidad">
                                <option value="0" selected disabled>Elija una opcion</option>
                                <option value="1">SI</option>
                                <option value="2">NO</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>SCTR</h5>
                            <select class="form-control activation" name="sctr">
                                <option value="0" selected disabled>Elija una opcion</option>
                                <option value="1">SI</option>
                                <option value="2">NO</option>
                            </select>
                        </div>
                        <div class="col-md-7" style="display: none;">
                            <h5>Proyecto</h5>
                            <select class="form-control" name="id_proyecto" disabled>
                                <option value="0" selected disabled>Elija una opcion</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Tabla de Resultados</h5>
                            <table class="table table-condensed table-bordered table-okc-view table-result-form" id="ListaRolTrab" width="100%">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>EMPRESA</th>
                                        <th>SEDE</th>
                                        <th>AREA</th>
                                        <th>CARGO</th>
                                        <th width="90">F. INICIO</th>
                                        <th width="90">F. FIN</th>
                                        <th width="100">SALARIO</th>
                                        <th width="40">ACCION</th>
                                    </tr>
                                </thead>
                                <tbody id="trab-rol">
                                    <tr><td></td><td colspan="8"> No hay datos registrados</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </section>
            <section id="cuentas" hidden>
                <form id="form-cuentas">
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Banco</h5>
                            <input type="hidden" class="form-control activation" name="id_cuenta_bancaria">
                            <select class="form-control activation" name="id_banco">
                                <option value="0" selected disabled>Elija una opción</option>
                                @foreach ($banco as $banco)
                                    <option value="{{$banco->id_banco}}">{{$banco->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Tipo Cuenta</h5>
                            <select class="form-control activation" name="id_tipo_cuenta">
                                <option value="0" selected disabled>Elija una opción</option>
                                @foreach ($tpcta as $tpcta)
                                    <option value="{{$tpcta->id_tipo_cuenta}}">{{$tpcta->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <h5>CCI</h5>
                            <input type="text" class="form-control activation" name="nro_cci">
                        </div>
                        <div class="col-md-2">
                            <h5>N° Cuenta</h5>
                            <input type="text" class="form-control activation" name="nro_cuenta">
                        </div>
                        <div class="col-md-3">
                            <h5>Moneda</h5>
                            <select class="form-control activation" name="id_moneda">
                                <option value="0" selected disabled>Elija una opción</option>
                                @foreach ($moneda as $moneda)
                                    <option value="{{$moneda->id_moneda}}">{{$moneda->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <h5>Tabla de Resultados</h5>
                            <table class="table table-condensed table-bordered table-okc-view table-result-form" id="ListaCtasTrab" width="100%">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>BANCO</th>
                                        <th>CCI</th>
                                        <th>CUENTA</th>
                                        <th>TIPO</th>
                                    </tr>
                                </thead>
                                <tbody id="trab-cta">
                                    <tr><td></td><td colspan="4"> No hay datos registrados</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
<!-- modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-trabajador">
    <div class="modal-dialog" style="width: 85%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Trabajadores</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-striped table-condensed table-bordered" id="listaTrabajador">
                    <thead>
                        <tr>
                            <th></th>
                            <th width="50">DNI</th>
                            <th>Apellidos y Nombres</th>
                            <th>Empresa</th>
                            <th>Sede</th>
                            <th>Grupo</th>
                            <th>Cargo</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer" id="footer-lista">
                <label style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="selectValue();">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<!-- modal Cerrar Roles y Contraros -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-terminos">
    <div class="modal-dialog" style="width: 15%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Actualizar</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Fecha Fin</h5>
                        <input type="hidden" name="id_termino" id="id_termino">
                        <input type="hidden" name="id_trab_termino" id="id_trab_termino">
                        <input type="hidden" name="tipo_termino" id="tipo_termino">
                        <input type="date" class="form-control input-sm" name="fecha_termino" id="fecha_termino">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-flat btn-block btn-success" onClick="save_close();">Grabar</button>
            </div>
        </div>
    </div>
</div>
@include('publico.modal_area')

@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/escalafon/trabajador.js')}}"></script>
<script src="{{('/js/publico/modal_area.js')}}"></script>
@include('layout.fin_html')