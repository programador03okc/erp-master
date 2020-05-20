<section class="sidebar">
    <div class="user-panel">
        <div class="pull-left image">
            <img src="img/user2-160x160.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
            <p>Usuario: {{ Auth::user()->nombre_corto }}</p>
            <a href="#"><i class="fa fa-circle"></i> Programador</a>
        </div>
    </div>

    <ul class="sidebar-menu">
        <li class="okc-menu-title"><label>Recursos Humanos</label><p>RH</p></li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Escalafón</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="cargo"> Cargos</a></li>
                <li><a href="persona"> Personas </a></li>
                <li><a href="postulante"> Postulantes</a></li>
                <li><a href="trabajador"> Trabajadores </a></li>
                <li><a href="derecho_hab"> Derecho Habientes </a></li>
                <li><a href="merito"> Méritos</a></li>
                <li><a href="demerito"> Deméritos</a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Control de Personal</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="periodo"> Periodo </a></li>
                <li><a href="tareo"> Tareo Diario</a></li>
                <li><a href="asistencia"> Asistencia</a></li>
                <li><a href="salidas"> Permisos/Salidas</a></li>
                <li><a href="horas_ext"> Horas Extras</a></li>
                <li><a href="prestamos"> Préstamos</a></li>
                <li><a href="vacaciones"> Vacaciones</a></li>
                <li><a href="cese"> Cese del Personal</a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Remuneraciones</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="bonificacion"> Ingresos </a></li>
                <li><a href="descuento"> Descuentos</a></li>
                <li><a href="retencion"> Retenciones</a></li>
                <li><a href="aportacion"> Aportaciones</a></li>
                <li><a href="reintegro"> Reintegro</a></li>
                <!-- <li><a href="vacaciones"> Vacaciones</a></li> -->
                <li><a href="planilla"> Planilla de Remuneraciones</a></li>
                <!-- <li><a href="grati"> Gratificación</a></li> -->
                <!-- <li><a href="cts"> CTS</a></li> -->
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Configuración RRHH</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="est_civil"> Estados Civiles </a></li>
                <li><a href="cond_derecho_hab"> Condición de D. Habientes </a></li>
                <li><a href="niv_estudios"> Niveles de Estudio</a></li>
                <li><a href="carreras"> Carreras Profesionales</a></li>
                <li><a href="tipo_trabajador"> Tipos de Trabajador</a></li>
                <li><a href="tipo_contrato"> Tipos de Contrato</a></li>
                <li><a href="modalidad"> Modalidad de Contrato</a></li>
                <li><a href="concepto_rol"> Conceptos de Rol (*) </a></li>
                <li><a href="cat_ocupacional"> Categoría Ocupacional</a></li>
                <li><a href="horario"> Horarios</a></li>
                <li><a href="tolerancia"> Tolerancia</a></li>
                <li><a href="pension"> Fondos de Pensiones</a></li>
                <li><a href="tipo_planilla"> Tipos de Planilla</a></li>
                <li><a href="tipo_merito"> Tipos de Mérito</a></li>
                <li><a href="tipo_demerito"> Tipos de Demérito</a></li>
                <li><a href="tipo_bonificacion"> Tipos de Ingresos</a></li>
                <li><a href="tipo_descuento"> Tipos de Descuentos</a></li>
                <li><a href="tipo_retencion"> Tipos de Retenciones</a></li>
                <li><a href="tipo_aportes"> Tipos de Aporte del Empleador</a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fas fa-tachometer-alt"></i> <span>Reportes</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="datos_personal"> CV Personal </a></li>
                <li><a href="busqueda_postulante"> Buscar Postulante </a></li>
                <li><a href="grupo_trabajador"> Trabajadores por Grupo </a></li>
                <li><a href="cumple"> Cumpleaños </a></li>
                <li><a href="datos_generales"> Datos Generales </a></li>
                <li><a href="reporte_afp"> Reporte Afp </a></li>
            </ul>
        </li>
    </ul>
</section>