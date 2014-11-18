// JavaScript Document
var isCtrl = false;
(function (jQuery) {
    jQuery.fn.clickoutside = function (callback) {
        var outside = 1, self = $(this);
        self.cb = callback;
        this.click(function () {
            outside = 0;
        });
        $(document).click(function () {
            outside && self.cb();
            outside = 1;
        });
        return $(this);
    }
})(jQuery);

(function ($) {
   $.fn.liveDraggable = function (opts, sub) {
      this.on("mouseover", sub, function() {
         if (!$(this).data("init")) {
            $(this).data("init", true).draggable(opts);
         }
      });
      return this;
   };
}(jQuery));

function precargaExp(capa, bloqueo) {
	if (bloqueo == true) {
		$(capa).block({
			message: '<h2 class="fg-white">Cargando</h2>',
			css: {
				'width': '230px'
			}
		});
	}
	else
		$(capa).unblock();
}

serializa = function(obj, prefix) {
  var str = [];
  for(var p in obj) {
    var k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
    k = k.replace(/"/g, '');
    str.push(typeof v == "object" ?
      serializa(v, k) :
      encodeURIComponent(k) + "=" + encodeURIComponent(v));
  }
  return str.join("&");
}

function cargarFlexiGrid(tabla, alto, colModelo) {
    $(tabla).flexigrid({
        height: alto,
        resizable: false,
        showToggleBtn: false,
        nowrap: false,
        colModel: colModelo
    });
}

function margenflexi(tabla, alto) {
    galto = $(tabla).height();
    if (alto >= galto)
	    $(tabla).parent().css({ "padding-right": "16px" });    
	else if (alto < galto)
	    $(tabla).parent().css({ "padding-right": "0px" });
}

function customAutoSearch(typesource, obj, inSelect)
{
    $(obj).autocomplete({
        source:function( request, response ) {
            $.getJSON( 'public/generic-search.php', {
                typesource: typesource,
                term: request.term
            }, response );
        }, 
        minLength:2,
        select: inSelect,
        html: true,
        open: function(event, ui) {
            $(".ui-autocomplete").css("z-index", 1000);
        }
    });
}

function setSelectOptions(objSelect, state){
    $(objSelect).find('option').each(function(){
        if (state==true)
            $(this).attr('selected', 'selected');
        else
            $(this).removeAttr('selected');
    });
}

function habilitarControl(idcontrol, flag) {
    if (flag == true)
        $(idcontrol).removeAttr("disabled");
    else
        $(idcontrol).attr("disabled","-1");
}

function enterTextArea(idtextarea, destino){
    $(idtextarea).keyup(function(e) {
        if (e.which == 17) isCtrl = false;
    }).keydown(function(e) {
        if (e.which == 17) isCtrl = true;
        if (e.which == 13 && isCtrl == true) {
            $(destino).focus();
            isCtrl = false;
            return false;
        }
    });
}

function showEditForm(idform){
    $(idform).show();
}

function closeEditForm(idform){
    $(idform).hide();   
    precargaExp(".divload", false);
}

function ConvertMySQLDate (date) {
    var dateOriginal = new String(date);
    var dateConverted = '';
    var year = '';
    var month = '';
    var day = '';
    dateSlash = dateOriginal.split("-");
    year = dateSlash[0];
    month = dateSlash[1];
    dayIncoming = dateSlash[2];
    day = new String(dayIncoming).split(' ');
    day = day[0];
    dateConverted = day + '/' + month + '/' + year;
    return dateConverted;
}

function ConvertMySQLTime (date) {
    var dateOriginal = new String(date);
    var dateConverted = '';
    dateSpace = dateOriginal.split(" ");
    strTime = dateSpace[1];
    
    return strTime;
}

function buscarItem(lista, valor){
    var ind, pos;
    for(ind=0; ind<lista.length; ind++)
    {
        if (lista[ind] == valor)
        break;
    }
    pos = (ind < lista.length)? ind : -1;
    return (pos);
}

function convertDate(inputFormat) {
  function pad(s) { return (s < 10) ? '0' + s : s; }
  var d = new Date(inputFormat);
  return [pad(d.getDate()), pad(d.getMonth()+1), d.getFullYear()].join('/');
}

function dialogo(capa, ancho, alto, titulo, redimensionar) {
    var ResZ = false;
    $(capa).removeClass("oculto");
    if (redimensionar == null)
        ResZ = false;
    else
        ResZ = redimensionar;
    $(capa).dialog({
        bgiframe: true,
        draggable: true,
        title: titulo,
        width: ancho,
        height: alto,
        position: 'center',
        modal: true,
        resizable: ResZ,
        hide: 'fade',
        overlay: {
            backgroundColor: '#336699',
            opacity: 0.5
        },
        create: function () {
            $("form").append($("body > .ui-widget-overlay"));
            $("form").append($(capa).parent());
        },
        open: function () {
            $("form").append($("body > .ui-widget-overlay"));
            $("form").append($(capa).parent());
        },
        close: function () {
            $(capa).addClass("oculto");
            $("form").append($(capa).parent());
        }
    });
}

function onlyNumbers (e) {
    var charCode = (e.which) ? e.which : e.keyCode;
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

function MessageBox (title, message, buttons, callback) {
    $.MetroMessageBox({
        title: title,
        content: message,
        NormalButton: "#232323",
        ActiveButton: "#a20025",
        buttons: buttons
    }, callback);
}

function audioNotif (tipo) {
    var i = document.createElement("audio");
    var pathAudio = '';
    if (tipo == 'BigBox')
        pathAudio = "scripts/metro-alert/sound/bigbox.mp3";
    i.setAttribute("src", pathAudio);
    i.addEventListener("load", function () {
        i.play()
    }, true);
    i.pause();
    i.play();
}

function resetForm (idform) {
    var formulario = document.getElementById(idform);
    $('#hdIdPrimary').val('0');
    formulario.reset();
}

function showErrorsInValidate (errorMap, errorList) {
    $.each(this.validElements(), function (index, element) {
        var $element = $(element);

        $element.data("title", "")
        .removeClass("error state")
        .tooltip("destroy");

        $element.parent('div').removeClass('error-state');
    });

    $.each(errorList, function (index, error) {
        var $element = $(error.element);

        $element.tooltip("destroy")
        .data("title", error.message)
        .addClass("error state")
        .tooltip();

        $element.parent('div').addClass('error-state');
    });
}

function LoadSubCategorias (idreferencia, idControl, defaultValue) {
    $.ajax({
        type: "GET",
        url: "services/categorias/categorias-search.php",
        cache: false,
        data: "idref=" + (idreferencia == '0' ? '1' : idreferencia),
        success: function(data){
            i = 0;
            datos = eval( "(" + data + ")" );
            countDatos = datos.length;
            if (countDatos > 0){
                while(i < countDatos){
                    $option = $('<option></option>').attr('value', datos[i].id).html(datos[i].value);
                    $(idControl).append($option);
                    i++;
                }
                habilitarControl(idControl, true);
                if (defaultValue != null)
                    $(idControl).val(defaultValue);
            }
            else {
                $option = $('<option></option>').attr('value', '0').html('No hay sub-categor&iacute;as disponibles');
                $(idControl).append($option);
            }
            $(idControl)[0].selectedIndex = 0;
        }
    });
}

function LoadClientes (tipobusqueda, criterio, tipocliente, pagina) {
    precargaExp('.inner-page:visible .divload', true);
    selector = '';
    $('#gvDatos').off('scroll');
    if (pagina == '1'){
        if (tipobusqueda == '00')
            selector = '#gvDatos .items-area';
        else if (tipobusqueda == '01')
            selector = '#gvClientes .items-area';
        $(selector).html('');
    }
    setButtonState("03");
    $('#hdPageActual').val(pagina);
    $.ajax({
        type: "GET",
        url: "services/clientes/clientes-search.php",
        cache: false,
        data: "criterio=" + criterio + "&tipocliente=" + tipocliente + "&lastId=" + pagina,
        success: function(data){
            var datos = eval( "(" + data + ")" );
            var i = 0;
            var countDatos = datos.length;
            var emptyMessage = '';

            if (countDatos > 0){
                while(i < countDatos){
                    iditem = datos[i].tm_idtipocliente;
                    foto = datos[i].tm_foto;
                    $list = $('<a href="#" class="list dato bg-olive"></a>');

                    $strContent = '<input name="chkItem[]" type="checkbox" class="oculto" value="' + iditem + '" />';
                    $strContent += '<div class="list-content">';
                    $strContent += '<div class="data">';
                    $strContent += '<aside>';
                    if (foto == 'no-set')
                        $strContent += '<i class="fa fa-user"></i>';
                    else
                        $strContent += '<img src="' + foto + '" />';
                    $strContent += '</aside>';
                    $strContent += '<main><p class="fg-white"><strong>' + datos[i].Descripcion + '</strong>Tel&eacute;fono: ' + datos[i].tm_telefono + '<br />' + datos[i].TipoDoc + ': ' + datos[i].tm_nrodoc + ' - Email: ' + datos[i].tm_email + '</p>';
                    $strContent += '</main></div></div>';
                    
                    $list.html($strContent);

                    $list.attr('rel', iditem).appendTo('#gvDatos .items-area').on('click', function (e) {
                        //selectInTile(this);
                        return false;
                    });
                    ++i;
                }
                $('#hdPage').val(Number($('#hdPage').val()) + 1);
                if (tipobusqueda == '00'){
                    $('#gvDatos').on('scroll', function(){
                        if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight){
                            var pagina = $('#hdPage').val();
                            BuscarDatos(pagina);
                        }
                    });
                }
                else if (tipobusqueda == '01'){
                    $('#gvClientes').on('scroll', function(){
                        if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight){
                            var pagina = $('#hdPage').val();
                            BuscarPersonal(pagina);
                        }
                    });
                }
            }
            else {
                if (pagina == '1'){
                    emptyMessage = '<h2>No se encontraron resultados.</h2>';
                    if (tipobusqueda == '00')
                        selector = '#gvDatos .items-area';
                    else if (tipobusqueda == '01')
                        selector = '#gvClientes .items-area';
                    $(selector).html(emptyMessage);
                }
            }
            
            precargaExp('.inner-page:visible .divload', false);
        }
    });
}

