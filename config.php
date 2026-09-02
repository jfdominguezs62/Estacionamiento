<?php
if ( !class_exists('Constants') ) {
  include( 'constants.php');
}	

if ( Constants::is_filejs() ) {
  createVarsPathJs();
}

// Paths
define( 'ROOT_PATH',  Constants::getpath_root() ); 
define( 'TWEB_PATH',  Constants::getpath_tweb() ); 
define( 'LIBS_PATH',  Constants::getpath_tweb() . 'libs/mylibs/' );
define( 'IMAGE_PATH', Constants::getpath_images() );
define( 'JS_PATH', 	  Constants::getpath_js() );
define( 'CSS_PATH', 	Constants::getpath_css() );

// Global Variables
define( "NAME_EMPRESA", 'Estacionamiento Central' );
define( 'APP_SESSION' , 'APP_ESTACIONAMIENTO');
define( 'LOGIN_PHP'   , 'index.php' );
define( 'COPYRIGHT'   , "© ParkingSys" );
define( 'APP_TITLE'   , "SISTEMA DE ESTACIONAMIENTO" );
define( "TWINDOW_CLR" , "#1a237e" );
define( 'BACKGROUND'	, '#f0f2f5' );
define( 'TBAR_COLOR'  , '#e9ecef' );
define( "FONT_FAMILY",  "Verdana, Segoe UI" );

// Load session management (tvalweb2)
include( Constants::getpath_tweb() . 'core.session.php' );

// Session timeout: 1 hora (3600 segundos)
$oSessionCfg = new TSession( APP_SESSION );
$oSessionCfg->nTimeOut = 3600;

// Errors
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);	

function createVarsPathJs() {
  $cHtml  = "<script>"; 
  $cHtml .= 'const _SUCCESS = "success";' . PHP_EOL;
  $cHtml .= 'const _ERROR   = "error";'   . PHP_EOL;
  $cHtml .= "	var path = {" . PHP_EOL;
  $cHtml .= '		tweb   : "' . Constants::getpath_tweb() . '",' 		. PHP_EOL;
  $cHtml .= '		images : "' . Constants::getpath_images() . '",' 	. PHP_EOL;
  $cHtml .= '		sound  : "' . Constants::getpath_sound() . '",' 	. PHP_EOL;
  $cHtml .= '		js     : "' . Constants::getpath_js() . '",' 			. PHP_EOL;
  $cHtml .= '		css    : "' . Constants::getpath_css() . '",' 		. PHP_EOL;
  $cHtml .= '		model  : "' . Constants::getpath_model() . '",' 	. PHP_EOL;
  $cHtml .= '		view   : "' . Constants::getpath_view() . '",' 		. PHP_EOL;
  $cHtml .= '		root   : "' . Constants::getpath_root() . '"' 		. PHP_EOL;
  $cHtml .= "	};" . PHP_EOL;
  $cHtml .= " console.log('From JS path : ', path);" . PHP_EOL;
  $cHtml .= "</script>";
  echo $cHtml;
}
?>