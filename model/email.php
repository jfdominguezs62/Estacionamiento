<?php
include ( '../constants.php' );
Constants::setpath_root  ("../");
Constants::create_filejs( false );

include ( Constants::getpath_root() . 'config.php' );
include ( Constants::getpath_root() . 'config_db.php' );

$cAction = filter_post( 'action' );

switch( $cAction ) {
	case 'send_recibo':
		$result = SendRecibo();
		break;
	default:
		$result = [ 'result' => false, 'message' => 'Acción no permitida' ];
}

die( json_encode( $result ) );

function SendRecibo() {
	$email = trim(filter_post('email'));
	$datosJson = filter_post('datos');

	if (empty($email) || empty($datosJson)) {
		return ['result' => false, 'message' => 'Correo y datos son requeridos'];
	}

	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return ['result' => false, 'message' => 'Correo electrónico no válido'];
	}

	$datos = json_decode($datosJson, true);
	if (!$datos) {
		return ['result' => false, 'message' => 'Datos del comprobante inválidos'];
	}

	$tipoCobro = $datos['cobro_fraccion'] == 1 ? 'Fracción como hora completa' : 'Solo horas completas';

	$html = '<div style="font-family:Arial,sans-serif;max-width:500px;margin:auto;border:1px solid #ddd;border-radius:8px;overflow:hidden;">';
	$html .= '<div style="background:linear-gradient(135deg,#1b5e20,#2e7d32);color:white;padding:15px;text-align:center;">';
	$html .= '<h2 style="margin:0;">ESTACIONAMIENTO CENTRAL</h2>';
	$html .= '<p style="margin:5px 0 0;font-size:14px;">Comprobante de Pago</p></div>';
	$html .= '<div style="padding:20px;">';
	$html .= '<table style="width:100%;border-collapse:collapse;font-size:14px;">';
	$html .= '<tr><td style="padding:6px 0;color:#666;width:120px;">Recibo:</td><td style="padding:6px 0;font-weight:bold;">' . htmlspecialchars($datos['recibo']) . '</td></tr>';
	$html .= '<tr><td style="padding:6px 0;color:#666;">Cajón:</td><td style="padding:6px 0;font-weight:bold;">' . htmlspecialchars($datos['cajon']) . '</td></tr>';
	$html .= '<tr><td style="padding:6px 0;color:#666;">Placa:</td><td style="padding:6px 0;font-weight:bold;">' . htmlspecialchars($datos['placa']) . '</td></tr>';
	$html .= '<tr><td style="padding:6px 0;color:#666;">Marca/Color:</td><td style="padding:6px 0;">' . htmlspecialchars(($datos['marca'] ?? '-') . ' / ' . ($datos['color'] ?? '-')) . '</td></tr>';
	$html .= '<tr><td style="padding:6px 0;color:#666;">Tipo:</td><td style="padding:6px 0;">' . htmlspecialchars($datos['tipo_vehiculo']) . '</td></tr>';
	$html .= '<tr><td style="padding:6px 0;color:#666;">Entrada:</td><td style="padding:6px 0;">' . htmlspecialchars($datos['fecha_entrada']) . '</td></tr>';
	$html .= '<tr><td style="padding:6px 0;color:#666;">Salida:</td><td style="padding:6px 0;">' . htmlspecialchars($datos['fecha_salida']) . '</td></tr>';
	$html .= '<tr><td style="padding:6px 0;color:#666;">Tiempo:</td><td style="padding:6px 0;">' . htmlspecialchars($datos['tiempo_real']) . '</td></tr>';
	$html .= '<tr><td style="padding:6px 0;color:#666;">Horas cobro:</td><td style="padding:6px 0;font-weight:bold;">' . $datos['horas'] . 'h</td></tr>';
	$html .= '<tr><td style="padding:6px 0;color:#666;">Tipo cobro:</td><td style="padding:6px 0;">' . $tipoCobro . '</td></tr>';
	$html .= '</table>';
	$html .= '<div style="text-align:center;margin-top:15px;padding-top:15px;border-top:2px solid #1b5e20;">';
	$html .= '<span style="font-size:24px;font-weight:bold;color:#1b5e20;">TOTAL: $' . number_format(floatval($datos['monto']), 2) . '</span></div>';
	$html .= '</div>';
	$html .= '<div style="background:#f8f9fa;text-align:center;padding:10px;font-size:11px;color:#999;">Gracias por su preferencia</div></div>';

	// Configuración SMTP - AJUSTAR CON TUS DATOS REALES
	$smtp_host = 'smtp.gmail.com';
	$smtp_port = 587;
	$smtp_user = 'jfdominguezs62@gmail.com';    // Cambiar
	$smtp_pass = 'djwd jdmf trjr xqcv';      // Cambiar (contraseña de aplicación Gmail)
	$from_name = 'Estacionamiento Central';

	try {
		require_once Constants::getpath_tweb() . 'libs/phpmailer/src/Exception.php';
		require_once Constants::getpath_tweb() . 'libs/phpmailer/src/PHPMailer.php';
		require_once Constants::getpath_tweb() . 'libs/phpmailer/src/SMTP.php';

		$mail = new PHPMailer\PHPMailer\PHPMailer(true);
		$mail->isSMTP();
		$mail->Host = $smtp_host;
		$mail->SMTPAuth = true;
		$mail->Username = $smtp_user;
		$mail->Password = $smtp_pass;
		$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
		$mail->Port = $smtp_port;
		$mail->CharSet = 'UTF-8';

		$mail->setFrom($smtp_user, $from_name);
		$mail->addAddress($email);
		$mail->isHTML(true);
		$mail->Subject = 'Comprobante de Pago - Recibo ' . $datos['recibo'];
		$mail->Body = $html;

		$mail->send();
		return ['result' => true, 'message' => 'Correo enviado exitosamente'];
	} catch (Exception $e) {
		return ['result' => false, 'message' => 'Error al enviar: ' . $mail->ErrorInfo];
	}
}
?>