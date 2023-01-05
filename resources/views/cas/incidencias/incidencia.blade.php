@extends('layout.main')
@include('layout.menu_cas')

@section('cabecera')
Registro de incidencia
@endsection

@section('estilos')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
<style>
    .d-none{
        display: none;
    }
</style>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('cas.index')}}"><i class="fas fa-tachometer-alt"></i> Servicios CAS</a></li>
    <li>Garantías</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')

<div class="page-main" type="incidencia">

    <form id="form-incidencia">
    <div class="box">
        <div class="box-header with-border">

            <h3 class="box-title">Incidencia N° <span class="badge badge-secondary" id="codigo_ficha">INC 00-000</span></h3>
            <div class="box-tools pull-right">

                <button type="button" class="btn btn-sm btn-warning nueva-incidencia" data-toggle="tooltip" data-placement="bottom"
                    title="Nueva Incidencia">
                    <i class="fas fa-copy"></i> Nuevo
                </button>

                <input id="submit_incidencia" class="btn btn-sm btn-success guardar-incidencia" type="submit" style="display: none;"
                    data-toggle="tooltip" data-placement="bottom" title="Actualizar Incidencia" value="Guardar">

                <button type="button" class="btn btn-sm btn-primary edit-incidencia"
                    data-toggle="tooltip" data-placement="bottom" title="Editar Incidencia">
                    <i class="fas fa-pencil-alt"></i> Editar
                </button>

                <button type="button" class="btn btn-sm btn-danger anular-incidencia" data-toggle="tooltip" data-placement="bottom"
                    title="Anular Incidencia" onClick="anularIncidencia();">
                    <i class="fas fa-trash"></i> Anular
                </button>

                <button type="button" class="btn btn-sm btn-info buscar-incidencia" data-toggle="tooltip" data-placement="bottom"
                    title="Buscar historial de registros" onClick="abrirIncidenciaModal();">
                    <i class="fas fa-search"></i> Buscar</button>

                <button type="button" class="btn btn-sm btn-secondary cancelar" data-toggle="tooltip" data-placement="bottom"
                    title="Cancelar" style="display: none;">
                     Cancelar</button>
                     |
                <button type="button" class="btn btn-sm btn-default" data-toggle="tooltip" data-placement="bottom"
                    title="Imprimir incidencia" onClick="imprimirIncidencia();"><i class="fas fa-file-pdf"></i> Reporte de Incidencia</button>

                <button type="button" class="btn btn-sm btn-default" data-toggle="tooltip" data-placement="bottom"
                    title="Ficha de atención en blanco" onClick="imprimirFichaAtencionBlanco();"><i class="fas fa-file-pdf"></i> Ficha Atención en blanco</button>

            </div>
        </div>
        <div class="box-body">
            <div class="row" style="margin-bottom:0px">
                <div class="col-md-12">
                    <label style="font-weight: bold;">Seleccione los datos del negocio:</label>
                    <button type="button" class="btn btn-sm btn-secondary edition" data-toggle="tooltip" data-placement="bottom"
                        title="Buscar historial de registros" onClick="openSalidasVentaModal();">
                         Buscar </button>
                </div>
            </div>

            <input type="text" style="display:none;" name="id_incidencia">
            <input type="text" style="display:none;" name="id_mov_alm" class="limpiarIncidencia">
            <input type="text" style="display:none;" name="id_guia_ven" class="limpiarIncidencia">
            <input type="text" style="display:none;" name="id_requerimiento" class="limpiarIncidencia">
            <input type="text" style="display:none;" name="id_contribuyente" class="limpiarIncidencia">
            {{-- <input type="text" style="display:none;" name="id_empresa"> --}}
            <input type="text" style="display:none;" name="codigo_oportunidad" class="limpiarIncidencia">
            <input type="text" style="display:none;" name="id_contacto" class="limpiarIncidencia">

            <fieldset class="group-table" id="fieldsetDatosNegocio">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-horizontal">
                            <div class="form-group" style="margin-bottom:10px;">
                                <label class="col-sm-4 control-label">Empresa</label>
                                <div class="col-sm-8">
                                    <select class="form-control js-example-basic-single edition limpiarIncidencia"
                                        name="id_empresa" required>
                                        <option value="">Elija una opción</option>
                                        @foreach ($empresas as $empresa)
                                        <option value="{{$empresa->id_empresa}}">{{$empresa->razon_social}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:15px;">
                                <label class="col-sm-4 control-label">Factura venta</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control edition limpiarIncidencia" name="factura" placeholder="000-0000"/>
                                </div>
                            </div>
                            <div class="form-group" style="margin-top:5px">
                                <label class="col-sm-4 control-label">Fecha reporte</label>
                                <div class="col-sm-8">
                                    <input type="date" class="form-control edition limpiarIncidencia" name="fecha_reporte"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-horizontal">
                            <div class="form-group" style="margin-bottom:10px">
                                <label class="col-sm-3 control-label">Cliente</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control edition limpiarIncidencia" name="cliente_razon_social"/>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:15px">
                                <label class="col-sm-3 control-label">Nro Orden</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control edition limpiarIncidencia" name="nro_orden"/>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:5px">
                                <label class="col-sm-3 control-label">Cód. CDP</label>
                                <div class="col-sm-9">
                                    <div class="form-control-static limpiarTexto codigo_oportunidad"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-horizontal">
                            <div class="form-group" style="margin-bottom:0px">
                                <label class="col-sm-4 control-label">Sede cliente</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control edition limpiarIncidencia" name="sede_cliente"/>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:0px;">
                                <label class="col-sm-4 control-label">Fecha registro</label>
                                <div class="col-sm-8">
                                    <div class="form-control-static limpiarTexto fecha_registro"></div>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:0px;">
                                <label class="col-sm-4 control-label">Responsable</label>
                                <div class="col-sm-8">
                                    <select class="form-control js-example-basic-single edition limpiarIncidencia"
                                        name="id_responsable" required>
                                        <option value="">Elija una opción</option>
                                        @foreach ($usuarios as $usuario)
                                        <option value="{{$usuario->id_usuario}}">{{$usuario->nombre_corto}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
            <br/>
            <div class="row" style="margin-bottom:0px">
                <div class="col-md-12">
                    <label style="font-weight: bold;">Seleccione los datos del contacto:</label>
                    <button type="button" class="btn btn-sm btn-secondary edition" data-toggle="tooltip" data-placement="bottom"
                        title="Buscar historial de registros" onClick="openContacto();">
                         Contacto </button>
                </div>
            </div>

            <fieldset class="group-table" id="fieldsetDatosContacto">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-horizontal">
                            <div class="form-group " style="margin-bottom:5px;">
                                <label class="col-sm-4 control-label">Quien reporta</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control edition limpiarIncidencia" name="usuario_final"/>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:5px;">
                                <label class="col-sm-4 control-label">Contacto </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control edition limpiarIncidencia" name="nombre_contacto"/>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:5px">
                                <label class="col-sm-4 control-label">Cargo / Área</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control edition limpiarIncidencia" name="cargo_contacto"/>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="form-horizontal">

                            <div class="form-group" style="margin-bottom:5px">
                                <label class="col-sm-2 control-label">Dirección</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control edition limpiarIncidencia" name="direccion_contacto"/>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:5px">
                                <label class="col-sm-2 control-label">Ubigeo</label>
                                <div class="col-sm-10" style="display:flex;">
                                    <input class="oculto" name="id_ubigeo_contacto"/>
                                    <input type="text" class="form-control" name="ubigeo_contacto" readOnly>
                                    <button type="button" class="input-group-text btn-primary edition" id="basic-addon1"
                                        onClick="abrirUbigeoModal('incidencia');">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:5px">
                                <label class="col-sm-2 control-label">Teléfono</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control edition limpiarIncidencia" name="telefono_contacto"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-horizontal">
                            <div class="form-group" style="margin-bottom:5px">
                                <label class="col-sm-3 control-label">Horario</label>
                                <div class="col-sm-9">
                                    <div class="form-control-static limpiarTexto horario_contacto d-none"></div>
                                    <input class="form-control edition limpiarIncidencia" type="text" name="horario_contacto" value="">
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:5px">
                                <label class="col-sm-3 control-label">Correo</label>
                                <div class="col-sm-9">
                                    <div class="form-control-static limpiarTexto email_contacto d-none"><label class=""></label></div>
                                    <input class="form-control edition limpiarIncidencia" type="text" name="email_contacto" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>

            <br/>
            <div class="row" style="margin-bottom:0px">
                <div class="col-md-12">
                    <label style="font-weight: bold;">Ingrese los datos del producto:</label>
                    {{-- <button type="button" class="btn btn-sm btn-secondary edition" data-toggle="tooltip" data-placement="bottom"
                        title="Buscar historial de registros" onClick="incidenciaProductoCreate();">
                         Agregar </button> --}}
                </div>
            </div>
            <fieldset class="group-table" id="fieldsetProductos">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-horizontal">
                            <div class="form-group" style="margin-bottom:5px;">
                                <label class="col-sm-4 control-label">Serie </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control edition limpiarIncidencia" name="serie" required/>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:5px">
                                <label class="col-sm-4 control-label">Marca</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control edition limpiarIncidencia d-none" name="marca"/>
                                    <select class="form-control edition limpiarIncidencia" name="marca" required>
                                        <option value="" disabled>Elija una opción</option>
                                        @foreach ($cas_marca as $item)
                                        <option value="{{ $item->descripcion }}">{{ $item->descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="form-horizontal">
                            <div class="form-group" style="margin-bottom:5px">
                                <label class="col-sm-2 control-label">Producto</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control edition limpiarIncidencia d-none" name="producto" />
                                    <select class="form-control edition limpiarIncidencia" name="producto" required>
                                        <option value="" disabled>Elija una opción</option>
                                        @foreach ($cas_producto as $item)
                                        <option value="{{ $item->descripcion }}">{{ $item->descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:5px">
                                <label class="col-sm-2 control-label">Tipo</label>
                                <div class="col-sm-10">
                                    <select class="form-control js-example-basic-single edition limpiarIncidencia" name="id_tipo" required>
                                        <option value="">Elija una opción</option>
                                        @foreach ($tiposProducto as $tp)
                                        <option value="{{$tp->id_tipo}}">{{$tp->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-horizontal">
                            <div class="form-group" style="margin-bottom:5px">
                                <label class="col-sm-3 control-label">Modelo</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control edition limpiarIncidencia d-none" name="modelo"/>
                                    <select class="form-control edition limpiarIncidencia" name="modelo" required>
                                        <option value="" disabled>Elija una opción</option>
                                        @foreach ($cas_modelo as $item)
                                        <option value="{{ $item->descripcion }}">{{ $item->descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <table class="mytable table table-condensed table-bordered table-okc-view" width="100%"
                    id="seriesProductos" style="margin-top:14px;">
                    <thead>
                        <tr>
                            <th>Serie</th>
                            <th>Producto</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table> --}}
            </fieldset>

            <br/>
            <div class="row" style="margin-bottom:0px">
                <div class="col-md-12">
                    <label style="font-weight: bold;">Ingrese los datos de la avería:</label>
                </div>
            </div>
            <fieldset class="group-table" id="fieldsetFallaReportada">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-horizontal">
                            <div class="form-group " style="margin-bottom:5px;">
                                <label class="col-sm-4 control-label">Tipo de falla</label>
                                <div class="col-sm-8">
                                    <select class="form-control js-example-basic-single edition limpiarIncidencia"
                                        name="id_tipo_falla">
                                        <option value="">Elija una opción</option>
                                        @foreach ($tipoFallas as $falla)
                                        <option value="{{$falla->id_tipo_falla}}">{{$falla->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group " style="margin-bottom:5px;">
                                <label class="col-sm-4 control-label">Modo</label>
                                <div class="col-sm-8">
                                    <select class="form-control js-example-basic-single edition limpiarIncidencia"
                                        name="id_modo">
                                        <option value="">Elija una opción</option>
                                        @foreach ($modos as $modo)
                                        <option value="{{$modo->id_modo}}">{{$modo->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group " style="margin-bottom:5px;">
                                <label class="col-sm-4 control-label">Tipo garantía</label>
                                <div class="col-sm-8">
                                    <select class="form-control js-example-basic-single edition limpiarIncidencia"
                                        name="id_tipo_garantia">
                                        <option value="">Elija una opción</option>
                                        @foreach ($tiposGarantia as $tipo)
                                        <option value="{{$tipo->id_tipo_garantia}}">{{$tipo->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-horizontal">
                            <div class="form-group " style="margin-bottom:5px;">
                                <label class="col-sm-4 control-label">Tipo de servicio</label>
                                <div class="col-sm-8">
                                    <select class="form-control js-example-basic-single edition limpiarIncidencia"
                                        name="id_tipo_servicio">
                                        <option value="">Elija una opción</option>
                                        @foreach ($tipoServicios as $servicio)
                                        <option value="{{$servicio->id_tipo_servicio}}">{{$servicio->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group " style="margin-bottom:5px;">
                                <label class="col-sm-4 control-label">Medio reporte</label>
                                <div class="col-sm-8">
                                    <select class="form-control js-example-basic-single edition limpiarIncidencia"
                                        name="id_medio">
                                        <option value="">Elija una opción</option>
                                        @foreach ($medios as $medio)
                                        <option value="{{$medio->id_medio}}">{{$medio->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group " style="margin-bottom:5px;">
                                <label class="col-sm-4 control-label">Atiende</label>
                                <div class="col-sm-8">
                                    <select class="form-control js-example-basic-single edition limpiarIncidencia"
                                        name="id_atiende">
                                        <option value="">Elija una opción</option>
                                        @foreach ($atiende as $at)
                                        <option value="{{$at->id_atiende}}">{{$at->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-horizontal">
                            <div class="form-group " style="margin-bottom:5px;">
                                <div class="col-sm-6">
                                    <div class="icheckbox_flat-blue">
                                        <label style="display:flex;">
                                            {{-- <input type="checkbox" class="flat-red activation" name="equipo_operativo" value="0"> --}}
                                            <span style="margin-top: 7px;margin-bottom: 10px;margin-right: 25px;">Equipo operativo</span>
                                            <input type="checkbox" name="equipo_operativo" style="margin-top: 10px;"/>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group " style="margin-bottom:5px;">
                                <label class="col-sm-4 control-label">Conformidad</label>
                                <div class="col-sm-8">
                                    <select class="form-control js-example-basic-single edition limpiarIncidencia"
                                        name="conformidad">
                                        <option value="">Elija una opción</option>
                                        <option value="PRE">PRE</option>
                                        <option value="POST">POST</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group " style="margin-bottom:5px;">
                                <label class="col-sm-4 control-label">Nro. de caso</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control edition limpiarIncidencia" name="numero_caso"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-horizontal">
                            <div class="form-group " style="margin-bottom:0px;">
                                {{-- <label class="col-sm-4 control-label">Ingrese la falla reportada</label> --}}
                                <div class="col-sm-12">
                                    <textarea class="form-control edition limpiarIncidencia" name="falla_reportada"
                                    placeholder="Ingrese la falla reportada"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
            <br/>
            <div class="row" style="margin-bottom:0px">
                <div class="col-md-12">
                    <label style="font-weight: bold;">Cierre de la incidencia:</label>
                </div>
            </div>
            <fieldset class="group-table" id="fieldsetComentariosCierre">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-horizontal">
                            <div class="form-group " style="margin-bottom:5px;">
                                <label class="col-sm-6 control-label">Costo del servicio contratado (S/)</label>
                                <div class="col-sm-6">
                                    <input type="number" class="form-control edition limpiarIncidencia" name="importe_gastado"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-horizontal">
                            <div class="form-group " style="margin-bottom:5px;">
                                <label class="col-sm-6 control-label">Parte reemplazada</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control edition limpiarReporte" name="parte_reemplazada"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-horizontal">
                            <div class="form-group " style="margin-bottom:0px;">
                                {{-- <label class="col-sm-4 control-label">Ingrese la falla reportada</label> --}}
                                <div class="col-sm-12">
                                    <textarea class="form-control edition limpiarIncidencia" name="comentarios_cierre"
                                    placeholder="Ingrese los comentarios del cierre"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="box-footer">

        </div>
      </div>
    </form>
</div>
@include('cas.incidencias.incidenciaModal')
@include('cas.incidencias.salidasVentaModal')
@include('cas.incidencias.seriesProductosModal')
@include('almacen.distribucion.ordenDespachoContacto')
@include('almacen.distribucion.agregarContacto')
@include('publico.ubigeoModal')
@endsection

@section('scripts')
<script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
<!-- <script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
<script src="{{ asset('template/plugins/moment.min.js') }}"></script>
<script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
<script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>

<script src="{{ asset('js/cas/incidencias/incidenciaModal.js')}}?v={{filemtime(public_path('js/cas/incidencias/incidenciaModal.js'))}}"></script>
{{-- <script src="{{ asset('js/cas/incidencias/incidenciaProductoCreate.js')}}?v={{filemtime(public_path('js/cas/incidencias/incidenciaProductoCreate.js'))}}"></script> --}}
<script src="{{ asset('js/cas/incidencias/salidasVentaModal.js')}}?v={{filemtime(public_path('js/cas/incidencias/salidasVentaModal.js'))}}"></script>
<script src="{{ asset('js/cas/incidencias/seriesProductosModal.js')}}?v={{filemtime(public_path('js/cas/incidencias/seriesProductosModal.js'))}}"></script>
<script src="{{ asset('js/cas/incidencias/incidencia.js')}}?v={{filemtime(public_path('js/cas/incidencias/incidencia.js'))}}"></script>
<script src="{{ asset('js/almacen/distribucion/ordenDespachoContacto.js?')}}?v={{filemtime(public_path('js/almacen/distribucion/ordenDespachoContacto.js'))}}"></script>
<script src="{{ asset('js/almacen/distribucion/contacto.js?')}}?v={{filemtime(public_path('js/almacen/distribucion/contacto.js'))}}"></script>
<script src="{{ asset('js/publico/ubigeoModal.js?')}}?v={{filemtime(public_path('js/publico/ubigeoModal.js'))}}"></script>

{{-- <script src="{{ asset('js/almacen/customizacion/transformacion.js')}}?v={{filemtime(public_path('js/almacen/customizacion/transformacion.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/transformacionModal.js')}}?v={{filemtime(public_path('js/almacen/customizacion/transformacionModal.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/transfor_materia.js')}}?v={{filemtime(public_path('js/almacen/customizacion/transfor_materia.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/transfor_directo.js')}}?v={{filemtime(public_path('js/almacen/customizacion/transfor_directo.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/transfor_indirecto.js')}}?v={{filemtime(public_path('js/almacen/customizacion/transfor_indirecto.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/transfor_sobrante.js')}}?v={{filemtime(public_path('js/almacen/customizacion/transfor_sobrante.js'))}}"></script>
<script src="{{ asset('js/almacen/customizacion/transfor_transformado.js')}}?v={{filemtime(public_path('js/almacen/customizacion/transfor_transformado.js'))}}"></script> --}}

<script>
    $(document).ready(function() {
        seleccionarMenu(window.location);
        $(".select2").select2({
            tags: true
        });
    });
</script>
@endsection
