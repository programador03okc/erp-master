<div class="page-main" type="devolucion">

    <div class="box">
        <form id="form-devolucion">
            <div class="box-header with-border">

                <h3 class="box-title">Devolución N° <span class="badge badge-secondary" id="codigo">DEV 00-000</span>
                    <span id="estado"></span>
                </h3>
                <div class="box-tools pull-right">

                    <button type="button" class="btn btn-sm btn-warning nueva-devolucion" data-toggle="tooltip" data-placement="bottom" title="Nueva Customización">
                        <i class="fas fa-copy"></i> Nuevo
                    </button>

                    <input id="submit_devolucion" class="btn btn-sm btn-success guardar-devolucion" type="submit" style="display: none;" data-toggle="tooltip" data-placement="bottom" title="Actualizar devolucion" value="Guardar">

                    <button type="button" class="btn btn-sm btn-primary edit-devolucion" data-toggle="tooltip" data-placement="bottom" title="Editar devolucion">
                        <i class="fas fa-pencil-alt"></i> Editar
                    </button>

                    <button type="button" class="btn btn-sm btn-danger anular-devolucion" data-toggle="tooltip" data-placement="bottom" title="Anular devolucion" onClick="anularDevolucion();">
                        <i class="fas fa-trash"></i> Anular
                    </button>

                    <button type="button" class="btn btn-sm btn-info buscar-devolucion" data-toggle="tooltip" data-placement="bottom" title="Buscar historial de registros" onClick="abrirDevolucionModal();">
                        <i class="fas fa-search"></i> Buscar</button>

                    <button type="button" class="btn btn-sm btn-secondary cancelar" data-toggle="tooltip" data-placement="bottom" title="Cancelar" style="display: none;">
                        Cancelar</button>

                    {{-- <button type="button" class="btn btn-sm btn-success procesar-devolucion" data-toggle="tooltip" data-placement="bottom" 
                    title="Procesar devolucion" onClick="procesardevolucion();">
                    <i class="fas fa-share"></i> Procesar
                </button> --}}
                </div>
            </div>
            <div class="box-body">

                <div class="row" style="padding-left: 10px;padding-right: 10px;margin-bottom: 0px;">
                    <div class="col-md-12">
                        <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                        <input type="hidden" name="id_devolucion" primary="ids">

                        <div class="row">
                            <div class="col-md-4">
                                <label class="col-sm-4 control-label">Almacén: </label>
                                <div class="col-sm-8">
                                    <select class="form-control js-example-basic-single edition limpiardevolucion" name="id_almacen" required>
                                        <option value="">Elija una opción</option>
                                        @foreach ($almacenes as $almacen)
                                        <option value="{{$almacen->id_almacen}}">{{$almacen->codigo}} - {{$almacen->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <label class="col-sm-2 control-label">Comentario: </label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control edition limpiardevolucion" name="observacion" required />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <label class="col-sm-4 control-label">Tipo: </label>
                                <div class="col-sm-8">
                                    <select class="form-control js-example-basic-single" name="id_tipo" required readOnly>
                                        <option value="">Elija una opción</option>
                                        @foreach ($tipos as $tp)
                                        <option value="{{$tp->id}}">{{$tp->descripcion}}</option>
                                        @endforeach
                                        {{-- <option value="cliente" selected>Cliente</option>
                                        <option value="proveedor">Proveedor</option> --}}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <label class="col-sm-2 control-label">Contribuyente: </label>
                                <div class="col-sm-10">
                                    <input type="text" class="oculto" name="id_cliente">
                                    <input type="text" class="oculto" name="id_proveedor">
                                    <input type="text" class="oculto" name="id_contribuyente" required>
                                    <div style="display:flex;">
                                        <input type="text" class="form-control limpiardevolucion" name="contribuyente" readonly>
                                        <button type="button" class="input-group-text activation" id="basic-addon1" onClick="openContribuyenteModal();">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="col-sm-4 control-label">Fecha documento: </label>
                                <div class="col-sm-8">
                                    <input type="date" class="form-control edition limpiardevolucion" name="fecha_documento"/>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row" style="padding-left: 10px;padding-right: 10px;margin-top: 0px;">
                                    <div class="col-md-4">
                                        <label>Registrado por:</label>
                                        <span id="nombre_registrado_por" class="limpiarTexto"></span>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Fecha registro:</label>
                                        <span id="fecha_registro" class="limpiarTexto"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row" style="padding-left: 10px;padding-right: 10px;margin-top: 0px;">
                    <div class="col-md-4">
                        <label>Revisado por:</label>
                        <span id="nombre_revisado_por" class="limpiarTexto"></span>
                    </div>
                    <div class="col-md-4">
                        <label>Comentario de conformidad:</label>
                        <span id="comentario_revision" class="limpiarTexto"></span>
                    </div>
                </div>
        </form>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-info" style="margin-bottom: 0px;">
                    <div class="panel-heading"><strong>Productos</strong></div>
                    <table id="listaProductosDevolucion" class="table">
                        <thead>
                            <tr style="background: lightskyblue;">
                                <th>Código</th>
                                <th width='15%'>Part Number</th>
                                <th width='50%'>Descripción</th>
                                <th width='10%'>Cant.</th>
                                <th>Unid.</th>
                                <th width='8%' style="padding:0px;">
                                    <i class="fas fa-plus-square icon-tabla green boton add-new-sobrante edition" id="addProducto" data-toggle="tooltip" data-placement="bottom" title="Agregar Producto" onClick="abrirProductos();"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot></tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="row salidas">
            <div class="col-md-12">
                <div class="panel panel-default" style="margin-bottom: 0px;">
                    <div class="panel-heading"><strong>Salidas</strong></div>
                    <table id="listaSalidasDevolucion" class="table">
                        <thead>
                            <tr>
                                <th>Guía Venta</th>
                                <th>Factura</th>
                                <th>Razon Social del Cliente</th>
                                <th>Cod.Salida</th>
                                <th width='8%' style="padding:0px;">
                                    <i class="fas fa-plus-square icon-tabla green boton add-new-sobrante edition" id="addSobrante" data-toggle="tooltip" data-placement="bottom" title="Agregar Salida Venta" onClick="verSalidasVenta();"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row ingresos">
            <div class="col-md-12">
                <div class="panel panel-default" style="margin-bottom: 0px;">
                    <div class="panel-heading"><strong>Ingresos</strong></div>
                    <table id="listaIngresosDevolucion" class="table">
                        <thead>
                            <tr>
                                <th>Guía Compra</th>
                                <th>Factura</th>
                                <th>Razon Social del Proveedor</th>
                                <th>Cod.Ingreso</th>
                                <th width='8%' style="padding:0px;">
                                    <i class="fas fa-plus-square icon-tabla green boton edition" data-toggle="tooltip"
                                     data-placement="bottom" title="Agregar Ingreso" onClick="verIngresos();"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default" style="margin-bottom: 0px;">
                    <div class="panel-heading"><strong>Incidencias</strong></div>
                    <table id="listaIncidenciasDevolucion" class="table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Fecha reporte</th>
                                <th>Razon social del cliente</th>
                                <th>Responsable</th>
                                <th>Estado</th>
                                <th width='8%' style="padding:0px;">
                                    <i class="fas fa-plus-square icon-tabla green boton add-new-sobrante edition" id="addSobrante" data-toggle="tooltip" data-placement="bottom" title="Agregar Producto" onClick="abrirIncidenciaModal();"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table id="totalSobrantesTransformados" width="100%">
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>