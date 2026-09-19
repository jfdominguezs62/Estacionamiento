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
$oLogin->cImageCss       = 'login-logo';
$oLogin->cTitle           = 'Sistema de Control de Estacionamiento';
$oLogin->cTextUser        = 'Usuario';
$oLogin->cTextPassword    = 'Contraseña';
$oLogin->lShowPassword    = false;
$oLogin->cTextLogin       = 'Iniciar Sesión';
$oLogin->cTextRegister    = '';
$oLogin->cTextForgot      = '';
$oLogin->bActionLogin     = 'login()';
$oLogin->bActionRegister  = '';
$oLogin->Developer( '© ParkingSys', ' v1.0', 'creditos()' );
$oLogin->Activate();

$oWeb->End();
?>

<style>
.login-logo { 
  border: none !important; 
  box-shadow: none !important; 
  outline: none !important; 
  background: transparent !important; 
  border-radius: 50%; 
}
.modal-login .avatar.login-logo {
  background: transparent !important;
  box-shadow: none !important;
}
</style>

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

  function creditos() {
    MsgNotify("© Sistema de Estacionamiento - v1.0", "info");
  }
</script>