<?php
require '../../adata/Db.class.php';
require '../../bussiness/productos.php';
require '../../bussiness/precios.php';
require '../../bussiness/cartadia.php';

$IdEmpresa = '1';
$IdCentro = '1';

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
$idcategoria = isset($_GET['idcategoria']) ? $_GET['idcategoria'] : '0';
$idsubcategoria = isset($_GET['idsubcategoria']) ? $_GET['idsubcategoria'] : '0';
$lastId = isset($_GET['lastId']) ? $_GET['lastId'] : '1';
$criterio = trim(strip_tags($_GET['criterio'])); 
$criterio = preg_replace('/\s+/', ' ', $criterio);

if ($tipobusqueda == '02'){
	$parametros = array(
		'IdEmpresa' => $IdEmpresa,
		'IdCentro' => $IdCentro,
		'criterio' => $criterio,
		'idcategoria' => $idcategoria,
		'idsubcategoria' => $idsubcategoria,
		'tipoMenuDia' => '',
		'esfavorito' => '',
		'fechaMenu' => date('Y-m-d'),
		'lastid' => $lastId );
}
else {
	$parametros = array(
		'criterio' => $criterio,
		'idcategoria' => $idcategoria,
		'idsubcategoria' => $idsubcategoria,
		'lastid' => $lastId );
}

$objProducto = new clsProducto();
$objPrecioProd = new clsPrecio();
$objCartaPrecioProd = new clsCartaDia();

if ($tipobusqueda == '00')
	$row = $objProducto->Listar("L", $parametros);
elseif (($tipobusqueda == '01') || ($tipobusqueda == '03'))
	$row = $objPrecioProd->Listar("ListPrecioProducto", $parametros);
elseif ($tipobusqueda == '02')
	$row = $objCartaPrecioProd->ListarAsignaciones('LISTAPRECIOS', $parametros);

if (isset($row))
	echo json_encode($row);
flush();
?>