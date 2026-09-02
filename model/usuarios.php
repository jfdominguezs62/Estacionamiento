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
	$usuarios = [];

	$r = $oDb->query("SELECT id, username, user, rol, created_at FROM " . TABLA_USUARIOS . " ORDER BY username");

	while ( $row = $oDb->getrow() ) {
		$usuarios[] = $row;
	}

	$oDb->Close();
	return [ 'result' => true, 'data' => $usuarios ];
}

function GetOne() {
	$id = filter_post('id');
	$oDb = create_conex();

	$r = $oDb->bind_params("SELECT id, username, user, rol FROM " . TABLA_USUARIOS . " WHERE id = ?", [$id]);
	$row = $oDb->getrow();

	$oDb->Close();
	return $row ? [ 'result' => true, 'data' => $row ] : [ 'result' => false, 'message' => 'Usuario no encontrado' ];
}

function Save() {
	$id       = filter_post('id');
	$username = filter_post('username');
	$user     = filter_post('user');
	$clave    = filter_post('clave');
	$rol      = filter_post('rol');

	if ( empty($username) || empty($user) ) {
		return [ 'result' => false, 'message' => 'Nombre y usuario son obligatorios' ];
	}

	$oDb = create_conex();

	// Verificar usuario duplicado
	$sqlCheck = "SELECT id FROM " . TABLA_USUARIOS . " WHERE user = ?" . (!empty($id) ? " AND id != ?" : "");
	$paramsCheck = [$user];
	if (!empty($id)) $paramsCheck[] = $id;

	if ( $oDb->bind_params($sqlCheck, $paramsCheck) ) {
		if ( $row = $oDb->getrow() ) {
			$oDb->Close();
			return [ 'result' => false, 'message' => 'El nombre de usuario ya está en uso' ];
		}
	}

	if ( !empty($id) ) {
		if ( !empty($clave) ) {
			$hashed_password = password_hash($clave, PASSWORD_DEFAULT);
			$sql = "UPDATE " . TABLA_USUARIOS . " SET username=?, user=?, pasw1=?, rol=? WHERE id=?";
			$success = $oDb->bind_params($sql, [$username, $user, $hashed_password, $rol, $id], true);
		} else {
			$sql = "UPDATE " . TABLA_USUARIOS . " SET username=?, user=?, rol=? WHERE id=?";
			$success = $oDb->bind_params($sql, [$username, $user, $rol, $id], true);
		}
	} else {
		if ( empty($clave) ) {
			$oDb->Close();
			return [ 'result' => false, 'message' => 'La contraseña es obligatoria para nuevos usuarios' ];
		}
		$hashed_password = password_hash($clave, PASSWORD_DEFAULT);
		$sql = "INSERT INTO " . TABLA_USUARIOS . " (username, user, pasw1, rol) VALUES (?,?,?,?)";
		$success = $oDb->bind_params($sql, [$username, $user, $hashed_password, $rol], true);
	}

	$oDb->Close();
	return $success ? [ 'result' => true ] : [ 'result' => false, 'message' => 'Error al guardar el usuario' ];
}

function Delete() {
	$id = filter_post('id');
	$oDb = create_conex();

	// Verificar que no sea el último admin
	$r = $oDb->bind_params("SELECT rol FROM " . TABLA_USUARIOS . " WHERE id = ?", [$id]);
	$row = $oDb->getrow();
	if ($row && $row['rol'] === 'admin') {
		$countR = $oDb->query("SELECT COUNT(*) as total FROM " . TABLA_USUARIOS . " WHERE rol = 'admin'");
		$countRow = $oDb->getrow();
		if ($countRow && $countRow['total'] <= 1) {
			$oDb->Close();
			return [ 'result' => false, 'message' => 'No se puede eliminar: es el único administrador' ];
		}
	}

	$success = $oDb->bind_params("DELETE FROM " . TABLA_USUARIOS . " WHERE id = ?", [$id], true);
	$oDb->Close();

	return $success ? [ 'result' => true ] : [ 'result' => false, 'message' => 'Error al eliminar el usuario' ];
}
?>