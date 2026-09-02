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
	case 'getdentro':
		$result = GetDentro();
		break;
	case 'registrar_entrada':
		$result = RegistrarEntrada();
		break;
	case 'registrar_salida':
		$result = RegistrarSalida();
		break;
	case 'calcular_pago':
		$result = CalcularPago();
		break;
	case 'getsecciones':
		$result = GetSecciones();
		break;
	case 'gettarifa':
		$result = GetTarifa();
		break;
	default:
		$result = [ 'result' => false, 'message' => 'Acción no permitida' ];
}

die( json_encode( $result ) );	

function GetAll() {
	$oDb = create_conex();
	$registros = [];

	$r = $oDb->query("SELECT r.*, s.nombre as seccion_nombre 
		FROM registros r 
		JOIN secciones s ON r.seccion_id = s.id 
		ORDER BY r.fecha_entrada DESC LIMIT 100");

	while ( $row = $oDb->getrow() ) {
		$registros[] = $row;
	}

	$oDb->Close();
	return [ 'result' => true, 'data' => $registros ];
}

function GetDentro() {
	$oDb = create_conex();
	$registros = [];

	$r = $oDb->query("SELECT r.*, s.nombre as seccion_nombre 
		FROM registros r 
		JOIN secciones s ON r.seccion_id = s.id 
		WHERE r.estado = 'dentro' 
		ORDER BY r.fecha_entrada ASC");

	while ( $row = $oDb->getrow() ) {
		// Calcular tiempo transcurrido
		$entrada = new DateTime($row['fecha_entrada']);
		$ahora = new DateTime();
		$dif = $entrada->diff($ahora);
		$row['tiempo_transcurrido'] = $dif->h . 'h ' . $dif->i . 'min';
		if ($dif->d > 0) $row['tiempo_transcurrido'] = $dif->d . 'd ' . $row['tiempo_transcurrido'];
		$registros[] = $row;
	}

	$oDb->Close();
	return [ 'result' => true, 'data' => $registros ];
}

function GetSecciones() {
	$oDb = create_conex();
	$secciones = [];

	$r = $oDb->query("SELECT id, nombre, capacidad FROM secciones ORDER BY nombre");
	while ( $row = $oDb->getrow() ) {
		$secciones[] = $row;
	}

	$oDb->Close();
	return [ 'result' => true, 'data' => $secciones ];
}

function GetTarifa() {
	$seccion_id = filter_post('seccion_id');
	$tipo_vehiculo = filter_post('tipo_vehiculo');
	$oDb = create_conex();

	$r = $oDb->bind_params("SELECT * FROM tarifas WHERE seccion_id = ? AND tipo_vehiculo = ? AND activo = 1 LIMIT 1", [$seccion_id, $tipo_vehiculo]);
	$row = $oDb->getrow();

	$oDb->Close();
	return $row ? [ 'result' => true, 'data' => $row ] : [ 'result' => false, 'message' => 'No hay tarifa activa' ];
}

function RegistrarEntrada() {
	$seccion_id    = filter_post('seccion_id');
	$cajon         = strtoupper(trim(filter_post('cajon')));
	$tipo_vehiculo = filter_post('tipo_vehiculo');
	$placa         = strtoupper(trim(filter_post('placa')));
	$marca         = trim(filter_post('marca'));
	$color         = trim(filter_post('color'));

	if ( empty($seccion_id) || empty($cajon) || empty($tipo_vehiculo) || empty($placa) ) {
		return [ 'result' => false, 'message' => 'Sección, cajón, tipo y placa son obligatorios' ];
	}

	$oDb = create_conex();

	// Verificar capacidad de la sección
	$r = $oDb->bind_params("SELECT s.capacidad, (SELECT COUNT(*) FROM registros r WHERE r.seccion_id = s.id AND r.estado = 'dentro') as ocupados FROM secciones s WHERE s.id = ?", [$seccion_id]);
	$sec = $oDb->getrow();
	if ($sec && $sec['ocupados'] >= $sec['capacidad']) {
		$oDb->Close();
		return [ 'result' => false, 'message' => 'La sección está llena. No hay espacio disponible.' ];
	}

	// Verificar que el cajón no esté ocupado en la misma sección
	$r = $oDb->bind_params("SELECT id, placa FROM registros WHERE seccion_id = ? AND cajon = ? AND estado = 'dentro' LIMIT 1", [$seccion_id, $cajon]);
	if ( $row = $oDb->getrow() ) {
		$oDb->Close();
		return [ 'result' => false, 'message' => 'El cajón ' . $cajon . ' ya está ocupado por el vehículo con placa ' . $row['placa'] ];
	}

	// Verificar que el vehículo no esté ya dentro
	$r = $oDb->bind_params("SELECT id, cajon FROM registros WHERE placa = ? AND estado = 'dentro' LIMIT 1", [$placa]);
	if ( $row = $oDb->getrow() ) {
		$oDb->Close();
		return [ 'result' => false, 'message' => 'Este vehículo ya está registrado dentro del estacionamiento en cajón ' . $row['cajon'] ];
	}

	$oSession = new TSession( APP_SESSION );
	$registrado_por = $oSession->GetVar('usuario') ?: '';

	$fecha_entrada = date('Y-m-d H:i:s');

	$sql = "INSERT INTO registros (seccion_id, cajon, tipo_vehiculo, placa, marca, color, fecha_entrada, estado, registrado_por) VALUES (?,?,?,?,?,?,?,'dentro',?)";
	$success = $oDb->bind_params($sql, [$seccion_id, $cajon, $tipo_vehiculo, $placa, $marca, $color, $fecha_entrada, $registrado_por], true);

	$oDb->Close();

	if ($success) {
		return [ 'result' => true, 'message' => 'Entrada registrada exitosamente. Cajón: ' . $cajon, 'fecha_entrada' => $fecha_entrada ];
	} else {
		return [ 'result' => false, 'message' => 'Error al registrar la entrada' ];
	}
}

function RegistrarSalida() {
	$id = filter_post('id');
	$metodo_pago = filter_post('metodo_pago') ?: 'efectivo';
	$observaciones = filter_post('observaciones') ?: '';
	$cobro_fraccion = intval(filter_post('cobro_fraccion')); // 1=fracción como hora completa, 0=solo horas completas

	$oDb = create_conex();

	// Obtener el registro
	$r = $oDb->bind_params("SELECT * FROM registros WHERE id = ? AND estado = 'dentro'", [$id]);
	$registro = $oDb->getrow();

	if (!$registro) {
		$oDb->Close();
		return [ 'result' => false, 'message' => 'Registro no encontrado o el vehículo ya salió' ];
	}

	// Obtener tarifa activa
	$r = $oDb->bind_params("SELECT * FROM tarifas WHERE seccion_id = ? AND tipo_vehiculo = ? AND activo = 1 LIMIT 1", [$registro['seccion_id'], $registro['tipo_vehiculo']]);
	$tarifa = $oDb->getrow();

	if (!$tarifa) {
		$oDb->Close();
		return [ 'result' => false, 'message' => 'No hay tarifa configurada para esta sección y tipo de vehículo' ];
	}

	// Calcular tiempo
	$entrada = new DateTime($registro['fecha_entrada']);
	$salida = new DateTime();
	$dif = $entrada->diff($salida);
	
	$totalMinutos = $dif->d * 24 * 60 + $dif->h * 60 + $dif->i;
	$horasExactas = $totalMinutos / 60;

	// Aplicar redondeo según opción de cobro
	if ($cobro_fraccion == 1) {
		// Fracción se cobra como hora completa: redondear hacia arriba
		$horasCobro = ceil($horasExactas);
		if ($horasCobro < 1) $horasCobro = 1;
	} else {
		// Solo horas completas: redondear hacia abajo, mínimo 1
		$horasCobro = floor($horasExactas);
		if ($horasCobro < 1) $horasCobro = 1;
	}

	// Calcular monto
	$monto = 0;
	$diasCompletos = $dif->d;
	$horasRestantes = $horasCobro - ($diasCompletos * 24);

	if ($diasCompletos >= 1 && $tarifa['monto_dia'] > 0) {
		$monto = $diasCompletos * $tarifa['monto_dia'];
		if ($horasRestantes > 0) {
			$monto += $horasRestantes * $tarifa['monto_hora'];
		}
	} else {
		$monto = $horasCobro * $tarifa['monto_hora'];
	}

	$monto = round($monto, 2);
	$fecha_salida = $salida->format('Y-m-d H:i:s');

	// Actualizar registro
	$sql = "UPDATE registros SET fecha_salida = ?, estado = 'fuera' WHERE id = ?";
	$success = $oDb->bind_params($sql, [$fecha_salida, $id], true);

	if ($success) {
		// Registrar pago
		$oSession = new TSession( APP_SESSION );
		$registrado_por = $oSession->GetVar('usuario') ?: '';
		$recibo = 'REC-' . date('Ymd') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

		$sqlPago = "INSERT INTO pagos (registro_id, monto_total, horas, cobro_fraccion, fecha_pago, recibo, metodo_pago, observaciones, registrado_por) VALUES (?,?,?,?,?,?,?,?,?)";
		$oDb->bind_params($sqlPago, [$id, $monto, $horasCobro, $cobro_fraccion, date('Y-m-d'), $recibo, $metodo_pago, $observaciones, $registrado_por], true);
	}

	$oDb->Close();

	// Formato legible del tiempo real
	$tiempoReal = $diasCompletos > 0 ? $diasCompletos . 'd ' : '';
	$tiempoReal .= $dif->h . 'h ' . $dif->i . 'min';

	if ($success) {
		return [ 
			'result' => true, 
			'message' => 'Salida registrada exitosamente',
			'placa' => $registro['placa'],
			'marca' => $registro['marca'],
			'color' => $registro['color'],
			'tipo_vehiculo' => $registro['tipo_vehiculo'],
			'fecha_entrada' => $registro['fecha_entrada'],
			'fecha_salida' => $fecha_salida,
			'tiempo_real' => $tiempoReal,
			'horas' => $horasCobro,
			'horas_exactas' => round($horasExactas, 2),
			'cobro_fraccion' => $cobro_fraccion,
			'monto_hora' => floatval($tarifa['monto_hora']),
			'monto_dia' => floatval($tarifa['monto_dia']),
			'dias_cobro' => $diasCompletos,
			'horas_resto' => $horasCobro - ($diasCompletos * 24),
			'monto' => $monto,
			'recibo' => $recibo
		];
	} else {
		return [ 'result' => false, 'message' => 'Error al registrar la salida' ];
	}
}

function CalcularPago() {
	$id = filter_post('id');
	$oDb = create_conex();

	$r = $oDb->bind_params("SELECT * FROM registros WHERE id = ? AND estado = 'dentro'", [$id]);
	$registro = $oDb->getrow();

	if (!$registro) {
		$oDb->Close();
		return [ 'result' => false, 'message' => 'Registro no encontrado' ];
	}

	$r = $oDb->bind_params("SELECT * FROM tarifas WHERE seccion_id = ? AND tipo_vehiculo = ? AND activo = 1 LIMIT 1", [$registro['seccion_id'], $registro['tipo_vehiculo']]);
	$tarifa = $oDb->getrow();

	$entrada = new DateTime($registro['fecha_entrada']);
	$ahora = new DateTime();
	$dif = $entrada->diff($ahora);
	$totalMinutos = $dif->d * 24 * 60 + $dif->h * 60 + $dif->i;
	$horasExactas = $totalMinutos / 60;

	$monto_hora = $tarifa ? floatval($tarifa['monto_hora']) : 0;
	$monto_dia = $tarifa ? floatval($tarifa['monto_dia']) : 0;

	// Opción 1: Fracción como hora completa (ceil)
	$horas_fraccion = ceil($horasExactas);
	if ($horas_fraccion < 1) $horas_fraccion = 1;
	$monto_fraccion = 0;
	$diasC = $dif->d;
	$horasRestC = $horas_fraccion - ($diasC * 24);
	if ($diasC >= 1 && $monto_dia > 0) {
		$monto_fraccion = $diasC * $monto_dia;
		if ($horasRestC > 0) $monto_fraccion += $horasRestC * $monto_hora;
	} else {
		$monto_fraccion = $horas_fraccion * $monto_hora;
	}

	// Opción 2: Solo horas completas (floor), mínimo 1
	$horas_completa = floor($horasExactas);
	if ($horas_completa < 1) $horas_completa = 1;
	$monto_completa = 0;
	$diasF = $dif->d;
	$horasRestF = $horas_completa - ($diasF * 24);
	if ($diasF >= 1 && $monto_dia > 0) {
		$monto_completa = $diasF * $monto_dia;
		if ($horasRestF > 0) $monto_completa += $horasRestF * $monto_hora;
	} else {
		$monto_completa = $horas_completa * $monto_hora;
	}

	$tiempoReal = $dif->d > 0 ? $dif->d . 'd ' : '';
	$tiempoReal .= $dif->h . 'h ' . $dif->i . 'min';

	$oDb->Close();

	return [ 
		'result' => true, 
		'data' => [
			'placa' => $registro['placa'],
			'marca' => $registro['marca'],
			'color' => $registro['color'],
			'tipo_vehiculo' => $registro['tipo_vehiculo'],
			'fecha_entrada' => $registro['fecha_entrada'],
			'tiempo_real' => $tiempoReal,
			'horas_exactas' => round($horasExactas, 2),
			'monto_hora' => $monto_hora,
			'monto_dia' => $monto_dia,
			'dias_reales' => $dif->d,
			'horas_reales' => $dif->h,
			'minutos_reales' => $dif->i,
			'opcion_fraccion' => [
				'horas' => $horas_fraccion,
				'dias' => $diasC,
				'horas_resto' => $horasRestC,
				'monto_dias' => round($diasC * $monto_dia, 2),
				'monto_horas' => round($horasRestC * $monto_hora, 2),
				'monto' => round($monto_fraccion, 2),
				'descripcion' => $horas_fraccion . 'h (fracción como hora completa)'
			],
			'opcion_completa' => [
				'horas' => $horas_completa,
				'dias' => $diasF,
				'horas_resto' => $horasRestF,
				'monto_dias' => round($diasF * $monto_dia, 2),
				'monto_horas' => round($horasRestF * $monto_hora, 2),
				'monto' => round($monto_completa, 2),
				'descripcion' => $horas_completa . 'h (solo horas completas)'
			]
		]
	];
}
?>