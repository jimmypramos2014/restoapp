<?php
include("bussiness/menu.php");
$objData = new clsMenu();

if(isset($_GET['lang']))
    $translate = new Translator($_GET['lang']);
else
    $translate = new Translator('es');

$rowMenu = $objData->ListMenuPerfil('00', '0', $idperfil);
$countMenu = count($rowMenu);
$counterMenu = 0;
?>
<!-- <header class="header">
    <div class="title">
        <img class="active-app" src="images/logo.png" />
        <h2 class="fg-white"><a href="logout.php">RESTORA</a></h2>
    </div>
    <div class="options">

    </div>
    <div class="user">
        <img class="foto" src="images/user-nosetimg-48.jpg" />
        <div class="info-user">
            <h4 class="fg-white"></h4>
            <p class="fg-white"><?php echo $login; ?></p>
        </div>
        <a class="more-options" href="#"><img src="images/Configuration.png" /></a>
    </div>
</header> -->

<div class="app-main">
    <h1 class="app-header">Inicio</h1>
    <div class="app-body">
        <a href="#" class="control-app"></a>
        <div class="modern-wrappanel">
            <div class="tile-area">
                <div class="tile-group seven">
        <?php
        $IdMenu = '0';
        $Titulo = '';
        $Cabecera = '';
        $Descripcion = '';
        $Icono = '';
        $URLMenu = '';
        $CssDouble = '';

        for ($counterMenu=0; $counterMenu < $countMenu; $counterMenu++) {
            $IdMenu = $rowMenu[$counterMenu]['tm_idmenu'];
            $Titulo = $rowMenu[$counterMenu]['tm_titulo'];
            $TipoIcono = $rowMenu[$counterMenu]['ta_tipoicon'];
            $Icono = $rowMenu[$counterMenu]['tm_iconuri'];
            $BgColorTile = $rowMenu[$counterMenu]['tm_iconbgcolor'];
            $URLMenu = $rowMenu[$counterMenu]['tm_uri'];

            if ($counterMenu % 2 == 0)
                $CssDouble = ' double';
            else
                $CssDouble = '';
        ?>
                    <div id="tile<?php echo $IdMenu; ?>" data-id="<?php echo $IdMenu; ?>" class="tile<?php echo $CssDouble; ?> <?php echo $BgColorTile; ?>" rel="<?php echo $URLMenu; ?>">
                        <div class="tile-content icon">
                            <?php
                            if ($TipoIcono == '00'){
                            ?>
                            <img src="<?php echo $Icono; ?>" />
                            <?php
                            }
                            else {
                            ?>
                            <span class="<?php echo $Icono; ?>"></span>
                            <?php
                            }
                            ?>
                        </div>
                        <div class="brand">
                            <div class="label"><?php echo $Titulo; ?></div>
                        </div>
                    </div>
        <?php
        }
        ?>
                </div>
            </div>
        </div>
    </div>
    <div class="list-sites">
        <a href="#" class="control-app"></a>
    </div>
</div>
<div id="charmOptions" class="control-center">
    <div class="container-control">
        <h3 class="header-control fg-white">RESTORA</h3>
        <div class="body-control">
            <div class="container-body">
                <div class="section-user">
                    <div class="container-user">
                        <div class="info-user">
                            <img class="photo-user" src="media/images/cute-short-haircuts-for-girls-under-12.jpg" />
                            <h5 class="name-user"><?php echo $login; ?></h5>
                            <h6 class="permission-user"><?php $translate->__('Administrador'); ?></h6>
                        </div>
                    </div>
                </div>
                <div class="section-activewin">
                    <div class="list-activewin">
                        <div class="view"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-control">
            <div class="appbar">
                <a href="#" id="lnkRecentTasks" class="cc-tab float-left active" title="<?php $translate->__('Tareas recientes'); ?>"><h4 class="no-margin"><i class="icon-layers"></i></h4></a>
                <a href="#" id="lnkNotifications" class="cc-tab float-left" title="<?php $translate->__('Notificaciones'); ?>"><h4 class="no-margin"><i class="icon-newspaper"></i></h4></a>
                <a href="logout.php" id="lnkLogout" class="float-right" title="<?php $translate->__('Cerrar sesi&oacute;n'); ?>"><h4 class="no-margin"><i class="icon-exit"></i></h4></a>
                <a href="#" id="lnkShowDesktop" class="float-right" title="<?php $translate->__('Mostrar escritorio'); ?>"><h4 class="no-margin"><i class="icon-grid-view"></i></h4></a>
            </div>
        </div>
    </div>
