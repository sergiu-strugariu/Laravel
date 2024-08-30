var _token = $('meta[name="csrf-token"]').attr('content'),
    testTimeLimit = null,
    testPaperTypeId = null,
    currentAudioTime = null,
    testID = null,
    listeningHasClickedSubmit = false,
    testHash = null;

var AjaxCall = function (options) {

    this.defaultOptions = {
        spinner: '<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>',
        headers: {'X-CSRF-TOKEN': _token},
        dataType: 'json',
        method: 'POST',
        url: null,
        successTitle: 'Success!',
        successMsg: null,
        errorTitle: 'Error!',
        errorMsg: null,
        data: null,
        processData: null,
        rules: {},
        resetForm: true,
        messages: {},
        showSuccess: true,
        validate: true,
        ignoreValidation: null,
    };
    this.btn = null;
    this.defaultOptions = $.extend(this.defaultOptions, options);

    this.makeCall = function (element, callback) {

        var self = this;
        var form = element.parents('form:first');
        self.btn = element;
        if (self.defaultOptions.validate) {
            form.show();
            form.validate({
                ignore: self.defaultOptions.ignoreValidation,
                rules: self.defaultOptions.rules,
                messages: self.defaultOptions.messages,
                submitHandler: function (form, e) {
                    e.preventDefault();
                    self.sendAjax(form, callback);
                }
            });
        } else {
           self.sendAjax(form, callback);
        }


    };

    this.sendAjax = function (form, callback) {

        var self = this;

        var data;

        if (self.defaultOptions.data) {
            data = self.defaultOptions.data;
        } else {
            data = $(form).serialize()
        }

        if (!self.btn.hasClass('ajax-loading')) {

            var btn_text = self.btn.html();
            self.btn.addClass('ajax-loading');
            //self.btn.html(self.defaultOptions.spinner);

            $.ajax({
                type: self.defaultOptions.method,
                url: self.defaultOptions.url,
                headers: self.defaultOptions.headers,
                data: data,
                processData: self.defaultOptions.processData,
                dataType: 'json',
                success: function (res) {
                    if (res == 'Success' || res.resType === true || res.resType === 'Success' || res.resType === 'success') {

                        if ($(form)[0] !== undefined && self.defaultOptions.resetForm === true) {
                            $(form)[0].reset();
                        }

                        if (self.defaultOptions.showSuccess) {
                            swal(self.defaultOptions.successTitle, self.defaultOptions.successMsg, 'success');
                        }

                        if (res.data != undefined && res.data.redirectTo != undefined && res.data.redirectTo != false) {
                            window.location.href = res.data.redirectTo;
                        }

                        if (callback) {
                            callback(res);
                        }

                        $(form).trigger("custom-ajax-success");
                    } else {
                        var errMsg = res.resMotive !== undefined ? res.resMotive : res.errMsg;

                        if (typeof errMsg === 'object') {
                            var errors = '';
                            $.each(errMsg, function (field, error) {
                                errors += error + '<br>';
                            });

                            errMsg = errors;
                        }

                        var errorText = self.defaultOptions.errorMsg ? self.defaultOptions.errorMsg : errMsg;
                        swal(self.defaultOptions.errorTitle, errorText, 'error');
                    }
                },
                error: function (res) {
                    console.log(res);
                },
                complete: function () {
                    self.btn.removeClass('ajax-loading');
                    //self.btn.html(btn_text);
                }
            });
        }
    };

    return this;
};

function verify_input_label() {
    $.each($('label.form-label').parent().find('input'), function (key, value) {
        if ($(value).val().length) {
            $(value).addClass('filled');
            $(value).parents('.form-group').addClass('focused');
        }
    });

}

if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
    $('#project-tasks-table').addClass('nowrap');
}

