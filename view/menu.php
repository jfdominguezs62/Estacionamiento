<?php 
include ( '../constants.php' );
Constants::setpath_root  ("../");
Constants::create_filejs( true );

include ( Constants::getpath_root() . 'config.php' );
include ( Constants::getpath_tweb() . 'core.php' );
include ( Constants::getpath_tweb() . 'core.sidebar.php' );
include ( Constants::getpath_root() . 'helpers.php' );

$oSession = new TSession( APP_SESSION );
$oSession->lExeError = false;
if ( !$oSession->Valid() ) {
  header("Location: ../index.php");
  exit;
}

$oWeb = new TWeb( APP_TITLE );
$oWeb->lAwesome = true; 
$oWeb->SetIcon( IMAGE_PATH . 'favicon.ico' );
$oWeb->SetFontFamily( FONT_FAMILY );
$oWeb->Activate();

$nombreUsuario = $oSession->GetVar('usuario');
$rolUsuario = $oSession->GetVar('rol') ?: 'operador';

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
$sidebar->AddItem('Cerrar Sesión', "logout()", 'fas fa-sign-out-alt');
?>

<div id="app-content" style="margin-left: 260px; transition: margin-left 0.15s;">
  <nav class="navbar navbar-expand-lg navbar-dark shadow-sm py-3 px-4" style="background: linear-gradient(135deg, #1a237e, #283593);">
    <a class="navbar-brand font-weight-bold" href="menu.php">
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
    <div class="row justify-content-center mb-4">
      <div class="col-lg-10">
        <div class="card border-0 shadow-lg" style="border-radius: 16px; background: rgba(255, 255, 255, 0.95);">
          <div class="card-body p-4 text-center">
            <h3 class="fw-bold text-dark mb-1">¡Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?>!</h3>
            <p class="text-muted">Selecciona un módulo del menú lateral para comenzar</p>
          </div>
        </div>
      </div>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="row">
          <div class="col-md-4 mb-3">
            <a href="registros.php" class="text-decoration-none">
              <div class="card border-0 shadow-sm h-100 text-center" style="border-radius: 16px; transition: transform .2s;"
                   onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform=''">
                <div class="card-body p-4">
                  <div class="rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 65px; height: 65px; background: linear-gradient(135deg, #1a237e, #283593);">
                    <i class="fas fa-car text-white" style="font-size: 28px;"></i>
                  </div>
                  <h5 class="fw-bold text-dark">Registro Entrada/Salida</h5>
                  <small class="text-muted">Registrar vehículos</small>
                </div>
              </div>
            </a>
          </div>

          <div class="col-md-4 mb-3">
            <a href="pagos.php" class="text-decoration-none">
              <div class="card border-0 shadow-sm h-100 text-center" style="border-radius: 16px; transition: transform .2s;"
                   onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform=''">
                <div class="card-body p-4">
                  <div class="rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 65px; height: 65px; background: linear-gradient(135deg, #1b5e20, #2e7d32);">
                    <i class="fas fa-money-check-alt text-white" style="font-size: 28px;"></i>
                  </div>
                  <h5 class="fw-bold text-dark">Pagos</h5>
                  <small class="text-muted">Cobrar estacionamiento</small>
                </div>
              </div>
            </a>
          </div>

          <div class="col-md-4 mb-3">
            <a href="dashboard.php" class="text-decoration-none">
              <div class="card border-0 shadow-sm h-100 text-center" style="border-radius: 16px; transition: transform .2s;"
                   onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform=''">
                <div class="card-body p-4">
                  <div class="rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 65px; height: 65px; background: linear-gradient(135deg, #0d47a1, #1565c0);">
                    <i class="fas fa-tachometer-alt text-white" style="font-size: 28px;"></i>
                  </div>
                  <h5 class="fw-bold text-dark">Panel Control</h5>
                  <small class="text-muted">Resumen general</small>
                </div>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php $sidebar->Activate(); ?>

<script>
  document.getElementById('app-main').style.marginLeft = '260px';

  function logout() {
    MsgServer( path.model + 'login.php', function(dat) {
      if ( dat.result ) {
        MsgNotify("Sesión cerrada.", "success");
        location.href = '../index.php';
      }
    }, { action: 'logout' });
  }
</script>