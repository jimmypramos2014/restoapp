<?php
require '../../adata/Db.class.php';
require '../../bussiness/receta.php';

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
$tipomenudia = isset($_GET['tipomenudia']) ? $_GET['tipomenudia'] : '0';

$parametros = array(
	'IdEmpresa' => $IdEmpresa,
	'IdCentro' => $IdCentro,
	'IdProducto' => $idproducto,
	'TipoMenuDia' => $tipomenudia);

$objData = new clsReceta();
$row = $objData->Listar($tipobusqueda, $parametros);

if (isset($row))
	echo json_encode($row);
flush();
?>