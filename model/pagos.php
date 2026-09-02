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
	case 'recibo':
		$result = GetRecibo();
		break;
	case 'resumen_dia':
		$result = ResumenDia();
		break;
	case 'resumen_mes':
		$result = ResumenMes();
		break;
	default:
		$result = [ 'result' => false, 'message' => 'Acción no permitida' ];
}

die( json_encode( $result ) );	

function GetAll() {
	$oDb = create_conex();
	$pagos = [];

	$r = $oDb->query("SELECT p.*, r.placa, r.cajon, r.marca, r.color, r.tipo_vehiculo, r.fecha_entrada, r.fecha_salida, s.nombre as seccion_nombre
		FROM pagos p
		JOIN registros r ON p.registro_id = r.id
		JOIN secciones s ON r.seccion_id = s.id
		ORDER BY p.created_at DESC LIMIT 100");

	while ( $row = $oDb->getrow() ) {
		$pagos[] = $row;
	}

	$oDb->Close();
	return [ 'result' => true, 'data' => $pagos ];
}

function GetOne() {
	$id = filter_post('id');
	$oDb = create_conex();

	$r = $oDb->bind_params("SELECT p.*, r.placa, r.cajon, r.marca, r.color, r.tipo_vehiculo, r.fecha_entrada, r.fecha_salida, s.nombre as seccion_nombre
		FROM pagos p
		JOIN registros r ON p.registro_id = r.id
		JOIN secciones s ON r.seccion_id = s.id
		WHERE p.id = ?", [$id]);
	$row = $oDb->getrow();

	$oDb->Close();
	return $row ? [ 'result' => true, 'data' => $row ] : [ 'result' => false, 'message' => 'Pago no encontrado' ];
}

function GetRecibo() {
	$recibo = filter_post('recibo');
	$oDb = create_conex();

	$r = $oDb->bind_params("SELECT p.*, r.placa, r.cajon, r.marca, r.color, r.tipo_vehiculo, r.fecha_entrada, r.fecha_salida, s.nombre as seccion_nombre
		FROM pagos p
		JOIN registros r ON p.registro_id = r.id
		JOIN secciones s ON r.seccion_id = s.id
		WHERE p.recibo = ?", [$recibo]);
	$row = $oDb->getrow();

	$oDb->Close();
	return $row ? [ 'result' => true, 'data' => $row ] : [ 'result' => false, 'message' => 'Recibo no encontrado' ];
}

function ResumenDia() {
	$fecha = filter_post('fecha') ?: date('Y-m-d');
	$oDb = create_conex();

	$r = $oDb->bind_params("SELECT COUNT(*) as cantidad, COALESCE(SUM(monto_total), 0) as total 
		FROM pagos WHERE fecha_pago = ?", [$fecha]);
	$row = $oDb->getrow();

	$oDb->Close();
	return [ 'result' => true, 'data' => $row ];
}

function ResumenMes() {
	$mes = filter_post('mes') ?: date('Y-m');
	$oDb = create_conex();

	$r = $oDb->bind_params("SELECT COUNT(*) as cantidad, COALESCE(SUM(monto_total), 0) as total 
		FROM pagos WHERE DATE_FORMAT(fecha_pago, '%Y-%m') = ?", [$mes]);
	$row = $oDb->getrow();

	$oDb->Close();
	return [ 'result' => true, 'data' => $row ];
}
?>