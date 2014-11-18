<?php
include('bussiness/categoria.php');
include('bussiness/cartadia.php');

$IdEmpresa = 1;
$IdCentro = 1;
$Id = 0;
$Codigo = '';
$Nombre = '';
$IdCategoria = 0;
$IdSubCategoria = 0;

$counterCategoria = 0;
$counterProducto = 0;
$counterValidItems = 0;

$strListItems = '';
$strListDelete = '';
$strListValids = '';


$strQueryAsignacion = '';
$strQueryDetAsignacion = '';
$strQueryUpdateAsignacion = '';

$validItems = false;
$arrayValid = array();
$arrayDelete = array();

$objCategoria = new clsCategoria();
$objData = new clsCartaDia();

$countExistMenu = 0;
$rpta = '0';
$validSQL = false;

if ($_POST){
    if (isset($_POST['btnAsignar'])){
        $hdTipoCarta = (isset($_POST['hdTipoCarta'])) ? $_POST['hdTipoCarta'] : '00';
        $hdFecha = isset($_POST['hdFecha']) ? $_POST['hdFecha'] : date('Y-m-d');
        $strQueryAsignacion = 'INSERT INTO td_producto_menudia (tm_idempresa, tm_idcentro, tm_idproducto, tm_idmoneda, td_fecha, ta_tipomenudia, td_precio, td_stockdia, td_esfavorito, Activo, IdUsuarioReg, FechaReg, IdUsuarioAct, FechaAct) VALUES ';
        $detalleMenu = json_decode(stripslashes($_POST['detalleMenu']));
        foreach ($detalleMenu as $item){
            if ($item->idCartaMenu == '0'){
                $strListItems .= $item->idProducto.', ';
                $strQueryDetAsignacion .= '('.$IdEmpresa.', '.$IdCentro.', '.$item->idProducto.', '.$item->idMoneda.', \''.$item->fechaMenu.'\', \''.$item->tipoMenuDia.'\', '.$item->precio.', '.$item->stock.', 0, 1, '.$idusuario.', NOW(), '.$idusuario.', NOW()),';
            }
            else {
                $strQueryUpdateAsignacion .= 'UPDATE td_producto_menudia SET';
                $strQueryUpdateAsignacion .= ' td_precio = '.$item->precio;
                $strQueryUpdateAsignacion .= ', td_stockdia = '.$item->stock;
                $strQueryUpdateAsignacion .= ', IdUsuarioAct = '.$idusuario;
                $strQueryUpdateAsignacion .= ', FechaAct = NOW()';
                $strQueryUpdateAsignacion .= ' WHERE td_idproducto_menudia = '.$item->idCartaMenu.'; ';
                ++$countExistMenu;
            }
        }

        if ($countExistMenu > 0){
            $validSQL = $objData->ActualizarAsignacion($strQueryUpdateAsignacion);
            if ($validSQL)
                $rpta = 1;
        }

        if (strlen($strQueryDetAsignacion) > 0) {
            if (strlen($strListItems) > 0){
                $strListItems = substr($strListItems, 0, strlen(trim($strListItems)) - 1);
                $objData->MultiDeleteByProd($strListItems, $hdFecha, $hdTipoCarta);
            }

            $strQueryDetAsignacion = substr($strQueryDetAsignacion, 0, strlen(trim($strQueryDetAsignacion)) - 1);
            $strQueryAsignacion .= $strQueryDetAsignacion;
            $validSQL = $objData->RegistrarAsignacion($strQueryAsignacion);
            if ($validSQL)
                $rpta = 1;
        }

        $jsondata = array('rpta' => $rpta);
    }
    elseif (isset($_POST['btnAsignarFavorito'])){
        $chkItem = $_POST['chkItemMenu'];
        $hdEstadoFavorito = $_POST['hdEstadoFavorito'];
        if (isset($chkItem))
            if (is_array($chkItem)) {
                $countCheckItems = count($chkItem);
                $strListItems = implode(',', $chkItem);
                $rpta = $objData->AsignarFavorito($hdEstadoFavorito, $strListItems);
            }
        $jsondata = array('rpta' => $rpta);
    }
    elseif ($_POST['btnEliminar']){
        $chkItem = $_POST['chkItemMenu'];
        if (isset($chkItem))
            if (is_array($chkItem)) {
                $countCheckItems = count($chkItem);
                $strListItems = implode(',', $chkItem);
                $rpta = $objData->MultiDelete($strListItems);
            }
        $jsondata = array('rpta' => $rpta, 'items_valid' => '');
    }
    echo json_encode($jsondata);
    exit(0);
}

