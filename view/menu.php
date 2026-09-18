<?php
include ( '../constants.php' );
Constants::setpath_root  ("../");
Constants::create_filejs( true );

include ( Constants::getpath_root() . 'config.php' );
include ( Constants::getpath_tweb() . 'core.php' );
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
$pageName = basename($_SERVER['PHP_SELF'], '.php');
?>

<div id="app-content">
  <nav class="navbar navbar-dark shadow-sm py-2 px-3" style="background: linear-gradient(135deg, #1a237e, #283593); position:sticky; top:0; z-index:1030;">
    <a class="navbar-brand fw-bold" href="menu.php" style="font-size:15px;">
      <i class="fas fa-parking me-1"></i> ParkingSys
    </a>
    <div class="d-flex align-items-center gap-2">
      <span class="text-white" style="font-size:12px;">
        <i class="fas fa-user-circle me-1"></i><?php echo htmlspecialchars($nombreUsuario); ?>
      </span>
      <a href="#" onclick="logout();return false;" class="text-white" style="font-size:13px;">
        <i class="fas fa-sign-out-alt"></i>
      </a>
    </div>
  </nav>

  <div class="container-fluid py-3 px-3" style="padding-bottom:70px!important;">
    <div class="text-center mb-3">
      <h5 class="fw-bold text-dark">Hola, <?php echo htmlspecialchars($nombreUsuario); ?></h5>
      <small class="text-muted">Selecciona un módulo</small>
    </div>
    <div class="row g-2">
      <div class="col-6">
        <a href="registros.php" class="text-decoration-none">
          <div class="card border-0 shadow-sm text-center h-100" style="border-radius:14px;">
            <div class="card-body py-3">
              <div class="rounded-circle d-inline-flex justify-content-center align-items-center mb-2" style="width:50px;height:50px;background:linear-gradient(135deg,#1a237e,#283593);">
                <i class="fas fa-car text-white" style="font-size:22px;"></i>
              </div>
              <h6 class="fw-bold text-dark mb-0" style="font-size:13px;">Vehículos</h6>
              <small class="text-muted" style="font-size:10px;">Entrada / Salida</small>
            </div>
          </div>
        </a>
      </div>
      <div class="col-6">
        <a href="pagos.php" class="text-decoration-none">
          <div class="card border-0 shadow-sm text-center h-100" style="border-radius:14px;">
            <div class="card-body py-3">
              <div class="rounded-circle d-inline-flex justify-content-center align-items-center mb-2" style="width:50px;height:50px;background:linear-gradient(135deg,#1b5e20,#2e7d32);">
                <i class="fas fa-dollar-sign text-white" style="font-size:22px;"></i>
              </div>
              <h6 class="fw-bold text-dark mb-0" style="font-size:13px;">Pagos</h6>
              <small class="text-muted" style="font-size:10px;">Cobrar parking</small>
            </div>
          </div>
        </a>
      </div>
      <div class="col-6">
        <a href="dashboard.php" class="text-decoration-none">
          <div class="card border-0 shadow-sm text-center h-100" style="border-radius:14px;">
            <div class="card-body py-3">
              <div class="rounded-circle d-inline-flex justify-content-center align-items-center mb-2" style="width:50px;height:50px;background:linear-gradient(135deg,#0d47a1,#1565c0);">
                <i class="fas fa-tachometer-alt text-white" style="font-size:22px;"></i>
              </div>
              <h6 class="fw-bold text-dark mb-0" style="font-size:13px;">Dashboard</h6>
              <small class="text-muted" style="font-size:10px;">Resumen</small>
            </div>
          </div>
        </a>
      </div>
      <div class="col-6">
        <a href="reportes.php" class="text-decoration-none">
          <div class="card border-0 shadow-sm text-center h-100" style="border-radius:14px;">
            <div class="card-body py-3">
              <div class="rounded-circle d-inline-flex justify-content-center align-items-center mb-2" style="width:50px;height:50px;background:linear-gradient(135deg,#e65100,#ef6c00);">
                <i class="fas fa-chart-bar text-white" style="font-size:22px;"></i>
              </div>
              <h6 class="fw-bold text-dark mb-0" style="font-size:13px;">Reportes</h6>
              <small class="text-muted" style="font-size:10px;">Estadísticas</small>
            </div>
          </div>
        </a>
      </div>
      <?php if ($rolUsuario === 'admin'): ?>
      <div class="col-6">
        <a href="secciones.php" class="text-decoration-none">
          <div class="card border-0 shadow-sm text-center h-100" style="border-radius:14px;">
            <div class="card-body py-3">
              <div class="rounded-circle d-inline-flex justify-content-center align-items-center mb-2" style="width:50px;height:50px;background:linear-gradient(135deg,#4a148c,#6a1b9a);">
                <i class="fas fa-th-large text-white" style="font-size:22px;"></i>
              </div>
              <h6 class="fw-bold text-dark mb-0" style="font-size:13px;">Secciones</h6>
              <small class="text-muted" style="font-size:10px;">Zonas</small>
            </div>
          </div>
        </a>
      </div>
      <div class="col-6">
        <a href="tarifas.php" class="text-decoration-none">
          <div class="card border-0 shadow-sm text-center h-100" style="border-radius:14px;">
            <div class="card-body py-3">
              <div class="rounded-circle d-inline-flex justify-content-center align-items-center mb-2" style="width:50px;height:50px;background:linear-gradient(135deg,#b71c1c,#c62828);">
                <i class="fas fa-tags text-white" style="font-size:22px;"></i>
              </div>
              <h6 class="fw-bold text-dark mb-0" style="font-size:13px;">Tarifas</h6>
              <small class="text-muted" style="font-size:10px;">Configurar</small>
            </div>
          </div>
        </a>
      </div>
      <div class="col-6">
        <a href="usuarios.php" class="text-decoration-none">
          <div class="card border-0 shadow-sm text-center h-100" style="border-radius:14px;">
            <div class="card-body py-3">
              <div class="rounded-circle d-inline-flex justify-content-center align-items-center mb-2" style="width:50px;height:50px;background:linear-gradient(135deg,#004d40,#00695c);">
                <i class="fas fa-users-cog text-white" style="font-size:22px;"></i>
              </div>
              <h6 class="fw-bold text-dark mb-0" style="font-size:13px;">Usuarios</h6>
              <small class="text-muted" style="font-size:10px;">Gestionar</small>
            </div>
          </div>
        </a>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Bottom Navigation Bar -->
