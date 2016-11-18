
$(function () {

    $('.close-alert').click(function (e) {
        $(e.target).parents('.alerte').fadeOut('fast', function () {
            this.remove();
        });
    });

});
