<?php
require '../../adata/Db.class.php';
require '../../bussiness/cartadia.php';

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if(!$isAjax) {
	$user_error = 'Access denied - direct call is not allowed...';
	trigger_error($user_error, E_USER_ERROR);
}
ini_set('display_errors',1);

if ( !isset($_REQUEST['tipobusqueda']) ) {
	exit;
}

$IdEmpresa = 1;
$IdCentro = 1;

$tipobusqueda = (isset($_GET['tipobusqueda'])) ? $_GET['tipobusqueda'] : 'DIAS';
$anho = (isset($_GET['anho'])) ? $_GET['anho'] : date('Y');
$mes = (isset($_GET['mes'])) ? $_GET['mes'] : date('m');

$parametros = array(
		'IdEmpresa' => $IdEmpresa,
		'IdCentro' => $IdCentro,
		'Year' => $anho,
		'Month' => $mes );

$objData = new clsCartaDia();
$row = $objData->ListarDiasAsignados($tipobusqueda, $parametros);

echo json_encode($row);
flush();
?>