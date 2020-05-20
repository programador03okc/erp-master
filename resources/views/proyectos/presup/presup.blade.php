@include('layout.head')
@include('layout.menu_proyectos')
@include('layout.body')
<div class="page-main" type="presup">
    <legend class="mylegend">
        <h2>Presupuesto</h2>
        <ol class="breadcrumb">
            <li><label id="codigo"></label></li>
        </ol>
    </legend>
    <form id="form-presup" type="register" form="formulario">
        <div class="row">
            <input type="hidden" name="_token" value="{{csrf_token()}}" id="token">
            <input type="text" class="oculto" name="id_presup" primary="ids">
            {{-- <input type="text" class="oculto" name="id_empresa"> --}}
            {{-- <input type="text" class="oculto" name="id_grupo"> --}}
            {{-- <input type="text" class="oculto" name="elaborado_por"> --}}
            <div class="col-md-2">
                <h5>Fecha Emisión</h5>
                <input type="date" class="form-control activation" name="fecha_emision" value="<?=date('Y-m-d');?>"  disabled="true"/>
            </div>
            <div class="col-md-10">  
                <h5>Descripción</h5>
                <input type="text" class="form-control activation" name="descripcion"/>
            </div>
            {{-- <div class="col-md-3">
                <h5>Empresa-Sede</h5>
                <input class="oculto" name="id_empresa"/>
                <select class="form-control activation js-example-basic-single" 
                name="id_sede" disabled="true" onChange="cargar_grupos();">
                    <option value="0">Elija una opción</option>
                    @foreach ($sedes as $tp)
                        <option value="{{$tp->id_sede}}">{{$tp->razon_social}} - {{$tp->codigo}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <h5>Grupo</h5>
                <select class="form-control activation" name="id_grupo" disabled="true">
                    <option value="0">Elija una opción</option>
                </select>
            </div> --}}
        </div>
        <div class="row">
            <div class="col-md-4">
                <h5 id="fecha_registro">Fecha Registro: <label></label></h5>
            </div>
            <div class="col-md-5">
                <h5 id="responsable">Registrado por: <label></label></h5>
            </div>
            <div class="col-md-3">
                <input type="text" name="id_estado" hidden/>
                <h5 id="des_estado">Estado: <label></label></h5>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-md-12">
            <table class="mytable table table-condensed table-bordered table-okc-view" width="100%"
                id="listaPresupuesto">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Relacionado</th>
                        <th width="15%">
                            <i class="fas fa-clone icon-tabla green boton" 
                            data-toggle="tooltip" data-placement="bottom" 
                            title="Agregar Título" onClick="agregar_primer_titulo();"></i>
                            {{-- <i class="fas fa-archive icon-tabla orange boton" 
                            data-toggle="tooltip" data-placement="bottom" 
                            title="Agregar ACU" onClick="agregar_acus_cd();"></i> --}}
                        </th>
                        <th hidden>padre</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@include('proyectos.presEstructura.presEstructuraModal')
@include('proyectos.presEstructura.partidaEstructura')
@include('proyectos.presEstructura.pardetModal')
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/proyectos/presupuesto/presEstructura.js')}}"></script>
<script src="{{('/js/proyectos/presupuesto/presEstructuraModal.js')}}"></script>
<script src="{{('/js/proyectos/presupuesto/partidaEstructura.js')}}"></script>
<script src="{{('/js/proyectos/presupuesto/pardetModal.js')}}"></script>
@include('layout.fin_html')