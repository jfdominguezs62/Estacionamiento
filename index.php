<?php
include( 'constants.php' );
Constants::unset();

include( Constants::getpath_root() . 'config.php' );
include( Constants::getpath_tweb() . 'core.php' );
include( Constants::getpath_tweb() . 'core.login.php' );

$oWeb = new TWeb( APP_TITLE );
$oWeb->lAwesome = true; 
$oWeb->SetIcon( IMAGE_PATH . 'favicon.ico' );
$oWeb->SetFontFamily( FONT_FAMILY );
$oWeb->Activate();

$oLogin = new TLogin( 'myLogin' );
$oLogin->cImage           = IMAGE_PATH . 'estacionamiento.png';
$oLogin->cTitle           = 'Sistema de Control de Estacionamiento';
$oLogin->cTextUser        = 'Usuario';
$oLogin->cTextPassword    = 'Contraseña';
$oLogin->lShowPassword    = false;
$oLogin->cTextLogin       = 'Iniciar Sesión';
$oLogin->cTextRegister    = 'Registrarse';
$oLogin->cTextForgot      = '';
$oLogin->bActionLogin     = 'login()';
$oLogin->bActionRegister  = 'register()';
$oLogin->Developer( '© ParkingSys', ' v1.0', 'creditos()' );
$oLogin->Activate();

$oWeb->End();
?>

<!-- Modal de Registro -->
<div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
      <div class="modal-header" style="background: linear-gradient(135deg, #1a237e, #283593); color: white;">
        <h5 class="modal-title font-weight-bold" id="registerModalLabel">Crear Nueva Cuenta</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4" style="background-color: #f8f9fa;">
        <form id="form-register" action="javascript:doRegister();" method="post">
          <div class="form-group mb-3">
            <label for="reg-username" class="text-secondary font-weight-bold">Nombre Completo</label>
            <input type="text" class="form-control" id="reg-username" placeholder="Ej. Juan Pérez" required style="border-radius: 8px;">
          </div>
          <div class="form-group mb-3">
            <label for="reg-email" class="text-secondary font-weight-bold">Usuario</label>
            <input type="text" class="form-control" id="reg-email" placeholder="Ej. juan" required style="border-radius: 8px;">
          </div>
          <div class="form-group mb-4">
            <label for="reg-password" class="text-secondary font-weight-bold">Contraseña</label>
            <input type="password" class="form-control" id="reg-password" placeholder="Mínimo 6 caracteres" required style="border-radius: 8px;">
          </div>
          <button type="submit" class="btn btn-primary btn-block btn-lg font-weight-bold shadow" style="border-radius: 8px; background: linear-gradient(135deg, #1a237e, #283593); border: none;">
            Registrarse <i class="fas fa-user-plus ms-2"></i>
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  var oLogin;

  $(function() {
    SetMessageServer("Cargando sistema...");
    oLogin = new TLogin("myLogin");
    oLogin.show();
    setTimeout(function() {
      var usr = document.getElementById('username');
      if (usr) usr.focus();
    }, 300);
  });

  function login() { 	
    var aPar = {};
  	aPar.action   = 'login';
  	aPar.usuario  = oLogin.getuser();
    aPar.clave    = oLogin.getpassword();

    MsgServer( path.model + 'login.php', response_login, aPar );					

    function response_login( dat ) {
  	  if ( dat.result ) {
        oLogin.hide();
        MsgNotify("¡Acceso concedido!", "success");
        location.href = path.view + 'menu.php';
      } else {
        MsgNotify("Acceso denegado. Verifique usuario o contraseña.", "error");
  	  }
    }  
  }	
  
  function register() {
    var modal = new bootstrap.Modal(document.getElementById('registerModal'));
    modal.show();
  }

  function doRegister() {
    var aPar = {};
    aPar.action   = 'register';
    aPar.nombre   = $('#reg-username').val();
    aPar.usuario  = $('#reg-email').val();
    aPar.clave    = $('#reg-password').val();

    MsgServer( path.model + 'login.php', response_register, aPar );

    function response_register( dat ) {
      if ( dat.result ) {
        MsgNotify("¡Registro exitoso! Ya puedes iniciar sesión.", "success");
        var modalEl = document.getElementById('registerModal');
        var modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        $('#reg-username').val('');
        $('#reg-email').val('');
        $('#reg-password').val('');
      } else {
        MsgNotify(dat.message || "Error al registrar el usuario.", "error");
      }
    }
  }

  function creditos() {
    MsgNotify("© Sistema de Estacionamiento - v1.0", "info");
  }
</script>