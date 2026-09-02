<?php
// Roles: admin, cajero, operador

function tieneRol( $rolRequerido ) {
	$oSession = new TSession( APP_SESSION );
	$rol = $oSession->GetVar('rol');
	if ( empty($rol) ) $rol = 'operador';

	if ( $rolRequerido == 'admin' ) {
		return $rol === 'admin';
	}
	if ( $rolRequerido == 'cajero' ) {
		return $rol === 'admin' || $rol === 'cajero';
	}
	// operador: todos los roles pueden
	return true;
}

function rolUsuario() {
	$oSession = new TSession( APP_SESSION );
	$rol = $oSession->GetVar('rol');
	return $rol ?: 'operador';
}

function checkAcceso( $rolesPermitidos = [] ) {
	if ( empty($rolesPermitidos) ) return;
	$oSession = new TSession( APP_SESSION );
	if ( is_string($rolesPermitidos) ) {
		$args = func_get_args();
		$rolesPermitidos = isset($args[1]) ? $args[1] : [];
		if ( empty($rolesPermitidos) ) return;
	}
	$rol = $oSession->GetVar('rol') ?: 'operador';
	if ( !in_array($rol, $rolesPermitidos) ) {
		header("Location: menu.php");
		exit;
	}
}
?>