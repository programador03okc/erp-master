<div class="modal fade" tabindex="-1" role="dialog" id="modal-proyecto_create" style="overflow-y:scroll;">
    <div class="modal-dialog" style="width: 1000px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Crear Proyecto</h3>
            </div>
            <div class="modal-body">
                <form id="form-proyecto"  enctype="multipart/form-data" method="post">
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Codigo</h5>
                            <input class="oculto" name="id_proyecto">
                            <input type="text"  name="codigo" placeholder="PY-00-000" class="form-control activation" readOnly>
                        </div>
                        <div class="col-md-9">
                            <h5>Descripción</h5>
                            <input class="oculto" name="id_op_com">
                            <div style="width: 100%; display:flex;">
                                <div style="width:94%; display:flex;">
                                    <input type="text" name="nombre_opcion" class="form-control input-sm" readOnly/>
                                </div>
                                <div style="width:6%;">
                                    <span class="input-group-addon input-sm" style="cursor:pointer;"  
                                        onClick="open_opcion_modal();">
                                        <i class="fas fa-search"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Tipo</h5>
                            <select class="form-control activation" name="tp_proyecto">
                                <option value="0">Elija una opción</option>
                                @foreach ($tipos as $tp)
                                    <option value="{{$tp->id_tp_proyecto}}">{{$tp->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <h5>Empresa</h5>
                            <select class="form-control activation" name="id_empresa">
                                <option value="0">Elija una opción</option>
                                @foreach ($empresas as $emp)
                                    <option value="{{$emp->id_empresa}}">{{$emp->razon_social}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <h5>Cliente</h5>
                            <div style="display:flex;">
                                <input class="oculto" name="id_cliente"/>
                                <input class="oculto" name="id_contrib"/>
                                <input type="text" class="form-control activation" name="cliente_razon_social" placeholder="Seleccione un cliente..." 
                                    aria-describedby="basic-addon1" disabled="true">
                                <button type="button" class="input-group-text activation btn-primary" id="basic-addon1" onClick="clienteModal();">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                            {{-- boton para Crear Opciones --}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Sistema de Contrato</h5>
                            <select class="form-control activation" name="sis_contrato">
                                <option value="0">Elija una opción</option>
                                @foreach ($sistemas as $sis)
                                    <option value="{{$sis->id_sis_contrato}}">{{$sis->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <h5>Plazo de Ejecución</h5>
                            <div style="display:flex;">
                                <input type="number" name="plazo_ejecucion" onChange="change_fechas();" class="form-control group-elemento" style="width:80px;text-align:right;"/>
                                <select class="form-control group-elemento activation" name="unid_program" onChange="change_fechas();">
                                    <option value="0">Elija una opción</option>
                                    @foreach ($unid_program as $unid)
                                        <option value="{{$unid->id_unid_program}}">{{$unid->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <h5>Importe sin IGV</h5>
                            <div style="display:flex;">
                                <input type="text" name="simbolo" class="form-control group-elemento" style="width:40px;text-align:center;" readOnly/>
                                <input type="number" name="importe" class="form-control group-elemento" style="text-align: right;" />
                                <select class="form-control group-elemento activation" name="moneda" onChange="change_moneda();">
                                    <option value="0">Elija una opción</option>
                                    @foreach ($monedas as $mon)
                                        <option value="{{$mon->id_moneda}}">{{$mon->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Modalidad</h5>
                            <select class="form-control activation" name="modalidad">
                                <option value="0">Elija una opción</option>
                                @foreach ($modalidades as $mod)
                                    <option value="{{$mod->id_modalidad}}">{{$mod->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <h5>Fecha Inicio / Fecha Fin</h5>
                            <div style="display:flex;">
                                <input type="date" name="fecha_inicio" value="<?=date('Y-m-d');?>" onChange="change_fechas();" class="form-control"/>
                                <input type="date" name="fecha_fin" value="<?=date('Y-m-d');?>" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h5>Jornal x día</h5>
                            <div class="input-group">
                                <input type="number" name="jornal" class="form-control"/>
                                <span class="input-group-addon">horas</span>
                            </div>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-md-12">
                        <div class="panel panel-info">
                            <div class="panel-heading">Documento de Autorización</div>
                            <div class="panel-body">
                                <table id="contrato" width="100%">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <h5>Tipo Contrato</h5>
                                                <select class="form-control" name="id_tp_contrato_proy">
                                                    <option value="0">Elija una opción</option>
                                                    @foreach ($tipo_contrato as $tp)
                                                        <option value="{{$tp->id_tp_contrato}}">{{$tp->descripcion}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td width="70px">
                                                <h5>Nro Contrato</h5>
                                                <input type="text" name="nro_contrato_proy" class="form-control"/>
                                            </td>
                                            <td width="230px">
                                                <h5>Descripción</h5>
                                                <input type="text" name="descripcion_proy" class="form-control"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="60px">
                                                <h5>Fecha Contrato</h5>
                                                <input type="date" name="fecha_contrato_proy" class="form-control" value="<?=date('Y-m-d');?>"/>
                                            </td>
                                            <td>
                                                <h5>Importe / Moneda</h5>
                                                <div style="display:flex;">
                                                    <input type="number" name="importe_contrato_proy" class="form-control group-elemento" style="text-align:right;"/>
                                                    <select class="form-control group-elemento" name="moneda_contrato">
                                                        {{-- <option value="0">Elija una opción</option> --}}
                                                        @foreach ($monedas as $mon)
                                                            <option value="{{$mon->id_moneda}}">{{$mon->descripcion}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <h5>Adjunto</h5>
                                                <input type="file" name="primer_adjunto" id="primer_adjunto" class="filestyle"
                                                    data-buttonName="btn-primary" data-buttonText="Adjuntar"
                                                    data-size="sm" data-iconName="fa fa-folder-open" data-disabled="false">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-success" onClick="guardar_proyecto();">Guardar</button>
            </div>
        </div>
    </div>  
</div>
