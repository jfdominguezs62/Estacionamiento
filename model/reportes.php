<?php 
include ( '../constants.php' );
Constants::setpath_root  ("../");
Constants::create_filejs( false );

include ( Constants::getpath_root() . 'config.php' );
include ( Constants::getpath_root() . 'config_db.php' );

$cAction = filter_post( 'action' );

switch( $cAction ) {	
	case 'recaudacion_diaria':
		$result = RecaudacionDiaria();
		break;
	case 'recaudacion_semanal':
		$result = RecaudacionSemanal();
		break;
	case 'recaudacion_mensual':
		$result = RecaudacionMensual();
		break;
	case 'por_seccion':
		$result = PorSeccion();
		break;
	case 'por_tipo_vehiculo':
		$result = PorTipoVehiculo();
		break;
	case 'registros_rango':
		$result = RegistrosRango();
		break;
	default:
		$result = [ 'result' => false, 'message' => 'Acción no permitida' ];
}

die( json_encode( $result ) );	

function RecaudacionDiaria() {
	$oDb = create_conex();
	$datos = [];

	$r = $oDb->query("SELECT fecha_pago as fecha, COUNT(*) as cantidad, SUM(monto_total) as total 
		FROM pagos 
		WHERE fecha_pago >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
		GROUP BY fecha_pago 
		ORDER BY fecha_pago DESC");

	while ( $row = $oDb->getrow() ) {
		$datos[] = $row;
	}

	$oDb->Close();
	return [ 'result' => true, 'data' => $datos ];
}

function RecaudacionSemanal() {
	$oDb = create_conex();
	$datos = [];

	$r = $oDb->query("SELECT YEARWEEK(fecha_pago, 1) as semana, MIN(fecha_pago) as inicio, MAX(fecha_pago) as fin, 
		COUNT(*) as cantidad, SUM(monto_total) as total 
		FROM pagos 
		WHERE fecha_pago >= DATE_SUB(CURDATE(), INTERVAL 12 WEEK)
		GROUP BY YEARWEEK(fecha_pago, 1) 
		ORDER BY semana DESC");

	while ( $row = $oDb->getrow() ) {
		$datos[] = $row;
	}

	$oDb->Close();
	return [ 'result' => true, 'data' => $datos ];
}

function RecaudacionMensual() {
	$oDb = create_conex();
	$datos = [];

	$r = $oDb->query("SELECT DATE_FORMAT(fecha_pago, '%Y-%m') as mes, COUNT(*) as cantidad, SUM(monto_total) as total 
		FROM pagos 
		WHERE fecha_pago >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
		GROUP BY DATE_FORMAT(fecha_pago, '%Y-%m') 
		ORDER BY mes DESC");

	while ( $row = $oDb->getrow() ) {
		$datos[] = $row;
	}

	$oDb->Close();
	return [ 'result' => true, 'data' => $datos ];
}

function PorSeccion() {
	$oDb = create_conex();
	$datos = [];

	$r = $oDb->query("SELECT s.nombre as seccion, COUNT(p.id) as cantidad, SUM(p.monto_total) as total
		FROM pagos p
		JOIN registros r ON p.registro_id = r.id
		JOIN secciones s ON r.seccion_id = s.id
		WHERE p.fecha_pago >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
		GROUP BY s.nombre
		ORDER BY total DESC");

	while ( $row = $oDb->getrow() ) {
		$datos[] = $row;
	}

	$oDb->Close();
	return [ 'result' => true, 'data' => $datos ];
}

function PorTipoVehiculo() {
	$oDb = create_conex();
	$datos = [];

	$r = $oDb->query("SELECT r.tipo_vehiculo, COUNT(p.id) as cantidad, SUM(p.monto_total) as total
		FROM pagos p
		JOIN registros r ON p.registro_id = r.id
		WHERE p.fecha_pago >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
		GROUP BY r.tipo_vehiculo
		ORDER BY total DESC");

	while ( $row = $oDb->getrow() ) {
		$datos[] = $row;
	}

	$oDb->Close();
	return [ 'result' => true, 'data' => $datos ];
}

function RegistrosRango() {
	$fecha_inicio = filter_post('fecha_inicio');
	$fecha_fin = filter_post('fecha_fin');

	if (empty($fecha_inicio)) $fecha_inicio = date('Y-m-d');
	if (empty($fecha_fin)) $fecha_fin = date('Y-m-d');

	$oDb = create_conex();
	$registros = [];

	$r = $oDb->bind_params("SELECT r.*, s.nombre as seccion_nombre,
		p.monto_total, p.recibo, p.metodo_pago
		FROM registros r
		JOIN secciones s ON r.seccion_id = s.id
		LEFT JOIN pagos p ON p.registro_id = r.id
		WHERE DATE(r.fecha_entrada) BETWEEN ? AND ?
		ORDER BY r.fecha_entrada DESC", [$fecha_inicio, $fecha_fin]);

	while ( $row = $oDb->getrow() ) {
		$registros[] = $row;
	}

	$oDb->Close();
	return [ 'result' => true, 'data' => $registros ];
}
?>