<?php
include('bussiness/cargos.php');
include('bussiness/organigrama.php');

$IdEmpresa = 1;
$IdCentro = 1;
$Id = 0;
$Codigo = '';
$Nombre = '';
$IdCargo = 0;
$IdSubCategoria = 0;

$counterCategoria = 0;
$counterProducto = 0;
$counterValidItems = 0;

$parametros = array(
'criterio' => '',
'idcargo' => '0',
'lastid' => '1' );

$strListItems = '';
$strListDelete = '';
$strListValids = '';
$validItems = false;
$arrayValid = array();
$arrayDelete = array();

$objCargo = new clsCargo();
$objData = new clsOrganigrama();

$rpta = '0';

if ($_POST){
    if ($_POST['btnGuardar']){
        $Id = isset($_POST['hdIdPrimary']) ? $_POST['hdIdPrimary'] : '0';
        $Codigo = isset($_POST['txtCodigo']) ? $_POST['txtCodigo'] : '';
        $NroDNI = isset($_POST['txtNroDNI']) ? $_POST['txtNroDNI'] : '';
        $ApePaterno = isset($_POST['txtApePaterno']) ? $_POST['txtApePaterno'] : '';
        $ApeMaterno = isset($_POST['txtApeMaterno']) ? $_POST['txtApeMaterno'] : '';
        $Nombres = isset($_POST['txtNombres']) ? $_POST['txtNombres'] : '';
        $Foto = isset($_POST['hdFoto']) ? $_POST['hdFoto'] : 'no-set';
        $Email = isset($_POST['txtEmail']) ? $_POST['txtEmail'] : '';
        $IdCargo = isset($_POST['ddlCargoReg']) ? $_POST['ddlCargoReg'] : '0';

        $entityInsert = array(   'tm_idempresa' => $IdEmpresa, 
                            'tm_idcentro' => $IdCentro,
                            'tm_codigo' => $Codigo, 
                            'Abreviatura' => '',
                            'Activo' => 1,
                            'IdUsuarioReg' => $idusuario,
                            'FechaReg' => date("Y-m-d h:i:s")
                            );

        $entityUpdate = array(
            'tm_idpersonal' => $Id, 
            'tm_nrodni' => $NroDNI,
            'tm_apellidopaterno' => $ApePaterno,
            'tm_apellidomaterno' => $ApeMaterno,
            'tm_nombres' => $Nombres, 
            'tm_foto' => $Foto, 
            'tm_email' => $Email,
            'tp_idcargo' => $IdCargo, 
            'IdUsuarioAct' => $idusuario,
            'FechaAct' => date("Y-m-d h:i:s") );

        if ($Id == '0')
            $entidad = array_merge($entityInsert, $entityUpdate);
        else
            $entidad = $entityUpdate;

        $rpta = $objData->Registrar($entidad);
        $jsondata = array("rpta" => $rpta);
    }
    elseif ($_POST['btnEliminar']) {
        $chkItem = $_POST['chkItem'];
        if (isset($chkItem))
            if (is_array($chkItem)) {
                $countCheckItems = count($chkItem);
                $strListItems = implode(',', $chkItem);
                /*$rsValidItems = $objData->Listar('VALID-VENTAS', $strListItems);
                $countValidItems = count($rsValidItems);
                
                if ($countValidItems > 0) {
                    for ($counterValidItems=0; $counterValidItems < $countValidItems; ++$counterValidItems)
                        array_push($arrayValid, $rsValidItems[$counterValidItems]['tm_idpersonal']);

                    $arrayDelete = array_diff($chkItem, $arrayValid);
                    if (!empty($arrayDelete))
                        $strListItems = implode(',', $arrayDelete);
                    else
                        $strListItems = '';
                }

                if ($countCheckItems > $countValidItems)
                    $rpta = $objData->MultiDelete($strListItems);*/
                $rpta = $objData->MultiDelete($strListItems);
            }
        if (!empty($arrayValid))
            $strListValids = implode(',', $arrayValid);
        $jsondata = array('rpta' => $rpta, 'items_valid' => $strListValids);
    }
    echo json_encode($jsondata);
    exit(0);
}

