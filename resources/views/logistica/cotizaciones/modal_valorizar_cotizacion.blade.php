<div class="modal fade" tabindex="-1" role="dialog" id="modal-valorizarCotizacion">
    <div class="modal-dialog" style="width: 95%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Valorizar Cotización</h3>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12-ms">
                        <form id="form-valorizar_cotizacion" type="register" form="formulario">
                            <div class="col-md-5">
                                <h5>Condición</h5>
                                <div style="display:flex;">
                                    <select class="form-control group-elemento" name="id_condicion" onchange="handlechangeCondicion(event);"
                                        style="width:120px;text-align:center;"  >
                                        @foreach ($condiciones as $cond)
                                            <option value="{{$cond->id_condicion_pago}}">{{$cond->descripcion}}</option>
                                        @endforeach
                                    </select>
                                    <input type="number" name="plazo_dias" class="form-control group-elemento" style="width:60px; text-align:right;" />
                                    <input type="text" value="días" class="form-control group-elemento" style="width:60px;text-align:center;" disabled />
                                    <button type="button" class="btn-success" title="Guardar" onClick="guardarCondicion()">
                        <i class="fas fa-save"></i> Guardar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-12-ms">
                    <table class="mytable table table-condensed table-bordered table-okc-view" id="listarItemCotizacion">
                        <thead>
                            <tr>
                                <th rowspan="2">#</th>
                                <th rowspan="2" width="100">CODIGO</th>
                                <th rowspan="2">DESCRIPCIÓN</th>
                                <th rowspan="2">UNIDAD</th>
                                <th rowspan="2">CANTIDAD</th>
                                <th rowspan="2">PRECIO REF.</th>
                                <th colspan="9" class="text-center">PROVEEDOR</th>
                            </tr>

                            <tr>
                                <th>UNID.</th>
                                <th width="80">CANT.</th>
                                <th width="80">PRECIO</th>
                                <th width="80">TOTAL</th>
                                <th width="80">FLETE</th>
                                <th width="50">% DES.</th>
                                <th width="80">MONTO DES.</th>
                                <th width="80">SUBTOTAL</th>
                                <th width="50">ACCIÓN</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <label style="display: none;" id="id_cotizacion"></label>
 
                <!-- <button class="btn btn-sm btn-success" onClick="">Aceptar</button> -->
            </div>
        </div>
    </div>
</div>