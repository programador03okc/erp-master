@include('layout.head')
@include('layout.menu_admin')
@include('layout.body')
<div class="page-main" type="grupo">
    <legend><h2>Grupos</h2></legend>
    <div class="row">
        <div class="col-md-7">
            <fieldset class="group-table">
                <table class="mytable table table-hover table-condensed table-bordered table-result-form" id="listaGrupo">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Empresa</th>
                            <th>Sede</th>
                            <th>Nombre del Grupo</th>
                            <th>Código</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-md-5">
            <form id="form-grupo" type="register" form="formulario">
                <input type="hidden" name="id_grupo" primary="ids">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Empresa</h5>
                        <select class="form-control activation" name="empresa" disabled="true" onchange="buscarSede(this.value, 'nuevo', 0);">
                            <option value="0" selected disabled>Elija una empresa</option>
                            @foreach ($emp as $emp)
                                <option value="{{$emp->id_empresa}}">{{$emp->razon_social}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Sede</h5>
                        <select class="form-control activation" name="sede" disabled="true">
                            <option value="0" selected disabled>Elija una opción</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Código / Descripción</h5>
                        <div class="flexAccion">
                            <input type="text" class="form-control activation" name="codigo" disabled="true" maxlength="2" placeholder="00"
                            style="width: 15%; text-align: center;">
                            <input type="text" class="form-control activation" name="descripcion" disabled="true" placeholder="Nommbre del grupo"
                            style="width: 85%;">
                        </div>
                    </div>
                </div>
            </form>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <h5 class="text-primary"><b>LEYENDA</b></h5>
                    <ul>
                        <li class="text-success">01 - Gerencia</li>
                        <li class="text-success">02 - Proyectos</li>
                        <li class="text-success">03 - Comercial</li>
                        <li class="text-success">04 - Administración</li>
                        <li class="text-success">05 - Control Interno</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/administracion/grupo.js')}}"></script>
@include('layout.fin_html')