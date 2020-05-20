<div class="modal fade" tabindex="-1" role="dialog" id="modal-envio-cotizacion">
    <div class="modal-dialog" style="width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="title-envio-cotizacion">Envio de Cotización</h3> 
           
            </div>
            <div class="modal-body">
            <form class="form-horizontal formEnviarCoti" id="form-envio_cotizacion" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="hidden" class="form-control" id="id_cotizacion">
                </div>

                    <div class="form-group">
                        <label for="email_destinatario" class="col-sm-2 control-label">A:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="email_destinatario" placeholder="Destinatario">
                            </div>
                    </div>
                    <div class="form-group">
                        <label for="email_remitente" class="col-sm-2 control-label">De:</label>
                        <div class="col-sm-10">
                        <select class="form-control" id="email_remitente" required>
                            <option value="0" disabled>Elija una opción</option>
                            @foreach ($empresas as $emp)
                                <option value="{{$emp->id_empresa}}" data-url-logo="{{$emp->logo_empresa}}">{{$emp->razon_social}}</option>
                            @endforeach
                        </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email_asunto" class="col-sm-2 control-label">Asunto:</label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="email_asunto" placeholder="Asunto">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email_contenido" class="col-sm-2 control-label">Contenido:</label>
                        <div class="col-sm-10">
                        <textarea class="form-control" rows="3" id="email_contenido" placeholder="Contenido"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputFile" class="col-sm-2 control-label">Adjuntos:</label>
                        <div class="col-sm-10">
                        <!-- <input type="file" id="file"> -->
                        <div style="display:flex;">
                            <input type="file" id="nombre_archivo_coti" class="custom-file-input"  onchange="agregarAdjuntoCotizacion(event); return false;" />
                            <div class="input-group-append">
                                <button
                                    id="btnUploadFileCoti"
                                    type="button"
                                    class="btn btn-info hidden"
                                    onClick="guardarAdjuntoCoti();"
                                    
                                    ><i class="fas fa-file-upload"></i> Subir Archivo
                                </button>
                            </div>
                        </div>
                        <p class="help-block"></p>
                        <div class="mailbox-attachment-info" id="attachment-container-nueva-cotiza">
                        </div>

                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <!-- <button class="btn btn-success" role="button"  onClick="enviar_cotizacion();">
                            Enviar <i class="fas fa-paper-plane"></i>
                            </button>                         -->
                            <button type="submit" id="btnEnviarCotizacion" class="btn btn-primary"> ENVIAR <i class="fas fa-paper-plane"></i></button>
                            &nbsp;<img width="10px" src="{{ asset('images/loading.gif')}}" class="loading invisible"> <span id="estado_email" name="estado_email" class="label label-info"></span></<img>

                        </div>
                    </div>
            </form>

            </div>
        </div>
    </div>
</div>