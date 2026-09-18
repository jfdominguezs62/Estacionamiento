<?php
echo '</div>'; // container-fluid
echo '</div>'; // app-content
?>

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

/* Mobile responsive */
@media(max-width:768px){
  .container-fluid{padding-left:10px!important;padding-right:10px!important;padding-top:8px!important}
  h4.fw-bold{font-size:16px;margin-bottom:12px!important}
  .card{border-radius:10px!important}
  .card-body{padding:10px!important}

  /* Tabs scrollable */
  .nav-tabs{flex-wrap:nowrap!important;overflow-x:auto;-webkit-overflow-scrolling:touch;scrollbar-width:none}
  .nav-tabs::-webkit-scrollbar{display:none}
  .nav-tabs .nav-item{flex:0 0 auto}
  .nav-tabs .nav-link{font-size:11px;padding:6px 10px;white-space:nowrap}

  /* Tables scrollable */
  .table-responsive{overflow-x:auto;-webkit-overflow-scrolling:touch}

  /* Modals full width on mobile */
  .modal-dialog{margin:8px!important;max-width:calc(100vw - 16px)!important}
  .modal-body{padding:12px!important}

  /* Summary cards 2 cols */
  .row .col-md-3,.row .col-md-4{flex:0 0 50%;max-width:50%}

  /* Entry form fields */
  .entry-field{margin-bottom:6px}
  .entry-field label{font-size:10px;margin-bottom:1px}
  .entry-field input,.entry-field select{font-size:13px;padding:4px 6px}

  /* Exit modal info row */
  .exit-info-row .col{padding:4px 2px}
  .exit-info-row small{font-size:9px}
  .exit-info-row span,.exit-info-row .badge{font-size:11px}

  /* Comprobante */
  .comprobante-row{font-size:12px;margin-bottom:4px!important}
  .comprobante-row .col-6{padding:2px 4px}
}
</style>

<script>
function logout(){
  MsgServer(path.model+'login.php',function(dat){
    if(dat.result){MsgNotify("Sesión cerrada.","success");location.href='../index.php';}
  },{action:'logout'});
}
</script>
<?php $oWeb->End(); ?>