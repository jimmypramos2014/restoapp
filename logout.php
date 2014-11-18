<?php
require_once("common/sesion.class.php");
$sesion = new sesion();
//$usuario = new clsUsuario();
$idusuario = $sesion->get("idusuario");
$login = $sesion->get("login");
//$mensajeSesion = "";
if( $login == true ){
	//$rpta = $usuario->registrarSesion($idsesionuser, $idusuario);
	//if ($rpta > 0){
	$login = $sesion->get("login");
	$sesion->termina_sesion();
	$login = false;
	//}
	header("location: acceso.php");
}
?>