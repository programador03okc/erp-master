function consultaSunat(){
	var ruc = $('[name=nro_documento]').val();

	if (ruc !== ''){
		var url = '/consulta_sunat';
		$('.loading').removeClass('invisible');
		$('.sunat-ico').addClass('invisible');
		$('#panel_consulta_sunat').removeClass('invisible');
		$.ajax({
		type:'POST',
		url:url,
		data:'ruc='+ruc,
		success: function(datos_dni){
			console.log(datos_dni);		
			$('.loading').addClass('invisible');
			$('.sunat-ico').removeClass('invisible');
			var datos = eval(datos_dni);
				var nada ='nada';
				if(datos[0]==nada){
					alert('DNI o RUC no válido o no registrado');
				}else{
					if(datos[5] =='Activo' || datos[5] == 'ACTIVO'){
						$('[name=estado]').addClass('label-success');
					}else{
						$('[name=estado]').addClass('label-warning');
					}
					if(datos[3] =='Habido' || datos[3] == 'HABIDO'){
						$('[name=condicion]').addClass('label-success');
					}else{
						$('[name=condicion]').addClass('label-warning');
					}
					$('[name=numero_ruc]').text(datos[0]);
					$('[name=razon_social]').text(datos[1]);
					$('[name=fecha_actividad]').text(datos[2]);
					$('[name=condicion]').text(datos[3]);
					$('[name=tipo]').text(datos[4]);
					$('[name=estado]').text(datos[5]);
					$('[name=fecha_inscripcion]').text(datos[6]);
					$('[name=domicilio]').text(datos[7]);
					$('[name=emision]').text(datos[8]);
					
					$('[name=razon_social]').val(datos[1]);
					// $('[name=estado_ruc]').val(datos[5]);
					switch (datos[5]) { //ESTADO RUC
						case 'ACTIVO':
							$('[name=estado_ruc]').val(1);
							break;
						case 'SUSPENSION TEMPORAL':
							$('[name=estado_ruc]').val(2);
							break;
						case 'BAJA PROVISIONAL':
							$('[name=estado_ruc]').val(3);
							break;
						case 'BAJA BAJA DEFINITIVA':
							$('[name=estado_ruc]').val(4);
							break;
						case 'BAJA PROVISIONAL DE OFICIO':
							$('[name=estado_ruc]').val(5);
							break;
						case 'BAJA DEFINITIVA DE OFICIO':
							$('[name=estado_ruc]').val(6);
							break;
						default:
							break;
					}
					// $('[name=condicion_ruc]').val(datos[3]);
					switch (datos[3]) { //CONDICION RU
						case 'HABIDO':
							$('[name=condicion_ruc]').val(1);
							break;
						case 'NO HALLADO':
							$('[name=condicion_ruc]').val(2);
							break;
						case 'NO HABIDO':
							$('[name=condicion_ruc]').val(3);
							break;
						default:
							break;
					}
					$('[name=direccion_fiscal]').val(datos[7]);
					
					var tipo = datos[4];
					var id_tipo = 0;
					$("[name=id_tipo_contribuyente] option").each(function(){
						if ($(this).val() != "" ){
							if ($(this).text() == tipo){
								id_tipo = $(this).val();
							}
						}
					});
					console.log('id_tipo:'+id_tipo);
					$('[name=id_tipo_contribuyente]').val(id_tipo);
				}		
			}
		});
	} else {
		alert('Es necesario que ingrese un numero de RUC!');
	}
	return false;
}
