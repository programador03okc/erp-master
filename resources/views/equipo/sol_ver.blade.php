<div class="modal fade" tabindex="-1" role="dialog" id="modal-sol_ver" style="overflow-y:scroll;">
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
                {{-- <input type="hidden" name="_token" value="{{csrf_token()}}" id="token"> --}}
                <div class="row">
                    <div class="col-md-12">
                        <table width="100%" class="table-okc-view">
                            <tbody>
                                <tr>
                                    <td width="120px"><strong>Fecha Solicitud:</strong></td>
                                    <td><label id="fecha_solicitud"></label></td>
                                    <td><strong>Fecha Inicio:</strong></td>
                                    <td><label id="fecha_inicio"></label></td>
                                    <td><strong>Fecha Fin:</strong></td>
                                    <td><label id="fecha_fin"></label></td>
                                    <td><strong>Solicitado por:</strong></td>
                                    <td><label id="nombre_trabajador"></label></td>
                                </tr>
                                <tr>
                                    <td><strong>Empresa:</strong></td>
                                    <td><label id="des_empresa"></label></td>
                                    <td><strong>Sede:</strong></td>
                                    <td><label id="des_sede"></label></td>
                                    <td><strong>Grupo:</strong></td>
                                    <td><label id="des_grupo"></label></td>
                                    <td><strong>Area:</strong></td>
                                    <td><label id="des_area"></label></td>
                                </tr>
                                <tr>
                                    <td><strong>Categoría:</strong></td>
                                    <td><label id="des_categoria"></label></td>
                                    <td><strong>Proyecto:</strong></td>
                                    <td colSpan="5"><label id="des_proyecto"></label></td>
                                </tr>
                                <tr>
                                    <td><strong>Observación:</strong></td>
                                    <td colSpan="7"><label id="observacion"></label></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="modal-title">Datos de la Asignación</h3>
                        <table width="100%" class="table-okc-view">
                            <tbody>
                                <tr>
                                    <td width="120px"><strong>Fecha Asignación:</strong></td>
                                    <td><label id="fecha_asignacion"></label></td>
                                    <td><strong>Fecha Inicio:</strong></td>
                                    <td><label id="fecha_inicio"></label></td>
                                    <td><strong>Fecha Fin:</strong></td>
                                    <td><label id="fecha_fin"></label></td>
                                    <td><strong>Usuario que asignó:</strong></td>
                                    <td><label id="nombre_trabajador"></label></td>
                                </tr>
                                <tr>
                                    <td><strong>Observación:</strong></td>
                                    <td colSpan="7"><label id="des_empresa"></label></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{-- <input type="submit" class="btn btn-success boton" value="Guardar"/> --}}
                {{-- <button class="btn btn-sm btn-success" onClick="guardar_asignacion();">Guardar</button> --}}
            </div>
        </div>
    </div>  
</div>
