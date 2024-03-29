<script>
$(document).ready(function() {
   getServicio();
   getReporte();
   pagination_transito(1);
});

$(document).ready(function() {
  $('#form_main_transito #servicio').on('change', function(){	
     pagination_transito(1);
  });
});

$(document).ready(function() {
  $('#form_main_transito #reporte').on('change', function(){	
     pagination_transito(1);
  });
});

$(document).ready(function() {
  $('#form_main_transito #fecha_i').on('change', function(){	
     pagination_transito(1);
  });
});

$(document).ready(function() {
  $('#form_main_transito #fecha_f').on('change', function(){	
     pagination_transito(1);
  });
});

$(document).ready(function() {
  $('#form_main_transito #bs-regis').on('keyup', function(){	
     pagination_transito(1);
  });
});

function getServicio(){
    var url = '<?php echo SERVERURL; ?>php/reportes_transito/getServicio.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){	
		    $('#form_main_transito #servicio').html("");
			$('#form_main_transito #servicio').html(data);
		}			
     });	
}

function getReporte(){
    var url = '<?php echo SERVERURL; ?>php/reportes_transito/getReporte.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#form_main_transito #reporte').html("");
			$('#form_main_transito #reporte').html(data);				
        }
     });		
}

function pagination_transito(partida){
	var servicio;
	var reporte;
	var desde = $('#form_main_transito #fecha_i').val();
	var hasta = $('#form_main_transito #fecha_f').val();
	var dato = $('#form_main_transito #bs-regis').val();
	
	if($('#form_main_transito #servicio').val() == "" || $('#form_main_transito #servicio').val() == null){
		servicio = 1;
	}else{
		servicio = $('#form_main_transito #servicio').val();
	}
	
	if($('#form_main_transito #reporte').val() == "" || $('#form_main_transito #reporte').val() == null){
		reporte = 1;
	}else{
		reporte = $('#form_main_transito #reporte').val();
	}
	
	if(reporte == 1){
		url = '<?php echo SERVERURL; ?>php/reportes_transito/paginar_transito_enviada.php';		
	}else{
		url = '<?php echo SERVERURL; ?>php/reportes_transito/paginar_transito_recibida.php';
	}

	$.ajax({
		type:'POST',
		url:url,
		data:'partida='+partida+'&desde='+desde+'&hasta='+hasta+'&servicio='+servicio+'&dato='+dato,	
		success:function(data){
			var array = eval(data);
			$('#agrega-registros').html(array[0]);
			$('#pagination').html(array[1]);			
		}
	});
	return false;	
}

function limpiar(){
	$('#unidad').html("");
	$('#medico_general').html("");
    $('#agrega-registros').html("");
	$('#pagination').html("");
    getServicio();
	getReporte();
	pagination_transito(1);
}

function reporteEXCEL(){
  if($('#form_main_transito #servicio').val()!=""){
	var servicio = $('#form_main_transito #servicio').val();
	var reporte = $('#form_main_transito #reporte').val();
	var desde = $('#form_main_transito #fecha_i').val();
	var hasta = $('#form_main_transito #fecha_f').val();
	
	if(reporte == ""){
		reporte = 1;
	}else{
		reporte = $('#form_main_transito #reporte').val();
	}
	
	if(reporte == 1){
		url = '<?php echo SERVERURL; ?>php/reportes_transito/reporteTransitoenviada.php?desde='+desde+'&hasta='+hasta+'&servicio='+servicio;
	}else{
		url = '<?php echo SERVERURL; ?>php/reportes_transito/reporteTransitorecibidas.php?desde='+desde+'&hasta='+hasta+'&servicio='+servicio;
	}
	    
	window.open(url);
}else{
	swal({
		title: "Acceso Denegado", 
		text: "No tiene permisos para ejecutar esta acción",
		type: "error", 
		confirmButtonClass: 'btn-danger'
	});	  
  }
}

function modal_eliminarTransitoRecibida(transito_id, expediente){
   if (getUsuarioSistema() == 1 || getUsuarioSistema() == 3){	
		swal({
		  title: "¿Esta seguro?",
		  text: "¿Desea eliminar el Transito Recibida para el usuario:  " + consultarNombre(expediente) + "?",
		  type: "input",
		  showCancelButton: true,
		  closeOnConfirm: false,
		  inputPlaceholder: "Comentario",
		  cancelButtonText: "Cancelar",	
		  confirmButtonText: "¡Sí, eliminar el Transito Recibido!",
		  confirmButtonClass: "btn-warning"
		}, function (inputValue) {
		  if (inputValue === false) return false;
		  if (inputValue === "") {
			swal.showInputError("¡Necesita escribir algo!");
			return false
		  }
			eliminarTransitoRecibida(transito_id, inputValue);
		});	
   }else{
		swal({
			title: "Acceso Denegado", 
			text: "No tiene permisos para ejecutar esta acción",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});					 
	}	
}

function modal_eliminarTransitoEnviada(transito_id, expediente){
   if (getUsuarioSistema() == 1 || getUsuarioSistema() == 3){
		swal({
		  title: "¿Esta seguro?",
		  text: "¿Desea eliminar el Transito Enviada para el usuario:  " + consultarNombre(expediente) + "?",
		  type: "input",
		  showCancelButton: true,
		  closeOnConfirm: false,
		  inputPlaceholder: "Comentario",
		  cancelButtonText: "Cancelar",	
		  confirmButtonText: "¡Sí, eliminar el Transito Enviada!",
		  confirmButtonClass: "btn-warning"
		}, function (inputValue) {
		  if (inputValue === false) return false;
		  if (inputValue === "") {
			swal.showInputError("¡Necesita escribir algo!");
			return false
		  }
			eliminarTransitoEnviada(transito_id, inputValue);
		});	   
   }else{
		swal({
			title: "Acceso Denegado", 
			text: "No tiene permisos para ejecutar esta acción",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});				 
	}	
}

