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

if ( !isset($_REQUEST['tipodata']) ) {
	exit;
}

$IdEmpresa = 1;
$IdCentro = 1;
$tipodata = isset($_GET['tipodata']) ? $_GET['tipodata'] : '0';
$fecha = isset($_GET['fecha']) ? $_GET['fecha']: '0';
$esfavorito = isset($_GET['esfavorito']) ? $_GET['esfavorito']: '';

$parametros = array(
		'IdEmpresa' => $IdEmpresa,
		'IdCentro' => $IdCentro,
		'criterio' => '',
		'idcategoria' => '0',
		'idsubcategoria' => '0',
		'tipoMenuDia' => $tipodata,
		'fechaMenu' => $fecha,
		'esfavorito' => $esfavorito,
		'lastid' => '' );

$objData = new clsCartaDia();
$row = $objData->ListarAsignaciones('L', $parametros);

echo json_encode($row);
flush();
?>