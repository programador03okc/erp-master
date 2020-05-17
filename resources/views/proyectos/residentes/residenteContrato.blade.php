<div class="modal fade" tabindex="-1" role="dialog" id="modal-residente_contrato" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 1100px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <div style="display:flex;">
                    <h3 class="modal-title">Contratos 
                        <h5 id="nombre_residente" style="padding:12px;margin:0px;"></h5>
                    </h3>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form id="form-residente_contrato"  enctype="multipart/form-data" method="post">
                            <input class="oculto" name="id_residente">
                            <table id="contrato" width="100%">
                                <tbody>
                                    <tr>
                                        <td>
                                            <h5>Cargo</h5>
                                            <select class="form-control" name="id_cargo">
                                                <option value="0">Elija una opción</option>
                                                @foreach ($cargos as $tp)
                                                    <option value="{{$tp->id_cargo}}">{{$tp->descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="100px">
                                            <h5>Nro Contrato</h5>
                                            <div style="display:flex;">
                                                <input type="text" name="id_contrato" class="oculto"/>
                                                <input type="text" name="nro_contrato" class="form-control input-sm"/>
                                                <span class="input-group-addon input-sm " style="cursor:pointer;" 
                                                    onClick="contratoModal();"><i class="fas fa-search"></i>
                                                </span>
                                            </div>
                                        </td>
                                        <td width="200px">
                                            <h5>Descripción</h5>
                                            <input type="text" name="descripcion" class="form-control"/>
                                        </td>
                                        <td width="80px">
                                            <h5>Fecha Inicio</h5>
                                            <input type="date" name="fecha_inicio" class="form-control" value="<?=date('Y-m-d');?>"/>
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
                            id="listaResidenteContratos" style="margin-top:10px;">
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
