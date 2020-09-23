<div class="modal fade" tabindex="-1" role="dialog" id="modal-transformacion_create" style="overflow-y:scroll;">
    <div class="modal-dialog"  style="width: 1000px;">
        <div class="modal-content">
            <form id="form-transformacion_create">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Generar Hoja de Transformación</h3>
                </div>
                <div class="modal-body">
                    <input type="text" class="oculto" name="id_cc">
                    <input type="text" class="oculto" name="tipo">
                    <input type="text" class="oculto" name="oportunidad">
                    <div class="row">
                        <div class="col-md-2">
                            <label>Almacén</label>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control activation js-example-basic-single" name="id_almacen">
                                <option value="0">Elija una opción</option>
                                @foreach ($almacenes as $alm)
                                    <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-info">
                                <div class="panel-heading">Materias Primas</div>
                                <table id="listaMateriasPrimas" class="table">
                                    <thead>
                                        <tr>
                                            <th>Part No.</th>
                                            <th>Descripción</th>
                                            <th>Cant.</th>
                                            <!-- <th>Unid.</th> -->
                                            <th>Unit.</th>
                                            <th>Total</th>
                                            <!-- <th>
                                                <i class="fas fa-plus-square icon-tabla green boton" 
                                                    data-toggle="tooltip" data-placement="bottom" 
                                                    title="Agregar Producto" onClick="productoModal();"></i>
                                            </th> -->
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">Servicios Directos</div>
                                <table id="listaServiciosDirectos" class="table">
                                    <thead>
                                        <tr>
                                            <!-- <th width='10%'>Part Number</th> -->
                                            <th>Descripción</th>
                                            <!-- <th width='10%'>Cant.</th>
                                            <th>Unit.</th> -->
                                            <th width='15%'>Total</th>
                                            <th>
                                                <i class="fas fa-plus-square icon-tabla green boton add-new-servicio" 
                                                    data-toggle="tooltip" data-placement="bottom" 
                                                    title="Agregar Servicio" ></i>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="panel panel-danger">
                                <div class="panel-heading">Sobrantes</div>
                                <table id="listaSobrantes" class="table">
                                    <thead>
                                        <tr>
                                            <th>Part Number</th>
                                            <th width='40%'>Descripción</th>
                                            <th>Cant.</th>
                                            <th>Unid.</th>
                                            <th>Unit.</th>
                                            <th>Total</th>
                                            <th width='8%'>
                                                <i class="fas fa-plus-square icon-tabla green boton add-new-sobrante" 
                                                    data-toggle="tooltip" data-placement="bottom" 
                                                    title="Agregar Producto" ></i>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="panel panel-success">
                                <div class="panel-heading">Productos Transformados</div>
                                <table id="listaProductoTransformado" class="table">
                                    <thead>
                                        <tr>
                                            <th>Part Number</th>
                                            <th width='40%'>Descripción</th>
                                            <th>Cant.</th>
                                            <th>Unid.</th>
                                            <th>Unit.</th>
                                            <th>Total</th>
                                            <th width='8%'>
                                                <i class="fas fa-plus-square icon-tabla green boton add-new-transformado" 
                                                    data-toggle="tooltip" data-placement="bottom" 
                                                    title="Agregar Producto" onClick="productoModal();"></i>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submit_transformacion" class="btn btn-success" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>
</div>