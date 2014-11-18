<?php 
include("bussiness/usuarios.php");

$objData = new clsUsuario();

$activeLetter = '';
$i = 0;

$linksearch = "?pag=$pag&subpag=$subpag&op=$op&txtSearch=$txtSearch&firstLit=";


if (isset($_POST['fnPost'])){
    if (isset($_POST['multiDelete'])){
        $paramVars = isset($_POST['paramVars']) ? $_POST['paramVars'] : '0';
        $listIds = str_replace('|', ',', $paramVars);
        $rpta = $objData->MultiDelete($listIds);
    }
    $jsondata = array('rpta' => $rpta);
    echo json_encode($jsondata);
    exit(0);
}

if(isset($_GET["lastid"]) && $_GET["lastid"] != "0"){
    $lastid = $_GET['lastid'];
}
else {
    $lastid = 0;
}

$busqueda = $firstLit != "" ? $firstLit.substr($txtSearch, 1, strlen($txtSearch)) : $txtSearch;

$parametros = array(
'criterio' => $busqueda,
'lastid' => $lastid );

$row = $objData->Listar("1", $parametros);

if (isset($_GET['viaAjax'])){
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    if(!$isAjax) {
      $user_error = 'Access denied - direct call is not allowed...';
      trigger_error($user_error, E_USER_ERROR);
    }
    echo json_encode($row);
    exit (0);
}

$countrows = count($row);
?>

<script type="text/javascript">

    $(document).ready(function(){
        intializeComponents();

        $("a.back-button").click(function(){
            window.parent.hideFrame();
            return false;
        });
    });

    function intializeComponents(){
        $('#btnSearch').click(function () {
            BuscarDatos('0');
            return false;
        });

        $("#btnPermisos").click(function(){
            window.location = "?pag=seguridad&subpag=permisos&op=form";
            return false;
        }); 

        $('#btnShowMore').click(function () {
            $(this).html("<img src='images/loading.gif' alt='loading'/>");
            var LastId  = $("#tblDatos tbody tr:last").attr("rel");
            BuscarDatos(LastId);
            return false;
        });
    }

    function BuscarDatos(lastid){
        var urlData = "";
        urlData = "fnGet=fnGet";
        urlData += "&pag=<?php echo $pag; ?>&subpag=<?php echo $subpag; ?>&op=<?php echo $op; ?>";
        urlData += "&firstLit=" + $('.pagination ul li.active a').attr('rel');
        urlData += "&txtSearch=" + $('#txtSearch').val();
        urlData += "&lastid=" + lastid;
        urlData += "&showAppBar=0";
        urlData += "&viaAjax=1";

        if (lastid == 0)
            $("#tblDatos tbody tr").remove();
        AsyncGetDataListar(urlData, callbackData);
    }
    
    function callbackData(data){
        datos = eval( "(" + data + ")" );
        i = 0;
        if (datos.length > 0){
            $.each(datos, function(i){
                $contentRow = "";
                $rowData = $('<tr></tr>').attr({ 
                    'id' : "row" + datos[i].tm_idusuario, 
                    'rel' : datos[i].tm_idusuario,
                    'class' : "modern-row"
                });
                $contentRow = '<td>' + datos[i].tm_login + '</td>';
                $contentRow += '<td class="hidden">' + datos[i].tm_nrodni + '</td>';
                $contentRow += '<td class="hidden">' + datos[i].tm_apellidopaterno + ' ' + datos[i].tm_apellidomaterno + ' ' + datos[i].tm_nombres + '</td>';
                $contentRow += '<td>' + datos[i].tm_email + '</td>';
                $rowData.append($contentRow);
                $('#tblDatos tbody').append($rowData);
                $('.grid-down').slideUp();
            });
            initEventRowsData();
        }
        $("#btnShowMore").html("...");
        $(".data-view").animate({ scrollTop: $('.data-view')[0].scrollHeight}, 1000);
        precargaExp(".divload", false);
    }
</script>

