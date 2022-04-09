<div class="modal fade" tabindex="-1" role="dialog" id="modal-mapeoAsignarProducto" style="overflow-y: scroll;">
    <div class="modal-dialog" style="width:800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Asignar Producto</h3>
            </div>
            <div class="modal-body">
                <input type="text" class="oculto" name="id_detalle_requerimiento">
                <div class="row">
                    <div class="col-md-12">
                        <label>Part Number: </label>
                        <span id="part_number"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label>Descripción: </label>
                        <span id="descripcion_producto"></span>
                    </div>
                </div>
                </br>
                <div class="row">
                    <div class="col-md-12" id="tab-productos">

                        <ul class="nav nav-tabs" id="myTab">
                            <li class="active"><a data-toggle="tab" href="#seleccionar">Seleccionar</a></li>
                            <li class=""><a data-toggle="tab" href="#crear">Crear</a></li>
                        </ul>

                        <div class="tab-content">

                            <div id="seleccionar" class="tab-pane fade in active">

                                <form id="form-seleccionar" type="register">

                                    <div class="row">
                                        <div class="col-md-12" style="padding-top:10px;padding-bottom:10px;padding-right: 30px;padding-left: 30px;">

                                            <div style="font-size:18px"><label>Productos sugeridos</label></div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <table class="mytable table table-condensed table-striped table-hover table-bordered table-okc-view" style="font-size:0.8em;" width="100%" id="productosSugeridos">
                                                        <thead>
                                                            <tr>
                                                                <th>Cód. producto</th>
                                                                <th>Cód. Soflink</th>
                                                                <th>PartNumber</th>
                                                                <th>Marca</th>
                                                                <th style="width: 70%;">Descripción</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                <table class="mytable table table-condensed table-striped table-hover table-bordered table-okc-view" style="font-size:0.8em;" width="100%" id="productosCatalogo">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 5%">#</th>
                                                                <th style="width: 5%">Cód. producto</th>
                                                                <th style="width: 5%">Cód. Soflink</th>
                                                                <th style="width: 5%">PartNumber</th>
                                                                <th style="width: 5%">Marca</th>
                                                                <th style="width: 70%;">Descripción</th>
                                                                <th style="width: 5%"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div id="crear" class="tab-pane fade">

                                <form id="form-crear" type="register">
                                    <div class="row">
                                        <div class="col-md-12" style="padding-top:10px;padding-bottom:10px;padding-right: 30px;padding-left: 30px;">

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <h5>SubCategoría</h5>
                                                    <select class="form-control activation js-example-basic-single" name="id_categoria" required>
                                                        <!-- <option value="0">Elija una opción</option> -->
                                                        @foreach ($categorias as $cat)
                                                        <option value="{{$cat->id_categoria}}">{{$cat->descripcion}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            
                                           
                                                <div class="col-md-3">
                                                    <h5>Marca</h5>
                                                    <select class="form-control activation js-example-basic-single" name="id_subcategoria" required>
                                                        <!-- <option value="0">Elija una opción</option> -->
                                                        @foreach ($subcategorias as $subcat)
                                                        <option value="{{$subcat->id_subcategoria}}">{{$subcat->descripcion}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <h5>Part Number</h5>
                                                    <input type="text" class="form-control activation" name="part_number">
                                                </div>
                                                <div class="col-md-2">
                                                    <h5>Unidad Medida</h5>
                                                    <select class="form-control activation " name="id_unidad_medida" required>
                                                        <!-- <option value="0">Elija una opción</option> -->
                                                        @foreach ($unidades as $unid)
                                                        <option value="{{$unid->id_unidad_medida}}">{{$unid->abreviatura}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <h5>Moneda</h5>
                                                    <select class="form-control activation " name="id_moneda_producto" required>
                                                        <option value="">Elija una opción</option>
                                                        @foreach ($monedas as $mnd)
                                                        <option value="{{$mnd->id_moneda}}">{{$mnd->descripcion}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <h5></h5>
                                                    <input type="checkbox" class="flat-red" name="series" style="padding-left: 0px;" />
                                                    Control de series
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4" style="display:none;">
                                                    <h5>Clasificación</h5>
                                                    <select class="form-control activation js-example-basic-single" name="id_clasif" >
                                                        <!-- <option value="0">Elija una opción</option> -->
                                                        @foreach ($clasificaciones as $clasif)
                                                        <option value="{{$clasif->id_clasificacion}}">{{$clasif->descripcion}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4" style="display:none;">
                                                    <h5>Categoría</h5>
                                                    <select class="form-control activation js-example-basic-single" name="id_tipo_producto" >
                                                        <!-- <option value="0">Elija una opción</option> -->
                                                        @foreach ($tipos as $cat)
                                                        <option value="{{$cat->id_tipo_producto}}">{{$cat->descripcion}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h5>Descripción</h5>
                                                    <textarea name="descripcion" class="form-control activation" id="descripcion" cols="50" rows="5" required></textarea>
                                                </div>
                                            </div>
                                            <br />
                                            <div class="row">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-4">
                                                    <input type="submit" id="submit_crear" class="btn btn-success btn-block" value="Seleccionar y Crear" />
                                                    <!-- <button class="btn btn-sm btn-success btn-block" onClick="crearProducto();">Seleccionar y Crear</button> -->
                                                </div>
                                                <div class="col-md-4"></div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-primary" class="close" data-dismiss="modal" >Cerrar</button>
            </div>
        </div>
    </div>
</div>