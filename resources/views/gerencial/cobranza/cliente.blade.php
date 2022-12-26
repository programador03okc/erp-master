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
            <div class="box box-solid">
                <div class="box-header">
                    <h3 class="box-title">Lista de clientes</h3>
                    <div class="pull-right box-tools">
                        <button type="button" class="btn btn-success" title="Nuevo Usuario" data-action="nuevo-cliente"><i class="fa fa-save"></i> Nuevo cliente</button>
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
                                        <th id="tdAct" width="50">-</th>
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
	<div class="modal-dialog" style="width: 500px;">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">Nuevo Cliente</h3>
			</div>
            <form action="{{route('gerencial.cobranza.clientes.crear')}}" data-form="guardar-cliente" type="POST" enctype="multipart/formdata">
                <div class="modal-body">
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
<script src="{{ asset('js/gerencial/cobranza/clientes.js') }}?v=2"></script>
<script>


</script>
@endsection
