@include('layout.head')
@include('layout.menu_admin')
@include('layout.body')

<div class="page-main" type="empresa">
    <legend><h2>Empresa</h2></legend>
    <div id="tab-empresa">
        <ul class="nav nav-tabs">
            <li class="active"><a type="#informacion">Datos Generales</a></li>
            <li><a type="#contacto">Datos de Contacto</a></li>
            <li><a type="#cuentas">Ctas. Bancarias</a></li>
        </ul>
        <div class="content-tabs">
            <input type="hidden" name="id_contribuyente">
            <section id="informacion" hidden>
                <form id="form-informacion" type="register" form="formulario">
                    <input type="hidden" name="id_empresa">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Tipo contribuyente</h5>
                            <select class="form-control input-sm activation" name="id_tipo_contribuyente">
                                <option value="0" selected disabled>Elija una opción</option>
                                @foreach ($tp_contri as $tp_contri)
                                    <option value="{{$tp_contri->id_tipo_contribuyente}}">{{$tp_contri->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <h5>N° RUC</h5>
                            <div class="input-group-okc">
                                <input type="number" class="form-control input-sm activation" name="nro_documento" placeholder="Ingrese RUC"
                                aria-describedby="basic-addon1" pattern="([0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9])" maxlength="11">
                                <div class="input-group-append">
                                    <button type="button" class="input-group-text" id="basic-addon1" onClick="consultaSunat();">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-9">
                            <h5>Razón Social</h5>
                            <input type="text" class="form-control input-sm activation" name="razon_social" disabled="true"
                            placeholder="Ingrese nombre de la empresa">
                        </div>
                        <div class="col-md-2">
                            <h5>Código</h5>
                            <input type="text" class="form-control input-sm activation" name="codigo" disabled="true"
                            placeholder="- - - - -" style="text-align: center;" maxlength="5">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-11">
                            <h5>Dirección</h5>
                            <input type="text" class="form-control input-sm activation" name="direccion_fiscal" disabled="true"
                            placeholder="Dirección actual">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Teléfono</h5>
                            <input type="number" class="form-control input-sm activation" name="telefono" disabled="true"
                            pattern="([0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9])" maxlength="9">
                        </div>
                        <div class="col-md-4">
                            <h5>País</h5>
                            <select class="form-control input-sm activation" name="id_pais">
                                @foreach ($pais as $pais)
                                    <option value="{{$pais->id_pais}}" @if($pais->descripcion == 'PERU') selected="selected"@endif>{{$pais->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Ubigeo</h5>
                            <div class="input-group-okc">
                                <input type="text" class="form-control input-sm" name="ubigeo" readonly placeholder="Seleccione">
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
            <section id="contacto" hidden>
                <form id="form-contacto">
                    <div class="row">
                        <div class="col-md-5">
                            <h5>Nombres y Apellidos</h5>
                            <input type="text" class="form-control input-sm activation" name="nombre_contact" disabled="true"
                            placeholder="Nombres del contacto">
                        </div>
                        <div class="col-md-3">
                            <h5>Teléfono</h5>
                            <input type="number" class="form-control input-sm activation" name="telefono_contact" maxlength="9"
                             pattern="([0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9])" disabled="true" placeholder="Número telefónico">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <h5>Correo Electrónico</h5>
                            <input type="email" class="form-control input-sm activation" name="email_contact" disabled="true"
                            placeholder="example@hotmail.com">
                        </div>
                        <div class="col-md-3">
                            <h5>Cargo</h5>
                            <input type="text" class="form-control input-sm activation" name="cargo_contact" disabled="true"
                            placeholder="Cargo del contacto">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-8">
                            <h5>Tabla de Resultados</h5>
                            <table class="mytable table table-condensed table-bordered table-okc-view table-result-form" id="ListaContacto" width="100%">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>NOMBRES DEL CONTACTO</th>
                                        <th>CARGO</th>
                                        <th>CORREO</th>
                                        <th>TELEFONO</th>
                                    </tr>
                                </thead>
                                <tbody id="empre-contact">
                                    <tr><td></td><td colspan="4"> No hay datos registrados</td></tr>
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
                        <div class="col-md-3">
                            <h5>N° Cuenta</h5>
                            <input type="text" class="form-control activation" name="nro_cuenta">
                        </div>
                        <div class="col-md-3">
                            <h5>N° Cuenta Interb.</h5>
                            <input type="text" class="form-control activation" name="nro_cci">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <h5>Tabla de Resultados</h5>
                            <table class="table table-condensed table-bordered table-okc-view table-result-form" id="ListaCuentas" width="100%">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>BANCO</th>
                                        <th>TIPO CUENTA</th>
                                        <th>N° CUENTA</th>
                                    </tr>
                                </thead>
                                <tbody id="empre-cta">
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
<div class="modal fade" tabindex="-1" role="dialog" id="modal-empresas">
    <div class="modal-dialog" style="width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Empresas</h3>
            </div>
            <div class="modal-body">
                <table class="mytable table table-striped table-condensed table-bordered" id="listaEmpresas">
                    <thead>
                        <tr>
                            <th></th>
                            <th width="120">N° RUC</th>
                            <th>Empresa</th>
                            <th>Dirección</th>
                            <th width="80">Teléfono</th>
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
<script src="{{('/js/administracion/empresa.js')}}"></script>
<script src="{{('/js/publico/ubigeo.js')}}"></script>
@include('layout.fin_html')
