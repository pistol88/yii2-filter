if (typeof pistol88 == "undefined" || !pistol88) {
    var pistol88 = {};
}

pistol88.filterAjax = {
    init: function() {
        $(document).on('change', '.pistol88-filter input', this.renderResults);
    },
    renderResults: function() {
        
        var data = $('.pistol88-filter').serialize();
        var resultHtmlSelector = $('.pistol88-filter').data('resulthtmlselector');
        
        console.log(resultHtmlSelector);
        
        $(resultHtmlSelector).css('opacity', 0.3);
        $(resultHtmlSelector).load(location.protocol + '//' + location.host + location.pathname+'?'+data+' '+resultHtmlSelector, function() {
            $(resultHtmlSelector).css('opacity', 1);
        });
        
        return false;
    }
};

pistol88.filterAjax.init();
