<div class="modal fade" tabindex="-1" role="dialog" id="modal-pres_acu_cd" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Análisis de Costos Unitarios (Costo Directo)</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                <input class="oculto" name="id_partida">
                <input class="oculto" name="id_cd">
                <div class="row">
                    <div class="col-md-12">
                        <br/>
                        <input class="oculto" name="anulados" >
                        <table  width="100%">
                            <tbody>
                                <tr>
                                    <td width="45%">
                                        <label>Seleccione un A.C.U.</label>
                                        <input class="oculto" name="id_partida">
                                        <input class="oculto" name="cod_compo">
                                        {{-- <input class="oculto" name="id_presupuesto"> --}}
                                        <div style="width: 100%; display:flex;">
                                            <div style="width:80%; display:flex;">
                                                <input class="oculto" name="id_cu">
                                                <input type="text" name="cod_acu" class="form-control input-sm" readOnly style="width:70px;"/>
                                                <input type="text" name="des_acu" class="form-control input-sm" readOnly/>
                                            </div>
                                            <div style="width:20%; display:flex;">
                                                <span class="input-group-addon input-sm" style="cursor:pointer;" 
                                                    onClick="acuPartidaModal('cd');">
                                                    <i class="fas fa-search"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <label>Unidad</label>
                                        <input type="text" name="id_unid_medida" hidden/>
                                        <input type="text" name="unid_medida" readOnly class="form-control input-sm"/>
                                    </td>
                                    <td>
                                        <label>Cantidad</label>
                                        <input type="number" name="cantidad" class="form-control input-sm" 
                                            onChange="calculaPrecioTotal();"/>
                                    </td>
                                    <td>
                                        <label>Unitario</label>
                                        <input type="number" name="precio_unitario" readOnly class="form-control input-sm" 
                                            onChange="calculaPrecioTotal();"/>
                                    </td>
                                    <td>
                                        <label>Total</label>
                                        <input type="number" name="precio_total" readOnly class="form-control input-sm" />
                                    </td>
                                    <td>
                                        <label>Sistema</label>
                                        <select class="form-control group-elemento activation" name="id_sistema" >
                                            <option value="0">Elija una opción</option>
                                            @foreach ($sistemas as $sis)
                                                <option value="{{$sis->id_sis_contrato}}">{{$sis->descripcion}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <label>Add</label>
                                        <button type="button" class="btn btn-success input-sm" id="basic-addon2" onClick="guardar_partida_cd();">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                            id="listaAcusCD"  style="margin-top:10px;">
                            <thead>
                                <tr>
                                    <th hidden>N°</th>
                                    <th>Código</th>
                                    <th width="40%">Descripción</th>
                                    <th>Unid.Med</th>
                                    <th width="70">Cantidad</th>
                                    <th>Unitario</th>
                                    <th width="100">Total</th>
                                    <th width="70">Sistema</th>
                                    <th width="70">Acción</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr class="blue info" style="font-size: 14px;">
                                    <td colSpan="4"></td>
                                    {{-- <td><input type="text" name="total_acus_cd" class="form-control input-sm" readOnly/></td> --}}
                                    <td class="right"><label id="simbolo_cd"></label></td>
                                    <td class="right"><label id="total_acus_cd"></label></td>
                                    <td colSpan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col-md-12">
                        <h5>Descripción</h5>
                        <textarea name="observacion" class="form-control" rows="4" cols="50"></textarea>
                    </div>
                </div> --}}
            </div>
            {{-- <div class="modal-footer">
                <button class="btn btn-sm btn-success" onClick="guardar_acus_cd();">Guardar</button>
            </div> --}}
        </div>
    </div>  
</div>