$rowCategoria = $objCategoria->Listar('M', $IdEmpresa, $IdCentro, '0');
$countRowCategoria = count($rowCategoria);

$rowSubCategoria = $objCategoria->Listar('REF', $IdEmpresa, $IdCentro);
$countRowSubCategoria = count($rowSubCategoria);
?>
<form id="form1" name="form1" method="post">
    <input type="hidden" id="fnPost" name="fnPost" value="fnPost" />
    <input type="hidden" id="hdPageActual" name="hdPageActual" value="1" />
    <input type="hidden" id="hdPage" name="hdPage" value="1" />
    <input type="hidden" id="hdIdPrimary" name="hdIdPrimary" value="0" />
    <input type="hidden" id="hdFecha" name="hdFecha" value="<?php echo date('Y-m-d'); ?>" />
    <input type="hidden" id="hdTipoCarta" name="hdTipoCarta" value="None" />
    <input type="hidden" id="hdEstadoFavorito" name="hdEstadoFavorito" value="0" />
    <input type="hidden" name="hdCurrentYear" id="hdCurrentYear" value="<?php echo date('Y'); ?>" />
    <input type="hidden" name="hdCurrentMonth" id="hdCurrentMonth" value="<?php echo date('m'); ?>" />
    <div class="page-region">
        <div id="pnlListado" class="inner-page">
            <h1 class="title-window">
                <?php $translate->__('Cartas y men&uacute;s'); ?>
            </h1>
            <div class="divContent">
                <!-- Responsive calendar - START -->
                <div class="responsive-calendar">
                    <div class="controls">
                        <a class="float-left" data-go="prev"><h1><i class="icon-arrow-left-3 fg-darker smaller"></i></h1></a>
                        <h2><span data-head-year></span> <span data-head-month></span></h2>
                        <a class="float-right" data-go="next"><h1><i class="icon-arrow-right-3 fg-darker smaller"></i></h1></a>
                        <div class="clear"></div>
                    </div>
                    <div class="day-headers">
                        <div class="day header fg-white"><?php $translate->__('Lun'); ?></div>
                        <div class="day header fg-white"><?php $translate->__('Mar'); ?></div>
                        <div class="day header fg-white"><?php $translate->__('Mie'); ?></div>
                        <div class="day header fg-white"><?php $translate->__('Jue'); ?></div>
                        <div class="day header fg-white"><?php $translate->__('Vie'); ?></div>
                        <div class="day header fg-white"><?php $translate->__('Sab'); ?></div>
                        <div class="day header fg-white"><?php $translate->__('Dom'); ?></div>
                    </div>
                    <div class="days-container">
                        <div class="days" data-group="days"></div>
                    </div>
                </div>
                <!-- Responsive calendar - END -->
            </div>
        </div>
        <div id="pnlMenuToday" style="display:none;">
            <h1 class="title-window">
                <a id="btnBackList" href="#" title="<?php $translate->__('Regresar a calendario'); ?>" class="back-button"><i class="icon-arrow-left-3 fg-darker smaller"></i></a>
                <a href="#pnlArticulos" data-tipomenu="None" class="link-title-window active"><?php $translate->__('Art&iacute;culos'); ?></a>
                <a href="#pnlCarta" data-tipomenu="00" class="link-title-window"><?php $translate->__('Carta'); ?></a>
                <a href="#pnlMenu" data-tipomenu="01" class="link-title-window"><?php $translate->__('Men&uacute;'); ?></a>
                <a id="lnkShowFavs" class="toggle-icon" href="#" title="<?php $translate->__('Mostrar favoritos'); ?>"><i class="icon-star-3 smaller"></i></a>
            </h1>
            <div id="pnlArticulos" class="inner-page with-panel-search with-filtro">
                <div class="inner-page-content">
                    <div class="panel-search">
                        <table class="tabla-normal">
                            <tr>
                                <td>
                                    <div class="input-control text" data-role="input-control">
                                        <input id="txtSearch" name="txtSearch" type="text" placeholder="<?php $translate->__('Ingrese criterios de b&uacute;squeda'); ?>">
                                        <button class="btn-clear" tabindex="-1" type="button"></button>
                                    </div>
                                </td>
                                <td style="width:95px;">
                                    <button id="btnSearch" type="button" title="<?php $translate->__('Buscar'); ?>"><i class="icon-search"></i></button>
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
                    <div id="precargaProd" class="divload">
                        <div id="gvProductos" class="scroll-content">
                            <div class="tile-area gridview"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="pnlCarta" class="inner-page" style="display:none;">
                <div class="divload">
                    <div class="menu-articulos">
                        <?php 
                        if ($countRowCategoria > 0){
                            for ($counterCategoria=0; $counterCategoria < $countRowCategoria; $counterCategoria++) {
                        ?>
                        <h3><?php echo $rowCategoria[$counterCategoria]['tm_nombre']; ?></h3>
                        <?php
                                for($counterSubCat=0; $counterSubCat < $countRowSubCategoria; $counterSubCat++){
                                    if ($rowCategoria[$counterCategoria]['tm_idcategoria'] == $rowSubCategoria[$counterSubCat]['tm_idrefcategoria']){
                        ?>
                        <div class="section-subcat" rel="<?php echo $rowSubCategoria[$counterSubCat]['tm_idcategoria']; ?>">
                            <h4><?php echo $rowSubCategoria[$counterSubCat]['tm_nombre']; ?></h4>
                            <div class="tile-area gridview">
                            </div>
                        </div>
                        <?php
                                    }
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div id="pnlMenu" class="inner-page" style="display:none;">
                <div class="divload">
                    <div id="gvMenu">
                        <div class="tile-area gridview"></div>
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
        <button id="btnGuardarCambios" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/save.png" alt="<?php $translate->__('Guardar cambios'); ?>" />
                <span class="text"><?php $translate->__('Guardar cambios'); ?></span>
            </span>
        </button>
        <button id="btnAsignarFavorito" name="btnAsignarFavorito" type="button" class="metro_button oculto float-left">
            <span class="content">
                <img src="images/fav.png" alt="<?php $translate->__('Marcar como favoritos'); ?>" />
                <span class="text"><?php $translate->__('Marcar como favoritos'); ?></span>
            </span>
        </button>
        <button id="btnQuitarFavorito" name="btnQuitarFavorito" type="button" class="metro_button oculto float-left">
            <span class="content">
                <img src="images/favs-remove.png" alt="<?php $translate->__('Quitar de favoritos'); ?>" />
                <span class="text"><?php $translate->__('Quitar de favoritos'); ?></span>
            </span>
        </button>
        <button id="btnAsignarCarta" name="btnAsignarCarta" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/plato-carta.png" alt="<?php $translate->__('Asignar a carta'); ?>" />
                <span class="text"><?php $translate->__('Asignar a carta'); ?></span>
            </span>
        </button>
        <button id="btnAsignarMenu" name="btnAsignarMenu" type="button" class="metro_button oculto float-right">
            <span class="content">
                <img src="images/plato-menu.png" alt="<?php $translate->__('Asignar a men&uacute;'); ?>" />
                <span class="text"><?php $translate->__('Asignar a men&uacute;'); ?></span>
            </span>
        </button>
        <button id="btnSelectYearMonth" type="button" class="metro_button float-right">
            <span class="content">
                <img src="images/calendar.png" alt="<?php $translate->__('Seleccionar mes y a&ntilde;o'); ?>" />
                <span class="text"><?php $translate->__('Seleccionar mes y a&ntilde;o'); ?></span>
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
<script src="scripts/responsive-calendar.js"></script>
<script>
    var TipoBusqueda = '03';

    $(function () {
        $(".responsive-calendar").responsiveCalendar({
            time: '<?php echo date('Y-m') ?>',
            onDayClick: function(events) {
                var fecha = $(this).data('year')+'-'+addLeadingZero( $(this).data('month') )+'-'+addLeadingZero( $(this).data('day') );
                $('#hdFecha').val(fecha);
                GoToArticles();
            },
            onInit: function () {
                setTimeout(function () {
                    var anho = $('#hdCurrentYear').val();
                    var mes = Number($('#hdCurrentMonth').val());
                     ListarDiasAsignados(anho, mes);
                }, 1000);
            },
            onMonthChange: function () {
                setTimeout(function () {
                    var firstDay = $(".responsive-calendar .days .day:not(.not-current)").first().children('a');
                    var anho = firstDay.attr('data-year');
                    var mes = firstDay.attr('data-month');
                    ListarDiasAsignados(anho, mes);
                }, 1000);
            }
        });

        $('#ddlCategoria').focus().on('change', function () {
            idreferencia = $(this).val();
            habilitarControl('#ddlSubCategoria', false);
            $('#ddlSubCategoria').find('option').remove();
            $('#ddlSubCategoria').append('<option value="0"><?php $translate->__('TODOS'); ?></option>');
            LoadSubCategorias(idreferencia, '#ddlSubCategoria');
            LoadProductos(TipoBusqueda, $(this).val(), '0', '', '1');
        });

        $('#ddlSubCategoria').on('change', function () {
            LoadProductos(TipoBusqueda, $('#ddlCategoria').val(), $(this).val(), '', '1');
        });

        $('#btnBackList').on('click', function () {
            clearOnlyListSelection();
            BackToList();
            return false;
        });

        $('#txtSearch').on('keydown', function(event) {
            if (event.keyCode == $.ui.keyCode.ENTER) {
                BuscarProductos('1');
                return false;
            }
        }).on('keypress', function(event) {
            if (event.keyCode == $.ui.keyCode.ENTER)
                return false;
        });

        $('#btnSearch').on('click', function (e) {
            BuscarProductos('1');
            return false;
        });

        $('.title-window').on('click', 'a.link-title-window', function() {
            var page = $(this).attr("href");
            var TipoCarta = $(this).attr('data-tipomenu');

            $('#hdTipoCarta').val(TipoCarta);

            $('#lnkShowFavs').removeClass('active');

            if (TipoCarta == 'None'){
                $('#lnkShowFavs').hide();
                $('#btnAsignarFavorito, #btnQuitarFavorito').addClass('oculto');
            }
            else
                $('#lnkShowFavs').show();

            clearView(TipoCarta);
            clearSelection();
            clearAsignacion();

            $(this).siblings().removeClass('active');
            $(this).addClass('active');

            $('#pnlMenuToday .inner-page').hide();
            panelx = $('#pnlMenuToday ' + page);
            panelx.show(0, function () {
                if (TipoCarta != 'None')
                    listarMenuDia(TipoCarta, $('#hdFecha').val());
            });
            
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

        $('#btnAsignarFavorito').on('click', function () {
            $('#hdEstadoFavorito').val('1');
            AsignarFavorito('1');
            return false;
        });

        $('#btnQuitarFavorito').on('click', function () {
            $('#hdEstadoFavorito').val('0');
            AsignarFavorito('0');
            return false;
        });
        
        $('#btnAsignarCarta').on('click', function () {
            AsignarSeleccion('N', '00');
            return false;
        });

        $('#btnAsignarMenu').on('click', function () {
            AsignarSeleccion('N', '01');
            return false;
        });

        $('#btnGuardarCambios').on('click', function () {
            AsignarSeleccion('M', $('#hdTipoCarta').val());
            return false;
        });

        $('#btnEliminar').on('click', function () {
            EliminarDatos();
            return false;
        });

        $("#btnLimpiarSeleccion").on('click', function(){
            var TipoCarta = '';
            TipoCarta = $('#hdTipoCarta').val();
            
            $('#btnLimpiarSeleccion').addClass('oculto');
            $('#btnSelectAll').removeClass('oculto');
            $('.inner-page:visible .tile.selected').removeClass('selected');
            $('.inner-page:visible .tile .input_spinner').hide();

            if (TipoCarta == 'None')
                $('#btnAsignarMenu, #btnAsignarCarta').addClass('oculto');
            else
                $('#btnGuardarCambios, #btnAsignarFavorito, #btnQuitarFavorito, #btnEliminar').addClass('oculto');
            return false;
        });

        $('#btnSelectAll').on('click', function () {
            selectAll();
            return false;
        });

        $('#lnkShowFavs').on('click', function () {
            var TipoCarta = '';
            TipoCarta = $('#hdTipoCarta').val();

            if ($(this).hasClass('active'))
                $(this).removeClass('active').attr('title', 'Mostrar favoritos');
            else
                $(this).addClass('active').attr('title', 'Mostrar todo');;

            clearView(TipoCarta);
            listarMenuDia(TipoCarta, $('#hdFecha').val());
            return false;
        });
    });

    function ListarDiasAsignados (anho, mes) {
        $.ajax({
            url: 'services/cartadia/cartadia-dias.php',
            type: 'GET',
            dataType: 'json',
            data: {
                tipobusqueda: 'DIAS',
                anho: anho,
                mes: mes
            },
            success: function  (data) {
                var count = data.length;
                var i = 0;
                if (count > 0) {
                    while(i < count){
                        $('.day:not(.not-current) a[data-day="' + data[i].dia + '"][data-month="' + data[i].mes + '"][data-year="' + data[i].anho + '"]').parent().addClass('active');
                        ++i;
                    }
                };
            }
        });
    }

    function clearView (TipoCarta) {
        var selector = '';
        if (TipoCarta == '00')
            selector = '.section-subcat .tile-area';
        else if (TipoCarta == '01')
            selector = '#pnlMenu .tile-area';
        $(selector).html('');
    }

    function UpdateData (tipoMenuDia, idProducto, precio) {
        var count = listaDetalle.length;
        if (count > 0){
            for (var i = 0; i < count; i++){
                if (listaDetalle[i].idProducto == idProducto && listaDetalle[i].tipoMenuDia == tipoMenuDia){ 
                    listaDetalle[i].precio = precio;
                    break;
                }
            }
        }
    }

    function selectAll () {
        var TipoCarta = '';
        var selector = '';

        TipoCarta = $('#hdTipoCarta').val();

        $('#btnSelectAll').addClass('oculto');
        $('#btnLimpiarSeleccion').removeClass('oculto');
        
        if (TipoCarta == 'None'){
            selector = '#pnlArticulos';
            $('#btnAsignarMenu, #btnAsignarCarta').removeClass('oculto');
            $('#btnEliminar').addClass('oculto');
        }
        else {
            if (TipoCarta == '00')
                selector = '#pnlCarta';
            else if (TipoCarta == '01')
                selector = '#pnlMenu';
            
            $('#btnEliminar, #btnGuardarCambios, #btnAsignarFavorito, #btnQuitarFavorito').removeClass('oculto');
        }
        $(selector + ':visible .tile').addClass('selected');
        $(selector + ':visible .tile input:checkbox').attr('checked', '');
        $(selector + ':visible .tile .input_spinner').show();

    }

    function GoToArticles () {
        $('#pnlListado').fadeOut(500, function () {
            $('#btnSelectYearMonth').addClass('oculto');
            $('#btnSelectAll').removeClass('oculto');
            $('#pnlMenuToday').fadeIn(500, function () {
                if ($("#gvProductos .gridview .tile").length == 0)
                    BuscarProductos('1');
            });
        });
    }

    function BackToList () {
        $('#pnlMenuToday').fadeOut(500, function () {
            $('#btnSelectYearMonth').removeClass('oculto');
            $('#btnSelectAll').addClass('oculto');
            $('#pnlListado').fadeIn(500, function () {
                setTimeout(function () {
                    var firstDay = $(".responsive-calendar .days .day:not(.not-current)").first().children('a');
                    var anho = firstDay.attr('data-year');
                    var mes = firstDay.attr('data-month');
                    ListarDiasAsignados(anho, mes);
                }, 1000);
                $('#txtSearch').focus();
            });
        });
    }

    var listaDetalle = [];

    function DetalleMenuDia (idCartaMenu, idProducto,  idMoneda, idCategoria, idSubCategoria, fechaMenu, tipoMenuDia, precio, stock) {
        this.idCartaMenu = idCartaMenu;
        this.idProducto = idProducto;
        this.idMoneda = idMoneda;
        this.fechaMenu = fechaMenu;
        this.tipoMenuDia = tipoMenuDia;
        this.precio = precio;
        this.stock = stock;
        this.idCategoria = idCategoria;
        this.idSubCategoria = idSubCategoria;
    }

    function clearAsignacion () {
        listaDetalle = [];
    }
    
    function AsignarSeleccion (TipoEdit, TipoCarta) {
        var selector = '';

        if (TipoEdit == 'N')
            selector = '#pnlArticulos';
        else {
            if (TipoCarta == '00')
                selector = '#pnlCarta';
            else if (TipoCarta == '01')
                selector = '#pnlMenu';
        }
            
        var articulos = $(selector + ' .tile.selected');
        var count = articulos.length;
        var i = 0;
        var idCartaMenu = '0';
        var idProducto = '0';
        var idMoneda = '0';
        var idCategoria = '0';
        var idSubCategoria = '0';
        var fechaMenu = $('#hdFecha').val();
        var precio = 0;
        var stock = 0;
        var detalleMenu = '';

        clearAsignacion();
        if (count > 0){
            while (i < count){
                if (TipoEdit == 'N'){
                    idCartaMenu = '0';
                    idProducto = articulos[i].getAttribute('rel');
                    idMoneda = articulos[i].getAttribute('data-idMoneda');
                    idCategoria = articulos[i].getAttribute('data-idCategoria');
                    idSubCategoria = articulos[i].getAttribute('data-idSubCategoria');
                    precio = Number($(articulos[i]).find('span.precio').text());
                    stock = Number($(articulos[i]).find('input.inputCantidad').val());
                }
                else {
                    idCartaMenu = articulos[i].getAttribute('rel');
                    idProducto = articulos[i].getAttribute('data-idProducto');
                    idMoneda = articulos[i].getAttribute('data-idMoneda');
                    idCategoria = articulos[i].getAttribute('data-idCategoria');
                    idSubCategoria = articulos[i].getAttribute('data-idSubCategoria');
                    precio = Number($(articulos[i]).find('input:text').val());
                    stock = Number(articulos[i].getAttribute('data-stock'));
                }

                var detalle = new DetalleMenuDia (idCartaMenu, idProducto,  idMoneda, idCategoria, idSubCategoria, fechaMenu, TipoCarta, precio.toFixed(2), stock);
                listaDetalle.push(detalle);
                ++i;
            }

            detalleMenu = JSON.stringify(listaDetalle);

            $.ajax({
                type: "POST",
                url: '?pag=<?php echo $pag; ?>&subpag=<?php echo $subpag; ?>',
                cache: false,
                data: {
                    fnPost: 'fnPost',
                    hdTipoCarta: TipoCarta,
                    hdFecha: fechaMenu,
                    btnAsignar: 'btnAsignar',
                    detalleMenu: detalleMenu
                },
                success: function(data){
                    datos = eval( "(" + data + ")" );
                    if (Number(datos.rpta) > 0){
                        MessageBox('<?php $translate->__('Datos guardados'); ?>', '<?php $translate->__('La operaci&oacute;n se complet&oacute; correctamente.'); ?>', "[<?php $translate->__('Aceptar'); ?>]", function () {
                            $('#hdPage').val('1');
                            $('#hdPageActual').val('1');
                            clearOnlyListSelection();
                            clearView(TipoCarta);
                            listarMenuDia(TipoCarta, fechaMenu);
                            $('#btnSelectAll').removeClass('oculto');
                        });
                    }
                }
            });
        }
    }

    function listarMenuDia (TipoCarta, fechaMenu) {
        var selector = '';
        var capaLoading = '';
        var esfavorito = '';

        if (TipoCarta == '00')
            capaLoading = '#pnlCarta';
        else if (TipoCarta == '01')
            capaLoading = '#pnlMenu';

        esfavorito = ($('#lnkShowFavs').hasClass('active') ? '1' : '');

        precargaExp(capaLoading, true);
        $.ajax({
            type: "GET",
            url: 'services/cartadia/cartadia-search.php',
            cache: false,
            data: {
                tipodata: TipoCarta,
                fecha: fechaMenu,
                esfavorito: esfavorito
            },
            success: function(data){
                var datos = eval( "(" + data + ")" );
                var count = datos.length;
                var i = 0;
                var idCartaMenu = '0';
                var idProducto = '0';
                var idMoneda = '0';
                var idCategoria = '0';
                var idSubCategoria = '0';
                var nombreProducto = '';
                var simboloProducto = '';
                var fechaMenu = $('#hdFecha').val();
                var precio = 0;
                var stock = 0;
                var esfavorito = '0';
                var capaLoading = '';

                if (count > 0){
                    while (i < count){
                        idCartaMenu = datos[i].td_idproducto_menudia;
                        idProducto = datos[i].tm_idproducto;
                        idMoneda = datos[i].tm_idmoneda;
                        idCategoria = datos[i].tm_idcategoria;
                        idSubCategoria = datos[i].tm_idsubcategoria;
                        nombreProducto = datos[i].nombreProducto;
                        simboloProducto = datos[i].simboloProducto;
                        precio = Number(datos[i].td_precio);
                        stock = Number(datos[i].td_stockdia);
                        foto = datos[i].tm_foto;
                        esfavorito = datos[i].td_esfavorito;

                        $tile = $('<div class="tile dato double"></div>');
                        $trueContent = $('<div class="tile_true_content"></div>');
                        $content = $('<div class="tile-content"></div>');
                        $img = $('<img />');
                        $status = $('<div class="tile-status bg-dark opacity"></div>');
                        $label = $('<span class="label"></span>');
                        $badge = $('<span class="badge bg-darkRed"></span>');
                        $checkbox = $('<input name="chkItemMenu[]" type="checkbox" class="oculto" />');
                        $flagFavorito = $('<div class="flag_tipocarta"></div>');
                                
                        if (esfavorito == '1'){

                            $flagFavorito.html('<i class="icon-star-3"></i>').addClass('bd_color-favorito');
                            $tile.append($flagFavorito);
                        }
                        
                        $tile.attr('rel', idCartaMenu);
                        $tile.attr('data-idProducto', idProducto);
                        $tile.attr('data-idMoneda', idMoneda);
                        $tile.attr('data-idCategoria', idCategoria);
                        $tile.attr('data-idSubCategoria', idSubCategoria);
                        $tile.attr('data-stock', stock);

                        $checkbox.val(idCartaMenu);

                        if (datos[i].tm_foto == 'no-set'){
                            $img.attr('src', 'images/food-48.png');
                            $tile.addClass('bg-olive');
                            $content.addClass('icon');
                        }
                        else {
                            $img.attr('src', foto);
                            $content.addClass('image');
                        }
                        $label.text(nombreProducto);
                        
                        $badge.text(stock);

                        $content.append($img);
                        $status.append($label);
                        $status.append($badge);

                        $trueContent.append($content);
                        $trueContent.append($status);

                        inputSpinner = $('<div class="input_spinner"></div>');
                        txtPrecio = $('<input type="text" name="txtPrecioInTile" class="inputPrecio" />').val(precio.toFixed(2));
                        
                        inputSpinnerButtons = $("<div class=\"buttons\"></div>");
                        iSButtonUp = $("<button type=\"button\" class=\"up bg-green fg-white\"></button>");
                        iSButtonUp.html('+');
                        iSButtonDown = $("<button type=\"button\" class=\"down bg-red fg-white\"></button>");
                        iSButtonDown.html('-');

                        txtPrecio.appendTo(inputSpinner).numeric(".").on('blur', function () {
                            if ($(this).val().trim().length == 0)
                                $(this).val('0.00');
                            else
                                $(this).val(Number($(this).val()).toFixed(2));
                        });

                        iSButtonUp.appendTo(inputSpinnerButtons).on('click', function () {
                            var inputSpinText = $(this).parent().parent().find('input');
                            var idProducto = $(this).parent().parent().parent().attr('data-idProducto');
                            var precio = Number(inputSpinText.val());
                            
                            if (precio < 999.99){
                                precio = precio + 1;
                                inputSpinText.val(precio);
                            }
                            return false;
                        });

                        iSButtonDown.appendTo(inputSpinnerButtons).on('click', function () {
                            var inputSpinText = $(this).parent().parent().find('input');
                            var idProducto = $(this).parent().parent().parent().attr('data-idProducto');
                            var precio = Number(inputSpinText.val());

                            if (precio > 0.01){
                                precio = precio - 1;
                                inputSpinText.val(precio);
                            }
                            return false;
                        });

                        inputSpinner.append(inputSpinnerButtons);

                        $tile.append($checkbox);
                        $trueContent.appendTo($tile).on('click', function () {
                            var TipoCarta = '';
                            var _inputSpinner = $(this).parent().find('.input_spinner');
                            var _tile = $(this).parent();
                            var _selectorButtons = '#btnLimpiarSeleccion, #btnGuardarCambios, #btnAsignarFavorito, #btnQuitarFavorito';

                            TipoCarta = $('#hdTipoCarta').val();

                            if (_tile.hasClass('selected')){
                                _tile.find('input:checkbox')[0].checked = false;
                                _tile.removeClass('selected');
                                if (_tile.siblings('.selected').length > 0){
                                    $(_selectorButtons).removeClass('oculto');
                                    if (TipoCarta != 'None')
                                        $(' #btnEliminar').removeClass('oculto');
                                }
                                else {
                                    $(_selectorButtons).addClass('oculto');
                                    if (TipoCarta != 'None')
                                        $(' #btnEliminar').addClass('oculto');
                                }
                                _inputSpinner.hide();
                            }
                            else {
                                _tile.find('input:checkbox')[0].checked = true;
                                _tile.addClass('selected');
                                $(_selectorButtons).removeClass('oculto');
                                if (TipoCarta != 'None')
                                    $(' #btnEliminar').removeClass('oculto');
                                _inputSpinner.show();
                            }
                            return false;
                        });

                        $tile.append(inputSpinner);

                        if (TipoCarta == '00')
                            selector = '#pnlCarta .section-subcat[rel="' + idSubCategoria + '"] .tile-area';
                        else if (TipoCarta == '01')
                            selector = '#pnlMenu .tile-area';

                        $tile.appendTo(selector);
                        ++i;
                    }
                }

                if (TipoCarta == '00')
                    capaLoading = '#pnlCarta';
                else if (TipoCarta == '01')
                    capaLoading = '#pnlMenu';

                precargaExp(capaLoading, false);
            }
        });
    }

    function AsignarFavorito (EstadoFavorito) {
        var serializedReturn = $("#form1 input[type!=text]").serialize() + '&btnAsignarFavorito=btnAsignarFavorito';
        $.ajax({
            type: "POST",
            url: '?pag=<?php echo $pag; ?>&subpag=<?php echo $subpag; ?>',
            cache: false,
            data: serializedReturn,
            success: function(data){
                datos = eval( "(" + data + ")" );
                if (Number(datos.rpta) > 0){
                    MessageBox('<?php $translate->__('Datos guardados'); ?>', '<?php $translate->__('La operaci&oacute;n se complet&oacute; correctamente.'); ?>', "[<?php $translate->__('Aceptar'); ?>]", function () {
                        var TipoCarta = '';
                        TipoCarta = $('#hdTipoCarta').val();
                        $('#hdEstadoFavorito').val('0');
                        clearView(TipoCarta);
                        listarMenuDia(TipoCarta, $('#hdFecha').val());
                        $('#btnSelectAll').removeClass('oculto');
                        $('#btnAsignarFavorito, #btnGuardarCambios, #btnEliminar, #btnLimpiarSeleccion, #btnQuitarFavorito').addClass('oculto');
                    });
                }
            }
        });
    }

    function BuscarProductos (pagina) {
        LoadProductos(TipoBusqueda, $('#ddlCategoria').val(), $('#ddlSubCategoria').val(), $('#txtSearch').val(), pagina);
    }
</script>