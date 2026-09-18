<?php
$pageName = basename($_SERVER['PHP_SELF'], '.php');

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
?>

<div id="app-content">
  <nav class="navbar navbar-dark shadow-sm py-2 px-3" style="background: linear-gradient(135deg, #1a237e, #283593); position:sticky; top:0; z-index:1030;">
    <a class="navbar-brand fw-bold" href="menu.php" style="font-size:15px;">
      <i class="fas fa-parking me-1"></i> ParkingSys
    </a>
    <div class="d-flex align-items-center">
      <span class="text-white" style="font-size:12px;">
        <i class="fas fa-user-circle me-1"></i><?php echo htmlspecialchars($nombreUsuario); ?>
      </span>
    </div>
  </nav>

  <div class="container-fluid py-2 px-3" style="padding-bottom: 70px !important;">