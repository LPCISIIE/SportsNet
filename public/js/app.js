
$(function () {

    $('.close-alert').click((e) => {
        $(e.target).parents('.alerte').remove();
    });

});
