<?php
require '../../adata/Db.class.php';
require '../../bussiness/insumos.php';

$IdEmpresa = '1';
$IdCentro = '1';

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if(!$isAjax) {
	$user_error = 'Access denied - direct call is not allowed...';
	trigger_error($user_error, E_USER_ERROR);
}
ini_set('display_errors',1);
$row = array(array());

$tipobusqueda = isset($_GET['tipobusqueda']) ? $_GET['tipobusqueda'] : 'L';
$idproducto = isset($_GET['idproducto']) ? $_GET['idproducto'] : '0';
$idalmacen = isset($_GET['idalmacen']) ? $_GET['idalmacen'] : '0';
$criterio = isset($_GET['criterio']) ? $_GET['criterio'] : '';

$parametros = array(
	'IdEmpresa' => $IdEmpresa,
	'IdCentro' => $IdCentro,
	'IdProducto' => $idproducto,
	'IdAlmacen' => $idalmacen,
	'criterio' => $criterio);

$objData = new clsInsumo();
$row = $objData->Listar($tipobusqueda, $parametros);

if (isset($row))
	echo json_encode($row);
flush();
?>