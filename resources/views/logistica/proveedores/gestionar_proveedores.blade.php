@extends('layout.head')
@include('layout.menu_logistica')

@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
    Gestionar Proveedores
@endsection

@section('content')
<div class="page-main" type="proveedores">
    <legend>
        <h2>Gestinar Proveedores</h2>
    </legend>
    <div>
            <input  type="text" class="form-control icd-okc invisible" name="id_proveedor" />
            <div id="tab-proveedores">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs">
                <li class="active"><a type="#contribuyente" >Proveedor</a></li>
                <li><a type="#establecimientos">Establecimientos</a></li>
                <li><a type="#cuentas_bancarias">Cuentas Bancarias</a></li>
                <li><a type="#contactos" >Contactos</a></li>
                <li><a type="#adjuntos">Adjuntos</a></li>
                </ul>

                <div class="content-tabs">
                    <section id="contribuyente" hidden>
                    <form id="form-contribuyente" type="register" form="formulario">
                        <div class="row">
                            <div class="col-md-2">
                                <h5>RUC</h5>
                                <div style="display:flex;">

                                    <input  type="text" pattern="^[0-9]{11}$" class="form-control icd-okc activation" name="nro_documento" onkeyup="mayus(this);"/>
                                    <button type="button" class="btn-default" title="Verificar Nro Documento" onclick="consultaSunat();">
                                        <img src="{{ asset('images/sunat.ico') }}" class="img-responsive sunat-ico" style="width:20px !important; border:none !important;"/> 
                                        <img src="{{ asset('images/loading.gif')}}" class="loading invisible">
                                    </button>
                                </div>
                                
                            </div>
                            <div class="col-md-4">
                                <h5>Razón Social</h5>
                                <input class="form-control icd-okc activation" name="razon_social" onkeyup="mayus(this);" />
                            </div>
                            <div class="col-md-3">
                                <h5>Estado</h5>
                                <select class="form-control group-elemento activation" name="estado_ruc" 
                                    style="text-align:center;" disabled="true">
                                    <option value="0">Elija una opción</option>
                                    @foreach ($estado_ruc as $est)
                                        <option value="{{$est->id_estado_ruc}}">{{$est->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <h5>Condición</h5>
                                <select class="form-control group-elemento activation" name="condicion_ruc" 
                                    style="text-align:center;" disabled="true">
                                    <option value="0">Elija una opción</option>
                                    @foreach ($condicion_ruc as $cnd)
                                        <option value="{{$cnd->id_condicion_ruc}}">{{$cnd->descripcion}}</option>
                                    @endforeach
                                </select>                            
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <h5>Tipo Empresa</h5>
                                <select class="form-control group-elemento activation" name="tipo_empresa" 
                                    style="text-align:center;" disabled="true">
                                    <option value="0">Elija una opción</option>
                                    @foreach ($tipo_contribuyente as $tpe)
                                        <option value="{{$tpe->id_tipo_contribuyente}}">{{$tpe->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <h5>telefono</h5>
                                <input class="form-control icd-okc activation" name="telefono" onkeyup="mayus(this);" />
                            </div>
                            <div class="col-md-4">
                                <h5>Dirección</h5>
                                <input class="form-control icd-okc activation" name="direccion" onkeyup="mayus(this);" />
                            </div>
                            <div class="col-md-2">
                                <h5>Ubigeo</h5>
                                <input class="form-control icd-okc activation" name="ubigeo" onkeyup="mayus(this);" />
                            </div>
                            <div class="col-md-2">
                                <h5>Pais</h5>
                                <select class="form-control group-elemento activation" id="paises" name="paises" style="text-align:center;" disabled="true">
                                <option value="0">Elija una opción</option>
                                    @foreach ($paises as $pa)
                                        <option value="{{$pa->id_pais}}">{{$pa->descripcion}}</option>
                                    @endforeach
                                </select>  
                            </div>

                        </div>

                    </form>
                    </section>
                    <section id="establecimientos" hidden>
                    <form id="form-establecimientos" type="register" form="formulario"> 
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Establecimientos</h5>
                                <div class="table-responsive">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="ListaEstablecimientos" width="100%">
                                        <thead>
                                            <tr>
                                                <th class="invisible"></th>
                                                <th width="10">#</th>
                                                <th width="50">TIPO</th>
                                                <th width="250">DIRECCIÓN</th>
                                                <th width="120">
                                                    <center><button class="btn btn-xs btn-success activation" onclick="AgregarEstablecimiento(event);" id="btn_agregar_establecimiento" data-toggle="tooltip" data-placement="bottom" title="Agregar Detalle" disabled="disabled"><i class="fas fa-plus"></i>
                                                    </button></center>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </form>
                    </section>
                    
                    <section id="cuentas_bancarias" hidden>
                        <form id="form-cuentas_bancarias">
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="ListaCuentasBancarias" width="100%">
                                        <thead>
                                            <tr>
                                                <th class="invisible"></th>
                                                <th width="5">#</th>
                                                <th width="150">BANCO</th>
                                                <th>TIPO CUENTA</th>
                                                <th >NRO CUENTA</th>
                                                <th >NRO INTERBANCARIA</th>
                                                <th width="120">
                                                    <center><button class="btn btn-xs btn-success activation" onclick="AgregarCuantaBancaria(event);" id="btn_add_cuenta_bancaria" data-toggle="tooltip" data-placement="bottom" title="Agregar Detalle" disabled="disabled"><i class="fas fa-plus"></i>
                                                    </button></center>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </section>

                    <section id="contactos" hidden>
                    <form id="form-contactos">
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="ListaContactos" width="100%">
                                        <thead>
                                            <tr>
                                                <th class="invisible"></th>
                                                <th width="10">#</th>
                                                <th width="150">Nombres</th>
                                                <th>Telefono</th>
                                                <th >E-mail</th>
                                                <th >Cargo</th>
                                                <th >Estabecimiento</th>
                                                <th width="120">
                                                    <center><button class="btn btn-xs btn-success activation" onclick="AgregarContacto(event);" id="btn_add_contacto" data-toggle="tooltip" data-placement="bottom" title="Agregar Detalle" disabled="disabled"><i class="fas fa-plus"></i>
                                                    </button></center>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </section>
 
                    <section id="adjuntos" hidden>
                        <form id="form-adjuntos">
                            <div class="row">
                            <div class="col-md-12">
                                <h5>Brochure y Otros Adjuntos del Proveedor</h5>
                                <div class="table-responsive">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" id="ListaArchivoAdjuntosProveedor" width="100%">
                                        <thead>
                                            <tr>
                                                <th class="invisible">#</th>
                                                <th width="10">#</th>
                                                <th>Archivo</th>
                                                <th width="120">
                                                    <center><button class="btn btn-xs btn-success activation" onclick="agregarAdjuntoProveedor(event);" id="btn-add" data-toggle="tooltip" data-placement="bottom" title="Agregar Detalle" disabled="disabled"><i class="fas fa-plus"></i>
                                                    </button></center>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            </div>

                        </form>
                    </section>
                </div>

            </div>
    </div>
</div>


@include('logistica.proveedores.modal_gestionar_archivo_adjunto_proveedor')
@include('logistica.proveedores.modal_gestionar_contacto')
@include('logistica.proveedores.modal_gestionar_cuenta_bancaria')
@include('logistica.proveedores.modal_gestionar_establecimiento')
@include('logistica.proveedores.modal_lista_proveedores')

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
    <script src="{{('/js/logistica/proveedores/modal_lista_proveedores.js')}}"></script>
    <script src="{{('/js/logistica/proveedores/tab_archivos_adjuntos_proveedor.js')}}"></script>
    <script src="{{('/js/logistica/proveedores/tab_contactos.js')}}"></script>
    <script src="{{('/js/logistica/proveedores/tab_cuentas_bancarias.js')}}"></script>
    <script src="{{('/js/logistica/proveedores/tab_establecimientos.js')}}"></script>
    <script src="{{('/js/logistica/proveedores/tab_proveedor.js')}}"></script>
    <script src="{{('/js/logistica/proveedores/gestionar_proveedores.js')}}"></script>
    <script src="{{('/js/publico/consulta_sunat.js')}}"></script>

@endsection