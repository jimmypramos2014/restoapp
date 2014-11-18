<?php
require '../../adata/Db.class.php';
require '../../bussiness/clientes.php';

$IdEmpresa = 1;
$IdCentro = 1;

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

$tipocliente = isset($_GET['tipocliente']) ? $_GET['tipocliente'] : '00';
$lastId = isset($_GET['lastId']) ? $_GET['lastId'] : '1';
$criterio = trim(strip_tags($_GET['criterio'])); 
$criterio = preg_replace('/\s+/', ' ', $criterio);

$parametros = array(
	'idempresa' => $IdEmpresa,
	'idcentro' => $IdCentro,
	'criterio' => $criterio,
	'tipocliente' => $tipocliente,
	'lastid' => $lastId );

$objData = new clsCliente();

$row = $objData->Listar('L', $parametros);

echo json_encode($row);
flush();
?>