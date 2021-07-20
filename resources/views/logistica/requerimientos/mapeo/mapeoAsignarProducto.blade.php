<div class="modal fade" tabindex="-1" role="dialog" id="modal-mapeoAsignarProducto"  style="overflow-y: scroll;">
    <div class="modal-dialog" style="width:800px;">
        <div class="modal-content" >
            <!-- <form id="form-mapeoAsignarProducto"> -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Asignar Producto</h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_detalle_requerimiento">
                    <div class="row">
                        <div class="col-md-12">
                            <span>Part Number: </span>
                            <label id="part_number"></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <span>Descripción: </span>
                            <label id="descripcion"></label>
                        </div>
                    </div>
                    </br>

                    <div class="col-md-12" id="tab-productos" style="padding-left:0px;padding-right:0px;">
                        
                        <ul class="nav nav-tabs" id="myTab">
                            <li class="active"><a data-toggle="tab" href="#seleccionar">Seleccionar</a></li>
                            <li class=""><a data-toggle="tab" href="#crear">Crear</a></li>
                        </ul>

                        <div class="tab-content">

                            <div id="seleccionar" class="tab-pane fade in active" >
                            
                                <form id="form-seleccionar" type="register">
                                    
                                    <div class="row">
                                        <div class="col-md-12" style="padding-top:10px;padding-bottom:10px;padding-right: 30px;padding-left: 30px;">

                                            <div style="text-align:center;font-size:18px"><label>Productos sugeridos</label></div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                                                        id="productosSugeridos" >
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>PartNumber</th>
                                                                <th>Marca</th>
                                                                <th width: "90%">Descripción</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div style="text-align:center;font-size:18px"><label>Catálogo de productos</label></div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                                                        id="productosCatalogo" >
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>PartNumber</th>
                                                                <th>Marca</th>
                                                                <th width: "90%">Descripción</th>
                                                                <th></th>
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

                            <div id="crear" class="tab-pane fade" >

                                <form id="form-crear" type="register">
                                    <div class="row">
                                        <div class="col-md-12" style="padding-top:10px;padding-bottom:10px;padding-right: 30px;padding-left: 30px;">

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <h5>Categoría</h5>
                                                    <select class="form-control activation js-example-basic-single" name="id_tipo_producto" required>
                                                        <!-- <option value="0">Elija una opción</option> -->
                                                        @foreach ($tipos as $cat)
                                                            <option value="{{$cat->id_tipo_producto}}">{{$cat->descripcion}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <h5>SubCategoría</h5>
                                                    <select class="form-control activation js-example-basic-single" name="id_categoria" required>
                                                        <!-- <option value="0">Elija una opción</option> -->
                                                        @foreach ($categorias as $cat)
                                                            <option value="{{$cat->id_categoria}}">{{$cat->descripcion}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <h5>Marca</h5>
                                                    <select class="form-control activation js-example-basic-single" name="id_subcategoria" required>
                                                        <!-- <option value="0">Elija una opción</option> -->
                                                        @foreach ($subcategorias as $subcat)
                                                            <option value="{{$subcat->id_subcategoria}}">{{$subcat->descripcion}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <h5>Clasificación</h5>
                                                    <select class="form-control activation js-example-basic-single" name="id_clasif" required>
                                                        <!-- <option value="0">Elija una opción</option> -->
                                                        @foreach ($clasificaciones as $clasif)
                                                            <option value="{{$clasif->id_clasificacion}}">{{$clasif->descripcion}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <h5>Part Number</h5>
                                                    <input type="text" class="form-control activation" name="part_number" >
                                                </div>
                                                <div class="col-md-4">
                                                    <h5>Unidad Medida</h5>
                                                    <select class="form-control activation " name="id_unidad_medida" required>
                                                        <!-- <option value="0">Elija una opción</option> -->
                                                        @foreach ($unidades as $unid)
                                                            <option value="{{$unid->id_unidad_medida}}">{{$unid->descripcion}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h5>Descripción</h5>
                                                    <textarea name="descripcion" class="form-control activation" id="descripcion" 
                                                        cols="50" rows="5" required></textarea>
                                                </div>
                                            </div>
                                            <br/>
                                            <div class="row">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-4">
                                                    <input type="submit" id="submit_crear" class="btn btn-success btn-block" value="Seleccionar y Crear"/>
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
                <div class="modal-footer">
                    <input type="submit" id="submit_mapeoAsignarProducto" class="btn btn-primary" value="Cerrar"/>
                </div>
            <!-- </form> -->
        </div>
    </div>
</div>