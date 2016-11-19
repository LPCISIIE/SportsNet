
$(function () {

    $('.close-alert').click(function (e) {
        $(e.target).parents('.alerte').fadeOut('fast', function () {
            this.remove();
        });
    });

    function readURL(input) {


    }

    $(".modifyPicHeader input").change(function(){
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('.page-header').fadeTo('slow',0.1, function(){
                    $(this).css('background-image', 'url(' + e.target.result + ')')
                }).delay(500).fadeTo('slow', 1);
                console.log(e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    $(".galerryAddPic input").change(function(){

        if (this.files && this.files[0]) {
            $('.galleryPreview').empty()
            for (var i = 0; i < this.files.length; i++) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var li=$('<li class="col-xs-6"></li>')
                    var img=$('<img src="'+e.target.result+'"/>')
                    li.append(img);
                    $('.galleryPreview').append(li)

                    console.log(e.target.result);
                }
                reader.readAsDataURL(this.files[i]);
                this.files[i]
            }

        }
    });

});
