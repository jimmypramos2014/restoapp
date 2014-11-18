<?php
require '../../adata/Db.class.php';
require '../../bussiness/ambientes.php';
require '../../bussiness/mesas.php';

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if(!$isAjax) {
	$user_error = 'Access denied - direct call is not allowed...';
	trigger_error($user_error, E_USER_ERROR);
}
ini_set('display_errors',1);

if ( !isset($_REQUEST['id']) ) {
	exit;
}

$row = array(array());

$tipo = (isset($_GET['tipo'])) ? $_GET['tipo'] : '00';
$id = isset($_GET['id']) ? $_GET['id'] : '0';

if ($tipo == '00')
	$objData = new clsAmbiente();
else
	$objData = new clsMesa();
$row = $objData->Listar('O', $id);

if (isset($row))
	echo json_encode($row);
flush();
?>