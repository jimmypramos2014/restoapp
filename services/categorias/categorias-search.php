<?php
require '../../adata/Db.class.php';
require '../../bussiness/categoria.php';
$IdEmpresa = 1;
$IdCentro = 0;
/* prevent direct access to this page */
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if(!$isAjax) {
  $user_error = 'Access denied - direct call is not allowed...';
  trigger_error($user_error, E_USER_ERROR);
}
ini_set('display_errors',1);
 
/* if the 'term' variable is not sent with the request, exit */
if ( !isset($_REQUEST['idref']) ) {
	exit;
}

 
/* retrieve the search term that autocomplete sends */
$term = trim(strip_tags($_GET['idref'])); 
/* replace multiple spaces with one */
$term = preg_replace('/\s+/', ' ', $term);
 
$a_json = array();
$a_json_row = array();
 
$a_json_invalid = array(array("id" => "#", "value" => $term, "label" => "Only letters and digits are permitted..."));
$json_invalid = json_encode($a_json_invalid);
 
/* SECURITY HOLE *************************************************************** */
/* allow space, any unicode letter and digit, underscore and dash                */
if(preg_match("/[^\040\pL\pN_-]/u", $term)) {
  print $json_invalid;
  exit;
}
/* ***************************************************************************** */


$i = 0;
$idfield = '';
$objData = new clsCategoria();
$row = $objData->Listar("M", $IdEmpresa, $IdCentro, $term);
$countrows = count($row);

if ($row) {
	while ($i < $countrows){
		/*$id = htmlentities(stripslashes($row[$i][$idfield]));
		$nombre = htmlentities(stripslashes($row[$i]['tp_nombre']));*/
		$id = stripslashes($row[$i]['tm_idcategoria']);
		$nombre = stripslashes($row[$i]['tm_nombre']);
		
		$a_json_row["id"] = $id;
		$a_json_row["value"] = $nombre;
		$a_json_row["label"] = $nombre;
		array_push($a_json, $a_json_row);
		++$i;
	}
}
 
/* jQuery wants JSON data */
echo json_encode($a_json);
flush();
?>