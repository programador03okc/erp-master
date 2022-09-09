@extends('layout.main')
@include('layout.menu_gerencial')

@section('cabecera')
Cobranzas
@endsection

@section('estilos')
<style>
    .group-okc-ini {
        display: flex;
        justify-content: start;
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
								<th width="10">OCAM</th>
								<th width="150">Nombre del Cliente</th>
								<th>Fact.</th>
								<th>UU. EE.</th>
								<th>FTE. FTO.</th>
								<th>OC.</th>
								<th>SIAF</th>
								<th>Fecha Emis.</th>
								<th>Fecha Recep.</th>
								<th>Días A.</th>
								<th>Mon</th>
								<th>Importe</th>
								<th id="tdEst">Estado</th>
								<th id="tdResp">A. Respo.</th>
								<th width="10">Fase</th>
								<th class="hidden">Tipo</th>
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

<div class="modal fade" tabindex="-1" role="dialog" id="modal-cobranza">
	<div class="modal-dialog" style="width: 70%;">
		<div class="modal-content">
			<form class="formPage" id="formulario" form="cobranza" type="register">
				<div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Registro de Cobranza</h3>
				</div>
				<div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="empresa">Empresa</label>
                                <select class="form-control input-sm" name="empresa" id="empresa" required>
                                    <option value="" disabled selected>Elija una opción</option>
                                    @foreach ($empresa as $item)
                                        <option value="{{$item->id_empresa }}">{{$item->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="sector">Sector</label>
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
                                <label for="tramite">Trámite</label>
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
                                <label for="periodo">Periodo</label>
                                <select name="periodo" id="periodo" class="form-control input-sm" onchange="cambiarPeriodos(this.value);">
                                    @foreach ($periodo as $item)
                                        <option value="{{$item->id_periodo }}">{{$item->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="cliente">Cliente</label>
                                <input type="hidden" name="id_cliente" id="id_cliente" value="0">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control input-sm" name="cliente" id="cliente" placeholder="N° RUC" readonly>
                                    <span class="input-group-btn">
                                        <button class="btn btn-default btn-flat" type="button" id="search_customer" onclick="ModalSearchCustomer();">
                                            <span class="fa fa-search"></span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="oc">Orden de Compra</label>
                                <input type="text" class="form-control input-sm text-center" name="oc" id="oc" required placeholder="N° OC">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="cdp">Cuadro de Presup.</label>
                                <input type="text" class="form-control input-sm text-center" name="cdp" id="cdp" placeholder="N° CDP">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="ocam">OCAM</label>
                                <input type="text" class="form-control input-sm text-center" name="ocam" id="ocam" placeholder="N° OCAM">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="fact">Factura</label>
                                <input type="text" class="form-control input-sm text-center" name="fact" id="fact" required placeholder="N° Fact">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="siaf">N° SIAF</label>
                                <input type="text" class="form-control input-sm text-center" name="siaf" id="siaf" placeholder="SIAF">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="ue">Unidad Ejec.</label>
                                <input type="text" class="form-control input-sm text-center" name="ue" id="ue" placeholder="UU.EE">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="ff">FTE FTO.</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control input-sm text-center" name="ff" id="ff" readonly>
                                    <span class="input-group-btn">
                                        <button class="btn btn-info btn-flat" type="button" onclick="searchSource();">
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
                                    <select class="form-control input-sm" name="moneda" id="moneda" required>
                                        <option value="1" selected>S/.</option>
                                        <option value="2">$</option>
                                    </select>
                                    <input type="text" class="form-control input-sm number text-right" name="importe" id="importe" required placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="categ">Categoría</label>
                                <input type="text" class="form-control input-sm text-center" name="categ" id="categ" placeholder="Categoría">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="fecha_emi">Fecha Emisión</label>
                                <input type="date" class="form-control input-sm text-center" name="fecha_emi" id="fecha_emi"
                                required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="fecha_rec">Fecha Recepción</label>
                                <input type="date" class="form-control input-sm text-center" name="fecha_rec" id="fecha_rec" onchange="calcularAtraso(this.value);" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="estado_doc">Estado Documento</label>
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
                                <label for="fecha_act">Fecha Actual</label>
                                <input type="date" class="form-control input-sm text-center" name="fecha_act" id="fecha_act" value="{{date('Y-m-d')}}" disabled>
                            </div>
                        </div>
                    </div>
					<div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="fecha_ppago">Fecha Pago (próx)</label>
                                <input type="date" class="form-control input-sm text-center" name="fecha_ppago" id="fecha_ppago" value="{{date('Y-m-d')}}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="atraso">Días Atras</label>
                                <input type="text" class="form-control input-sm text-center" name="atraso" id="atraso" value="0" disabled>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="plazo_credito">Plazo Crédito</label>
                                <input type="text" class="form-control input-sm text-center" name="plazo_credito" id="plazo_credito" value="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="nom_vendedor">Nombre del Vendedor</label>
                                <input type="text" class="form-control input-sm" name="nom_vendedor" id="nom_vendedor" placeholder="Nombre y Apellido">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="area">Area</label>
                                <select class="form-control input-sm" name="area" id="area" required>
                                    <option value="1" selected>Almacén</option>
                                    <option value="2">Contabilidad</option>
                                    <option value="3">Logística</option>
                                    <option value="4">Tesorería</option>
                                </select>
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
<div class="modal fade" tabindex="-1" role="dialog" id="modal-buscar-cliente">
	<div class="modal-dialog" style="width: 50%;">
		<div class="modal-content">
			<form class="formPage" type="search">
				<div class="modal-header">
					<h3 class="modal-title">Catálogo de Clientes</h3>
					<button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
                                <button class="btn btn-success btn-flat" title="Agregar Nuevo" type="button" id="add_new_customer"
								onclick="ModalAddNewCustomer();">
                                    <span class="fa fa-plus"></span> Nuevo
                                </button>
                                <button class="btn btn-warning btn-flat" title="Editar" type="button" id="edit_customer"
                                    onclick="ModalEditCustomer();" disabled>
                                    <span class="fa fa-edit"></span> Editar
                                </button>
                            </div>
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
					<button type="button" class="btn btn-sm btn-success" id="btnAgregarCliente" onclick="agregarCliente('cobranza');" disabled> Aceptar </button>

				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal-agregar-cliente">
	<div class="modal-dialog" style="width: 30%;">
		<div class="modal-content">
			<form class="formPage" type="register" data-form="guardar-cliente">
				<div class="modal-header">
					<h3 class="modal-title">Agregar Nuevo Cliente</h3>
					<button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<h5>RUC/DNI</h5>
							<input type="text" class="form-control input-sm" name="nuevo_ruc_dni_cliente" id="nuevo_ruc_dni_cliente" placeholder="Nro. Documento">
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<h5>Cliente <i style="font-size: 12px;">( Nombre de la empresa )</i></h5>
							<input type="text" class="form-control input-sm" name="nuevo_cliente" id="nuevo_cliente"
								placeholder="Razón Social/Nombre" onkeyup="javascript:this.value = this.value.toUpperCase();">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-sm btn-success" onclick="SaveNewCustomer();"> Guardar </button>

				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal-editar-cliente">
	<div class="modal-dialog" style="width: 30%;">
		<div class="modal-content">
			<form class="formPage" type="register">
				<div class="modal-header">
					<h3 class="modal-title">Editar Cliente</h3>
					<button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
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
					<button type="button" class="btn btn-sm btn-success" onclick="updateCustomer();"> Actualizar </button>

				</div>
			</form>
		</div>
	</div>
</div>
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
    <script src="{{ asset('template/plugins/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('template/plugins/bootstrap-select/dist/js/i18n/defaults-es_ES.min.js') }}"></script>


    <script src="{{ asset('js/gerencial/cobranza/registro.js') }}"></script>
@endsection