<?php
include('bussiness/almacen.php');
include('bussiness/monedas.php');
$objAlmacen = new clsAlmacen();
$objMoneda = new clsMoneda();
$counterAlmacen = 0;
$counterMoneda = 0;
$IdEmpresa = 1;
$IdCentro = 1;
$selected = '';
$parametros = array('IdEmpresa' => $IdEmpresa, 'IdCentro' => $IdCentro, 'criterio' => '' );
$rsAlmacen = $objAlmacen->Listar('ALM', $parametros);
$countAlmacen = count($rsAlmacen);
$rowMoneda = $objMoneda->Listar('L', '');
$countRowMoneda = count($rowMoneda);
?>
<form id="form1" name="form1" method="post">
    <input type="hidden" id="fnPost" name="fnPost" value="fnPost" />
    <input type="hidden" id="hdPageActual" name="hdPageActual" value="1" />
    <input type="hidden" id="hdPage" name="hdPage" value="1" />
    <input type="hidden" id="hdIdPrimary" name="hdIdPrimary" value="0" />
    <input type="hidden" id="hdFoto" name="hdFoto" value="no-set" />
    <input type="hidden" id="hdComodinBack" name="hdComodinBack" value="no-set" />
    <div class="page-region">
        <div id="pnlListado" class="inner-page">
            <h1 class="title-window">
                <?php $translate->__('Log&iacute;stica'); ?>
            </h1>
            <div class="divContent">
                <div id="pnlLogistica" class="moduloTwoPanel">
                    <div class="colTwoPanel1 column-panel">
                        <h2 class="header-panel"><?php $translate->__('Almacenes'); ?></h2>
                        <div class="body-panel">
                            <div class="scroll-panel">
                                <div id="gvAlmacen" class="tile-area numeric-tile">
                                    <?php
                                    if ($countAlmacen > 0):
                                        for ($counterAlmacen=0; $counterAlmacen < $countAlmacen; $counterAlmacen++):
                                            if ($counterAlmacen == 0)
                                                $selected = ' selected';
                                            else
                                                $selected = '';
                                    ?>
                                    <div data-id="<?php echo $rsAlmacen[$counterAlmacen]['tm_idalmacen']; ?>" class="tile double bg-lime<?php echo $selected; ?>">
                                        <div class="tile-content">
                                            <div class="padding10 ntp">
                                                <h4 class="fg-dark"><?php echo $rsAlmacen[$counterAlmacen]['tm_nombre']; ?></h4>
                                                <p class="fg-dark"><?php echo $rsAlmacen[$counterAlmacen]['tm_direccion']; ?></p>
                                            </div>
                                        </div>
                                        <div class="tile-status bg-dark">
                                            <div class="badge bg-darkCyan">
                                                <?php echo $rsAlmacen[$counterAlmacen]['CountInsumo']; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                        endfor;
                                    endif;
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="colTwoPanel2 column-panel">
                        <h2 class="header-panel"><?php $translate->__('Insumos'); ?></h2>
                        <div class="body-panel">
                            <div class="scroll-panel">
                                <div id="gvInsumos" class="tile-area">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="pnlOrdenesCompra" class="inner-page" style="display:none;">
            <h1 class="title-window">
                <a href="#" title="<?php $translate->__('Volver'); ?>" class="back-button"><i class="icon-arrow-left-3 fg-darker smaller"></i></a>
                <?php $translate->__('&Oacute;rdenes de compra'); ?>
            </h1>
            <div class="divContent">
                <div class="container-details">
                    <div class="scroll-content">
                        <div class="items-area listview gridview">
                            <?php for ($i=0; $i < 10; $i++): ?>
                            <!-- <a href="#" class="list dato bg-white shadow" rel="4">
                                <input name="chkItem[]" type="checkbox" class="oculto" value="4">
                                <div class="list-content">
                                    <div class="item-money-list">
                                    </div>
                                </div>
                            </a> -->
                            <?php endfor; ?>
                            <h2><?php $translate->__('No se encontraron resultados.'); ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="pnlRegistroCompra" class="inner-page" style="display:none;">
            <h1 class="title-window">
                <a id="btnBackToOrdenCompra" href="#" title="Volver"><i class="icon-arrow-left-3 fg-darker smaller"></i></a>
                <?php $translate->__('Registro de orden de compra'); ?>
            </h1>
            <div class="divContent" class="no-overflow">
                <div class="detalleComercio container-details">
                    <div class="headerPedido">
                        <ul>
                            <li class="colProducto"><a href="#"><h4 class="fg-white"><?php $translate->__('Items'); ?></h4></a></li>
                            <li class="colPrecio"><a href="#"><h4 class="fg-white"><?php $translate->__('Precio'); ?></h4></a></li>
                            <li class="colCantidad"><a href="#"><h4 class="fg-white"><?php $translate->__('Cantidad'); ?></h4></a></li>
                            <li class="colSubTotal"><a href="#"><h4 class="fg-white"><?php $translate->__('Importe'); ?></h4></a></li>
                        </ul>
                    </div>
                    <div class="contentPedido">
                        <div class="scroll-content">
                            <table>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="totalbar">
                        <div class="currency">
                            <div class="slide-currency">
                                <?php
                                for ($counterMoneda=0; $counterMoneda < $countRowMoneda; ++$counterMoneda) { 
                                ?>
                                <div title="<?php echo $rowMoneda[$counterMoneda]['tm_nombre']; ?>" rel="<?php echo $rowMoneda[$counterMoneda]['tm_idmoneda']; ?>" class="simbol-currency">
                                    <h1><?php echo $rowMoneda[$counterMoneda]['tm_simbolo']; ?></h1>
                                </div>
                                <?php
                                }
                                ?>
                            </div>
                            <div class="buttons">
                                <button type="button" class="upCurrency" disabled=""><i class="icon-arrow-up-4"></i></button>
                                <button type="button" class="downCurrency"><i class="icon-arrow-down-4"></i></button>
                            </div>
                        </div>
                        <div class="mount">
                            <h1 id="totalDetails">0.00</h1>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        </div>
        <div id="pnlGuiaRemision" class="inner-page" style="display:none;">
            <h1 class="title-window">
                <a href="#" title="Volver" class="back-button"><i class="icon-arrow-left-3 fg-darker smaller"></i></a>
                <?php $translate->__('Gu&iacute;as de remisi&oacute;n'); ?>
            </h1>
            <div class="divContent">
                
            </div>
        </div>
        <div id="pnlRegistroGuia" class="inner-page" style="display:none;">
            <h1 class="title-window">
                <a id="btnBackToGuiaRemision" href="#" title="Volver"><i class="icon-arrow-left-3 fg-darker smaller"></i></a>
                <?php $translate->__('Registro de gu&iacute;as de remisi&oacute;n'); ?>
            </h1>
            <div class="divContent" class="no-overflow">
                <div class="detalleComercio container-details">
                    <div class="headerPedido">
                        <ul>
                            <li class="colProducto"><a href="#"><h4 class="fg-white"><?php $translate->__('Items'); ?></h4></a></li>
                            <li class="colPrecio"><a href="#"><h4 class="fg-white">UM</h4></a></li>
                            <li class="colCantidad"><a href="#"><h4 class="fg-white"><?php $translate->__('Cantidad'); ?></h4></a></li>
                        </ul>
                    </div>
                    <div class="contentPedido">
                        <div class="scroll-content">
                            <table>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="pnlBusquedaItems" class="inner-page with-title-window with-panel-search" style="display:none;">
            <h1 class="title-window">
                <a id="btnBackToRegister" href="#" title="Regresar a mesas"><i class="icon-arrow-left-3 fg-darker smaller"></i></a>
                <a href="#pnlInsumos" data-tipomenu="00" class="link-title-window active"><?php $translate->__('Insumos'); ?></a>
                <a href="#pnlProductos" data-tipomenu="01" class="link-title-window"><?php $translate->__('Productos'); ?></a>
            </h1>
            <div class="panel-search">
                <div class="input-control text" data-role="input-control">
                    <input type="text" id="txtSearchItems" name="txtSearchItems" placeholder="<?php $translate->__('Ingrese criterios de b&uacute;squeda'); ?>">
                    <button id="btnSearchItems" type="button" class="btn-search" tabindex="-1"></button>
                </div>
            </div>
            <div class="divload">
                <div id="gvBusquedaItem">
                    <div class="tile-area gridview"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="appbar">
        <button id="btnAdminAlmacen" name="btnAdminAlmacen" type="button" class="metro_button float-left">
            <span class="content">
                <img src="images/trash.png" alt="<?php $translate->__('Administrar almacenes'); ?>" />
                <span class="text"><?php $translate->__('Administrar almacenes'); ?></span>
            </span>
        </button>
        <button id="btnOrdenCompra" name="btnOrdenCompra" type="button" class="metro_button float-left">
            <span class="content">
                <img src="images/trash.png" alt="<?php $translate->__('&Oacute;rdenes de compra'); ?>" />
                <span class="text"><?php $translate->__('&Oacute;rdenes de compra'); ?></span>
            </span>
        </button>
        <button id="btnGuiaRemision" name="btnGuiaRemision" type="button" class="metro_button float-left">
            <span class="content">
                <img src="images/trash.png" alt="<?php $translate->__('Gu&iacute;as de remisi&oacute;n'); ?>" />
                <span class="text"><?php $translate->__('Gu&iacute;as de remisi&oacute;n'); ?></span>
            </span>
        </button>
        <button id="btnNewOrdenCompra" name="btnNewOrdenCompra" type="button" class="oculto metro_button float-left">
            <span class="content">
                <img src="images/add.png" alt="<?php $translate->__('Nueva orden de compra'); ?>" />
                <span class="text"><?php $translate->__('Nueva orden de compra'); ?></span>
            </span>
        </button>
        <button id="btnNewGuiaRemision" name="btnNewGuiaRemision" type="button" class="oculto metro_button float-left">
            <span class="content">
                <img src="images/add.png" alt="<?php $translate->__('Nueva gu&iacute;a de remisi&oacute;n'); ?>" />
                <span class="text"><?php $translate->__('Nueva gu&iacute;a de remisi&oacute;n'); ?></span>
            </span>
        </button>
        <button id="btnBuscarItems" name="btnBuscarItems" type="button" class="oculto metro_button float-left">
            <span class="content">
                <img src="images/find.png" alt="<?php $translate->__('Buscar items'); ?>" />
                <span class="text"><?php $translate->__('Buscar items'); ?></span>
            </span>
        </button>
        <button id="btnAddItems" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/add-2.png" alt="<?php $translate->__('Agregar a detalle'); ?>" />
                <span class="text"><?php $translate->__('Agregar a detalle'); ?></span>
            </span>
        </button>
        <button id="btnSelectAll" type="button" class="metro_button oculto float-left">
            <span class="content">
                <img src="images/checkall.png" alt="<?php $translate->__('Seleccionar todo'); ?>" />
                <span class="text"><?php $translate->__('Seleccionar todo'); ?></span>
            </span>
        </button>
        <button id="btnLimpiarSeleccion" type="button" class="metro_button oculto float-left">
            <span class="content">
                <img src="images/icon_uncheck.png" alt="<?php $translate->__('Limpiar selecci&oacute;n'); ?>" />
                <span class="text"><?php $translate->__('Limpiar selecci&oacute;n'); ?></span>
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
        <div class="clear"></div>
    </div>
