@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body_sin_option')
<div class="page-main" type="busqueda_ingresos">
    <legend class="mylegend">
        <h2>Búsqueda Avanzada de Ingresos</h2>
        <ol class="breadcrumb">
            <li>
                {{-- <button type="submit" class="btn btn-success" data-toggle="tooltip" 
                    data-placement="bottom" title="Descargar Kardex Sunat" 
                    onClick="downloadKardexSunat();">Kardex Sunat</button> --}}
                <button type="button" class="btn btn-primary" data-toggle="tooltip" 
                    data-placement="bottom" title="Ingrese los filtros" 
                    onClick="open_filtros();">
                    <i class="fas fa-search"></i>  Filtros</button>
            </li>
        </ol>
    </legend>
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" 
                id="listaBusquedaIngresos">
                <thead>
                    <tr>
                        <th hidden></th>
                        <th>Tp</th>
                        <th>Serie-Número</th>
                        <th>Fecha Emisión</th>
                        <th>RUC</th>
                        <th width="20%">Razon Social</th>
                        <th>Condición</th>
                        <th>Código</th>
                        <th>Cod.Anexo</th>
                        <th width="30%">Descripción</th>
                        <th>Cant.</th>
                        <th>Estado</th>
                        <th width="20%">Almacén</th>
                        <th>Fecha Registro</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-busq_filtros">
    <div class="modal-dialog">
        <div class="modal-content" style="width:500px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Filtros de Búsqueda de Ingresos</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Empresas</h5>
                        <div style="display:flex">
                            <input type="checkbox" name="todas_empresas" style="width:30px;margin-top:10px;"/>
                            <h5 style="width:50px;">Todas</h5>
                            <select class="form-control" name="id_empresa" >
                                @foreach ($empresas as $alm)
                                    <option value="{{$alm->id_empresa}}">{{$alm->razon_social}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Almacén</h5>
                        <div style="display:flex">
                            <input type="checkbox" name="todos_almacenes" style="width:30px;margin-top:10px;"/>
                            <h5 style="width:50px;">Todos</h5>
                            <select class="form-control" name="almacen" multiple>
                                @foreach ($almacenes as $alm)
                                    <option value="{{$alm->id_almacen}}">{{$alm->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Ingrese un criterio de búsqueda</h5>
                        <div style="display:flex;">
                            <input class="oculto" name="id_proveedor"/>
                            <input class="oculto" name="id_contrib"/>
                            <select class="form-control" name="buscar" style="width:30%;">
                                {{-- <option value="0">Elija una opción</option> --}}
                                <option value="1">Frase</option>
                                <option value="2">Código</option>
                                <option value="3">Nro.Parte</option>
                            </select>
                            <input type="text" class="form-control" name="descripcion" aria-describedby="basic-addon1">
                            {{-- <button type="button" class="input-group-text btn-primary" id="basic-addon1" onClick="proveedorModal();">
                                <i class="fa fa-search"></i>
                            </button>
                            <button type="button" class="input-group-text btn-danger" id="basic-addon1" onClick="limpiar_proveedor();">
                                <i class="fas fa-trash-alt"></i>
                            </button> --}}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Documentos</h5>
                        <select class="form-control" name="documento" multiple>
                            @foreach ($tp_doc_almacen as $alm)
                                <option value="{{$alm->id_tp_doc_almacen}}">{{$alm->descripcion}}</option>
                            @endforeach
                        </select>
                        <div style="display:flex">
                            <input type="checkbox" name="todos_documentos" style="width:30px;margin-top:10px;"/>
                            <h5>Todas</h5>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Rango de Fechas</h5>
                        <div style="display:flex;">
                            <span class="form-control" style="width:100px;"> Desde: </span>
                            <input type="date" class="form-control" name="fecha_inicio">
                            <span class="form-control" style="width:100px;"> Hasta: </span>
                            <input type="date" class="form-control" name="fecha_fin">
                        </div>
                    </div>
                </div>
                {{-- <fieldset class="group-importes"><legend><h6>Filtros específicos</h6></legend>
                    <table width="100%">
                        <tbody>
                            <tr>
                                <td colSpan="2" text-align="right">
                                    <div style="display:flex;">
                                        <span class="form-control" style="width:100px;"> Desde: </span>
                                        <input type="date" class="form-control" name="fecha_inicio">
                                        <span class="form-control" style="width:100px;"> Hasta: </span>
                                        <input type="date" class="form-control" name="fecha_fin">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td width="40%">Proveedor: </td>
                                <td>
                                    <div style="display:flex;">
                                        <input class="oculto" name="id_proveedor"/>
                                        <input class="oculto" name="id_contrib"/>
                                        <input type="text" class="form-control" name="razon_social" placeholder="Seleccione un proveedor..." 
                                            aria-describedby="basic-addon1" required>
                                        <button type="button" class="input-group-text btn-primary" id="basic-addon1" onClick="proveedorModal();">
                                            <i class="fa fa-search"></i>
                                        </button>
                                        <button type="button" class="input-group-text btn-danger" id="basic-addon1" onClick="limpiar_proveedor();">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Responsable: </td>
                                <td>
                                    <div style="display:flex;">
                                        <select class="form-control" name="responsable">
                                            <option value="0" >Elija una opción</option>
                                            @foreach ($trabajadores as $alm)
                                                <option value="{{$alm->id_trabajador}}">{{$alm->nombre_trabajador}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Moneda: </td>
                                <td>
                                    <div style="display:flex;">
                                        <select class="form-control" name="moneda_opcion">
                                            <option value="0" >Elija una opción</option>
                                            <option value="1" >Docs en Soles (S/)</option>
                                            <option value="2" >Docs en Dólares (US$)</option>
                                            <option value="3" >Docs en Soles y Dólares</option>
                                            <option value="4" >Convertir Docs en Soles</option>
                                            <option value="5" >Convertir Docs en Dólares</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Mostrar Documentos Referenciados: 
                                    <input type="checkbox" name="referenciado" style="width:30px;margin-top:10px;"/>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset> --}}
            </div>
            <div class="modal-footer">
                <label id="mid_doc_com" style="display: none;"></label>
                <button class="btn btn-sm btn-success" onClick="actualizarLista();">Listar</button>
            </div>
        </div>
    </div>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/almacen/busqueda_ingresos.js')}}"></script>
@include('layout.fin_html')