<div class="modal fade" tabindex="-1" role="dialog" id="modal-guia_create">
    <div class="modal-dialog">
        <div class="modal-content" style="width:600px;">
            <form id="form-guia_create">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Generar Guía de Compra</h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_orden_compra">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Serie-Número</h5>
                            <div class="input-group">
                                <input type="text" class="form-control activation" 
                                    name="serie" onBlur="ceros_numero('serie');" placeholder="0000">
                                <span class="input-group-addon">-</span>
                                <input type="text" class="form-control activation" 
                                    name="numero" onBlur="ceros_numero('numero');" placeholder="0000000">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Fecha de Emisión</h5>
                            <input type="date" class="form-control activation" name="fecha_emision" value="<?=date('Y-m-d');?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Almacén</h5>
                            <select class="form-control activation js-example-basic-single" name="id_almacen">
                                <option value="0">Elija una opción</option>
                                @foreach ($almacenes as $alm)
                                    <option value="{{$alm->id_almacen}}">{{$alm->codigo}} - {{$alm->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <h5>Fecha de Almacén</h5>
                            <input type="date" class="form-control activation" name="fecha_almacen" value="<?=date('Y-m-d');?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Tipo de Operación</h5>
                            <select class="form-control activation js-example-basic-single" name="id_operacion">
                                <option value="0">Elija una opción</option>
                                @foreach ($tp_operacion as $tp)
                                    <option value="{{$tp->id_operacion}}">{{$tp->cod_sunat}} - {{$tp->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <h5>Clasif. de los Bienes y Servicios</h5>
                            <select class="form-control activation" name="id_guia_clas">
                                <option value="0">Elija una opción</option>
                                @foreach ($clasificaciones as $clas)
                                    <option value="{{$clas->id_clasificacion}}">{{$clas->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                <input type="submit" id="submit_guia" class="btn btn-success" value="Guardar"/>
                    <!-- <label id="mid_doc_com" style="display: none;"></label>
                    <button class="btn btn-sm btn-success" onClick="guardar_guia_create();">Guardar</button> -->
                </div>
            </form>
        </div>
    </div>
</div>