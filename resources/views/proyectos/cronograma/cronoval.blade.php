@include('layout.head')
@include('layout.menu_proyectos')
@include('layout.body')
<div class="page-main" type="cronoval">
    <form id="form-cronoval" type="register" form="formulario">
        <div class="thumbnail" style="padding-left: 10px;padding-right: 10px;">
            <legend class="mylegend">
                <h2>Cronograma Valorizado de Ejecución</h2>
                <ol class="breadcrumb" style="background-color: white;">
                    <li><label id="codigo"></label></li>
                    <li>Duración Total: <label id="duracion"></label></li>
                    <li>Sub Total: <label id="importe"></label></li>
                    <li><i class="fas fa-file-excel icon-tabla green boton"
                        data-toggle="tooltip" data-placement="bottom" 
                        title="Exportar a Excel" onclick="exportTableToExcel('listaPartidas','CronogramaValorizado')"></i></li>
                </ol>
            </legend>
            <div class="row">
                <div class="col-md-1">
                    <h5>Presupuesto:</h5>
                </div>
                <div class="col-md-6">
                    <div class="input-group-okc">
                        <input type="text" class="oculto" name="id_presupuesto" primary="ids">
                        <input type="text" class="oculto" name="modo">
                        <input type="text" class="form-control" aria-describedby="basic-addon2" 
                            readonly name="nombre_opcion" disabled="true">
                        {{-- <div class="input-group-append">
                            <button type="button" class="input-group-text btn btn-primary " id="basic-addon2" data-toggle="tooltip" 
                                data-placement="bottom" title="Buscar Presupuesto de Ejecución"
                                onClick="presejeModal('crononuevo');">
                                <i class="fa fa-search"></i>
                            </button>
                        </div> --}}
                    </div>
                </div>
                <div class="col-md-1">
                    <h5>Mostrar en:</h5>
                </div>
                <div class="col-md-2">
                    <div style="display:flex;">
                        <input type="number" class="form-control" name="numero"  disabled="true" style="width:80px;"/>
                        <select class="form-control group-elemento" name="unid_program" disabled="true" >
                            <option value="0" selected>Elija una opción</option>
                            @foreach ($unid_program as $unid)
                                <option value="{{$unid->id_unid_program}}">{{$unid->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-1">
                    <input type="button" class="form-control btn btn-success" name="btn_actualizar" disabled="true"
                    onClick="mostrar_crono_valorizado();" style="width:100px;" value="Actualizar"/>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                        id="listaPartidas" style="margin-top:10px;">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th width="40%">Descripción</th>
                                <th>Duración</th>
                                <th width="70">Montos Parciales</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot></tfoot>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>
@include('proyectos.presupuesto.presejeModal')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/proyectos/cronograma/cronoval.js')}}"></script>
<script src="{{('/js/proyectos/presupuesto/presejeModal.js')}}"></script>
@include('layout.fin_html')