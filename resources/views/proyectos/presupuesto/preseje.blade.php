@extends('layout.main')
@include('layout.menu_proyectos')
@section('option')
    @include('layout.option')
@endsection

@section('cabecera')
Presupuesto de Ejecución
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{route('proyectos.index')}}"><i class="fas fa-tachometer-alt"></i> Proyectos</a></li>
  <li>Ejecución</li>
  <li class="active">@yield('cabecera')</li>
</ol>
@endsection

@section('content')
<div class="page-main" type="preseje">
    <form id="form-preseje" type="register" form="formulario">
        <div class="thumbnail" style="padding-left: 10px;padding-right: 10px;">
            <legend class="mylegend">
                <h2>Datos Generales</h2>
                <ol class="breadcrumb" style="background-color: white;">
                    <li><label id="codigo"></label></li>
                    <li><label id="version" class="label label-default"></label></li>
                    <li><label>Estado:  <span id="des_estado"></span></label></li>
                    <li><i id="cronograma" class="fas fa-calendar-alt blue" id="basic-addon2" 
                        data-toggle="tooltip" data-placement="bottom" title="Cronograma generado" ></i></li>
                    <li><i id="cronoval" class="fas fa-donate green" id="basic-addon2" 
                        data-toggle="tooltip" data-placement="bottom" title="Cronograma Valorizado generado" ></i></li>
                </ol>
            </legend>
            <div class="row">
                <label id="estado" class='oculto'></label>
                <input type="text" class="oculto" name="id_presupuesto" primary="ids">
                <input type="text" class="oculto" name="id_empresa">
                {{-- 2 Presupuesto Ejecución --}}
                <input type="text" class="oculto" name="id_tp_presupuesto" value="2">
                <input type="text" class="oculto" name="elaborado_por">
                <input type="text" class="oculto" name="id_presup">

                <div class="col-md-6">
                    <h5>Seleccione el Proyecto</h5>
                    <div class="input-group-okc">
                        <input class="oculto" name="id_proyecto" >
                        <input type="text" class="form-control" aria-describedby="basic-addon2" 
                            readonly name="descripcion_proy" disabled="true">
                        <div class="input-group-append">
                            <button type="button" class="input-group-text activation btn btn-primary" id="basic-addon2"  id="basic-addon2" data-toggle="tooltip" 
                                data-placement="bottom" title="Buscar Proyecto"
                                onClick="proyectoModal();">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <h5>Moneda</h5>
                    <div class="input-group-okc">
                        <select class="form-control group-elemento activation" name="moneda" disabled="true">
                            <option value="0">Elija una opción</option>
                            @foreach ($monedas as $mon)
                                <option value="{{$mon->id_moneda}}">{{$mon->descripcion}}</option>
                            @endforeach
                        </select>
                        <div class="input-group-append">
                            <button type="button" class="input-group-text  btn btn-success " id="basic-addon2" data-toggle="tooltip" 
                                data-placement="bottom" title="Actualizar Importes" 
                                onClick="actualiza_moneda();">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <h5>Fecha Emisión</h5>
                    <input type="date" name="fecha_emision" class="form-control activation" value="<?=date('Y-m-d');?>"  disabled="true"/>
                </div>
                <div class="col-md-2">
                    <h5>Tipo de Cambio</h5>
                    <input type="number" name="tipo_cambio" class="form-control activation"/>
                </div>
                {{-- <div class="col-md-1">
                    <h5>Copiar</h5>
                    <button type="button" class="btn btn-warning" data-toggle="tooltip" 
                        data-placement="bottom" title="Copiar Partidas de un Presupuesto" 
                        onClick="presintCopiaModal();"><i class="fas fa-file-alt"></i></button>
                </div> --}}
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-md-12">
            <div id="tab-preseje">
                <ul class="nav nav-tabs" id="myTab">
                    <li class="active"><a type="#par">Partidas</a></li>
                    <li class=""><a type="#cd">Costos Directos</a></li>
                    <li class=""><a type="#ci">Costos Indirectos</a></li>
                    <li class=""><a type="#gg">Gastos Generales</a></li>
                    <li class=""><a type="#est">Estructura</a></li>
                </ul>
                <div class="content-tabs">
                    <section id="par" hidden>
                        <form id="form-par" type="register">
                            {{-- <div class="row">
                                <div class="col-md-12">
                                    <br/>
                                    <input class="oculto" name="anulados" >
                                    <table  width="100%">
                                        <tbody>
                                            <tr>
                                                <td width="50%">
                                                    <label>Seleccione un A.C.U.</label>
                                                    <input class="oculto" name="id_partida">
                                                    <input class="oculto" name="cod_compo">
                                                    <div style="width: 100%; display:flex;">
                                                        <div style="width:90%; display:flex;">
                                                            <input class="oculto" name="id_cu_partida">
                                                            <input type="text" name="cod_cu" class="form-control input-sm" readOnly style="width:70px;"/>
                                                            <input type="text" name="des_cu" class="form-control input-sm" readOnly/>
                                                        </div>
                                                        <div style="width:7%;">
                                                            <span class="input-group-addon input-sm btn-primary" style="cursor:pointer; height: 31px;" 
                                                                onClick="acuPartidaModal('cd');">
                                                                <i class="fas fa-search"></i>
                                                            </span>
                                                        </div>
                                                        <div style="width:7%;">
                                                            <span class="input-group-addon input-sm btn-success" style="cursor:pointer; height: 31px;" 
                                                                onClick="acuParticaCreateModal();">
                                                                <i class="fas fa-plus"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <label>Unidad</label>
                                                    <input type="text" name="id_unid_medida" hidden/>
                                                    <input type="text" name="unid_medida" readOnly class="form-control input-sm"/>
                                                </td>
                                                <td>
                                                    <label>Cantidad</label>
                                                    <input type="number" name="cantidad" class="form-control input-sm" 
                                                        onChange="calculaPrecioTotalPartida();"/>
                                                </td>
                                                <td>
                                                    <label>Unitario</label>
                                                    <input type="number" name="precio_unitario" readOnly class="form-control input-sm" 
                                                        onChange="calculaPrecioTotalPartida();"/>
                                                </td>
                                                <td>
                                                    <label>Total</label>
                                                    <input type="number" name="precio_total_partida" readOnly class="form-control input-sm" />
                                                </td>
                                                <td>
                                                    <label>Sistema</label>
                                                    <select class="form-control group-elemento" name="id_sistema" >
                                                        <option value="0">Elija una opción</option>
                                                        @foreach ($sistemas as $sis)
                                                            <option value="{{$sis->id_sis_contrato}}">{{$sis->descripcion}}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <label>Add</label>
                                                    <div style="display:flex;">
                                                        <button type="button" class="btn btn-success" id="basic-addon2" data-toggle="tooltip" 
                                                            data-placement="bottom" title="Guardar Partida" onClick="guardar_partida_cd();">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div> --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                                        id="listaAcusCD"  style="margin-top:10px;">
                                        <thead>
                                            <tr>
                                                <th width="5%"></th>
                                                <th>Código</th>
                                                <th width="40%">Descripción</th>
                                                <th>Unid.Med</th>
                                                <th width="70">Cantidad</th>
                                                <th width="100">Unitario</th>
                                                <th width="100">Total</th>
                                                <th width="150">Sistema</th>
                                                <th width="70">
                                                <i class="fas fa-plus-square icon-tabla green boton" 
                                                    data-toggle="tooltip" data-placement="bottom" 
                                                    title="Agregar Título" onClick="agregar_componente_cd();"></i>
                                                </th>
                                                <th hidden>codPadre</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr class="blue info" style="font-size: 14px;">
                                                <td colSpan="5"></td>
                                                <td class="right"><label name="simbolo"></label></td>
                                                <td class="right"><label id="total_acus_cd"></label></td>
                                                <td colSpan="2"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </section>
                    <section id="cd" hidden>
                        <form id="form-cd" type="register">
                            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%"
                                id="listaCD">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Descripción</th>
                                        <th>Unidad</th>
                                        <th>Cantidad</th>
                                        <th>P.Unit</th>
                                        <th width="15%">P.Parcial</th>
                                        <th>
                                        <i class="fas fa-sync-alt icon-tabla orange boton" 
                                            data-toggle="tooltip" data-placement="bottom" 
                                            title="Refrescar Totales" onClick="refresh_cd();"></i>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </form>
                    </section>
                    <section id="ci" hidden>
                        <form id="form-ci" type="register">
                            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%"
                                id="listaCI">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Código</th>
                                        <th>Descripción</th>
                                        <th>Unidad</th>
                                        <th>Cantidad</th>
                                        <th>P.Unit</th>
                                        <th>Particip.</th>
                                        <th>Tiempo</th>
                                        <th>Veces</th>
                                        <th>P.Parcial</th>
                                        <th>SubTotal</th>
                                        <th width="10%">
                                            <i class="fas fa-plus-square icon-tabla green boton" 
                                            data-toggle="tooltip" data-placement="bottom" 
                                            title="Agregar Título" onClick="agregar_componente_ci();"></i>
                                            <i class="fas fa-arrow-alt-circle-down icon-tabla orange boton" 
                                            data-toggle="tooltip" data-placement="bottom" 
                                            title="Agregar Titulos Base" onClick="crear_titulos_ci();"></i>
                                        </th>
                                        <th hidden>padre</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </form>
                    </section>
                    <section id="gg" hidden>
                        <form id="form-gg" type="register">
                            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%"
                                id="listaGG">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Código</th>
                                        <th>Descripción</th>
                                        <th>Unidad</th>
                                        <th>Cantidad</th>
                                        <th>P.Unit</th>
                                        <th>Particip.</th>
                                        <th>Tiempo</th>
                                        <th>Veces</th>
                                        <th>P.Parcial</th>
                                        <th>SubTotal</th>
                                        <th width="10%">
                                            <i class="fas fa-plus-square icon-tabla green boton" 
                                            data-toggle="tooltip" data-placement="bottom" 
                                            title="Agregar Título" onClick="agregar_componente_gg();"></i>
                                            <i class="fas fa-arrow-alt-circle-down icon-tabla orange boton" 
                                            data-toggle="tooltip" data-placement="bottom" 
                                            title="Agregar Titulos Base" onClick="crear_titulos_gg();"></i>
                                        </th>
                                        <th hidden>padre</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </form>
                    </section>
                    <section id="est" hidden>
                        <form id="form-est" type="register">
                            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%"
                                id="listaEstructura" style="font-size: 13px;">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Descripción</th>
                                        {{-- <th>Imp.Base</th> --}}
                                        <th class="right">Imp.Total
                                            <i class="fas fa-file-download icon-tabla orange boton" 
                                            data-toggle="tooltip" data-placement="bottom" 
                                            title="Generar Estructura" onClick="generar_estructura();"></i>
                                            <i class="fas fa-trash icon-tabla red boton" 
                                            data-toggle="tooltip" data-placement="bottom" 
                                            title="Anular Estructura" onClick="anular_estructura();"></i>
                                            <i class="fas fa-file-excel icon-tabla green boton"
                                            data-toggle="tooltip" data-placement="bottom" 
                                            title="Exportar a Excel" onClick="exportar_presupuesto();"></i>
                                        </th>
                                        <th hidden>padre</th>
                                        {{-- <th>Acción</th> --}}
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <form id="form-totales" type="register">
            <div class="col-md-6"></div>
            <div class="col-md-5">
                <table class="tabla-totales" width="100%">
                    <tbody>
                        <tr>
                            <td width="50%">Costo Directo</td>
                            <td width="20%" class="right"><label name="simbolo"></label></td>
                            <td><input type="number" name="total_costo_directo" disabled="true" value="0"/></td>
                        </tr>
                        <tr>
                            <td>Costo Indirecto</td>
                            <td class="right"><label name="simbolo"></label></td>
                            <td><input type="number" name="total_ci" disabled="true" value="0"/></td>
                        </tr>
                        <tr>
                            <td>Gastos Generales</td>
                            <td class="right"><label name="simbolo"></label></td>
                            <td><input type="number" name="total_gg" disabled="true" value="0"/></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td><strong>SubTotal</strong></td>
                            <td class="right"><label name="simbolo"></label></td>
                            <td><input type="number" name="sub_total" disabled="true" value="0"/></td>
                        </tr>
                        <tr>
                            <td style="border-top:0px;">IGV</td>
                            <td style="border-top:0px;">
                                <input type="number" class="porcen" name="porcentaje_igv" disabled="true" value="0"/>
                                <label>%</label>
                            </td>
                            <td style="border-top:0px;"><input type="number" name="total_igv" disabled="true" value="0"/></td>
                        </tr>
                        <tr>
                            <td><strong>Total Presupuestado</strong></td>
                            <td class="right"><label name="simbolo"></label></td>
                            <td><input type="number" name="total_presupuestado" disabled="true" value="0"/></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </form>
    </div>
</div>
@include('proyectos.proyecto.proyectoModal')
@include('proyectos.presupuesto.presejeModal')
@include('proyectos.presupuesto.presintCopiaModal')
@include('proyectos.presupuesto.partidaCDCreate')
@include('proyectos.presupuesto.partidaCICreate')
@include('proyectos.presupuesto.partidaGGCreate')
@include('proyectos.presupuesto.verAcu')
@include('proyectos.presupuesto.verPartidaInsumo')
@include('proyectos.variables.add_unid_med')
@include('proyectos.acu.acuPartidaModal')
@include('proyectos.acu.acuPartidaCreate')
@include('proyectos.acu.acuCreate')
@include('proyectos.acu.acuModal')
@include('proyectos.insumo.insumoModal')
@include('proyectos.insumo.insumoCreate')
@include('proyectos.insumo.insumoPrecioModal')
@include('proyectos.presupuesto.presLeccion')
@endsection

@section('scripts')
    <script src="{{ asset('datatables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables/DataTables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('datatables/Buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('datatables/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('datatables/JSZip/jszip.min.js') }}"></script>
    <script src="{{ asset('template/plugins/moment.min.js') }}"></script>

    <script src="{{ asset('js/proyectos/presupuesto/preseje.js')}}"></script>
    <script src="{{ asset('js/proyectos/presupuesto/presejeModal.js')}}"></script>
    <script src="{{ asset('js/proyectos/presupuesto/presejeCopiaModal.js')}}"></script>
    <script src="{{ asset('js/proyectos/presupuesto/partidaCDCreate.js')}}"></script>
    <script src="{{ asset('js/proyectos/presupuesto/verAcu.js')}}"></script>
    <script src="{{ asset('js/proyectos/presupuesto/verPartidaInsumo.js')}}"></script>
    <script src="{{ asset('js/proyectos/variables/add_unid_med.js')}}"></script>
    
    <script src="{{ asset('js/proyectos/presupuesto/partidaCICreate.js')}}"></script>
    <script src="{{ asset('js/proyectos/presupuesto/partidaGGCreate.js')}}"></script>
    <script src="{{ asset('js/proyectos/presupuesto/compo_cd.js')}}"></script>
    <script src="{{ asset('js/proyectos/presupuesto/compo_ci.js')}}"></script>
    <script src="{{ asset('js/proyectos/presupuesto/compo_gg.js')}}"></script>
    <script src="{{ asset('js/proyectos/acus/acuPartidaModal.js')}}"></script>
    <script src="{{ asset('js/proyectos/acus/acuPartidaCreate.js')}}"></script>
    <script src="{{ asset('js/proyectos/acus/acuCreate.js')}}"></script>
    <script src="{{ asset('js/proyectos/acus/acuModal.js')}}"></script>
    <script src="{{ asset('js/proyectos/insumos/insumoModal.js')}}"></script>
    <script src="{{ asset('js/proyectos/insumos/insumoPrecioModal.js')}}"></script>
    <script src="{{ asset('js/proyectos/insumos/insumoCreate.js')}}"></script>
    <script src="{{ asset('js/proyectos/proyecto/proyectoModal.js')}}"></script>
    <script src="{{ asset('js/proyectos/presupuesto/presLeccion.js')}}"></script>
    <script>
    $(document).ready(function(){
        seleccionarMenu(window.location);
    });
    </script>
@endsection