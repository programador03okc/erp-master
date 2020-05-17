<div class="modal fade" tabindex="-1" role="dialog" id="modal-aprob_flujos" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 1000px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <div style="display:flex;">
                    <h3 class="modal-title">Solicitud de Equipo <label id="codigo"></label></h3>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table>
                            <tbody>
                                <tr>
                                    <th width="140px">Fecha Solicitud:</th>
                                    <td><h5 id="fecha_solicitud">25/08/2019</h5></td>
                                    <td></td>
                                    <th width="140px">Fecha Inicio / Fin:</th>
                                    <td colSpan="3" style="display:flex;"><h5 id="fecha_inicio"></h5><h5><strong> / </strong></h5><h5 id="fecha_fin"></h5></td>
                                </tr>
                                <tr>
                                    <th>Solicitado por:</th>
                                    <td><h5 id="nombre_trabajador"></h5></td>
                                    <td width="30px"></td>
                                    <th>Empresa:</th>
                                    <td><h5 id="nombre_empresa"></h5></td>
                                    <th width="50px">Área:</th>
                                    <td><h5 id="area_descripcion"></h5></td>
                                </tr>
                                <tr>
                                    <th>Concepto:</th>
                                    <td colSpan="5"><h5 id="observaciones"></h5></td>
                                </tr>
                                <tr>
                                    <th>Categoría:</th>
                                    <td><h5 id="des_categoria"></h5></td>
                                    <td></td>
                                    <th>Cantidad:</th>
                                    <td><h5 id="cantidad"></h5></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                            id="listaSolFlujos" style="margin-top:10px;">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Fecha VoBo</th>
                                    <th>VoBo</th>
                                    <th>Rol</th>
                                    <th>Usuario</th>
                                    <th width="300px">Observación</th>
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
