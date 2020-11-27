<div class="modal fade" tabindex="-1" role="dialog" id="modal-despachoAdjuntos" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <div style="display:flex;">
                    <h3 class="modal-title">Comentarios a la Orden Despacho</h3>
                </div>
            </div>
            <div class="modal-body">
                <form id="form-od_adjunto"  enctype="multipart/form-data" method="post">
                    <div class="row">
                        <input type="text" class="oculto" name="id_od_adjunto"/>
                        <input type="text" class="oculto" name="id_od"/>
                        <input type="text" class="oculto" name="codigo_od"/>
                        <input type="text" class="oculto" name="numero"/>
                        <input type="text" class="oculto" name="proviene_de"/>
                        <div class="col-md-6">
                            <label>Descripción:</label>
                            <input type="text" name="descripcion" class="form-control"/>
                        </div>
                        <div class="col-md-4">
                            <label>Buscar Archivo:</label>
                            <input type="file" name="archivo_adjunto" id="archivo_adjunto" class="filestyle"
                                data-buttonName="btn-warning" data-buttonText="Adjuntar" 
                                data-size="sm" data-iconName="fa fa-folder-open" data-disabled="false">
                        </div>
                        <div class="col-md-2">
                            <input type="submit" style="height:32px; margin-top:23px;" class="btn btn-success boton" value="Agregar"/>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-md-12">
                        <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                            id="listaAdjuntos" style="margin-top:10px;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Descripción</th>
                                    <th>Adjunto</th>
                                    <th>Fecha Registro</th>
                                    <th>Quitar</th>
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
