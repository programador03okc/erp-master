@extends('layout.main')
@include('layout.menu_gerencial')

@section('cabecera')
Cobranzas
@endsection

@section('estilos')
<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{asset('template/plugins/select2/select2.min.css')}}">
<style>
    .group-okc-ini {
        display: flex;
        justify-content: start;
    }
    .selecionar{
        cursor: pointer;
    }
</style>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('gerencial.index')}}"><i class="fas fa-tachometer-alt"></i> Gerencial</a></li>
    <li>Cobranzas</li>
    <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="usuarios">
    <div class="box box-solid">
        <div class="box-header">
            <h3 class="box-title">Lista de registro</h3>
            <div class="pull-right box-tools">
                <button type="button" class="btn btn-success" title="Nuevo Usuario" data-action="nuevo-registro">Nuevo registro</button>
                {{-- <button class="btn btn-primary" data-action="actualizar"><i class="fa fa-refresh"></i> Actualizar</button> --}}
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table class="mytable table table-striped table-condensed table-bordered" id="listar-registros">
                        <thead>
                            <tr>
                                <th></th>
                                <th width="10">Emp</th>
								<th width="50">OCAM</th>
								<th width="150">Nombre del Cliente</th>
								<th>Fact.</th>
								<th>UU. EE</th>
								<th width="10">FTE. FTO</th>
								<th width="15">OC Fisica</th>
								<th>SIAF</th>
								<th>Fec. Emis</th>
								<th>Fec. Recep</th>
								<th>Días A.</th>
								<th>Mon</th>
								<th>Importe</th>
								<th id="tdEst">Estado</th>
								<th id="tdResp">A. Respo.</th>
								<th width="10">Fase</th>
								<th class="hidden">Tipo</th>
                                <th width="10">Fec. inicio / entrega </th>
								{{-- <th width="10">Fecha entrega</th> --}}
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