function LoadPersonal (tipobusqueda, idcargo, criterio, pagina) {
    precargaExp('.inner-page:visible .divload', true);
    selector = '';
    $('#gvDatos').off('scroll');
    if (pagina == '1'){
        if (tipobusqueda == '00')
            selector = '#gvDatos .items-area';
        else if (tipobusqueda == '01')
            selector = '#gvPersonal .items-area';
        $(selector).html('');
    }
    setButtonState("03");
    $('#hdPageActual').val(pagina);
    $.ajax({
        type: "GET",
        url: "services/organigrama/organigrama-search.php",
        cache: false,
        data: "tipobusqueda=" + tipobusqueda + "&idcargo=" + idcargo + "&criterio=" + criterio + "&lastId=" + pagina,
        success: function(data){
            var datos = eval( "(" + data + ")" );
            var i = 0;
            var countDatos = datos.length;
            var emptyMessage = '';

            if (countDatos > 0){
                if (tipobusqueda == '00'){
                    while(i < countDatos){
                        iditem = datos[i].tm_idpersonal;
                        foto = datos[i].tm_foto;
                        $list = $('<a href="#" class="list dato bg-olive"></a>');

                        $strContent = '<input name="chkItem[]" type="checkbox" class="oculto" value="' + iditem + '" />';
                        $strContent += '<div class="list-content">';
                        $strContent += '<div class="data">';
                        $strContent += '<aside>';
                        if (foto == 'no-set')
                            $strContent += '<i class="fa fa-user"></i>';
                        else
                            $strContent += '<img src="' + foto + '" />';
                        $strContent += '</aside>';
                        $strContent += '<main><p class="fg-white"><strong>' + datos[i].Nombres + '</strong>Cargo: ' + datos[i].Cargo + '<br />DNI: ' + datos[i].tm_nrodni + ' - Email: ' + datos[i].tm_email + '</p>';
                        $strContent += '</main></div></div>';
                        
                        $list.html($strContent);

                        $list.attr('rel', iditem).appendTo('#gvDatos .items-area').on('click', function (e) {
                            selectInTile(this);
                            return false;
                        });
                        ++i;
                    }
                    $('#hdPage').val(Number($('#hdPage').val()) + 1);
                    $('#gvDatos').on('scroll', function(){
                        if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight){
                            var pagina = $('#hdPage').val();
                            BuscarDatos(pagina);
                        }
                    });
                }
            }
            else {
                if (pagina == '1'){
                    emptyMessage = '<h2>No se encontraron resultados.</h2>';
                    if (tipobusqueda == '00')
                        selector = '#gvDatos .items-area';
                    else if (tipobusqueda == '01')
                        selector = '#gvPersonal .items-area';
                    $(selector).html(emptyMessage);
                }
            }
            
            precargaExp('.inner-page:visible .divload', false);
        }
    });
}

