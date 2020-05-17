@include('layout.head')
@include('layout.menu_proyectos')
@include('layout.body_sin_option')
<div class="page-main" type="curvas">
    <div class="thumbnail" style="padding-left: 10px;padding-right: 10px;">
        <legend class="mylegend">
            <h2>Información General de la Ejecución de Proyectos</h2>
            <ol class="breadcrumb" style="background-color: white;">
                <li><label id="nro_proyectos"></label></li>
            </ol>
        </legend>
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fas fa-tachometer-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Opciones</span>
                        <span class="info-box-text">Generadas</span>
                        <span class="info-box-number" id="opciones_generadas"></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fas fa-tachometer-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Proyectos</span>
                        <span class="info-box-text">En Ejecución</span>
                        <span class="info-box-number" id="proyectos_generados"></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fas fa-tachometer-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total</span>
                        <span class="info-box-text">Valorizado</span>
                        <span class="info-box-number" id="total_valorizado"></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fas fa-tachometer-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Saldo</span>
                        <span class="info-box-text">por cobrar</span>
                        <span class="info-box-number" id="total_saldo"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-7">
                <label>AÑO - MES : </label> <span class="label label-primary" id="anio_mes"></span>
                <table class="mytable table table-condensed table-bordered table-okc-view" width="100%" 
                    id="Proyectos" style="margin-top:10px;">
                    <thead>
                        <tr>
                            <th rowSpan="2">#</th>
                            <th rowSpan="2">D</th>
                            <th rowSpan="2">Código</th>
                            <th rowSpan="2">Plazo Mes</th>
                            <th rowSpan="2">Contrato</th>
                            <th colSpan="2" style="text-align:center;">Ejecutado</th>
                            <th colSpan="3" style="text-align:center;">Valorizado</th>
                        </tr>
                        <tr>
                            <th>Mes</th>
                            <th>Acumulado</th>
                            <th>Mes</th>
                            <th>Acumulado</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot></tfoot>
                </table>
            </div>
            <div class="col-md-5">
                <canvas id="chartProyectos" width="600" height="300"></canvas>
            </div>
        </div>
    </div>
</div>
@include('layout.footer')
@include('layout.scripts')
<script src="{{('/js/proyectos/dashboardProyectos.js')}}"></script>
@include('layout.fin_html')