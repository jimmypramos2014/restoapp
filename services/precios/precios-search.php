<?php
require '../../adata/Db.class.php';
require '../../bussiness/precios.php';

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if(!$isAjax) {
	$user_error = 'Access denied - direct call is not allowed...';
	trigger_error($user_error, E_USER_ERROR);
}
ini_set('display_errors',1);

if ( !isset($_REQUEST['idmoneda']) ) {
	exit;
}

$row = array(array());
$IdEmpresa = 1;
$idmoneda = isset($_GET['idmoneda']) ? $_GET['idmoneda'] : '0';
$listProductos = isset($_GET['listProductos']) ? $_GET['listProductos']: '0';

$objPrecioProd = new clsPrecio();
$row = $objPrecioProd->GetListPricesByCurrency($IdEmpresa, $idmoneda, $listProductos);

if (isset($row))
	echo json_encode($row);
flush();
?>