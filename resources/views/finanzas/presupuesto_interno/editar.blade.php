@extends('layout.main')
@include('layout.menu_finanzas')

@section('cabecera')
Presupuesto Interno
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/jquery-datatables-checkboxes/css/dataTables.checkboxes.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuario-accesos.css') }}">
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
@if (in_array(298,$array_accesos))
    <form action="{{ route('finanzas.presupuesto.presupuesto-interno.actualizar') }}" method="post" data-form="editar-partida" enctype="multipart/formdata">
        <input type="hidden" name="id_presupuesto_interno" value="{{ $id }}">
        <input type="hidden" name="estado" value="{{ $presupuesto_interno->estado }}">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-danger">
                    <div class="box-header with-border">
                        <h3 class="box-title" style="display: inline-block;">EDITAR PRESUPUESTO INTERNO <span class="text-primary">{{$presupuesto_interno->codigo}}</span></h3>
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
                        @if ($presupuesto_interno->estado === 2)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-warning" role="alert">
                                        <strong><i class="fa fa-question-circle"></i></strong>
                                        El presupuesto se encuentra aprobado.
                                        Al editar tener en concideración que la suma de las partidas no puede pasar el monto ya aprobado del Presupuesto Interno.
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="empresa_id">Empresas :</label>
                                    <select class="form-control" name="empresa_id" id="empresa_id" required>
                                        <option value="">Seleccione...</option>
                                        @foreach ($empresas as $item)
                                            <option value="{{ $item->id_empresa }}" {{($item->id_empresa===$presupuesto_interno->empresa_id?'selected':'')}}>{{ $item->codigo }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sede_id">Sedes :</label>
                                    <select class="form-control" name="sede_id" id="sede_id" required>
                                        <option value="">Seleccione...</option>
                                        @foreach ($sedes as $item)
                                            <option value="{{ $item->id_sede }}" {{($item->id_sede===$presupuesto_interno->sede_id?'selected':'')}}>{{ $item->descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
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
                                            <option value="{{ $item->id_division }}" {{($item->id_division===$presupuesto_interno->id_area?'selected':'')}}>{{ $item->descripcion }}</option>
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
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="descripcion">Descripcion : </label>
                                    <textarea id="descripcion" class="form-control" name="descripcion" rows="3" required>{{$presupuesto_interno->descripcion}}</textarea>
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
                                            <th class="text-left" width="" hidden>%</th>

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
                                        @php
                                            $array_porcentajes=[];
                                            $numero_partida ;
                                            $elemento_array ;
                                        @endphp
                                        @foreach ($ingresos as $item)

                                            @php
                                                $array = explode(".", $item->partida);
                                                $id=rand();
                                                $id_padre=rand();
                                                $input_key=rand();

                                                $partida_padre  = '';
                                                $partida_hijo   = $item->partida;
                                                $numero_partida ='';

                                                if ($item->registro==='2') {

                                                    foreach ($array as $key => $value) {
                                                        if ($key < sizeof($array)-1) {
                                                            $partida_padre = ($key===0 ?$value :$partida_padre.'.'.$value );
                                                        }
                                                        if ($key === sizeof($array)-1) {
                                                            $numero_partida = $value;
                                                        }
                                                    }

                                                if ($item->porcentaje_gobierno || $item->porcentaje_privado) {
                                                    if (sizeof($array_porcentajes)>0) {
                                                        $partida_encontrada=false;
                                                        foreach ($array_porcentajes as $key_array => $value_array) {

                                                            if ($value_array->partida===$partida_padre) {
                                                                $elemento_array = $value_array;
                                                                $value_array->partida_gobierno= ($numero_partida==='01' ? $item->partida : $value_array->partida_gobierno);

                                                                $value_array->partida_privada=($numero_partida==='02' ? $item->partida : $value_array->partida_privada);

                                                                $value_array->procentaje_gobierno   =($numero_partida==='01'?$item->porcentaje_gobierno:$value_array->procentaje_gobierno);
                                                                $value_array->porcentaje_privado    =($numero_partida==='02'?$item->porcentaje_privado:$value_array->porcentaje_privado);
                                                                $value_array->porcentaje_comisiones =$item->porcentaje_comicion;
                                                                $value_array->porcentaje_penalidades =$item->porcentaje_penalidad;
                                                                $partida_encontrada=true;
                                                            }
                                                        }
                                                        if ($partida_encontrada===false) {
                                                            array_push($array_porcentajes,(object)array(
                                                                "partida"=>$partida_padre,
                                                                "partida_gobierno"=>($numero_partida=='01' ? $partida_hijo : ''),
                                                                "partida_privada"=>($numero_partida=='02' ? $partida_hijo : ''),
                                                                "procentaje_gobierno"=>$item->porcentaje_gobierno,
                                                                "porcentaje_privado"=>$item->porcentaje_privado,
                                                                "porcentaje_comisiones"=>$item->porcentaje_comicion,
                                                                "porcentaje_penalidades"=>$item->porcentaje_penalidad
                                                            ));
                                                        }

                                                    } else {

                                                        array_push($array_porcentajes,(object)array(
                                                            "partida"=>$partida_padre,
                                                            "partida_gobierno"=>($numero_partida=='01' ? $partida_hijo : ''),
                                                            "partida_privada"=>($numero_partida=='02' ? $partida_hijo : ''),
                                                            "procentaje_gobierno"=>$item->porcentaje_gobierno,
                                                            "porcentaje_privado"=>$item->porcentaje_privado,
                                                            "porcentaje_comisiones"=>$item->porcentaje_comicion,
                                                            "porcentaje_penalidades"=>$item->porcentaje_penalidad
                                                        ));
                                                    }
                                                }


                                                }
                                            @endphp

                                            <input class="form-control" type="hidden" name="" value="{{$numero_partida}}">
                                            {{-- <input class="form-control" type="hidden" name="" value="{{$partida_hijo}}"> --}}
                                        <input type="hidden" value="{{$item->id_presupuesto_interno_detalle}}" name="ingresos[{{$input_key}}][id_presupuesto_interno_detalle]" class="form-control input-sm">
                                        <tr key="{{$input_key}}" data-nivel="{{sizeof($array)}}" data-partida="{{$item->partida}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}"

                                            {{ (sizeof($array)===2?'class=text-primary':'') }}
                                            {{ ($item->registro==='2'?'class=bg-danger':'') }}
                                            >

                                            <td data-td="partida">

                                                <input type="hidden" value="{{$item->partida}}" name="ingresos[{{$input_key}}][partida]" class="form-control input-sm">

                                                <input type="hidden" value="{{$item->id_hijo}}" name="ingresos[{{$input_key}}][id_hijo]" class="form-control input-sm">
                                                <input type="hidden" value="{{$item->id_padre}}" name="ingresos[{{$input_key}}][id_padre]" class="form-control input-sm">

                                                <input type="hidden" value="{{$item->porcentaje_gobierno}}" name="ingresos[{{$input_key}}][porcentaje_gobierno]" class="form-control input-sm">
                                                <input type="hidden" value="{{$item->porcentaje_privado}}" name="ingresos[{{$input_key}}][porcentaje_privado]" class="form-control input-sm">
                                                <input type="hidden" value="{{$item->porcentaje_comicion}}" name="ingresos[{{$input_key}}][porcentaje_comicion]" class="form-control input-sm">
                                                <input type="hidden" value="{{$item->porcentaje_penalidad}}" name="ingresos[{{$input_key}}][porcentaje_penalidad]" class="form-control input-sm">

                                                <span>{{$item->partida}}</span>
                                            </td>

                                            <td data-td="descripcion">
                                                <input type="hidden" value="{{$item->descripcion}}" class="form-control input-sm" name="ingresos[{{$input_key}}][descripcion]" placeholder="{{$item->descripcion}}"><span>{{$item->descripcion}}</span>
                                            </td>

                                            <td data-td="porcentaje" hidden>
                                                <input type="hidden" value="{{$item->porcentaje_costo}}" name="ingresos[{{$input_key}}][porcentaje_costo]" class="form-control input-sm">
                                            </td>

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
                                                title="ENERO"
                                                >
                                                @if ($item->registro==='1')
                                                <span>{{$item->enero}}</span>
                                                @endif

                                                @if ($presupuesto_interno->estado =='2')
                                                    <label hidden class="total-limite">{{$item->enero}}</label>
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
                                                title="FEBRERO"
                                                >
                                                @if ($item->registro==='1')
                                                <span>{{$item->febrero}}</span>
                                                @endif
                                                @if ($presupuesto_interno->estado =='2')
                                                    <label hidden class="total-limite">{{$item->febrero}}</label>
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
                                                title="MARZO"
                                                >
                                                @if ($item->registro==='1')
                                                <span>{{$item->marzo}}</span>
                                                @endif
                                                @if ($presupuesto_interno->estado =='2')
                                                    <label hidden class="total-limite">{{$item->marzo}}</label>
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
                                                title="ABRIL"
                                                >
                                                @if ($item->registro==='1')
                                                <span>{{$item->abril}}</span>
                                                @endif
                                                @if ($presupuesto_interno->estado =='2')
                                                    <label hidden class="total-limite">{{$item->abril}}</label>
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
                                                title="MAYO"
                                                >
                                                @if ($item->registro==='1')
                                                <span>{{$item->mayo}}</span>
                                                @endif
                                                @if ($presupuesto_interno->estado =='2')
                                                    <label hidden class="total-limite">{{$item->mayo}}</label>
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
                                                title="JUNIO"
                                                >
                                                @if ($item->registro==='1')
                                                <span>{{$item->junio}}</span>
                                                @endif
                                                @if ($presupuesto_interno->estado =='2')
                                                    <label hidden class="total-limite">{{$item->junio}}</label>
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
                                                title="JULIO"
                                                >
                                                @if ($item->registro==='1')
                                                <span>{{$item->julio}}</span>
                                                @endif
                                                @if ($presupuesto_interno->estado =='2')
                                                    <label hidden class="total-limite">{{$item->julio}}</label>
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
                                                title="AGOSTO"
                                                >
                                                @if ($item->registro==='1')
                                                <span>{{$item->agosto}}</span>
                                                @endif
                                                @if ($presupuesto_interno->estado =='2')
                                                    <label hidden class="total-limite">{{$item->agosto}}</label>
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
                                                title="SETIEMBRE"
                                                >
                                                @if ($item->registro==='1')
                                                <span>{{$item->setiembre}}</span>
                                                @endif
                                                @if ($presupuesto_interno->estado =='2')
                                                    <label hidden class="total-limite">{{$item->setiembre}}</label>
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
                                                title="OCTUBRE"
                                                >
                                                @if ($item->registro==='1')
                                                <span>{{$item->octubre}}</span>
                                                @endif
                                                @if ($presupuesto_interno->estado =='2')
                                                    <label hidden class="total-limite">{{$item->octubre}}</label>
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
                                                title="NOVIEMBRE"
                                                >
                                                @if ($item->registro==='1')
                                                <span>{{$item->noviembre}}</span>
                                                @endif
                                                @if ($presupuesto_interno->estado =='2')
                                                    <label hidden class="total-limite">{{$item->noviembre}}</label>
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
                                                title="DICIEMBRE"
                                                >
                                                @if ($item->registro==='1')
                                                <span>{{$item->diciembre}}</span>
                                                @endif
                                                @if ($presupuesto_interno->estado =='2')
                                                    <label hidden class="total-limite">{{$item->diciembre}}</label>
                                                @endif
                                            </td>
                                            <td data-td="accion" {{ ($item->registro==='2') ? '' : 'hidden'  }}>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                        <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                    @if ($item->registro==='1')
                                                        <input type="hidden" name="ingresos[{{$input_key}}][registro]" value="1">
                                                        {{-- <li><a href="#" class="" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-nuevo" data-select="titulo" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="ingresos" title="Agregar titulo" data-tipo="nuevo">Agregar titulo</a></li> --}}

                                                        {{-- <li><a href="#" class="" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-partida" data-select="partida" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="ingresos" title="Agregar partida" data-tipo="nuevo">Agregar partida</a></li> --}}

                                                        {{-- <li>
                                                            <a href="#" class="" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-nuevo" data-select="titulo" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="ingresos" title="Editar" data-tipo="editar">Editar</a>
                                                        </li> --}}
                                                    @endif
                                                    @if ($item->registro==='2')
                                                        <input type="hidden" name="ingresos[{{$input_key}}][registro]" value="2">
                                                        {{-- <li><a href="#" class="" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-partida" data-select="partida" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="ingresos" title="Editar partida" data-tipo="editar">Editar partida</a></li> --}}

                                                        <li><a href="#" class="" key="{{$input_key}}" data-action="click-porcentaje" data-nivel="{{sizeof($array)}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="ingresos" title="Editar porcentaje" data-tipo="editar" data-text-partida="{{$item->partida}}" >Editar porcentaje</a></li>
                                                    @endif

                                                    {{-- @if (sizeof($array)!==1)
                                                        <li><a href="#" class="" data-partida="{{$item->partida}}" key="{{$input_key}}" data-action="click-eliminar" data-nivel="{{sizeof($array)}}" title="Eliminar" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}" data-tipo-text="ingresos">Eliminar</a></li>
                                                    @endif --}}
                                                    </ul>
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
                                                <th class="text-left" width="" >%</th>
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
                                                <th class="text-center" width="10" hidden></th>
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
                                        <input type="hidden" value="{{$item->id_presupuesto_interno_detalle}}" name="costos[{{$input_key}}][id_presupuesto_interno_detalle]" class="form-control input-sm">
                                        <tr key="{{$input_key}}" data-nivel="{{sizeof($array)}}" data-partida="{{$item->partida}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}"
                                            {{ (sizeof($array)===2?'class=text-primary':'') }}
                                            {{ ($item->registro==='2'?'class=bg-danger':'') }}>

                                            <td data-td="partida">
                                                <input type="hidden" value="{{$item->partida}}" name="costos[{{$input_key}}][partida]" class="form-control input-sm">

                                                <input type="hidden" value="{{$item->id_hijo}}" name="costos[{{$input_key}}][id_hijo]" class="form-control input-sm">
                                                <input type="hidden" value="{{$item->id_padre}}" name="costos[{{$input_key}}][id_padre]" class="form-control input-sm">

                                                <input type="hidden" value="{{$item->porcentaje_gobierno}}" name="costos[{{$input_key}}][porcentaje_gobierno]" class="form-control input-sm">
                                                <input type="hidden" value="{{$item->porcentaje_privado}}" name="costos[{{$input_key}}][porcentaje_privado]" class="form-control input-sm">
                                                <input type="hidden" value="{{$item->porcentaje_comicion}}" name="costos[{{$input_key}}][porcentaje_comicion]" class="form-control input-sm">
                                                <input type="hidden" value="{{$item->porcentaje_penalidad}}" name="costos[{{$input_key}}][porcentaje_penalidad]" class="form-control input-sm">

                                                <span>{{$item->partida}}</span>
                                            </td>

                                                {{-- @if (sizeof($array)===3 || sizeof($array)===4) --}}
                                            <td data-td="descripcion">
                                                <input type="hidden" value="{{$item->descripcion}}" class="form-control input-sm" name="costos[{{$input_key}}][descripcion]" placeholder="{{$item->descripcion}}"><span>{{$item->descripcion}}</span>
                                            </td>
                                            <td data-td="porcentaje">
                                                <input type="hidden" value="{{$item->porcentaje_costo}}" name="costos[{{$input_key}}][porcentaje_costo]" class="form-control input-sm">
                                                @if ($item->registro==='2')
                                                    <span>{{$item->porcentaje_costo}}</span>%
                                                @endif

                                            </td>

                                            <td data-td="enero">
                                                <input
                                                type="hidden"
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
                                                title="ENERO"
                                                >
                                                <span>{{$item->enero}}</span>
                                                @if ($presupuesto_interno->estado =='2')
                                                    <label hidden class="total-limite">{{$item->enero}}</label>
                                                @endif
                                            </td>
                                            <td data-td="febrero">
                                                <input
                                                type="hidden"
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
                                                title="FEBRERO"
                                                >
                                                <span>{{$item->febrero}}</span>
                                                @if ($presupuesto_interno->estado =='2')
                                                    <label hidden class="total-limite">{{$item->febrero}}</label>
                                                @endif
                                            </td>

                                            <td data-td="marzo">
                                                <input
                                                type="hidden"
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
                                                title="MARZO"
                                                >
                                                {{-- @if ($item->registro==='1') --}}
                                                <span>{{$item->marzo}}</span>
                                                {{-- @endif --}}
                                                @if ($presupuesto_interno->estado =='2')
                                                    <label hidden class="total-limite">{{$item->marzo}}</label>
                                                @endif
                                            </td>

                                            <td data-td="abril">
                                                <input
                                                type="hidden"
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
                                                title="ABRIL"
                                                >
                                                {{-- @if ($item->registro==='1') --}}
                                                <span>{{$item->abril}}</span>
                                                {{-- @endif --}}
                                                @if ($presupuesto_interno->estado =='2')
                                                    <label hidden class="total-limite">{{$item->abril}}</label>
                                                @endif
                                            </td>

                                            <td data-td="mayo">
                                                <input
                                                type="hidden"
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
                                                title="MAYO"
                                                >
                                                {{-- @if ($item->registro==='1') --}}
                                                <span>{{$item->mayo}}</span>
                                                {{-- @endif --}}
                                                @if ($presupuesto_interno->estado =='2')
                                                    <label hidden class="total-limite">{{$item->mayo}}</label>
                                                @endif
                                            </td>

                                            <td data-td="junio">
                                                <input
                                                type="hidden"
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
                                                title="JUNIO"
                                                >
                                                {{-- @if ($item->registro==='1') --}}
                                                <span>{{$item->junio}}</span>
                                                {{-- @endif --}}
                                                @if ($presupuesto_interno->estado =='2')
                                                    <label hidden class="total-limite">{{$item->junio}}</label>
                                                @endif
                                            </td>

                                            <td data-td="julio">
                                                <input
                                                type="hidden"
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
                                                title="JULIO"
                                                >
                                                {{-- @if ($item->registro==='1') --}}
                                                <span>{{$item->julio}}</span>
                                                {{-- @endif --}}
                                                @if ($presupuesto_interno->estado =='2')
                                                    <label hidden class="total-limite">{{$item->julio}}</label>
                                                @endif
                                            </td>

                                            <td data-td="agosto">
                                                <input
                                                type="hidden"
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
                                                title="AGOSTO"
                                                >
                                                {{-- @if ($item->registro==='1') --}}
                                                <span>{{$item->agosto}}</span>
                                                {{-- @endif --}}
                                                @if ($presupuesto_interno->estado =='2')
                                                    <label hidden class="total-limite">{{$item->agosto}}</label>
                                                @endif
                                            </td>

                                            <td data-td="setiembre">
                                                <input
                                                type="hidden"
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
                                                title="SETIEMBRE"
                                                >
                                                {{-- @if ($item->registro==='1') --}}
                                                <span>{{$item->setiembre}}</span>
                                                {{-- @endif --}}
                                                @if ($presupuesto_interno->estado =='2')
                                                    <label hidden class="total-limite">{{$item->setiembre}}</label>
                                                @endif
                                            </td>

                                            <td data-td="octubre">
                                                <input
                                                type="hidden"
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
                                                title="OCTUBRE"
                                                >
                                                {{-- @if ($item->registro==='1') --}}
                                                <span>{{$item->octubre}}</span>
                                                {{-- @endif --}}
                                                @if ($presupuesto_interno->estado =='2')
                                                    <label hidden class="total-limite">{{$item->octubre}}</label>
                                                @endif
                                            </td>

                                            <td data-td="noviembre">
                                                <input
                                                type="hidden"
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
                                                title="NOVIEMBRE"
                                                >
                                                {{-- @if ($item->registro==='1') --}}
                                                <span>{{$item->noviembre}}</span>
                                                {{-- @endif --}}
                                                @if ($presupuesto_interno->estado =='2')
                                                    <label hidden class="total-limite">{{$item->noviembre}}</label>
                                                @endif
                                            </td>

                                            <td data-td="diciembre">
                                                <input
                                                type="hidden"
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
                                                title="DICIEMBRE"
                                                >
                                                {{-- @if ($item->registro==='1') --}}
                                                <span>{{$item->diciembre}}</span>
                                                {{-- @endif --}}
                                                @if ($presupuesto_interno->estado =='2')
                                                    <label hidden class="total-limite">{{$item->diciembre}}</label>
                                                @endif
                                            </td>

                                                {{-- @else
                                                    <td colspan="2" data-td="descripcion"><input type="hidden" value="{{$item->descripcion}}" class="form-control input-sm" name="costos[{{$input_key}}][descripcion]"><span>{{$item->descripcion}}</span></td>
                                                @endif --}}

                                            <td data-td="accion" hidden>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                    <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu ">
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
                                            <th class="text-left" width="" hidden>%</th>
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
                                            // $array_excluidos = array('03.01.02.01','03.01.02.02','03.01.02.03','03.01.03.01','03.01.03.02','03.01.03.03');
                                            $array_excluidos = array();
                                            $partida_hidden = in_array($item->partida, $array_excluidos);
                                        @endphp
                                    <input type="hidden" value="{{$item->id_presupuesto_interno_detalle}}" name="gastos[{{$input_key}}][id_presupuesto_interno_detalle]" class="form-control input-sm">
                                    <input type="hidden" name="" value="{{$partida_hidden}}">
                                    <tr key="{{$input_key}}" data-nivel="{{sizeof($array)}}" data-partida="{{$item->partida}}" data-id="{{$item->id_hijo}}" data-id-padre="{{$item->id_padre}}"
                                        {{ sizeof($array)===2?'class=text-primary':'' }}
                                        {{ ($item->registro==='2'?'class=bg-danger':'') }}>

                                        <td data-td="partida">
                                            <input type="hidden" value="{{$item->partida}}" name="gastos[{{$input_key}}][partida]" class="form-control input-sm">

                                            <input type="hidden" value="{{$item->id_hijo}}" name="gastos[{{$input_key}}][id_hijo]" class="form-control input-sm">
                                            <input type="hidden" value="{{$item->id_padre}}" name="gastos[{{$input_key}}][id_padre]" class="form-control input-sm">

                                            <input type="hidden" value="{{$item->porcentaje_gobierno}}" name="gastos[{{$input_key}}][porcentaje_gobierno]" class="form-control input-sm">
                                            <input type="hidden" value="{{$item->porcentaje_privado}}" name="gastos[{{$input_key}}][porcentaje_privado]" class="form-control input-sm">
                                            <input type="hidden" value="{{$item->porcentaje_comicion}}" name="gastos[{{$input_key}}][porcentaje_comicion]" class="form-control input-sm">
                                            <input type="hidden" value="{{$item->porcentaje_penalidad}}" name="gastos[{{$input_key}}][porcentaje_penalidad]" class="form-control input-sm">

                                            <span>{{$item->partida}}</span>
                                        </td>

                                        <td data-td="descripcion">
                                            <input type="hidden" value="{{$item->descripcion}}" class="form-control input-sm" name="gastos[{{$input_key}}][descripcion]" placeholder="{{$item->descripcion}}"><span>{{$item->descripcion}}</span>
                                        </td>

                                        <td data-td="porcentaje" hidden>
                                            <input type="hidden" value="{{$item->porcentaje_costo}}" name="gastos[{{$input_key}}][porcentaje_costo]" class="form-control input-sm">
                                        </td>

                                        <td data-td="enero">
                                            <input
                                            type="{{($item->registro==='1' || $partida_hidden==1? 'hidden':'text')}}"
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
                                            title="ENERO"
                                            data-auxiliar-valor="{{$item->enero_aux}}"
                                            data-valor="{{$item->enero}}"
                                            >
                                            @if ($item->registro==='1' || $partida_hidden==1)
                                            <span>{{$item->enero}}</span>
                                            @endif
                                            @if ($presupuesto_interno->estado =='2')
                                                <label hidden class="total-limite">{{$item->enero}}</label>
                                            @endif
                                        </td>
                                        <td data-td="febrero">
                                            <input
                                            type="{{($item->registro==='1' || $partida_hidden==1? 'hidden':'text')}}"
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
                                            title="FEBRERO"
                                            data-auxiliar-valor="{{$item->febrero_aux}}"
                                            data-valor="{{$item->febrero}}"
                                            >
                                            @if ($item->registro==='1' || $partida_hidden==1)
                                            <span>{{$item->febrero}}</span>
                                            @endif
                                            @if ($presupuesto_interno->estado =='2')
                                                <label hidden class="total-limite">{{$item->febrero}}</label>
                                            @endif
                                        </td>

                                        <td data-td="marzo">
                                            <input
                                            type="{{($item->registro==='1' || $partida_hidden==1? 'hidden':'text')}}"
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
                                            title="MARZO"
                                            data-auxiliar-valor="{{$item->marzo_aux}}"
                                            data-valor="{{$item->marzo}}"
                                            >
                                            @if ($item->registro==='1' || $partida_hidden==1)
                                            <span>{{$item->marzo}}</span>
                                            @endif
                                            @if ($presupuesto_interno->estado =='2')
                                                <label hidden class="total-limite">{{$item->marzo}}</label>
                                            @endif
                                        </td>

                                        <td data-td="abril">
                                            <input
                                            type="{{($item->registro==='1' || $partida_hidden==1? 'hidden':'text')}}"
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
                                            title="ABRIL"
                                            data-auxiliar-valor="{{$item->abril_aux}}"
                                            data-valor="{{$item->abril}}"
                                            >
                                            @if ($item->registro==='1' || $partida_hidden==1)
                                            <span>{{$item->abril}}</span>
                                            @endif
                                            @if ($presupuesto_interno->estado =='2')
                                                <label hidden class="total-limite">{{$item->abril}}</label>
                                            @endif
                                        </td>

                                        <td data-td="mayo">
                                            <input
                                            type="{{($item->registro==='1' || $partida_hidden==1? 'hidden':'text')}}"
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
                                            title="MAYO"
                                            data-auxiliar-valor="{{$item->mayo_aux}}"
                                            data-valor="{{$item->mayo}}"
                                            >
                                            @if ($item->registro==='1' || $partida_hidden==1)
                                            <span>{{$item->mayo}}</span>
                                            @endif
                                            @if ($presupuesto_interno->estado =='2')
                                                <label hidden class="total-limite">{{$item->mayo}}</label>
                                            @endif
                                            <label hidden class="total-aux">{{$item->mayo_aux}}</label>
                                        </td>

                                        <td data-td="junio">
                                            <input
                                            type="{{($item->registro==='1' || $partida_hidden==1? 'hidden':'text')}}"
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
                                            title="JUNIO"
                                            data-auxiliar-valor="{{$item->junio_aux}}"
                                            data-valor="{{$item->junio}}"
                                            >
                                            @if ($item->registro==='1' || $partida_hidden==1)
                                            <span>{{$item->junio}}</span>
                                            @endif
                                            @if ($presupuesto_interno->estado =='2')
                                                <label hidden class="total-limite">{{$item->junio}}</label>
                                            @endif
                                        </td>

                                        <td data-td="julio">
                                            <input
                                            type="{{($item->registro==='1' || $partida_hidden==1? 'hidden':'text')}}"
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
                                            title="JULIO"
                                            data-auxiliar-valor="{{$item->julio_aux}}"
                                            data-valor="{{$item->julio}}"
                                            >
                                            @if ($item->registro==='1' || $partida_hidden==1)
                                            <span>{{$item->julio}}</span>
                                            @endif
                                            @if ($presupuesto_interno->estado =='2')
                                                <label hidden class="total-limite">{{$item->julio}}</label>
                                            @endif
                                        </td>

                                        <td data-td="agosto">
                                            <input
                                            type="{{($item->registro==='1' || $partida_hidden==1? 'hidden':'text')}}"
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
                                            title="AGOSTO"
                                            data-auxiliar-valor="{{$item->agosto_aux}}"
                                            data-valor="{{$item->agosto}}"
                                            >
                                            @if ($item->registro==='1' || $partida_hidden==1)
                                            <span>{{$item->agosto}}</span>
                                            @endif
                                            @if ($presupuesto_interno->estado =='2')
                                                <label hidden class="total-limite">{{$item->agosto}}</label>
                                            @endif
                                        </td>

                                        <td data-td="setiembre">
                                            <input
                                            type="{{($item->registro==='1' || $partida_hidden==1? 'hidden':'text')}}"
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
                                            title="SETIEMBRE"
                                            data-auxiliar-valor="{{$item->setiembre_aux}}"
                                            data-valor="{{$item->setiembre}}"
                                            >
                                            @if ($item->registro==='1' || $partida_hidden==1)
                                            <span>{{$item->setiembre}}</span>
                                            @endif
                                            @if ($presupuesto_interno->estado =='2')
                                                <label hidden class="total-limite">{{$item->setiembre}}</label>
                                            @endif
                                        </td>

                                        <td data-td="octubre">
                                            <input
                                            type="{{($item->registro==='1' || $partida_hidden==1? 'hidden':'text')}}"
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
                                            title="OCTUBRE"
                                            data-auxiliar-valor="{{$item->octubre_aux}}"
                                            data-valor="{{$item->octubre}}"
                                            >
                                            @if ($item->registro==='1' || $partida_hidden==1)
                                            <span>{{$item->octubre}}</span>
                                            @endif
                                            @if ($presupuesto_interno->estado =='2')
                                                <label hidden class="total-limite">{{$item->octubre}}</label>
                                            @endif
                                        </td>

                                        <td data-td="noviembre">
                                            <input
                                            type="{{($item->registro==='1' || $partida_hidden==1? 'hidden':'text')}}"
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
                                            title="NOVIEMBRE"
                                            data-auxiliar-valor="{{$item->noviembre_aux}}"
                                            data-valor="{{$item->noviembre}}"
                                            >
                                            @if ($item->registro==='1' || $partida_hidden==1)
                                            <span>{{$item->noviembre}}</span>
                                            @endif
                                            @if ($presupuesto_interno->estado =='2')
                                                <label hidden class="total-limite">{{$item->noviembre}}</label>
                                            @endif
                                        </td>

                                        <td data-td="diciembre">
                                            <input
                                            type="{{($item->registro==='1' || $partida_hidden==1? 'hidden':'text')}}"
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
                                            title="DICIEMBRE"
                                            data-auxiliar-valor="{{$item->diciembre_aux}}"
                                            data-valor="{{$item->diciembre}}"
                                            >
                                            @if ($item->registro==='1' || $partida_hidden==1)
                                            <span>{{$item->diciembre}}</span>
                                            @endif
                                            @if ($presupuesto_interno->estado =='2')
                                                <label hidden class="total-limite">{{$item->diciembre}}</label>
                                            @endif
                                        </td>

                                        <td data-td="accion" hidden>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                    <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right">
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
    <div id="modal-costos" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <form action="" method="post" data-form="guardar-costos-modal">
                    <div class="modal-header">
                        {{-- <button class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button> --}}
                        <h5 class="modal-title" id="my-modal-title">Ingrese porcentajes</h5>

                    </div>
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        {{-- <button class="btn btn-light" data-dismiss="modal" type="button"><i class="fa fa-times"></i> CERRAR</button> --}}
                        <button class="btn btn-success" type="submit"><i class="fa fa-save"></i> Guardar</button>
                    </div>
                </form>
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
    @if (in_array(298,$array_accesos))
    <script>
        let array = {!! json_encode($array_porcentajes) !!};
        // $elemento_array
        $(document).ready(function () {
            $('select[name="mes"] option[value="'+"{{$presupuesto_interno->mes}}"+'"]').attr('selected',true);


        });

        // window.onscroll = function() {
        //     var y = window.scrollY;
        //     var position = $('table#partida-ingresos').position();
        //     var top = position.top;
        //     var left = position.left;
        //     // console.log(y);
        //     console.log(top);



        // };

    </script>

    <script src="{{asset('js/finanzas/presupuesto_interno/crear.js') }}"></script>
    @endif
@endsection
