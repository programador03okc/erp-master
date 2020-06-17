@include('layout.head')
@include('layout.menu_almacen')
@include('layout.body_sin_option')
<section class="content">

    <div class="row">
        <div class="col-lg-3 col-xs-6">
              <!-- small box -->
            <div class="small-box bg-blue">
                <div class="icon">
                    <i class="fas fa-truck"></i>
                    </div>
                    <div class="inner">
                        <h3>{{$cantidad_despachos_pendientes}}</h3>
                        <p style="font-size:15px;display:flex;width:20px;">Despachos Pendientes</p>
                    </div>
                    <a href="ordenesDespacho" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
                <!-- </div> -->
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-orange">
                <div class="icon">
                    <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <div class="inner">
                        <h3>{{$cantidad_ingresos_pendientes}}</h3>
                        <p style="font-size:15px;display:flex;width:20px;">Ingresos Pendientes</p>
                    </div>
                    <a href="ordenesPendientes" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
                <!-- </div> -->
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-teal">
                <div class="icon">
                    <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <div class="inner">
                        <h3>{{$cantidad_salidas_pendientes}}</h3>
                        <p style="font-size:15px;display:flex;width:20px;">Salidas Pendientes</p>
                    </div>
                    <a href="despachosPendientes" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
                <!-- </div> -->
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
                <div class="icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div class="inner">
                    <h3>{{$cantidad_transferencias_pendientes}}</h3>
                    <p style="font-size:15px;display:flex;width:20px;">Transferencias Pendientes</p>
                </div>
                <a href="listar_transferencias" class="small-box-footer">Ir <i class="fa fa-arrow-circle-right"></i></a>
                <!-- </div> -->
            </div>
        </div>
    </div>
</section>

@include('layout.footer')
@include('layout.scripts')
@include('layout.fin_html')