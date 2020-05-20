@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<fieldset>
    <legend><h2>Busqueda Avanzada de Trabajadores</h2></legend>
    <div class="row">
        <div class="col-md-3">
            <h5>Empresa</h5>
            <select id="id_empresa" class="form-control input-sm" onChange="cambiarEmpresa(this.value);">
                <option value="0" selected disabled>Elija una opcion</option>
                @foreach ($empresa as $empresa)
                    <option value="{{$empresa->id_empresa}}">{{$empresa->razon_social}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <h5>Sede</h5>
            <select id="id_sede" class="form-control input-sm" onChange="cambiarSede(this.value);">
                <option value="" selected disabled>Elija una opcion</option>
            </select>
        </div>
        <div class="col-md-3">
            <h5>Grupo</h5>
            <select id="id_grupo" class="form-control input-sm">
                <option value="" selected disabled>Elija una opcion</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-sm bg-olive" style="margin-top: 34px;" onclick="crearReporteGrupoTrab();">
                <i class="fa fa-search"></i> Buscar
            </button>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="group-table">
                <table class="table table-striped table-bordered table-okc-view" id="my-report-table" width="100%"></table>
            </div>
        </div>
    </div>
</fieldset>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/reportes/reporte_rrhh.js')}}"></script>
@include('layout.fin_html')