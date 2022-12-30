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
    <form action="{{route('gerencial.cobranza.clientes.actulizar')}}" data-form="guardar-cliente" type="POST" enctype="multipart/formdata">
        <input type="hidden" name="id_contribuyente" value="{{$contribuyente->id_contribuyente}}">
        <input type="hidden" name="id_cliente" value="{{$cliente?$cliente->id_cliente:''}}">
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Editar cliente</h3>

                {{-- <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                    <i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
                    <i class="fa fa-times"></i></button>
                </div> --}}
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#tab_1" data-toggle="tab">Datos principales</a></li>
                                <li><a href="#tab_2" data-toggle="tab">Establecimientos</a></li>
                                <li><a href="#tab_3" data-toggle="tab">Contacto</a></li>
                                <li><a href="#tab_4" data-toggle="tab">Cuentas Bancarias</a></li>
                                <li><a href="#tab_5" data-toggle="tab">Observaciones</a></li>


                                <li class="pull-right"><button class="btn btn-success" type="submit"><i class="fa fa-save"></i> Guardar</button></li>
                                <li class="pull-right"><button class="btn btn-danger volver-cliente" type="button"><i class="fa fa-arrow-left"></i> Volver</button></li>
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
                                                        <option value="{{ $items->id_pais }}" {{ (( $contribuyente->id_pais == $items->id_pais) ? 'selected' : ''  ) }}>{{ $items->descripcion }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Departamento :</label>
                                                <select name="departamento"  data-select="departamento-select" class="form-control" required>
                                                    <option value="">Seleccione...</option>
                                                    @if ($departamento)
                                                        @foreach ($departamento as $items)
                                                            <option value="{{ $items->id_dpto }}" {{ (( $departamento_first&&$departamento_first->id_dpto == $items->id_dpto) ? 'selected' : ''  ) }}>{{ $items->descripcion }}</option>
                                                        @endforeach
                                                    @endif

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Provincia :</label>
                                                <select name="provincia" id="" class="form-control" data-select="provincia-select" required>
                                                    <option value="">Seleccione...</option>
                                                    @if ($provincia_get)
                                                        @foreach ($provincia_get as $items)
                                                            <option value="{{ $items->id_prov }}" {{ (($provincia_first&& $provincia_first->id_prov == $items->id_prov) ? 'selected' : ''  ) }}>{{ $items->descripcion }}</option>
                                                        @endforeach
                                                    @endif

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Distrito :</label>
                                                <select name="distrito" id="nuevo_distrito" class="form-control" required>
                                                    <option value="">Seleccione...</option>
                                                    @if ($distrito_get)
                                                        @foreach ($distrito_get as $items)
                                                            <option value="{{ $items->id_dis }}" {{ (($distrito_first&& $distrito_first->id_dis == $items->id_dis ) ? 'selected' : ''  ) }}>{{ $items->descripcion }}</option>
                                                        @endforeach
                                                    @endif

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
                                                        <option value="{{ $items->id_doc_identidad }}" {{ (( $contribuyente->id_doc_identidad == $items->id_doc_identidad) ? 'selected' : ''  ) }}>{{ $items->descripcion }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="tipo_contribuyente">Tipo contribuyente :</label>
                                                <select name="tipo_contribuyente" id="" class="form-control" required>
                                                    <option value="">Seleccione...</option>
                                                    @foreach ($tipo_contribuyente as $items)
                                                        <option value="{{ $items->id_tipo_contribuyente }}" {{ (( $contribuyente->id_tipo_contribuyente == $items->id_tipo_contribuyente) ? 'selected' : ''  ) }} >{{ $items->descripcion }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="documento">RUC/DNI :</label>
                                                <input id="" class="form-control" type="text" name="documento" value="{{$contribuyente->nro_documento}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="razon_social">Razon social :</label>
                                                <input id="" class="form-control" type="text" name="razon_social" value="{{$contribuyente->razon_social}}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="direccion">Dirección :</label>
                                                <input id="" class="form-control" type="text" name="direccion" value="{{$contribuyente->direccion_fiscal}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="telefono">Teléfono :</label>
                                                <input id="" class="form-control" type="number" name="telefono" value="{{$contribuyente->telefono}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="celular">Celular :</label>
                                                <input id="" class="form-control" type="number" name="celular" value="{{$contribuyente->celular}}"  required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="email">Email :</label>
                                                <input id="" class="form-control" type="email" name="email" value="{{$contribuyente->email}}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="tab_2">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button class="btn btn-success agregar-establecimiento" type="button"><i class="fa fa-plus"></i> Agregar establecimiento</button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="mytable table table-hover table-condensed table-bordered table-okc-view text-center" id="lista-establecimiento" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" style="width:10%">Dirección</th>
                                                        <th class="text-center" style="width:8%">Ubigeo</th>
                                                        <th class="text-center" style="width:8%">Horario atención</th>
                                                        <th class="text-center" style="width:8%">Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody data-table="tbody-establecimiento">
                                                    @if ($establecimiento_cliente)
                                                        @foreach ($establecimiento_cliente as $item)
                                                            <tr key="{{ $item->id_establecimiento_cliente }}">
                                                                <td data-select="direccion">
                                                                    <label for="">{{ $item->direccion }}</label>
                                                                    <input type="hidden" multiple name="establecimiento[{{ $item->id_establecimiento_cliente }}][direccion]" value="{{ $item->direccion }}">
                                                                </td>
                                                                <td data-select="ubigeo">
                                                                    <label for="">{{ $item->ubigeo_text }}</label>
                                                                    <input type="hidden" multiple name="establecimiento[{{ $item->id_establecimiento_cliente }}][ubigeo]" value="{{ $item->ubigeo }}">
                                                                </td>
                                                                <td data-select="horario">
                                                                    <label for="">{{ $item->horario }}</label>
                                                                    <input type="hidden" multiple name="establecimiento[{{ $item->id_establecimiento_cliente }}][horario]" value="{{ $item->horario }}">
                                                                </td>
                                                                <td data-select="accion">
                                                                    <button class="btn btn-warning editar-establecimiento" type="button" data-key="{{ $item->id_establecimiento_cliente }}"><i class="fa fa-edit"></i></button>
                                                                    <button class="btn btn-danger anular-establecimiento" type="button" data-key="{{ $item->id_establecimiento_cliente }}"><i class="fas fa-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="tab_3">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button class="btn btn-success agregar-contactos" type="button"><i class="fa fa-plus"></i> Agregar contactos</button>
                                        </div>
                                    </div>
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
                                                        <th class="text-center" style="width:10%">Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody data-table="lista-contactos">
                                                    @if ($contacto)
                                                        @foreach ($contacto as $item)
                                                        <tr key={{ $item->id_datos_contacto }}>
                                                            <td data-select="nombre">
                                                                <input type="hidden" name="contacto[{{ $item->id_datos_contacto }}][nombre]" value="{{$item->nombre}}">   <label>{{$item->nombre}}</label>
                                                            </td>
                                                            <td data-select="cargo">
                                                                <input type="hidden" name="contacto[{{ $item->id_datos_contacto }}][cargo]" value="{{$item->cargo}}"> <label>{{$item->cargo}}</label>
                                                            </td>
                                                            <td data-select="telefono">
                                                                <input type="hidden" name="contacto[{{ $item->id_datos_contacto }}][telefono]" value="{{$item->telefono}}"> <label>{{$item->telefono}}</label>
                                                            </td>
                                                            <td data-select="email">
                                                                <input type="hidden" name="contacto[{{ $item->id_datos_contacto }}][email]" value="{{$item->email}}"> <label>{{$item->email}}</label>
                                                            </td>
                                                            <td data-select="direccion">
                                                                <input type="hidden" name="contacto[{{ $item->id_datos_contacto }}][direccion]" value="{{$item->direccion}}"> <label>{{$item->direccion}}</label>
                                                            </td>
                                                            <td data-select="ubigeo">
                                                                <input type="hidden" name="contacto[{{ $item->id_datos_contacto }}][ubigeo]" value="{{$item->ubigeo}}"> <label>{{$item->ubigeo_text}}</label>
                                                            </td>
                                                            <td data-select="horario">
                                                                <input type="hidden" name="contacto[{{ $item->id_datos_contacto }}][horario]" value="{{$item->horario}}"> <label>{{$item->horario}}</label>
                                                            </td>

                                                            <td data-select="action">
                                                                <button class="btn btn-warning editar-contacto" type="button" data-key="{{ $item->id_datos_contacto }}"><i class="fa fa-edit"></i></button> <button class="btn btn-danger anular-contacto" type="button" data-key="{{ $item->id_datos_contacto }}"><i class="fas fa-trash"></i></button>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    @endif


                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="tab_4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button class="btn btn-success agregar-cuenta-bancaria" type="button"><i class="fa fa-plus"></i> Agregar cuenta bancaria</button>
                                        </div>
                                    </div>
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
                                                <tbody data-table="lista-cuenta-bancaria">
                                                    @if ($cuenta_bancaria)
                                                        @foreach ($cuenta_bancaria as $item)
                                                        <tr key={{$item->id_cuenta_contribuyente}}>
                                                            <td data-select="banco">
                                                                <input type="hidden" name="cuenta_bancaria[{{$item->id_cuenta_contribuyente}}][banco]" value="{{$item->id_banco}}">   <label>{{$item->banco_text}}</label>
                                                            </td>
                                                            <td data-select="tipo_cuenta">
                                                                <input type="hidden" name="cuenta_bancaria[{{$item->id_cuenta_contribuyente}}][tipo_cuenta]" value="{{$item->id_tipo_cuenta}}"> <label>{{$item->cuenta_text}}</label>
                                                            </td>
                                                            <td data-select="moneda">
                                                                <input type="hidden" name="cuenta_bancaria[{{$item->id_cuenta_contribuyente}}][moneda]" value="{{$item->id_moneda}}"> <label>{{$item->modena_text}}</label>
                                                            </td>
                                                            <td data-select="numero_cuenta">
                                                                <input type="hidden" name="cuenta_bancaria[{{$item->id_cuenta_contribuyente}}][numero_cuenta]" value="{{$item->nro_cuenta}}"> <label>{{$item->nro_cuenta}}</label>
                                                            </td>
                                                            <td data-select="cuenta_interbancaria">
                                                                <input type="hidden" name="cuenta_bancaria[{{$item->id_cuenta_contribuyente}}][cuenta_interbancaria]" value="{{$item->nro_cuenta_interbancaria}}"> <label>{{$item->nro_cuenta_interbancaria}}</label>
                                                            </td>
                                                            <td data-select="swift">
                                                                <input type="hidden" name="cuenta_bancaria[{{$item->id_cuenta_contribuyente}}][swift]" value="{{$item->swift}}"> <label>{{$item->swift}}</label>
                                                            </td>

                                                            <td data-select="action">
                                                                <button class="btn btn-warning editar-cuenta-bancaria" type="button" data-key="{{$item->id_cuenta_contribuyente}}"><i class="fa fa-edit"></i></button> <button class="btn btn-danger anular-cuenta-bancaria" type="button" data-key="{{$item->id_cuenta_contribuyente}}"><i class="fas fa-trash"></i></button>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    @endif


                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab_5">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="">Observación :</label>
                                                <textarea id="" class="form-control" name="observacion">{{ $cliente? $cliente->observacion:'' }}</textarea>
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
            <!-- /.box-body -->
            {{-- <div class="box-footer">
                Footer
            </div> --}}
            <!-- /.box-footer-->
        </div>

        </div>
        {{-- <button class="btn btn-success" type="submit">Guardar</button> --}}
    </form>

</div>

{{-- establecimiento --}}
<div class="modal fade" tabindex="-1" role="dialog" id="nuevo-establecimiento">
	<div class="modal-dialog" style="width: 500px;">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">Nuevo Establecimiento</h3>
			</div>
            <form action="" data-form="guardar-establecimiento">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Dirección</label>
                                <input type="text" class="form-control" name="direccionEstablecimiento" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Provincia :</label>
                                <select name="provincia" id="" class="form-control" data-select="provincia-select" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
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
                                <label>Horario</label>
                                <input type="text" class="form-control" name="horarioEstablecimiento" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </form>
		</div>
	</div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="editar-establecimiento">
	<div class="modal-dialog" style="width: 500px;">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">Editar Establecimiento</h3>
			</div>
            <form action="" data-form="editar-establecimiento">
                <input type="hidden" name="id_establecimiento" value="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Dirección</label>
                                <input type="text" class="form-control" name="direccionEstablecimiento" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Provincia :</label>
                                <select name="provincia" id="" class="form-control" data-select="provincia-select" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Distrito :</label>
                                <select name="distrito" id="" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Horario</label>
                                <input type="text" class="form-control" name="horarioEstablecimiento" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </form>
		</div>
	</div>
</div>
{{-- contacto --}}
<div class="modal fade" tabindex="-1" role="dialog" id="nuevo-contacto">
	<div class="modal-dialog" style="width: 500px;">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">Nuevo Contacto</h3>
			</div>
            <form action="" data-form="guardar-contacto">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Provincia :</label>
                                <select name="provincia" id="" class="form-control" data-select="provincia-select" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Distrito :</label>
                                <select name="distrito" id="nuevo_distrito" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Nombre :</label>
                                <input type="text" class="form-control" name="nombreContacto" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Cargo :</label>
                                <input type="text" class="form-control" name="cargoContacto" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Telefono :</label>
                                <input type="text" class="form-control handleKeyUpTelefono" name="telefonoContacto" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Dirección :</label>
                                <input type="text" class="form-control" name="direccionContacto" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Horario :</label>
                                <input type="text" class="form-control" name="horarioContacto" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Email :</label>
                                <input type="email" class="form-control" name="emailContacto" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </form>
		</div>
	</div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="editar-contacto">
	<div class="modal-dialog" style="width: 500px;">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">Editar Contacto</h3>
			</div>
            <form action="" data-form="editar-contacto">
                <input type="hidden" name="id_contacto" value="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Provincia :</label>
                                <select name="provincia" id="" class="form-control" data-select="provincia-select" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Distrito :</label>
                                <select name="distrito" id="nuevo_distrito" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Nombre :</label>
                                <input type="text" class="form-control" name="nombreContacto" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Cargo :</label>
                                <input type="text" class="form-control" name="cargoContacto" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Telefono :</label>
                                <input type="text" class="form-control handleKeyUpTelefono" name="telefonoContacto" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Dirección :</label>
                                <input type="text" class="form-control" name="direccionContacto" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Horario :</label>
                                <input type="text" class="form-control" name="horarioContacto" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Email :</label>
                                <input type="email" class="form-control" name="emailContacto" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </form>
		</div>
	</div>
</div>
{{-- cuenta bancaria --}}
<div class="modal fade" tabindex="-1" role="dialog" id="nuevo-cuenta-bancaria">
	<div class="modal-dialog" style="width: 500px;">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">Nuva cuenta bancria</h3>
			</div>
            <form action="" data-form="nuevo-cuenta-bancaria">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Banco</label>
                                <select class="form-control group-elemento" name="idBanco"
                                style="text-align:center;" required>
                                    <option value="0" disabled>Elija una opción</option>
                                    @foreach ($bancos as $banco)
                                    <option value="{{$banco->id_banco}}">{{$banco->razon_social}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Tipo de Cuenta</label>
                                <select class="form-control group-elemento" name="idTipoCuenta"
                                    style="text-align:center;" required>
                                    <option value="0" disabled>Elija una opción</option>
                                    @foreach ($tipo_cuenta as $tipo)
                                        <option value="{{$tipo->id_tipo_cuenta}}">{{$tipo->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Moneda</label>
                                <select class="form-control group-elemento" name="idMoneda"
                                    style="text-align:center;" required>
                                    @foreach ($monedas as $moneda)
                                        <option value="{{$moneda->id_moneda}}">{{$moneda->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>N° Cuenta</label>
                                <input type="text" class="form-control icd-okc" name="nroCuenta" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>N° Cuenta Interbancaria</label>
                                <input type="text" class="form-control icd-okc" name="nroCuentaInterbancaria" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>SWIFT</label>
                                <input type="text" class="form-control icd-okc" name="swift" required />
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </form>
		</div>
	</div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="editar-cuenta-bancaria">
	<div class="modal-dialog" style="width: 500px;">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">Editar cuenta bacnaria</h3>
			</div>
            <form action="" data-form="editar-cuenta-bancaria">
                <div class="modal-body">
                    <input type="hidden" name="id_cuenta_bancaria" value="">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Banco</label>
                                <select class="form-control group-elemento" name="idBanco"
                                style="text-align:center;" required>
                                    <option value="0" disabled>Elija una opción</option>
                                    @foreach ($bancos as $banco)
                                    <option value="{{$banco->id_banco}}">{{$banco->razon_social}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Tipo de Cuenta</label>
                                <select class="form-control group-elemento" name="idTipoCuenta"
                                    style="text-align:center;" required>
                                    <option value="0" disabled>Elija una opción</option>
                                    @foreach ($tipo_cuenta as $tipo)
                                        <option value="{{$tipo->id_tipo_cuenta}}">{{$tipo->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Moneda</label>
                                <select class="form-control group-elemento" name="idMoneda"
                                    style="text-align:center;" required>
                                    @foreach ($monedas as $moneda)
                                        <option value="{{$moneda->id_moneda}}">{{$moneda->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>N° Cuenta</label>
                                <input type="text" class="form-control icd-okc" name="nroCuenta" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>N° Cuenta Interbancaria</label>
                                <input type="text" class="form-control icd-okc" name="nroCuentaInterbancaria" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>SWIFT</label>
                                <input type="text" class="form-control icd-okc" name="swift" required />
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit"><i class="fa fa-save"></i> Guardar</button>
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
{{-- <script src="{{ asset('js/gerencial/cobranza/clientes.js') }}?v=2"></script> --}}

<script src="{{ asset('js/gerencial/cobranza/editar_cliente.js') }}?v=2"></script>
<script>
    const route_cliente = "{{route('gerencial.cobranza.cliente')}}";
</script>
@endsection
