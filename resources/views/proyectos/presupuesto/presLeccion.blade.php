<div class="modal fade" tabindex="-1" role="dialog" id="modal-presLeccion" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 1100px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <div style="display:flex;">
                    <h3 class="modal-title">Lecciones Aprendidas 
                        <h5 id="cod_partida" style="padding:12px;margin:0px;"></h5>
                        <h5 id="des_partida" style="padding:12px;margin:0px;"></h5>
                    </h3>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form id="form-presLeccion"  enctype="multipart/form-data" method="post">
                            <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                            <input class="oculto" name="id_cd_partida">
                            <input class="oculto" name="id_ci_detalle">
                            <input class="oculto" name="id_gg_detalle">
                            {{-- <input class="oculto" name="id_usuario"> --}}
                            <table id="leccion" width="100%">
                                <tbody>
                                    <tr>
                                        <td width="70%">
                                            <h5>Descripción</h5>
                                            <input type="text" name="observacion" class="form-control"/>
                                        </td>
                                        <td>
                                            <h5>Adjunto</h5>
                                            <input type="file" name="adjunto" id="adjunto" class="filestyle"
                                                data-buttonName="btn-primary" data-buttonText="Adjuntar"
                                                data-size="sm" data-iconName="fa fa-folder-open" data-disabled="false">
                                        </td>
                                        <td>
                                            <h5>Agregar</h5>
                                            <input type="submit" class="btn btn-success" value="Agregar"/>
                                            {{-- <button type="button" class="btn btn-success input-sm boton" id="basic-addon2" onClick="guardar_contrato();">
                                                <i class="fas fa-plus-circle"></i>
                                            </button> --}}
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
                            id="listaLecciones" style="margin-top:10px;">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th width="50%">Observación</th>
                                    <th>Usuario</th>
                                    <th>Fecha Registro</th>
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
