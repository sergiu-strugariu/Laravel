$(document).ready(function(){

    $(document).on('click', 'a.edit_mail_template_button', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var modal = $('#edit_template');

        modal.addClass('control-sidebar-open');
        $('body').addClass('open');

        $.ajax({
            url: '/admin/mails/' + id,
            type: 'GET',
            success: function (response) {
                modal.html(response);
                modal.find('.wysihtml5-editor').wysihtml5({
                    stylesheets: ["/css/styles.min.css"],
                    toolbar:{
                        image: false,
                        blockquote: false,
                        lists: false
                    }
                });
            },
            error: function (response) {
                swal({
                    type: 'error',
                    title: 'The template cannot be updated.'
                });
            }
        });
    });

    $(document).on('click', '.edit_template_button', function (e) {

        var id = $(this).attr('data-id');

        AjaxCall({
            url: '/admin/mails/' + $(this).attr('data-id')
        }).makeCall($(this), function(res){
            $('a.edit_mail_template_button[data-id="'+id+'"]').text( id + '. ' + res.data.name);
            $('.grid').masonry();
            $(".control-sidebar").removeClass('control-sidebar-open');
            $('body').removeClass('open');
        });

    });
    
});