$rowCargo = $objCargo->Listar('L');
$countrowCargo = count($rowCargo);
?>
<form id="form1" name="form1" method="post">
    <input type="hidden" id="fnPost" name="fnPost" value="fnPost" />
    <input type="hidden" id="hdPageActual" name="hdPageActual" value="1" />
    <input type="hidden" id="hdPage" name="hdPage" value="1" />
    <input type="hidden" id="hdIdPrimary" name="hdIdPrimary" value="0">
    <input type="hidden" id="hdFoto" name="hdFoto" value="no-set">
    <div class="page-region">
        <div id="pnlListado" class="inner-page with-title-window with-panel-search">
            <h1 class="title-window">
                Proveedores
            </h1>
            <div class="panel-search">
                <table class="tabla-normal">
                    <tr>
                        <td>
                            <div class="input-control text" data-role="input-control">
                                <input id="txtSearch" name="txtSearch" type="text" placeholder="<?php $translate->__('Ingrese criterios de b&uacute;squeda'); ?>">
                                <button id="btnSearch" name="btnSearch" type="button"  tabindex="-1" title="Buscar" class="btn-search"></button>
                            </div>
                        </td>
                        <td style="width:45px;">
                            <button id="btnFilter" type="button" title="<?php $translate->__('M&aacute;s filtros'); ?>" style="margin-left:10px; margin-bottom:0px;"><i class="icon-filter"></i></button>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="grid filtro">
                <div class="row">
                    <div class="span12" style="margin-right:10px;">
                        <label for="ddlCargo"><?php $translate->__('Cargos'); ?></label>
                        <div class="input-control select" data-role="input-control">
                            <select id="ddlCargo" name="ddlCargo">
                                <option value="0"><?php $translate->__('TODOS'); ?></option>
                                <?php 
                                if ($countrowCargo > 0){
                                    for ($counterCategoria=0; $counterCategoria < $countrowCargo; $counterCategoria++) {
                                ?>
                                <option value="<?php echo $rowCargo[$counterCategoria]['tp_idcargo']; ?>"><?php echo $rowCargo[$counterCategoria]['tp_nombre']; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="divload">
                <div id="gvDatos">
                    <div class="items-area listview gridview">
                    </div>
                </div>
            </div>
        </div>
        <div id="pnlForm" class="inner-page" style="display:none;">
            <h1 class="title-window">
                <a id="btnBackList" href="#" title="Regresar a listado" class="back-button"><i class="icon-arrow-left-3 fg-darker smaller"></i></a>
                <?php $translate->__('Registro de personal'); ?>
            </h1>
            <div class="divContent">
                <div class="form-register-data container">
                    <div class="grid">
                        <div class="row">
                            <div class="span6">
                                <div id="area">
                                    <input id="userFoto" type="file" class="droparea spot" name="xfile" data-post="upload.php" data-width="235" data-height="235" data-crop="true"/>
                                    <div class="clear"></div>
                                </div>
                            </div>
                            <div class="span6">
                                <div class="grid">
                                    <div class="row">
                                        <label for="txtCodigo"><?php $translate->__('C&oacute;digo'); ?></label>
                                        <div class="input-control text" data-role="input-control">
                                            <input id="txtCodigo" name="txtCodigo" type="text" placeholder="<?php $translate->__('Ejemplo 001'); ?>" />
                                            <button class="btn-clear" tabindex="-1" type="button"></button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="txtNroDNI">DNI</label>
                                        <div class="input-control text" data-role="input-control">
                                            <input id="txtNroDNI" name="txtNroDNI" type="text" placeholder="<?php $translate->__('Ejemplo: 45035046'); ?>">
                                            <button class="btn-clear" tabindex="-1" type="button"></button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="txtApePaterno"><?php $translate->__('Apellido paterno'); ?></label>
                                        <div class="input-control text" data-role="input-control">
                                            <input id="txtApePaterno" name="txtApePaterno" type="text" placeholder="<?php $translate->__('Ejemplo: Gonzales'); ?>">
                                            <button class="btn-clear" tabindex="-1" type="button"></button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="txtApeMaterno"><?php $translate->__('Apellido materno'); ?></label>
                                        <div class="input-control text" data-role="input-control">
                                            <input id="txtApeMaterno" name="txtApeMaterno" type="text" placeholder="<?php $translate->__('Ejemplo: Gonzales'); ?>">
                                            <button class="btn-clear" tabindex="-1" type="button"></button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="txtNombres"><?php $translate->__('Nombres'); ?></label>
                                        <div class="input-control text" data-role="input-control">
                                            <input id="txtNombres" name="txtNombres" type="text" placeholder="<?php $translate->__('Ejemplo: Luis'); ?>">
                                            <button class="btn-clear" tabindex="-1" type="button"></button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="txtEmail"><?php $translate->__('Email'); ?></label>
                                        <div class="input-control text" data-role="input-control">
                                            <input id="txtEmail" name="txtEmail" type="text" placeholder="<?php $translate->__('Ejemplo: tunombre@tudominio.com'); ?>">
                                            <button class="btn-clear" tabindex="-1" type="button"></button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="ddlCargoReg"><?php $translate->__('Categor&iacute;a'); ?></label>
                                        <div class="input-control select" data-role="input-control">
                                            <select id="ddlCargoReg" name="ddlCargoReg">
                                            <?php 
                                            if ($countrowCargo > 0){
                                                for ($counterCategoria=0; $counterCategoria < $countrowCargo; $counterCategoria++) {
                                            ?>
                                            <option value="<?php echo $rowCargo[$counterCategoria]['tp_idcargo']; ?>"><?php echo $rowCargo[$counterCategoria]['tp_nombre']; ?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    <div class="appbar">
        <button id="btnEliminar" name="btnEliminar" type="button" class="cancel metro_button oculto float-right">
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
        <button id="btnReporte" type="button" class="metro_button float-right">
            <span class="content">
                <img src="images/Send-message.png" alt="<?php $translate->__('Reporte'); ?>" />
                <span class="text"><?php $translate->__('Reporte'); ?></span>
            </span>
        </button>
        <button id="btnCancelar" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/cancel.png" alt="<?php $translate->__('Cancelar'); ?>" />
                <span class="text"><?php $translate->__('Cancelar'); ?></span>
            </span>
        </button>
        <button id="btnGuardar" name="btnGuardar" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/save.png" alt="<?php $translate->__('Guardar'); ?>" />
                <span class="text"><?php $translate->__('Guardar'); ?></span>
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
    <div id="modalItemsError" class="modal-example-content" style="display:none;">
        <div class="modal-example-header">
            <a class="close" href="#" onclick="$.fn.custombox('close');">&times;</a>
            <h4><?php $translate->__('Informe de errores'); ?></h4>
        </div>
        <div class="modal-example-body">
            <div id="errorList" class="error-list">
            </div>
        </div>
    </div>
</form>
<?php
include('common/libraries-js.php');
?>
<script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.13.0/jquery.validate.min.js"></script>
<script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.13.0/additional-methods.min.js"></script>
<script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.13.0/localization/messages_es.js "></script>
<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
<script src="scripts/droparea.js"></script>
<script>
    var TipoBusqueda = '00';

    $(function () {
        $('#ddlCargo').focus().on('change', function () {
            idreferencia = $(this).val();
            LoadPersonal(TipoBusqueda, $(this).val(), '0', '1');
        });

        $("#form1").validate({
            lang: 'es',
            showErrors: showErrorsInValidate,
            submitHandler: EnvioAdminDatos
        });

        $('.droparea').droparea({
            'instructions': '<?php $translate->__('Arrastre una imagen o haga click aqu&iacute;'); ?>',
            'init' : function(result){
                clearImagenForm();
            },
            'start' : function(area){
                area.find('.error').remove(); 
            },
            'error' : function(result, input, area){
                $('<div class="error">').html(result.error).prependTo(area); 
                return 0;
            },
            'complete' : function(result, file, input, area){
                if((/image/i).test(file.type)){
                    area.find('img').remove();
                    area.append($('<img>',{'src': result.filename + '?' + Math.random() }));
                    $('#hdFoto').val(result.filename);
                } 
            }
        });

        addValidFormRegister();
    });

    function clearImagenForm () {
        $("#area").find('img').remove();
        $('#area .instructions').html('<?php $translate->__('Arrastre una imagen o haga click aqu&iacute;'); ?>');
        $("#area > .spot").append($('<img>',{'src': 'images/product-nosetimg.png'}));
    }

    function addValidFormRegister () {
        $('#txtCodigo').rules('add', {
            required: true,
            maxlength: 6
        });

        $('#txtNroDNI').rules('add', {
            required: true,
            maxlength: 20
        });

        $('#txtApePaterno').rules('add', {
            required: true,
            maxlength: 150
        });

        $('#txtApeMaterno').rules('add', {
            required: true,
            maxlength: 150
        });

        $('#txtNombres').rules('add', {
            required: true,
            maxlength: 150
        });

        $('#txtEmail').rules('add', {
            email: true,
            maxlength: 100
        });
    }

    function removeValidFormRegister () {
        $('#txtCodigo').rules('remove');
        $('#txtNroDNI').rules('remove');
        $('#txtApePaterno').rules('remove');
        $('#txtApeMaterno').rules('remove');
        $('#txtNombres').rules('remove');
        $('#txtEmail').rules('remove');
    }

    function GetDetails (data) {
        var datos = eval( "(" + data + ")" );
        var foto = datos[0].tm_foto;

        $('#hdIdPrimary').val(datos[0].tm_idpersonal);
        $('#txtCodigo').val(datos[0].tm_codigo);
        $('#txtNroDNI').val(datos[0].tm_nrodni);
        $('#txtApePaterno').val(datos[0].tm_apellidopaterno);
        $('#txtApeMaterno').val(datos[0].tm_apellidomaterno);
        $('#txtNombres').val(datos[0].tm_nombres);
        $('#txtEmail').val(datos[0].tm_email);
        $('#ddlCargoReg').val(datos[0].tp_idcargo);
        
        $('#hdFoto').val(foto);
        if (foto != 'no-set')
            $('#area .instructions').html('');
        $("#area > .spot > img").remove();
        $("#area > .spot").append($('<img>',{'src': (foto == 'no-set' ? 'images/product-nosetimg.png' : foto)}));
    }

    function BuscarDatos (pagina) {
        LoadPersonal(TipoBusqueda, $('#ddlCargo').val(), $('#txtSearch').val(), pagina);
    }
</script>