function consultaSunat(){
	var ruc = $('[name=nro_documento]').val();

	if (ruc !== ''){
		var url = '/consulta_sunat';
		$('.loading').removeClass('invisible');
		$('.sunat-ico').addClass('invisible');
		$('#panel_consulta_sunat').removeClass('invisible');
		$.ajax({
		type:'GET',
		dataType: 'JSON',
		url:url,
		data:'ruc='+ruc,
		success: function(response){
			console.log(response);		
			$('.loading').addClass('invisible');
			$('.sunat-ico').removeClass('invisible');
			var data = response.data;
			// 	var nada ='nada';
				if(data.length ==0){
					alert('DNI o RUC no válido o no registrado');
				}else{
					if(data.contribuyente_estado == 'ACTIVO'){
						$('[name=estado]').addClass('label-success');
					}else{
						$('[name=estado]').addClass('label-warning');
					}
					if(data.contribuyente_estado == 'HABIDO'){
						$('[name=condicion]').addClass('label-success');
					}else{
						$('[name=condicion]').addClass('label-warning');
					}
					$('[name=numero_ruc]').text(data.ruc);
					$('[name=razon_social]').text(data.razon_social);
					$('[name=fecha_actividad]').text(data.fecha_actividad);
					$('[name=condicion]').text(data.contribuyente_condicion);
					$('[name=tipo]').text(data.contribuyente_tipo);
					$('[name=estado]').text(data.contribuyente_estado);
					$('[name=fecha_inscripcion]').text(data.fecha_inscripcion);
					$('[name=domicilio]').text(data.domicilio_fiscal);
					$('[name=direccion]').val(data.domicilio_fiscal);

					$('[name=emision]').text(data.sistema_emision);
					
					$('[name=razon_social]').val(data.razon_social);
					$('[name=estado_ruc]').val(data.contribuyente_estado);
					switch (data.contribuyente_estado) { //ESTADO RUC
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

					switch (data.contribuyente_condicion) { //CONDICION RU
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
					
			// 		var tipo = datos[4];
			// 		var id_tipo = 0;
			// 		$("[name=id_tipo_contribuyente] option").each(function(){
			// 			if ($(this).val() != "" ){
			// 				if ($(this).text() == tipo){
			// 					id_tipo = $(this).val();
			// 				}
			// 			}
			// 		});
			// 		// console.log('id_tipo:'+id_tipo);
			// 		$('[name=id_tipo_contribuyente]').val(id_tipo);
				}		
			}
		}).fail( function( jqXHR, textStatus, errorThrown ){
			console.log(jqXHR);
			console.log(textStatus);
			console.log(errorThrown);
		});
	} else {
		alert('Es necesario que ingrese un numero de RUC!');
	}
	return false;
}
