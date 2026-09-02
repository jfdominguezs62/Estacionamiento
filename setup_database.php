<?php
session_start();

define( "TWEB",  "../tvalweb2/"  );

include( TWEB . 'tmysql.php' );

define( 'DB_HOST',      "localhost" );
define( 'DB_DATABASE',  "estacionamiento_db");
define( 'DB_USER',      "root");
define( 'DB_PASSWORD',  "P3m3xCatalina*2026");
define( 'DB_PORT', 3306 );

function create_conex( $host = DB_HOST, $user = DB_USER, $pass = DB_PASSWORD, $database = DB_DATABASE ) {  
  return new TMySQL( $host, $user, $pass, $database );
}

echo "<h2>Configuración de Base de Datos - Estacionamiento</h2>";

// Primero conectar sin base de datos para crearla
$oDbServer = new TMySQL( DB_HOST, DB_USER, DB_PASSWORD, "" );

if ( !$oDbServer->execute("CREATE DATABASE IF NOT EXISTS estacionamiento_db CHARACTER SET utf8 COLLATE utf8_general_ci") ) {
    echo "<p style='color:red;'>❌ Error al crear la base de datos: " . $oDbServer->getError() . "</p>";
    $oDbServer->Close();
    exit;
}
echo "<p style='color:green;'>✅ Base de datos 'estacionamiento_db' creada/verificada.</p>";
$oDbServer->Close();

// Ahora conectar a la base de datos
$oDb = create_conex();

// Crear tabla de usuarios
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    user VARCHAR(50) NOT NULL UNIQUE,
    pasw1 VARCHAR(255) NOT NULL,
    rol VARCHAR(20) DEFAULT 'operador',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

if ( $oDb->execute($sql) ) {
    echo "<p style='color:green;'>✅ Tabla 'users' creada/verificada correctamente.</p>";
} else {
    echo "<p style='color:red;'>❌ Error al crear la tabla: " . $oDb->getError() . "</p>";
}

// Crear tabla de secciones
$sql = "CREATE TABLE IF NOT EXISTS secciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    capacidad INT NOT NULL DEFAULT 50,
    descripcion TEXT,
    estatus ENUM('activo','inactivo') DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

if ( $oDb->execute($sql) ) {
    echo "<p style='color:green;'>✅ Tabla 'secciones' creada/verificada correctamente.</p>";
} else {
    echo "<p style='color:red;'>❌ Error al crear la tabla: " . $oDb->getError() . "</p>";
}

// Migración: agregar columna estatus a secciones si no existe
$r = $oDb->query("SHOW COLUMNS FROM secciones LIKE 'estatus'");
if ( !$oDb->getrow() ) {
    if ( $oDb->execute("ALTER TABLE secciones ADD COLUMN estatus ENUM('activo','inactivo') DEFAULT 'activo' AFTER descripcion") ) {
        echo "<p style='color:green;'>✅ Columna 'estatus' agregada a tabla 'secciones'.</p>";
    }
}

