<div class="modal fade" tabindex="-1" role="dialog" id="modal-proveedor" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 700px;">
        <div class="page-main" type="modal-proveedor">

            <div class="modal-content">
                <form id="form-proveedor" method="post">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                        <div style="display:flex;">
                            <h3 class="modal-title">Nuevo Proveedor</h3>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
                                <input class="oculto" name="id_proveedor">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h5>Tipo de Documento</h5>
                                        <select class="form-control" name="id_doc_identidad" onchange="evaluarDocumentoSeleccionado(event);" required>
                                            <option value="0" disabled>Elija una opción</option>
                                            @foreach ($sis_identidad as $tp)
                                            @if($tp->id_doc_identidad === 2)
                                            <option value="{{$tp->id_doc_identidad}}" selected>{{$tp->descripcion}}</option>
                                            @else
                                            <option value="{{$tp->id_doc_identidad}}">{{$tp->descripcion}}</option>
                                            @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-8">
                                        <h5>Nro. de Documento</h5>
                                        <input type="text" style="display:none;" name="transportista">
                                        <input type="number" class="form-control" name="nro_documento_prov" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <h5>Razon Social</h5>
                                        <input type="text" name="razon_social" class="form-control" required />
                                    </div>
                                    <div class="col-md-4">
                                        <h5>Teléfono</h5>
                                        <input type="text" name="telefono" class="form-control" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <h5>Dirección Fiscal</h5>
                                        <input type="text" name="direccion_fiscal" class="form-control" required />
                                    </div>
                                    <div class="col-md-4">
                                        <h5>Ubigeo</h5>
                                        <div style="display:flex;">
                                            <input type="text" class="oculto" name="ubigeo">
                                            <input type="text" class="form-control" name="name_ubigeo" readonly="">
                                            <button type="button" title="Seleccionar Ubigeo" class="btn-primary" onclick="ubigeoModalProveedor();"><i class="far fa-compass"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <br />
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel-group" role="tablist">
                                        <div id="panel_consulta_sunat" class="panel panel-default invisible">
                                            <div class="panel-heading" role="tab" id="collapseListGroupHeading1">
                                                <h4 class="panel-title">
                                                    <a href="#collapseListGroup1" class="" role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseListGroup1">
                                                        <h6><strong>Razon Social:</strong><img width="10px" src="{{ asset('images/loading.gif')}}" class="loading invisible"> <span name="razon_social"></span> <strong>RUC:<img width="10px" src="{{ asset('images/loading.gif')}}" class="loading invisible"> </strong>[<span name="numero_ruc"></span>] <strong>Condicion:</strong><img width="10px" src="{{ asset('images/loading.gif')}}" class="loading invisible"> <span name="condicion" class="label label-default"></span> <strong>Estado:<img width="10px" src="{{ asset('images/loading.gif')}}" class="loading invisible"> </strong> <span name="estado" class="label label-default"></span></h6>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div class="panel-collapse collapse" role="tabpanel" id="collapseListGroup1" aria-labelledby="collapseListGroupHeading1" aria-expanded="true">
                                                <ul class="list-group">
                                                    <li class="list-group-item">
                                                        <h6><strong>Ruc:</strong> <span name="numero_ruc"></span></h6>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <h6><strong>Fecha Actividad:</strong> <span name="fecha_actividad"></span></h6>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <h6><strong>Tipo:</strong> <span name="tipo"></span></h6>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <h6><strong>Fecha Inscripción:</strong> <span name="fecha_inscripcion"></span></h6>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <h6><strong>Domicilio:</strong> <span name="domicilio"></span></h6>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <h6><strong>Emisión:</strong> <span name="emision"></span></h6>
                                                    </li>
                                                </ul>
                                                <!-- <div class="panel-footer">Footer</div> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" id="submitProveedor" class="btn btn-success boton" value="Guardar" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>