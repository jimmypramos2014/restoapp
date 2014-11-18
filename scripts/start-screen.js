(function($) {
    $.StartScreen = function(){
        var plugin = this;

        plugin.init = function(){
            setTilesAreaSize();
            addMouseWheel();
        };

        var setTilesAreaSize = function(){
            var groups = $(".modern-wrappanel .tile-group");
            var tileAreaWidth = 160;
            $.each(groups, function(i, t){
                tileAreaWidth += $(t).outerWidth()+46;
            });
            $(".modern-wrappanel .tile-area").css({
                width: tileAreaWidth
            });
        };

        var addMouseWheel = function (){
            $(".modern-wrappanel").mousewheel(function(event, delta){
                var scroll_value = delta * 50;
                $('.modern-wrappanel').scrollLeft($('.modern-wrappanel').scrollLeft() - scroll_value);
                return false;
            });
        };

        plugin.init();
    }
})(jQuery);

$(function(){
    $.StartScreen();
});
