<?php 
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$colaborador_id = $_SESSION['colaborador_id'];
$paginaActual = $_POST['partida'];
$fechai = $_POST['fechai'];
$fechaf = $_POST['fechaf'];
$dato = $_POST['dato'];
$estado = $_POST['estado'];
$type = $_SESSION['type'];
	
//CONSULTAR PUESTO_ID	
$consultar_puesto = "SELECT puesto_id 
	FROM colaboradores 
	WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consultar_puesto) or die($mysqli->error);

$consultar_puesto2 = $result->fetch_assoc();

$puesto_id = "";

if($result->num_rows>0){
	$puesto_id = $consultar_puesto2['puesto_id'];
}

if($dato !=""){
	$where = "WHERE CAST(a.fecha_cita AS DATE) BETWEEN '$fechai' AND '$fechaf' AND a.status = '$estado' AND a.colaborador_id = '$colaborador_id' AND a.preclinica = 1 AND (p.expediente LIKE '%$dato%' OR CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.identidad LIKE '$dato%' OR p.apellido LIKE '$dato%')";
}else{
	$where = "WHERE CAST(a.fecha_cita AS DATE) BETWEEN '$fechai' AND '$fechaf' AND a.status = '$estado' AND a.colaborador_id = '$colaborador_id' AND a.preclinica = 1";	
}


$query = "SELECT p.pacientes_id AS 'pacientes_id',  a.agenda_id AS 'agenda_id', p.identidad AS 'identidad', CONCAT(p.apellido,' ',p.nombre) AS 'paciente', DATE_FORMAT(CAST(a.fecha_cita AS DATE), '%d/%m/%Y') AS 'fecha_cita', 
    a.hora AS 'hora', a.paciente AS 'tipo_paciente', p.telefono1 AS 'telefono', CONCAT(c.apellido,' ',c.nombre) AS 'colaborador', s.nombre AS 'servicio', 
	a.observacion AS 'observacion', a.comentario AS 'comentario',
   (CASE WHEN a.status = '1' THEN 'Atendido' ELSE 'Pendiente' END) AS 'estatus', CAST(a.fecha_cita AS DATE) AS 'fecha', a.colaborador_id AS 'colaborador_id', a.servicio_id AS 'servicio_id'
	FROM agenda AS a
	INNER JOIN pacientes AS p
	ON a.pacientes_id = p.pacientes_id
	INNER JOIN servicios AS s
	ON a.servicio_id = s.servicio_id
	INNER JOIN colaboradores AS c
	ON a.colaborador_id = c.colaborador_id
	".$where."
	ORDER BY a.hora, a.pacientes_id ASC";

$result = $mysqli->query($query) or die($mysqli->error);

$nroLotes = 25;
$nroProductos = $result->num_rows;
$nroPaginas = ceil($nroProductos/$nroLotes);
$lista = '';
$tabla = '';

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination('.(1).');void(0);">Inicio</a></li>';
}

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination('.($paginaActual-1).');void(0);">Anterior '.($paginaActual-1).'</a></li>';
}

if($paginaActual < $nroPaginas){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination('.($paginaActual+1).');void(0);">Siguiente '.($paginaActual+1).' de '.$nroPaginas.'</a></li>';
}

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination('.($nroPaginas).');void(0);">Ultima</a></li>';
}

if($paginaActual <= 1){
	$limit = 0;
}else{
	$limit = $nroLotes*($paginaActual-1);
}

