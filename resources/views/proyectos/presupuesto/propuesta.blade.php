@extends('layout.main')
@include('layout.menu_proyectos')
@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
Propuesta Cliente
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('proyectos.index')}}"><i class="fas fa-tachometer-alt"></i> Proyectos</a></li>
  <li>Propuestas</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="propuesta">
    <div class="thumbnail" style="padding-left: 10px;padding-right: 10px;">
    <legend class="mylegend">
        <h2>Datos Generales</h2>
        <ol class="breadcrumb" style="background-color: white;">
            <li><label id="codigo"></label></li>
            <li><label id="cod_presint"></label></li>
            <li><label>Estado:  <span id="des_estado"></span></h5></li>
            <li><i id="cronograma" class="fas fa-calendar-alt blue" id="basic-addon2" 
                data-toggle="tooltip" data-placement="bottom" title="Cronograma generado" ></i></li>
            <li><i id="cronoval" class="fas fa-donate green" id="basic-addon2" 
                data-toggle="tooltip" data-placement="bottom" title="Cronograma Valorizado generado" ></i></li>
        </ol>
    </legend>
    <form id="form-propuesta" type="register" form="formulario">
        <div class="row">
            <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
            <input type="text" class="oculto" name="id_presup" primary="ids">
            {{-- <input type="text" class="oculto" name="id_empresa"> --}}
            {{-- 3 Propuesta Cliente --}}
            <input type="text" class="oculto" name="tp_presup" value="3">
            <input type="text" class="oculto" name="id_grupo" value="5">
            <label id="estado" class='oculto'></label>
            {{-- <input type="text" class="oculto" name="elaborado_por"> --}}
            <div class="col-md-7">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Seleccione Opcion Comercial</h5>
                        <div class="input-group-okc">
                            <input class="oculto" name="id_op_com" >
                            <input type="text" class="form-control" aria-describedby="basic-addon2" 
                                readonly name="nombre_opcion">
                            <div class="input-group-append">
                                <button type="button" class="input-group-text activation" id="basic-addon2"
                                    onClick="open_opcion_modal();">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <h5>Responsable de la Cotización</h5>
                        <select class="form-control activation js-example-basic-single" name="responsable" disabled="true">
                            <option value="0">Elija una opción</option>
                            @foreach ($usuarios as $usu)
                                <option value="{{$usu->id_usuario}}">{{$usu->nombre_corto}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <h5>Fecha Emisión</h5>
                        <input type="date" name="fecha_emision" class="form-control activation" value="<?=date('Y-m-d');?>" disabled="true"/>
                    </div>
                    <div class="col-md-2">
                        <h5>Moneda</h5>
                        <select class="form-control activation" name="moneda" disabled="true">
                            <option value="0">Elija una opción</option>
                            @foreach ($monedas as $mon)
                                <option value="{{$mon->id_moneda}}">{{$mon->descripcion}} - {{$mon->simbolo}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <h5 name="cod_presint">SubTotal Pres.Int.</h5>
                        <div style="display:flex;">
                            <input type="text" name="id_presupuesto" class="oculto"/>
                            <input type="number" name="sub_total_presint" class="form-control right" readonly/>
                            {{-- <button type="button" class="input-group-text btn-success" id="basic-addon2" 
                                data-toggle="tooltip" data-placement="bottom" title="Copiar partidas del Presupuesto Interno" 
                                onClick="copiar_partidas_presint();">
                                <i class="fas fa-copy"></i>
                            </button> --}}
                        </div> 
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <table class="tabla-totales" width="100%">
                    <tbody>
                        <tr>
                            <td width="50%">SubTotal</td>
                            <td width="20%" class="right"><label name="simbolo"></label></td>
                            <td><input type="number" class="importe" name="sub_total" disabled="true" value="0"/></td>
                        </tr>
                        <tr>
                            <td>Utilidad</td>
                            <td>
                                <input type="number" class="porcen activation" name="porcen_utilidad" disabled="true" 
                                    onChange="change_utilidad();" value="0"/>
                                <label>%</label>
                            </td>
                            <td><input type="number" class="importe activation" name="impor_utilidad" disabled="true" 
                                    onChange="change_importe_utilidad();" value="0"/></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td width="50%">Total</td>
                            <td width="20%" class="right"><label name="simbolo"></label></td>
                            <td><input type="number" class="importe" name="total" disabled="true" value="0"/></td>
                        </tr>
                        <tr>
                            <td style="border-top:0px;">IGV</td>
                            <td style="border-top:0px;">
                                <input type="number" class="porcen" name="porcen_igv" disabled="true" value="0"/>
                                <label>%</label>
                            </td>
                            <td style="border-top:0px;"><input type="number" class="importe" name="importe_igv" disabled="true" value="0"/></td>
                        </tr>
                        <tr>
                            <td><strong>Total Presupuestado</strong></td>
                            <td class="right"><label name="simbolo"></label></td>
                            <td><input type="number" class="importe" name="total_propuesta" disabled="true" value="0"/></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </form>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div id="tab-propuesta">
                <table class="mytable table table-condensed table-bordered table-okc-view" width="100%"
                    id="listaPresupuesto">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th width="10%">Und.</th>
                            <th width="5%">Metrado</th>
                            <th width="5%">Unitario</th>
                            <th width="5%">Sub Total</th>
                            <th width="5%">% Utilidad</th>
                            <th width="5%">Importe Uti.</th>
                            <th width="10%">
                                <i class="fas fa-plus-square icon-tabla  boton" 
                                    data-toggle="tooltip" data-placement="bottom" 
                                    title="Agregar Título" onClick="agregar_primer_titulo();"></i>
                                <i class="fas fa-file-excel icon-tabla green boton"
                                    data-toggle="tooltip" data-placement="bottom" 
                                    title="Exportar a Excel" onClick="exportar_propuesta();"></i>
                                <i class="fas fa-sync-alt icon-tabla orange boton" 
                                    data-toggle="tooltip" data-placement="bottom" 
                                    title="Refrescar Partidas" onClick="refresh_partidas();"></i>
                            </th>
                            <th hidden>padre</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@include('proyectos.presupuesto.propuestaModal')
@include('proyectos.opcion.opcionModal')
@include('proyectos.presupuesto.presLeccion')
@include('proyectos.presEstructura.pardetModal')
@include('proyectos.presupuesto.propuestaParObs')
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
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>
    
    <script src="{{ asset('js/proyectos/presupuesto/propuestaModal.js')}}"></script>
    <script src="{{ asset('js/proyectos/presupuesto/propuesta.js')}}"></script>
    <script src="{{ asset('js/proyectos/presupuesto/titulos.js')}}"></script>
    <script src="{{ asset('js/proyectos/opcion/opcionModal.js')}}"></script>
    <script src="{{ asset('js/proyectos/presupuesto/presLeccion.js')}}"></script>
    <script src="{{ asset('js/proyectos/presupuesto/pardetModal.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
    </script>
@endsection