// Crear tabla de tarifas
$sql = "CREATE TABLE IF NOT EXISTS tarifas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seccion_id INT NOT NULL,
    tipo_vehiculo ENUM('auto','moto','camioneta','bicicleta') NOT NULL,
    monto_hora DECIMAL(10,2) NOT NULL,
    monto_dia DECIMAL(10,2) DEFAULT 0,
    vigente_desde DATE NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seccion_id) REFERENCES secciones(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

if ( $oDb->execute($sql) ) {
    echo "<p style='color:green;'>✅ Tabla 'tarifas' creada/verificada correctamente.</p>";
} else {
    echo "<p style='color:red;'>❌ Error al crear la tabla: " . $oDb->getError() . "</p>";
}

// Crear tabla de registros (estacionamiento)
$sql = "CREATE TABLE IF NOT EXISTS registros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seccion_id INT NOT NULL,
    cajon VARCHAR(20) NOT NULL,
    tipo_vehiculo ENUM('auto','moto','camioneta','bicicleta') NOT NULL,
    placa VARCHAR(20) NOT NULL,
    marca VARCHAR(50) DEFAULT '',
    color VARCHAR(30) DEFAULT '',
    fecha_entrada DATETIME NOT NULL,
    fecha_salida DATETIME DEFAULT NULL,
    estado ENUM('dentro','fuera') DEFAULT 'dentro',
    registrado_por VARCHAR(100) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seccion_id) REFERENCES secciones(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

if ( $oDb->execute($sql) ) {
    echo "<p style='color:green;'>✅ Tabla 'registros' creada/verificada correctamente.</p>";
} else {
    echo "<p style='color:red;'>❌ Error al crear la tabla: " . $oDb->getError() . "</p>";
}

// Agregar columna cajon si no existe (migración)
$r = $oDb->query("SHOW COLUMNS FROM registros LIKE 'cajon'");
if ( !$oDb->getrow() ) {
    if ( $oDb->execute("ALTER TABLE registros ADD COLUMN cajon VARCHAR(20) NOT NULL AFTER seccion_id") ) {
        echo "<p style='color:green;'>✅ Columna 'cajon' agregada a tabla 'registros'.</p>";
    } else {
        echo "<p style='color:red;'>❌ Error al agregar columna cajon: " . $oDb->getError() . "</p>";
    }
}

// Agregar columnas marca y color si no existen (migración)
$r = $oDb->query("SHOW COLUMNS FROM registros LIKE 'marca'");
if ( !$oDb->getrow() ) {
    if ( $oDb->execute("ALTER TABLE registros ADD COLUMN marca VARCHAR(50) DEFAULT '' AFTER placa") ) {
        echo "<p style='color:green;'>✅ Columna 'marca' agregada a tabla 'registros'.</p>";
    }
}
$r = $oDb->query("SHOW COLUMNS FROM registros LIKE 'color'");
if ( !$oDb->getrow() ) {
    if ( $oDb->execute("ALTER TABLE registros ADD COLUMN color VARCHAR(30) DEFAULT '' AFTER marca") ) {
        echo "<p style='color:green;'>✅ Columna 'color' agregada a tabla 'registros'.</p>";
    }
}

// Crear tabla de pagos
$sql = "CREATE TABLE IF NOT EXISTS pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registro_id INT NOT NULL,
    monto_total DECIMAL(10,2) NOT NULL,
    horas DECIMAL(10,2) NOT NULL,
    cobro_fraccion TINYINT(1) DEFAULT 1 COMMENT '1=fraccion como hora completa, 0=solo horas completas',
    fecha_pago DATE NOT NULL,
    recibo VARCHAR(50) DEFAULT '',
    metodo_pago ENUM('efectivo','tarjeta','otro') DEFAULT 'efectivo',
    observaciones TEXT,
    registrado_por VARCHAR(100) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (registro_id) REFERENCES registros(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

if ( $oDb->execute($sql) ) {
    echo "<p style='color:green;'>✅ Tabla 'pagos' creada/verificada correctamente.</p>";
} else {
    echo "<p style='color:red;'>❌ Error al crear la tabla: " . $oDb->getError() . "</p>";
}

// Agregar columna cobro_fraccion si no existe (migración)
$r = $oDb->query("SHOW COLUMNS FROM pagos LIKE 'cobro_fraccion'");
if ( !$oDb->getrow() ) {
    if ( $oDb->execute("ALTER TABLE pagos ADD COLUMN cobro_fraccion TINYINT(1) DEFAULT 1 COMMENT '1=fraccion como hora completa, 0=solo horas completas' AFTER horas") ) {
        echo "<p style='color:green;'>✅ Columna 'cobro_fraccion' agregada a tabla 'pagos'.</p>";
    } else {
        echo "<p style='color:red;'>❌ Error al agregar columna cobro_fraccion: " . $oDb->getError() . "</p>";
    }
}

// Insertar secciones por defecto
$countSql = "SELECT COUNT(*) as total FROM secciones";
if ( $oDb->query($countSql) ) {
    $row = $oDb->getrow();
    if ( $row['total'] == 0 ) {
        $oDb->insert('secciones', ['nombre', 'capacidad', 'descripcion'], ['Principal', 100, 'Estacionamiento principal']);
        $oDb->insert('secciones', ['nombre', 'capacidad', 'descripcion'], ['VIP', 30, 'Estacionamiento preferencial VIP']);
        $oDb->insert('secciones', ['nombre', 'capacidad', 'descripcion'], ['Motos', 50, 'Sección exclusiva para motocicletas']);
        echo "<p style='color:green;'>✅ Secciones por defecto creadas (Principal, VIP, Motos).</p>";
    }
}

// Insertar tarifas por defecto si no existen
$countSql = "SELECT COUNT(*) as total FROM tarifas";
if ( $oDb->query($countSql) ) {
    $row = $oDb->getrow();
    if ( $row['total'] == 0 ) {
        $hoy = date('Y-m-d');
        // Tarifas para Principal
        $oDb->insert('tarifas', ['seccion_id','tipo_vehiculo','monto_hora','monto_dia','vigente_desde','activo'], [1,'auto',20,100,$hoy,1]);
        $oDb->insert('tarifas', ['seccion_id','tipo_vehiculo','monto_hora','monto_dia','vigente_desde','activo'], [1,'moto',10,50,$hoy,1]);
        $oDb->insert('tarifas', ['seccion_id','tipo_vehiculo','monto_hora','monto_dia','vigente_desde','activo'], [1,'camioneta',30,150,$hoy,1]);
        $oDb->insert('tarifas', ['seccion_id','tipo_vehiculo','monto_hora','monto_dia','vigente_desde','activo'], [1,'bicicleta',5,25,$hoy,1]);
        // Tarifas para VIP
        $oDb->insert('tarifas', ['seccion_id','tipo_vehiculo','monto_hora','monto_dia','vigente_desde','activo'], [2,'auto',40,200,$hoy,1]);
        $oDb->insert('tarifas', ['seccion_id','tipo_vehiculo','monto_hora','monto_dia','vigente_desde','activo'], [2,'moto',20,100,$hoy,1]);
        $oDb->insert('tarifas', ['seccion_id','tipo_vehiculo','monto_hora','monto_dia','vigente_desde','activo'], [2,'camioneta',50,250,$hoy,1]);
        $oDb->insert('tarifas', ['seccion_id','tipo_vehiculo','monto_hora','monto_dia','vigente_desde','activo'], [2,'bicicleta',10,50,$hoy,1]);
        // Tarifas para Motos
        $oDb->insert('tarifas', ['seccion_id','tipo_vehiculo','monto_hora','monto_dia','vigente_desde','activo'], [3,'auto',15,75,$hoy,1]);
        $oDb->insert('tarifas', ['seccion_id','tipo_vehiculo','monto_hora','monto_dia','vigente_desde','activo'], [3,'moto',8,40,$hoy,1]);
        $oDb->insert('tarifas', ['seccion_id','tipo_vehiculo','monto_hora','monto_dia','vigente_desde','activo'], [3,'camioneta',20,100,$hoy,1]);
        $oDb->insert('tarifas', ['seccion_id','tipo_vehiculo','monto_hora','monto_dia','vigente_desde','activo'], [3,'bicicleta',5,25,$hoy,1]);
        echo "<p style='color:green;'>✅ Tarifas por defecto creadas para todas las secciones y tipos de vehículo.</p>";
    }
}

// Crear usuario admin por defecto
$countSql = "SELECT COUNT(*) as total FROM users";
if ( $oDb->query($countSql) ) {
    $row = $oDb->getrow();
    if ( $row['total'] == 0 ) {
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $success = $oDb->insert(
            'users',
            [ 'username', 'user', 'pasw1', 'rol' ],
            [ 'Administrador', 'admin', $hashed_password, 'admin' ]
        );
        if ( $success ) {
            echo "<p style='color:green;'>✅ Usuario administrador creado:</p>";
            echo "<ul>";
            echo "<li><strong>Usuario:</strong> admin</li>";
            echo "<li><strong>Contraseña:</strong> admin123</li>";
            echo "<li><strong>Rol:</strong> admin</li>";
            echo "</ul>";
            echo "<p style='color:red;'><strong>⚠️ IMPORTANTE:</strong> Cambia la contraseña después de iniciar sesión.</p>";
        }
    }
}

$oDb->Close();

echo "<hr>";
echo "<p><a href='index.php'>Ir al Login →</a></p>";
?>