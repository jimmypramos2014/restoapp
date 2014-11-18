<?php
include('bussiness/categoria.php');
include('bussiness/productos.php');
include('bussiness/receta.php');
include('bussiness/insumos.php');
include('bussiness/unidadmedida.php');
$IdEmpresa = 1;
$IdCentro = 1;
$Id = 0;
$IdResultProducto = '0';
$Codigo = '';
$Nombre = '';
$IdCategoria = 0;
$IdSubCategoria = 0;
$counterCategoria = 0;
$counterUM = 0;
$counterProducto = 0;
$counterValidItems = 0;
$parametros = array(
'criterio' => '',
'idcategoria' => '0',
'idsubcategoria' => '0',
'lastid' => '1' );
$strListItems = '';
$strListDelete = '';
$strListValids = '';
$strQueryDetReceta = '';
$strItemsDetalle = '';
$validItems = false;
$arrayValid = array();
$arrayDelete = array();
$objCategoria = new clsCategoria();
$objUM = new clsUnidadMedida();
$objReceta = new clsReceta();
$objInsumo = new clsInsumo();
$objData = new clsProducto();
$rpta = '0';
if ($_POST){
    if ($_POST['btnGuardar']){
        $TipoMenuDia = isset($_POST['TipoMenuDia']) ? $_POST['TipoMenuDia'] : '00';
        $Id = isset($_POST['hdIdPrimary']) ? $_POST['hdIdPrimary'] : '0';
        $Codigo = isset($_POST['txtCodigo']) ? $_POST['txtCodigo'] : '';
        $Nombre = isset($_POST['txtNombre']) ? $_POST['txtNombre'] : '';
        $Descripcion = isset($_POST['txtDescripcion']) ? $_POST['txtDescripcion'] : '';
        $Foto = isset($_POST['hdFoto']) ? $_POST['hdFoto'] : 'no-set';
        $IdCategoria = isset($_POST['ddlCategoriaReg']) ? $_POST['ddlCategoriaReg'] : '0';
        $IdSubCategoria = isset($_POST['ddlSubCategoriaReg']) ? $_POST['ddlSubCategoriaReg'] : '0';
        $entityInsert = array(   'tm_idempresa' => $IdEmpresa, 
                            'tm_idcentro' => $IdCentro,
                            'tm_codigo' => $Codigo, 
                            'ta_estadoprod' => '00',
                            'Abreviatura' => '',
                            'Activo' => 1,
                            'IdUsuarioReg' => $idusuario,
                            'FechaReg' => date("Y-m-d h:i:s")
                            );
        $entityUpdate = array(   'tm_idproducto' => $Id, 
                            'tm_nombre' => $Nombre, 
                            'td_contenido' => $Descripcion,
                            'tm_foto' => $Foto, 
                            'tm_idcategoria' => $IdCategoria, 
                            'tm_idsubcategoria' => $IdSubCategoria, 
                            'IdUsuarioAct' => $idusuario,
                            'FechaAct' => date("Y-m-d h:i:s") );
        if ($Id == '0')
            $entidad = array_merge($entityInsert, $entityUpdate);
        else
            $entidad = $entityUpdate;
        $rpta = $objData->Registrar($entidad);
        if ($rpta > 0){
            if ($Id == '0')
                $IdResultProducto = $rpta;
            else
                $IdResultProducto = $Id;
            $detalleReceta = json_decode(stripslashes($_POST['detalleReceta']));
            $countDetalle = count($detalleReceta);
            $objReceta->DeletePrevDetReceta($IdResultProducto, $TipoMenuDia);
            
            if ($countDetalle > 0){
                $strQueryDetReceta = 'INSERT INTO td_receta (';
                $strQueryDetReceta .= 'tm_idempresa, tm_idcentro, tm_idproducto, tm_idinsumo_orig, ta_tipomenudia, ta_tipoinsumo, td_cantidad, ';
                $strQueryDetReceta .= 'Activo, IdUsuarioReg, FechaReg, IdUsuarioAct, FechaAct) VALUES ';
                foreach($detalleReceta as $item){
                    $entidadRecetaInsert = array(
                        'tm_idempresa' => $IdEmpresa,
                        'tm_idcentro' => $IdCentro,
                        'tm_nombre' => $item->nombreInsumo,
                        'tm_idunidadmedida' => $item->idunidadmedida,
                        'ta_estadoinsumo' => '00',
                        'Activo' => 1,
                        'IdUsuarioReg' => $idusuario,
                        'FechaReg' => date("Y-m-d h:i:s"),
                        'IdUsuarioAct' => $idusuario,
                        'FechaAct' => date("Y-m-d h:i:s")
                    );
                    $rptaInsumo = $objInsumo->Registrar($entidadRecetaInsert);
                    if ($rptaInsumo > 0){
                        if (strlen($strItemsDetalle) > 0)
                            $strItemsDetalle .= ',';
                        $strItemsDetalle .= '('.$IdEmpresa.', '.$IdCentro.', '.$IdResultProducto.', '.$rptaInsumo.', \''.$item->tipomenudia.'\', \'00\', '.$item->cantidad.', ';
                        $strItemsDetalle .= '1, '.$idusuario.', \''.date("Y-m-d h:i:s").'\', '.$idusuario.', \''.date("Y-m-d h:i:s").'\')';
                    }
                }
                
                if (strlen($strItemsDetalle) > 0) {
                    $strQueryDetReceta .= $strItemsDetalle;
                    $objReceta->RegistrarDetalle($strQueryDetReceta);
                }
            }
        }
        $jsondata = array("rpta" => $rpta);
    }
    elseif ($_POST['btnEliminar']) {
        $chkItem = $_POST['chkItem'];
        if (isset($chkItem))
            if (is_array($chkItem)) {
                $countCheckItems = count($chkItem);
                $strListItems = implode(',', $chkItem);
                $rsValidItems = $objData->Listar('VALID-VENTAS', $strListItems);
                $countValidItems = count($rsValidItems);
                
                if ($countValidItems > 0) {
                    for ($counterValidItems=0; $counterValidItems < $countValidItems; ++$counterValidItems)
                        array_push($arrayValid, $rsValidItems[$counterValidItems]['tm_idproducto']);
                    $arrayDelete = array_diff($chkItem, $arrayValid);
                    if (!empty($arrayDelete))
                        $strListItems = implode(',', $arrayDelete);
                    else
                        $strListItems = '';
                }
                if ($countCheckItems > $countValidItems)
                    $rpta = $objData->MultiDelete($strListItems);
            }
        if (!empty($arrayValid))
            $strListValids = implode(',', $arrayValid);
        $jsondata = array('rpta' => $rpta, 'items_valid' => $strListValids);
    }
    elseif ($_POST['btnUploadExcel']){
    }
    echo json_encode($jsondata);
    exit(0);
}
$rowCategoria = $objCategoria->Listar('M', $IdEmpresa, $IdCentro, '0');
$countRowCategoria = count($rowCategoria);
$rowUM = $objUM->Listar('L', '');
$countRowUM = count($rowUM);
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
                <?php $translate->__('Art&iacute;culos'); ?>
            </h1>
            <div class="panel-search">
                <table class="tabla-normal">
                    <tr>
                        <td>
                            <div class="input-control text" data-role="input-control">
                                <input id="txtSearch" name="txtSearch" type="text" placeholder="<?php $translate->__('Ingrese criterios de b&uacute;squeda'); ?>">
                                <button id="btnSearch" name="btnSearch" type="button"  tabindex="-1" title="<?php $translate->__('Buscar'); ?>" class="btn-search"></button>
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
                    <div class="span6" style="margin-right:10px;">
                        <label for="ddlCategoria"><?php $translate->__('Categor&iacute;as'); ?></label>
                        <div class="input-control select" data-role="input-control">
                            <select id="ddlCategoria" name="ddlCategoria">
                                <option value="0"><?php $translate->__('TODOS'); ?></option>
                                <?php 
                                if ($countRowCategoria > 0){
                                    for ($counterCategoria=0; $counterCategoria < $countRowCategoria; $counterCategoria++) {
                                ?>
                                <option value="<?php echo $rowCategoria[$counterCategoria]['tm_idcategoria']; ?>"><?php echo $rowCategoria[$counterCategoria]['tm_nombre']; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="span6" style="margin-right:10px;">
                        <label for="ddlSubCategoria"><?php $translate->__('Sub-Categor&iacute;as'); ?></label>
                        <div class="input-control select" data-role="input-control">
                            <select id="ddlSubCategoria" name="ddlSubCategoria" disabled="">
                                <option value="0"><?php $translate->__('No hay sub-categor&iacute;as disponibles'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="divload">
                <div id="gvDatos">
                    <div class="tile-area gridview"></div>
                </div>
            </div>
        </div>
        <div id="pnlForm" class="inner-page" style="display:none;">
            <h1 class="title-window">
                <a id="btnBackList" href="#" title="<?php $translate->__('Regresar a listado'); ?>" class="back-button"><i class="icon-arrow-left-3 fg-darker smaller"></i></a>
                <?php $translate->__('Registro'); ?>
            </h1>
            <div class="divContent">
                <div class="form-register-data">
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
                                        <label for="txtNombre"><?php $translate->__('Nombre de art&iacute;culo'); ?></label>
                                        <div class="input-control text" data-role="input-control">
                                            <input id="txtNombre" name="txtNombre" type="text" placeholder="<?php $translate->__('Ejemplo: Arroz'); ?>">
                                            <button class="btn-clear" tabindex="-1" type="button"></button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="txtDescripcion"><?php $translate->__('Descripci&oacute;n'); ?></label>
                                        <div class="input-control textarea">
                                            <textarea id="txtDescripcion" name="txtDescripcion"></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="ddlCategoriaReg"><?php $translate->__('Categor&iacute;a'); ?></label>
                                        <div class="input-control select" data-role="input-control">
                                            <select id="ddlCategoriaReg" name="ddlCategoriaReg">
                                            <?php 
                                            if ($countRowCategoria > 0){
                                                for ($counterCategoria=0; $counterCategoria < $countRowCategoria; $counterCategoria++) {
                                            ?>
                                            <option value="<?php echo $rowCategoria[$counterCategoria]['tm_idcategoria']; ?>"><?php echo $rowCategoria[$counterCategoria]['tm_nombre']; ?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="ddlSubCategoriaReg"><?php $translate->__('Sub-Categor&iacute;a'); ?></label>
                                        <div class="input-control select" data-role="input-control">
                                            <select id="ddlSubCategoriaReg" name="ddlSubCategoriaReg">
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
        <div id="pnlReceta" class="inner-page" style="display:none;">
            <h1 class="title-window">
                <a id="btnBackRegistro" href="#" title="Regresar a" class="back-button"><i class="icon-arrow-left-3 fg-darker smaller"></i></a>
                <ul id="mnuNavigate" class="dropdown-menu" data-role="dropdown">
                    <li><a href="#" rel="regproducto"><?php $translate->__('Registro de productos'); ?></a></li>
                    <li><a href="#" rel="productos"><?php $translate->__('Listado de productos'); ?></a></li>
                </ul>
                <?php $translate->__('Recetas'); ?>
            </h1>
            <div class="divContent">
                <div id="pnlRecetario">
                    <div class="sectionHeader">
                        <button id="btnOptionMenu" name="btnOptionMenu" class="large success" type="button" data-tipomenu="01"><?php $translate->__('Men&uacute;'); ?></button>
                        <button id="btnOptionCarta" name="btnOptionCarta" class="large" type="button" data-tipomenu="00"><?php $translate->__('Carta'); ?></button>
                        <h2 class="title-articulo"></h2>
                    </div>
                    <div class="sectionContent">
                        <div class="moduloTwoPanel">
                            <div class="colTwoPanel1">
                                <div id="tableReceta" class="itables">
                                    <div class="ihead">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th><?php $translate->__('Ingredientes'); ?></th>
                                                    <th>UM</th>
                                                    <th><?php $translate->__('Cantidad'); ?></th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div class="ibody">
                                        <div class="ibody-content">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="4">
                                                            <h3><?php $translate->__('No se encontraron registros'); ?></h3>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="colTwoPanel2">
                                <div id="pnlFormReceta">
                                    <div class="formReceta">
                                        <div class="grid">
                                            <div class="row">
                                                <label for="txtNombreIngrediente"><?php $translate->__('Nombre de ingrediente'); ?></label>
                                                <div class="input-control text" data-role="input-control">
                                                    <input id="txtNombreIngrediente" name="txtNombreIngrediente" type="text" placeholder="<?php $translate->__('Ejemplo: ingrediente maestro'); ?>" />
                                                    <button class="btn-clear" tabindex="-1" type="button"></button>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label for="ddlUnidadMedida"><?php $translate->__('Unidad de medida'); ?></label>
                                                <div class="input-control select" data-role="input-control">
                                                    <select id="ddlUnidadMedida" name="ddlUnidadMedida">
                                                    <?php 
                                                    if ($countRowUM > 0){
                                                        for ($counterUM=0; $counterUM < $countRowUM; $counterUM++) {
                                                    ?>
                                                    <option data-simbolo="<?php echo $rowUM[$counterUM]['tm_abreviatura']; ?>" value="<?php echo $rowUM[$counterUM]['tm_idunidadmedida']; ?>"><?php echo $rowUM[$counterUM]['tm_nombre'].' ('.$rowUM[$counterUM]['tm_abreviatura'].')'; ?></option>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label for="txtCantidad"><?php $translate->__('Cantidad'); ?></label>
                                                <div class="input-control text" data-role="input-control">
                                                    <input id="txtCantidad" name="txtCantidad" type="text" class="text-right only-numbers" placeholder="0.000" />
                                                    <button class="btn-clear" tabindex="-1" type="button"></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="filaButtons">
                                        <div class="columna1">
                                            <button id="btnAddReceta" class="large default" type="button">
                                                <?php $translate->__('Agregar ingrediente'); ?>
                                            </button>
                                        </div>
                                        <div class="columna2">
                                            <button id="btnCancelReceta" class="large danger" type="button">
                                                <?php $translate->__('Cancelar'); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
        <button id="btnUploadExcel" type="button" class="metro_button float-right">
            <span class="content">
                <img src="images/cloud-upload.png" alt="<?php $translate->__('Importar datos'); ?>" />
                <span class="text"><?php $translate->__('Importar datos'); ?></span>
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
                <span class="text"><?php $translate->__('Guardar cambios'); ?></span>
            </span>
        </button>
        <button id="btnEditarIngrediente" type="button" class="metro_button oculto show-if-phone float-left">
            <span class="content">
                <img src="images/edit.png" alt="<?php $translate->__('Editar'); ?>" />
                <span class="text"><?php $translate->__('Editar'); ?></span>
            </span>
        </button>
        <button id="btnAgregarIngrediente" type="button" class="metro_button oculto show-if-phone float-left">
            <span class="content">
                <img src="images/add.png" alt="<?php $translate->__('Agregar insumos'); ?>" />
                <span class="text"><?php $translate->__('Nuevo ingrediente'); ?></span>
            </span>
        </button>
        <button id="btnRecetas" type="button" class="metro_button oculto float-left">
            <span class="content">
                <img src="images/recipe.png" alt="<?php $translate->__('Recetas'); ?>" />
                <span class="text"><?php $translate->__('Recetas'); ?></span>
            </span>
        </button>
        <button id="btnBackListRecetas" type="button" class="metro_button oculto show-if-phone float-left">
            <span class="content">
                <img src="images/back.png" alt="<?php $translate->__('Regresar a ingredientes'); ?>" />
                <span class="text"><?php $translate->__('Regresar a ingredientes'); ?></span>
            </span>
        </button>
        <button id="btnNuevo" type="button" class="metro_button float-right">
            <span class="content">
                <img src="images/add.png" alt="<?php $translate->__('Nuevo'); ?>" />
                <span class="text"><?php $translate->__('Nuevo'); ?></span>
            </span>
        </button>
        <button id="btnQuitarIngrediente" name="btnQuitarIngrediente" type="button" class="cancel metro_button oculto float-left">
            <span class="content">
                <img src="images/trash.png" alt="<?php $translate->__('Quitar ingrediente'); ?>" />
                <span class="text"><?php $translate->__('Quitar ingrediente'); ?></span>
            </span>
        </button>
        <button id="btnLimpiarSeleccion" type="button" class="metro_button oculto float-left">
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
    <div id="modalUpload" class="modal-example-content" style="display:none;">
        <div class="modal-example-header">
            <a class="close" href="#" onclick="$.fn.custombox('close');">&times;</a>
            <h4><?php $translate->__('Importar datos'); ?></h4>
        </div>
        <div class="modal-example-body">
            <div id="fileuploader">Upload</div>
        </div>
    </div>
</form>
<?php
include('common/libraries-js.php');
include('common/validate-js.php');
include('common/bootstrap-js.php');
?>
<script src="scripts/droparea.js"></script>
<script src="scripts/jquery.uploadfile.min.js"></script>
<script>
    var TipoBusqueda = '00';
    $(function () {
        $('#ddlCategoria').focus().on('change', function () {
            idreferencia = $(this).val();
            habilitarControl('#ddlSubCategoria', false);
            $('#ddlSubCategoria').html('<option value="0"><?php $translate->__('TODOS'); ?></option>');
            LoadSubCategorias(idreferencia, '#ddlSubCategoria');
            LoadProductos(TipoBusqueda, $(this).val(), '0', '', '1');
        });
        $('#ddlSubCategoriaReg').html('');
        LoadSubCategorias($('#ddlCategoriaReg').val(), '#ddlSubCategoriaReg');
        $('#ddlCategoriaReg').on('change', function () {
            idreferencia = $(this).val();
            $('#ddlSubCategoriaReg').html('');
            LoadSubCategorias(idreferencia, '#ddlSubCategoriaReg');
        });
        $('#ddlSubCategoria').on('change', function () {
            LoadProductos(TipoBusqueda, $('#ddlCategoria').val(), $(this).val(), '', '1');
        });
        $('#btnCancelar, #btnBackList').on('click', function () {
            removeValidFormReceta();
            $('.divContent').animate({ scrollTop: 0 }, 0);
            resetForm('form1');
            limpiarSeleccionados();
            clearImagenForm();
            BackToList();
            BuscarDatos('1');
            return false;
        });
        $('#btnNuevo, #btnEditar').on('click', function () {
            resetForm('form1');
            GoToEdit();
            return false;
        });
        $('#btnRecetas').on('click', function () {
            GoToReceta();
            return false;
        });
        $('#btnAgregarIngrediente, #btnEditarIngrediente').on('click', function (event) {
            GoToAddReceta();
            return false;
        });
        $('#btnBackListRecetas, #btnCancelReceta').on('click', function (event) {
            BackToListRecetas();
            clearFormIngrediente();
            $('#tableReceta tbody tr.selected').removeClass('selected');
            $('#btnAddReceta').html('Agregar ingrediente').removeClass('mode-edit');
            if (event.target.id == 'btnCancelReceta')
                $('#btnLimpiarSeleccion, #btnEditarIngrediente, #btnQuitarIngrediente').addClass('oculto');
            return false;
        });
        $('#btnBackRegistro').on('click', function () {
            $('#mnuNavigate').fadeIn(500);
            return false;
        });
        $('#btnAddReceta').on('click', function () {
            if ($('#form1').valid())
                ModificarIngrediente();
            return false;
        })
        $('#mnuNavigate li a').on('click', function () {
            var aLink = $(this);
            var típoLink = aLink.attr('rel');
            $('.divContent').removeClass('no-overflow').animate({ scrollTop: 0 }, 500);
            $('#btnBackListRecetas, #btnAgregarIngrediente').addClass('oculto');
            if (típoLink == 'productos'){
                removeValidFormReceta();
                removeValidFormProducto();
                $('#btnRecetas, #btnGuardar, #btnCancelar').addClass('oculto');
                $('#btnNuevo, #btnUploadExcel').removeClass('oculto');
                limpiarSeleccionados();
            }
            else {
                removeValidFormReceta();
                addValidFormProducto();
                $('#btnRecetas').removeClass('oculto');
            }
            
            $('#pnlReceta').fadeOut(500, function () {
                 if (típoLink == 'productos'){
                    $('#pnlListado').fadeIn(500, function () {
                        $('#txtSearch').focus();
                    });
                }
                else {
                    $('#pnlForm').fadeIn(500, function () {
                        $('#txtNombre').focus();
                    });
                 }
            });
        });
        $('#pnlRecetario .sectionHeader button').on('click', function () {
            $(this).siblings('.success').removeClass('success');
            $(this).addClass('success');
            $('#btnAgregarIngrediente').removeClass('oculto');
            $('#btnBackListRecetas, #btnEditarIngrediente, #btnQuitarIngrediente, #btnLimpiarSeleccion').addClass('oculto');
            $('#btnAddReceta').html('Agregar ingrediente').removeClass('mode-edit');
            ListarReceta();
            return false;
        });
        
        $('#gvDatos').on('click', function () {
            if ($('.filtro').length > 0){
                $('#btnFilter').removeClass('active');
                $('.filtro').slideUp();
            }
        });
        BuscarDatos('1');
        $('#txtSearch').on('keydown', function(event) {
            if (event.keyCode == $.ui.keyCode.ENTER) {
                BuscarDatos('1');
                return false;
            }
        }).on('keypress', function(event) {
            if (event.keyCode == $.ui.keyCode.ENTER)
                return false;
        });
        $('#btnSearch').on('click', function () {
            BuscarDatos('1');
            return false;
        });
        if ($('#btnFilter').length > 0){
            $('#btnFilter').on('click', function(){
                if (!$(this).hasClass('active')){
                    $(this).addClass('active');
                    $('.filtro').slideDown();
                    if ($('#ddlCategoria').length > 0)
                        $('#ddlCategoria').focus();
                }
                else {
                    $(this).removeClass('active');
                    $('.filtro').slideUp();
                    $('#txtSearch').focus();                
                }
                return false;
            });
        }
        
        $('#btnGuardar').on('click', function (evt) {
            GuardarDatos();
            return false;
        });
        $('#btnEliminar').on('click', function () {
            EliminarDatos();
            return false;
        });
        $("#btnLimpiarSeleccion").on('click', function(){
            if ($('#pnlListado').is(':visible'))
                clearSelection();
            else if ($('#pnlReceta').is(':visible')){
                clearFormIngrediente();
                $('#tableReceta tbody tr.selected').removeClass('selected');
                $('#btnLimpiarSeleccion, #btnEditarIngrediente, #btnQuitarIngrediente').addClass('oculto');
                $('#btnAddReceta').html('Agregar ingrediente').removeClass('mode-edit');
                $('#btnAgregarIngrediente').removeClass('oculto');
            }
            return false;
        });
        $('#btnQuitarIngrediente').on('click', function(){
            QuitarIngrediente();
            return false;
        });
        $('#tableReceta tbody').on('click', 'tr', function () {
            clearFormIngrediente();
            $('#btnAddReceta').html('<?php $translate->__('Agregar ingrediente'); ?>').removeClass('mode-edit');
            if ($(this).hasClass('selected')){
                $(this).removeClass('selected');
                if ($(this).siblings('.selected').length == 0){
                    $('#btnAgregarIngrediente').removeClass('oculto');
                    $('#btnQuitarIngrediente, #btnEditarIngrediente, #btnLimpiarSeleccion').addClass('oculto');
                }
                else {
                    if ($(this).siblings('.selected').length == 1){
                        $('#btnEditarIngrediente').removeClass('oculto');
                        GetInfoIngrediente();
                    }
                    else
                        $('#btnEditarIngrediente').addClass('oculto');
                }
            }
            else{
                $(this).addClass('selected');
                if ($(this).siblings('.selected').length == 0){
                    $('#btnAgregarIngrediente').addClass('oculto');
                    $('#btnQuitarIngrediente, #btnEditarIngrediente, #btnLimpiarSeleccion').removeClass('oculto');
                    GetInfoIngrediente();
                }
                else
                    $('#btnEditarIngrediente').addClass('oculto');
            }
        });
        $('#txtNombreIngrediente').on({
            mouseup: function () {
                return false;
            },
            focus: function () {
                $(this).select();
                return false;
            },
            click: function () {
                $(this).select();
                return false;
            },
            keydown: function (event) {
                if (event.keyCode == $.ui.keyCode.ENTER){
                    $('#ddlUnidadMedida').focus();
                    return false;
                }
            },
            keyup: function (event) {
                if (event.keyCode == $.ui.keyCode.ENTER)
                    return false;
            }
        });
        $('#ddlUnidadMedida').on({
            keydown: function (event) {
                if (event.keyCode == $.ui.keyCode.ENTER){
                    $('#txtCantidad').focus();
                    return false;
                }
            },
            keyup: function (event) {
                if (event.keyCode == $.ui.keyCode.ENTER)
                    return false;
            }
        });
        $('#txtCantidad').on({
            mouseup: function () {
                return false;
            },
            focus: function () {
                $(this).select();
                return false;
            },
            click: function () {
                $(this).select();
                return false;
            },
            blur: function () {
                if ($(this).val().trim().length == 0)
                    $(this).val('0.000');
                else
                    $(this).val(Number($(this).val()).toFixed(3));
            },
            keydown: function (event) {
                if (event.keyCode == $.ui.keyCode.ENTER){
                    $('#btnAddReceta').focus();
                    return false;
                }
            },
            keyup: function (event) {
                if (event.keyCode == $.ui.keyCode.ENTER)
                    return false;
            }
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
        $('#fileuploader').uploadFile({
            allowedTypes:"xls,xlsx",
            fileName:'myfile',
            url: 'upload-import.php',
            dragDrop:true,
            returnType:'json',
            onSuccess:function(files,data,xhr){
                MessageBox('<?php $translate->__('Datos guardados'); ?>', '<?php $translate->__('La operaci&oacute;n se complet&oacute; correctamente.'); ?>', "<?php $translate->__('Aceptar'); ?>", function () {
                    $('#hdPage').val('1');
                    $('#hdPageActual').val('1');
                });
            }
        });
        $('#btnUploadExcel').on('click', function () {
            importData();
        });
        addValidFormProducto();
    });
    function clearFormIngrediente () {
        $('#txtNombreIngrediente').val('');
        $('#ddlUnidadMedida')[0].selectedIndex = 0;
        $('#txtCantidad').val('0.000');
    }
    
    function limpiarSeleccionados () {
        $('.gridview .selected').removeClass('selected');
        $('.gridview .tile input:checkbox').removeAttr('checked');
    }
    function importData () {
        $('#modalUpload').show();
        $.fn.custombox({
            url: '#modalUpload',
            effect: 'slit'
        });
    }
    function clearImagenForm () {
        $("#area").find('img').remove();
        $('#area .instructions').html('<?php $translate->__('Arrastre una imagen o haga click aqu&iacute;'); ?>');
        $("#area > .spot").append($('<img>',{'src': 'images/product-nosetimg.png'}));
    }
    function addValidFormProducto () {
        $('#txtCodigo').rules('add', {
            required: true,
            maxlength: 6
        });
        $('#txtNombre').rules('add', {
            required: true,
            maxlength: 150
        });
    }
    function removeValidFormProducto () {
        $('#txtCodigo').rules('remove');
        $('#txtNombre').rules('remove');
    }
    function addValidFormReceta () {
        $('#txtNombreIngrediente').rules('add', {
            required: true,
            maxlength: 150
        });
        $('#txtCantidad').rules('add', {
            required: true,
            maxlength: 11
        });
    }
    function removeValidFormReceta () {
        $('#txtNombreIngrediente').rules('remove');
        $('#txtCantidad').rules('remove');
    }
    function GoToEdit () {
        $('#pnlListado').fadeOut(500, function () {
            $('.appbar .metro_button').addClass('oculto');
            $('#btnRecetas, #btnGuardar, #btnCancelar').removeClass('oculto');
            $('#pnlForm').fadeIn(500, function () {
                addValidFormProducto();
                var itemSelected = $('#gvDatos .gridview .dato.selected');
                if (itemSelected.length > 0){
                    var idItem = itemSelected[0].getAttribute('rel');
                    var nombreArticulo = $(itemSelected[0]).find('.tile-status .label .nombre').text();
                    $('#pnlReceta h2.title-articulo').text(nombreArticulo);
                    $.ajax({
                        type: "GET",
                        url: '<?php echo $path_URLEditService; ?>',
                        cache: false,
                        data: 'id=' + idItem,
                        success: function (data) {
                            var datos = eval( "(" + data + ")" );
                            var foto = datos[0].tm_foto;
                            $('#hdIdPrimary').val(datos[0].tm_idproducto);
                            $('#txtCodigo').val(datos[0].tm_codigo);
                            $('#txtNombre').val(datos[0].tm_nombre);
                            $('#txtDescripcion').val(datos[0].td_contenido);
                            $('#ddlCategoriaReg').val(datos[0].tm_idcategoria);
                            
                            $('#ddlSubCategoriaReg').html('');
                            LoadSubCategorias(datos[0].tm_idcategoria, '#ddlSubCategoriaReg', datos[0].tm_idsubcategoria);
                            
                            $('#hdFoto').val(foto);
                            if (foto != 'no-set')
                                $('#area .instructions').html('');
                            $("#area > .spot > img").remove();
                            $("#area > .spot").append($('<img>',{'src': (foto == 'no-set' ? 'images/product-nosetimg.png' : foto)}));
                        }
                    });
                }
                $('#txtCodigo').focus();
            });
        });
    }
    function GoToReceta () {
        $('.divContent').addClass('no-overflow');
        $('#btnAgregarIngrediente').removeClass('oculto');
        $('#btnRecetas').addClass('oculto');
        removeValidFormProducto();
        $('#pnlForm').fadeOut(500, function () {
            $('#pnlReceta').fadeIn(500, function () {
                ListarReceta();
            });
        });
    }
    function GoToAddReceta () {
        addValidFormReceta();
        $('#btnAgregarIngrediente, #btnEditarIngrediente, #btnQuitarIngrediente, #btnLimpiarSeleccion').addClass('oculto');
        $('#btnBackListRecetas').removeClass('oculto');
        $('.moduloTwoPanel').animate({ scrollTop: $('.colTwoPanel2').offset().top }, 'slow', function () {
            $('#txtNombreIngrediente').focus();
        });
    }
    function BackToListRecetas () {
        removeValidFormReceta();
        $('#btnAgregarIngrediente').removeClass('oculto');
        $('#btnBackListRecetas').addClass('oculto');
        $(".moduloTwoPanel").animate({ scrollTop: 0 }, 500);
    }
    function BackToList () {
        removeValidFormProducto();
        $('#btnNuevo, #btnUploadExcel').removeClass('oculto');
        $('#btnGuardar, #btnCancelar, #btnBackListRecetas, #btnAgregarIngrediente, #btnRecetas').addClass('oculto');
        $('#pnlReceta').hide();
        $('#pnlForm').fadeOut(500, function () {
            $('#pnlListado').fadeIn(500, function () {
                $('#txtSearch').focus();
            });
        });
    }
    function DetalleReceta (idDetalle, nombreInsumo, idunidadmedida, tipomenudia, cantidad) {
        this.idDetalle = idDetalle;
        this.nombreInsumo = nombreInsumo;
        this.idunidadmedida = idunidadmedida;
        this.tipomenudia = tipomenudia;
        this.cantidad = cantidad;
    }
    function ExtraerDetalle () {
        var detalleReceta = '';
        var listaDetalle = [];
        var idDetalle = '0';
        var idunidadmedida = '0';
        var nombreInsumo = '';
        var tipomenudia = '';
        var precio = 0;
        var cantidad = 0;
        var subtotal = 0;
        var i = 0;
        
        var itemsDetalle = $('#tableReceta tbody tr');
        var countDetalle = itemsDetalle.length;
        if (countDetalle > 0){
            while(i < countDetalle){
                idDetalle = itemsDetalle[i].getAttribute('data-iddetalle');
                idunidadmedida = itemsDetalle[i].getAttribute('data-idunidadmedida');
                tipomenudia = itemsDetalle[i].getAttribute('data-tipomenu');
                nombreInsumo = $(itemsDetalle[i]).find('td.nombreInsumo').text();
                cantidad = $(itemsDetalle[i]).find('td.cantidad').text();
                var detalle = new DetalleReceta (idDetalle, nombreInsumo, idunidadmedida, tipomenudia, cantidad);
                listaDetalle.push(detalle);
                ++i;
            }
        }
        detalleReceta = JSON.stringify(listaDetalle);
        return detalleReceta;
    }
    
    function EnvioAdminDatos (form) {
        var detalleReceta = ExtraerDetalle();
        var tipomenudia = '00';
        tipomenudia = $('#pnlRecetario .sectionHeader button.success').attr('data-tipomenu');
        $.ajax({
            type: "POST",
            url: '?pag=<?php echo $pag; ?>&subpag=<?php echo $subpag; ?>',
            cache: false,
            data: $(form).serialize() + '&TipoMenuDia=' + tipomenudia + '&detalleReceta=' + detalleReceta + "&btnGuardar=btnGuardar",
            success: function(data){
                datos = eval( "(" + data + ")" );
                if (Number(datos.rpta) > 0){
                    MessageBox('<?php $translate->__('Datos guardados'); ?>', '<?php $translate->__('La operaci&oacute;n se complet&oacute; correctamente.'); ?>', "[<?php $translate->__('Aceptar'); ?>]", function () {
                        $('#hdPage').val('1');
                        $('#hdPageActual').val('1');
                        limpiarSeleccionados();
                        BuscarDatos('1');
                        clearImagenForm();
                        resetForm('form1');
                        BackToList();
                        $('#tableReceta tbody').html('<tr><td colspan="4"><h3><?php $translate->__('No se encontraron registros'); ?></h3></td></tr>');
                    });
                }
            }
        });
    }
    function GuardarDatos () {
        $('#form1').submit();
    }
    function BuscarDatos (pagina) {
        LoadProductos(TipoBusqueda, $('#ddlCategoria').val(), $('#ddlSubCategoria').val(), $('#txtSearch').val(), pagina);
    }
    function ListarReceta () {
        var idproducto = $('#hdIdPrimary').val();
        var tipomenudia = '00';
        tipomenudia = $('#pnlRecetario .sectionHeader button.success').attr('data-tipomenu');
        $('#tableReceta tbody tr').remove();
        $.ajax({
            type: "GET",
            url: 'services/recetas/recetas-search.php',
            cache: false,
            data: 'tipobusqueda=L&idproducto=' + idproducto + '&tipomenudia=' + tipomenudia,
            success: function(data){
                var datos = eval( "(" + data + ")" );
                var countDatos = datos.length;
                var i = 0;
                var content = '';
                if (countDatos > 0){
                    while(i < countDatos){
                        content += '<tr data-iddetalle="' + datos[i].td_idreceta + '" data-idinsumo="' + datos[i].tm_idinsumo + '" data-idunidadmedida="' + datos[i].tm_idunidadmedida + '" data-tipomenu="' + datos[i].ta_tipomenudia + '">';
                        content += '<td class="nombreInsumo">' + datos[i].tm_nombre + '</td>';
                        content += '<td class="unidadmedida">' + datos[i].UnidadMedida + '</td>';
                        content += '<td class="cantidad text-right">' + Number(datos[i].td_cantidad).toFixed(3) + '</td>';
                        ++i;
                    }
                }
                else
                    content = '<tr><td colspan="4"><h3><?php $translate->__('No se encontraron registros'); ?></h3></td></tr>';
                $('#tableReceta tbody').append(content);
            }
        });
    }
    function GetInfoIngrediente () {
        var nombreInsumo = '';
        var idunidadmedida = '0';
        var cantidad = 0;
        var rowSelected = $('#tableReceta tbody tr.selected:first');
        idunidadmedida = rowSelected[0].getAttribute('data-idunidadmedida');
        nombreInsumo = rowSelected.find('.nombreInsumo').text();
        cantidad = Number(rowSelected.find('.cantidad').text());
        $('#btnAddReceta').html('<?php $translate->__('Modificar ingrediente'); ?>').addClass('mode-edit');
        $('#ddlUnidadMedida').val(idunidadmedida);
        $('#txtCantidad').val(cantidad.toFixed(3));
        $('#txtNombreIngrediente').val(nombreInsumo);
    }
    function ModificarIngrediente () {
        var nombre = '';
        var idunidadmedida = '0';
        var unidadmedida = '';
        var tipomenudia = '';
        var cantidad = 0;
        var precio = 0;
        var subtotal = 0;
        var content = '';
        var rowSelected = $('#tableReceta tbody tr.selected');
        
        nombre = $('#txtNombreIngrediente').val();
        tipomenudia = $('#pnlRecetario .sectionHeader button.success').attr('data-tipomenu');
        idunidadmedida = $('#ddlUnidadMedida').val();
        unidadmedida = $('#ddlUnidadMedida option:selected').attr('data-simbolo');
        cantidad = Number($('#txtCantidad').val());
        if ($('#btnAddReceta').hasClass('mode-edit')){
            if (rowSelected.length > 0){
                rowSelected.attr('data-idunidadmedida', idunidadmedida);
                rowSelected.find('.nombreInsumo').text(nombre);
                rowSelected.find('.unidadmedida').text(unidadmedida);
                rowSelected.find('.cantidad').text(cantidad.toFixed(3));
                $('#btnCancelReceta').trigger('click');
            }
        }
        else {
            if ($('#tableReceta tbody tr:first').find('h3').length > 0)
                $('#tableReceta tbody tr').remove();
            
            content = '<tr data-iddetalle="0" data-idinsumo="0" data-idunidadmedida="' + idunidadmedida + '" data-tipomenu="' + tipomenudia + '">';
            content += '<td class="nombreInsumo">' + nombre + '</td>';
            content += '<td class="unidadmedida">' + unidadmedida + '</td>';
            content += '<td class="cantidad text-right">' + cantidad.toFixed(3) + '</td>';
            content += '</tr>';
            $('#tableReceta tbody').append(content);
            $('#txtNombreIngrediente').focus();
        }
        clearFormIngrediente();
    }
    function QuitarIngrediente () {
        clearFormIngrediente();
        $('#tableReceta tbody tr.selected').remove();
        $('#btnAgregarIngrediente').removeClass('oculto');
        $('#btnQuitarIngrediente, #btnEditarIngrediente, #btnLimpiarSeleccion').addClass('oculto');
    }
</script>