<?php 
include ( '../constants.php' );
Constants::setpath_root  ("../");
Constants::create_filejs( false );

include ( Constants::getpath_root() . 'config.php' );
include ( Constants::getpath_root() . 'config_db.php' );

$cAction = filter_post( 'action' );

switch( $cAction ) {	
	case 'getall':
		$result = GetAll();
		break;
	case 'getone':
		$result = GetOne();
		break;
	case 'save':
		$result = Save();
		break;
	case 'delete':
		$result = Delete();
		break;
	case 'getactiva':
		$result = GetActiva();
		break;
	default:
		$result = [ 'result' => false, 'message' => 'Acción no permitida' ];
}

die( json_encode( $result ) );	

function GetAll() {
	$oDb = create_conex();
	$tarifas = [];

	$r = $oDb->query("SELECT t.*, s.nombre as seccion_nombre 
		FROM tarifas t 
		JOIN secciones s ON t.seccion_id = s.id 
		ORDER BY s.nombre, t.tipo_vehiculo");

	while ( $row = $oDb->getrow() ) {
		$tarifas[] = $row;
	}

	$oDb->Close();
	return [ 'result' => true, 'data' => $tarifas ];
}

function GetOne() {
	$id = filter_post('id');
	$oDb = create_conex();

	$r = $oDb->bind_params("SELECT * FROM tarifas WHERE id = ?", [$id]);
	$row = $oDb->getrow();

	$oDb->Close();
	return $row ? [ 'result' => true, 'data' => $row ] : [ 'result' => false, 'message' => 'Tarifa no encontrada' ];
}

function GetActiva() {
	$seccion_id = filter_post('seccion_id');
	$tipo_vehiculo = filter_post('tipo_vehiculo');
	$oDb = create_conex();

	$r = $oDb->bind_params("SELECT * FROM tarifas WHERE seccion_id = ? AND tipo_vehiculo = ? AND activo = 1 LIMIT 1", [$seccion_id, $tipo_vehiculo]);
	$row = $oDb->getrow();

	$oDb->Close();
	return $row ? [ 'result' => true, 'data' => $row ] : [ 'result' => false, 'message' => 'No hay tarifa activa para esta sección y tipo de vehículo' ];
}

function Save() {
	$id             = filter_post('id');
	$seccion_id     = filter_post('seccion_id');
	$tipo_vehiculo  = filter_post('tipo_vehiculo');
	$monto_hora     = filter_post('monto_hora');
	$monto_dia      = filter_post('monto_dia');
	$vigente_desde  = filter_post('vigente_desde');
	$activo         = filter_post('activo');

	if ( empty($seccion_id) || empty($tipo_vehiculo) || empty($monto_hora) ) {
		return [ 'result' => false, 'message' => 'Sección, tipo de vehículo y monto por hora son obligatorios' ];
	}

	$oDb = create_conex();

	if ( !empty($id) ) {
		$sql = "UPDATE tarifas SET seccion_id=?, tipo_vehiculo=?, monto_hora=?, monto_dia=?, vigente_desde=?, activo=? WHERE id=?";
		$success = $oDb->bind_params($sql, [$seccion_id, $tipo_vehiculo, $monto_hora, $monto_dia, $vigente_desde, $activo, $id], true);
	} else {
		$sql = "INSERT INTO tarifas (seccion_id, tipo_vehiculo, monto_hora, monto_dia, vigente_desde, activo) VALUES (?,?,?,?,?,?)";
		$success = $oDb->bind_params($sql, [$seccion_id, $tipo_vehiculo, $monto_hora, $monto_dia, $vigente_desde, $activo], true);
	}

	$oDb->Close();
	return $success ? [ 'result' => true ] : [ 'result' => false, 'message' => 'Error al guardar la tarifa' ];
}

function Delete() {
	$id = filter_post('id');
	$oDb = create_conex();
	$success = $oDb->bind_params("DELETE FROM tarifas WHERE id = ?", [$id], true);
	$oDb->Close();
	return $success ? [ 'result' => true ] : [ 'result' => false, 'message' => 'Error al eliminar la tarifa' ];
}
?>