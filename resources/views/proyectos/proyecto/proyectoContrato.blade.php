<div class="modal fade" tabindex="-1" role="dialog" id="modal-proyecto_contratoc" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 1100px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <div style="display:flex;">
                    <h3 class="modal-title">Contratos 
                        <h5 id="cod_proyecto" style="padding:12px;margin:0px;"></h5>
                        <h5 id="des_proyecto" style="padding:12px;margin:0px;"></h5>
                    </h3>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form id="form-contrato"  enctype="multipart/form-data" method="post">
                            <input class="oculto" name="id_proyecto">
                            <table id="contrato" width="100%">
                                <tbody>
                                    <tr>
                                        <td>
                                            <h5>Tipo Contrato</h5>
                                            <select class="form-control" name="id_tp_contrato">
                                                <option value="0">Elija una opción</option>
                                                @foreach ($tipo_contrato as $tp)
                                                    <option value="{{$tp->id_tp_contrato}}">{{$tp->descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="100px">
                                            <h5>Nro Contrato</h5>
                                            <input type="text" name="nro_contrato" class="form-control"/>
                                        </td>
                                        <td width="200px">
                                            <h5>Descripción</h5>
                                            <input type="text" name="descripcion" class="form-control"/>
                                        </td>
                                        <td width="80px">
                                            <h5>Fecha Contrato</h5>
                                            <input type="date" name="fecha_contrato" class="form-control" value="<?=date('Y-m-d');?>"/>
                                        </td>
                                        <td>
                                            <h5>Importe / Moneda</h5>
                                            <div style="display:flex;">
                                                {{-- <input type="text" name="simbolo" class="form-control group-elemento" style="width:40px;text-align:center;" readOnly/> --}}
                                                <input type="number" name="importe_contrato" class="form-control group-elemento" style="text-align:right;"/>
                                                <select class="form-control group-elemento" name="moneda_con">
                                                    <option value="0">Elija una opción</option>
                                                    @foreach ($monedas as $mon)
                                                        <option value="{{$mon->id_moneda}}">{{$mon->descripcion}} - {{$mon->simbolo}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <h5>Adjunto</h5>
                                            <input type="file" name="adjunto" id="adjunto" class="filestyle"
                                                data-buttonName="btn-primary" data-buttonText="Adjuntar"
                                                data-size="sm" data-iconName="fa fa-folder-open" data-disabled="false">
                                        </td>
                                        <td>
                                            <h5>Add</h5>
                                            <input type="submit" value="Agregar" class="btn btn-success"/>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                            id="listaContratos" style="margin-top:10px;">
                            <thead>
                                <tr>
                                    <th hidden>N°</th>
                                    <th>Tipo</th>
                                    <th>Nro.Contrato</th>
                                    <th width="300px">Descripción</th>
                                    <th>Fecha Contrato</th>
                                    <th>Mnd</th>
                                    <th>Importe</th>
                                    <th>Adjunto</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>
