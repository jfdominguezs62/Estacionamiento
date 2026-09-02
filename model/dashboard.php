<?php 
include ( '../constants.php' );
Constants::setpath_root  ("../");
Constants::create_filejs( false );

include ( Constants::getpath_root() . 'config.php' );
include ( Constants::getpath_root() . 'config_db.php' );

$cAction = filter_post( 'action' );

switch( $cAction ) {	
	case 'stats':
		$result = GetStats();
		break;
	case 'secciones_ocupacion':
		$result = GetSeccionesOcupacion();
		break;
	case 'registros_hoy':
		$result = GetRegistrosHoy();
		break;
	default:
		$result = [ 'result' => false, 'message' => 'Acción no permitida' ];
}

die( json_encode( $result ) );	

function GetStats() {
	$oDb = create_conex();
	$stats = [];

	// Total secciones activas
	$r = $oDb->query("SELECT COUNT(*) as total FROM secciones WHERE estatus = 'activo'");
	$row = $oDb->getrow();
	$stats['secciones_activas'] = $row ? $row['total'] : 0;

	// Vehículos dentro ahora
	$r = $oDb->query("SELECT COUNT(*) as total FROM registros WHERE estado = 'dentro'");
	$row = $oDb->getrow();
	$stats['vehiculos_dentro'] = $row ? $row['total'] : 0;

	// Registros hoy
	$hoy = date('Y-m-d');
	$r = $oDb->bind_params("SELECT COUNT(*) as total FROM registros WHERE DATE(fecha_entrada) = ?", [$hoy]);
	$row = $oDb->getrow();
	$stats['registros_hoy'] = $row ? $row['total'] : 0;

	// Pagos hoy (monto)
	$r = $oDb->bind_params("SELECT COALESCE(SUM(monto_total), 0) as total, COUNT(*) as cantidad FROM pagos WHERE fecha_pago = ?", [$hoy]);
	$row = $oDb->getrow();
	$stats['cobro_hoy'] = $row ? floatval($row['total']) : 0;
	$stats['pagos_hoy'] = $row ? $row['cantidad'] : 0;

	// Pagos mes
	$mes = date('Y-m');
	$r = $oDb->bind_params("SELECT COALESCE(SUM(monto_total), 0) as total, COUNT(*) as cantidad FROM pagos WHERE DATE_FORMAT(fecha_pago, '%Y-%m') = ?", [$mes]);
	$row = $oDb->getrow();
	$stats['cobro_mes'] = $row ? floatval($row['total']) : 0;
	$stats['pagos_mes'] = $row ? $row['cantidad'] : 0;

	$oDb->Close();
	return [ 'result' => true, 'data' => $stats ];
}

function GetSeccionesOcupacion() {
	$oDb = create_conex();
	$secciones = [];

	$r = $oDb->query("SELECT s.id, s.nombre, s.capacidad, 
		(SELECT COUNT(*) FROM registros r WHERE r.seccion_id = s.id AND r.estado = 'dentro') as ocupados
		FROM secciones s WHERE s.estatus = 'activo' ORDER BY s.nombre");

	while ( $row = $oDb->getrow() ) {
		$secciones[] = $row;
	}

	$oDb->Close();
	return [ 'result' => true, 'data' => $secciones ];
}

function GetRegistrosHoy() {
	$oDb = create_conex();
	$registros = [];
	$hoy = date('Y-m-d');

	$r = $oDb->bind_params("SELECT r.*, s.nombre as seccion_nombre 
		FROM registros r 
		JOIN secciones s ON r.seccion_id = s.id 
		WHERE DATE(r.fecha_entrada) = ? 
		ORDER BY r.fecha_entrada DESC", [$hoy]);

	while ( $row = $oDb->getrow() ) {
		$registros[] = $row;
	}

	$oDb->Close();
	return [ 'result' => true, 'data' => $registros ];
}
?>