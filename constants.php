<?php
if ( session_status() == PHP_SESSION_NONE ) { 
  session_start(); 
}

define( "TWEB",  "../tvalweb2/"  );
define( "SLASH", "/"         ); 

class Constants {

  public static function setpath_root( $value ) {
    $_SESSION['root'] = $value;
  }

  public static function getpath_root() {
    $path_root = "./";
    if ( isset( $_SESSION['root'] ) ) {
      $path_root = $_SESSION['root'];
    }  
    return $path_root;
  }

  public static function getpath_tweb() {
    $root = self::getpath_root();
    if ( $root === "./" || $root === "" ) {
      return "../tvalweb2/";    
    } else {
      return rtrim($root, '/') . "/../tvalweb2/";
    }  
  }

  public static function getpath_js() {
    return self::getpath_root() . "js" . SLASH;
  }

  public static function getpath_css() {
    return self::getpath_root() . "css" . SLASH;
  }

  public static function getpath_view() {
    return self::getpath_root() . "view" . SLASH;
  }

  public static function getpath_model() {
    return self::getpath_root() . "model" . SLASH;
  }

  public static function getpath_images() {
    return self::getpath_root() . "images" . SLASH;
  }

  public static function getpath_sound() {
    return self::getpath_root() . "sound" . SLASH;
  }

  public static function create_filejs( $value = true ) {
    $_SESSION['create_js'] = $value; 
  }

  public static function is_filejs() {
    if ( isset( $_SESSION['create_js'] ) ) {
      return $_SESSION['create_js'];
    } else {
      return true;
    }  
  }

  public static function unset() {
    unset( $_SESSION['root'] );
    unset( $_SESSION['create_js'] );
  }

}
?>