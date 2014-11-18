<?php
include('bussiness/categoria.php');
$IdEmpresa = 1;
$IdCentro = 1;
$Id = '0';
$IdSubCategoria = '0';
$Codigo = '';
$Nombre = '';
$strListValids = '';
$objCategoria = new clsCategoria();
if ($_POST){
    $hdTipoData = (isset($_POST['hdTipoData'])) ? $_POST['hdTipoData'] : '00';
    if ($_POST['btnSaveCategoria']){
        $Id = (isset($_POST['hdIdPrimary'])) ? $_POST['hdIdPrimary'] : '0';
        $Codigo = isset($_POST['txtCodigo']) ? $_POST['txtCodigo'] : '';
        $Nombre = isset($_POST['txtNombre']) ? $_POST['txtNombre'] : '';
        $IdSubCategoria = (isset($_POST['hdIdSubCategoria'])) ? $_POST['hdIdSubCategoria'] : '0';
        $EsCorrelativo = isset($_POST['chkCorrelativo']) ? $_POST['chkCorrelativo'] : '0';
        if ($hdTipoData == '00')
            $IdSubCategoria = '0';
        if ($EsCorrelativo == '1'){
            if ($Id == '0'){
                $rsCorrelativo = $objCategoria->Correlativo($IdEmpresa, $IdCentro);
                $Codigo = $rsCorrelativo[0]['Correlativo'];
            }
        }
        $entityInsert = array(
            'tm_idempresa' => $IdEmpresa, 
            'tm_idcentro' => $IdCentro,
            'tm_codigo' => $Codigo,
            'tm_idrefcategoria' => $IdSubCategoria,
            'ta_tipocategoria' => $hdTipoData,
            'Activo' => 1,
            'IdUsuarioReg' => $idusuario,
            'FechaReg' => date("Y-m-d h:i:s")
        );
        $entityUpdate = array(
            'tm_idcategoria' => $Id, 
            'tm_nombre' => $Nombre,
            'IdUsuarioAct' => $idusuario,
            'FechaAct' => date("Y-m-d h:i:s")
        );
        if ($Id == '0')
            $entidad = array_merge($entityInsert, $entityUpdate);
        else
            $entidad = $entityUpdate;
        $rpta = $objCategoria->Registrar($entidad);
        if ($Id != '0')
            $rpta = $Id;
        $jsondata = array('rpta' => $rpta, 'nombre' => $Nombre);
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
                $rpta = $objCategoria->MultiDelete($strListItems);
            }
        if (!empty($arrayValid))
            $strListValids = implode(',', $arrayValid);
        $jsondata = array('rpta' => $rpta, 'items_valid' => $strListValids);
    }
    elseif ($_POST['btnDelCategoria']){
        $IdSubCategoria = (isset($_POST['hdIdSubCategoria'])) ? $_POST['hdIdSubCategoria'] : '0';
        
        $row = $objCategoria->Listar("M", $IdEmpresa, $IdCentro, $IdSubCategoria);
        $countrow = count($row);
        if ($countrow > 0){
            $strListValids = $IdSubCategoria;
            $rpta = 1;
        }
        else
            $rpta = $objCategoria->Delete($IdSubCategoria);
        $jsondata = array('rpta' => $rpta, 'items_valid' => $strListValids);   
    }
    echo json_encode($jsondata);
    exit(0);
}
$RowCat = $objCategoria->Listar('M', $IdEmpresa, $IdCentro, '0');
$countRowCat = count($RowCat);
?>
<form id="form1" name="form1" method="post">
    <input type="hidden" id="fnPost" name="fnPost" value="fnPost" />
    <input type="hidden" id="hdPageActual" name="hdPageActual" value="1" />
    <input type="hidden" id="hdPage" name="hdPage" value="1" />
    <input type="hidden" id="hdIdPrimary" name="hdIdPrimary" value="0" />
    <input type="hidden" id="hdIdSubCategoria" name="hdIdSubCategoria" value="0" />
    <input type="hidden" id="hdTipoData" name="hdTipoData" value="no-set" />
    <div class="page-region">
        <div id="pnlListado" class="inner-page">
            <h1 class="title-window">
                Categor&iacute;as y subcategor&iacute;as
            </h1>
            <div class="divload">
                <div class="scroll-content">
                    <ul id="nav">
                        <?php
                        for ($counterCat=0; $counterCat < $countRowCat; $counterCat++) { 
                        ?>
                        <li>
                            <a href="#" rel="<?php echo $RowCat[$counterCat]['tm_idcategoria']; ?>"><?php echo $RowCat[$counterCat]['tm_nombre']; ?></a>
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
                Registro
            </h1>
            <div class="divContent">
                <div class="form-register-data container">
                    <div class="grid">
                        <div class="row">
                            <div class="span6">
                                <label for="txtCodigo"><?php $translate->__('C&oacute;digo'); ?></label>
                                <div class="input-control text" data-role="input-control">
                                    <input id="txtCodigo" name="txtCodigo" type="text" placeholder="<?php $translate->__('Ejemplo 001'); ?>" disabled />
                                    <button class="btn-clear" tabindex="-1" type="button"></button>
                                </div>
                                <div class="input-control checkbox">
                                    <label>
                                        <input id="chkCorrelativoMesa" name="chkCorrelativoMesa" type="checkbox" value="1" checked />
                                        <span class="check"></span>
                                        Correlativo
                                    </label>
                                </div>
                            </div>
                            <div class="span6">
                                <label for="txtNombre"><?php $translate->__('Nombre del ambiente'); ?></label>
                                <div class="input-control text" data-role="input-control">
                                    <input id="txtNombre" name="txtNombre" type="text" placeholder="<?php $translate->__('Ejemplo: Patio'); ?>" />
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
    <div class="appbar">
        <button id="btnEliminar" type="button" class="cancel metro_button oculto float-right">
            <span class="content">
                <img src="images/trash.png" alt="<?php $translate->__('Eliminar'); ?>" />
                <span class="text"><?php $translate->__('Eliminar'); ?></span>
            </span>
        </button>
        <button id="btnEditRooms" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/edit.png" alt="<?php $translate->__('Editar subcategor&iacute;a'); ?>" />
                <span class="text"><?php $translate->__('Editar subcategor&iacute;a'); ?></span>
            </span>
        </button>
        <button id="btnCancelRooms" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/cancel.png" alt="<?php $translate->__('Cancelar'); ?>" />
                <span class="text"><?php $translate->__('Cancelar'); ?></span>
            </span>
        </button>
        <button id="btnSaveCategoria" name="btnSaveCategoria" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/save.png" alt="<?php $translate->__('Guardar'); ?>" />
                <span class="text"><?php $translate->__('Guardar'); ?></span>
            </span>
        </button>
        <button id="btnNuevaMesa" type="button" class="metro_button float-right">
            <span class="content">
                <img src="images/add.png" alt="<?php $translate->__('Nueva subcategor&iacute;a'); ?>" />
                <span class="text"><?php $translate->__('Nueva subcategor&iacute;a'); ?></span>
            </span>
        </button>
        <button id="btnNuevoAmbiente" type="button" class="metro_button float-left">
            <span class="content">
                <img src="images/add-2.png" alt="<?php $translate->__('Nueva categor&iacute;a'); ?>" />
                <span class="text"><?php $translate->__('Nueva categor&iacute;a'); ?></span>
            </span>
        </button>
        <button id="btnEditAmbiente" type="button" class="metro_button float-left">
            <span class="content">
                <img src="images/edit-2.png" alt="<?php $translate->__('Editar categor&iacute;a'); ?>" />
                <span class="text"><?php $translate->__('Editar categor&iacute;a'); ?></span>
            </span>
        </button>
        <button id="btnDelCategoria" type="button" class="metro_button float-left">
            <span class="content">
                <img src="images/delete-2.png" alt="<?php $translate->__('Eliminar categor&iacute;a'); ?>" />
                <span class="text"><?php $translate->__('Eliminar categor&iacute;a'); ?></span>
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
            ShowDataByCurrentTab();
        });
        
        $('#nav').on('click', 'li > a', function() {
            selectCategoria(this);
        });
        $('#btnSaveCategoria').on('click', function (evt) {
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
            $('#hdIdSubCategoria').val('0');
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
        $('#btnDelCategoria').on('click', function () {
            EliminarCategoria();
            return false;
        })
        $("#form1").validate({
            lang: 'es',
            showErrors: showErrorsInValidate,
            submitHandler: EnvioAdminDatos
        });
        $('#chkCorrelativo').on('click', function () {
            var check = this.checked;
            habilitarControl('#txtCodigo', !check);
            if (!check)
                $('#txtCodigo').focus();
        })
        addValidFormRegister('00');
    });
    function BackToList () {
        resetForm('form1');
        BackToListRooms();
        $('.tile-area .dato').removeClass('selected');
    }
    function selectCategoria (obj) {
        var linkAmb = $(obj);
        clearOnlyListSelection();
        if (!linkAmb.parent().hasClass('active')) {
            $('#nav .is-open').removeClass('is-open').hide(300);
            linkAmb.next().toggleClass('is-open').toggle(300, function () {
                ShowDataByCurrentTab();
            });
          
            $('#nav').find('.active').removeClass('active');
            linkAmb.parent().addClass('active');
            $('#hdIdSubCategoria').val(linkAmb.attr('rel'));
            $('#btnEditAmbiente, #btnDelCategoria, #btnNuevaMesa').removeClass('oculto');
        }
        else {
            $('#nav .is-open').removeClass('is-open').hide(300);
            linkAmb.parent().removeClass('active');
            if ($('#nav li.active').length == 0){
                $('#hdIdSubCategoria').val('0');
                $('#btnEditAmbiente, #btnDelCategoria, #btnNuevaMesa').addClass('oculto');
            }
        }
    }
    function ShowDataByCurrentTab () {
        var currentTab = $('#nav li.active a');
        if (currentTab != null)
            MostrarSubCategorias(currentTab.attr('rel'));
    }
    function EnvioAdminDatos (form) {
        $.ajax({
            type: "POST",
            url: '?pag=<?php echo $pag; ?>&subpag=<?php echo $subpag; ?>',
            cache: false,
            data: $(form).serialize() + "&btnSaveCategoria=btnSaveCategoria",
            success: function(data){
                datos = eval( "(" + data + ")" );
                if (Number(datos.rpta) > 0){
                    MessageBox('<?php $translate->__('Datos guardados'); ?>', '<?php $translate->__('La operaci&oacute;n se complet&oacute; correctamente.'); ?>', "[<?php $translate->__('Aceptar'); ?>]", function () {
                        var TipoData = $('#hdTipoData').val();
                        var Id = $('#hdIdPrimary').val();
                        var NombreCategoria = $('#txtNombre').val();
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
                                aLink.attr('rel', datos.rpta).text(NombreCategoria);
                                section.append(tileArea);
                                li.append(aLink);
                                li.append(section);
                                $('#nav').append(li);
                            }
                            else
                                $('#nav li.active a[rel="' + datos.rpta + '"]').text(NombreCategoria);
                        }
                        else if (TipoData == '01'){
                            if (Id == '0'){
                                $('#nav li.active section .tile-area').html('');
                                
                                tile = $('<div class="tile double dato ribbed-blue" rel="' + datos.rpta + '"></div>');
                                strContent = '<div class="tile-content">';
                                strContent += '<input type="checkbox" name="chkItem[]" class="oculto" value="' + datos.rpta + '"  />';
                                strContent += '<div class="text-center">';
                                strContent += '<h5 class="fg-white" style="margin:30px 0px;">' + datos.nombre + '</h5>';
                                strContent += '</div></div>';
                                tile.html(strContent).appendTo('#nav li.active section .tile-area').on('click', function () {
                                    $('#hdTipoData').val('01');
                                    selectInTile (this);
                                    return false;
                                });
                            }
                            else
                                $('#nav li.active .tile[rel="' + datos.rpta + '"] .text-center h5').text(datos.nombre);
                        }
                        resetForm('form1');
                    });
                }
            }
        });
    }
    function EliminarCategoria () {
        var objCategoria = $('#nav li.active a');
        var IdAmbiente = objCategoria.attr('rel');
        var serializedReturn = $("#form1 input[type!=text]").serialize() + '&hdIdSubCategoria=' + IdAmbiente + '&btnDelCategoria=btnDelCategoria';
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
                            Notif += '<main><p><strong>Error en item con ID: ' + objCategoria.attr('rel') + ' - ' + objCategoria.text() + '</strong>';
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
            $('#btnEditAmbiente, #btnDelCategoria, #btnNuevaMesa').removeClass('oculto');
        $('#btnSaveCategoria, #btnCancelRooms').addClass('oculto')
        $('#pnlForm').fadeOut(500, function () {;
            $('#pnlListado').fadeIn(500, function () {
                $('#txtSearch').focus();
            });
        });
    }
    function GoToEditRoom (TipoData, ModoEdit) {
        $('#hdTipoData').val(TipoData);
        $('#pnlListado').fadeOut(500, function () {
            $('.appbar .metro_button').addClass('oculto');
            $('#btnSaveCategoria, #btnCancelRooms').removeClass('oculto');
            $('#pnlForm').fadeIn(500, function () {
                if (ModoEdit == 'E'){
                    if (TipoData == '00')
                        itemSelected = $('#nav li.active a');
                    else
                        itemSelected = $('.gridview .dato.selected');
                    if (itemSelected.length > 0){
                        var idItem = itemSelected[0].getAttribute('rel');
                        $.ajax({
                            type: "GET",
                            url: "services/categorias/categorias-getdetails.php",
                            cache: false,
                            data: "id=" + idItem,
                            success: function(data){
                                var datos = eval( "(" + data + ")" );
                                $('#hdIdPrimary').val(datos[0].tm_idcategoria);
                                $('#txtCodigo').val(datos[0].tm_codigo);
                                $('#txtNombre').val(datos[0].tm_nombre);
                            }
                        });
                    }
                }
                $('#txtCodigo').focus();
            });
        });
    }
    function addValidFormRegister () {
        $('#txtCodigo').rules('add', {
            required: true,
            maxlength: 4
        });
        $('#txtNombre').rules('add', {
            required: true,
            maxlength: 100
        });
    }
    function removeValidFormRegister () {
        $('#txtCodigo').rules('remove');
        $('#txtNombre').rules('remove');
    }
    function MostrarSubCategorias (idref) {
        $.ajax({
            type: "GET",
            url: "services/categorias/categorias-search.php",
            cache: false,
            data: "idref=" + idref,
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
                        tile = $('<div class="tile double dato ribbed-blue" rel="' + datos[i].id + '"></div>');
                        strContent = '<div class="tile-content">';
                        strContent = '<input type="checkbox" name="chkItem[]" class="oculto" value="' + datos[i].id + '" />';
                        strContent += '<div class="text-center">';
                        strContent += '<h5 class="fg-white" style="margin:30px 0px;">' + datos[i].label + '</h5>';
                        strContent += '</div></div>';
                        tile.html(strContent).appendTo(selector).on('click', function () {
                            $('#hdTipoData').val('01');
                            selectInTile (this);
                            return false;
                        });
                        ++i;
                    }
                }
                else {
                    emptyMessage = '<h2><?php $translate->__('No se encontraron resultados'); ?></h2>';
                    $(selector).html(emptyMessage);
                }
            }
        });
    }
    function GuardarDatos () {
        $('#form1').submit();
    }
</script>