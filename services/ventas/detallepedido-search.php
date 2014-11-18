<?php
require '../../adata/Db.class.php';
require '../../bussiness/atencion.php';

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if(!$isAjax) {
	$user_error = 'Access denied - direct call is not allowed...';
	trigger_error($user_error, E_USER_ERROR);
}
ini_set('display_errors',1);

if ( !isset($_REQUEST['idatencion']) ) {
	exit;
}

$idatencion = isset($_GET['idatencion']) ? $_GET['idatencion'] : '0';

$objData = new clsAtencion();
$row = $objData->ListarDetallePedidos($idatencion);
echo json_encode($row);
flush();
?>