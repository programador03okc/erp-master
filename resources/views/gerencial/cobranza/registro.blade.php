@extends('layout.main')
@include('layout.menu_gerencial')

@section('cabecera') Cobranzas de ventas @endsection

@section('estilos')
    <link href='{{ asset("template/plugins/bootstrap-select/dist/css/bootstrap-select.min.css") }}' rel="stylesheet" type="text/css" />
    <style>
        .group-okc-ini {
            display: flex;
            justify-content: start;
        }
        .selecionar,
        table tbody tr td {
            cursor: pointer;
        }
        .eventClick {
            background-color: #f7d9d9 !important;
        }
        .mb-3 {
            margin-bottom: 15px;
        }

        .flag-rojo {
            background-color: #ffd6d6;
        }

        .flag-amarillo {
            background-color: #fff799;
        }
        .label-check {
            cursor: pointer;
            font-weight: normal;
        }
    </style>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('gerencial.index')}}"><i class="fas fa-tachometer-alt"></i> Gerencial</a></li>
    <li>Cobranzas de ventas</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
@if (in_array(307,$array_accesos))
<div class="page-main" type="usuarios">
    <div class="box box-solid">
        <div class="box-header">
            <h3 class="box-title">Lista de registro</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table class="mytable table table-striped table-condensed table-bordered" id="tablaCobranza">
                        <thead>
                            <tr>
                                <th hidden></th>
                                <th width="10">Emp</th>
								<th width="90">OCAM</th>
								<th>Nombre del Cliente</th>
								<th width="70">Fact.</th>
								<th width="11">UU. EE</th>
								<th width="11">FTE. FTO</th>
								<th width="15">OC Fisica</th>
								<th width="11">SIAF</th>
								<th width="60">Fec. Emis</th>
								<th width="60">Fec. Recep</th>
								<th width="12">Periodo</th>
								<th width="12">Atraso</th>
								<th width="12">Mon</th>
								<th>Importe</th>
								<th id="tdEst">Estado</th>
								<th id="tdResp">A. Respo.</th>
								<th width="10">Fase</th>
								<th class="hidden">Tipo</th>
                                <th width="60">Fec. inicio / entrega </th>
								<th id="tdAct">-</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-cobranza" data-action="modal">
	<div class="modal-dialog" style="width: 70%;">
		<div class="modal-content">
			<form class="formPage" id="formulario" form="cobranza" type="register" data-form="guardar-formulario">
				<div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title"></h3>
				</div>
				<div class="modal-body">
                    <input type="hidden" name="id" id="id" value="0">
                    <input type="hidden" name="id_doc_ven" value="">
                    <input type="hidden" name="id_oc" value="">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <h6>Empresa</h6>
                                <select class="form-control input-sm" name="empresa" id="empresa" required>
                                    <option value="" disabled selected>Elija una opción</option>
                                    @foreach ($empresas as $empresa)
                                        @if (isset($empresa['contribuyente']))
                                            @if ($empresa['contribuyente']['nro_documento'] != null || $empresa['contribuyente']['nro_documento'] != '')
                                                <option value="{{ $empresa['id_empresa'] }}">[{{ $empresa['contribuyente']['nro_documento'] }}] - {{ $empresa['contribuyente']['razon_social'] }}</option>
                                            @endif
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>Sector</h6>
                                <select class="form-control input-sm" name="sector" id="sector" required>
                                    <option value="" disabled selected>Elija una opción</option>
                                    @foreach ($sector as $item)
                                        <option value="{{$item->id_sector }}">{{$item->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>Trámite</h6>
                                <select class="form-control input-sm" name="tramite" id="tramite" required>
                                    <option value="" disabled selected>Elija una opción</option>
                                    @foreach ($tipo_ramite as $item)
                                        <option value="{{$item->id_tipo_tramite }}">{{$item->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <h6>Periodo</h6>
                                <select name="periodo" id="periodo" class="form-control input-sm">
                                    @foreach ($periodo as $item)
                                        <option value="{{$item->id_periodo }}" {{ ($item->id_periodo == session()->get('cobranzaIdPeriodo') ?'selected' : '') }}>{{$item->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <h6>Cliente</h6>
                                <div class="input-group input-group-sm">
                                    <input type="hidden" name="id_cliente" id="id_cliente" value="0">
                                    <input type="text" class="form-control input-sm" name="cliente" id="cliente" placeholder="N° RUC" readonly>
                                    <span class="input-group-btn">
                                        <button class="btn btn-default btn-flat" type="button" onclick="listaClientes();">
                                            <span class="fa fa-search"></span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <h6>OC Fisica</h6>
                                <input type="text" class="form-control" name="orden_compra" id="orden_compra_nuevo" value="" placeholder="N° OC">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>Cuadro de Presup.</h6>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control input-sm text-center" name="cdp" id="cdp" placeholder="N° CDP">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default btn-flat buscarMgc" type="button" data-action="cdp">
                                            <span class="fa fa-search"></span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>OCAM</h6>
                                <div class="input-group input-group-sm">

                                    <input type="text" class="form-control input-sm text-center" name="oc" id="oc" required placeholder="OCAM">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default btn-flat buscarMgc" type="button" data-action="oc">
                                            <span class="fa fa-search"></span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <h6>Factura</h6>
                                <input type="text" class="form-control input-sm text-center buscar-factura" name="fact" id="fact" required placeholder="N° Fact">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <h6>N° SIAF</h6>
                                <input type="text" class="form-control input-sm text-center" name="siaf" id="siaf" placeholder="SIAF">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <h6>Unidad Ejec.</h6>
                                <input type="text" class="form-control input-sm text-center" name="ue" id="ue" placeholder="UU.EE">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>FTE FTO.</h6>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control input-sm text-center" name="ff" id="ff" readonly>
                                    <span class="input-group-btn">
                                        <button class="btn btn-info btn-flat" type="button" onclick="buscarFuente();">
                                            <span class="fa fa-search"></span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>Importe</h6>
                                <div class="group-okc-ini">
                                    <select class="form-control input-sm" name="moneda" id="moneda" style="width: 40%;" required>
                                        <option value="1" selected>S/.</option>
                                        <option value="2">$</option>
                                    </select>
                                    <input type="text" class="form-control input-sm numero text-right" name="importe" id="importe" required placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>Categoría</h6>
                                <input type="text" class="form-control input-sm text-center" name="categ" id="categ" placeholder="Categoría">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <h6>Fecha Emisión</h6>
                                <input type="date" class="form-control input-sm text-center" name="fecha_emi" id="fecha_emi"
                                required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <h6>Fecha Recepción</h6>
                                <input type="date" class="form-control input-sm text-center dias-atraso" name="fecha_rec" id="fecha_rec" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>Estado Documento</h6>
                                <select class="form-control input-sm" name="estado_doc" id="estado_doc" required>
                                    <option value="" disabled selected>Elija una opción</option>
                                    @foreach ($estado_documento as $item)
                                        <option value="{{$item->id_estado_doc}}" >{{$item->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <h6>Fecha Actual</h6>
                                <input type="date" class="form-control input-sm text-center" name="fecha_act" id="fecha_act" value="{{date('Y-m-d')}}" disabled>
                            </div>
                        </div>
                    </div>
					<div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>Fecha Pago (próx)</h6>
                                <input type="date" class="form-control input-sm text-center dias-atraso" data-form="editar-formulario" name="fecha_ppago" id="fecha_ppago" value="{{date('Y-m-d')}}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <h6>Días Atraso</h6>
                                <input type="hidden" name="dias_atraso" value="0">
                                <input type="text" class="form-control input-sm text-center" name="atraso" id="atraso" value="0" data-form="guardar-formulario" disabled>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <h6>Plazo Crédito</h6>
                                <input type="text" class="form-control input-sm text-center" name="plazo_credito" id="plazo_credito" value="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>Nombre del Vendedor</h6>
                                <select name="vendedor" class="selectpicker" title="Elija un vendedor" data-live-search="true" data-width="100%" data-actions-box="true" data-size="5" required>
                                    @foreach ($vendedores as $vendedor)
                                        <option value="{{ $vendedor->id_vendedor }}">{{ $vendedor->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <h6>Area</h6>
                                <select class="form-control input-sm" name="area" id="area" required>
                                    <option value="1" selected>Almacén</option>
                                    <option value="2">Contabilidad</option>
                                    <option value="3">Logística</option>
                                    <option value="4">Tesorería</option>
                                </select>
                            </div>
                        </div>
					</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h6>Fecha inicio :</h6>
                                        <input id="fecha_inicio_nuevo" class="form-control input-sm text-center" type="date" name="fecha_inicio">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h6>Fecha termino :</h6>
                                        <input id="fecha_entrega_nuevo" class="form-control input-sm text-center" type="date" name="fecha_entrega">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal"><span class="fa fa-time"></span> Cerrar</button>
					<button type="submit" class="btn btn-success"><span class="fa fa-save"></span> Guardar</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-lista-cliente">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Lista de Clientes</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-hover" id="tablaClientes" width="100%" style="font-size: 11px;">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th hidden>ID Contribuyente</th>
                                    <th width="100">Nro Documento</th>
                                    <th>Nombre</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-success" id="btnAgregarCliente"> Seleccionar</button>
            </div>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="lista-procesadas">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="lista-procesadas">Lista ventas procesadas</h3>

            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="mytable table table-striped table-condensed table-bordered" id="tablaVentasProcesadas">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>OC</th>
                                    <th>CDP</th>
                                    <th>Documento</th>
                                    <th>Fecha Emisión</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button class="btn btn-success" id="btnAgregarMgc">Seleccionar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-fue-fin">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">Fuente de Financiamiento</h3>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-12">
								<h5>Fuente Financiamiento</h5>
								<select class="form-control input-sm" name="fuente" id="fuente" onchange="fuenteFinan(this.value);">
									<option value="" disabled selected>Elija una opción</option>
									<option value="1">RECURSOS ORDINARIOS</option>
									<option value="2">RECURSOS DIRECTAMENTE RECAUDADOS</option>
									<option value="3">RECURSOS POR OPERACIONES OFICIALES DE CREDITO</option>
									<option value="4">DONACIONES Y TRANSFERENCIAS</option>
									<option value="5">RECURSOS DETERMINADOS</option>
								</select>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<h5>Rubro</h5>
								<select class="form-control input-sm" name="rubro" id="rubro">
									<option value="" disabled selected>Elija una opción</option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				<button type="button" class="btn btn-sm btn-success" id="btnAgregarFuente">Seleccionar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-agregar-fase">
	<div class="modal-dialog" >
		<div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Fases</h3>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <form class="formPage" id="formulario-fase">
                            <input type="hidden" name="id_registro_cobranza">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Fase :</label>
                                        <select class="form-control" name="fase" required>
                                            <option value="" disabled selected>Elija una opción</option>
                                            <option value="COMPROMISO">COMPROMISO</option>
                                            <option value="DEVENGADO">DEVENGADO</option>
                                            <option value="PAGADO">PAGADO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Fecha :</label>
                                        <input type="date" class="form-control text-center" name="fecha_fase" value="{{date('Y-m-d')}}" required>
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-block btn-sm btn-success"><span class="fa fa-save"></span> Grabar Fase</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-condensed table-hover">
                            <thead>
                                <tr>
                                    <th>Fases</th>
                                    <th>Fecha</th>
                                    <th>Accion</th>
                                </tr>
                            </thead>
                            <tbody id="resultadoFase"></tbody>
                        </table>
                    </div>
                </div>
            </div>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-observaciones">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">Lista de observaciones </h3>
			</div>
			<div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <form id="formulario-observaciones">
                            <input type="hidden" name="cobranza_id" value="0">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Descripcion</label>
                                        <textarea class="form-control input-sm" name="descripcion" id="descripcion_observacion" rows="3" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-block btn-sm btn-success"><span class="fa fa-save"></span> Grabar Observación</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
				<div class="row">
					<div class="col-md-12">
                        <table class="table table-bordered table-condensed table-hover">
                            <thead>
                                <tr>
                                    <th>Descripción</th>
                                    <th>Usuario</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>-</th>
                                </tr>
                            </thead>
                            <tbody id="resultadoObservaciones"></tbody>
                        </table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-filtros">
	<div class="modal-dialog" style="width: 500px;">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">Filtros</h3>
			</div>
			<div class="modal-body">
                <form class="form-horizontal" id="form-filtros">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="checkEmpresa" name="checkEmpresa" @if (session('cobranzaEmpresa') !== null) checked @endif>
                                        <label class="text-muted label-check" for="checkEmpresa">Empresa</label>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control" name="filterEmpresa" id="filterEmpresa">
                                        <option value="" selected disabled>Elija una opción</option>
                                        @foreach ($empresas as $empresa)
                                            @if (isset($empresa['contribuyente']))
                                                @if ($empresa['contribuyente']['nro_documento'] != null || $empresa['contribuyente']['nro_documento'] != '')
                                                    <option @if (session()->has('cobranzaEmpresa')) @if ($empresa['codigo'] == session('cobranzaEmpresa')) selected @endif @endif
                                                        value="{{ $empresa['codigo'] }}">[{{ $empresa['contribuyente']['nro_documento'] }}] - {{ $empresa['contribuyente']['razon_social'] }}</option>
                                                @endif
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="checkFase" name="checkFase" @if (session('cobranzaFase') !== null) checked @endif>
                                        <label class="text-muted label-check" for="checkFase">Fases</label>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control input-sm" name="filterFase" id="filterFase">
                                        <option value="" selected disabled>Elija una opción</option>
                                        @foreach ($fases as $fase)
                                            <option @if (session()->has('cobranzaFase')) @if ($fase->descripcion == session('cobranzaFase')) selected @endif @endif
                                                value="{{ $fase->descripcion }}">{{ $fase->descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="checkEstadoDoc" name="checkEstadoDoc" @if (session('cobranzaEstadoDoc') !== null) checked @endif>
                                        <label class="text-muted label-check" for="checkEstadoDoc">Estado cobranza</label>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control input-sm" name="filterEstadoDoc" id="estado_doc" required>
                                        <option value="" disabled selected>Elija una opción</option>
                                        @foreach ($estado_documento as $estado)
                                        <option @if (session()->has('cobranzaEstadoDoc')) @if ($estado->nombre == session('cobranzaEstadoDoc')) selected @endif @endif
                                            value="{{ $estado->nombre }}">{{ $estado->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="checkEmi" name="checkEmi" @if (session('cobranzaEmisionDesde') !== null) checked @endif>
                                        <label class="text-muted label-check" for="checkEmi">Fecha Emisión</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <input type="date" class="form-control input-sm" name="filterEmisionDesde" id="filterEmisionDesde" value="@if(session('cobranzaEmisionDesde') !== null){{session('cobranzaEmisionDesde')}}@else{{date('Y-m-d')}}@endif">
                                </div>
                                <div class="col-md-4">
                                    <input type="date" class="form-control input-sm" name="filterEmisionHasta" id="filterEmisionHasta" value="@if(session('cobranzaEmisionHasta') !== null){{session('cobranzaEmisionHasta')}}@else{{date('Y-m-d')}}@endif">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="checkPenalidad" name="checkPenalidad" @if (session('cobranzaPenalidad') !== null) checked @endif>
                                        <label class="text-muted label-check" for="checkPenalidad">Lista que tienen penalidad</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
			</div>
			<div class="modal-footer">
                <button type="button" class="btn btn-pill btn-default shadow-none" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-penalidad">
	<div class="modal-dialog" style="width: 700px;">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title"> </h3>
			</div>
			<div class="modal-body">
                <form id="formulario-penalidad" data-formulario="">
                    <input type="hidden" name="id_penalidad" value="0">
                    <input type="hidden" name="tipo_registro" value="">
                    <input type="hidden" name="id_cobranza" value="0">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Fecha :</label>
                                <input type="date" class="form-control input-sm text-center" name="fecha_penal" id="fecha_penal" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>N° Comprobante</label>
                                <input type="text" class="form-control input-sm text-cente" name="doc_penal" id="doc_penal" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Importe</label>
                                <input type="text" class="form-control input-sm numero" name="importe_penal" id="importe_penal" placeholder="0.00" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Detalle</label>
                                <textarea class="form-control input-sm" name="obs_penal" id="obs_penal" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-block btn-sm btn-success" id="btnPenalidad">Grabar <span class="fa fa-save"></span></button>
                        </div>
                    </div>
                </form>
				<div class="row">
					<div class="col-md-12">
						<fieldset>
                            <legend><h4 class="titulo-form"></h4></legend>
							<table class="table table-bordered table-hover table-aux text-center" id="tablaPenalidad">
								<thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Comprobante</th>
                                        <th>Importe</th>
                                        <th>Estado</th>
                                        <th class="estado-penalidad">Estado de Penalidad</th>
                                        <th>Fecha</th>
                                        <th width="120">-</th>
                                    </tr>
                                </thead>
								<tbody id="resultadoPenalidades"></tbody>
							</table>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@else
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-danger pulse" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <span class="sr-only">Error de Accesos:</span>
            Solicite los accesos
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
    <script src="{{ asset('template/plugins/loadingoverlay.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script>
    <script src="{{ asset('template/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('template/plugins/moment/locale/es.js') }}"></script>
    <script src="{{ asset('template/plugins/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('template/plugins/bootstrap-select/dist/js/i18n/defaults-es_ES.min.js') }}"></script>
    <script src="{{ asset('js/util.js') }}"></script>

    <script>

    var array_accesos = JSON.parse('{!!json_encode($array_accesos)!!}');

    // let csrf_token = '{{ csrf_token() }}';

    let carga_ini = 1;
    let tempClienteSelected = {};
    let tempoNombreCliente = '';
    let userNickname = '';

    const idioma = {
        sProcessing: "<div class='spinner'></div>",
        sLengthMenu: "Mostrar _MENU_ registros",
        sZeroRecords: "No se encontraron resultados",
        sEmptyTable: "Ningún dato disponible en esta tabla",
        sInfo: "Del _START_ al _END_ de un total de _TOTAL_ registros",
        sInfoEmpty: "Del 0 al 0 de un total de 0 registros",
        sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
        sInfoPostFix: "",
        sSearch: "Buscar:",
        sUrl: "",
        sInfoThousands: ",",
        sLoadingRecords: "Cargando...",
        oPaginate: {
            sFirst: "Primero",
            sLast: "Último",
            sNext: "Siguiente",
            sPrevious: "Anterior"
        },
        oAria: {
            sSortAscending:
                ": Actilet para ordenar la columna de manera ascendente",
            sSortDescending:
                ": Activar para ordenar la columna de manera descendente"
        }
    };

    let periodoSelect = {!! $periodo !!};
    let periodoActivo = {!! session('cobranzaPeriodo') !!};
    let idCliente = 0;
    let nombreCliente = '';
    let idRequerimiento = 0;
    let actualizar = false;
    let spanFiltro = 0;

    $(document).ready(function() {
        $('.main-header nav.navbar.navbar-static-top').find('a.sidebar-toggle').click();
        $('.numero').number(true, 2);
    });

    function formatRepo (repo) {
        if (repo.id) {
            return repo.text;
        }
        var state = $(
            `<span>`+repo.text+`</span>`
        );
        return state;

    }

    function formatRepoSelection (repo) {
        return repo.nombre || repo.text;
    }
    </script>
    {{--  <script src="{{ asset('js/gerencial/cobranza/registro.js') }}"></script>  --}}
    <script src="{{ asset('js/gerencial/cobranza/rc_ventas.js') }}"></script>
@endsection