$(document).ready(function () {

    $(document).on('click', 'a', function(e){
        if (!$('#project-tasks-table').has(e.target).length) {
            clearDatatablesStorage();
        }
    });

    if ($('.cruds ul li.active').length) {
        $('.cruds').addClass('menu-open');
        $('.cruds ul').css('display', 'block');
    }

    $(document).on('click', '.close', function () {
        $('body').removeClass('open');
    });

    $('.form-control.fly-placeholder').on('focus blur change', function (e) {
        var $this = $(this),
            _this = this,
            _e = e;
        setTimeout(function () {
            $this.parents('.form-group').toggleClass('focused', (_e.type === 'focus' || _this.value.length > 0 || $this.is(':-webkit-autofill')));
        }, 10);
    }).trigger('blur');

    $('.update_profile').on('click', function (e) {

        var form = $(this).parents('form:first');
        form.show();
        form.validate({
            errorPlacement: function(error, element){
                var div = element.closest('.input-group');
                error.insertAfter(div);
            },
            rules: {
                password_confirmation: {
                    equalTo: '#password'
                },
                phone: {
                    digits: true
                }
            },
            messages: {
                password_confirmation: {
                    equalTo: "Password does not match"
                },
                phone: {
                    equalTo: "Please enter numbers only"
                }
            },
            submitHandler: function (form, e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: '/user/update',
                    headers: {'X-CSRF-TOKEN': _token},
                    data: $('#form').serialize(),
                    dataType: 'json',
                    success: function (res) {
                        if (res.resType == 'success') {
                            swal(
                                'Success!',
                                'Your profile has been updated.',
                                'success'
                            );

                            window.location.href = "/home";
                        } else {
                            swal(
                                'Error!',
                                res.resMotive,
                                'error'
                            );
                        }
                    },
                    error: function (res) {
                        console.log(res);
                    }
                });
            }
        });
    });

    $('.create_user_manually').on('click', function () {

        var form = $(this).parents('form:first');

        $.validator.methods.email = function (value, element) {
            return this.optional(element) || /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i.test(value);
        };
        form.show();
        form.validate({
            submitHandler: function (form, e, _this) {
                e.preventDefault();
                $.ajax({
                    url: '/admin/createUserManually',
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: $(form).serialize(),
                    dataType: 'json',
                    success: function (res) {
                        if (res.resType === 'Success') {
                            swal(
                                'Success!',
                                'User have been created.',
                                'success'
                            );
                            $(form)[0].reset();
                        } else {
                            swal(
                                'Error!',
                                res.errMsg,
                                'error'
                            );
                        }
                    },
                    error: function (res) {
                    }
                })
            }
        });
    });

    $('.create_user_automatically').on('click', function (e) {

        e.preventDefault();

        var formData = new FormData();
        formData.append('file', $('#file')[0].files[0]);

        $.ajax({
            type: 'POST',
            url: '/admin/createUserAutomatically',
            headers: {'X-CSRF-TOKEN': _token},
            data: formData,
            processData: false,  // tell jQuery not to process the data
            contentType: false,  // tell jQuery not to set contentType
            success: function (res) {
                if (res.resType == 'Success') {
                    swal(
                        'Success!',
                        'Users have been created.',
                        'success'
                    );
                } else {
                    swal(
                        'Error!',
                        res.resMotive,
                        'error'
                    );
                }
            },
            error: function (res) {
                console.log(res);
            }
        });
    });

    $('#role_id').on('change', function () {
        if ($('#role_id :selected').text() == 'Client' || $('#role_id :selected').text() == 'TDS') {
            $('.client_parent').css('display', 'block');
            $('#client_id').attr('name', 'client_id');
        } else {
            $('.client_parent').css('display', 'none');
            $('#client_id').attr('name', '');
        }
    });

    $('.client_add_new').on('click', function (e) {
        e.preventDefault();

        var _this = $(this);

        if (!$('#client_datatable_' + _this.attr('data-id')).find('tbody>tr').hasClass('new_class_client')) {
            if (window.matchMedia('(max-width: 767px)').matches) {
                $('#client_datatable_' + _this.attr('data-id')).find('tbody').prepend('<tr role="row" class="odd new_class_client"><td class="sorting_1" colspan="5"><input type="text" name="first_name" value="" class="form-control" placeholder="First Name" required/></td></tr><tr role="row" class="odd new_class_client"><td colspan="5"><input type="text" name="last_name" value="" class="form-control" placeholder="Last Name" required/></td></tr><tr role="row" class="odd new_class_client"><td colspan="5"><input type="email" name="email" value="" class="form-control" placeholder="Email" required/>' +
                    '</td></tr><tr role="row" class="odd new_class_client"><td colspan="5"><select name="projects[]" id="projects_id_' + _this.attr("data-id") + '"  class="form-control"  multiple="multiple" required> </select></td></tr>' +
                    '<tr role="row" class="odd new_class_client"><td colspan="5"><input type="submit" class="submit_project_participant" data-id="' + _this.attr("data-id") + '" value=""/><a href="#" class="confirm_css_recruiter" style="margin-left: 43px"><span class="fa fa-check"></span></a></td></tr>');
            } else {
                $('#client_datatable_' + _this.attr('data-id')).find('tbody').prepend('<tr role="row" class="odd new_class_client"><td class="sorting_1"><input type="text" name="first_name" value="" class="form-control" required/></td><td><input type="text" name="last_name" value="" class="form-control" required/></td><td><input type="email" name="email" value="" class="form-control" required/>' +
                    '</td><td><select name="projects[]" id="projects_id_' + _this.attr("data-id") + '"  class="form-control"  multiple="multiple"  required> </select></td>' +
                    '<td><input type="submit" class="submit_project_participant" data-id="' + _this.attr("data-id") + '" value=""/><a href="#" class="confirm_css_recruiter" style="margin-left: 43px"><span class="fa fa-check"></span></a></td></tr>');
            }
        }
        $.ajax({
            type: "GET",
            url: "/project/getClientProjects/" + $(this).attr('data-id'),
            dataType: 'json',
            success: function (data) {
                for (var i = 0; i < data.length; i++) {
                    $('#projects_id_' + _this.attr('data-id')).append('<option value="' + data[i].id + '">' + data[i].name + '</option>');
                }
            }
        });

        $('#projects_id_' + _this.attr('data-id')).select2({
            allowClear: true,
        });
    });

    $('.group_add_new').on('click', function (e) {
        e.preventDefault();

        var _this = $(this);

        if (!$('#group_datatable_' + _this.attr('data-id')).find('tbody>tr').hasClass('new_class_group')) {
            if (window.matchMedia('(max-width: 767px)').matches) {
                $('#group_datatable_' + _this.attr('data-id')).find('tbody').prepend('<tr role="row" class="odd new_class_group">' +
                    '<td colspan="4"><select name="user_id" id="user_id_' + _this.attr("data-id") + '"  class="form-control" required></select></td></tr>' +
                    '<tr role="row" class="odd new_class_group"><td colspan="4"><input name="native" id="native_' + _this.attr("data-id") + '" value="1" type="checkbox"></td></tr>' +
                    '<tr role="row" class="odd new_class_group"><td colspan="4"><input type="submit" class="submit_group_user" data-id="' + _this.attr("data-id") + '" data-group="' + _this.attr("data-group") + '" value=""/><a href="#" class="confirm_css_recruiter"><span class="fa fa-check"></span></a></td>' +
                    '</tr>');
            } else {
                $('#group_datatable_' + _this.attr('data-id')).find('tbody').prepend('<tr role="row" class="odd new_class_group">' +
                    '<td><select name="user_id" id="user_id_' + _this.attr("data-id") + '"  class="form-control" required></select></td>' +
                    '<td colspan="2"><input name="native" id="native_' + _this.attr("data-id") + '" value="1" type="checkbox"></td>' +
                    '<td><input type="submit" class="submit_group_user" data-id="' + _this.attr("data-id") + '" data-group="' + _this.attr("data-group") + '" value=""/><a href="#" class="confirm_css_recruiter" style="margin-left: 43px"><span class="fa fa-check"></span></a></td>' +
                    '</tr>');
            }
        }

        $('#native_' + _this.attr('data-id')).bootstrapSwitch({
            onText: 'Yes',
            offText: 'No'
        });

        $.ajax({
            type: "GET",
            url: "/group/get-assessors",
            success: function (data) {
                result = JSON.parse(data);

                var options = [];

                $.each(result, function (key, value) {
                    options.push({id: key, text: value})
                });

                $('#user_id_' + _this.attr('data-id')).empty().select2({
                    data: options,
                    allowClear: true,
                    placeholder: 'Assessor'
                });
            }
        });

        $('#user_id_' + _this.attr('data-id')).select2({
            allowClear: true,
            placeholder: 'Assessor'
        });
    });

    $('.css_recruiter_add_new').on('click', function (e) {
        e.preventDefault();

        var _this = $(this);

        if (!$('#css_datatable').find('tbody>tr').hasClass('new_class_client')) {
            if (window.matchMedia('(max-width: 767px)').matches) {
                $('#css_datatable').find('tbody').prepend('<tr role="row" class="odd new_class_client"><td colspan="6" class="sorting_1"><input type="text" name="first_name" value="" class="form-control" placeholder="First Name" required/></td></tr><tr role="row" class="odd new_class_client"><td colspan="6"><input type="text" name="last_name" value="" class="form-control" placeholder="Last Name" required/></td></tr><tr role="row" class="odd new_class_client"><td colspan="6"><input type="email" name="email" value="" class="form-control" placeholder="Email" required/>' +
                    '</td></tr role="row" class="odd new_class_client"><tr role="row" class="odd new_class_client"><td colspan="6"><select name="role" id="role"  class="form-control" required><option value="3">Recruiter</option> <option value="4">Css</option></select></td></tr>' +
                    '<tr role="row" style="display: none" class="odd new_class_client"><td></td></tr><tr role="row" class="odd new_class_client"><td colspan="6"><input type="submit" class="submit_css_recruiter" value=""/><a href="#" class="confirm_css_recruiter" style="margin-left: 43px"><span class="fa fa-check"></span></a></td></tr>');
            } else {
                $('#css_datatable').find('tbody').prepend('<tr role="row" class="odd new_class_client"><td class="sorting_1"><input type="text" name="first_name" value="" class="form-control" required/></td><td><input type="text" name="last_name" value="" class="form-control" required/></td><td><input type="email" name="email" value="" class="form-control" required/>' +
                    '</td><td><select name="role" id="role"  class="form-control" required><option value="3">Recruiter</option> <option value="4">Css</option></select></td>' +
                    '<td></td><td><input type="submit" class="submit_css_recruiter" value=""/><a href="#" class="confirm_css_recruiter" style="margin-left: 43px"><span class="fa fa-check"></span></a></td></tr>');
            }
        }
    });

    $('.tds_add_new').on('click', function (e) {
        e.preventDefault();

        var _this = $(this);

        if (!$('#tds_datatable').find('tbody>tr').hasClass('new_class_client')) {
            if (window.matchMedia('(max-width: 767px)').matches) {
                $('#tds_datatable').find('tbody').prepend('<tr role="row" class="odd new_class_client"><td colspan="6" class="sorting_1"><input type="text" name="first_name" value="" class="form-control" placeholder="First Name" required/></td></tr><tr role="row" class="odd new_class_client"><td colspan="6"><input type="text" name="last_name" value="" class="form-control" placeholder="Last Name" required/></td></tr><tr role="row" class="odd new_class_client"><td colspan="6"><input type="text" name="email" value="" class="form-control" placeholder="Email" required/></td></tr>' +
                    '<tr style="display: none" role="row" class="odd new_class_client"><td></td></tr><tr style="display: none" role="row" class="odd new_class_client"><td></td></tr><tr role="row" class="odd new_class_client"><td colspan="6"><input type="submit" class="submit_tds" value=""/><a href="#" class="confirm_css_recruiter" style="margin-left: 43px"><span class="fa fa-check"></span></a></td></tr>');
            } else {
                $('#tds_datatable').find('tbody').prepend('<tr role="row" class="odd new_class_client"><td class="sorting_1"><input type="text" name="first_name" value="" class="form-control" required/></td><td><input type="text" name="last_name" value="" class="form-control" required/></td><td><input type="text" name="email" value="" class="form-control" required/>' +
                    '<td></td><td></td><td><input type="submit" class="submit_tds" value=""/><a href="#" class="confirm_css_recruiter" style="margin-left: 43px"><span class="fa fa-check"></span></a></td></tr>');
        }
    }
    });

    $('.add_new_project').on('click', function (e) {
        AjaxCall({
            url: '/project/create',
            successMsg: 'Project has been created.',
            errorMsg: 'Project has not been created.'
        }).makeCall($(this), function (res) {
            // $('#create_project_form')[0].reset();
            var clientTemplate = $('#project-template').html();
            var newItem = $(clientTemplate);
            newItem.attr('data-model', res.data.project_type_id);
            newItem.find('.pt_a_first').attr('href', '/project/' + res.data.id + '/tasks').html(res.data.name);
            newItem.find('.pt_a_second span').attr('data-id', res.data.id);
            newItem.find('.pt_a_second span').attr('data_client_id', res.data.client_id);
            newItem.find('.pt_a_second > .delete_project').attr('data-id', res.data.id);
            $('.add-project[data-id="' + res.data.client_id + '"]').before(newItem);
            $('.grid').masonry();
            $('.add-new-project-modal').removeClass('control-sidebar-open');
            $('body').removeClass('open');
        });

    });

    $('.edit-project-button').on('click', function (e) {
        var projectID = $('#project_id_edit').val();

        AjaxCall({
            url: '/project/update/' + projectID,
            successMsg: 'Project has been updated.',
            errorMsg: 'Project has not been updated.'
        }).makeCall($(this), function () {
            window.location.reload();
        });
    });

    $(document).on('click', '.delete-project', function (e) {
        var _this = $(this);

        swal({
            title: "Are you sure?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            focusConfirm: false
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    type: 'GET',
                    url: '/project/delete/' + _this.attr('data-id'),
                    headers: {'X-CSRF-TOKEN': _token},
                    dataType: 'json',
                    success: function (res) {
                        if (res == 1) {
                            swal(
                                'Success!',
                                'Project has been deleted.',
                                'success'
                            );
                            window.location.reload();
                        } else {
                            swal(
                                'Error!',
                                res,
                                'error'
                            );
                        }
                    },
                    error: function (res) {
                    }
                });
            }
        });
    });

    $('.add_new_client_button').on('click', function (e) {

        AjaxCall({
            url: '/admin/createClient'
        }).makeCall($(this), function (res) {
            // $('#add_new_client_form')[0].reset();
            var clientTemplate = $('#client-template').html();
            var newItem = $(clientTemplate);
            newItem.find('.client-name').attr('data-id', res.data.id);
            newItem.find('.client-name').html(res.data.name);
            newItem.find('.add-button').attr('data-id', res.data.id);
            var gridMasonry = $('.grid');
            gridMasonry.append(newItem);
            gridMasonry.masonry('appended', newItem, true);
            gridMasonry.masonry('reloadItems');
            $('#add_new_client').removeClass('control-sidebar-open');
            $('body').removeClass('open');
        });

    });

    $(document).mousedown(function (e) {
        var container = $(".control-sidebar");

        // if the target of the click isn't the container nor a descendant of the container
        if (
            container.has(e.target).length === 0
            && !$(e.target).hasClass('select2-results__option')
            && !$(e.target).parents('.modal').length > 0
            && !$(e.target).hasClass('modal')
            && !$(e.target).hasClass('select2-selection__renders')
            && !$(e.target).hasClass('select2-dropdown')
            && !$(e.target).hasClass('select2-results__options')
            && !$(e.target).hasClass('select2-search__field')
            && $('.ui-timepicker').has($(e.target)).length === 0
            && !container.hasClass('unclosable')
            && !$(e.target).hasClass('control-sidebar')
            && $('.swal2-container').has(e.target).length === 0
            && !$(e.target).hasClass('swal2-container')
        ) {
            container.removeClass('control-sidebar-open');
            $('body').removeClass('open');
        }
    });

    $(document).on('change', '#project_type_select', function (e) {

        if ($(this).val() == 0) {
            $('.project-name').removeClass('hidden');
        } else {
            $('.project-name').removeClass('hidden');
            $(".project-name:not([data-model='" + $(this).val() + "'])").addClass('hidden');
        }

        setTimeout(function(){
            projectsMasonry.masonry();
        }, 300);
    });

    $(document).on('click', '.input-group-addon', function () {
        var container = $(this).parent();
        if (!container.hasClass('picker-opened')) {
            container.addClass('picker-opened');
            container.find('input').focus();
        } else {
            container.removeClass('picker-opened');
            container.find('input').blur();
        }
    });


    $('#answer').bind("cut copy paste", function (e) {
        e.preventDefault();
    });

    function countWords(textArea) {
        var value = textArea.val();
        var regex = /\s+/gi;
        var wordCount = value.trim().replace(regex, ' ').split(' ').length;
        $('#words_used').text(wordCount);
        $('#words_left > label').text(wordCount);
        return wordCount;
    }

    $('#answer').keydown(function (e) {
        var max = parseInt($('#words_counter').val());
        var extraWords = 20;
        var wordCount = countWords($(this));
        if (wordCount >= max ){
            $('#words_used').addClass('red');
        } else {
            $('#words_used').removeClass('red');
        }
        if (wordCount >= max + extraWords) {
            if (e.keyCode !== 8) {
                e.preventDefault();
            } else {
                wordCount = countWords($(this));
            }
        }
    });

    $('.writing_submit').on('click', function (e) {
        $(this).attr('disabled', true);
        AjaxCall({
            url: '/test/submitWritingAnswer',
            showSuccess: false,
            resetForm: false,
            validate: false
        }).makeCall($(this), function () {
            $('.timer').countdown('stop');
            window.location.reload();
        });
    });

    $('.reading_submit').on('click', function (e) {
        $(this).attr('disabled', true);
        AjaxCall({
            url: '/test/submitReadingAnswer',
            showSuccess: false,
            resetForm: false,
            validate: false
        }).makeCall($(this), function () {
            $('.timer').countdown('stop');
            window.location.reload();
        });
    });

    $('.listening_submit').on('click', function (e) {
        $(this).attr('disabled', true);
        AjaxCall({
            url: '/test/submitListeningAnswer',
            showSuccess: false,
            resetForm: false,
            validate: false
        }).makeCall($(this), function () {
            $('.timer').countdown('stop');
            listeningHasClickedSubmit = true;
            window.location.reload();
        });
    });
    
    $('#form-language-use-new-test input').keydown(function (e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            return false;
        }
    });

    $('.language_use_new_submit').on('click', function (e) {
        var empty = false;
        if ($('.language-use-1').length > 0) {
            var checked = $('#form-language-use-new-test').find("input[type=radio]:checked").length;
            if (checked == 0) {
                empty = true;
            }
        } else {
            var userAnswer = $('#form-language-use-new-test').find("input[name=user_answer]");
            if ($('.language-use-2').length > 0) {
                if (userAnswer.val()) {
                    var parsedUserAnswer = JSON.parse(userAnswer.val());
                    var draggedElements = $('#arrangedList li:not(:empty)').length;
                    if (parsedUserAnswer.length != draggedElements) {
                       empty = true;
                   }
                }
            }
            if (!userAnswer.val()) {
                empty = true;
            }
        }
        if ((empty && confirm('Are you sure you want to leave it blank?')) || !empty) {
            $(this).attr('disabled', true);
            AjaxCall({
                url: '/test/submitLanguageUseNewAnswer',    
                showSuccess: false,
                resetForm: false,
                validate: false
            }).makeCall($(this), function () {
                $('.timer').countdown('stop');
                window.location.reload();
            });
        }
    });

    $('.language_use_submit').on('click', function (e) {
        var checked = $('#form-language-use-test').find("input[type=radio]:checked").length;
        if ((checked == 0 && confirm('Are you sure you want to leave it blank?')) || checked > 0) {
            $(this).attr('disabled', true);
            AjaxCall({
                url: '/test/submitLanguageUseAnswer',
                showSuccess: false,
                validate: false,
                resetForm: false
            }).makeCall($(this), function () {
                $('.timer').countdown('stop');
                window.location.reload();
            });
        }
        
    });

    $(document).on('click', '.setting-edit-button', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');

        $('#edit_setting').addClass('control-sidebar-open');
        $('body').addClass('open');


        $.ajax({
            url: '/admin/settings/' + id,
            type: 'GET',
            success: function (response) {
                $('#edit_setting').html(response);
                $('#edit_setting').find('.wysihtml5-editor').wysihtml5({
                    stylesheets: ["/css/styles.min.css"],
                    toolbar: {
                        image: false,
                        blockquote: false,
                        lists: true
                    }
                });

            },
            error: function (response) {
                swal({
                    type: 'error',
                    title: 'The setting cannot be updated.'
                });
            }
        });
    });

    $(document).on('click', '.edit_setting_button_submit', function () {
        var form = $(this).parents('form:first');
        var submitHandler = function () {

            var formData = new FormData($('#setting_update_form')[0]);
            var action = form.attr('action');

            $.ajax({
                url: action,
                type: 'POST',
                data: formData,
                success: function (res) {
                    if (res.resType == 'success') {
                        swal('Success', 'Setting updated!', 'success').then(function () {
                            form[0].reset();
                            $(".control-sidebar").removeClass('control-sidebar-open');
                            $('body').removeClass('open');
                        });
                    } else {
                        swal('Error', res.errMsg, 'error');
                    }
                },
                cache: false,
                contentType: false,
                processData: false,
                complete: generateSettingsTable
            });
        };


        form.show();
        form.validate({
            submitHandler: submitHandler
        });
    });


    $('.table').on('draw.dt', function () {
        $('.dataTables_length select option').each(function (index, element) {
            if ($(this).text().indexOf('entries') < 0) {
                $(this).text($(this).text() + ' entries');
            } else {
                $(this).text($(this).text());
            }
        });
    });

    $.extend($.fn.dataTable.defaults, {
        language: {
            lengthMenu: "Show _MENU_"
        }
    });

    if ($('body').hasClass('tests-layout')) {

        document.addEventListener("contextmenu", function (e) {
            e.preventDefault();
        }, false);

        document.addEventListener("keydown", function (e) {
            // "f5" key
            if (e.keyCode == 116) {
                disabledEvent(e);
            }
            // "f12" key
            if (e.keyCode == 123) {
                disabledEvent(e);
            }

            if (e.ctrlKey && (e.keyCode === 85)) {
                disabledEvent(e);
            }
        }, false);

        function disabledEvent(e) {
            if (e.stopPropagation) {
                e.stopPropagation();
            } else if (window.event) {
                window.event.cancelBubble = true;
            }
            e.preventDefault();
            return false;
        }


        if (testTimeLimit != null && testPaperTypeId != null) {
            startTimer(atob(testTimeLimit), atob(testPaperTypeId));
        }

        if ($('body').hasClass('section-test-listening')) {

            var current_audio_time = parseFloat(atob(currentAudioTime));
            var players;
            var plyr_options = {
                clickToPlay: true,
                showPosterOnEnd: true,
                controls: ['play', 'progress', 'volume'],
                keyboardShortcuts: {focused: false, global: false},
                tooltips: {
                    seek: false
                }
            };
            players = plyr.setup(plyr_options);

            players[0].on('pause', function (event) {});
            players[0].on('ended', function (event) {
                $('button[data-plyr="play"]').remove();
            });
            players[0].on('play', function (event) {
                if (current_audio_time != null) {
                    players[0].seek(current_audio_time);
                }
                $('button[data-plyr="pause"]').remove();
            });


            jQuery(document).ready(
                function () {
                    $(window).on('beforeunload', function () {
                        var seekTime = players[0].getCurrentTime();
                        if (seekTime != 0) {
                            $.ajax({
                                method: 'POST',
                                async: false,
                                url: '/test/insertCurrentAudio',
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                data: {
                                    'time': listeningHasClickedSubmit === true ? 0 : seekTime,
                                    'test_id': atob(testID),
                                    'hash': atob(testHash)
                                },
                                success: function (res) {
                                    // console.log(res);
                                },
                                error: function (res) {
                                    console.log(res);
                                }
                            });
                        }
                    });
                }
            );


        }

        if ($('body').hasClass('section-test-language-use')) {

            var el = document.getElementById('items');
            if (el != null) {
                var sortable = Sortable.create(el, {
                    sort: true,
                    group: {
                        name: 'advanced',
                        pull: true,
                        put: true
                    },
                    onRemove: function (/**Event*/evt) {
                        $('#arrangedList').find('.ghost-li').first().remove();
                    },
                    onStart: function (evt, originalEvent) {
                        $('#arrangedList').find('.ghost-li').first().hide();
                    },
                    onEnd: function (evt, originalEvent) {
                        $('#arrangedList').find('.ghost-li').first().show();
                    }
                });

                var listToSend = document.getElementById('arrangedList');
                var list = Sortable.create(listToSend, {
                    sort: true,
                    group: {
                        name: 'advanced',
                        pull: false,
                        put: true
                    },
                    onAdd: getUserAnswer,
                    onSort: getUserAnswer
                });

                function getUserAnswer() {
                    var questionArray = [];
                    $('#arrangedList li').each(function () {
                        questionArray.push($(this).text());
                    });
                    $('#user_answer').val(JSON.stringify(questionArray));
                }

            }

        }

    }

    $('#send_email_manager').on('click', function (e) {
        var form = $('#email_manager_form');
        form.show();
        form.validate({
            submitHandler: function (form, e, _this) {
                $.ajax({
                    method: 'POST',
                    url: '/admin/sendMailToLanguageAuditManager',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    dataType: 'json',
                    data: $('#email_manager_form').serialize(),
                    success: function (res) {

                        if (res.resType == 'success') {
                            swal('Success', 'Mail sent!', 'success').then(function () {
                                $('#email_manager_form')[0].reset();
                                $(".control-sidebar").removeClass('control-sidebar-open');
                                $('body').removeClass('open');
                            });
                        } else {
                            swal('Error', res.errMsg, 'error');
                        }
                    },
                    error: function (res) {
                        console.log(res);
                    }
                });
            }
        });

    });

    $('#menu-button').on('click', function () {
        $('#project-tasks-table').dataTable().fnDraw();
    });

});


