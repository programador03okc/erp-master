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
                    <h3 class="box-title">EDITAR PRESUPUESTO INTERNO <span class="text-primary">{{$presupuesto_interno->codigo}}</span></h3>
                    <div class="box-tools pull-right">
                        {{-- <div class="btn-group" role="group"> --}}
                            <a href="{{ route('finanzas.presupuesto.presupuesto-interno.lista') }}" title="Volver a la lista de presupuesto interno"
                                class="btn btn-sm btn-danger">
                                <i class="fa fa-arrow-left"></i>
                                Volver
                            </a>
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
                                {{-- <input type="hidden" name="id_tipo_presupuesto"value="{{$presupuesto_interno->id_tipo_presupuesto}}"> --}}

                                <input type="hidden" name="tipo_ingresos"value="{{$presupuesto_interno->ingresos}}">
                                <input type="hidden" name="tipo_gastos"value="{{$presupuesto_interno->gastos}}">
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
                        {{-- <div class="col-md-3">
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
                        </div> --}}
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
        <div class="col-md-12 animate__animated {{(sizeof($ingresos)>0?'':'d-none')}}">
            <div class="box box-success">
                <div class="box-body" data-presupuesto="interno-modelo">
                    <div class="row" data-select="presupuesto-1">
                        {{-- @if ($presupuesto_interno->id_tipo_presupuesto==1) --}}
                        <div class="col-md-12">
                            <label>INGRESOS</label>
                            <div class="pull-right">
                                <a class="btn btn-box-tool" data-toggle="collapse" data-parent="#accordion" href="#collapse_ingresos">
                                <i class="fa fa-minus"></i></a>

                                <button type="button" class="btn btn-box-tool"  title="" data-tipo="1" data-action="remove">
                                    <i class="fa fa-times"></i></button>

                                <button type="button" class="btn btn-box-tool d-none" ><i class="fa fa-plus" title="Agregar presupuesto de costos" data-tipo="2" data-action="generar"></i></button>

                            </div>
                        </div>
                        <div class="col-md-12 panel-collapse collapse in" id="collapse_ingresos">
                            <table class="table small" id="partida-ingresos">
                                <thead>
                                    <tr>
                                        <th class="text-left" width="30">PARTIDA</th>
                                        <th class="text-left" width="">DESCRIPCION</th>

                                        <th class="text-left" width=""colspan="">ENE </th>
                                        <th class="text-left" width=""colspan="">FEB</th>
                                        <th class="text-left" width=""colspan="">MAR</th>
                                        <th class="text-left" width=""colspan="">ABR</th>
                                        <th class="text-left" width=""colspan="">MAY</th>
                                        <th class="text-left" width=""colspan="">JUN</th>
                                        <th class="text-left" width=""colspan="">JUL</th>
                                        <th class="text-left" width=""colspan="">AGO</th>
                                        <th class="text-left" width=""colspan="">SET</th>
                                        <th class="text-left" width=""colspan="">OCT</th>
                                        <th class="text-left" width=""colspan="">NOV</th>
                                        <th class="text-left" width=""colspan="">DIC</th>

                                        <th class="text-center" width="10"></th>

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
                                    <tr key="{{$input_key}}" data-nivel="{{sizeof($array)}}" data-partida="{{$item->partida}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}"

                                        {{ (sizeof($array)===2?'class=text-primary':'') }}
                                        {{ ($item->registro==='2'?'class=bg-danger':'') }}
                                        >
                                        <td data-td="partida">
                                            <input type="hidden" value="{{$item->partida}}" name="ingresos[{{$input_key}}][partida]" class="form-control input-sm">

                                            <input type="hidden" value="{{$item->id_hijo}}" name="ingresos[{{$input_key}}][id_hijo]" class="form-control input-sm">
                                            <input type="hidden" value="{{$item->id_padre}}" name="ingresos[{{$input_key}}][id_padre]" class="form-control input-sm">
                                            <span>{{$item->partida}}</span></td>

                                            {{-- @if (sizeof($array)===3 || sizeof($array)===4) --}}
                                                <td data-td="descripcion">
                                                    <input type="hidden" value="{{$item->descripcion}}" class="form-control input-sm" name="ingresos[{{$input_key}}][descripcion]" placeholder="{{$item->descripcion}}"><span>{{$item->descripcion}}</span>
                                                </td>

                                                {{-- <td data-td="monto">
                                                    <input type="hidden" value="{{$item->monto}}" class="form-control input-sm" name="ingresos[{{$input_key}}][monto]" placeholder="Ingrese monto" step="0.01">
                                                    <span> {{$item->monto}} </span>
                                                </td> --}}

                                                <td data-td="enero">
                                                    <input
                                                    type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                    value="{{$item->enero}}"
                                                    class="form-control input-sm"
                                                    name="ingresos[{{$input_key}}][enero]"
                                                    placeholder="Ingrese monto"
                                                    key="{{$input_key}}"
                                                    data-nivel="{{sizeof($array)}}"
                                                    data-id="{{$item->id_hijo}}"
                                                    data-id-padre="{{$item->id_padre}}"
                                                    data-tipo-text="ingresos"
                                                    data-mes="enero"
                                                    {{($item->registro==='2'?'data-input=partida':'')}}
                                                    >
                                                    @if ($item->registro==='1')
                                                    <span>{{$item->enero}}</span>
                                                    @endif
                                                </td>
                                                <td data-td="febrero">
                                                    <input
                                                    type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                    value="{{$item->febrero}}"
                                                    class="form-control input-sm"
                                                    name="ingresos[{{$input_key}}][febrero]"
                                                    placeholder="Ingrese monto"
                                                    key="{{$input_key}}"
                                                    data-nivel="{{sizeof($array)}}"
                                                    data-id="{{$item->id_hijo}}"
                                                    data-id-padre="{{$item->id_padre}}"
                                                    data-tipo-text="ingresos"
                                                    data-mes="febrero"
                                                    {{($item->registro==='2'?'data-input=partida':'')}}
                                                    >
                                                    @if ($item->registro==='1')
                                                    <span>{{$item->febrero}}</span>
                                                    @endif
                                                </td>

                                                <td data-td="marzo">
                                                    <input
                                                    type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                    value="{{$item->marzo}}"
                                                    class="form-control input-sm"
                                                    name="ingresos[{{$input_key}}][marzo]"
                                                    placeholder="Ingrese monto"
                                                    key="{{$input_key}}"
                                                    data-nivel="{{sizeof($array)}}"
                                                    data-id="{{$item->id_hijo}}"
                                                    data-id-padre="{{$item->id_padre}}"
                                                    data-tipo-text="ingresos"
                                                    data-mes="marzo"
                                                    {{($item->registro==='2'?'data-input=partida':'')}}
                                                    >
                                                    @if ($item->registro==='1')
                                                    <span>{{$item->marzo}}</span>
                                                    @endif
                                                </td>

                                                <td data-td="abril">
                                                    <input
                                                    type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                    value="{{$item->abril}}"
                                                    class="form-control input-sm"
                                                    name="ingresos[{{$input_key}}][abril]"
                                                    placeholder="Ingrese monto"
                                                    key="{{$input_key}}"
                                                    data-nivel="{{sizeof($array)}}"
                                                    data-id="{{$item->id_hijo}}"
                                                    data-id-padre="{{$item->id_padre}}"
                                                    data-tipo-text="ingresos"
                                                    data-mes="abril"
                                                    {{($item->registro==='2'?'data-input=partida':'')}}
                                                    >
                                                    @if ($item->registro==='1')
                                                    <span>{{$item->abril}}</span>
                                                    @endif
                                                </td>

                                                <td data-td="mayo">
                                                    <input
                                                    type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                    value="{{$item->mayo}}"
                                                    class="form-control input-sm"
                                                    name="ingresos[{{$input_key}}][mayo]"
                                                    placeholder="Ingrese monto"
                                                    key="{{$input_key}}"
                                                    data-nivel="{{sizeof($array)}}"
                                                    data-id="{{$item->id_hijo}}"
                                                    data-id-padre="{{$item->id_padre}}"
                                                    data-tipo-text="ingresos"
                                                    data-mes="mayo"
                                                    {{($item->registro==='2'?'data-input=partida':'')}}
                                                    >
                                                    @if ($item->registro==='1')
                                                    <span>{{$item->mayo}}</span>
                                                    @endif
                                                </td>

                                                <td data-td="junio">
                                                    <input
                                                    type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                    value="{{$item->junio}}"
                                                    class="form-control input-sm"
                                                    name="ingresos[{{$input_key}}][junio]"
                                                    placeholder="Ingrese monto"
                                                    key="{{$input_key}}"
                                                    data-nivel="{{sizeof($array)}}"
                                                    data-id="{{$item->id_hijo}}"
                                                    data-id-padre="{{$item->id_padre}}"
                                                    data-tipo-text="ingresos"
                                                    data-mes="junio"
                                                    {{($item->registro==='2'?'data-input=partida':'')}}
                                                    >
                                                    @if ($item->registro==='1')
                                                    <span>{{$item->junio}}</span>
                                                    @endif
                                                </td>

                                                <td data-td="julio">
                                                    <input
                                                    type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                    value="{{$item->julio}}"
                                                    class="form-control input-sm"
                                                    name="ingresos[{{$input_key}}][julio]"
                                                    placeholder="Ingrese monto"
                                                    key="{{$input_key}}"
                                                    data-nivel="{{sizeof($array)}}"
                                                    data-id="{{$item->id_hijo}}"
                                                    data-id-padre="{{$item->id_padre}}"
                                                    data-tipo-text="ingresos"
                                                    data-mes="julio"
                                                    {{($item->registro==='2'?'data-input=partida':'')}}
                                                    >
                                                    @if ($item->registro==='1')
                                                    <span>{{$item->julio}}</span>
                                                    @endif
                                                </td>

                                                <td data-td="agosto">
                                                    <input
                                                    type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                    value="{{$item->agosto}}"
                                                    class="form-control input-sm"
                                                    name="ingresos[{{$input_key}}][agosto]"
                                                    placeholder="Ingrese monto"
                                                    key="{{$input_key}}"
                                                    data-nivel="{{sizeof($array)}}"
                                                    data-id="{{$item->id_hijo}}"
                                                    data-id-padre="{{$item->id_padre}}"
                                                    data-tipo-text="ingresos"
                                                    data-mes="agosto"
                                                    {{($item->registro==='2'?'data-input=partida':'')}}
                                                    >
                                                    @if ($item->registro==='1')
                                                    <span>{{$item->agosto}}</span>
                                                    @endif
                                                </td>

                                                <td data-td="setiembre">
                                                    <input
                                                    type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                    value="{{$item->setiembre}}"
                                                    class="form-control input-sm"
                                                    name="ingresos[{{$input_key}}][setiembre]"
                                                    placeholder="Ingrese monto"
                                                    key="{{$input_key}}"
                                                    data-nivel="{{sizeof($array)}}"
                                                    data-id="{{$item->id_hijo}}"
                                                    data-id-padre="{{$item->id_padre}}"
                                                    data-tipo-text="ingresos"
                                                    data-mes="setiembre"
                                                    {{($item->registro==='2'?'data-input=partida':'')}}
                                                    >
                                                    @if ($item->registro==='1')
                                                    <span>{{$item->setiembre}}</span>
                                                    @endif
                                                </td>

                                                <td data-td="octubre">
                                                    <input
                                                    type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                    value="{{$item->octubre}}"
                                                    class="form-control input-sm"
                                                    name="ingresos[{{$input_key}}][octubre]"
                                                    placeholder="Ingrese monto"
                                                    key="{{$input_key}}"
                                                    data-nivel="{{sizeof($array)}}"
                                                    data-id="{{$item->id_hijo}}"
                                                    data-id-padre="{{$item->id_padre}}"
                                                    data-tipo-text="ingresos"
                                                    data-mes="octubre"
                                                    {{($item->registro==='2'?'data-input=partida':'')}}
                                                    >
                                                    @if ($item->registro==='1')
                                                    <span>{{$item->octubre}}</span>
                                                    @endif
                                                </td>

                                                <td data-td="noviembre">
                                                    <input
                                                    type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                    value="{{$item->noviembre}}"
                                                    class="form-control input-sm"
                                                    name="ingresos[{{$input_key}}][noviembre]"
                                                    placeholder="Ingrese monto"
                                                    key="{{$input_key}}"
                                                    data-nivel="{{sizeof($array)}}"
                                                    data-id="{{$item->id_hijo}}"
                                                    data-id-padre="{{$item->id_padre}}"
                                                    data-tipo-text="ingresos"
                                                    data-mes="noviembre"
                                                    {{($item->registro==='2'?'data-input=partida':'')}}
                                                    >
                                                    @if ($item->registro==='1')
                                                    <span>{{$item->noviembre}}</span>
                                                    @endif
                                                </td>

                                                <td data-td="diciembre">
                                                    <input
                                                    type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                    value="{{$item->diciembre}}"
                                                    class="form-control input-sm"
                                                    name="ingresos[{{$input_key}}][diciembre]"
                                                    placeholder="Ingrese monto"
                                                    key="{{$input_key}}"
                                                    data-nivel="{{sizeof($array)}}"
                                                    data-id="{{$item->id_hijo}}"
                                                    data-id-padre="{{$item->id_padre}}"
                                                    data-tipo-text="ingresos"
                                                    data-mes="diciembre"
                                                    {{($item->registro==='2'?'data-input=partida':'')}}
                                                    >
                                                    @if ($item->registro==='1')
                                                    <span>{{$item->diciembre}}</span>
                                                    @endif
                                                </td>





                                        <td data-td="accion">
                                            <div class="btn-group">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                    <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                    @if ($item->registro==='1')
                                                        <input type="hidden" name="ingresos[{{$input_key}}][registro]" value="1">
                                                        <li><a href="#" class="" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-nuevo" data-select="titulo" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="ingresos" title="Agregar titulo" data-tipo="nuevo">Agregar titulo</a></li>

                                                        <li><a href="#" class="" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-partida" data-select="partida" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="ingresos" title="Agregar partida" data-tipo="nuevo">Agregar partida</a></li>

                                                        <li><a href="#" class="" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-nuevo" data-select="titulo" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="ingresos" title="Editar" data-tipo="editar">Editar</a></li>
                                                    @endif
                                                    @if ($item->registro==='2')
                                                        <input type="hidden" name="ingresos[{{$input_key}}][registro]" value="2">
                                                        <li><a href="#" class="" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-partida" data-select="partida" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="ingresos" title="Editar partida" data-tipo="editar">Editar partida</a></li>
                                                    @endif

                                                    @if (sizeof($array)!==1) {
                                                        <li><a href="#" class="" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-eliminar" data-nivel="{{sizeof($array)}}" title="Eliminar" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="ingresos">Eliminar</a></li>
                                                    @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{-- @endif --}}

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 animate__animated {{(sizeof($costos)>0?'':'d-none')}}">
            <div class="box box-success">
                <div class="box-body" data-presupuesto="interno-modelo">
                    <div class="row" data-select="presupuesto-2">
                        {{-- @if ($presupuesto_interno->id_tipo_presupuesto===1) --}}
                            <div class="col-md-12">
                                <label>COSTOS</label>
                                <div class="pull-right">
                                    <a class="btn btn-box-tool" data-toggle="collapse" data-parent="#accordion" href="#collapse_costos">
                                    <i class="fa fa-minus"></i></a>
                                    <button type="button" class="btn btn-box-tool"  title="" data-tipo="2" data-action="remove">
                                        <i class="fa fa-times"></i></button>
                                    <button type="button" class="btn btn-box-tool d-none" ><i class="fa fa-plus" title="Agregar presupuesto de costos" data-tipo="3" data-action="generar"></i></button>

                                </div>
                            </div>
                            <div class="col-md-12 panel-collapse collapse in" id="collapse_costos">
                                <table class="table small" id="partida-costos">
                                    <thead>
                                        <tr>
                                            <th class="text-left" width="30">PARTIDA</th>
                                            <th class="text-left" width="">DESCRIPCION</th>

                                            <th class="text-left" width=""colspan="">ENE </th>
                                            <th class="text-left" width=""colspan="">FEB</th>
                                            <th class="text-left" width=""colspan="">MAR</th>
                                            <th class="text-left" width=""colspan="">ABR</th>
                                            <th class="text-left" width=""colspan="">MAY</th>
                                            <th class="text-left" width=""colspan="">JUN</th>
                                            <th class="text-left" width=""colspan="">JUL</th>
                                            <th class="text-left" width=""colspan="">AGO</th>
                                            <th class="text-left" width=""colspan="">SET</th>
                                            <th class="text-left" width=""colspan="">OCT</th>
                                            <th class="text-left" width=""colspan="">NOV</th>
                                            <th class="text-left" width=""colspan="">DIC</th>
                                            <th class="text-center" width="10"></th>
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
                                    <tr key="{{$input_key}}" data-nivel="{{sizeof($array)}}" data-partida="{{$item->partida}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}"
                                        {{ (sizeof($array)===2?'class=text-primary':'') }}
                                        {{ ($item->registro==='2'?'class=bg-danger':'') }}>
                                        <td data-td="partida">
                                            <input type="hidden" value="{{$item->partida}}" name="costos[{{$input_key}}][partida]" class="form-control input-sm">

                                            <input type="hidden" value="{{$item->id_hijo}}" name="costos[{{$input_key}}][id_hijo]" class="form-control input-sm">
                                            <input type="hidden" value="{{$item->id_padre}}" name="costos[{{$input_key}}][id_padre]" class="form-control input-sm">
                                            <span>{{$item->partida}}</span></td>

                                            {{-- @if (sizeof($array)===3 || sizeof($array)===4) --}}
                                                <td data-td="descripcion">
                                                    <input type="hidden" value="{{$item->descripcion}}" class="form-control input-sm" name="costos[{{$input_key}}][descripcion]" placeholder="{{$item->descripcion}}"><span>{{$item->descripcion}}</span>
                                                </td>

                                                {{-- <td data-td="monto">
                                                    <input type="hidden" value="{{$item->monto}}" class="form-control input-sm" name="costos[{{$input_key}}][monto]" placeholder="Ingrese monto" step="0.01">
                                                    <span> {{$item->monto}} </span>
                                                </td> --}}
                                                <td data-td="enero">
                                                    <input
                                                    type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                    value="{{$item->enero}}"
                                                    class="form-control input-sm"
                                                    name="costos[{{$input_key}}][enero]"
                                                    placeholder="Ingrese monto"
                                                    key="{{$input_key}}"
                                                    data-nivel="{{sizeof($array)}}"
                                                    data-id="{{$item->id_hijo}}"
                                                    data-id-padre="{{$item->id_padre}}"
                                                    data-tipo-text="costos"
                                                    data-mes="enero"
                                                    {{($item->registro==='2'?'data-input=partida':'')}}
                                                    >
                                                    @if ($item->registro==='1')
                                                    <span>{{$item->enero}}</span>
                                                    @endif
                                                </td>
                                                <td data-td="febrero">
                                                    <input
                                                    type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                    value="{{$item->febrero}}"
                                                    class="form-control input-sm"
                                                    name="costos[{{$input_key}}][febrero]"
                                                    placeholder="Ingrese monto"
                                                    key="{{$input_key}}"
                                                    data-nivel="{{sizeof($array)}}"
                                                    data-id="{{$item->id_hijo}}"
                                                    data-id-padre="{{$item->id_padre}}"
                                                    data-tipo-text="costos"
                                                    data-mes="febrero"
                                                    {{($item->registro==='2'?'data-input=partida':'')}}
                                                    >
                                                    @if ($item->registro==='1')
                                                    <span>{{$item->febrero}}</span>
                                                    @endif
                                                </td>

                                                <td data-td="marzo">
                                                    <input
                                                    type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                    value="{{$item->marzo}}"
                                                    class="form-control input-sm"
                                                    name="costos[{{$input_key}}][marzo]"
                                                    placeholder="Ingrese monto"
                                                    key="{{$input_key}}"
                                                    data-nivel="{{sizeof($array)}}"
                                                    data-id="{{$item->id_hijo}}"
                                                    data-id-padre="{{$item->id_padre}}"
                                                    data-tipo-text="costos"
                                                    data-mes="marzo"
                                                    {{($item->registro==='2'?'data-input=partida':'')}}
                                                    >
                                                    @if ($item->registro==='1')
                                                    <span>{{$item->marzo}}</span>
                                                    @endif
                                                </td>

                                                <td data-td="abril">
                                                    <input
                                                    type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                    value="{{$item->abril}}"
                                                    class="form-control input-sm"
                                                    name="costos[{{$input_key}}][abril]"
                                                    placeholder="Ingrese monto"
                                                    key="{{$input_key}}"
                                                    data-nivel="{{sizeof($array)}}"
                                                    data-id="{{$item->id_hijo}}"
                                                    data-id-padre="{{$item->id_padre}}"
                                                    data-tipo-text="costos"
                                                    data-mes="abril"
                                                    {{($item->registro==='2'?'data-input=partida':'')}}
                                                    >
                                                    @if ($item->registro==='1')
                                                    <span>{{$item->abril}}</span>
                                                    @endif
                                                </td>

                                                <td data-td="mayo">
                                                    <input
                                                    type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                    value="{{$item->mayo}}"
                                                    class="form-control input-sm"
                                                    name="costos[{{$input_key}}][mayo]"
                                                    placeholder="Ingrese monto"
                                                    key="{{$input_key}}"
                                                    data-nivel="{{sizeof($array)}}"
                                                    data-id="{{$item->id_hijo}}"
                                                    data-id-padre="{{$item->id_padre}}"
                                                    data-tipo-text="costos"
                                                    data-mes="mayo"
                                                    {{($item->registro==='2'?'data-input=partida':'')}}
                                                    >
                                                    @if ($item->registro==='1')
                                                    <span>{{$item->mayo}}</span>
                                                    @endif
                                                </td>

                                                <td data-td="junio">
                                                    <input
                                                    type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                    value="{{$item->junio}}"
                                                    class="form-control input-sm"
                                                    name="costos[{{$input_key}}][junio]"
                                                    placeholder="Ingrese monto"
                                                    key="{{$input_key}}"
                                                    data-nivel="{{sizeof($array)}}"
                                                    data-id="{{$item->id_hijo}}"
                                                    data-id-padre="{{$item->id_padre}}"
                                                    data-tipo-text="costos"
                                                    data-mes="junio"
                                                    {{($item->registro==='2'?'data-input=partida':'')}}
                                                    >
                                                    @if ($item->registro==='1')
                                                    <span>{{$item->junio}}</span>
                                                    @endif
                                                </td>

                                                <td data-td="julio">
                                                    <input
                                                    type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                    value="{{$item->julio}}"
                                                    class="form-control input-sm"
                                                    name="costos[{{$input_key}}][julio]"
                                                    placeholder="Ingrese monto"
                                                    key="{{$input_key}}"
                                                    data-nivel="{{sizeof($array)}}"
                                                    data-id="{{$item->id_hijo}}"
                                                    data-id-padre="{{$item->id_padre}}"
                                                    data-tipo-text="costos"
                                                    data-mes="julio"
                                                    {{($item->registro==='2'?'data-input=partida':'')}}
                                                    >
                                                    @if ($item->registro==='1')
                                                    <span>{{$item->julio}}</span>
                                                    @endif
                                                </td>

                                                <td data-td="agosto">
                                                    <input
                                                    type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                    value="{{$item->agosto}}"
                                                    class="form-control input-sm"
                                                    name="costos[{{$input_key}}][agosto]"
                                                    placeholder="Ingrese monto"
                                                    key="{{$input_key}}"
                                                    data-nivel="{{sizeof($array)}}"
                                                    data-id="{{$item->id_hijo}}"
                                                    data-id-padre="{{$item->id_padre}}"
                                                    data-tipo-text="costos"
                                                    data-mes="agosto"
                                                    {{($item->registro==='2'?'data-input=partida':'')}}
                                                    >
                                                    @if ($item->registro==='1')
                                                    <span>{{$item->agosto}}</span>
                                                    @endif
                                                </td>

                                                <td data-td="setiembre">
                                                    <input
                                                    type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                    value="{{$item->setiembre}}"
                                                    class="form-control input-sm"
                                                    name="costos[{{$input_key}}][setiembre]"
                                                    placeholder="Ingrese monto"
                                                    key="{{$input_key}}"
                                                    data-nivel="{{sizeof($array)}}"
                                                    data-id="{{$item->id_hijo}}"
                                                    data-id-padre="{{$item->id_padre}}"
                                                    data-tipo-text="costos"
                                                    data-mes="setiembre"
                                                    {{($item->registro==='2'?'data-input=partida':'')}}
                                                    >
                                                    @if ($item->registro==='1')
                                                    <span>{{$item->setiembre}}</span>
                                                    @endif
                                                </td>

                                                <td data-td="octubre">
                                                    <input
                                                    type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                    value="{{$item->octubre}}"
                                                    class="form-control input-sm"
                                                    name="costos[{{$input_key}}][octubre]"
                                                    placeholder="Ingrese monto"
                                                    key="{{$input_key}}"
                                                    data-nivel="{{sizeof($array)}}"
                                                    data-id="{{$item->id_hijo}}"
                                                    data-id-padre="{{$item->id_padre}}"
                                                    data-tipo-text="costos"
                                                    data-mes="octubre"
                                                    {{($item->registro==='2'?'data-input=partida':'')}}
                                                    >
                                                    @if ($item->registro==='1')
                                                    <span>{{$item->octubre}}</span>
                                                    @endif
                                                </td>

                                                <td data-td="noviembre">
                                                    <input
                                                    type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                    value="{{$item->noviembre}}"
                                                    class="form-control input-sm"
                                                    name="costos[{{$input_key}}][noviembre]"
                                                    placeholder="Ingrese monto"
                                                    key="{{$input_key}}"
                                                    data-nivel="{{sizeof($array)}}"
                                                    data-id="{{$item->id_hijo}}"
                                                    data-id-padre="{{$item->id_padre}}"
                                                    data-tipo-text="costos"
                                                    data-mes="noviembre"
                                                    {{($item->registro==='2'?'data-input=partida':'')}}
                                                    >
                                                    @if ($item->registro==='1')
                                                    <span>{{$item->noviembre}}</span>
                                                    @endif
                                                </td>

                                                <td data-td="diciembre">
                                                    <input
                                                    type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                    value="{{$item->diciembre}}"
                                                    class="form-control input-sm"
                                                    name="costos[{{$input_key}}][diciembre]"
                                                    placeholder="Ingrese monto"
                                                    key="{{$input_key}}"
                                                    data-nivel="{{sizeof($array)}}"
                                                    data-id="{{$item->id_hijo}}"
                                                    data-id-padre="{{$item->id_padre}}"
                                                    data-tipo-text="costos"
                                                    data-mes="diciembre"
                                                    {{($item->registro==='2'?'data-input=partida':'')}}
                                                    >
                                                    @if ($item->registro==='1')
                                                    <span>{{$item->diciembre}}</span>
                                                    @endif
                                                </td>

                                            {{-- @else
                                                <td colspan="2" data-td="descripcion"><input type="hidden" value="{{$item->descripcion}}" class="form-control input-sm" name="costos[{{$input_key}}][descripcion]"><span>{{$item->descripcion}}</span></td>
                                            @endif --}}

                                        <td data-td="accion">
                                            <div class="btn-group">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                    <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                    @if ($item->registro==='1')
                                                        <input type="hidden" name="costos[{{$input_key}}][registro]" value="1">
                                                        <li><a href="#" class="" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-nuevo" data-select="titulo" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="costos" title="Agregar titulo" data-tipo="nuevo">Agregar titulo</a></li>

                                                        <li><a href="#" class="" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-partida" data-select="partida" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="costos" title="Agregar partida" data-tipo="nuevo">Agregar partida</a></li>

                                                        <li><a href="#" class="" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-nuevo" data-select="titulo" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="costos" title="Editar" data-tipo="editar">Editar</a></li>
                                                    @endif
                                                    @if ($item->registro==='2')
                                                        <input type="hidden" name="costos[{{$input_key}}][registro]" value="2">
                                                        <li><a href="#" class="" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-partida" data-select="partida" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="costos" title="Editar partida" data-tipo="editar">Editar partida</a></li>
                                                    @endif

                                                    @if (sizeof($array)!==1) {
                                                        <li><a href="#" class="" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-eliminar" data-nivel="{{sizeof($array)}}" title="Eliminar" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="costos">Eliminar</a></li>
                                                    @endif
                                                    </ul>
                                                </div>
                                            </div>


                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        {{-- @endif --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 animate__animated {{(sizeof($gastos)>0?'':'d-none')}}">
            <div class="box box-success">
                <div class="box-body" data-presupuesto="interno-modelo">
                    <div class="row" data-select="presupuesto-3">
                        {{-- @if ($presupuesto_interno->id_tipo_presupuesto===3) --}}
                        <div class="col-md-12">
                            <label>GASTOS</label>
                            <div class="pull-right">
                                <a class="btn btn-box-tool" data-toggle="collapse" data-parent="#accordion" href="#collapse_gastos">
                                <i class="fa fa-minus"></i></a>
                                <button type="button" class="btn btn-box-tool"  title="" data-tipo="3" data-action="remove">
                                    <i class="fa fa-times"></i></button>
                                <button type="button" class="btn btn-box-tool d-none" ><i class="fa fa-plus" title="Agregar presupuesto de costos" data-tipo="1" data-action="generar"></i></button>

                            </div>
                        </div>
                        <div class="col-md-12 panel-collapse collapse in" id="collapse_gastos">
                            <table class="table small" id="partida-gastos">
                                <thead>
                                    <tr>
                                        <th class="text-left" width="30">PARTIDA</th>
                                        <th class="text-left" width="">DESCRIPCION</th>
                                        <th class="text-left" width=""colspan="">ENE </th>
                                            <th class="text-left" width=""colspan="">FEB</th>
                                            <th class="text-left" width=""colspan="">MAR</th>
                                            <th class="text-left" width=""colspan="">ABR</th>
                                            <th class="text-left" width=""colspan="">MAY</th>
                                            <th class="text-left" width=""colspan="">JUN</th>
                                            <th class="text-left" width=""colspan="">JUL</th>
                                            <th class="text-left" width=""colspan="">AGO</th>
                                            <th class="text-left" width=""colspan="">SET</th>
                                            <th class="text-left" width=""colspan="">OCT</th>
                                            <th class="text-left" width=""colspan="">NOV</th>
                                            <th class="text-left" width=""colspan="">DIC</th>
                                        <th class="text-center" width="10"></th>
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
                                <tr key="{{$input_key}}" data-nivel="{{sizeof($array)}}" data-partida="{{$item->partida}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}"
                                    {{ sizeof($array)===2?'class=text-primary':'' }}
                                    {{ ($item->registro==='2'?'class=bg-danger':'') }}>
                                    <td data-td="partida">
                                        <input type="hidden" value="{{$item->partida}}" name="gastos[{{$input_key}}][partida]" class="form-control input-sm">

                                        <input type="hidden" value="{{$item->id_hijo}}" name="gastos[{{$input_key}}][id_hijo]" class="form-control input-sm">
                                        <input type="hidden" value="{{$item->id_padre}}" name="gastos[{{$input_key}}][id_padre]" class="form-control input-sm">
                                        <span>{{$item->partida}}</span></td>

                                        {{-- @if (sizeof($array)===3 || sizeof($array)===4) --}}
                                            <td data-td="descripcion">
                                                <input type="hidden" value="{{$item->descripcion}}" class="form-control input-sm" name="gastos[{{$input_key}}][descripcion]" placeholder="{{$item->descripcion}}"><span>{{$item->descripcion}}</span>
                                            </td>

                                            {{-- <td data-td="monto">
                                                <input type="hidden" value="{{$item->monto}}" class="form-control input-sm" name="gastos[{{$input_key}}][monto]" placeholder="Ingrese monto" step="0.01">
                                                <span> {{$item->monto}} </span>
                                            </td> --}}

                                            <td data-td="enero">
                                                <input
                                                type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                value="{{$item->enero}}"
                                                class="form-control input-sm"
                                                name="gastos[{{$input_key}}][enero]"
                                                placeholder="Ingrese monto"
                                                key="{{$input_key}}"
                                                data-nivel="{{sizeof($array)}}"
                                                data-id="{{$item->id_hijo}}"
                                                data-id-padre="{{$item->id_padre}}"
                                                data-tipo-text="gastos"
                                                data-mes="enero"
                                                {{($item->registro==='2'?'data-input=partida':'')}}
                                                >
                                                @if ($item->registro==='1')
                                                <span>{{$item->enero}}</span>
                                                @endif
                                            </td>
                                            <td data-td="febrero">
                                                <input
                                                type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                value="{{$item->febrero}}"
                                                class="form-control input-sm"
                                                name="gastos[{{$input_key}}][febrero]"
                                                placeholder="Ingrese monto"
                                                key="{{$input_key}}"
                                                data-nivel="{{sizeof($array)}}"
                                                data-id="{{$item->id_hijo}}"
                                                data-id-padre="{{$item->id_padre}}"
                                                data-tipo-text="gastos"
                                                data-mes="febrero"
                                                {{($item->registro==='2'?'data-input=partida':'')}}
                                                >
                                                @if ($item->registro==='1')
                                                <span>{{$item->febrero}}</span>
                                                @endif
                                            </td>

                                            <td data-td="marzo">
                                                <input
                                                type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                value="{{$item->marzo}}"
                                                class="form-control input-sm"
                                                name="gastos[{{$input_key}}][marzo]"
                                                placeholder="Ingrese monto"
                                                key="{{$input_key}}"
                                                data-nivel="{{sizeof($array)}}"
                                                data-id="{{$item->id_hijo}}"
                                                data-id-padre="{{$item->id_padre}}"
                                                data-tipo-text="gastos"
                                                data-mes="marzo"
                                                {{($item->registro==='2'?'data-input=partida':'')}}
                                                >
                                                @if ($item->registro==='1')
                                                <span>{{$item->marzo}}</span>
                                                @endif
                                            </td>

                                            <td data-td="abril">
                                                <input
                                                type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                value="{{$item->abril}}"
                                                class="form-control input-sm"
                                                name="gastos[{{$input_key}}][abril]"
                                                placeholder="Ingrese monto"
                                                key="{{$input_key}}"
                                                data-nivel="{{sizeof($array)}}"
                                                data-id="{{$item->id_hijo}}"
                                                data-id-padre="{{$item->id_padre}}"
                                                data-tipo-text="gastos"
                                                data-mes="abril"
                                                {{($item->registro==='2'?'data-input=partida':'')}}
                                                >
                                                @if ($item->registro==='1')
                                                <span>{{$item->abril}}</span>
                                                @endif
                                            </td>

                                            <td data-td="mayo">
                                                <input
                                                type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                value="{{$item->mayo}}"
                                                class="form-control input-sm"
                                                name="gastos[{{$input_key}}][mayo]"
                                                placeholder="Ingrese monto"
                                                key="{{$input_key}}"
                                                data-nivel="{{sizeof($array)}}"
                                                data-id="{{$item->id_hijo}}"
                                                data-id-padre="{{$item->id_padre}}"
                                                data-tipo-text="gastos"
                                                data-mes="mayo"
                                                {{($item->registro==='2'?'data-input=partida':'')}}
                                                >
                                                @if ($item->registro==='1')
                                                <span>{{$item->mayo}}</span>
                                                @endif
                                            </td>

                                            <td data-td="junio">
                                                <input
                                                type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                value="{{$item->junio}}"
                                                class="form-control input-sm"
                                                name="gastos[{{$input_key}}][junio]"
                                                placeholder="Ingrese monto"
                                                key="{{$input_key}}"
                                                data-nivel="{{sizeof($array)}}"
                                                data-id="{{$item->id_hijo}}"
                                                data-id-padre="{{$item->id_padre}}"
                                                data-tipo-text="gastos"
                                                data-mes="junio"
                                                {{($item->registro==='2'?'data-input=partida':'')}}
                                                >
                                                @if ($item->registro==='1')
                                                <span>{{$item->junio}}</span>
                                                @endif
                                            </td>

                                            <td data-td="julio">
                                                <input
                                                type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                value="{{$item->julio}}"
                                                class="form-control input-sm"
                                                name="gastos[{{$input_key}}][julio]"
                                                placeholder="Ingrese monto"
                                                key="{{$input_key}}"
                                                data-nivel="{{sizeof($array)}}"
                                                data-id="{{$item->id_hijo}}"
                                                data-id-padre="{{$item->id_padre}}"
                                                data-tipo-text="gastos"
                                                data-mes="julio"
                                                {{($item->registro==='2'?'data-input=partida':'')}}
                                                >
                                                @if ($item->registro==='1')
                                                <span>{{$item->julio}}</span>
                                                @endif
                                            </td>

                                            <td data-td="agosto">
                                                <input
                                                type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                value="{{$item->agosto}}"
                                                class="form-control input-sm"
                                                name="gastos[{{$input_key}}][agosto]"
                                                placeholder="Ingrese monto"
                                                key="{{$input_key}}"
                                                data-nivel="{{sizeof($array)}}"
                                                data-id="{{$item->id_hijo}}"
                                                data-id-padre="{{$item->id_padre}}"
                                                data-tipo-text="gastos"
                                                data-mes="agosto"
                                                {{($item->registro==='2'?'data-input=partida':'')}}
                                                >
                                                @if ($item->registro==='1')
                                                <span>{{$item->agosto}}</span>
                                                @endif
                                            </td>

                                            <td data-td="setiembre">
                                                <input
                                                type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                value="{{$item->setiembre}}"
                                                class="form-control input-sm"
                                                name="gastos[{{$input_key}}][setiembre]"
                                                placeholder="Ingrese monto"
                                                key="{{$input_key}}"
                                                data-nivel="{{sizeof($array)}}"
                                                data-id="{{$item->id_hijo}}"
                                                data-id-padre="{{$item->id_padre}}"
                                                data-tipo-text="gastos"
                                                data-mes="setiembre"
                                                {{($item->registro==='2'?'data-input=partida':'')}}
                                                >
                                                @if ($item->registro==='1')
                                                <span>{{$item->setiembre}}</span>
                                                @endif
                                            </td>

                                            <td data-td="octubre">
                                                <input
                                                type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                value="{{$item->octubre}}"
                                                class="form-control input-sm"
                                                name="gastos[{{$input_key}}][octubre]"
                                                placeholder="Ingrese monto"
                                                key="{{$input_key}}"
                                                data-nivel="{{sizeof($array)}}"
                                                data-id="{{$item->id_hijo}}"
                                                data-id-padre="{{$item->id_padre}}"
                                                data-tipo-text="gastos"
                                                data-mes="octubre"
                                                {{($item->registro==='2'?'data-input=partida':'')}}
                                                >
                                                @if ($item->registro==='1')
                                                <span>{{$item->octubre}}</span>
                                                @endif
                                            </td>

                                            <td data-td="noviembre">
                                                <input
                                                type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                value="{{$item->noviembre}}"
                                                class="form-control input-sm"
                                                name="gastos[{{$input_key}}][noviembre]"
                                                placeholder="Ingrese monto"
                                                key="{{$input_key}}"
                                                data-nivel="{{sizeof($array)}}"
                                                data-id="{{$item->id_hijo}}"
                                                data-id-padre="{{$item->id_padre}}"
                                                data-tipo-text="gastos"
                                                data-mes="noviembre"
                                                {{($item->registro==='2'?'data-input=partida':'')}}
                                                >
                                                @if ($item->registro==='1')
                                                <span>{{$item->noviembre}}</span>
                                                @endif
                                            </td>

                                            <td data-td="diciembre">
                                                <input
                                                type="{{($item->registro==='1'? 'hidden':'text')}}"
                                                value="{{$item->diciembre}}"
                                                class="form-control input-sm"
                                                name="gastos[{{$input_key}}][diciembre]"
                                                placeholder="Ingrese monto"
                                                key="{{$input_key}}"
                                                data-nivel="{{sizeof($array)}}"
                                                data-id="{{$item->id_hijo}}"
                                                data-id-padre="{{$item->id_padre}}"
                                                data-tipo-text="gastos"
                                                data-mes="diciembre"
                                                {{($item->registro==='2'?'data-input=partida':'')}}
                                                >
                                                @if ($item->registro==='1')
                                                <span>{{$item->diciembre}}</span>
                                                @endif
                                            </td>

                                        {{-- @else
                                            <td colspan="2" data-td="descripcion"><input type="hidden" value="{{$item->descripcion}}" class="form-control input-sm" name="gastos[{{$input_key}}][descripcion]"><span>{{$item->descripcion}}</span></td>
                                        @endif --}}

                                    <td data-td="accion">
                                        <div class="btn-group">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                @if ($item->registro==='1')
                                                    <input type="hidden" name="gastos[{{$input_key}}][registro]" value="1">
                                                    <li><a href="#" class="" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-nuevo" data-select="titulo" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="gastos" title="Agregar titulo" data-tipo="nuevo">Agregar titulo</a></li>

                                                    <li><a href="#" class="" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-partida" data-select="partida" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="gastos" title="Agregar partida" data-tipo="nuevo">Agregar partida</a></li>

                                                    <li><a href="#" class="" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-nuevo" data-select="titulo" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="gastos" title="Editar" data-tipo="editar">Editar</a></li>
                                                @endif
                                                @if ($item->registro==='2')
                                                    <input type="hidden" name="gastos[{{$input_key}}][registro]" value="2">
                                                    <li><a href="#" class="" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-partida" data-select="partida" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="gastos" title="Editar partida" data-tipo="editar">Editar partida</a></li>
                                                @endif

                                                @if (sizeof($array)!==1) {
                                                    <li><a href="#" class="" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-eliminar" data-nivel="{{sizeof($array)}}" title="Eliminar" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="gastos">Eliminar</a></li>
                                                @endif
                                                </ul>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{-- @endif --}}
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
                        <input id="id_descripcion_titulo" class="form-control" type="text" name="descripcion" onkeyup="javascript:this.value=this.value.toUpperCase();"style="text-transform:uppercase;" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-dismiss="modal" type="button"><i class="fa fa-times"></i> CERRAR</button>
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
                        <input id="id_descripcion_partida" class="form-control" type="text" name="descripcion" onkeyup="javascript:this.value=this.value.toUpperCase();"style="text-transform:uppercase;" required>
                    </div>
                    {{-- <div class="form-group">
                        <label for="id_monto_partida">Monto :</label>
                        <input id="id_monto_partida" class="form-control" type="number" name="monto" step="0.01" required>
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-dismiss="modal" type="button"><i class="fa fa-times"></i> CERRAR</button>
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
