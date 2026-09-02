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
	default:
		$result = [ 'result' => false, 'message' => 'Acción no permitida' ];
}

die( json_encode( $result ) );	

function GetAll() {
	$oDb = create_conex();
	$secciones = [];

	$r = $oDb->query("SELECT s.*, 
		(SELECT COUNT(*) FROM registros r WHERE r.seccion_id = s.id AND r.estado = 'dentro') as ocupados
		FROM secciones s ORDER BY s.nombre");

	while ( $row = $oDb->getrow() ) {
		$secciones[] = $row;
	}

	$oDb->Close();
	return [ 'result' => true, 'data' => $secciones ];
}

function GetOne() {
	$id = filter_post('id');
	$oDb = create_conex();

	$r = $oDb->bind_params("SELECT * FROM secciones WHERE id = ?", [$id]);
	$row = $oDb->getrow();

	$oDb->Close();
	return $row ? [ 'result' => true, 'data' => $row ] : [ 'result' => false, 'message' => 'Sección no encontrada' ];
}

function Save() {
	$id          = filter_post('id');
	$nombre      = filter_post('nombre');
	$capacidad   = filter_post('capacidad');
	$descripcion = filter_post('descripcion');
	$estatus     = filter_post('estatus');

	if ( empty($nombre) || empty($capacidad) ) {
		return [ 'result' => false, 'message' => 'Nombre y capacidad son obligatorios' ];
	}

	$oDb = create_conex();

	// Verificar nombre duplicado
	$sqlCheck = "SELECT id FROM secciones WHERE nombre = ?" . (!empty($id) ? " AND id != ?" : "");
	$paramsCheck = [$nombre];
	if (!empty($id)) $paramsCheck[] = $id;

	if ( $oDb->bind_params($sqlCheck, $paramsCheck) ) {
		if ( $row = $oDb->getrow() ) {
			$oDb->Close();
			return [ 'result' => false, 'message' => 'Ya existe una sección con ese nombre' ];
		}
	}

	if ( !empty($id) ) {
		$sql = "UPDATE secciones SET nombre=?, capacidad=?, descripcion=?, estatus=? WHERE id=?";
		$success = $oDb->bind_params($sql, [$nombre, $capacidad, $descripcion, $estatus, $id], true);
	} else {
		$sql = "INSERT INTO secciones (nombre, capacidad, descripcion, estatus) VALUES (?,?,?,?)";
		$success = $oDb->bind_params($sql, [$nombre, $capacidad, $descripcion, $estatus], true);
	}

	$oDb->Close();
	return $success ? [ 'result' => true ] : [ 'result' => false, 'message' => 'Error al guardar la sección' ];
}

function Delete() {
	$id = filter_post('id');

	$oDb = create_conex();

	// Verificar si hay vehículos dentro
	$r = $oDb->bind_params("SELECT COUNT(*) as total FROM registros WHERE seccion_id = ? AND estado = 'dentro'", [$id]);
	$row = $oDb->getrow();
	if ( $row && $row['total'] > 0 ) {
		$oDb->Close();
		return [ 'result' => false, 'message' => 'No se puede eliminar: hay vehículos estacionados en esta sección' ];
	}

	$success = $oDb->bind_params("DELETE FROM secciones WHERE id = ?", [$id], true);
	$oDb->Close();

	return $success ? [ 'result' => true ] : [ 'result' => false, 'message' => 'Error al eliminar la sección' ];
}
?>