</div>
<span id="spnIdPerfil" class="oculto"><?php echo $idperfil; ?></span>
<div id="info" style="display: none;" class="modal-example-content">
    <div class="modal-example-header">
        <a class="close" href="#" onclick="$.fn.custombox('close');">&times;</a>
        <h4></h4>
    </div>
    <div class="modal-example-body"></div>
</div>
<div id="help" style="display: none;" class="modal-example-content">
    <div class="modal-example-header">
        <a class="close" href="#" onclick="$.fn.custombox('close');">&times;</a>
        <h4></h4>
    </div>
    <div class="modal-example-body"></div>
</div>
<div id="divpedido" style="display: none;" class="modal-example-content">
    <div class="modal-example-header">
        <a class="close" href="#" onclick="$.fn.custombox('close');">&times;</a>
        <h4></h4>
    </div>
    <div class="modal-example-body"></div>
</div>
<div id="formats" style="display: none;" class="modal-example-content">
    <div class="modal-example-header">
        <a class="close" href="#" onclick="$.fn.custombox('close');">&times;</a>
        <h4></h4>
    </div>
    <div class="modal-example-body">
        
    </div>
</div>
<?php
include('common/libraries-js.php');
?>
<script src="scripts/start-screen.js"></script>
<script>
    $(function () {
        $(".modern-wrappanel").on('click', '.tile', function(){
            createThumbWin(this);
            navigateInFrame("00", this);
            return false;
        });

        $('#btnConfigSistema').on('click', function () {
            createThumbWin(this);
            navigateInFrame("00", this);
        });
        
        $(".active-app").on('click', function () {
            if ($(".list-activewin .activewin").length > 0)
                showOrHideListActive(true);
            return false;
        });

        $('body').on('click', 'a.control-app', function () {
            //showOrHideListActive(false);
            showOrHideCharmOptions(true);
            return false;
        });

        /*$("html").click(function () { 
            hideAllSlidePanels();
        });*/

        $('.appbar').on('click', 'a.cc-tab', function(event) {
            event.preventDefault();
            $(this).siblings('.active').removeClass('active');
            $(this).addClass('active');
        });

        $('#lnkShowDesktop').on('click', function () {
            hideFrame();
            hideAllSlidePanels();
            loadConfigMenu("01");
            return false;
        });
        loadConfigMenu("01");
    });

    var outSideListActiveWin = 1,
        outSideCharmOptions = 1;

    function hideAllSlidePanels () {
        /*if ($(".list-activewin .activewin").length > 0)
            showOrHideListActive(false);*/
        showOrHideCharmOptions(false);
    }

    function showOrHideCharmOptions(state) {
        if (state == false) {
            if ($("#charmOptions").is(':visible'))
                $("#charmOptions").hide('slide', {'direction':'right'}, 300);
        }
        else {
            if (!$("#charmOptions").is(':visible')){
                $("#charmOptions").show('slide', {'direction':'right'}, 300);
            }
        }
    }

    function showOrHideListActive(state) {
        if (state == false) {
            if ($(".list-activewin").is(':visible'))
                $(".list-activewin").hide('slide', {}, 500);
        }
        else {
            if (!$(".list-activewin").is(':visible'))
                $(".list-activewin").show('slide', {}, 500);
        }
    }
    
    function hideFrame() {
        $(".modern-wrappanel").show();
        $(".list-sites").hide();
        $(".list-sites .panelWin").removeClass("active").hide();
        $(".list-activewin .activewin").removeClass("active");
    }
    
    function navigateInFrame(type, obj) {
        var idPanel = "";
        var page = $(obj).attr("rel");
        var dataId = $(obj).attr("data-id");
        
        if (type == "00")
            idPanel = "pnl" + $(obj).attr("id");
        else
            idPanel = $(".list-sites .panelWin[rel='" + page + "']").attr("id");
        
        hideFrame();
        
        $(".modern-wrappanel").hide();
        $(".list-sites").show();
        
        if ($(".list-sites div.panelWin[id='" + idPanel + "']").length == 0){
            var idFrame = "ifr" + page;
            
            blockLoadWin(true);
            
            $panel = $('<div id="' + idPanel + '" class="panelWin active"></div>').attr({"rel": page, "data-id": dataId});
            $frame = $('<iframe id="' + idFrame + '"></iframe>')
                        .attr({
                            "scrolling": "no",
                            "marginwidth" : "0",
                            "marginheight" : "0",
                            "width" : "0",
                            "height" : "0",
                            "frameborder" : "no",
                            "src" : page
                        }).load(function(){
                            blockLoadWin(false);
                            $(this).contents().find("body, body *").on('click', function(event) {
                                hideAllSlidePanels();
                            });
                        });
            $panel.append($frame);
            $panel.appendTo($(".list-sites")).addClass('active');
        }
        else
            $(".list-sites div.panelWin[id='" + idPanel + "']").addClass("active").show();
        loadConfigMenu("01");
    }
    
    function blockLoadWin(state) {
        precargaExp(".list-sites", state);
        if (state == false)
            $(".list-sites div.active").children("iframe").attr({"width":"100%","height":"100%"});
    }
    
    function createThumbWin(obj) {
        var idactive = "thumb" + $(obj).attr("id");
        var dataId = $(obj).attr("data-id");
        var titlewin = '';
        var iconwin = '';
        var bgcolor = '';

        if ($(".list-activewin .activewin[id='" + idactive + "']").length == 0){
            var titulo = $(obj).find(".brand .label").html();
            var page = $(obj).attr("rel");

            bgcolor = $(obj).attr('class').split(' ').pop();
            iconwin = '<div class="iconwin ' + bgcolor + '">' +  $(obj).find('.tile-content').html() + '</div>';

            $activewin = $('<div id="' + idactive + '" class="activewin active"></div>');
            $activewin.attr({
                "title": titulo,
                "rel": page,
                "data-id": dataId
            });
            //$thumb = $('<img class="thumb" />');
            //$thumb.attr("src", "images/preview-window.jpg");
            $lnkClose = $('<a href="#" class="close"></a>').append('<i class="icon-cancel-2"></i>');
            $lnkClose.appendTo($activewin).on('click', function(){
                $parent = $(this).parent();
                $(".list-sites .panelWin[rel='" + $parent.attr("rel") + "']").remove();
                $parent.remove();
                if ($(".list-activewin > .activewin").length == 0)
                    hideFrame();
                return false;
            });
            titlewin = '<span class="title">' + titulo + '</span>';
            $activewin.append(iconwin).append(titlewin).append('<div class="clear"></div>');
            $activewin.appendTo(".list-activewin .view").on('click', function(){
                hideFrame();
                navigateInFrame("01", this);
                $(this).addClass("active");
                return false;
            });
        }
        else {
            hideFrame();
            $(".list-activewin .activewin[id='" + idactive + "']").addClass("active");
        }
    }

    function loadConfigMenu (tipomenu) {
        var i = 0,
            idmenu = '0',
            idperfil = '0';

        idmenu = $('.list-sites div.panelWin.active').attr('data-id');
        idperfil = $('#spnIdPerfil').text();

        if (idmenu == null)
            idmenu = '0';

        $('#charmButton li').remove();
        $.ajax({
            type: "GET",
            url: "services/menu/charm-menu.php",
            cache: false,
            data: 'idmenu=' + idmenu + '&idperfil=' + idperfil + '&tipomenu=' + tipomenu + '&tipoconsulta=LIST',
            success: function(data){
                var datos = eval( "(" + data + ")" );
                var countDatos = datos.length;
                var nombre = '';
                var tagAction = '';
                
                while(i < countDatos){
                    nombre = datos[i].tm_nombre;
                    tagAction = datos[i].tm_tagaction;
                    $li = $('<li></li>');
                    $aLink = $('<a></a>');
                    $aLink.attr('href', '#' + tagAction).text(nombre);

                    $aLink.appendTo($li).on('click', function (e) {
                        showOptionWindow(this);
                        e.preventDefault();
                    });

                    $('#charmButton').append($li);
                    ++i;
                }
            }
        });
    }

    function showOptionWindow (obj) {
        idlink = $(obj).attr('href');
        header = $(obj).text();
        $(idlink).find('.modal-example-header h4').text(header);
        $.fn.custombox( obj, {
            effect: 'slit'
        });
    }
</script>