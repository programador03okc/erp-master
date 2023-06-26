let page;
// $(document).ajaxStart(function () {
// 	Pace.restart();
// });
$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
});

$(document).ready(function () {
	// $(":file").filestyle();
	$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
		checkboxClass: 'icheckbox_flat-green',
		radioClass: 'iradio_flat-green'
	});
	// $('.js-example-basic-single').select2();

	page = $('.page-main').attr('type');
	var form = $('.page-main form[type=register]').attr('id');
	if (page == 'asistencia') {
		$('.sidebar-mini').addClass('sidebar-collapse');
	} else if (page == 'datos_rrhh') {
		$('.sidebar-mini').addClass('sidebar-collapse');
	} else if (page == 'planilla') {
		$('.sidebar-mini').addClass('sidebar-collapse');
	}

	// Para los tabs
	$('.page-main section form').removeAttr('type');
	$("#tab-" + page + " section:first").attr('hidden', false);
	$("#tab-" + page + " section:first form").attr('type', 'register');

	$('.mytable').css('width', '100%');

	changeStateInput(form, true);
	changeStateButton('inicio');

	$('.btn-okc').on('click', function () {
		var forms = $('.page-main form[form=formulario]').attr('id');
		var frm_active = $('.page-main form[type=register]').attr('id');
		if (frm_active == undefined) {
			var frm_active = $('.page-main form[type=edition]').attr('id');
		}
		var element = $(this).attr('id');

		switch (element) {
			case 'btnNuevo':
				if (page !== 'ubicacion') {
					clearForm(forms);
				}
				changeStateInput(forms, false);
				changeStateButton('nuevo');
				if (page == 'guia_compra') {
					nuevo_guia_compra();
				}
				else if (page == 'guia_venta') {
					nuevo_guia_venta();
				}
				else if (page == 'doc_compra') {
					nuevo_doc_compra();
				}
				else if (page == 'doc_venta') {
					nuevo_doc_venta();
				}
				else if (page == 'transformacion') {
					nuevo_transformacion();
				}
				// else if (page == 'producto'){
				// 	console.log('page:'+page);
				// 	nuevo_producto();
				// }
				else if (page == 'tp_combustible') {
					nuevo_tp_combustible();
				}
				else if (page == 'equi_sol') {
					nuevo_equi_sol();
				}
				else if (page == 'crear-orden-requerimiento') {
					nuevaOrden();
					// document.querySelector("div[id='group-migrar-oc-softlink']").classList.remove("oculto");


				}
				else if (page == 'requerimiento') {
					const requerimientoModel = new RequerimientoModel();
					const requerimientoController = new RequerimientoCtrl(requerimientoModel);
					const requerimientoView = new RequerimientoView(requerimientoController);
					requerimientoView.RestablecerFormularioRequerimiento();
					document.getElementsByName("btn-adjuntos-requerimiento")[0].removeAttribute('disabled');
					document.querySelector("form[id='form-requerimiento']").setAttribute('type', 'register');

				}
				else if (page == 'proveedores') {
					nuevo(forms);
				}
				else if (page == 'subCategoria') {
					$('[name=id_tipo_producto]').attr('disabled', false);
				}
				else if (page == 'presint') {
					nuevo_presint();
				}
				else if (page == 'cronoint') {
					nuevo_cronoint();
				}
				else if (page == 'cronovalint') {
					nuevo_cronovalint();
				}
				else if (page == 'preseje') {
					nuevo_preseje();
				}
				else if (page == 'cronoeje') {
					nuevo_cronoeje();
				}
				else if (page == 'cronopro') {
					nuevo_cronopro();
				}
				else if (page == 'cronoval') {
					nuevo_cronoval();
				}
				else if (page == 'cronovaleje') {
					nuevo_cronovaleje();
				}
				else if (page == 'cronovalpro') {
					nuevo_cronovalpro();
				}
				else if (page == 'valorizacion') {
					nueva_valorizacion();
				}
				else if (page == 'presEstructura') {
					nuevo_presEstructura();
				}
				else if (page == 'propuesta') {
					nuevo_propuesta();
				}
				else if (page == 'prorrateo') {
					nuevo_prorrateo();
				}

				break;
			case 'btnGuardar':
				var data = $("#" + forms).serialize();
				var action = $("#" + forms).attr('type');
				// console.log('forms '+forms);
				// console.log('frm_active '+frm_active);
				eventRegister(page, data, action, frm_active);

				if (forms !== "form-equi_cat" && forms !== "form-equi_sol" && forms !== "form-equi_tipo"
					&& forms !== "form-mtto" && forms !== "form-tp_combustible" && forms !== "form-almacenes"
					&& forms !== "form-categoria" && forms !== "form-subCategoria" && forms !== "form-clasificacion"
					&& forms !== "form-producto" && forms !== "form-requerimiento" && forms !== "form-crear-orden-requerimiento" && forms !== "form-general"
					&& forms !== "form-doc_venta" && forms !== "form-presint" && forms !== "form-preseje"
					&& forms !== "form-cronopro" && forms !== "form-cronoeje" && forms !== "form-cronoint"
					&& forms !== "form-cronovalint" && forms !== "form-cronovaleje") {
					changeStateButton('guardar');
					$('#' + forms).attr('type', 'register');
					changeStateInput(frm_active, true);
				}
				break;
			case 'btnEditar':
				if (page == 'equi_sol') {
					edit_equi_sol();
				}
				else {
					changeStateInput(frm_active, false);
					changeStateButton('editar');
					$('#' + forms).attr('type', 'edition');
					// console.log(page);

					if (page == 'requerimiento') {
						const requerimientoModel = new RequerimientoModel();
						const requerimientoController = new RequerimientoCtrl(requerimientoModel);
						const requerimientoView = new RequerimientoView(requerimientoController);
						requerimientoView.editRequerimiento();
					}
					else if (page == 'cuadro_comparativo') {
						editValorizaciones();
					}
					// else if (page == 'subCategoria') {
					// 	$('[name=id_tipo_producto]').attr('disabled', true);
					// }
					else if (page == 'guia_venta') {
						$('[name=modo]').val('edicion');
					}
					else if (page == 'doc_compra') {
						editar_doc_compra();
					}
					else if (page == 'crear-orden-requerimiento') {
						changeReadInputDetails(frm_active, false);

						editarOrden();

					}
				}
				break;
			case 'btnAnular':
				var ids = $("#" + forms + ' input[primary="ids"]').val();
				Swal.fire({
					title: '¿Esta seguro que desea anular?',
					text: "No podrás revertir esto.",
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					cancelButtonText: 'Cancelar',
					confirmButtonText: 'Si, anular'

				}).then((result) => {
					if (result.isConfirmed) {
						if (ids == undefined) {
							ids = $("#" + frm_active + ' input[primary="ids"]').val();
						}
						anularRegister(page, ids, frm_active);
						changeStateInput(frm_active, true);
					}
				});
				break;
			case 'btnHistorial':
				changeStateButton('historial');
				openModal(page, frm_active);
				break;
			case 'btnCancelar':
				$('#' + forms).attr('type', 'register');
				changeStateInput(forms, true);
				changeStateButton('cancelar');
				clearForm(forms);
				if (page == 'requerimiento') {
					const requerimientoModel = new RequerimientoModel();
					const requerimientoController = new RequerimientoCtrl(requerimientoModel);
					const requerimientoView = new RequerimientoView(requerimientoController);
					requerimientoView.cancelarRequerimiento();

				}
				else if (page == 'cotizacion') {
					document.getElementById('btnNuevo').setAttribute("disabled", "true");
					document.getElementById('btnGuardar').setAttribute("disabled", "true");
				}
				else if (page == 'proveedores') {

				}
				// else if (page == 'subCategoria') {
				// 	$('[name=id_tipo_producto]').attr('disabled', true);
				// }
				else if (page == 'crear-orden-requerimiento') {
					var reqTrueList = JSON.parse(sessionStorage.getItem('reqCheckedList'));
					cancelarOrden()
					if (reqTrueList != null && (reqTrueList.length > 0)) {
						window.location.reload();

					}
				}
				break;
			case 'btnCopiar':
				// console.log('copiar'+page);
				if (page == 'requerimiento') {
					copiarDocumento();
				}
				else if (page == 'presint') {
					presintCopiaModal();
				}
				else if (page == 'propuesta') {
					copiar_partidas_presint();
				}
				break;
		}
	});
});

