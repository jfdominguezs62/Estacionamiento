<?php
/**
 * view_header.php - Cabecera común para todas las vistas del módulo
 * Incluye: constants, config, core, sidebar, session validation, navbar
 */

$currentPage = basename($_SERVER['PHP_SELF'], '.php');

$oSession = new TSession( APP_SESSION );
$oSession->lExeError = false;
if ( !$oSession->Valid() ) {
  header("Location: ../index.php");
  exit;
}

$nombreUsuario = $oSession->GetVar('usuario');
$rolUsuario = $oSession->GetVar('rol') ?: 'operador';

$oWeb = new TWeb( APP_TITLE );
$oWeb->lAwesome = true; 
$oWeb->SetIcon( IMAGE_PATH . 'favicon.ico' );
$oWeb->SetFontFamily( FONT_FAMILY );
$oWeb->Activate();

$sidebar = new TSidebar(null, 'mainSidebar', '', [], '260px', 'sidebar bg-dark');
$sidebar->TopTogglePos = 13;

$sidebar->AddItem('Panel Control', "location.href='dashboard.php'", 'fas fa-tachometer-alt');
$sidebar->AddItem('Secciones', "location.href='secciones.php'", 'fas fa-th-large');
$sidebar->AddItem('Tarifas', "location.href='tarifas.php'", 'fas fa-tags');
$sidebar->AddItem('Registro Entrada/Salida', "location.href='registros.php'", 'fas fa-car');
$sidebar->AddItem('Pagos', "location.href='pagos.php'", 'fas fa-money-check-alt');
$sidebar->AddItem('Reportes', "location.href='reportes.php'", 'fas fa-chart-bar');

if ($rolUsuario === 'admin') {
    $sidebar->AddItem('Usuarios', "location.href='usuarios.php'", 'fas fa-users-cog');
}

$sidebar->AddSeparator();
$sidebar->AddItem('Menú Principal', "location.href='menu.php'", 'fas fa-th-large');
$sidebar->AddItem('Cerrar Sesión', "logout()", 'fas fa-sign-out-alt');

$sidebar->Activate();
?>

<div id="app-content" style="margin-left: 260px; transition: margin-left 0.15s;">
  <nav class="navbar navbar-expand-lg navbar-dark shadow-sm py-3 px-4" style="background: linear-gradient(135deg, #1a237e, #283593);">
    <a class="navbar-brand fw-bold" href="menu.php">
      <i class="fas fa-parking me-2"></i> Sistema de Estacionamiento
    </a>
    <div class="ms-auto d-flex align-items-center">
      <span class="text-white me-3">
        <i class="fas fa-user-circle me-1"></i> <span class="fw-bold"><?php echo htmlspecialchars($nombreUsuario); ?></span>
        <span class="badge bg-light text-dark ms-1"><?php echo strtoupper($rolUsuario); ?></span>
      </span>
    </div>
  </nav>

  <div class="container-fluid py-4 px-4">