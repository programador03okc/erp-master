@extends('layout.main')
@include('layout.menu_finanzas')

@section('cabecera')
Presupuesto Interno
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/jquery-datatables-checkboxes/css/dataTables.checkboxes.css') }}">
<style>
    .lbl-codigo:hover{
        color:#007bff !important;
        cursor:pointer;
    }
    .d-none{
        display: none;
    }
</style>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><i class="fa fa-usd"></i> Finanzas </li>
        <li class="active"> @yield('cabecera')</li>
    </ol>
@endsection

@section('content')
<form action="{{ route('finanzas.presupuesto.presupuesto-interno.actualizar') }}" method="post" data-form="editar-partida" enctype="multipart/formdata">
    <input type="hidden" name="id_presupuesto_interno" value="{{ $id }}">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">EDITAR PRESUPUESTO INTERNO</h3>
                    <div class="box-tools pull-right">
                        {{-- <div class="btn-group" role="group"> --}}
                            <button type="button" title="Volver a la lista de presupuesto interno"
                                class="btn btn-sm btn-danger">
                                <i class="fa fa-arrow-left"></i>
                                Volver
                            </button>
                            <button title="Guardar" type="submit"
                                class="btn btn-sm btn-success">
                                <i class="fa fa-save"></i>
                                Guardar
                            </button>
                            <button title="" type="button"
                                class="btn btn-sm btn-success" data-action="generar" data-tipo="1">
                                <i class="fa fa-retweet"></i>
                                Ingresos
                            </button>
                            <button title="" type="button"
                                class="btn btn-sm btn-success" data-action="generar" data-tipo="3">
                                <i class="fa fa-retweet"></i>
                                Gasto
                            </button>
                            <!-- <a target="_blank" href="#" title="Imprimir" class="btn">
                                <i class="glyphicon glyphicon-search" aria-hidden="true"></i>
                            </a> -->
                        {{-- </div> --}}
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="hidden" name="id_tipo_presupuesto"value="{{$presupuesto_interno->id_tipo_presupuesto}}">
                                <label for="id_grupo">Grupo :</label>
                                <select class="form-control" name="id_grupo" id="id_grupo" required>
                                    <option value="">Seleccione...</option>
                                    @foreach ($grupos as $item)
                                        <option value="{{ $item->id_grupo }}"
                                            {{($item->id_grupo===$presupuesto_interno->id_grupo?'selected':'')}}
                                            >
                                            {{ $item->descripcion }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="id_area">Area :</label>
                                <select class="form-control" name="id_area" id="id_area" required>
                                    <option value="">Seleccione...</option>
                                    @foreach ($area as $item)
                                        <option value="{{ $item->id_area }}" {{($item->id_area===$presupuesto_interno->id_area?'selected':'')}}>{{ $item->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="id_moneda">Moneda :</label>
                                <select class="form-control" name="id_moneda" id="id_moneda" required>
                                    <option value="">Seleccione...</option>
                                    @foreach ($moneda as $item)
                                    <option value="{{ $item->id_moneda }}" {{($item->id_moneda===$presupuesto_interno->id_moneda?'selected':'')}}>{{ $item->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="mes">Meses :</label>
                                <select id="mes" name="mes" class="form-control" required>
                                    <option selected hidden>Mes</option>
                                    <option value="Enero">Enero</option>
                                    <option value="Febrero">Febrero</option>
                                    <option value="Marzo">Marzo</option>
                                    <option value="Abril">Abril</option>
                                    <option value="Mayo">Mayo</option>
                                    <option value="Junio">Junio</option>
                                    <option value="Julio">Julio</option>
                                    <option value="Agosto">Agosto</option>
                                    <option value="Septiembre">Septiembre</option>
                                    <option value="Octubre">Octubre</option>
                                    <option value="Noviembre">Noviembre</option>
                                    <option value="Diciembre">Diciembre</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="descripcion">Descripcion : </label>
                                <textarea id="descripcion" class="form-control" name="descripcion" rows="3" >{{$presupuesto_interno->descripcion}}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 animate__animated {{($presupuesto_interno->id_tipo_presupuesto===1?'':'d-none')}}">
            <div class="box box-success">
                <div class="box-body" data-presupuesto="interno-modelo">
                    <div class="row" data-select="presupuesto-1">
                        @if ($presupuesto_interno->id_tipo_presupuesto==1)
                        <div class="col-md-12">
                            <label>INGRESOS</label>
                            <div class="pull-right">
                                <a class="btn btn-box-tool" data-toggle="collapse" data-parent="#accordion" href="#collapse_ingresos">
                                <i class="fa fa-minus"></i></a>

                                <button type="button" class="btn btn-box-tool d-none" ><i class="fa fa-plus" title="Agregar presupuesto de costos" data-tipo="2" data-action="generar"></i></button>

                            </div>
                        </div>
                        <div class="col-md-12 panel-collapse collapse in" id="collapse_ingresos">
        <table class="table table-hover" id="partida-ingresos">
            <thead>
                <tr>
                    <th class="text-left" width="20%">PARTIDA</th>
                    <th class="text-left" width=""colspan="2">DESCRIPCION</th>
                    <th class="text-center"></th>
                </tr>
            </thead>
            <tbody data-table-presupuesto="ingreso">
                @foreach ($ingresos as $item)
                    @php
                        $array = explode(".", $item->partida);
                        $id=rand();
                        $id_padre=rand();
                        $input_key=rand();
                    @endphp
                <tr key="{{$input_key}}" data-nivel="{{sizeof($array)}}" data-partida="{{$item->partida}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" >
                    <td data-td="partida">
                        <input type="hidden" value="{{$item->partida}}" name="ingresos[{{$input_key}}][partida]" class="form-control input-sm">

                        <input type="hidden" value="{{$item->id_presupuesto_interno_detalle}}" name="ingresos[{{$input_key}}][id_hijo]" class="form-control input-sm">
                        <input type="hidden" value="{{$item->id_padre}}" name="ingresos[{{$input_key}}][id_padre]" class="form-control input-sm">
                        <span>{{$item->partida}}</span></td>

                        {{-- @if (sizeof($array)===3 || sizeof($array)===4) --}}
                            <td data-td="descripcion">
                                <input type="hidden" value="{{$item->descripcion}}" class="form-control input-sm" name="ingresos[{{$input_key}}][descripcion]" placeholder="{{$item->descripcion}}"><span>{{$item->descripcion}}</span>
                            </td>

                            <td data-td="monto">
                                <input type="hidden" value="{{$item->monto}}" class="form-control input-sm" name="ingresos[{{$input_key}}][monto]" placeholder="Ingrese monto" step="0.01">
                                <span> {{$item->monto}} </span>
                            </td>

                        {{-- @else
                            <td colspan="2" data-td="descripcion"><input type="hidden" value="{{$item->descripcion}}" class="form-control input-sm" name="ingresos[{{$input_key}}][descripcion]"><span>{{$item->descripcion}}</span></td>
                        @endif --}}

                    <td data-td="accion">

                        @if (sizeof($array)!=4)
                            <button type="button" class="btn btn-xs" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-nuevo" data-select="titulo" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="ingresos" title="Agregar titulo" data-tipo="nuevo"><i class="fa fa-level-down-alt"></i></button>

                            <button type="button" class="btn btn-xs" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-partida" data-select="partida" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="ingresos" title="Agregar partida" data-tipo="nuevo"><i class="fa fa-plus"></i></button>
                            <button type="button" class="btn btn-xs" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-nuevo" data-select="titulo" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="ingresos" title="Editar" data-tipo="editar"><i class="fa fa-edit"></i></button>
                        @endif

                        @if (sizeof($array)==4)
                            <button type="button" class="btn btn-xs" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-partida" data-select="partida" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="ingresos" title="Editar partida" data-tipo="editar"><i class="fa fa-edit"></i></button>
                        @endif
                        @if (sizeof($array)!==1)
                        <button type="button" class="btn btn-xs" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-eliminar" data-nivel="{{sizeof($array)}}" data-tipo-text="ingresos" title="Eliminar" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}"><i class="fa fa-trash"></i></button>
                        @endif

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 animate__animated {{($presupuesto_interno->id_tipo_presupuesto===1?'':'d-none')}}">
            <div class="box box-success">
                <div class="box-body" data-presupuesto="interno-modelo">
                    <div class="row" data-select="presupuesto-2">
                        @if ($presupuesto_interno->id_tipo_presupuesto===1)
                            <div class="col-md-12">
                                <label>COSTOS</label>
                                <div class="pull-right">
                                    <a class="btn btn-box-tool" data-toggle="collapse" data-parent="#accordion" href="#collapse_costos">
                                    <i class="fa fa-minus"></i></a>

                                    <button type="button" class="btn btn-box-tool d-none" ><i class="fa fa-plus" title="Agregar presupuesto de costos" data-tipo="3" data-action="generar"></i></button>

                                </div>
                            </div>
                            <div class="col-md-12 panel-collapse collapse in" id="collapse_costos">
                                <table class="table table-hover" id="partida-costos">
                                    <thead>
                                        <tr>
                                            <th class="text-left" width="20%">PARTIDA</th>
                                            <th class="text-left" width=""colspan="2">DESCRIPCION</th>
                                            <th class="text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody data-table-presupuesto="ingreso">
                                        @foreach ($costos as $item)
                                        @php
                                            $array = explode(".", $item->partida);
                                            $id=rand();
                                            $id_padre=rand();
                                            $input_key=rand();
                                        @endphp
                                    <tr key="{{$input_key}}" data-nivel="{{sizeof($array)}}" data-partida="{{$item->partida}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" >
                                        <td data-td="partida">
                                            <input type="hidden" value="{{$item->partida}}" name="costos[{{$input_key}}][partida]" class="form-control input-sm">

                                            <input type="hidden" value="{{$item->id_presupuesto_interno_detalle}}" name="costos[{{$input_key}}][id_hijo]" class="form-control input-sm">
                                            <input type="hidden" value="{{$item->id_padre}}" name="costos[{{$input_key}}][id_padre]" class="form-control input-sm">
                                            <span>{{$item->partida}}</span></td>

                                            {{-- @if (sizeof($array)===3 || sizeof($array)===4) --}}
                                                <td data-td="descripcion">
                                                    <input type="hidden" value="{{$item->descripcion}}" class="form-control input-sm" name="costos[{{$input_key}}][descripcion]" placeholder="{{$item->descripcion}}"><span>{{$item->descripcion}}</span>
                                                </td>

                                                <td data-td="monto">
                                                    <input type="hidden" value="{{$item->monto}}" class="form-control input-sm" name="costos[{{$input_key}}][monto]" placeholder="Ingrese monto" step="0.01">
                                                    <span> {{$item->monto}} </span>
                                                </td>

                                            {{-- @else
                                                <td colspan="2" data-td="descripcion"><input type="hidden" value="{{$item->descripcion}}" class="form-control input-sm" name="costos[{{$input_key}}][descripcion]"><span>{{$item->descripcion}}</span></td>
                                            @endif --}}

                                        <td data-td="accion">

                                            @if (sizeof($array)!=4)
                                                <button type="button" class="btn btn-xs" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-nuevo" data-select="titulo" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="costos" title="Agregar titulo" data-tipo="nuevo"><i class="fa fa-level-down-alt"></i></button>

                                                <button type="button" class="btn btn-xs" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-partida" data-select="partida" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="costos" title="Agregar partida" data-tipo="nuevo"><i class="fa fa-plus"></i></button>
                                                <button type="button" class="btn btn-xs" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-nuevo" data-select="titulo" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="costos" title="Editar" data-tipo="editar"><i class="fa fa-edit"></i></button>
                                            @endif

                                            @if (sizeof($array)==4)
                                                <button type="button" class="btn btn-xs" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-partida" data-select="partida" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="costos" title="Editar partida" data-tipo="editar"><i class="fa fa-edit"></i></button>
                                            @endif
                                            @if (sizeof($array)!==1)
                                            <button type="button" class="btn btn-xs" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-eliminar" data-nivel="{{sizeof($array)}}" data-tipo-text="costos" title="Eliminar" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}"><i class="fa fa-trash"></i></button>
                                            @endif

                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-md-offset-3 animate__animated {{($presupuesto_interno->id_tipo_presupuesto===3?'':'d-none')}}">
            <div class="box box-success">
                <div class="box-body" data-presupuesto="interno-modelo">
                    <div class="row" data-select="presupuesto-3">
                        @if ($presupuesto_interno->id_tipo_presupuesto===3)
                        <div class="col-md-12">
                            <label>GASTOS</label>
                            <div class="pull-right">
                                <a class="btn btn-box-tool" data-toggle="collapse" data-parent="#accordion" href="#collapse_gastos">
                                <i class="fa fa-minus"></i></a>

                                <button type="button" class="btn btn-box-tool d-none" ><i class="fa fa-plus" title="Agregar presupuesto de costos" data-tipo="1" data-action="generar"></i></button>

                            </div>
                        </div>
                        <div class="col-md-12 panel-collapse collapse in" id="collapse_gastos">
                            <table class="table table-hover" id="partida-gastos">
                                <thead>
                                    <tr>
                                        <th class="text-left" width="20%">PARTIDA</th>
                                        <th class="text-left" width=""colspan="2">DESCRIPCION</th>
                                        <th class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody data-table-presupuesto="ingreso">
                                    @foreach ($gastos as $item)
                                    @php
                                        $array = explode(".", $item->partida);
                                        $id=rand();
                                        $id_padre=rand();
                                        $input_key=rand();
                                    @endphp
                                <tr key="{{$input_key}}" data-nivel="{{sizeof($array)}}" data-partida="{{$item->partida}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" >
                                    <td data-td="partida">
                                        <input type="hidden" value="{{$item->partida}}" name="gastos[{{$input_key}}][partida]" class="form-control input-sm">

                                        <input type="hidden" value="{{$item->id_presupuesto_interno_detalle}}" name="gastos[{{$input_key}}][id_hijo]" class="form-control input-sm">
                                        <input type="hidden" value="{{$item->id_padre}}" name="gastos[{{$input_key}}][id_padre]" class="form-control input-sm">
                                        <span>{{$item->partida}}</span></td>

                                        {{-- @if (sizeof($array)===3 || sizeof($array)===4) --}}
                                            <td data-td="descripcion">
                                                <input type="hidden" value="{{$item->descripcion}}" class="form-control input-sm" name="gastos[{{$input_key}}][descripcion]" placeholder="{{$item->descripcion}}"><span>{{$item->descripcion}}</span>
                                            </td>

                                            <td data-td="monto">
                                                <input type="hidden" value="{{$item->monto}}" class="form-control input-sm" name="gastos[{{$input_key}}][monto]" placeholder="Ingrese monto" step="0.01">
                                                <span> {{$item->monto}} </span>
                                            </td>

                                        {{-- @else
                                            <td colspan="2" data-td="descripcion"><input type="hidden" value="{{$item->descripcion}}" class="form-control input-sm" name="gastos[{{$input_key}}][descripcion]"><span>{{$item->descripcion}}</span></td>
                                        @endif --}}

                                    <td data-td="accion">

                                        @if (sizeof($array)!=4)
                                            <button type="button" class="btn btn-xs" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-nuevo" data-select="titulo" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="gastos" title="Agregar titulo" data-tipo="nuevo"><i class="fa fa-level-down-alt"></i></button>

                                            <button type="button" class="btn btn-xs" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-partida" data-select="partida" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="gastos" title="Agregar partida" data-tipo="nuevo"><i class="fa fa-plus"></i></button>
                                            <button type="button" class="btn btn-xs" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-nuevo" data-select="titulo" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="gastos" title="Editar" data-tipo="editar"><i class="fa fa-edit"></i></button>
                                        @endif

                                        @if (sizeof($array)==4)
                                            <button type="button" class="btn btn-xs" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-partida" data-select="partida" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="gastos" title="Editar partida" data-tipo="editar"><i class="fa fa-edit"></i></button>
                                        @endif
                                        @if (sizeof($array)!==1)
                                        <button type="button" class="btn btn-xs" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-eliminar" data-nivel="{{sizeof($array)}}" data-tipo-text="gastos" title="Eliminar" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}"><i class="fa fa-trash"></i></button>
                                        @endif

                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<div id="modal-titulo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form action="" method="post" data-form="guardar-formulario">
                <div class="modal-header">
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="my-modal-title">Titulo</h5>

                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="id_descripcion_titulo">Descripcion</label>
                        <input id="id_descripcion_titulo" class="form-control" type="text" name="descripcion" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-dismiss="modal" type="button"><i class="fa fa-times"></i> Cerra</button>
                    <button class="btn btn-success" type="submit"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="modal-partida" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form action="" method="post" data-form="guardar-partida-modal">
                <div class="modal-header">
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="my-modal-title">Partida</h5>

                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="id_descripcion_partida">Descripcion :</label>
                        <input id="id_descripcion_partida" class="form-control" type="text" name="descripcion" required>
                    </div>
                    <div class="form-group">
                        <label for="id_monto_partida">Monto :</label>
                        <input id="id_monto_partida" class="form-control" type="number" name="monto" step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-dismiss="modal" type="button"><i class="fa fa-times"></i> Cerra</button>
                    <button class="btn btn-success" type="submit"><i class="fa fa-save"></i> Guardar</button>
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
    <script src="{{ asset('template/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('template/plugins/jquery-datatables-checkboxes/js/dataTables.checkboxes.min.js') }}"></script>
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>
    <script>
        // let csrf_token = "{{ csrf_token() }}";
        $(document).ready(function () {
            $('select[name="mes"] option[value="'+"{{$presupuesto_interno->mes}}"+'"]').attr('selected',true);
        });
    </script>

    <script src="{{asset('js/finanzas/presupuesto_interno/crear.js') }}""></script>
@endsection