<div class="page-region">

    <h1>
        <a href="#" title="Regresar" class="back-button"><i class="icon-arrow-left-3 fg-darker smaller"></i></a>
        Usuarios
    </h1>
    <div class="page-region divload">
        <form id="form1" name="form1" method="get">
            <input type="hidden" id="pag" name="pag" value="<?php echo $pag; ?>" />
            <input type="hidden" id="subpag" name="subpag" value="<?php echo $subpag; ?>" />
            <input type="hidden" id="op" name="op" value="<?php echo $op; ?>" />
            <input type="hidden" id="firstLit" name="firstLit" value="<?php echo $firstLit; ?>" />
            <div class="main-filter">
                <table class="tabla-normal">
                    <tr>
                        <td>
                            <div class="input-control text" style="padding-bottom:0px; margin-bottom:0px;">
                                <input id="txtSearch" name="txtSearch" type="text" class="search" placeholder="Ingrese criterio de b&uacute;squeda..." value="<?php echo $txtSearch; ?>">
                                <button class="btn-clear" tabindex="-1" type="button"></button>
                            </div>
                        </td>
                        <td style="width:85px;">
                            <div class="toolbar" style="margin:0px !important; padding:0px !important;">
                                <button id="btnFilter" type="button" title="<?php $translate->__('M&aacute;s filtros'); ?>" class="default" style="margin-left:10px; margin-bottom:0px;"><i class="icon-filter"></i></button>
                                <button id="btnSearch" type="button" title="M&aacute;s filtros" class="default" style="margin-bottom:0px;"><i class="icon-search"></i></button>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="grid-down">
                <div class="pagination">
                    <ul>
                        <li<?php 
                            if ($firstLit == '')
                                $activeLetter = ' class="active"';
                            echo $activeLetter; 
                        ?>><a href="<?php echo $linksearch; ?>" rel="">*</a></li>
                        <?php
                        
                        foreach (range('A', 'Z') as $char) {
                            $activeLetter = '';
                            if ($char == $firstLit)
                                $activeLetter = ' class="active"';
                            echo '<li'.$activeLetter.'><a href="'.$linksearch.$char.'" rel="'.$char.'">'.$char.'</a></li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <div class="data-view">
                <table id="tblDatos" class="bordered hovered fg-color-white">
                    <thead>
                        <tr>
                            <th class="bg-color-darken fg-color-white"><?php $translate->__('Usuario'); ?></th>
                            <th class="bg-color-darken fg-color-white hidden">DNI</th>
                            <th class="bg-color-darken fg-color-white hidden"><?php $translate->__('Nombre completo'); ?></th>
                            <th class="bg-color-darken fg-color-white"><?php $translate->__('Email'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    $i = 0;
                    $countrows = count($row);
                    $checked = '';
                    while ($i < $countrows){
                    ?>
                    <tr id="row<?php echo $row[$i]['tm_idusuario']; ?>" rel="<?php echo $row[$i]['tm_idusuario']; ?>" class="modern-row">
                        <td><?php echo $row[$i]['tm_login']; ?></td>
                        <td class="hidden"><?php echo $row[$i]['tm_nrodni']; ?></td>
                        <td class="hidden"><?php echo $row[$i]['tm_apellidopaterno'].' '.$row[$i]['tm_apellidomaterno'].' '.$row[$i]['tm_nombres']; ?></td>
                        <td><?php echo $row[$i]['tm_email']; ?></td>
                    </tr>
                    <?php
                        ++$i;
                    }
                    ?>
                    </tbody>
                </table>
                <div class="clear"></div>
            </div>
            <div class="more">
                <button id="btnShowMore" type="button">...</button>
            </div>
            <div class="appbar">
                <button id="btnPermisos" type="button" class="metro_button float-right">
                    <span class="content">
                        <img src="images/permissions.png" alt="<?php $translate->__('Permisos'); ?>" />
                        <span class="text"><?php $translate->__('Permisos'); ?></span>
                    </span>
                </button>
                <button id="btnEliminar" type="button" class="metro_button oculto float-right">
                    <span class="content">
                        <img src="images/trash.png" alt="<?php $translate->__('Eliminar'); ?>" />
                        <span class="text"><?php $translate->__('Eliminar'); ?></span>
                    </span>
                </button>
                <button id="btnEditar" type="button" class="metro_button oculto float-right">
                    <span class="content">
                        <img src="images/edit.png" alt="<?php $translate->__('Editar'); ?>" />
                        <span class="text"><?php $translate->__('Editar'); ?></span>
                    </span>
                </button>
                <button id="btnNuevo" type="button" class="metro_button float-right">
                    <span class="content">
                        <img src="images/add.png" alt="<?php $translate->__('Nuevo'); ?>" />
                        <span class="text"><?php $translate->__('Nuevo'); ?></span>
                    </span>
                </button>
                <button id="btnLimpiarSeleccion" type="button" class="metro_button oculto float-right">
                    <span class="content">
                        <img src="images/icon_uncheck.png" alt="<?php $translate->__('Limpiar selecci&oacute;n'); ?>" />
                        <span class="text"><?php $translate->__('Limpiar selecci&oacute;n'); ?></span>
                    </span>
                </button>
                <div class="clear"></div>
            </div>
        </form>
        <div class="clear"></div>
    </div>
</div>