function startTimer(timeLimit, paperType) {

    paperType = parseInt(paperType);

    $.get('/question-time-metadata?t='+(timeLimit), function(srvrTimeLimit){

        srvrTimeLimit = parseInt(srvrTimeLimit);
        var endDate = new Date(Date.parse(new Date()) + srvrTimeLimit );


        $('.timer').countdown(endDate)
            .on('update.countdown', function (event) {
                $('.timer_div').removeClass('hidden');
                var format = '%M:%S';
                if (event.offset.totalDays > 0) {
                    format = '%-d day%!d ' + format;
                }
                if (event.offset.weeks > 0) {
                    format = '%-w week%!w ' + format;
                }
                $(this).html(event.strftime(format));
            })
            .on('finish.countdown', function (event) {
                switch (paperType) {
                    case 1:
                        $('#form-language-use-new-test').find('.next-question-button').trigger('click');
                        break;
                    case 2:
                        break;
                    case 3:
                        $('#form-writing-test').find('.next-question-button').trigger('click');
                        break;
                    case 4:
                        $('#form-listening-test').find('.next-question-button').trigger('click');
                        break;
                    case 5:
                        $('#form-reading-test').find('.next-question-button').trigger('click');
                        break;
                    case 6:
                        $('#form-language-use-test').find('.next-question-button').trigger('click');
                        break;
                    default: break;
                }
            })

    });

}

