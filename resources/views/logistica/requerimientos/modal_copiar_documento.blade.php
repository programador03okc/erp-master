<div class="modal fade" tabindex="-1" role="dialog" id="modal-copiar-documento">
    <div class="modal-dialog" style="width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"><strong>Copiar Documento</strong></h3>
            </div>
            <div class="modal-body">
            <form class="box-scroll">
            <div class="panel panel-default">
                    <div class="panel-heading"><strong>Documento de Origen</strong></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12 form-inline">
                                <div class="form-group">
                                    <label>Código</label>
                                    <input type="text" class="form-control input-sm" name="codigo" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Documento de Destino</strong></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Concepto/Motivo</label>
                                    <input type="text" class="form-control input-sm" name="concepto" id="textConcepto"> 
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Fecha</label>
                                    <input type="date" class="form-control input-sm" name="fecha_requerimiento" id="textFechaRequerimiento"  min={{ date('Y-m-d H:i:s') }} value={{ date('Y-m-d H:i:s') }}>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Prioridad</label>
                                    <select class="form-control input-sm" name="prioridad" id="textPrioridad" >
                                    @foreach ($prioridades as $prioridad)
                                        <option value="{{$prioridad->id_prioridad}}">{{$prioridad->descripcion}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                <label>Moneda</label>
                                    <select class="form-control input-sm" name="moneda" id="textMoneda" >
                                    @foreach ($monedas as $moneda)
                                        <option value="{{$moneda->id_moneda}}">{{$moneda->descripcion}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Periodo</label>
                                    <select class="form-control" name="periodo" id="textPeriodo">
                                    @foreach ($periodos as $periodo)
                                        <option value="{{$periodo->id_periodo}}">{{$periodo->descripcion}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Empresa</label>
                                    <select name="empresa" class="form-control input-sm" id="textEmpresa"
                                        required>
                                        <option value="">Elija una opción</option>
                                        @foreach ($empresas as $empresa)
                                            <option value="{{$empresa->id_empresa}}">{{ $empresa->contribuyente->razon_social}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Area</label>
                                    <input type="hidden" class="form-control input-sm" name="id_grupo" id="textGrupo">
                                    <input type="hidden" class="form-control input-sm" name="id_area" id="textArea">
                                    <div class="input-group-okc">
                                        <input type="text" class="form-control input-sm" name="nombre_area" id="textNombreArea">
                                        <div class="input-group-append">
                                            <button type="button" class="input-group-text" onclick="modal_area();">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="checkbox disabled">
                                        <label>
                                        <input type="checkbox" checked disabled> Incluir Items
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Roles del Usuario</label>
                                    <select class="form-control input-sm" name="rol_usuario" id="textRolUsuario" >
                                    @foreach ($roles as $rol)
                                    <option value="{{$rol->id_rol}}">{{$rol->descripcion}}</option>

                                    @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div> <!-- panel-body-->
                    </div>
        
                </div>
            </form>
                
            <div class="modal-footer">
                <!-- <label style="display: none;" id="id_archivo_adjunto"></label>
                <label style="display: none;" id="id_requerimiento"></label> -->
                <button class="btn btn-sm btn-success" onClick="copiarDatosRequerimiento();">Copiar Documento</button>
            </div>
        </div>
    </div>
</div>

