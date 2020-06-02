@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body')
<div class="page-main" type="transformacion">
    <legend class="mylegend">
        <h2 id="titulo">Hoja de Transformación</h2>
        <ol class="breadcrumb">
            {{-- <li><label id="tp_doc_almacen"></label> - <label id="serie"></label> - <label id="numero"></label></li> --}}
            <li><label id="codigo_transformacion"></label>
                <button type="submit" class="btn btn-success" onClick="procesar_transformacion();">
                    Procesar Transformación </button>
                <button type="button" class="btn btn-warning" data-toggle="tooltip" 
                    data-placement="bottom" title="Ver Salida de Almacén" 
                    onClick="abrir_salida();"><i class="fas fa-file-alt"></i></button>
                <button type="button" class="btn btn-info" data-toggle="tooltip" 
                    data-placement="bottom" title="Ver Ingreso a Almacén" 
                    onClick="abrir_ingreso();"><i class="fas fa-file-alt"></i></button>
            </li>
        </ol>
    </legend>
    <div class="row">
    <form id="form-transformacion" type="register"  form="formulario">
        <div class="col-md-7">
            <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
            <input type="hidden" name="id_transformacion" primary="ids">
            <div class="row">
                <div class="col-md-6">
                    <h5>Empresa</h5>
                    <select class="form-control activation js-example-basic-single" name="id_empresa" disabled="true">
                        <option value="0">Elija una opción</option>
                        @foreach ($empresas as $emp)
                            <option value="{{$emp->id_empresa}}">{{$emp->razon_social}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <h5>Serie-Número</h5>
                    <div class="input-group">
                        <input type="text" class="form-control activation" name="serie" 
                            placeholder="000" >
                        <span class="input-group-addon">-</span>
                        <input type="text" class="form-control activation" name="numero"
                            placeholder="000000" onBlur="ceros_numero('numero');">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h5>Almacén</h5>
                    <select class="form-control activation js-example-basic-single" name="id_almacen" disabled="true">
                        <option value="0">Elija una opción</option>
                        @foreach ($almacenes as $alm)
                            <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
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
                <div class="col-md-6">
                    <h5>Fecha de Transformación</h5>
                    <input type="date" class="form-control activation" name="fecha_transformacion" value="<?=date('Y-m-d');?>" >
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <h5 id="fecha_registro">Fecha Registro: <label></label></h5>
                </div>
                <div class="col-md-4">
                    <h5 id="registrado_por">Registrado por: <label></label></h5>
                </div>
                <div class="col-md-3">
                    <input type="text" name="cod_estado" hidden/>
                    <h5 id="estado">Estado: <label></label></h5>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <table class="tabla-totales mytable table table-condensed table-bordered table-okc-view" width="100%">
                <thead>
                    <tr>
                        <th colSpan="3" style="text-align:center;">Resumen Contable</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width="50%">Total Materias Primas</td>
                        <td width="20%"></td>
                        <td><input type="number" class="importe" name="total_materias" disabled="true" value="0"/></td>
                    </tr>
                    <tr>
                        <td>Total Servicios Directos</td>
                        <td></td>
                        <td><input type="number" class="importe" name="total_directos" disabled="true" value="0"/></td>
                    </tr>
                    <tr>
                        <td><strong>Costo Primo</strong></td>
                        <td></td>
                        <td><input type="number" class="importe" name="costo_primo" disabled="true" value="0"/></td>
                    </tr>
                    <tr>
                        <td>Total Costos Indirectos</td>
                        <td></td>
                        <td><input type="number" class="importe" name="total_indirectos" disabled="true" value="0"/></td>
                    </tr>
                    <tr>
                        <td>Total Sobrantes</td>
                        <td></td>
                        <td><input type="number" class="importe" name="total_sobrantes" disabled="true" value="0"/></td>
                    </tr>
                    <tr>
                        <td><strong>Costo de Transformación</strong></td>
                        <td></td>
                        <td><input type="number" class="importe" name="costo_transformacion" disabled="true" value="0"/></td>
                    </tr>
                    {{-- <tr>
                        <td>Costo por unidad</td>
                        <td></td>
                        <td><input type="number" class="importe" name="costo_unitario" disabled="true" value="0"/></td>
                    </tr> --}}
                </tbody>
            </table>
        </div>
    </form>
    </div>
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
                <table id="listaMateriasPrimas" class="mytable table table-condensed table-bordered table-okc-view">
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
                <table id="listaServiciosDirectos" class="mytable table table-condensed table-bordered table-okc-view" width="100%">
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
                <table id="listaCostosIndirectos" class="mytable table table-condensed table-bordered table-okc-view" width="100%">
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
                <table id="listaSobrantes" class="mytable table table-condensed table-bordered table-okc-view" width="100%">
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
                <table id="listaProductoTransformado" class="mytable table table-condensed table-bordered table-okc-view" width="100%">
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
@include('almacen.guias.transformacionModal')
@include('almacen.producto.productoModal')
@include('logistica.servicioModal')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/transformacion/transformacion.js')}}"></script>
<script src="{{('/js/almacen/transformacion/transformacionModal.js')}}"></script>
<script src="{{('/js/almacen/producto/productoModal.js')}}"></script>
<script src="{{('/js/almacen/transformacion/transfor_materia.js')}}"></script>
<script src="{{('/js/almacen/transformacion/transfor_directo.js')}}"></script>
<script src="{{('/js/almacen/transformacion/transfor_indirecto.js')}}"></script>
<script src="{{('/js/almacen/transformacion/transfor_sobrante.js')}}"></script>
<script src="{{('/js/almacen/transformacion/transfor_transformado.js')}}"></script>
<script src="{{('/js/logistica/servicioModal.js')}}"></script>
@include('layout.fin_html')