$('#eliminar_transito_recibida #Si').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
	 e.preventDefault();
	 eliminarTransitoRecibida();		
});

$('#eliminar_transito_enviada #Si').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
	 e.preventDefault();
	 eliminarTransitoEnviada();		 
});

function eliminarTransitoRecibida(id, comentario){
  if (getUsuarioSistema() == 1 || getUsuarioSistema() == 3){	
	var url = '<?php echo SERVERURL; ?>php/reportes_transito/eliminarTransitoRecibida.php';
		
	var fecha = getFechaRegistroTransitoRecibida(id);
    var hoy = new Date();
    fecha_actual = convertDate(hoy);		
	
  if(getMes(fecha)==2){	   
		swal({
			title: "Error", 
			text: "No se puede agregar/modificar registros fuera de este periodo",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});		 
	 return false;	
  }else{	
   if ( fecha <= fecha_actual){  
	$.ajax({
      type:'POST',
	  url:url,
	  data:'id='+id+'&comentario='+comentario,
	  success: function(registro){
		 if(registro == 1){
			swal({
				title: "Success", 
				text: "Registro eliminado correctamente",
				type: "success",
				timer: 3000, //timeOut for auto-close
			});			 
			pagination_transito(1);
		 }else{	
			swal({
				title: "Error", 
				text: "Error al Eliminar el Registro",
				type: "error", 
				confirmButtonClass: 'btn-danger'
			});				 
		 }		 
		 return false;
  	  }
	});
	}else{
		swal({
			title: "Error", 
			text: "No se puede agregar/modificar registros fuera de esta fecha",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});		   
	   return false;		
	}
   }
  }else{
	swal({
		title: "Acceso Denegado", 
		text: "No tiene permisos para ejecutar esta acción",
		type: "error", 
		confirmButtonClass: 'btn-danger'
	});	 	
  }
}

function eliminarTransitoEnviada(id, comentario){
  if (getUsuarioSistema() == 1 || getUsuarioSistema() == 3){	
	var url = '<?php echo SERVERURL; ?>php/reportes_transito/eliminarTransitoEnviada.php';	
	var fecha = getFechaRegistroTransitoEnviada(id);
	
    var hoy = new Date();
    fecha_actual = convertDate(hoy);
  if(getMes(fecha)==2){
	swal({
		title: "Error", 
		text: "No se puede agregar/modificar registros fuera de este periodo",
		type: "error", 
		confirmButtonClass: 'btn-danger'
	});		 
	 return false;	
  }else{	
   if ( fecha <= fecha_actual){  
	$.ajax({
      type:'POST',
	  url:url,
	  data:'id='+id+'&comentario='+comentario,
	  success: function(registro){
		 if(registro == 1){
			swal({
				title: "Success", 
				text: "Registro eliminado correctamente",
				type: "success",
				timer: 3000, //timeOut for auto-close
			});
			pagination_transito(1);
		 }else{
			swal({
				title: "Error", 
				text: "Error al Eliminar el Registro",
				type: "error", 
				confirmButtonClass: 'btn-danger'
			});				 
		 }		 
		 return false;
  	  }
	});
	}else{
		swal({
			title: "Error", 
			text: "No se puede agregar/modificar registros fuera de este periodo",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});			
		return false;		
	}
   }
  }else{
	swal({
		title: "Acceso Denegado", 
		text: "No tiene permisos para ejecutar esta acción",
		type: "error", 
		confirmButtonClass: 'btn-danger'
	});	 
  }
}

function convertDate(inputFormat) {
  function pad(s) { return (s < 10) ? '0' + s : s; }
  var d = new Date(inputFormat);
  return [d.getFullYear(), pad(d.getMonth()+1), pad(d.getDate())].join('-');
}

function getMes(fecha){
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getMes.php';
	var resp;
	
	$.ajax({
	    type:'POST',
		data:'fecha='+fecha,
		url:url,
		async: false,
		success:function(data){	
          resp = data;			  		  		  			  
		}
	});
	return resp	;	
}

function getFechaRegistroTransitoRecibida(transito_id){
    var url = '<?php echo SERVERURL; ?>php/reportes_transito/getFechaTransitoRecibida.php';
	var fecha;
	$.ajax({
	    type:'POST',
		url:url,
		data:'transito_id='+transito_id,
		async: false,
		success:function(data){	
          fecha = data;			  		  		  			  
		}
	});
	return fecha;
}

function getFechaRegistroTransitoEnviada(transito_id){
    var url = '<?php echo SERVERURL; ?>php/reportes_transito/getFechaTransitoEnviada.php';
	var fecha;
	$.ajax({
	    type:'POST',
		url:url,
		data:'transito_id='+transito_id,
		async: false,
		success:function(data){	
          fecha = data;			  		  		  			  
		}
	});
	return fecha;	
}

$('#form_main_transito #reporte_excel').on('click', function(e){
    e.preventDefault();
    reporteEXCEL();
});

$('#form_main_transito #limpiar').on('click', function(e){
    e.preventDefault();
    limpiar();
});

function consultarNombre(pacientes_id){	
    var url = '<?php echo SERVERURL; ?>php/pacientes/getNombre.php';
	var resp;
		
	$.ajax({
	    type:'POST',
		url:url,
		data:'pacientes_id='+pacientes_id,
		async: false,
		success:function(data){	
          resp = data;			  		  		  			  
		}
	});
	return resp;	
}
</script>