</form>
<?php
include('common/libraries-js.php');
?>
<script>
    $(document).ready(function() {
        listarInsumosAlmacen ();
        $('#btnOrdenCompra, #btnGuiaRemision').on('click', function(event) {
            event.preventDefault();
            $('#btnOrdenCompra, #btnGuiaRemision, #btnAdminAlmacen').addClass('oculto');
            if ($(this).attr('id') == 'btnOrdenCompra')
                mostrarOrdenesCompra();
            else
                mostrarGuiasdeRemision();
        });
        $('#btnNewOrdenCompra, #btnNewGuiaRemision').on('click', function(event) {
            var idbutton = $(this).attr('id');
            event.preventDefault();
            $('#' + idbutton).addClass('oculto');
            $('#btnBuscarItems').removeClass('oculto');
            if (idbutton == 'btnNewOrdenCompra')
                mostrarRegistroOrden(true);
            else
                mostrarRegistroGuia(true);
        });
        $('.back-button').on('click', function(event) {
            event.preventDefault();
            $('#btnOrdenCompra, #btnGuiaRemision, #btnAdminAlmacen').removeClass('oculto');
            mostrarAlmacenInsumos();
        });
        $('#btnBackToOrdenCompra, #btnBackToGuiaRemision').on('click', function(event) {
            var idbutton = $(this).attr('id');
            event.preventDefault();
            $('#btnBuscarItems').addClass('oculto');
            if (idbutton == 'btnBackToOrdenCompra'){
                $('#btnNewOrdenCompra').removeClass('oculto');
                mostrarRegistroOrden(false);
            }
            else {
                $('#btnNewGuiaRemision').removeClass('oculto');
                mostrarRegistroGuia(false);
            }
        });
        $('#gvAlmacen').on('click', '.tile', function(event) {
            event.preventDefault();
            $(this).siblings('.selected').removeClass('selected');
            $(this).addClass('selected');
            listarInsumosAlmacen();
        });
        $('#btnBackToRegister').on('click', function(event) {
            event.preventDefault();
            
        });
        $('#btnBuscarItems').on('click', function(event) {
            var idPanelOut = '';
            event.preventDefault();
            $(this).addClass('oculto');
            $('#btnGuardar, #btnCancelar').addClass('oculto');
            $('#btnSelectAll').removeClass('oculto');
            
            if ($('#pnlRegistroCompra').is(':visible')){
                idPanelOut = '#pnlRegistroCompra';
            }
            else {
                idPanelOut = '#pnlRegistroGuia';
            }
            $(idPanelOut).fadeOut(400, function() {
                $('#pnlBusquedaItems').fadeIn(400, function() {
                    
                });
            });
        });
        $('#btnSelectAll').on('click', function(event) {
            event.preventDefault();
            $(this).addClass('oculto');
            $('#btnLimpiarSeleccion, #btnAddItems').removeClass('oculto');
        });
        $('#btnLimpiarSeleccion').on('click', function(event) {
            event.preventDefault();
            $(this).addClass('oculto');
            $('#btnAddItems').addClass('oculto');
            $('#btnSelectAll').removeClass('oculto');
        });
        $('#btnAddItems').on('click', function(event) {
            event.preventDefault();
            
        });
    });
    function searchItems (pagina) {
        var tipobusqueda = '00';
        var urlservice = '';
        var datasearch = '';
        var selector = '';
        var criterio = '';
        tipobusqueda = $('#pnlBusquedaItems .title-window a.link-title-window.active').attr('data-tipomenu');
        criterio = $('#txtSearchItems').val();
        if (tipobusqueda == '00') {
            urlservice = 'services/insumos-search.php';
            datasearch = 'tipobusqueda=SEARCH&criterio=' + criterio;
        }
        else if (tipobusqueda == '01'){
            urlservice = 'services/products-search.php';
            datasearch = 'tipobusqueda=01&criterio=' + criterio + '&lastId' + pagina;
        }
        $.ajax({
            url: urlservice,
            type: 'GET',
            dataType: 'json',
            data: {param1: 'value1'},
            success: function (data) {
                var count = data.length;
                var i = 0;
                if (count > 0){
                }
            }
        });            
        /*else {
            $.ajax({
                url: 'services/products-search.php',
                type: 'GET',
                dataType: 'json',
                data: {param1: 'value1'},
                success: function (data) {
                    var count = data.length;
                    var i = 0;
                }
            });
        };*/
    }
    function mostrarAlmacenInsumos () {
        var panelOut = '';
        if ($('#pnlOrdenesCompra').is(':visible'))
            panelOut = '#pnlOrdenesCompra';
        else
            panelOut = '#pnlGuiaRemision';
        $('#btnNewOrdenCompra, #btnNewGuiaRemision').addClass('oculto');
        $(panelOut).fadeOut(400, function() {
            $('#pnlListado').fadeIn(400, function() {
                
            });
        });
    }
    function mostrarOrdenesCompra () {
        $('#btnNewOrdenCompra').removeClass('oculto');
        $('#pnlListado').fadeOut(400, function() {
            $('#pnlOrdenesCompra').fadeIn(400, function() {
                
            });
        });
    }
    function mostrarGuiasdeRemision () {
        $('#btnNewGuiaRemision').removeClass('oculto');
        $('#pnlListado').fadeOut(400, function() {
            $('#pnlGuiaRemision').fadeIn(400, function() {
                
            });
        });
    }
    function mostrarRegistroOrden(state){
        if (state){
            $('#btnGuardar, #btnCancelar').removeClass('oculto');
            $('#pnlOrdenesCompra').fadeOut(400, function() {
                $('#pnlRegistroCompra').fadeIn(400, function() {
                    
                });
            });
        }
        else {
            $('#btnGuardar, #btnCancelar').addClass('oculto');
            $('#pnlRegistroCompra').fadeOut(400, function() {
                $('#pnlOrdenesCompra').fadeIn(400, function() {
                    
                });
            });
        }
    }
    function mostrarRegistroGuia(state){
        if (state){
            $('#btnGuardar, #btnCancelar').removeClass('oculto');
            $('#pnlGuiaRemision').fadeOut(400, function() {
                $('#pnlRegistroGuia').fadeIn(400, function() {
                    
                });
            });
        }
        else {
            $('#btnGuardar, #btnCancelar').addClass('oculto');
            $('#pnlRegistroGuia').fadeOut(400, function() {
                $('#pnlGuiaRemision').fadeIn(400, function() {
                    
                });
            });
        }
    }
    function listarInsumosAlmacen () {
        idalmacen = $('#gvAlmacen .tile.selected').attr('data-id');
        precargaExp('#pnlLogistica .colTwoPanel2 .body-panel', true);
        if (idalmacen != null) {
            $.ajax({
                url: 'services/insumos/insumos-search.php',
                type: 'GET',
                cache: false,
                dataType: 'json',
                data: {
                    tipobusqueda:'ALM', 
                    idalmacen: idalmacen
                },
                success: function (data) {
                    var count = data.length;
                    var i = 0;
                    var strhtml = '';
                    if (count > 0) {
                        while(i < count){
                            strhtml += '<div class="tile double ribbed-lime">';
                            strhtml += '<div class="tile-content">';
                            strhtml += '<p class="fg-darker margin10"><strong>' + data[i].tm_nombre + '</strong></p>';
                            strhtml += '</div>';
                            strhtml += '<div class="tile-status bg-dark">';
                            strhtml += '<h3 class=" margin10 fg-white text-right">' + data[i].td_stock + '</h3>';
                            strhtml += '</div>';
                            strhtml += '</div>';
                            ++i;
                        }
                        $('#gvInsumos').html(strhtml);
                    }
                    else
                        $('#gvInsumos').html('<h2><?php $translate->__('No se encontraron resultados.'); ?></h2>');
                    precargaExp('#pnlLogistica .colTwoPanel2 .body-panel', false);
                }
            });
        }
    }
</script>