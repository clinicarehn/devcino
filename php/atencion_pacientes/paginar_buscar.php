<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$colaborador_id = $_SESSION['colaborador_id'];
$paginaActual = $_POST['partida'];
$dato = $_POST['dato'];
	
$where = "WHERE c.colaborador_id = '$colaborador_id' AND (p.expediente LIKE '%$dato%' OR CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.apellido LIKE '$dato%' OR p.identidad LIKE '$dato%')";

$query = "SELECT p.pacientes_id AS 'pacientes_id', DATE_FORMAT(c.fecha, '%d/%m/%Y') AS 'fecha', p.identidad AS 'identidad', CONCAT(p.nombre,' ',p.apellido) AS 'paciente', s.nombre AS 'servicio', c.inicio_obecidad AS 'inicio_obecidad', c.peso_maximo_alcansado AS 'peso_maximo_alcansado', c.tipo_obecidad AS 'tipo_obecidad'
	FROM clinico AS c
	INNER JOIN pacientes AS p
	ON c.pacientes_id = p.pacientes_id
	INNER JOIN servicios AS s
	ON c.servicio_id = s.servicio_id
	".$where."
	ORDER BY c.fecha DESC";	

$result = $mysqli->query($query) or die($mysqli->error);

$nroLotes = 5;
$nroProductos = $result->num_rows;
$nroPaginas = ceil($nroProductos/$nroLotes);
$lista = '';
$tabla = '';

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:paginationBusqueda('.(1).');void(0);">Inicio</a></li>';
}

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:paginationBusqueda('.($paginaActual-1).');void(0);">Anterior '.($paginaActual-1).'</a></li>';
}

if($paginaActual < $nroPaginas){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:paginationBusqueda('.($paginaActual+1).');void(0);">Siguiente '.($paginaActual+1).' de '.$nroPaginas.'</a></li>';
}

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:paginationBusqueda('.($nroPaginas).');void(0);">Ultima</a></li>';
}

if($paginaActual <= 1){
	$limit = 0;
}else{
	$limit = $nroLotes*($paginaActual-1);
}

$registro = "SELECT p.pacientes_id AS 'pacientes_id', DATE_FORMAT(c.fecha, '%d/%m/%Y') AS 'fecha', p.identidad AS 'identidad', CONCAT(p.nombre,' ',p.apellido) AS 'paciente', s.nombre AS 'servicio', c.inicio_obecidad AS 'inicio_obecidad', c.peso_maximo_alcansado AS 'peso_maximo_alcansado', c.tipo_obecidad AS 'tipo_obecidad'
	FROM clinico AS c
	INNER JOIN pacientes AS p
	ON c.pacientes_id = p.pacientes_id
	INNER JOIN servicios AS s
	ON c.servicio_id = s.servicio_id
	".$where."
	ORDER BY c.fecha DESC
	LIMIT $limit, $nroLotes";
$result = $mysqli->query($registro) or die($mysqli->error);


$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
			<tr>
			<th width="1.28%">No.</th>
			<th width="6.28%">Fecha</th>				
			<th width="23.28%">Paciente</th>
			<th width="20.28%">Inicio Obecidad</th>
			<th width="20.28%">Peso Maximo Alcanzado</th>
			<th width="14.28%">Tipo Obecidad</th>			
			<th width="14.28%">Servicio</th>
			</tr>';
$i = 1;				
while($registro2 = $result->fetch_assoc()){  
	$tabla = $tabla.'<tr>
			<td>'.$i.'</td> 
			<td>'.$registro2['fecha'].'</td> 		
			<td>'.$registro2['paciente'].'</td>
			<td>'.$registro2['inicio_obecidad'].'</td>
			<td>'.$registro2['peso_maximo_alcansado'].'</td>
			<td>'.$registro2['tipo_obecidad'].'</td>			
			<td>'.$registro2['servicio'].'</td>
			</tr>';	
			$i++;				
}

if($nroProductos == 0){
	$tabla = $tabla.'<tr>
	   <td colspan="7" style="color:#C7030D">No se encontraron resultados</td>
	</tr>';		
}else{
   $tabla = $tabla.'<tr>
	  <td colspan="7"><b><p ALIGN="center">Total de Registros Encontrados: '.$nroProductos.'</p></b>
   </tr>';		
}        

$tabla = $tabla.'</table>';

$array = array(0 => $tabla,
			   1 => $lista);

echo json_encode($array);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN	
?>