{{-- crear registro de cobranza --}}
<div class="modal fade" tabindex="-1" role="dialog" id="modal-cobranza" data-action="modal">
	<div class="modal-dialog" style="width: 70%;">
		<div class="modal-content">
			<form class="formPage" id="formulario" form="cobranza" type="register" data-form="guardar-formulario">
				<div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Registro de Cobranza</h3>
				</div>
				<div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="id_doc_ven" value="">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <h6>Empresa</h6>
                                <select class="form-control input-sm" name="empresa" id="empresa" required>
                                    <option value="" disabled selected>Elija una opción</option>
                                    @foreach ($empresa as $item)
                                        <option value="{{$item->id_contribuyente }}">{{$item->razon_social }}</option>
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
                                        <option value="{{$item->id_periodo }}" {{ ($item->id_periodo===5?'selected':'') }} >{{$item->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <h6>Cliente</h6>
                                <input type="hidden" name="id_cliente" id="id_cliente" value="0">
                                <input type="hidden" name="id_contribuyente" value="0">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control input-sm" name="cliente" id="cliente" placeholder="N° RUC" readonly>
                                    <span class="input-group-btn">
                                        <button class="btn btn-default btn-flat" type="button" data-form="guardar-formulario" data-action="modal-search-customer">
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
                                    <input type="text" class="form-control input-sm text-center buscar-registro" name="cdp" id="cdp" placeholder="N° CDP" data-action="cdp">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default btn-flat modal-lista-procesadas" type="button" data-form="guardar-formulario">
                                            <span class="fa fa-search"></span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <input type="hidden"name="id_oc" >
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>OCAM</h6>
                                <div class="input-group input-group-sm">

                                    <input type="text" class="form-control input-sm text-center buscar-registro" name="oc" id="oc" required placeholder="OCAM" data-action="oc">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default btn-flat modal-lista-procesadas" type="button" data-form="guardar-formulario">
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
                                        <button class="btn btn-info btn-flat" type="button" onclick="searchSource('guardar-formulario');">
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
                                <input type="date" class="form-control input-sm text-center dias-atraso" data-form="guardar-formulario" name="fecha_rec" id="fecha_rec"  required>
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
                                <input type="text" class="form-control input-sm text-center dias-atraso" name="atraso" id="atraso" value="0" data-form="guardar-formulario" disabled>
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
                                {{-- <input type="text" class="form-control input-sm" name="nom_vendedor" id="nom_vendedor" placeholder="Nombre y Apellido"> --}}
                                <select class="select2 search-vendedor-guardar" name="nom_vendedor">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>Fecha inicio :</h6>
                                <input id="fecha_inicio_nuevo" class="form-control text-center" type="date" name="fecha_inicio">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <h6>Fecha entrega :</h6>
                                <input id="fecha_entrega_nuevo" class="form-control text-center" type="date" name="fecha_entrega">
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

<div class="modal fade" tabindex="-1" role="dialog" id="modal-editar-cliente">
	<div class="modal-dialog" style="width: 30%;">
		<div class="modal-content">
			<form class="formPage" type="register" data-form="editar">
				<div class="modal-header">
					<h3 class="modal-title">Editar Cliente</h3>
					<button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
                    <input type="hidden" name="id_cliente" value="">
                    <input type="hidden" name="id_contribuyente" value="">
                    <div class="row">
						<div class="col-md-12">
							<h5>Pais :</h5>
                            <select name="pais" id="nuevo_pais" class="form-control">
                                <option value="">Seleccione...</option>
                                @foreach ($pais as $items)
                                    <option value="{{ $items->id_pais }}">{{ $items->descripcion }}</option>
                                @endforeach
                            </select>
						</div>
					</div>
                    <div class="row">
						<div class="col-md-12">
							<h5>Departamento :</h5>
                            <select name="departamento" id="nuevo_provincia" data-select="departamento-select" class="form-control">
                                <option value="">Seleccione...</option>
                                @foreach ($departamento as $items)
                                    <option value="{{ $items->id_dpto }}">{{ $items->descripcion }}</option>
                                @endforeach
                            </select>
						</div>
					</div>
                    <div class="row">
						<div class="col-md-12">
							<h5>Provincia :</h5>
                            <select name="provincia" id="nuevo_provincia" class="form-control" data-select="provincia-select">
                                <option value="">Seleccione...</option>
                            </select>
						</div>
					</div>
                    <div class="row">
						<div class="col-md-12">
							<h5>Distrito :</h5>
                            <select name="distrito" id="nuevo_distrito" class="form-control">
                                <option value="">Seleccione...</option>
                            </select>
						</div>
					</div>
					<div class="row">
						<input type="hidden" class="form-control input-sm" name="edit_id" id="edit_id">
						<div class="col-md-12">
							<h5>RUC/DNI</h5>
							<input type="text" class="form-control input-sm" name="edit_ruc_dni_cliente" id="edit_ruc_dni_cliente" placeholder="Nro. Documento">
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<h5>Cliente <i style="font-size: 12px;">( Nombre de la empresa )</i></h5>
							<input type="text" class="form-control input-sm" name="edit_cliente" id="edit_cliente"
								placeholder="Razón Social/Nombre" onkeyup="javascript:this.value = this.value.toUpperCase();" disabled>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-sm btn-success" > Actualizar </button>

				</div>
			</form>
		</div>
	</div>
</div>

  {{-- editar cobranza --}}
<div class="modal fade" tabindex="-1" role="dialog" id="modal-editar-cobranza" data-action="modal">
	<div class="modal-dialog" style="width: 70%;">
		<div class="modal-content">
			<form data-form="editar-formulario" id="editar-formulario-cobranzas">
				<div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Editar Registro de Cobranza</h3>
				</div>
				<div class="modal-body">
                    {{-- <input type="hidden" name="id" id="id"> --}}
                    <input type="hidden" name="id_doc_ven" value="">
                    <input type="hidden" name="id_registro_cobranza" value="">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="empresa">Empresa</label>
                                <select class="form-control input-sm" name="empresa" required>
                                    <option value="" disabled selected>Elija una opción</option>
                                    @foreach ($empresa as $item)
                                        <option value="{{$item->id_contribuyente }}">{{$item->razon_social }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="sector">Sector</label>
                                <select class="form-control input-sm" name="sector" required>
                                    <option value="" disabled selected>Elija una opción</option>
                                    @foreach ($sector as $item)
                                        <option value="{{$item->id_sector }}">{{$item->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tramite">Trámite</label>
                                <select class="form-control input-sm" name="tramite" required>
                                    <option value="" disabled selected>Elija una opción</option>
                                    @foreach ($tipo_ramite as $item)
                                        <option value="{{$item->id_tipo_tramite }}">{{$item->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="periodo">Periodo</label>
                                <select name="periodo" class="form-control input-sm">
                                    @foreach ($periodo as $item)
                                        <option value="{{$item->id_periodo }}" >{{$item->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cliente">Cliente</label>
                                <input type="hidden" name="id_cliente" value="0">
                                <input type="hidden" name="id_contribuyente" value="0">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control input-sm" name="cliente" placeholder="N° RUC" readonly>
                                    <span class="input-group-btn">
                                        <button class="btn btn-default btn-flat" type="button" data-form="editar-formulario" data-action="modal-search-customer">
                                            <span class="fa fa-search"></span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="orden_compra_editar">OC Fisica</label>
                                <input type="text" class="form-control" name="orden_compra" id="orden_compra_editar" value="" placeholder="N° OC">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cdp">Cuadro de Presup.</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control input-sm text-center buscar-registro" name="cdp" placeholder="N° CDP" data-action="cdp">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default btn-flat modal-lista-procesadas" type="button" data-form="editar-formulario">
                                            <span class="fa fa-search"></span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <input type="hidden"name="id_oc">
                            <div class="form-group">
                                <label for="oc">Orden de Compra / OCAM</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control input-sm text-center buscar-registro" name="oc" required placeholder="N° OC / OCAM" data-action="oc">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default btn-flat modal-lista-procesadas" type="button" data-form="editar-formulario">
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
                                <label for="fact">Factura</label>
                                <input type="text" class="form-control input-sm text-center buscar-factura" name="fact" required placeholder="N° Fact">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="siaf">N° SIAF</label>
                                <input type="text" class="form-control input-sm text-center" name="siaf" placeholder="SIAF">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="ue">Unidad Ejec.</label>
                                <input type="text" class="form-control input-sm text-center" name="ue" placeholder="UU.EE">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="ff">FTE FTO.</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control input-sm text-center" name="ff" readonly>
                                    <span class="input-group-btn">
                                        <button class="btn btn-info btn-flat" type="button" onclick="searchSource('editar-formulario');">
                                            <span class="fa fa-search"></span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="importe">Importe</label>
                                <div class="group-okc-ini">
                                    <select class="form-control input-sm" name="moneda" required>
                                        <option value="1" selected>S/.</option>
                                        <option value="2">$</option>
                                    </select>
                                    <input type="number" class="form-control input-sm number text-right" name="importe" required placeholder="0.00" step="0.01">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="categ">Categoría</label>
                                <input type="text" class="form-control input-sm text-center" name="categ" placeholder="Categoría">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="fecha_emi">Fecha Emisión</label>
                                <input type="date" class="form-control input-sm text-center" name="fecha_emi"
                                required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="fecha_rec">Fecha Recepción</label>
                                <input type="date" class="form-control input-sm text-center dias-atraso" data-form="editar-formulario" name="fecha_rec" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="estado_doc">Estado Documento</label>
                                <select class="form-control input-sm" name="estado_doc" required>
                                    <option value="" disabled selected>Elija una opción</option>
                                    @foreach ($estado_documento as $item)
                                        <option value="{{$item->id_estado_doc}}" >{{$item->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="fecha_act">Fecha Actual</label>
                                <input type="date" class="form-control input-sm text-center" name="fecha_act" value="{{date('Y-m-d')}}" disabled>
                            </div>
                        </div>
                    </div>
					<div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="fecha_ppago">Fecha Pago (próx)</label>
                                <input type="date" class="form-control input-sm text-center dias-atraso" data-form="editar-formulario" name="fecha_ppago" value="">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="atraso">Días Atraso</label>
                                <input type="hidden" name="dias_atraso" value="0">
                                <input type="text" class="form-control input-sm text-center dias-atraso" name="atraso" value="0" data-form="editar-formulario" disabled>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="plazo_credito">Plazo Crédito</label>
                                <input type="text" class="form-control input-sm text-center" name="plazo_credito" value="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="nom_vendedor">Nombre del Vendedor</label>
                                {{-- <input type="text" class="form-control input-sm" name="nom_vendedor" placeholder="Nombre y Apellido"> --}}
                                <select class="select2 search-vendedor" name="nom_vendedor">
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="area">Area</label>
                                <select class="form-control input-sm" name="area" required>
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
                            <div class="form-group">
                                <label for="fecha_inicio_editar">Fecha inicio :</label>
                                <input id="fecha_inicio_editar" class="form-control" type="date" name="fecha_inicio">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fecha_entrega_editar">Fecha entrega :</label>
                                <input id="fecha_entrega_editar" class="form-control" type="date" name="fecha_entrega">
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

{{-- lista de oc y cdp --}}
<div class="modal fade" id="lista-procesadas" tabindex="-1" aria-labelledby="lista-procesadas" aria-hidden="true">
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
                        <table class="mytable table table-striped table-condensed table-bordered" id="lista-ventas-procesadas">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>OC</th>
                                    <th>CDP</th>
                                    <th>DOCUMENTO</th>
                                    <th>FECHE EMISION</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button class="btn btn-primary btn-seleccionar" data disabled>Seleccionar</button>
            </div>
        </div>
    </div>
</div>
{{-- lista de clientes --}}
<div class="modal fade" tabindex="-1" role="dialog" id="modal-buscar-cliente">
	<div class="modal-dialog" style="width: 70%;">
		<div class="modal-content">
			<form class="formPage" type="search">
				<div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
					<h3 class="modal-title">Catálogo de Clientes</h3>

				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							{{-- <div class="form-group">
                                <button class="btn btn-success btn-flat" title="Agregar Nuevo" type="button" id="add_new_customer"
								onclick="ModalAddNewCustomer();">
                                    <span class="fa fa-plus"></span> Nuevo
                                </button>
                                <button class="btn btn-warning btn-flat modal-editar" title="Editar" type="button" id="edit_customer"
                                     disabled>
                                    <span class="fa fa-edit"></span> Editar
                                </button>
                            </div> --}}
						</div>
						<div class="col-md-12">
							<table class="table table-hover" id="tabla-clientes" width="100%" style="font-size: 11px;">
								<thead>
									<tr>
										<th width="60">Código</th>
										<th>Nombre</th>
										<th width="100">Nro Documento</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-sm btn-success" id="btnAgregarCliente" disabled data-button="cobranza" data-action="agregar-cliente"> Aceptar </button>

				</div>
			</form>
		</div>
	</div>
</div>
<!-- Modal Fuente Financiamiento -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-fue-fin">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">Clasificador</h3>
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
				<button type="button" class="btn btn-sm btn-success select-source">Seleccionar <span class="fa fa-download"></span></button>
			</div>
		</div>
	</div>
</div>
{{-- nuevo cliente --}}
<div class="modal fade" tabindex="-1" role="dialog" id="modal-agregar-cliente">
	<div class="modal-dialog" style="width: 30%;">
		<div class="modal-content">
			<form class="formPage" type="register" data-form="guardar-cliente">
				<div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
					<h3 class="modal-title">Agregar Nuevo Cliente</h3>

				</div>
				<div class="modal-body">
                    <div class="row">
						<div class="col-md-12">
							<h5>Pais :</h5>
                            <select name="pais" id="nuevo_pais" class="form-control" required>
                                <option value="">Seleccione...</option>
                                @foreach ($pais as $items)
                                    <option value="{{ $items->id_pais }}">{{ $items->descripcion }}</option>
                                @endforeach
                            </select>
						</div>
					</div>
                    <div class="row">
						<div class="col-md-12">
							<h5>Departamento :</h5>
                            <select name="departamento" id="nuevo_provincia" data-select="departamento-select" class="form-control" required>
                                <option value="">Seleccione...</option>
                                @foreach ($departamento as $items)
                                    <option value="{{ $items->id_dpto }}">{{ $items->descripcion }}</option>
                                @endforeach
                            </select>
						</div>
					</div>
                    <div class="row">
						<div class="col-md-12">
							<h5>Provincia :</h5>
                            <select name="provincia" id="nuevo_provincia" class="form-control" data-select="provincia-select" required>
                                <option value="">Seleccione...</option>
                            </select>
						</div>
					</div>
                    <div class="row">
						<div class="col-md-12">
							<h5>Distrito :</h5>
                            <select name="distrito" id="nuevo_distrito" class="form-control" required>
                                <option value="">Seleccione...</option>
                            </select>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<h5>RUC/DNI</h5>
							<input type="text" class="form-control input-sm" name="nuevo_ruc_dni_cliente" id="nuevo_ruc_dni_cliente" placeholder="Nro. Documento" required>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<h5>Cliente <i style="font-size: 12px;">( Nombre de la empresa )</i></h5>
							<input type="text" class="form-control input-sm" name="nuevo_cliente" id="nuevo_cliente"
								placeholder="Razón Social/Nombre" onkeyup="javascript:this.value = this.value.toUpperCase();" required>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-sm btn-success" > Guardar </button>

				</div>
			</form>
		</div>
	</div>
</div>
{{-- Fases --}}
<div class="modal fade" tabindex="-1" role="dialog" id="modal-agregar-fase">
	<div class="modal-dialog" style="width: 30%;">
		<div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Fases</h3>

            </div>
            <div class="modal-body">
                <form class="formPage" type="register" data-form="guardar-fase">
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
                                <input type="date" class="form-control" name="fecha_fase" value="{{date('Y-m-d')}}" required>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-block btn-sm btn-success"><span class="fa fa-save"></span> Grabar Fase</button>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Fases</th>
                                    <th>Fecha</th>
                                    <th>Accion</th>
                                </tr>
                            </thead>
                            <tbody data-table="table-fase">

                            </tbody>
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
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-4">
								<div class="form-check">
									<input type="checkbox" class="form-check-input select-check" id="checkEmpresa" data-check="empresa">
									<label class="text-muted" for="checkEmpresa">Empresa</label>
								</div>
							</div>
							<div class="col-md-8">
                                <select class="form-control" name="empresa" data-select="select" data-check="empresa" disabled>
                                    <option value="">Elija una opción</option>
                                    @foreach ($empresa as $item)
                                        <option value="{{$item->id_contribuyente }}">{{$item->razon_social }}</option>
                                    @endforeach
                                </select>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-4">
								<div class="form-check">
									<input type="checkbox" class="form-check-input select-check" id="checkEstado" data-check="estado">
									<label class="text-muted" for="checkEstado">Estado</label>
								</div>
							</div>
							<div class="col-md-8">
								<select class="form-control input-sm" name="fil_estado" id="fil_estado" data-select="select" data-check="estado" disabled>
                                    <option value="">Elija una opción</option>
									<option value="1">EN TRAMITE</option>
									<option value="2">PENDIENTE</option>
									<option value="3">EN VERIFICACION</option>
									<option value="4">SIN PRESUPUESTO</option>
									<option value="5">PAGADO</option>
									<option value="6">ANULADO</option>
								</select>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-4">
								<div class="form-check">
									<input type="checkbox" class="form-check-input select-check" id="checkFase" data-check="fase">
									<label class="text-muted" for="checkFase">Fases</label>
								</div>
							</div>
							<div class="col-md-8">
								<select class="form-control input-sm" name="fil_fase" id="fil_fase" data-select="select" data-check="fase" disabled>
                                    <option value="">Elija una opción</option>
									<option value="COMPROMISO">COMPROMISO</option>
									<option value="DEVENGADO">DEVENGADO</option>
									<option value="PAGADO">PAGADO</option>
								</select>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-4">
								<div class="form-check">
									<input type="checkbox" class="form-check-input select-check" id="checkEmi" data-check="emision">
									<label class="text-muted" for="checkEmi">Fecha Emisión</label>
								</div>
							</div>
							<div class="col-md-4">
								<input type="date" class="form-control input-sm" name="fil_emision_ini" id="fil_emision_ini"  data-select="select" data-check="emision" disabled>
							</div>
							<div class="col-md-4">
								<input type="date" class="form-control input-sm" name="fil_emision_fin" id="fil_emision_fin" data-select="select" data-check="emision" disabled>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-4">
								<div class="form-check">
									<input type="checkbox" class="form-check-input select-check" id="checkImporte" data-check="importe">
									<label class="text-muted" for="checkImporte">Importe</label>
								</div>
							</div>
							<div class="col-md-2">
								<select class="form-control input-sm" name="fil_simbol" id="fil_simbol" data-select="select" data-check="importe" disabled>
									<option value="1"><</option>
									<option value="2">></option>
								</select>
							</div>
							<div class="col-md-6">
								<input type="number" class="form-control input-sm" name="fil_importe" id="fil_importe" step="any" value="0" data-select="select" data-check="importe" disabled>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">

			</div>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-penalidad-cobro">
	<div class="modal-dialog" style="width: 700px;">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title"> </h3>
			</div>
			<div class="modal-body">
                <form id="form-penalidad" data-form="guardar-penalidad">
                    <input type="hidden" name="id" value="0">
                    <input type="hidden" name="tipo_penal" value="">
                    <input type="hidden" name="id_cobranza_penal">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Fecha :</label>
                                <input type="date" class="form-control input-sm text-center" name="fecha_penal" id="fecha_penal" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>N° Comp.</label>
                                <input type="text" class="form-control input-sm" name="doc_penal" id="doc_penal" required>
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
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-block btn-sm btn-success" id="btnPenalidad">Grabar <span class="fa fa-save"></span></button>
                        </div>
                    </div>
                </form>
				<div class="row">
					<div class="col-md-12">
						<fieldset>
                            <legend><h4>1° Historial de Penalidades</h4></legend>
							<table class="table table-bordered table-hover table-aux text-center" id="tablaPenalidad">
								<thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Comprobante</th>
                                        <th>Importe</th>
                                        <th>Estado</th>
                                        <th data-estado="cambio">Estado de Penalidad</th>
                                        <th>Fecha</th>
                                        <th>-</th>
                                    </tr>
                                </thead>
								<tbody data-table="penalidades"></tbody>
							</table>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
{{-- modal observaciones  --}}
<div class="modal fade" tabindex="-1" role="dialog" id="modal-observaciones">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">OBSERVACIONES </h3>
			</div>
			<div class="modal-body">
                <form action="" data-form="guardar-observaciones">
                    <input type="hidden" name="id" value="0">
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
				<div class="row">
					<div class="col-md-12">
						<fieldset>
                            <legend><h4>1° Historial de Observaciones</h4></legend>
							<table class="table table-bordered table-hover table-aux text-center" >
								<thead>
                                    <tr>
                                        <th>Descripción</th>
                                        <th>Usuario</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>-</th>
                                    </tr>
                                </thead>
								<tbody data-table="observaciones"></tbody>
							</table>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script>
// $.widget.bridge('uibutton', $.ui.button);
</script>
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
    <script src="{{ asset('template/plugins/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('template/plugins/bootstrap-select/dist/js/i18n/defaults-es_ES.min.js') }}"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{asset('template/plugins/select2/select2.min.js')}}"></script>
    
    <script>
    let csrf_token = '{{ csrf_token() }}';
    let carga_ini = 1;
    let tempClienteSelected = {};
    let tempoNombreCliente = '';
    let userNickname = '';
    let data_filtros ={
        "empresa":null,
        "estado":null,
        "fase":null,
        "fecha_emision_inicio":null,
        "fecha_emision_fin":null,
        "simbolo":null,
        "importe":null
    };
    let empresa_filtro = null,
        estado_filttro = null,
        fase_filtro = null,
        fecha_emision_inicio_filtro = null,
        fecha_emision_fin_filtro = null,
        importe_simbolo_filtro = null,
        importe_total_filtro = null;
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

    $(document).ready(function() {
        $('.main-header nav.navbar.navbar-static-top').find('a.sidebar-toggle').click();
        $('.numero').number(true, 2);
        $('.select2').select2();
        $('.search-vendedor-guardar').select2({
            dropdownParent: $('#modal-cobranza'),
            placeholder: 'Selecciona un vendedor',
            ajax: {
                url: 'buscar-vendedor',
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        searchTerm: params.term, // search term
                        page: params.page
                    };
            },
            processResults: function (data, params) {
                return {
                    results: $.map(data, function (item) {
                        return{
                            text:item.nombre,
                            id:item.id_vendedor
                        }
                     })

                };
            },
            cache: true,
            },
            minimumInputLength: 2,
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });
        $('.search-vendedor').select2({
            dropdownParent: $('#modal-editar-cobranza'),
            placeholder: 'Selecciona un vendedor',
            ajax: {
                url: 'buscar-vendedor',
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        searchTerm: params.term, // search term
                        page: params.page
                    };
                    // return query;
            },
            processResults: function (data, params) {
                // params.page = params.page || 1;
                return {
                    // results: data.items,
                    // pagination: {
                    //     more: (params.page * 30) < data.total_count
                    // }
                    results: $.map(data, function (item) {
                        return{
                            text:item.nombre,
                            id:item.id_vendedor
                        }
                     })

                };
            },
            cache: true,
            },
            minimumInputLength: 2,
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });
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
    <script src="{{ asset('js/gerencial/cobranza/registro.js') }}"></script>
@endsection
