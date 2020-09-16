@extends('layout.main')
@include('layout.menu_logistica')

@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
Hoja de Transformación
@endsection

@section('estilos')
<link rel="stylesheet" href="{{ asset('template/plugins/select2/select2.css') }}">
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('logistica.index')}}"><i class="fas fa-tachometer-alt"></i> Logística y Almacenes</a></li>
  <li>Customización</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')

<div class="page-main" type="transformacion">
    <!-- <div class="row"> -->
    <form id="form-transformacion" type="register"  form="formulario">
        <div class="thumbnail" style="padding-left: 10px;padding-right: 10px;">
            
            <div class="row"  style="padding-left: 10px;padding-right: 10px;margin-bottom: 0px;">
                <div class="col-md-10">
                    <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                    <input type="hidden" name="id_transformacion" primary="ids">
                    <input type="text" name="cod_estado" hidden/>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Código</h5>
                            <input type="text" name="codigo" class="form-control" readOnly/>
                        </div>
                        <div class="col-md-3">
                            <h5>Almacén</h5>
                            <select class="form-control activation js-example-basic-single" name="id_almacen" disabled="true">
                                <option value="0">Elija una opción</option>
                                @foreach ($almacenes as $alm)
                                    <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Fecha Transformación</h5>
                            <input type="date" class="form-control activation" name="fecha_transformacion"/>
                        </div>
                        <!-- <div class="col-md-3">
                            <h5>Serie-Número</h5>
                            <div class="input-group">
                                <input type="text" class="form-control activation" name="serie" 
                                    placeholder="000" >
                                <span class="input-group-addon">-</span>
                                <input type="text" class="form-control activation" name="numero"
                                    placeholder="000000" onBlur="ceros_numero('numero');">
                            </div>
                        </div> -->
                        <div class="col-md-3">
                            <h5>Responsable</h5>
                            <select class="form-control activation js-example-basic-single" 
                                name="responsable" disabled="true">
                                <option value="0">Elija una opción</option>
                                @foreach ($usuarios as $usu)
                                    <option value="{{$usu->id_usuario}}">{{$usu->nombre_corto}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- <h5>Observación</h5> -->
                            <textarea name="observacion" id="observacion" cols="150" rows="3"></textarea>
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="col-md-5">
                            <h5 id="fecha_registro">Fecha Registro: <label></label></h5>
                        </div>
                        <div class="col-md-4">
                            <h5 id="registrado_por">Registrado por: <label></label></h5>
                        </div>
                        <div class="col-md-3">
                            
                            <h5 id="estado">Estado: <label></label></h5>
                        </div>
                    </div> -->
                </div>
                <div class="col-md-2">
                    <div class="row"  style="padding-left: 10px;padding-right: 0px;padding-top: 15px;">
                        <div class="col-md-10" style="text-align:right;">
                            <button type="submit" class="btn btn-success btn-sm" onClick="procesar_transformacion();" data-toggle="tooltip" data-placement="bottom" title="Procesar Transformación">
                            <i class="fas fa-step-forward"></i> </button>
                            <button type="button" class="btn btn-warning btn-sm" data-toggle="tooltip" 
                                data-placement="bottom" title="Ver Salida de Almacén" 
                                onClick="abrir_salida();">S</button>
                            <button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" 
                                data-placement="bottom" title="Ver Ingreso a Almacén" 
                                onClick="abrir_ingreso();">I</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- </div> -->
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingOne">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                Materias Primas
                </a>
            </h4>
            </div>
            <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
            <div class="panel-body">
                <table id="listaMateriasPrimas" class="mytable table table-condensed table-bordered table-okc-view" style="margin-bottom: 0px;">
                    <thead>
                        <tr>
                            <th width='5%'>Nro</th>
                            <th width='10%'>Código</th>
                            <th width='40%'>Descripción</th>
                            <th width='10%'>Cant.</th>
                            <th>Unid.</th>
                            <th>Unit.</th>
                            <th>Total</th>
                            <th width='5%'>
                                <i class="fas fa-plus-square icon-tabla green boton" 
                                    data-toggle="tooltip" data-placement="bottom" 
                                    title="Agregar Producto" onClick="productoModal();"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <td colSpan="6"></td>
                            <td><input type="number" class="input-data right" name="total_materias" disabled="true"/></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8"></div>
            <div class="col-md-4">
                <table class="table table-condensed " width="100%">
                    <tbody>
                        <tr>
                            <td width="50%" style="text-align: right;">Total Materias Primas</td>
                            <td width="10%"></td>
                            <td><label name="total_materias">0.00</label></td>
                            
                        </tr>
                        <tr>
                            <td style="text-align: right;">Total Servicios Directos</td>
                            <td width="10%"></td>
                            <td><label name="total_directos">0.00</label></td>
                            
                        </tr>
                        <tr>
                            <td style="text-align: right;"><strong>Costo Primo</strong></td>
                            <td width="10%"></td>
                            <td><label name="costo_primo">0.00</label></td>
                            
                        </tr>
                        <tr>
                            <td style="text-align: right;">Total Costos Indirectos</td>
                            <td width="10%"></td>
                            <td><label name="total_indirectos">0.00</label></td>
                            
                        </tr>
                        <tr>
                            <td style="text-align: right;">Total Sobrantes</td>
                            <td width="10%"></td>
                            <td><label name="total_sobrantes">0.00</label></td>
                            
                        </tr>
                        <tr>
                            <td style="text-align: right;"><strong>Costo de Transformación</strong></td>
                            <td width="10%"></td>
                            <td><label name="costo_transformacion">0.00</label></td>
                            
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingTwo">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                Servicios Directos
                </a>
            </h4>
            </div>
            <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
            <div class="panel-body">
                <table id="listaServiciosDirectos" class="mytable table table-condensed table-bordered table-okc-view" width="100%" style="margin-bottom: 0px;">
                    <thead>
                        <tr>
                            <th width='5%'>Nro</th>
                            <th width='10%'>Código</th>
                            <th width='40%'>Descripción</th>
                            <th width='10%'>Cant.</th>
                            <th>Unit.</th>
                            <th>Total</th>
                            <th width='5%'>
                                <i class="fas fa-plus-square icon-tabla green boton" 
                                    data-toggle="tooltip" data-placement="bottom" 
                                    title="Agregar Servicio" onClick="servicioModal();"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <td colSpan="5"></td>
                            <td><input type="number" class="input-data right" name="total_directos" disabled="true"/></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingThree">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                Costos Indirectos
                </a>
            </h4>
            </div>
            <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
            <div class="panel-body">
                <table id="listaCostosIndirectos" class="mytable table table-condensed table-bordered table-okc-view" width="100%" style="margin-bottom: 0px;">
                    <thead>
                        <tr>
                            <th width='5%'>Nro</th>
                            <th width='10%'>Código</th>
                            <th width='40%'>Descripción</th>
                            <th>Tasa(%)</th>
                            <th>Parámetro</th>
                            <th>Unit.</th>
                            <th>Total</th>
                            <th width='5%'>
                                <i class="fas fa-plus-square icon-tabla green boton" 
                                    data-toggle="tooltip" data-placement="bottom" 
                                    title="Agregar Servicio" onClick="servicioModal();"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <td colSpan="6"></td>
                            <td><input type="number" class="input-data right" name="total_indirectos" disabled="true"/></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingFour">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                Sobrantes
                </a>
            </h4>
            </div>
            <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
            <div class="panel-body">
                <table id="listaSobrantes" class="mytable table table-condensed table-bordered table-okc-view" width="100%" style="margin-bottom: 0px;">
                    <thead>
                        <tr>
                            <th width='5%'>Nro</th>
                            <th width='10%'>Código</th>
                            <th width='40%'>Descripción</th>
                            <th width='10%'>Cant.</th>
                            <th>Unid.</th>
                            <th>Unit.</th>
                            <th>Total</th>
                            <th width='5%'>
                                <i class="fas fa-plus-square icon-tabla green boton" 
                                    data-toggle="tooltip" data-placement="bottom" 
                                    title="Agregar Producto" onClick="productoModal();"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <td colSpan="6"></td>
                            <td><input type="number" class="input-data right" name="total_sobrantes" disabled="true"/></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingFive">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                Productos Transformados
                </a>
            </h4>
            </div>
            <div id="collapseFive" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
            <div class="panel-body">
                <table id="listaProductoTransformado" class="mytable table table-condensed table-bordered table-okc-view" width="100%" style="margin-bottom: 0px;">
                    <thead>
                        <tr>
                            <th width='5%'>Nro</th>
                            <th width='10%'>Código</th>
                            <th width='40%'>Descripción</th>
                            <th width='10%'>Cant.</th>
                            <th>Unid.</th>
                            <th>Unit.</th>
                            <th>Total</th>
                            <th width='5%'>
                                <i class="fas fa-plus-square icon-tabla green boton" 
                                    data-toggle="tooltip" data-placement="bottom" 
                                    title="Agregar Producto" onClick="productoModal();"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <td colSpan="6"></td>
                            <td><input type="number" class="input-data right" name="total_transformado" disabled="true"/></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            </div>
        </div>
    </div>
</div>
@include('almacen.customizacion.transformacionModal')
@include('almacen.producto.productoModal')
@include('logistica.servicioModal')
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
    <script src="{{ asset('template/plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>

    <script src="{{('/js/almacen/customizacion/transformacion.js')}}"></script>
    <script src="{{('/js/almacen/customizacion/transformacionModal.js')}}"></script>
    <script src="{{('/js/almacen/producto/productoModal.js')}}"></script>
    <script src="{{('/js/almacen/customizacion/transfor_materia.js')}}"></script>
    <script src="{{('/js/almacen/customizacion/transfor_directo.js')}}"></script>
    <script src="{{('/js/almacen/customizacion/transfor_indirecto.js')}}"></script>
    <script src="{{('/js/almacen/customizacion/transfor_sobrante.js')}}"></script>
    <script src="{{('/js/almacen/customizacion/transfor_transformado.js')}}"></script>
    <script src="{{('/js/logistica/servicioModal.js')}}"></script>
@endsection