$registro = "SELECT p.pacientes_id AS 'pacientes_id',  a.agenda_id AS 'agenda_id', p.identidad AS 'identidad', CONCAT(p.apellido,' ',p.nombre) AS 'paciente', DATE_FORMAT(CAST(a.fecha_cita AS DATE), '%d/%m/%Y') AS 'fecha_cita', 
a.hora AS 'hora', a.paciente AS 'tipo_paciente', p.telefono1 AS 'telefono', CONCAT(c.apellido,' ',c.nombre) AS 'colaborador', s.nombre AS 'servicio', 
a.observacion AS 'observacion', a.comentario AS 'comentario',
(CASE WHEN a.status = '1' THEN 'Atendido' ELSE 'Pendiente' END) AS 'estatus', CAST(a.fecha_cita AS DATE) AS 'fecha', a.colaborador_id AS 'colaborador_id', a.servicio_id AS 'servicio_id'
	FROM agenda AS a
	INNER JOIN pacientes AS p
	ON a.pacientes_id = p.pacientes_id
	INNER JOIN servicios AS s
	ON a.servicio_id = s.servicio_id
	INNER JOIN colaboradores AS c
	ON a.colaborador_id = c.colaborador_id	  
	".$where."
	ORDER BY a.hora, a.pacientes_id ASC
	LIMIT $limit, $nroLotes";
$result = $mysqli->query($registro) or die($mysqli->error);


$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
			<tr>
			<th width="1.09%">No.</th>
			<th width="8.09%">Identidad</th>				
			<th width="20.09%">Nombre</th>
			<th width="6.09%">Fecha</th>
			<th width="5.09%">Hora</th>
			<th width="5.09%">Paciente</th>
			<th width="10.09%">Servicio</th>
			<th width="9.09%">Teléfono</th>
			<th width="16.09%">Observación</th>
			<th width="4.09%">Estado</th>
			<th width="15.09%">Opciones</th>
			</tr>';
$i = 1;				
while($registro2 = $result->fetch_assoc()){		  
  $telefonousuario = '<a style="text-decoration:none" title = "Teléfono Usuario" href="tel:9'.$registro2['telefono'].'">'.$registro2['telefono'].'</a>'; 
  $tipo_paciente = $registro2['tipo_paciente'];
  
  $atenciones = "";

  	if($type == 1 || $type == 2  || $type == 3){
		$atencion = '<a class="btn btn btn-secondary ml-2" href="javascript:setAtencion('.$registro2['pacientes_id'].','.$colaborador_id.','.$registro2['servicio_id'].','.$registro2['agenda_id'].');void(0);"><div class="sb-nav-link-icon"></div><i class="fas fa-book-medical fa-lg"></i> Atención</a>';
	}else{
		$atencion = '';
	}
  
	$tabla = $tabla.'<tr>
			<td>'.$i.'</td> 
			<td>'.$registro2['identidad'].'</td>	
			<td>'.$registro2['paciente'].'</td>	
			<td>'.$registro2['fecha_cita'].'</td>
			<td>'.date('g:i a',strtotime($registro2['hora'])).'</td>
			<td>'.$registro2['tipo_paciente'].'</td>
			<td>'.$registro2['servicio'].'</td>
			<td>'.$telefonousuario.'</td>
            <td>'.$registro2['observacion'].'</td>
            <td>'.$registro2['estatus'].'</td>			
			<td>	
			  '.$atencion.'
			  <a class="btn btn btn-secondary ml-2" href="javascript:nosePresentoRegistro('.$registro2['pacientes_id'].','.$registro2['agenda_id'].','.$registro2['fecha'].');void(0);"><div class="sb-nav-link-icon"></div><i class="fas fa-times-circle fa-lg" title = "Marcar Ausencia"></i> Ausencia</a>
			</td>
			</tr>';	
			$i++;				
}

if($nroProductos == 0){
	$tabla = $tabla.'<tr>
	   <td colspan="12" style="color:#C7030D">No se encontraron resultados</td>
	</tr>';		
}else{
   $tabla = $tabla.'<tr>
	  <td colspan="12"><b><p ALIGN="center">Total de Registros Encontrados: '.$nroProductos.'</p></b>
   </tr>';		
}        

$tabla = $tabla.'</table>';

$array = array(0 => $tabla,
			   1 => $lista);

echo json_encode($array);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN	
?>