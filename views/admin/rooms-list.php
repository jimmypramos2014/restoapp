<?php
include('bussiness/ambientes.php');
include('bussiness/mesas.php');
$IdEmpresa = 1;
$IdCentro = 1;
$Id = '0';
$IdAmbiente = '0';
$Codigo = '';
$NroComensales = '';
$strListValids = '';
$objAmbiente = new clsAmbiente();
$objMesa = new clsMesa();
if ($_POST){
    $hdTipoData = (isset($_POST['hdTipoData'])) ? $_POST['hdTipoData'] : '00';
    if ($_POST['btnSaveRooms']){
        $Id = (isset($_POST['hdIdPrimary'])) ? $_POST['hdIdPrimary'] : '0';
        if ($hdTipoData == '00'){
            $Codigo = isset($_POST['txtCodigoAmbiente']) ? $_POST['txtCodigoAmbiente'] : '';
            $Nombre = isset($_POST['txtNombreAmbiente']) ? $_POST['txtNombreAmbiente'] : '';
            $entityInsert = array(   
                'tm_idempresa' => $IdEmpresa, 
                'tm_idcentro' => $IdCentro,
                'tm_codigo' => $Codigo,
                'Activo' => 1,
                'IdUsuarioReg' => $idusuario,
                'FechaReg' => date("Y-m-d h:i:s")
            );
            $entityUpdate = array(
                'tm_idambiente' => $Id, 
                'tm_nombre' => $Nombre,
                'IdUsuarioAct' => $idusuario,
                'FechaAct' => date("Y-m-d h:i:s")
            );
        }
        elseif ($hdTipoData == '01'){
            $IdAmbiente = (isset($_POST['hdIdAmbiente'])) ? $_POST['hdIdAmbiente'] : '0';
            $Codigo = isset($_POST['txtCodigoMesa']) ? $_POST['txtCodigoMesa'] : '';
            $EsCorrelativo = isset($_POST['chkCorrelativoMesa']) ? $_POST['chkCorrelativoMesa'] : '0';
            $NroComensales = (isset($_POST['txtNroComensales'])) ? $_POST['txtNroComensales'] : '1';
            if ($EsCorrelativo == '1'){
                if ($Id == '0'){
                    $rsCorrelativo = $objMesa->Correlativo($IdEmpresa, $IdCentro);
                    $Codigo = $rsCorrelativo[0]['Correlativo'];
                }
            }
            $entityInsert = array(
                'tm_idempresa' => $IdEmpresa, 
                'tm_idcentro' => $IdCentro,
                'tm_codigo' => $Codigo,
                'tm_idambiente' => $IdAmbiente,
                'ta_estadoatencion' => '00',
                'Activo' => 1,
                'IdUsuarioReg' => $idusuario,
                'FechaReg' => date("Y-m-d h:i:s")
            );
            $entityUpdate = array(
                'tm_idmesa' => $Id, 
                'tm_nrocomensales' => $NroComensales,
                'IdUsuarioAct' => $idusuario,
                'FechaAct' => date("Y-m-d h:i:s")
            );
        }
        if ($Id == '0')
            $entidad = array_merge($entityInsert, $entityUpdate);
        else
            $entidad = $entityUpdate;
        if ($hdTipoData == '00')
            $rpta = $objAmbiente->Registrar($entidad);
        elseif ($hdTipoData == '01')
            $rpta = $objMesa->Registrar($entidad);
        if ($Id != '0')
            $rpta = $Id;
        $jsondata = array('rpta' => $rpta, 'codigo' => $Codigo);
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
                $rpta = $objMesa->MultiDelete($strListItems);
            }
        if (!empty($arrayValid))
            $strListValids = implode(',', $arrayValid);
        $jsondata = array('rpta' => $rpta, 'items_valid' => $strListValids);
    }
    elseif ($_POST['btnDelAmbiente']){
        $IdAmbiente = (isset($_POST['hdIdAmbiente'])) ? $_POST['hdIdAmbiente'] : '0';
        
        $row = $objMesa->Listar("M", $IdAmbiente);
        $countrow = count($row);
        if ($countrow > 0){
            $strListValids = $IdAmbiente;
            $rpta = 1;
        }
        else
            $rpta = $objAmbiente->Delete($IdAmbiente);
        $jsondata = array('rpta' => $rpta, 'items_valid' => $strListValids);   
    }
    echo json_encode($jsondata);
    exit(0);
}
$rowAmbiente = $objAmbiente->Listar('L', $IdEmpresa);
$countRowAmbiente = count($rowAmbiente);
?>
<form id="form1" name="form1" method="post">
    <input type="hidden" id="fnPost" name="fnPost" value="fnPost" />
    <input type="hidden" id="hdPageActual" name="hdPageActual" value="1" />
    <input type="hidden" id="hdPage" name="hdPage" value="1" />
    <input type="hidden" id="hdIdPrimary" name="hdIdPrimary" value="0" />
    <input type="hidden" id="hdIdAmbiente" name="hdIdAmbiente" value="0" />
    <input type="hidden" id="hdTipoData" name="hdTipoData" value="no-set" />
    <div class="page-region">
        <div id="pnlListado" class="inner-page">
            <h1 class="title-window">
                <?php $translate->__('Ambientes y mesas'); ?>
            </h1>
            <div class="divload">
                <div class="scroll-content">
                    <ul id="nav">
                        <?php
                        for ($counterAmbiente=0; $counterAmbiente < $countRowAmbiente; $counterAmbiente++) { 
                        ?>
                        <li>
                            <a href="#" rel="<?php echo $rowAmbiente[$counterAmbiente]['tm_idambiente']; ?>"><?php echo $rowAmbiente[$counterAmbiente]['tm_nombre']; ?></a>
                            <section>
                                <div class="tile-area gridview"></div>
                            </section>
                        </li>
                        <?php 
                        }
                        ?>
                    </ul>
                </div>
            </div>
		</div>
		<div id="pnlForm" class="inner-page" style="display:none;">
            <h1 class="title-window">
                <a id="btnBackList" href="#" title="Regresar a listado" class="back-button"><i class="icon-arrow-left-3 fg-darker smaller"></i></a>
                <?php $translate->__('Registro'); ?>
            </h1>
            <div class="divContent">
                <div id="pnlEditAmbiente">
	                <div class="form-register-data container">
	                    <div class="grid">
	                        <div class="row">
	                            <div class="span6">
                                    <label for="txtCodigoAmbiente"><?php $translate->__('C&oacute;digo'); ?></label>
                                    <div class="input-control text" data-role="input-control">
                                        <input id="txtCodigoAmbiente" name="txtCodigoAmbiente" type="text" placeholder="<?php $translate->__('Ejemplo 001'); ?>" />
                                        <button class="btn-clear" tabindex="-1" type="button"></button>
                                    </div>
                                </div>
                                <div class="span6">
                                    <label for="txtNombreAmbiente"><?php $translate->__('Nombre de ambiente'); ?></label>
                                    <div class="input-control text" data-role="input-control">
                                        <input id="txtNombreAmbiente" name="txtNombreAmbiente" type="text" placeholder="<?php $translate->__('Ejemplo: Patio'); ?>" />
                                        <button class="btn-clear" tabindex="-1" type="button"></button>
                                    </div>
                        		</div>
	                        </div>
	                    </div>
	                </div>
	                <div class="clear"></div>
	            </div>
                <div id="pnlEditMesa">
                    <div class="form-register-data container">
                        <div class="grid">
                            <div class="row">
                                <div class="span6">
                                    <label for="txtCodigoMesa"><?php $translate->__('C&oacute;digo'); ?></label>
                                    <div class="input-control text" data-role="input-control">
                                        <input id="txtCodigoMesa" name="txtCodigoMesa" type="text" disabled />
                                        <button class="btn-clear" tabindex="-1" type="button"></button>
                                    </div>
                                    <div class="input-control checkbox">
                                        <label>
                                            <input id="chkCorrelativoMesa" name="chkCorrelativoMesa" type="checkbox" value="1" checked />
                                            <span class="check"></span>
                                            <?php $translate->__('Correlativo'); ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="span6">
                                    <label for="txtNroComensales"><?php $translate->__('Numero de comensales'); ?></label>
                                    <div class="input-control text" data-role="input-control">
                                        <input id="txtNroComensales" name="txtNroComensales" type="text" class="only-numbers" value="1" />
                                        <button class="btn-clear" tabindex="-1" type="button"></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="appbar">
        <button id="btnEliminar" type="button" class="cancel metro_button oculto float-right">
            <span class="content">
                <img src="images/trash.png" alt="<?php $translate->__('Eliminar'); ?>" />
                <span class="text"><?php $translate->__('Eliminar mesa'); ?></span>
            </span>
        </button>
        <button id="btnEditRooms" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/edit.png" alt="<?php $translate->__('Editar'); ?>" />
                <span class="text"><?php $translate->__('Editar mesa'); ?></span>
            </span>
        </button>
        <button id="btnCancelRooms" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/cancel.png" alt="<?php $translate->__('Cancelar'); ?>" />
                <span class="text"><?php $translate->__('Cancelar'); ?></span>
            </span>
        </button>
        <button id="btnSaveRooms" name="btnSaveRooms" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/save.png" alt="<?php $translate->__('Guardar'); ?>" />
                <span class="text"><?php $translate->__('Guardar'); ?></span>
            </span>
        </button>
        <button id="btnNuevaMesa" type="button" class="metro_button float-right">
            <span class="content">
                <img src="images/add.png" alt="<?php $translate->__('Nuevo'); ?>" />
                <span class="text"><?php $translate->__('Nueva mesa'); ?></span>
            </span>
        </button>
        <button id="btnNuevoAmbiente" type="button" class="metro_button float-left">
            <span class="content">
                <img src="images/add-2.png" alt="<?php $translate->__('Nuevo'); ?>" />
                <span class="text"><?php $translate->__('Nuevo ambiente'); ?></span>
            </span>
        </button>
        <button id="btnEditAmbiente" type="button" class="metro_button float-left">
            <span class="content">
                <img src="images/edit-2.png" alt="<?php $translate->__('Editar ambiente'); ?>" />
                <span class="text"><?php $translate->__('Editar ambiente'); ?></span>
            </span>
        </button>
        <button id="btnDelAmbiente" type="button" class="metro_button float-left">
            <span class="content">
                <img src="images/delete-2.png" alt="<?php $translate->__('Eliminar ambiente'); ?>" />
                <span class="text"><?php $translate->__('Eliminar ambiente'); ?></span>
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
</form>
<?php
include('common/libraries-js.php');
include('common/validate-js.php');
include('common/bootstrap-js.php');
?>
<script>
    $(function () {
        $('#nav').children('li').first().addClass('active').children('a')
        .next().addClass('is-open').show(300, function () {
            ShowMesasByCurrentTab();
        });
        
        $('#nav').on('click', 'li > a', function() {
            selectAmbiente(this);
        });
        $('#btnSaveRooms').on('click', function (evt) {
            GuardarDatos();
        });
        $('#btnCancelRooms').on('click', function () {
            BackToList();
            return false;
        });
        $('#pnlForm').on('click', '#btnBackList', function () {
            BackToList();
            return false;
        });
        $('#btnNuevoAmbiente').on('click', function () {
            var tipoEdit = 'N';
            var TipoData = '00';
            GoToEditRoom(TipoData, tipoEdit);
            return false;
        });
        $('#btnNuevaMesa').on('click', function () {
            var tipoEdit = 'N';
            var TipoData = '01';
            GoToEditRoom(TipoData, tipoEdit);
            return false;
        });
        $('#btnEditAmbiente').on('click', function () {
            var tipoEdit = 'E';
            var TipoData = '00';
            GoToEditRoom(TipoData, tipoEdit);
            return false;
        });
        $('#btnEditRooms').on('click', function () {
            var tipoEdit = 'E';
            var TipoData = '01';
            GoToEditRoom(TipoData, tipoEdit);
            return false;
        });
        $("#btnLimpiarSeleccion").on('click', function(){
            clearSelection();
            return false;
        });
        $('#btnEliminar').on('click', function () {
            EliminarDatos();
            return false;
        });
        $('#btnDelAmbiente').on('click', function () {
            EliminarAmbiente();
            return false;
        })
    	$("#form1").validate({
            lang: 'es',
            showErrors: showErrorsInValidate,
            submitHandler: EnvioAdminDatos
        });
        $('#chkCorrelativoMesa').on('click', function () {
            var check = this.checked;
            habilitarControl('#txtCodigoMesa', !check);
            if (!check)
                $('#txtCodigoMesa').focus();
        })
        addValidFormRegister('00');
    });
    function BackToList () {
        resetForm('form1');
        BackToListRooms();
        $('.tile-area .dato').removeClass('selected');
    }
    function selectAmbiente (obj) {
        var linkAmb = $(obj);
        clearOnlyListSelection();
        if (!linkAmb.parent().hasClass('active')) {
            $('#nav .is-open').removeClass('is-open').hide(300);
            linkAmb.next().toggleClass('is-open').toggle(300, function () {
                ShowMesasByCurrentTab();
            });
          
            $('#nav').find('.active').removeClass('active');
            linkAmb.parent().addClass('active');
            $('#hdIdAmbiente').val(linkAmb.attr('rel'));
            $('#btnEditAmbiente, #btnDelAmbiente, #btnNuevaMesa').removeClass('oculto');
        }
        else {
            $('#nav .is-open').removeClass('is-open').hide(300);
            linkAmb.parent().removeClass('active');
            if ($('#nav li.active').length == 0){
                $('#hdIdAmbiente').val('0');
                $('#btnEditAmbiente, #btnDelAmbiente, #btnNuevaMesa').addClass('oculto');
            }
        }
    }
    function ShowMesasByCurrentTab () {
        var currentTab = $('#nav li.active a');
        $('#nav li.active section .tile-area').html('');
        if (currentTab != null)
            MostrarMesas(currentTab.attr('rel'));
    }
    function EnvioAdminDatos (form) {
        $.ajax({
            type: "POST",
            url: '?pag=<?php echo $pag; ?>&subpag=<?php echo $subpag; ?>',
            cache: false,
            data: $(form).serialize() + "&btnSaveRooms=btnSaveRooms",
            success: function(data){
                datos = eval( "(" + data + ")" );
                if (Number(datos.rpta) > 0){
                    MessageBox('<?php $translate->__('Datos guardados'); ?>', '<?php $translate->__('La operaci&oacute;n se complet&oacute; correctamente.'); ?>', "[<?php $translate->__('Aceptar'); ?>]", function () {
                        var TipoData = $('#hdTipoData').val();
                        var Id = $('#hdIdPrimary').val();
                        var NombreAmbiente = $('#txtNombreAmbiente').val();
                        var strContent = '';
                        $('#hdPage').val('1');
                        $('#hdPageActual').val('1');
                        clearOnlyListSelection();
                        BackToListRooms();
                        if (TipoData == '00'){
                            if (Id == '0'){
                                aLink = $('<a href="#"></a>');
                                li = $('<li></li>');
                                section = $('<section></section>');
                                tileArea = $('<div class="tile-area gridview"></div>');
                                aLink.attr('rel', datos.rpta).text(NombreAmbiente);
                                section.append(tileArea);
                                li.append(aLink);
                                li.append(section);
                                $('#nav').append(li);
                            }
                            else
                                $('#nav li.active a[rel="' + datos.rpta + '"]').text(NombreAmbiente);
                        }
                        else if (TipoData == '01'){
                            if (Id == '0'){
                                $('#nav li.active section .tile-area h2').remove();
                                
                                tile = $('<div class="tile dato ribbed-green" rel="' + datos.rpta + '"></div>');
                                strContent = '<div class="tile-content">';
                                strContent += '<input type="checkbox" name="chkItem[]" class="oculto" value="' + datos.rpta + '"  />';
                                strContent += '<div class="text-center">';
                                strContent += '<h1 class="fg-white" style="margin:30px 0px;">' + datos.codigo + '</h1>';
                                strContent += '</div></div>';
                                tile.html(strContent).appendTo('#nav li.active section .tile-area').on('click', function () {
                                    $('#hdTipoData').val('01');
                                    selectInTile (this);
                                    return false;
                                });
                            }
                            else
                                $('#nav li.active .tile[rel="' + datos.rpta + '"] .text-center h1').text(datos.codigo);
                        }
                        resetForm('form1');
                    });
                }
            }
        });
    }
    function EliminarAmbiente () {
        var objAmbiente = $('#nav li.active a');
        var IdAmbiente = objAmbiente.attr('rel');
        var serializedReturn = $("#form1 input[type!=text]").serialize() + '&hdIdAmbiente=' + IdAmbiente + '&btnDelAmbiente=btnDelAmbiente';
        if (IdAmbiente != null){
            precargaExp('.page-region', true);
            $.ajax({
                type: "POST",
                url: '?pag=<?php echo $pag; ?>&subpag=<?php echo $subpag; ?>',
                cache: false,
                data: serializedReturn,
                success: function(data){
                    var titleMensaje = '';
                    var contentMensaje = '';
                    var datos = eval( "(" + data + ")" );
                    var validItems = datos.items_valid;
                    var countValidItems = validItems.length;
                    var Notif = '';
                    precargaExp('.page-region', false);
                    if (Number(datos.rpta) > 0){
                        if (countValidItems > 0){
                            titleMensaje = '<?php $translate->__('No se pudo eliminar'); ?>';
                            contentMensaje = '<?php $translate->__('Algunos items no se eliminaron. Click en "Aceptar" para ver detalle.'); ?>';
                        }
                        else {
                            titleMensaje = '<?php $translate->__('Items eliminados correctamente'); ?>';
                            contentMensaje = '<?php $translate->__('La operaci&oacute;n ha sido completada'); ?>';    
                        }
                    }
                    else {
                        titleMensaje = '<?php $translate->__('No se pudo eliminar'); ?>';
                        contentMensaje = '<?php $translate->__('La operaci&oacute;n no pudo completarse'); ?>';
                    }
                    MessageBox(titleMensaje, contentMensaje, "[<?php $translate->__('Aceptar'); ?>]", function () {
                        if (countValidItems > 0){
                            $('.error-list').html('');
                            Notif += '<div class="notification warning">';
                            Notif += '<aside><i class="fa fa-warning"></i></aside>';
                            Notif += '<main><p><strong>Error en item con ID: ' + objAmbiente.attr('rel') + ' - ' + objAmbiente.text() + '</strong>';
                            Notif += '<?php $translate->__('El item no pudo ser eliminado por tener referencia con otras operaciones realizadas.'); ?></p></main>';
                            Notif += '</div>';
                            $('.error-list').html(Notif);
                            $('#modalItemsError').show();
                            $.fn.custombox({
                                url: '#modalItemsError',
                                effect: 'slit'
                            });
                        }
                        else {
                            $('#nav li.active').fadeOut(400, function () {
                                $(this).remove();
                            });
                        }
                    });
                }
            });
        }
    }
    function BackToListRooms () {
        $('#btnNuevoAmbiente').removeClass('oculto');
        if ($('#nav li.active').length > 0)
            $('#btnEditAmbiente, #btnDelAmbiente, #btnNuevaMesa').removeClass('oculto');
        $('#btnSaveRooms, #btnCancelRooms').addClass('oculto')
        $('#pnlForm').fadeOut(500, function () {;
            $('#pnlListado').fadeIn(500, function () {
                $('#txtSearch').focus();
            });
            aplicarDimensiones();
        });
    }
    function GoToEditRoom (TipoData, ModoEdit) {
        var contentTitle = '<a id="btnBackList" href="#" title="<?php $translate->__('Regresar a listado'); ?>" class="back-button"><i class="icon-arrow-left-3 fg-darker smaller"></i></a>';
        var titleForm = '';
        $('#hdTipoData').val(TipoData);
        aplicarDimensiones();
        if (TipoData == '00'){
            $('#pnlEditAmbiente').show();
            $('#pnlEditMesa').hide();
            titleForm = '<?php $translate->__('Registrar ambiente'); ?>';
        }
        else {
            $('#pnlEditAmbiente').hide();
            $('#pnlEditMesa').show();
            titleForm = '<?php $translate->__('Registrar mesa'); ?>';
        }
        $('#pnlForm h1.title-window').html(contentTitle + titleForm);
        $('#pnlListado').fadeOut(500, function () {
            $('.appbar .metro_button').addClass('oculto');
            $('#btnSaveRooms, #btnCancelRooms').removeClass('oculto');
            $('#pnlForm').fadeIn(500, function () {
                aplicarDimensiones();
                if (ModoEdit == 'E'){
                    if (TipoData == '00')
                        itemSelected = $('#nav li.active a');
                    else
                        itemSelected = $('.gridview .dato.selected');
                    if (itemSelected.length > 0){
                        var idItem = itemSelected[0].getAttribute('rel');
                        $.ajax({
                            type: "GET",
                            url: "services/ambientes/amesas-getdetails.php",
                            cache: false,
                            data: "tipo=" + TipoData + "&id=" + idItem,
                            success: function(data){
                                var datos = eval( "(" + data + ")" );
                                if (TipoData == '00'){
                                    removeValidFormRegister('01');
                                    addValidFormRegister('00');
                                    $('#hdIdPrimary').val(datos[0].tm_idambiente);
                                    $('#txtCodigoAmbiente').val(datos[0].tm_codigo);
                                    $('#txtNombreAmbiente').val(datos[0].tm_nombre);
                                }
                                else {
                                    removeValidFormRegister('00');
                                    addValidFormRegister('01');
                                    $('#hdIdPrimary').val(datos[0].tm_idmesa);
                                    $('#txtCodigoMesa').val(datos[0].tm_codigo);
                                    $('#txtNroComensales').val(datos[0].tm_nrocomensales);
                                }
                            }
                        });
                    }
                }
                if ($('#pnlEditAmbiente').is(':visible'))
                    $('#txtCodigoAmbiente').focus();
                else
                    $('#txtCodigoMesa').focus();
            });
        });
    }
    function addValidFormRegister (TipoData) {
        if (TipoData == '00'){
            $('#txtCodigoAmbiente').rules('add', {
                required: true,
                maxlength: 4
            });
            $('#txtNombreAmbiente').rules('add', {
                required: true,
                maxlength: 100
            });
        }
        else if (TipoData == '01') {
            $('#txtCodigoMesa').rules('add', {
                required: true,
                maxlength: 5
            });
            $('#txtNroComensales').rules('add', {
                required: true
            });
        }
    }
    function removeValidFormRegister (TipoData) {
        if (TipoData == '00'){
            $('#txtCodigoAmbiente').rules('remove');
            $('#txtNombreAmbiente').rules('remove');
        }
        else if (TipoData == '01') {
            $('#txtCodigoMesa').rules('remove');
            $('#txtNroComensales').rules('remove');
        }
    }
    function MostrarMesas (idambiente) {
    	$.ajax({
	        type: "GET",
	        url: "services/ambientes/mesas-search.php",
	        cache: false,
	        data: "idambiente=" + idambiente,
	        success: function(data){
	        	var datos = eval( "(" + data + ")" );
	        	var countDatos = datos.length;
	        	var i = 0;
	            var emptyMessage = '';
	            var selector = '#nav li.active section .tile-area';
                var strContent = '';
	            $(selector).html('');
	            if (countDatos > 0){
	            	while(i < countDatos){
	            		tile = $('<div class="tile dato ribbed-green" rel="' + datos[i].tm_idmesa + '"></div>');
	            		strContent = '<div class="tile-content">';
                        strContent = '<input type="checkbox" name="chkItem[]" class="oculto" value="' + datos[i].tm_idmesa + '" />';
			            strContent += '<div class="text-center">';
			            strContent += '<h1 class="fg-white" style="margin:30px 0px;">' + datos[i].tm_codigo + '</h1>';
			            strContent += '</div></div>';
                        strContent += '<div class="brand"><span class="badge bg-dark">' + datos[i].tm_nrocomensales + '</span></div>';
			            tile.html(strContent).appendTo(selector).on('click', function () {
			            	$('#hdTipoData').val('01');
			            	selectInTile (this);
			            	return false;
			            });
	            		++i;
	            	}
	            }
	            else {
	            	emptyMessage = '<h2><?php $translate->__('No se encontraron registros'); ?></h2>';
                    $(selector).html(emptyMessage);
	            }
	        }
	    });
    }
    function GuardarDatos () {
        $('#form1').submit();
    }
</script>