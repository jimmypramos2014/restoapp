<?php
require '../../adata/Db.class.php';
require '../../bussiness/ventas.php';

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if(!$isAjax) {
	$user_error = 'Access denied - direct call is not allowed...';
	trigger_error($user_error, E_USER_ERROR);
}
ini_set('display_errors',1);

if ( !isset($_REQUEST['criterio']) ) {
	exit;
}

$row = array(array());

$tipobusqueda = isset($_GET['tipobusqueda']) ? $_GET['tipobusqueda'] : '00';
$idcargo = isset($_GET['idcargo']) ? $_GET['idcargo'] : '0';
$lastId = isset($_GET['lastId']) ? $_GET['lastId'] : '1';
$criterio = trim(strip_tags($_GET['criterio'])); 
$criterio = preg_replace('/\s+/', ' ', $criterio);

$parametros = array(
	'criterio' => $criterio,
	'idcargo' => $idcargo,
	'lastid' => $lastId );

$objVenta = new clsVenta();

if ($tipobusqueda == '00')
	$row = $objVenta->Listar("L", $parametros);

if (isset($row))
	echo json_encode($row);
flush();
?>