function resizeSide() {
	var wrapper = document.getElementById("wrapper-okc");
	var altura;
	if (page == 'guia_compra' || page == 'guia_venta' ||
		page == 'doc_compra' || page == 'doc_venta') {
		altura = wrapper.offsetHeight + 400;
		// console.log(altura);
	} else {
		altura = wrapper.offsetHeight + 100;
		// console.log(altura);
	}
	$('.sidebar').css('min-height', altura + 'px');
}

function changeStateInput(element, state) {
	var evalu = $("#" + element).attr('type');
	if (evalu == 'register' || 'edition') {
		$("#" + element + " .activation").attr('disabled', state);
	}
}
function changeReadInputDetails(element, state) {
	var evalu = $("#" + element).attr('type');
	if (evalu == 'register' || 'edition') {
		$("#" + element + " .activation").attr('readOnly', state);
	}
}

function changeStateButton(type) {
	switch (type) {
		case 'nuevo':
			$('#btnNuevo').attr('disabled', true);
			$('#btnGuardar').attr('disabled', false);
			$('#btnEditar').attr('disabled', true);
			$('#btnAnular').attr('disabled', true);
			$('#btnHistorial').attr('disabled', true);
			$('#btnCancelar').attr('disabled', false);
			break;
		case 'guardar':
			$('#btnNuevo').attr('disabled', false);
			$('#btnGuardar').attr('disabled', true);
			$('#btnEditar').attr('disabled', false);
			$('#btnAnular').attr('disabled', false);
			$('#btnHistorial').attr('disabled', false);
			$('#btnCancelar').attr('disabled', true);
			break;
		case 'editar':
			$('#btnNuevo').attr('disabled', true);
			$('#btnGuardar').attr('disabled', false);
			$('#btnEditar').attr('disabled', true);
			$('#btnAnular').attr('disabled', true);
			$('#btnHistorial').attr('disabled', true);
			$('#btnCancelar').attr('disabled', false);
			break;
		case 'anular':
			$('#btnNuevo').attr('disabled', false);
			$('#btnGuardar').attr('disabled', true);
			$('#btnEditar').attr('disabled', true);
			$('#btnAnular').attr('disabled', true);
			$('#btnHistorial').attr('disabled', false);
			$('#btnCancelar').attr('disabled', true);
			break;
		case 'historial':
			$('#btnNuevo').attr('disabled', false);
			$('#btnGuardar').attr('disabled', true);
			$('#btnEditar').attr('disabled', false);
			$('#btnAnular').attr('disabled', false);
			$('#btnHistorial').attr('disabled', false);
			$('#btnCancelar').attr('disabled', true);
			break;
		case 'cancelar':
			$('#btnNuevo').attr('disabled', false);
			$('#btnGuardar').attr('disabled', true);
			$('#btnEditar').attr('disabled', true);
			$('#btnAnular').attr('disabled', true);
			$('#btnHistorial').attr('disabled', false);
			$('#btnCancelar').attr('disabled', true);
			break;
		case 'inicio':
			$('#btnNuevo').attr('disabled', false);
			$('#btnGuardar').attr('disabled', true);
			$('#btnEditar').attr('disabled', true);
			$('#btnAnular').attr('disabled', true);
			$('#btnHistorial').attr('disabled', false);
			$('#btnCancelar').attr('disabled', true);
			break;
		default:
			$('#btnNuevo').attr('disabled', true);
			$('#btnGuardar').attr('disabled', true);
			$('#btnEditar').attr('disabled', true);
			$('#btnAnular').attr('disabled', true);
			$('#btnHistorial').attr('disabled', true);
			$('#btnCancelar').attr('disabled', true);
			break;
	}
}