function ApplyValidNumbers () {
    /*var fieldNumbers = $('.only-numbers');
    var count = fieldNumbers.length;
    var i = 0;

    while (i < count){
        $(fieldNumbers[i]).on('keypress', function (event) {
            //return /\d/.test(String.fromCharCode(event.keyCode));
            var charCode = (event.which) ? event.which : event.keyCode
            if (charCode != 45 && (charCode != 46 || $(this).val().indexOf('.') != -1) && (charCode < 48 || charCode > 57))
                return false;
            return true;
        });
        ++i;
    }*/
    $('.only-numbers').numeric(".");
}

function addLeadingZero(num) {
    return ((num < 10) ? "0" + num : "" + num);
}

function LoadProductos (tipobusqueda, idcategoria, idsubcategoria, criterio, pagina) {
    precargaExp('.inner-page:visible .divload', true);
    selector = '';
    
    if ($('#gvDatos').length > 0)
        $('#gvDatos').off('scroll');
    
    if ($('#gvProductos').length > 0)
        $('#gvProductos').off('scroll');
    
    if (Number(pagina) == 1){
        $('#hdPage').val('1');
        if (tipobusqueda == '00')
            selector = '#gvDatos .tile-area';
        else if (tipobusqueda == '01' || tipobusqueda == '02')
            selector = '#gvProductos .tile-area';
        $(selector).html('');
        setButtonState("03");
    }

    $('#hdPageActual').val(pagina);
    $.ajax({
        type: "GET",
        url: "services/productos/productos-search.php",
        cache: false,
        data: "tipobusqueda=" + tipobusqueda + "&idcategoria=" + idcategoria + "&idsubcategoria=" + idsubcategoria + "&criterio=" + criterio + "&lastId=" + pagina,
        success: function(data){
            var datos = eval( "(" + data + ")" );
            var idProducto = '0';
            var idMoneda = '0';
            var simboloMoneda = '';
            var nombreProducto = '';
            var precio = 0;
            var txtCantidadInTile = '';
            var cantidad = 1;
            var subtotal = 0;
            var i = 0;
            var countDatos = datos.length;
            var emptyMessage = '';
            var iconTipoCarta = '';
            var codTipoMenuDia = '';
            var tipoMenuDia = '';
            var bdColorCarta = '';

            if (countDatos > 0){
                if (tipobusqueda == '00'){
                    while(i < countDatos){
                        tile = $('<div class="tile dato double"></div>');
                        content = $('<div class="tile-content"></div>');
                        img = $('<img />');
                        trueContent = $('<div class="tile_true_content"></div>');
                        _status = $('<div class="tile-status bg-dark opacity"></div>');
                        label = $('<span class="label"></span>');
                        checkbox = $('<input name="chkItem[]" type="checkbox" class="oculto" />');

                        tile.attr('rel', datos[i].tm_idproducto);
                        checkbox.val(datos[i].tm_idproducto);

                        if (datos[i].tm_foto == 'no-set'){
                            img.attr('src', 'images/food-48.png');
                            tile.addClass('bg-olive');
                            content.addClass('icon');
                        }
                        else {
                            img.attr('src', datos[i].tm_foto);
                            content.addClass('image');
                        }
                        label.html('<span>' + datos[i].tm_codigo + ' </span><span class="nombre">' + datos[i].tm_nombre + '</span>');

                        content.append(img);
                        _status.append(label);

                        trueContent.append(content);
                        trueContent.append(_status);


                        tile.append(checkbox);
                        tile.append(trueContent);
                        
                        tile.appendTo('#gvDatos .tile-area').on('click', function () {
                            selectInTile (this);
                            return false;
                        });
                        ++i;
                    }
                    $('#gvDatos').on('scroll', function(){
                        if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight){
                            var pagina = $('#hdPage').val();
                            BuscarDatos(pagina);
                        }
                    });
                }
                else if ((tipobusqueda == '01') || (tipobusqueda == '02') || (tipobusqueda == '03')){
                    while(i < countDatos){
                        codTipoMenuDia = datos[i].codTipoMenuDia;

                        tile = $('<div class="tile dato double"></div>');
                        trueContent = $('<div class="tile_true_content"></div>');
                        content = $('<div class="tile-content"></div>');
                        img = $('<img />');
                        _status = $('<div class="tile-status bg-dark opacity"></div>');
                        label = $('<span class="label"></span>');
                        badge = $('<div class="badge bg-red"></div>');
                        moneda = $('<span class="moneda"></span>');
                        precio = $('<span class="precio"></span>');

                        tile.attr('rel', datos[i].tm_idproducto);
                        tile.attr('data-nomCategoria', datos[i].Categoria);
                        tile.attr('data-nomSubCategoria', datos[i].SubCategoria);
                        tile.attr('data-idCategoria', datos[i].tm_idcategoria);
                        tile.attr('data-idSubCategoria', datos[i].tm_idsubcategoria);
                        tile.attr('data-idMoneda', datos[i].tm_idmoneda);
                        tile.attr('data-codTipoMenuDia',  codTipoMenuDia);
                        tile.attr('data-tipoMenuDia',  datos[i].tipoMenuDia);

                        if (datos[i].tm_foto == 'no-set'){
                            img.attr('src', 'images/food-48.png');
                            tile.addClass('bg-olive');
                            content.addClass('icon');
                        }
                        else {
                            img.attr('src', datos[i].tm_foto);
                            content.addClass('image');
                        }
                        label.text(datos[i].nombreProducto);

                        moneda.attr('rel', datos[i].tm_idmoneda).text(datos[i].simboloMoneda + ' ');
                        precio.text(Number(datos[i].td_precio).toFixed(2));
                        
                        badge.append(moneda);
                        badge.append(precio);

                        content.append(img);
                        _status.append(label);
                        _status.append(badge);

                        trueContent.append(content);
                        trueContent.append(_status);

                        if ((tipobusqueda == '02') || (tipobusqueda == '03')) {
                            if (tipobusqueda == '02'){
                                flagTipoCarta = $('<div class="flag_tipocarta"></div>');
                                
                                if (codTipoMenuDia == '00'){
                                    iconTipoCarta = '<i class="icon-book"></i>';
                                    bdColorCarta = 'bd_color-carta';
                                }
                                else if (codTipoMenuDia == '01'){
                                    iconTipoCarta = '<i class="icon-clipboard-2"></i>';
                                    bdColorCarta = 'bd_color-menu';
                                }
                                else if (codTipoMenuDia == '02'){
                                    iconTipoCarta = '<i class="icon-star-3"></i>';
                                    bdColorCarta = 'bd_color-favorito';
                                }

                                flagTipoCarta.html(iconTipoCarta).addClass(bdColorCarta);
                                tile.append(flagTipoCarta);
                            }

                            inputSpinner = $('<div class="input_spinner"></div>');
                            txtCantidad = $('<input type="text" name="txtCantidadInTile" class="inputCantidad" />').attr('maxlength','3').val(cantidad);
                            
                            inputSpinnerButtons = $("<div class=\"buttons\"></div>");
                            iSButtonUp = $("<button type=\"button\" class=\"up bg-green fg-white\"></button>");
                            iSButtonUp.html('+');
                            iSButtonDown = $("<button type=\"button\" class=\"down bg-red fg-white\"></button>");
                            iSButtonDown.html('-');

                            txtCantidad.appendTo(inputSpinner).on('keypress keyup', function (event) {
                                var charCode = (event.which) ? event.which : event.keyCode;
                                if (charCode != 8 && charCode != 0 && (charCode < 48 || charCode > 57))
                                    return false;
                            }).on('blur', function () {
                                if ($(this).val().trim().length == 0)
                                    $(this).val('1');
                            });

                            iSButtonUp.appendTo(inputSpinnerButtons).on('click', function () {
                                inputSpinText = $(this).parent().parent().find('input');
                                if (Number(inputSpinText.val()) < 999)
                                    inputSpinText.val(Number(inputSpinText.val()) + 1);
                                return false;
                            });

                            iSButtonDown.appendTo(inputSpinnerButtons).on('click', function () {
                                inputSpinText = $(this).parent().parent().find('input');
                                if (Number(inputSpinText.val()) > 1)
                                    inputSpinText.val(Number(inputSpinText.val()) - 1);
                                return false;
                            });

                            inputSpinner.append(inputSpinnerButtons);
                            trueContent.appendTo(tile).on('click', function () {
                                $('#hdTipoSeleccion').val('01');
                                selectingTile(this);
                                inputSpinner = $(this).parent().find('.input_spinner');
                                if (!$(this).parent().hasClass('selected'))
                                    inputSpinner.hide();
                                else
                                    inputSpinner.show();
                                return false;
                            });
                            
                            tile.append(inputSpinner).appendTo('#gvProductos .tile-area');
                        }
                        else {
                            trueContent.appendTo(tile).on('click', function () {
                                selectingTile(this);
                                return false;
                            });
                            tile.appendTo('#gvProductos .tile-area');
                        }
                        ++i;
                    }
                    $('#gvProductos').on('scroll', function(){
                        if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight){
                            var pagina = $('#hdPage').val();
                            BuscarProductos(pagina);
                        }
                    });
                }
                $('#hdPage').val(Number($('#hdPage').val()) + 1);
            }
            else {
                if (pagina == '1'){
                    emptyMessage = '<h2>No se encontraron resultados.</h2>';
                    if (tipobusqueda == '00')
                        selector = '#gvDatos .tile-area';
                    else if (tipobusqueda == '01' || tipobusqueda == '02')
                        selector = '#gvProductos .tile-area';
                    $(selector).html(emptyMessage);
                }
            }
            
            precargaExp('.inner-page:visible .divload', false);
        }
    });
}