function generateSettingsTable() {

    return $('#settings').DataTable({
            destroy: true,
            paging: true,
            lengthChange: false,
            pageLength: 30,
            searching: false,
            ordering: true,
            info: true,
            autoWidth: true, 
            processing: true,
            serverSide: true,
            ajax: {
                url: '/admin/settings/datatable',
                type: "GET"
            }, createdRow: function createdRow(row, data) {
                $(row).attr('data-id', data.id);
            },
            columns: [
                {
                    data: 'key',
                    name: 'key',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'description',
                    name: 'description',
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        var html = '<a class="action-button setting-edit-button" href="#" data-id="' + row.id + '"><span class="fa fa-pencil"></span></a>';
                        return html;
                    },
                    className: "actions",
                    defaultContent: '',
                    orderable: false,
                    searchable: false
                }
            ],
            "fnDrawCallback": function () {
                var paginateRow = $('.dataTables_paginate');
                var pageCount = Math.ceil((this.fnSettings().fnRecordsDisplay()) / this.fnSettings()._iDisplayLength);
                if (pageCount > 1) {
                    paginateRow.css("display", "block");
                } else {
                    paginateRow.css("display", "none");
                }
            }
        }
    );

}

(function ($) {

    $(document).on('click', '.task-batch-all', function () {
        $('.task-batch').prop('checked', $(this).is(':checked'));
    });

    $(document).on('click', '.task-batch', function () {
        var allChecked = $('.task-batch:checked').length == $('.task-batch').length;
        $('.task-batch-all').prop('checked', allChecked);
    });

    $(document).on('click', '.task-batch-all, .task-batch', function () {
        $('.btn-batch').toggle($('.task-batch:checked').length > 0);
    });

    $(document).on('click', '.btn-batch', function (e) {
        var ids = [],
            languages = [];
        $('.task-batch:checked').each(function () {
            ids.push($(this).val());
            languages.push($(this).attr('data-language'));
        });

        $('.hidden_task_ids').val(JSON.stringify(ids));

        e.preventDefault();
        var id = $(this).attr('data-id');

        var modal = $('#batch-modal');

        modal.addClass('control-sidebar-open');
        $('body').addClass('open');

        $('#edit-form-batch').find('select').val(null).trigger('change');

        if (languages.allValuesSame()) {

            $('#batch-assessor').removeClass('hidden');
            $('#no-assessor-change').addClass('hidden');

            modal.find('#batch-native').bootstrapSwitch({
                onText: 'Yes',
                offText: 'No'
            }).on('switchChange.bootstrapSwitch', function (event, state) {
                $.ajax({
                    url: '/project/language-assessors/1/' + languages[0] + '/native/' + Number(this.checked),
                    type: 'GET',
                    success: function (response) {
                        var result = JSON.parse(response);

                        var options = [];

                        $.each(result.assessors, function (key, value) {
                            options.push({id: key, text: value})
                        });

                        modal.find("select#batch_assessor_id").empty().select2({
                            data: options,
                            placeholder: 'Any',
                            allowClear: true
                        });

                    }
                });
            });

            $.ajax({
                url: '/project/language-assessors/1/' + languages[0],
                type: 'GET',
                success: function (response) {
                    result = JSON.parse(response);

                    var options = [{
                        id: '',
                        key: 'Select'
                    }];

                    $.each(result.assessors, function (key, value) {
                        options.push({id: key, text: value})
                    });

                    if (result.nativeButton) {
                        modal.find('#batch_native_parent').removeClass('hidden');
                        modal.find('select#batch_assessor_id').parent().removeClass('col-sm-12').addClass('col-sm-8');
                    } else {
                        modal.find('#batch_native_parent').addClass('hidden');
                        modal.find('select#batch_assessor_id').parent().removeClass('col-sm-8').addClass('col-sm-12');
                    }

                    modal.find('select#batch_assessor_id').empty().select2({
                        data: options,
                        placeholder: 'Any',
                        allowClear: true
                    });

                }
            });

        } else {

            $('#batch-assessor').addClass('hidden');
            $('#no-assessor-change').removeClass('hidden');
            
            // $('#batch-tests').addClass('hidden');
            // $('#no-test-type-change').removeClass('hidden');

        }

        /// get all available test types
        $.ajax({
            url: '/task/task-batch-get-test-types',
            headers: {'X-CSRF-TOKEN': _token},
            type: 'POST',
            data: {
                ids: ids
            },
            success:  function(res){

                result = res.data;

                var options = [{
                    id: '',
                    key: 'Select'
                }];

                $.each(result.test_types, function (key, value) {
                    options.push({id: key, text: value})
                });

                modal.find('select#batch_tests').empty().select2({
                    data: options,
                    placeholder: 'Any',
                    allowClear: true
                });
            }
        });


    });

    $(document).on('click', '#delete-batch-submit-button', function (e) {
        e.preventDefault();
        var btn = $(this);
        var form = btn.parents('form:first');
        swal({
            title: 'Are you sure you want to delete all selected tasks?',
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            focusConfirm: false
        }).then(function (result) {

            if (result.value) {

                AjaxCall({
                    url: form.attr('action'),
                    validate: false
                }).makeCall(btn, function () {
                    $('#task_status_filter').trigger('change');
                    $('#batch-modal').removeClass('control-sidebar-open');
                    $('.btn-batch').css('display', 'none');
                    $('body').removeClass('open');
                });

            }
        });

    });

    $(document).on('click', '.edit-batch-submit-button', function (e) {
        $('body > .loading').addClass('active');
        var form = $(this).parents('form:first');
        AjaxCall({
            url: form.attr('action')
        }).makeCall($(this), function () {
            $('#task_status_filter').trigger('change');
            $('#batch-modal').removeClass('control-sidebar-open');
            $('.btn-batch').css('display', 'none');
            $('body').removeClass('open');
            $('body > .loading').removeClass('active');
        });
    })

}(jQuery));

Array.prototype.allValuesSame = function () {

    for (var i = 1; i < this.length; i++) {
        if (this[i] !== this[0])
            return false;
    }

    return true;
};



$('.a-link').on('click', function (e) {
    window.location.href = $(this).attr('href');
});
$('.a-override').on('click', function (e) {

    if ($(e.target).hasClass('a-link')){
        e.preventDefault();
        e.stopPropagation();
        return false;
    }

});

function clearDatatablesStorage(){
    var arr = []; // Array to hold the keys
    // Iterate over localStorage and insert the keys that meet the condition into arr
    for (var i = 0; i < localStorage.length; i++){
        if (localStorage.key(i).substring(0,10) == 'DataTables') {
            arr.push(localStorage.key(i));
        }
    }

    // Iterate over arr and remove the items by key
    for (var i = 0; i < arr.length; i++) {
        localStorage.removeItem(arr[i]);
    }
}

