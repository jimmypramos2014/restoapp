<?php
require '../../adata/Db.class.php';
require '../../bussiness/usuarios.php';
header('Content-type: application/json');

$valid = '';
$objUsuario = new clsUsuario();

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if(!$isAjax) {
	$user_error = 'Access denied - direct call is not allowed...';
	trigger_error($user_error, E_USER_ERROR);
}
ini_set('display_errors',1);

if ( !isset($_REQUEST['txtUsuario']) )
	exit;

$username = trim(strip_tags($_REQUEST['txtUsuario'])); 
$username = preg_replace('/\s+/', ' ', $username);

$row = $objUsuario->checkUsername($username);
$countrow = count($row);
if ($countrow == 0)
	$valid = 'true';
else
	$valid = 'false';
echo $valid;
?>