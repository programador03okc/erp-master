<div class="modal fade" tabindex="-1" role="dialog" id="modal-add-cliente" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">
            <form id="form-agregar-cliente" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" 
                    aria-label="close"><span aria-hidden="true">&times;</span></button>
                    <div style="display:flex;">
                        <h3 class="modal-title">Nuevo Cliente <span id="nombre_tipo_cliente"></span></h3>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <h5>Tipo de Cliente</h5>
                                    <select class="form-control"  name="tipo_cliente" onChange="handleChangeTipoCliente(event);" required>
                                        <option value="1" default>Persona Natural</option>
                                        <option value="2">Persona Juridica</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <h5>Tipo de Documento</h5>
                                    <select class="form-control" name="id_doc_identidad" onchange="evaluarDocumentoSeleccionado(event);" required>
                                        @foreach ($sis_identidad as $tp)
                                                <option value="{{$tp->id_doc_identidad}}">{{$tp->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-5">
                                    <h5>Nro. de Documento</h5>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="nro_documento" required>
                                        <span class="input-group-btn">
                                            <button id="btnConsultaSunat" class="btn btn-default" type="button" onclick="consultaSunat();"><i class="fa fa-search"></i> Consultar Sunat</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row" >
                                <div id="input-group-persona-natural">
                                    <div class="col-md-4">
                                        <h5>Nombres</h5>
                                        <input type="text" name="nombre" class="form-control"/>
                                    </div>
                                    <div class="col-md-4">
                                        <h5>Apellido Paterno</h5>
                                        <input type="text" name="apellido_paterno" class="form-control"/>
                                    </div>
                                    <div class="col-md-4">
                                        <h5>Apellido Materno</h5>
                                        <input type="text" name="apellido_materno" class="form-control"/>
                                    </div>
                                </div>
                                <div  id="input-group-persona-juridica" hidden>
                                    <div class="col-md-8">
                                        <h5>Razón Social</h5>
                                        <input type="text" name="razon_social" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <h5>Telefono</h5>
                                    <input type="text" name="telefono" class="form-control" required/>
                                </div>
                                <div class="col-md-4">
                                    <h5>Email</h5>
                                    <input type="text" name="email" class="form-control" required/>
                                </div>
                                <div class="col-md-4">
                                    <h5>Dirección</h5>
                                    <input type="text" name="direccion" class="form-control" required/>
                                </div>
                            </div>

                            <br/>
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
                                            <div
                                                class="panel-collapse collapse"
                                                role="tabpanel"
                                                id="collapseListGroup1"
                                                aria-labelledby="collapseListGroupHeading1"
                                                aria-expanded="true"
                                                style=""
                                            >
                                                <ul class="list-group">
                                                    <li class="list-group-item"><h6><strong>Ruc:</strong> <span name="numero_ruc"></span></h6></li>
                                                    <li class="list-group-item"><h6><strong>Fecha Actividad:</strong> <span name="fecha_actividad"></span></h6></li>
                                                    <li class="list-group-item"><h6><strong>Tipo:</strong> <span name="tipo"></span></h6></li>
                                                    <li class="list-group-item"><h6><strong>Fecha Inscripción:</strong> <span name="fecha_inscripcion"></span></h6></li>
                                                    <li class="list-group-item"><h6><strong>Domicilio:</strong> <span name="domicilio"></span></h6></li>
                                                    <li class="list-group-item"><h6><strong>Emisión:</strong> <span name="emision"></span></h6></li>
                                                </ul>
                                                <!-- <div class="panel-footer">Footer</div> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-success boton" value="Guardar"/>
                </div>
            </form>
        </div>
    </div>  
</div>

