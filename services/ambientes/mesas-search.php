<?php
require '../../adata/Db.class.php';
require '../../bussiness/mesas.php';

$IdEmpresa = 1;
$IdCentro = 1;

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if(!$isAjax) {
	$user_error = 'Access denied - direct call is not allowed...';
	trigger_error($user_error, E_USER_ERROR);
}
ini_set('display_errors',1);

if ( !isset($_REQUEST['idambiente']) ) {
	exit;
}

$row = array(array());

$tipobusqueda = isset($_GET['tipobusqueda']) ? $_GET['tipobusqueda'] : 'M';
$idambiente = isset($_GET['idambiente']) ? $_GET['idambiente'] : '0';
$objMesa = new clsMesa();

if ($tipobusqueda == 'M')
	$row = $objMesa->Listar($tipobusqueda, $idambiente);
elseif (($tipobusqueda == 'ATENCION') || ($tipobusqueda == 'NOTIFTV') || ($tipobusqueda == 'NOTIFATENCION')){
	if (($tipobusqueda == 'NOTIFTV') || ($tipobusqueda == 'NOTIFATENCION'))
		$row = $objMesa->Listar($tipobusqueda, $IdEmpresa, $IdCentro);
	else
		$row = $objMesa->Listar($tipobusqueda, $IdEmpresa, $IdCentro, $idambiente);
}
elseif ($tipobusqueda == 'NOTIFCOCINA')
	$row = $objMesa->Listar($tipobusqueda, $IdEmpresa, $IdCentro, '\'03\'');

echo json_encode($row);
flush();
?>