function NotificarAtencion (TipoNotificacion) {
    $.ajax({
        type: "GET",
        url: "services/ambientes/mesas-search.php",
        cache: false,
        data: 'tipobusqueda=' + TipoNotificacion + '&idambiente=0',
        success: function(data){
            var datos = eval( "(" + data + ")" );
            var countDatos = datos.length;
            var selector = '#pnlMesas .tile-area';
            var strContent = '';
            var IdAtencion = '0';
            var IdMesa = '0';
            var CodEstado = '';
            var ColorEstado = '';
            var NroComentales = '';

            if (countDatos > 0){

                IdAtencion = datos[0].tm_idatencion;
                IdMesa = datos[0].tm_idmesa;
                Codigo = datos[0].tm_codigo;
                CodEstado = datos[0].ta_estadoatencion;
                ColorEstado = datos[0].ta_colorleyenda;
                NroComentales = datos[0].tm_nrocomensales;

                if ($(selector + ' .tile[rel="' + IdMesa + '"]').length == 0){
                    $('#pnlMesas .tile-area h2').remove();

                    tile = $('<div class="tile"></div>');
                    tile.attr({
                         'data-idatencion': IdAtencion,
                         'data-state': CodEstado,
                         'rel': IdMesa
                    }).css('background-color', ColorEstado).addClass('blink').hide();

                    strContent = '<div class="tile-content">';
                    strContent += '<div class="text-right padding10 ntp">';
                    strContent += '<h1 class="fg-white">' + Codigo + '</h1>';
                    strContent += '</div></div>';
                    strContent += '<div class="brand"><span class="badge bg-dark">' + NroComentales + '</span></div>';

                    tile.html(strContent).prependTo('#pnlMesas .tile-area').fadeIn(300);
                    audioNotif('BigBox');
                }
                else {
                    tile = $('#pnlMesas .tile[rel="' + IdMesa + '"]');
                    if (tile.attr('data-state') != CodEstado){
                        tile.attr('data-state', CodEstado).css('background-color', ColorEstado);
                        audioNotif('BigBox');
                    }
                }
            }                    
        }
    });
}