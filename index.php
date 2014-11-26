<?php
header('Content-type: text/html; charset=utf-8');
require('common/class.translation.php');
include("common/sesion.class.php");

$sesion = new sesion();
$idusuario = $sesion->get("idusuario");
$codigo = $sesion->get("codigo");
$login = $sesion->get("login");
$nombres = $sesion->get("nombres");
$idperfil = $sesion->get("idperfil");
$fotoUsuario = $sesion->get("foto");
$correoUsuario = $sesion->get("correo");

$lang = (isset($_GET['lang'])) ? $_GET['lang'] : 'es';
$translate = new Translator($lang);

$pag = "";
$subpag = "";
$op = "";
$pag = (isset($_GET['pag'])) ? $_GET['pag'] : 'inicio';
$subpag = (isset($_GET['subpag'])) ? $_GET['subpag'] : "";
$op = (isset($_GET['op'])) ? $_GET['op'] : "list";
$id = (isset($_GET['id'])) ? $_GET['id'] : "0";
$txtSearch = isset($_GET['txtSearch']) ? $_GET['txtSearch'] : '';
$firstLit = isset($_GET['firstLit']) ? $_GET['firstLit'] : '';
$showAppBar =  isset($_GET['showAppBar']) ? $_GET['showAppBar'] : '1';
$hdIdTipoOrg = isset($_GET['hdIdTipoOrg']) ? $_GET['hdIdTipoOrg'] : '1';

$isAction = false;
if (isset($_GET['fnGet']) || isset($_POST['fnPost']))
    $isAction = true;

if ($isAction == false) {
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, maximum-scale=1">
    <title>RESTORA APP</title>
    <link rel="stylesheet" href="styles/metro-bootstrap.css"/>
    <link rel="stylesheet" href="styles/metro-bootstrap-responsive.css"/>
    <link rel="stylesheet" href="styles/iconFont.css"/>
    <link rel="stylesheet" href="scripts/prettify/prettify.css"/>
    <link rel="stylesheet" href="scripts/metro-alert/css/MetroNotificationStyle.css" media="screen"/>
    <link rel="stylesheet" href="styles/jquery.custombox.css"/>
    <link rel="stylesheet" href="styles/custombox-modal.css"/>
    <link rel="stylesheet" href="styles/tooltip.css"/>
    <link rel="stylesheet" href="styles/styles.css"/>
    <link rel="stylesheet" href="styles/droparea-single.css"/>
    <link rel="stylesheet" href="styles/responsive-calendar.css"/>
    <link rel="stylesheet" href="styles/uploadfile.css"/>
    <link rel="stylesheet" href="styles/responsivemobilemenu.css"/>
    <style>
<?php
    if ($pag == 'inicio') {
?>
    .tile-area {
        padding-left: 40px;
        padding-top: 10px;
    }

    .tile-group {
        margin-left: 0 !important;
        margin-right: 40px !important;
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    #improved li {
        overflow:hidden;
        position:relative;
    }
<?php
    }
    else {
?>
    .tile-area {
        padding-left: 10px !important;
        padding-right: 10px !important;
        padding-top: 10px !important;
    }

    .grid, .row {
        margin: 0px !important;
    }

    #gvDatos {
        height: 100%;
        margin: 0px;
        overflow: auto;
        padding: 0px;
    }
<?php
        if ($pag == 'admin') {
            if ($subpag == 'productos') {
?>
    .colTwoPanel1 {
        width: 60%;
    }

    .moduloTwoPanel .colTwoPanel2 {
        width: 40%;
        overflow: auto !important;
    }
<?php
            }
        }
        elseif ($pag == 'procesos') {
            if ($subpag == 'ventas') {
?>
    #pnlMesas .tile-area,
    #pnlEstadoMesa .tile-area {
        margin-top: 0px !important;
        margin-left: 40px !important;
        padding-right: 0px !important;
    }

    #pnlMesas ul li {
        overflow: auto;
    }
    #pnlMesas ul li h2 {
        padding-left: 50px !important;
    }

    #pnlOrden .divContent {
        overflow: hidden;
    }

    #pnlEstadoMesa > h2 {
        padding-left: 10px;
    }
    
    /*#pnlVenta .modal-example-body {
        height: 290px;
    }  
    
    @media (max-width: 480px) {
        #pnlVenta .modal-example-body {
            height: 250px;
        } 
    }*/

    #gvProductos {
        height: 100%;
        margin: 0px;
        overflow: auto;
        padding: 0px;
    }
<?php
            }
            elseif ($subpag == 'cocina'){
?>
    .tile-group {
        margin:0 32px !important;
    }

    .tile-area {
        padding:10px !important;
    }

    .divContent {
        overflow: hidden !important;
    }

    .colTwoPanel1 {
        overflow: auto;
        width: 40%;
    }

    .moduloTwoPanel .colTwoPanel2 {
        width: 60%;
    }

<?php
            }
        }
    }
?>
    </style>
</head>
<body class="metro">
<?php 
}
if( $login == true )
    include("common/contents.php");
else
    header('location: acceso.php#login');
?>
</body>
</html>