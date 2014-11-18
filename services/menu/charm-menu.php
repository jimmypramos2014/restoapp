<?php
require '../../adata/Db.class.php';
require '../../bussiness/menu.php';

$objMenu = new clsMenu();

/* prevent direct access to this page */
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if(!$isAjax) {
  $user_error = 'Access denied - direct call is not allowed...';
  trigger_error($user_error, E_USER_ERROR);
}
ini_set('display_errors',1);
 
/* if the 'tipomenu' variable is not sent with the request, exit */
if ( !isset($_REQUEST['tipomenu']) ) {
	exit;
}

$tipomenu = trim(strip_tags($_GET['tipomenu'])); 
$tipomenu = preg_replace('/\s+/', ' ', $tipomenu);

$idmenu = isset($_GET['idmenu']) ? $_GET['idmenu'] : '0';
$idperfil = isset($_GET['idperfil']) ? $_GET['idperfil'] : '0';
$tipoconsulta = isset($_GET['tipoconsulta']) ? $_GET['tipoconsulta'] : 'EDIT';

$row = $objMenu->GetControlsMenu($idmenu, $idperfil, $tipomenu, $tipoconsulta);
if (isset($row))
	echo json_encode($row);
flush();
?>