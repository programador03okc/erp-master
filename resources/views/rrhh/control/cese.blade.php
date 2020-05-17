@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')
<div class="page-main" type="cese">
    <legend><h2>Cese del Personal</h2></legend>
    <form id="form-cese" type="register" form="formulario">
        <input type="hidden" name="id_cese" primary="ids">
        <div class="row">
            <div class="col-md-2">
                <h5>Buscar DNI</h5>
                <input type="hidden" class="form-control" name="id_trabajador">
                <div class="input-group-okc">
                    <input type="text" class="form-control" name="nro_documento" placeholder="Ingrese DNI" aria-describedby="basic-addon1">
                    <div class="input-group-append">
                        <button type="button" class="input-group-text" id="basic-addon1" onClick="buscarPersona();">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <h5>Nombre del trabajador</h5>
                <input type="text" class="form-control" name="datos_trabajador" disabled="true">
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <h5>Fecha</h5>
                <input type="date" class="form-control activation" name="fecha_cese" disabled="true">
            </div>
            <div class="col-md-5">
                <h5>Motivo Baja</h5>
                <select class="form-control activation" name="id_baja" disabled="true">
                    <option value="0" selected disabled>Elija una opción</option>
                    @foreach ($baja as $baja)
                        <option value="{{$baja->id_baja}}">{{$baja->descripcion}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>
</div>

@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/rrhh/control/cese.js')}}"></script>
@include('layout.fin_html')