<nav class="bottom-nav d-lg-none">
  <a href="dashboard.php" class="bottom-nav-item <?php echo $pageName=='dashboard'?'active':''; ?>">
    <i class="fas fa-home"></i><span>Inicio</span>
  </a>
  <a href="registros.php" class="bottom-nav-item <?php echo $pageName=='registros'?'active':''; ?>">
    <i class="fas fa-car"></i><span>Vehículos</span>
  </a>
  <a href="pagos.php" class="bottom-nav-item <?php echo $pageName=='pagos'?'active':''; ?>">
    <i class="fas fa-dollar-sign"></i><span>Pagos</span>
  </a>
  <a href="reportes.php" class="bottom-nav-item <?php echo $pageName=='reportes'?'active':''; ?>">
    <i class="fas fa-chart-bar"></i><span>Reportes</span>
  </a>
  <a href="menu.php" class="bottom-nav-item <?php echo $pageName=='menu'?'active':''; ?>">
    <i class="fas fa-bars"></i><span>Más</span>
  </a>
</nav>

<style>
.bottom-nav{position:fixed;bottom:0;left:0;right:0;background:#1a237e;display:flex;z-index:1050;box-shadow:0 -2px 10px rgba(0,0,0,.15);padding:0;height:56px}
.bottom-nav-item{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;color:rgba(255,255,255,.65);text-decoration:none;font-size:10px;padding:4px 0;transition:color .2s}
.bottom-nav-item i{font-size:18px;margin-bottom:2px}
.bottom-nav-item.active,.bottom-nav-item:active{color:#fff}
.bottom-nav-item.active i{color:#64b5f6}
@media(min-width:992px){.bottom-nav{display:none!important}}
</style>

<script>
function logout(){
  MsgServer(path.model+'login.php',function(dat){
    if(dat.result){MsgNotify("Sesión cerrada.","success");location.href='../index.php';}
  },{action:'logout'});
}
</script>
<?php $oWeb->End(); ?>