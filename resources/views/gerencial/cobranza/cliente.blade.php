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
    <div class="row">
        {{-- <div class="col-md-2"></div> --}}
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-danger">
                <div class="box-header">
                    <h3 class="box-title">Lista de clientes</h3>
                    <div class="pull-right box-tools">
                        {{-- <button type="button" class="btn btn-success" title="Nuevo Usuario" data-action="nuevo-cliente"><i class="fa fa-save"></i> Nuevo cliente</button> --}}
                        @if (in_array(315, $array_accesos, true))
                        <a class="btn btn-success" title="Nuevo Usuario" href="{{ route('gerencial.cobranza.nuevo.cliente') }}"><i class="fa fa-save"></i> Nuevo cliente</a>
                        @endif

                        {{-- <button class="btn btn-primary" data-action="actualizar"><i class="fa fa-refresh"></i> Actualizar</button> --}}
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="mytable table table-striped table-condensed table-bordered table-responsive" id="listar-clientes">
                                <thead>
                                    <tr>
                                        <th></th>
                                        {{-- <th width="10">N°</th> --}}
                                        <th >RUC</th>
                                        <th >Nombre del Cliente</th>
                                        <th id="tdAct" width="20%">-</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
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

					</div>
				</div>
			</div>
			<div class="modal-footer">

			</div>
		</div>
	</div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="nuevo-cliente">
	<div class="modal-dialog" style="width: 900px;">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">Nuevo Cliente</h3>
			</div>
            <form action="{{route('gerencial.cobranza.clientes.crear')}}" data-form="guardar-cliente" type="POST" enctype="multipart/formdata">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="nav-tabs-custom">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#tab_1" data-toggle="tab">Datos principales</a></li>
                                    <li><a href="#tab_2" data-toggle="tab">Establecimientos</a></li>
                                    <li><a href="#tab_3" data-toggle="tab">Contacto</a></li>
                                    <li><a href="#tab_4" data-toggle="tab">Cuentas Bancarias</a></li>
                                    <li><a href="#tab_5" data-toggle="tab">Observaciones</a></li>

                                    <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="tab_1">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Pais :</label>
                                                    <select name="pais" id="pais" class="form-control" required>
                                                        <option value="">Seleccione...</option>
                                                        @foreach ($pais as $items)
                                                            <option value="{{ $items->id_pais }}">{{ $items->descripcion }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Departamento :</label>
                                                    <select name="departamento"  data-select="departamento-select" class="form-control" required>
                                                        <option value="">Seleccione...</option>
                                                        @foreach ($departamento as $items)
                                                            <option value="{{ $items->id_dpto }}">{{ $items->descripcion }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Provincia :</label>
                                                    <select name="provincia" id="" class="form-control" data-select="provincia-select" required>
                                                        <option value="">Seleccione...</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Distrito :</label>
                                                    <select name="distrito" id="nuevo_distrito" class="form-control" required>
                                                        <option value="">Seleccione...</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="tipo_documnto">Tipo de documento :</label>
                                                    <select name="tipo_documnto" id="" class="form-control" required>
                                                        <option value="">Seleccione...</option>
                                                        @foreach ($tipo_documentos as $items)
                                                            <option value="{{ $items->id_doc_identidad }}">{{ $items->descripcion }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="tipo_contribuyente">Tipo contribuyente :</label>
                                                    <select name="tipo_contribuyente" id="" class="form-control" required>
                                                        <option value="">Seleccione...</option>
                                                        {{-- @foreach ($tipo_documentos as $items)
                                                            <option value="{{ $items->id_doc_identidad }}">{{ $items->descripcion }}</option>
                                                        @endforeach --}}
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="documento">RUC/DNI :</label>
                                                    <input id="" class="form-control" type="text" name="documento" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="razon_social">Razon social :</label>
                                                    <input id="" class="form-control" type="text" name="razon_social" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="direccion">Dirección :</label>
                                                    <input id="" class="form-control" type="text" name="direccion" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="telefono">Teléfono :</label>
                                                    <input id="" class="form-control" type="number" name="telefono" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="celular">Celular :</label>
                                                    <input id="" class="form-control" type="number" name="celular" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="email">Email :</label>
                                                    <input id="" class="form-control" type="email" name="email" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                  <!-- /.tab-pane -->
                                    <div class="tab-pane" id="tab_2">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button class="btn btn-success agregar-establecimiento" type="button">Agregar establecimiento</button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="lista-establecimiento" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" style="width:10%">Dirección</th>
                                                            <th class="text-center" style="width:8%">Ubigeo</th>
                                                            <th class="text-center" style="width:8%">Horario atención</th>
                                                            <th class="text-center" style="width:8%">Acción</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="bodylistaEstablecimientoProveedorSoloLectura"></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                  <!-- /.tab-pane -->
                                    <div class="tab-pane" id="tab_3">

                                    </div>
                                  <!-- /.tab-pane -->
                                </div>
                                <!-- /.tab-content -->
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Guardar</button>
                </div>
            </form>

		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="editar-cliente">
	<div class="modal-dialog" style="width: 500px;">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">Editar Cliente</h3>
			</div>
            <form action="{{route('gerencial.cobranza.clientes.actulizar')}}" data-form="editar-cliente" type="POST" enctype="multipart/formdata">
                <div class="modal-body">
                    <input type="hidden" name="id_contribuyente" value="">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Pais :</label>
                                <select name="pais" id="pais" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                    @foreach ($pais as $items)
                                        <option value="{{ $items->id_pais }}">{{ $items->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Departamento :</label>
                                <select name="departamento"  data-select="departamento-select" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                    @foreach ($departamento as $items)
                                        <option value="{{ $items->id_dpto }}">{{ $items->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Provincia :</label>
                                <select name="provincia" id="" class="form-control" data-select="provincia-select" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Distrito :</label>
                                <select name="distrito" id="nuevo_distrito" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="tipo_documnto">Tipo de documento :</label>
                                <select name="tipo_documnto" id="" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                    @foreach ($tipo_documentos as $items)
                                        <option value="{{ $items->id_doc_identidad }}">{{ $items->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="documento">RUC/DNI :</label>
                                <input id="" class="form-control" type="text" name="documento" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="razon_social">Razon social :</label>
                                <input id="razon_social" class="form-control" type="text" name="razon_social" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Guardar</button>
                </div>
            </form>

		</div>
	</div>
</div>



<div class="modal fade" tabindex="-1" role="dialog" id="ver-cliente">
	<div class="modal-dialog modal-lg" >
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">Ver Cliente</h3>
			</div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#tab_datos" data-toggle="tab">Datos principales</a></li>
                                <li><a href="#tab_establecimiento" data-toggle="tab">Establecimientos</a></li>
                                <li><a href="#tab_contacto" data-toggle="tab">Contacto</a></li>
                                <li><a href="#tab_bancarias" data-toggle="tab">Cuentas Bancarias</a></li>
                                <li><a href="#tab_observaciones" data-toggle="tab">Observaciones</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab_datos">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Pais :</label><span class="pais"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Departamento :</label><span class="departamento"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Provincia :</label><span class="provincia"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Distrito :</label><span class="distrito"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tipo_documnto">Tipo de documento :</label><span class="tipo_documento"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tipo_contribuyente">Tipo contribuyente :</label><span class="tipo_contribuyente"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="documento">RUC/DNI :</label><span class="documento"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="razon_social">Razon social :</label><span class="razon_social"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="direccion">Dirección :</label><span class="direccion"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="telefono">Teléfono :</label><span class="telefono"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="celular">Celular :</label><span class="celular"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email :</label><span class="email"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="tab_establecimiento">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="mytable table table-hover table-condensed table-bordered table-okc-view text-center" id="lista-establecimiento" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" style="width:10%">Dirección</th>
                                                        <th class="text-center" style="width:8%">Ubigeo</th>
                                                        <th class="text-center" style="width:8%">Horario atención</th>
                                                    </tr>
                                                </thead>
                                                <tbody data-table="tbody-establecimiento">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="tab_contacto">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="mytable table table-hover table-condensed table-bordered table-okc-view text-center" id="lista-contactos" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" style="width:10%">Nombre</th>
                                                        <th class="text-center" style="width:10%">Cargo</th>
                                                        <th class="text-center" style="width:10%">Telefono</th>
                                                        <th class="text-center" style="width:10%">Email</th>
                                                        <th class="text-center" style="width:10%">Dirección</th>
                                                        <th class="text-center" style="width:10%">Ubigeo</th>
                                                        <th class="text-center" style="width:10%">Horario atención</th>
                                                    </tr>
                                                </thead>
                                                <tbody data-table="lista-contactos"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="tab_bancarias">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="mytable table table-hover table-condensed table-bordered table-okc-view" id="lista-cuenta-bancaria" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" style="width:20%">Banco</th>
                                                        <th class="text-center" style="width:5%">Tipo cuenta</th>
                                                        <th class="text-center" style="width:8%">Moneda</th>
                                                        <th class="text-center" style="width:10%">Nro cuenta</th>
                                                        <th class="text-center" style="width:10%">Nro cuenta interbancaria</th>
                                                        <th class="text-center" style="width:10%">Swift</th>
                                                        <th class="text-center" style="width:10%">Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody data-table="lista-cuenta-bancaria"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab_observaciones">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="">Observación :</label>
                                                <textarea id="" class="form-control" name="observacion"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.tab-content -->
                        </div>
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
<script>
    var array_accesos = JSON.parse('{!!json_encode($array_accesos)!!}');
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{asset('template/plugins/select2/select2.min.js')}}"></script>
<script src="{{ asset('js/gerencial/cobranza/clientes.js') }}?v=2"></script>
<script>


</script>
@endsection
