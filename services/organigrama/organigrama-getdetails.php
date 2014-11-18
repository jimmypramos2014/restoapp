<?php
require '../../adata/Db.class.php';
require '../../bussiness/organigrama.php';

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

$id = isset($_GET['id']) ? $_GET['id'] : '0';

$objData = new clsOrganigrama();
$row = $objData->Listar('O', $id);

if (isset($row))
	echo json_encode($row);
flush();
?>