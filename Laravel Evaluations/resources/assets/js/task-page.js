var sliderChanged = false;

$('#email_lang_manager').on('click', function () {
    document.getElementById("email_manager_form").reset();
    document.getElementById('email_manager_modal').scrollTop = 0;
    // $('#email_manager_modal .loading').addClass('active');
    $('#email_manager_modal').addClass('control-sidebar-open');
    $('body').addClass('open');
});

if ($('select#edit-language').length) {
    $('select#edit-language').select2({
        allowClear: false
    });
}

if ($('select#edit-assessor').length) {
    $('select#edit-assessor').select2({
        allowClear: false
    });
}


var task_status = $('select#task_status');
if (task_status.length) {
    task_status.select2({
        allowClear: false,
        minimumResultsForSearch: -1
    }).parent().find('span.select2-selection')[0].setAttribute('style', 'background-color:' + taskStatus.color);

    if (['In Progress', 'Allocated', 'Issue'].indexOf(task_status.select2('data')[0].text) !== -1) {
        task_status.parent().find('span.select2-selection > span')[0].setAttribute('style', 'color: #4A4A4A;');
        task_status.parent().find('span.select2-selection .select2-selection__arrow > b')[0].setAttribute('style', 'border-color: #4A4A4A transparent;');
    } else {
        task_status.parent().find('span.select2-selection > span')[0].setAttribute('style', 'color: #fff;');
        task_status.parent().find('span.select2-selection .select2-selection__arrow > b')[0].setAttribute('style', 'border-color: #fff transparent;');
    }

    task_status.on('change', function () {
        var data = $(this).select2('data');

        task_status.parent().find('span.select2-selection')[0].setAttribute('style', 'background-color:' + taskStatusesColor[data[0].text]);

        if (['In Progress', 'Allocated', 'Issue'].indexOf($('select#task_status').select2('data')[0].text) !== -1) {
            task_status.parent().find('span.select2-selection > span')[0].setAttribute('style', 'color: #4A4A4A;');
            task_status.parent().find('span.select2-selection .select2-selection__arrow > b')[0].setAttribute('style', 'border-color: #4A4A4A transparent;');
        } else {
            task_status.parent().find('span.select2-selection > span')[0].setAttribute('style', 'color: #fff;');
            task_status.parent().find('span.select2-selection .select2-selection__arrow > b')[0].setAttribute('style', 'border-color: #fff transparent;');
        }

        $.ajax({
            url: '/task/' + task.id + '/update',
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {
                task_status_id: $(this).select2('data')[0].id
            },
            cache: false,
            dataType: 'json',
            success: function (res) {
                if (res.resType === 'Success') {

                    if (res.reloadPage && res.reloadPage == true) {
                        window.location.reload();
                    }

                    task_status.parent().find('span.select2-selection')[0].setAttribute('style', 'background-color:' + res.taskStatus.color);



                    if (res.log.type === "Task History") {
                        $('#task-history').prepend(
                            "<div class='row'>" +
                            "<div class='col-xs-12'>" + res.log.description +
                            "<span>" + dateFormat(new Date(res.log.created_at.replace(/-/g, "/")), "dd mmm yyyy") + " at " + dateFormat(new Date(res.log.created_at.replace(/-/g, "/")), "HH:MM") + " by " + res.user.first_name + " " + res.user.last_name + "</span>" +
                            "</div>" +
                            "</div>"
                        );
                    }
                } else {
                    var errors = '';
                    $.each(res.resMotive, function (key, value) {
                        errors += value[0] + '<br/>';
                    });
                    swal(
                        'Error!',
                        errors,
                        'error'
                    );
                }
            },
            error: function (res) {
                console.log(res);
            }
        });
    });
}




if ($('#task-tests-requested').length) {
    $.each(task.papers, function (key, value) {

        var paper_status = $('select#paper-status-' + value.id);
        paper_status.select2({
            allowClear: false,
            minimumResultsForSearch: -1
        }).parent().find('span.select2-selection')[0].setAttribute('style', 'background-color:' + value.status.color);

        if (['In Progress', 'Allocated', 'Issue'].indexOf(paper_status.select2('data')[0].text) !== -1) {
            paper_status.parent().find('span.select2-selection > span')[0].setAttribute('style', 'color: #4A4A4A;');
            paper_status.parent().find('span.select2-selection .select2-selection__arrow > b')[0].setAttribute('style', 'border-color: #4A4A4A transparent;');
        } else {
            paper_status.parent().find('span.select2-selection > span')[0].setAttribute('style', 'color: #fff;');
            paper_status.parent().find('span.select2-selection .select2-selection__arrow > b')[0].setAttribute('style', 'border-color: #fff transparent;');
        }

        var selOpt = paper_status.val();
        paper_status.on('change', function () {
            var data = $(this).select2('data');
            var id = $(this).attr('data-id');

            paper_status.parent().find('span.select2-selection')[0].setAttribute('style', 'background-color:' + taskStatusesColor[data[0].text]);

            if (['In Progress', 'Allocated', 'Issue'].indexOf(paper_status.select2('data')[0].text) !== -1) {
                paper_status.parent().find('span.select2-selection > span')[0].setAttribute('style', 'color: #4A4A4A;');
                paper_status.parent().find('span.select2-selection .select2-selection__arrow > b')[0].setAttribute('style', 'border-color: #4A4A4A transparent;');
            } else {
                paper_status.parent().find('span.select2-selection > span')[0].setAttribute('style', 'color: #fff;');
                paper_status.parent().find('span.select2-selection .select2-selection__arrow > b')[0].setAttribute('style', 'border-color: #fff transparent;');
            }

            $.ajax({
                url: '/task/' + task.id + '/update',
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
                    paper: {
                        id: paper_status.attr('data-id'),
                        status_id: $(this).select2('data')[0].id
                    }
                },
                cache: false,
                dataType: 'json',
                success: function (res) {
                    if (res.resType === 'Success') {
                        selOpt = $('#paper-status-' + id).val();


                        if ('log' in res) {
                            $('#task-history').prepend(
                                "<div class='row'>" +
                                "<div class='col-xs-12'>" + res.log.description +
                                "<span>" + dateFormat(new Date(res.log.created_at.replace(/-/g, "/")), "dd mmm yyyy") + " at " + dateFormat(new Date(res.log.created_at.replace(/-/g, "/")), "HH:MM") + " by " + res.user.first_name + " " + res.user.last_name + "</span>" +
                                "</div>" +
                                "</div>"
                            );
                        }

                        if (res.reloadPage && res.reloadPage == true) {
                            window.location.reload();
                        }

                    } else {
                        var errors = '';
                        $.each(res.resMotive, function (key, value) {
                            errors += value[0] + '<br/>';
                        });
                        swal(
                            'Error!',
                            errors,
                            'error'
                        );
                        $('#paper-status-' + id).val(selOpt).trigger('change');
                        stop();
                    }
                },
                error: function (res) {
                    console.log(res);
                }
            });
        });
    });
}
var speaking_slider_ability_aquired,
    writing_slider_ability_aquired;

if ($('#speaking-ability-aquired').length) {
    $('select#speaking-accent').select2({
        allowClear: true,
        placeholder: 'Details on accent and pronuntiation',
        minimumResultsForSearch: -1
    });

    speaking_slider_ability_aquired = new Slider('#speaking-ability-aquired', {
        min: 0,
        max: 100,
        value: 70,
        formatter: function (value) {
            return 'Ability: ' + value + '%';
        },
    });

    speaking_slider_ability_aquired.on("change", function (e) {

        sliderChanged = true;

        var value = speaking_slider_ability_aquired.getValue();
        if (value < 70) {
            speaking_slider_ability_aquired.setValue(70);
        }
        if (value == 100) {
            $('.speaking-slider.slider-2').removeClass('hidden');
        } else {
            $('.speaking-slider.slider-2').addClass('hidden');
        }
    });

    var speaking_slider_ability_next = new Slider('#speaking-ability-next', {
        min: 0,
        max: 100,
        value: 0,
        formatter: function (value) {
            return 'Ability: ' + value + '%';
        }
    });

    speaking_slider_ability_next.on("change", function (e) {
        sliderChanged = true;
        if (speaking_slider_ability_next.getValue() > 70) {
            speaking_slider_ability_next.setValue(70);
        }
    });


}

if ($('#writing-ability-aquired').length) {

    writing_slider_ability_aquired = new Slider('#writing-ability-aquired', {
        min: 0,
        max: 100,
        value: 70,
        formatter: function (value) {
            return 'Ability: ' + value + '%';
        }
    });

    writing_slider_ability_aquired.on("change", function (e) {

        sliderChanged = true;

        var value = writing_slider_ability_aquired.getValue();
        if (value < 70) {
            writing_slider_ability_aquired.setValue(70);
        }
        if (value == 100) {
            $('.writing-slider.slider-2').removeClass('hidden');
        } else {
            $('.writing-slider.slider-2').addClass('hidden');
        }
    });

    var writing_slider_ability_next = new Slider('#writing-ability-next', {
        min: 0,
        max: 100,
        value: 0,
        formatter: function (value) {
            return 'Ability: ' + value + '%';
        }
    });

    writing_slider_ability_next.on("change", function (e) {
        sliderChanged = true;
        if (writing_slider_ability_next.getValue() > 70) {
            writing_slider_ability_next.setValue(70);
        }
    });


}

if ($('select#task-update-action').length) {
    var task_reschedule = $('#task-update-reschedule');

    $('select#task-update-action').select2({
        allowClear: true,
        placeholder: 'Insert update...'
    }).on('change', function (e) {
        var _this = $(this);
        if (!_this.val()) {
            return;
        }
        var selectValue = _this.select2('data');
        if (!task.skype && selectValue[0] && selectValue[0].id == "contact-via-skype") {
            swal({
                title: '',
                type: 'warning',
                text: 'The user does not have a Skype ID!',
                customClass: 'swal2-overflow',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Cancel',
            });
            $('select#task-update-action').val('').trigger('change');
            return;
        }


        var swalOpts = {
            title: '',
            type: 'warning',
            text: 'Please confirm action',
            customClass: 'swal2-overflow',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirm',
            focusConfirm: false
        };

        if (_this.val() === "custom") {
            var customRow = $(".custom-update-row:not(.shown)");
            swalOpts.html = customRow[0].outerHTML;
            customRow.addClass("shown");
            swalOpts.type = "info";
        }

        swal(swalOpts).then(function (result) {
            if (result.value){
                var data = _this.select2('data');
                var eltText = data[0].text;
                if (data && data[0] && data[0].id === "custom") {
                    eltText = $(".custom-update-row:not(.shown)").find("textarea").val();
                }

                task_reschedule[0].style.display = 'none';

                if (data[0].id.length) {
                    if (data[0].id !== "reschedule") {
                        $.ajax({
                            url: '/task/' + task.id + '/request-updates',
                            type: 'POST',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            data: {
                                'description': eltText,
                                'status_slug': data[0].id
                            },
                            cache: false,
                            dataType: 'json',
                            success: function (res) {
                                if (res.resType === 'Success') {
                                    swal({
                                        type: 'success',
                                        title: 'Task update request was sent!',
                                        showConfirmButton: false,
                                        timer: 2500
                                    });

                                    $('select#task-update-action').val('').trigger('change');
                                    $(".custom-update-row").removeClass("shown");

                                    if ($('select#task_status option[value="'+res.task.task_status_id+'"]').length > 0) {
                                        $('select#task_status').val(res.task.task_status_id).trigger('change');
                                    } else if (res.reloadPage && res.reloadPage === true) {
                                        window.location.reload();
                                    }

                                    // No more task updates 2019-04-10
                                    $('#task-updates').find('.contents').prepend(
                                        "<div class='row'>" +
                                        "<div class='col-xs-12'>" + res.log.description +
                                        "<span>" + dateFormat(new Date(res.log.created_at.replace(/-/g, "/")), "dd mmm yyyy") + " at " + dateFormat(new Date(res.log.created_at.replace(/-/g, "/")), "HH:MM") + " by " + res.user.first_name + " " + res.user.last_name + "</span>" +
                                        "</div>" +
                                        "</div>"
                                    );

                                    // $('#task-history').prepend(
                                    //     "<div class='row'>" +
                                    //     "<div class='col-xs-12'>" + res.log.description +
                                    //     "<span>" + dateFormat(new Date(res.log.created_at.replace(/-/g, "/")), "dd mmm yyyy") + " at " + dateFormat(new Date(res.log.created_at.replace(/-/g, "/")), "HH:MM") + " by " + res.user.first_name + " " + res.user.last_name + "</span>" +
                                    //     "</div>" +
                                    //     "</div>"
                                    // );

                                } else {
                                    var errors = '';
                                    $.each(res.resMotive, function (key, value) {
                                        errors += value[0] + '<br/>';
                                    });
                                    swal(
                                        'Error!',
                                        errors,
                                        'error'
                                    );
                                }
                            },
                            error: function (res) {
                                console.log(res);
                            }
                        });
                    } else {
                        task_reschedule[0].style.display = 'block';
                    }
                }
            } else {
                _this.val(null).trigger('change');
                $(".custom-update-row").removeClass("shown");
            }
        });


    });

    task_reschedule.find('input#availability_from').datetimepicker({
        showClear: true,
        minDate: new Date(),
        format: 'MM/DD/YYYY'
    });

    var timepickerFrom = task_reschedule.find('input.timepick_from'),
        timepickerTo = task_reschedule.find('input.timepick_to'),
        rescheduler = task_reschedule.find('select.rescheduler')

    var defaultTimepickerConfig = {
        amPmText: ['', ''],
        hours: {
            starts: 8,
            ends: 20
        },
        minutes: {
            starts: 0,
            ends: 45,
            interval: 15
        },
        rows: 4
    };

    var fromConfig = $.extend({}, defaultTimepickerConfig);
    fromConfig.onClose = function (time, obj) {
        if (time === '') {
            return;
        }
        timepickerTo.timepicker('setTime', time);
        timepickerTo.timepicker('option', {minTime: {hour: obj.hours, minute: obj.minutes}});
    };
    fromConfig.onSelect = function(){
        timepickerTo.focus();
    };

    timepickerFrom.timepicker(fromConfig);
    timepickerTo.timepicker(defaultTimepickerConfig);

    task_reschedule.find('input#availability_from').on("dp.change", function (e) {
        var selectedDate = new Date(e.date.toString());
        var now = new Date();
        if (selectedDate.getFullYear() == now.getFullYear()
            && selectedDate.getMonth() == now.getMonth()
            && selectedDate.getDate() == now.getDate()) {
            timepickerFrom.timepicker('option', {
                minTime: {
                    hour: now.getHours(),
                    minute: now.getMinutes()
                }
            });
        } else {
            timepickerFrom.timepicker('option', {minTime: {hour: 0, minute: 0}});
        }
    });

    $('form#task-update-reschedule-form').submit(function (event) {
        event.preventDefault();

        form = $(this);

        form.show();
        form.validate({
            submitHandler: function (form, e, _this) {
                e.preventDefault();

                var description = $('#task-update-action').data('select2').data()[0].text + 'd for ';

                description += dateFormat(new Date(task_reschedule.find('input#availability_from')[0].value.replace(/-/g, "/")), "dd mmm yyyy");
                description += ' from ' + timepickerFrom[0].value;
                description += ' to ' + timepickerTo[0].value;
                description += ' by ' + rescheduler[0].value;


                $.ajax({
                    url: '/task/' + task.id + '/request-updates',
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        description: description,
                        reschedule: {
                            on: task_reschedule.find('input#availability_from')[0].value,
                            from: timepickerFrom[0].value,
                            to: timepickerTo[0].value,
                            rescheduler: rescheduler[0].value
                        }
                    },
                    cache: false,
                    dataType: 'json',
                    success: function (res) {
                        if (res.resType === 'Success') {
                            swal({
                                type: 'success',
                                title: 'Task update request was sent!',
                                showConfirmButton: false,
                                timer: 2500
                            });

                            $('select#task-update-action').val('').trigger('change');

                            // No more task updates 2019-04-10
                            $('#task-updates').find('.contents').prepend(
                                "<div class='row'>" +
                                "<div class='col-xs-12'>" + res.log.description +
                                "<span>" + dateFormat(new Date(res.log.created_at.replace(/-/g, "/")), "dd mmm yyyy") + " at " + dateFormat(new Date(res.log.created_at.replace(/-/g, "/")), "HH:MM") + " by " + res.user.first_name + " " + res.user.last_name + "</span>" +
                                "</div>" +
                                "</div>"
                            );

                            // $('#task-history').prepend(
                            //     "<div class='row'>" +
                            //     "<div class='col-xs-12'>" + res.log.description +
                            //     "<span>" + dateFormat(new Date(res.log.created_at.replace(/-/g, "/")), "dd mmm yyyy") + " at " + dateFormat(new Date(res.log.created_at.replace(/-/g, "/")), "HH:MM") + " by " + res.user.first_name + " " + res.user.last_name + "</span>" +
                            //     "</div>" +
                            //     "</div>"
                            // );

                            $('#task-update-reschedule').hide();

                            if (res.reloadPage && res.reloadPage == true) {
                                window.location.reload();
                            }

                        } else {
                            var errors = '';
                            $.each(res.resMotive, function (key, value) {
                                errors += value[0] + '<br/>';
                            });
                            swal(
                                'Error!',
                                errors,
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
    }).trigger('submit');
}

var task_show_more = $('.task-show-more');
if (task_show_more.length) {
    $('.task-show-more').on('click', function (e) {
        var contents = $(this).parent().find(".row:not('.task-show-more')");

        var rows = 5;
        $.each(contents, function (key, value) {
            if ($(value).hasClass('hidden')) {
                rows--;
                $(value).removeClass('hidden');

                if (rows === 0) {
                    return false;
                }
            }
        });

        if ($(this).parent().find(".row:not('.task-show-more').hidden").length === 0) {
            $(this).parent().find('.row.task-show-more').remove();
        }
    });
}

if ($('.task-edit-details').length) {
    $('.task-edit-details').on('click', function (e) {
        $(this).closest('.show-edit').find('.edit-html')[0].style.display = 'none';
        $(this).closest('.show-edit').find('.edit-input')[0].style.display = 'block';
        if($(this).closest('.show-edit').find('.edit-input').find('input.form-control').length){
            $(this).closest('.show-edit').find('.edit-input').find('input.form-control')[0].value = $(this).closest('.edit-html').find('#replace').text();
            var this_input = $(this).closest('.show-edit').find('.edit-input').find('input.form-control');
            this_input.focus();
            setTimeout(function () {
                this_input[0].selectionStart = this_input.selectionEnd = 10000;
            },100);
        }
        $(this).closest('.show-edit').find('label.error').remove();
    });
}

if ($('form.inline').length) {
    $('form.inline').submit(function (event) {
        event.preventDefault();

        form = $(this);

        form.show();
        form.validate({
            submitHandler: function (form, e, _this) {
                e.preventDefault();


                $('body > .loading').addClass('active');

                $.ajax({
                    url: '/task/' + task.id + '/update',
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: new FormData(form),
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (res) {
                        if (res.resType === 'Success') {
                            var edit_div = $(form).closest('.show-edit');
                            if ($(form).find('input').length > 1) {
                                edit_div.find('.edit-html').find('#replace')[0].innerText = $(form).find('input')[1].value;
                            } else {
                                edit_div.find('.edit-html').find('#replace')[0].innerText = $(form).find('select').select2('data')[0].text;
                            }
                            $('.phone-input-task-details').text(res.task.phone);
                            edit_div.find('.edit-html')[0].style.display = 'block';
                            edit_div.find('.edit-input')[0].style.display = 'none';
                            if ('log' in res) {
                                $('#task-history').prepend(
                                    "<div class='row'>" +
                                    "<div class='col-xs-12'>" + res.log.description +
                                    "<span>" + dateFormat(new Date(res.log.created_at.replace(/-/g, "/")), "dd mmm yyyy") + " at " + dateFormat(new Date(res.log.created_at.replace(/-/g, "/")), "HH:MM") + " by " + res.user.first_name + " " + res.user.last_name + "</span>" +
                                    "</div>" +
                                    "</div>"
                                );
                            }

                            if (res.reloadPage && res.reloadPage == true) {
                               window.location.reload();
                            }

                        } else {
                            var errors = '';
                            $.each(res.resMotive, function (key, value) {
                                errors += value[0] + '<br/>';
                            });
                            swal(
                                'Error!',
                                errors,
                                'error'
                            );
                        }
                    },
                    error: function (res) {
                        console.log(res);
                    },
                    complete: function(){
                        $('body > .loading').removeClass('active');
                    }
                });
            }
        });
    }).trigger('submit');

    $('.edit-input i.submit-form-inline-task').on('click', function (e) {
        var edit_div = $(this).closest('.show-edit');
        var form = edit_div.find('form');
        form.trigger('submit');
    });

    $('.edit-input i.fa-times-circle-o').on('click', function (e) {
        var edit_div = $(this).closest('.show-edit');
        edit_div.find('.edit-html')[0].style.display = 'block';
        edit_div.find('.edit-input')[0].style.display = 'none';
    });
}

var validator = null;

if ($('form.task-assessment-form').length) {
    $(document).on('click', '.form-submit-assessment', function (event) {
        if (validator !== null)
            validator.destroy();
        var form = $(this).parents('form:first');

        if (!sliderChanged && (form.find('.slider-1:visible').length || form.find('.slider-2:visible').length )) {
            swal({
                type: 'error',
                html: 'Please move the sliders'
            });
            return false;
        }

        form.show();
        validator = form.validate({
            submitHandler: function (form, e, _this) {
                e.preventDefault();
                var formData = new FormData(form);

                var assessments = $(form).find('li.active');
                if (assessments.length < $(form).find('>.row').length) {
                    swal(
                        'Error!',
                        'You need to set every criteria level.',
                        'error'
                    );
                    return false;
                }

                $.each(assessments, function (key, value) {
                    formData.append($(value).attr('data-skill'), $(value).attr('data-grade'));
                });

                $('body > .loading').addClass('active');
                $.ajax({
                    url: '/task/' + task.id + '/assessments',
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (res) {
                        if (res.resType === 'Success') {
                            var report_div = $(form).closest('.panel').parent();

                            swal({
                                type: 'success',
                                title: 'Assessment report submitted!',
                                showConfirmButton: false,
                                timer: 2500
                            });

                            $('select#paper-status-' + res.paper.id).val(res.paper.status_id).trigger('change');

                            report_div.slideToggle('slow');
                            location.reload();
                        } else {
                            var errors = '';
                            $.each(res.resMotive, function (key, value) {
                                errors += value[0] + '<br/>';
                            });
                            swal(
                                'Error!',
                                errors,
                                'error'
                            );
                        }
                        $('body > .loading').removeClass('active');
                    },
                    error: function (res) {
                        console.log(res);
                    }
                });
            }
        });

    }).trigger('submit');
}

if ($('#native-toggle').length) {
    $('#native-toggle').on('click', function () {
        var checked = $(this).attr('data-checked') == 'true';
        $(this).attr('data-checked', !checked);
        if (checked) {
            $(this).html('Native');
            $('.native-off').toggleClass('hidden', false);
            $('#native-user').toggleClass('hidden', true);
        } else {
            $(this).html('Native <i class="fa fa-check"></i>');
            $('.native-off').toggleClass('hidden', true);
            $('#native-user').toggleClass('hidden', false);
        }
    });

    $(document).ready(function(){
        var _this = $('#native-toggle');
        var checked = _this.attr('data-checked') == 'false';
        if (checked) {
            _this.html('Native');
            $('.native-off').toggleClass('hidden', false);
            $('#native-user').toggleClass('hidden', true);
        } else {
            _this.html('Native <i class="fa fa-check"></i>');
            $('.native-off').toggleClass('hidden', true);
            $('#native-user').toggleClass('hidden', false);
        }
    });
}

if ($('button#native-user').length) {
    $(document).on('click', 'button#native-user', function (e) {
        if (validator !== null) validator.destroy();
        var form = $(this).parents('form:first');

        var formData = {};
        $.each(form.serializeArray(), function(_, kv) {
            formData[kv.name] = kv.value;
        });
        formData.native = true;

        form.show();
        validator = form.validate({
            submitHandler: function(form, e, _this) {

                e.preventDefault();
                swal({
                    title: "Are you sure this candidate is a native speaker?",
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    focusConfirm: false
                }).then(function (result) {
                    if (result.value) {

                        var formData = {};
                        $.each($(form).serializeArray(), function(_, kv) {
                            formData[kv.name] = kv.value;
                        });
                        formData.native = true;

                        $('body > .loading').addClass('active');
                        $.ajax({
                            url: '/task/' + task.id + '/assessments',
                            type: 'POST',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            data: formData,
                            cache: false,
                            dataType: 'json',
                            success: function (res) {
                                if (res.resType === 'Success') {
                                    var report_div = $(form).closest('.panel').parent();

                                    swal({
                                        type: 'success',
                                        title: 'Assessment report submitted!',
                                        showConfirmButton: false,
                                        timer: 2500
                                    });

                                    $('select#paper-status-' + res.paper.id).val(res.paper.status_id).trigger('change');

                                    report_div.slideToggle('slow');
                                    location.reload();
                                } else {
                                    var errors = '';
                                    $.each(res.resMotive, function (key, value) {
                                        errors += value[0] + '<br/>';
                                    });
                                    swal(
                                        'Error!',
                                        errors,
                                        'error'
                                    );
                                }
                                $('body > .loading').removeClass('active');
                            },
                            error: function (res) {
                                console.log(res);
                            }
                        });
                    }
                });
            }
        });
    });
}

if ($('button#task-refuse').length) {
    $('button#task-refuse').on('click', function (e) {
        swal({
            title: "Are you sure?",
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            focusConfirm: false
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: '/task/' + task.id + '/refuse',
                    type: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        if (res.resType === 'success') {
                            // $('#replace.assessor')[0].innerText = res.data.assessor.first_name + ' ' + res.data.assessor.last_name;
                            $('button#task-refuse').remove();
                            swal({
                                type: 'success',
                                title: 'Task refused successfully',
                                showConfirmButton: true,
                                timer: 2500
                            }).then(function(){
                                window.location.href = '/tasks?all=true';
                            });
                        } else {
                            swal(
                                'Error!',
                                res.errMsg,
                                'error'
                            );
                        }
                    }
                });
            }
        });
    });
}
if ($('button#task-resend').length) {
    $('button#task-resend').on('click', function (e) {
        swal({
            title: "Are you sure?",
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            focusConfirm: false
        }).then(function (result) {
            if (result.value) {
                resendEmailInvitation(task.id);
            }
        });
    });
}

if ($('button#task-reset').length) {
    $('button#task-reset').on('click', function (e) {
        swal({
            title: "Are you sure?",
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            focusConfirm: false
        }).then(function (result) {
            if (result.value) {
                $('body > .loading').addClass('active');
                $.ajax({
                    url: '/task/' + task.id + '/reset',
                    type: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        if (res.resType === 'Success') {
                            swal({
                                type: 'success',
                                title: 'Online tests were reset!',
                                showConfirmButton: false,
                                timer: 2500
                            });
                            location.reload();
                        } else {
                            var errors = '';
                            $.each(res.resMotive, function (key, value) {
                                errors += value[0] + '<br/>';
                            });
                            swal(
                                'Error!',
                                errors,
                                'error'
                            );
                        }
                    },
                    complete: function(){
                        $('body > .loading').removeClass('active');
                    }
                });
            }
        });
    });
}

if ($('button#task-link-reset').length) {
    $('button#task-link-reset').on('click', function (e) {
        swal({
            title: "Are you sure?",
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            focusConfirm: false
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: '/task/' + task.id + '/reset-link',
                    type: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        if (res.resType === 'success') {
                            swal({
                                title: "The link was reset!",
                                text: "Do you want to resend email invitation?",
                                type: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes',
                                focusConfirm: false
                            }).then(function (result) {
                                resendEmailInvitation(task.id);
                            });
                        } else {
                            swal('Error!', 'Something went wrong, contact developer', 'error');
                        }
                    }
                });
            }
        });
    });
}

function resendEmailInvitation(task_id){
    $('body > .loading').addClass('active');
    $.ajax({
        url: '/task/' + task_id + '/resend-mail',
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            if (res.resType === 'success') {

                if ( res.data.log ) {
                    $('#task-history').prepend(

                        "<div class='row'>" +
                        "<div class='col-xs-12'>" + res.data.log.description +
                        "<span>" + dateFormat(new Date(res.data.log.created_at.replace(/-/g, "/")), "dd mmm yyyy") + " at " + dateFormat(new Date(res.data.log.created_at.replace(/-/g, "/")), "HH:MM") + " by " + res.data.user.first_name + " " + res.data.user.last_name + "</span>" +
                        "</div>" +
                        "</div>"
                    );
                }

                swal({
                    type: 'success',
                    title: 'Invitation mail was sent!',
                    text: 'Link expires at ' + res.data.link_expires_at,
                    showConfirmButton: false,
                    timer: 5000
                });

            } else {
                swal('Error!', 'Something went wrong, contact developer', 'error');
            }
        },
        complete: function(){
            $('body > .loading').removeClass('active');
        }
    });
}

if ($('button#task-test-here').length) {
    $('button#task-test-here').on('click', function (e) {
        swal({
            title: "Are you sure?",
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            focusConfirm: false
        }).then(function (result) {
            if (result.value) {
                window.location.href = "/test/instructions/" + task.link;
            }
        });
    });
}

if ($('button.btn-test-take-here-multiple').length) {
    $('button.btn-test-take-here-multiple').on('click', function (e) {
        var button = $(this);
        swal({
            title: "Are you sure?",
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            focusConfirm: false
        }).then(function (result) {
            if (result.value) {
                window.location.href = "/test/instructions/" + task.link + "/" + button.attr("data-type");
            }
        });
    });
}

if ($('button.btn-reset-report').length) {
    $('button.btn-reset-report').on('click', function (e) {
        var _this = $(this);
        swal({
            title: "Are you sure?",
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            focusConfirm: false
        }).then(function (result) {
            if (result.value) {
                $('body > .loading').addClass('active');
                $.ajax({
                    url: '/task/' + _this.attr('data-id') + '/reset-report',
                    type: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        if (res.resType === 'success') {
                            swal({
                                type: 'success',
                                title: res.data.type.name + ' test has been successfully reset.',
                                showConfirmButton: false,
                                timer: 2500
                            });
                            location.reload();
                        } else {
                            var errors = '';
                            $.each(res.resMotive, function (key, value) {
                                errors += value[0] + '<br/>';
                            });
                            swal(
                                'Error!',
                                errors,
                                'error'
                            );
                        }
                    },
                    complete: function () {
                        $('body > .loading').removeClass('active');
                    }
                });
            }
        });
    });
}

if ($('button.btn-reset-test').length) {
    $('button.btn-reset-test').on('click', function (e) {
        var _this = $(this);
        swal({
            title: "Are you sure?",
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            focusConfirm: false
        }).then(function (result) {
            if (result.value) {
                $('body > .loading').addClass('active');
                $.ajax({
                    url: '/task/' + _this.attr('data-id') + '/reset-test',
                    type: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        if (res.resType === 'success') {
                            swal({
                                type: 'success',
                                title: 'The test has been successfully reset.',
                                showConfirmButton: false,
                                timer: 2500
                            });
                            location.reload();
                        } else {
                            var errors = '';
                            $.each(res.resMotive, function (key, value) {
                                errors += value[0] + '<br/>';
                            });
                            swal(
                                'Error!',
                                errors,
                                'error'
                            );
                        }
                    },
                    complete: function () {
                        $('body > .loading').removeClass('active');
                    }
                });
            }
        });
    });
}

if ($('#task-tests-chart').length) {

    var labels = [];
    var backgroundColors = [];
    var paperGrades = [];
    var label = [];
    var tooltips = {};

    // console.log(papers);
    $.each(papers, function (key, paper) { 
        labels.push(paper.type.name);
            tooltips[paper.type.name] = paper.report.grade;

        switch (paper.type.id) {
            case TEST_LANGUAGE_USE_NEW:
            case TEST_LANGUAGE_USE:
                backgroundColors.push('#07a8e4');
                break;
            case TEST_SPEAKING:
                backgroundColors.push('#f01f1f');
                break;
            case TEST_WRITING:
                backgroundColors.push('#00b497');
                break;
            case TEST_LISTENING:
                backgroundColors.push('#ff8300');
                break;
            case TEST_READING:
                backgroundColors.push('#782aa9');
                break;
            default:
                backgroundColors.push('#782AA9');
                break;
        }

        paperGrades.push(paper.report.ability);

    });

    var grades = [
       '0', 'A1', 'A2', 'B1', 'B2', 'C1', 'C2', 'N'
    //   0   1      2     3     4    5     6     7
    ];

    var ctx = document.getElementById("task-tests-chart").getContext('2d');
    var chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: paperGrades,
                backgroundColor: backgroundColors
            }]
        },
        options: {
            scales: {
                xAxes: [{
                    gridLines: {
                        color: 'rgba(0, 0, 0, 0)'
                    },
                    ticks: {
                        autoSkip: false
                    }
                }],
                yAxes: [{
                    ticks: {
                        min: 0,
                        max: 7,
                        stepSize: 1,
                        beginAtZero: true,
                        callback: function (label, index, labels) {
                            return grades[Math.abs(label)];
                        }
                    }
                }]
            },
            tooltips: false,
            legend: {
                display: false
            },
            hover: false,
            animation: {
                duration: 1,
                onComplete: function () {
                    var chartInstance = this.chart,
                        ctx = chartInstance.ctx;
                    ctx.font = Chart.helpers.fontString(17, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'bottom';
                    ctx.fillStyle = 'white';
                    ctx.shadowBlur = 5;
                    ctx.shadowColor = "black";

                    this.data.datasets.forEach(function (dataset, i) {
                        var meta = chartInstance.controller.getDatasetMeta(i);
                        meta.data.forEach(function (bar, index) {
                            var data = tooltips[bar._model.label];
                            ctx.fillText(data, bar._model.x, bar._yScale.bottom - 1 );
                        });
                    });
                }
            }
        }
    });

}
/**
 * @deprecated 21-10-2019
 */
$('aside#tasks_filter form#tasks_filter_form').submit(function (event) {
    event.preventDefault();

    var project_id = $(this).find('#project_id')[0].value;
    var search_tasks = $(this).find('#search_tasks')[0].value;

    if (search_tasks.length === 0) {
        search_tasks = null;
    }

    $.ajax({
        url: '/project/' + project_id + '/filter-tasks-by-name/' + search_tasks,
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            var html = "";
            $.each(response.tasks, function (index, value) {
                html += "<li><a href='/task/" + index + "'>" + value + "</a></li>";
            });
            $('aside#tasks_filter ul.list-unstyled').html(html);
        }
    });
});

$('input#search_tasks').on('blur', function (e) {
    $('aside#tasks_filter form#tasks_filter_form').trigger('submit');
});

$("li[data-skill='general_descriptors']").on('click', function (e) {

    var grade = $(this).attr('data-grade');
    $(this).closest('.row').find("label.label-top")[0].innerHTML = 'Move the slider to indicate how much of the ' + grade + ' level the test-taker has aquired:';
    var slider1div = $(this).closest('.row').find('.slider-1');
    var slider2div = $(this).closest('.row').find('.slider-2');
    var grade_next = 'A1';

    if (speaking_slider_ability_aquired instanceof Slider) {
        speaking_slider_ability_aquired.setValue(70);
    }
    if (writing_slider_ability_aquired instanceof Slider) {
        writing_slider_ability_aquired.setValue(70);
    }

    if (grade == 'Pre-A1') {
        slider1div.addClass('hidden');
        slider2div.removeClass('hidden');
    } else if (grade.substr(-1, 1) == '+') {
        slider1div.addClass('hidden');
        slider2div.addClass('hidden');
    } else {
        slider1div.removeClass('hidden');
        slider2div.addClass('hidden');
    }


    switch (grade) {
        case 'Pre-A1':
            grade_next = 'A1';
            break;
        case 'A1':
            grade_next = 'A2';
            break;
        case 'A2':
            grade_next = 'B1';
            break;
        case 'A2+':
            grade_next = 'B1';
            break;
        case 'B1':
            grade_next = 'B2';
            break;
        case 'B2':
            grade_next = 'C1';
            break;
        case 'B2+':
            grade_next = 'C1';
            break;
        case 'C1':
            grade_next = 'C2';
            break;
        case 'C2':
            grade_next = 'Native';
            break;
    }
    $(this).closest('.row').find("label.label-bottom")[0].innerHTML = 'If the test-taker has aquired ' + grade_next + ' skills, move the slider below to indicate how much:';
});

if ($('#attachment').length) {
    $('#attachment').on('change', function (e) {
        e.preventDefault();
        var file = this.files[0];
        var formData = new FormData();
        formData.append('attachment', file);
        $.ajax({
            url: '/task/' + $(this).attr('data-task-id') + '/upload-attachment',
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: formData,
            contentType: false,
            processData: false,
            //Ajax events
            success: function (res) {
                window.location.reload();
            }
        });
    });
    $('.task-delete-attachment').on('click', function(e){
        e.preventDefault();
        var attachmentId = $(this).attr('data-attachment-id');
        swal({
            title: '',
            type: 'warning',
            text: 'Are you sure you want to delete this attachment?',
            customClass: 'swal2-overflow',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Delete',
            focusConfirm: false
        }).then(function (result) {
            if (result.value){
                $('body > .loading').addClass('active');
                $.ajax({
                    url: '/task/attachment/' + attachmentId + '/delete-attachment',
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    contentType: false,
                    processData: false,
                    //Ajax events
                    success: function (res) {
                        if (res.resType == 'success'){
                            window.location.reload();
                        } else {
                            swal({
                                type: 'error',
                                text: res.errMsg
                            });
                        }
                    },
                    complete: function(){
                        $('body > .loading').removeClass('active');
                    }
                });
            }
        });
    });
}

if ($('.task-edit-details-assessor').length) {
    $('.task-edit-details-assessor').on('click', function (e) {
        var modal = $('#assessor-modal');
        modal.find('.select2, .select2-multiple').select2({ width: '100%' });
        modal.modal('show');
    });
}
if ($('.task-edit-details-modal').length) {

    function checkEmergencySpeakingTaskPage(time) {
        var avbFrom = time !== undefined ? time : $('.timepick_from:visible').val();
        var speakingAvb = $('.timepick_from:visible').closest('.row').find('.speaking-availability').val();
        var dateToCheck = speakingAvb + ' ' + avbFrom;
        var date1 = new Date(dateToCheck);
        var date2 = new Date();

        var hours = Math.abs(date1 - date2) / 36e5;
        if (hours < 2) {
            swal({
                title: 'Attention',
                type: 'info',
                text: 'There are less than 2 hours until the speaking test. You have to call the Eucom Manager'
            });
            $('.add-test-submit').focus();
        }

    }

    $('.task-edit-details-modal').on('click', function (e) {
        var modal = $(this).parent().find('.modal');
        modal.find('.select2, .select2-multiple').select2({ width: '100%', showClear: true });
        modal.find('#deadline').datetimepicker({ minDate: new Date()});

        var defaultTimepickConfig = {
            defaultTime: '08:00',
            amPmText: ['', ''],
            hours: {
                starts: 8,
                ends: 20
            },
            minutes: {
                starts: 0,
                ends: 45,
                interval: 15
            },
            rows: 4
        };

        var timepickerFrom = modal.find('#timepick_from'),
            timepickerTo = modal.find('#timepick_to'),
            availabilityDataInput = modal.find('#availability_from');

        availabilityDataInput.datetimepicker({showClear: true,
            minDate: new Date(),
            format: 'MM/DD/YYYY'
        });

        var fromConfig = $.extend({}, defaultTimepickConfig);
        fromConfig.onClose = function (time, obj) {
            if (time === '') {
                availabilityDataInput.removeAttr('required');
                return;
            }
            availabilityDataInput.attr('required', true);

            checkEmergencySpeakingTaskPage(time);

            timepickerTo.timepicker('setTime', time);
            timepickerTo.timepicker('option', {minTime: {hour: obj.hours, minute: obj.minutes}});
        };

        var toConfig = $.extend({}, defaultTimepickConfig);
        toConfig.onClose = function (time, obj) {
            if (time === '') {
                availabilityDataInput.removeAttr('required');
                return;
            }

            availabilityDataInput.attr('required', true);

            if(!timepickerFrom.val()) {
                timepickerFrom.timepicker('setTime', time);
            }
        };

        timepickerFrom.timepicker(fromConfig);
        timepickerTo.timepicker(toConfig);

        availabilityDataInput.on("dp.change", function (e) {
            var selectedDate = new Date(e.date.toString());
            var now = new Date();
            if (selectedDate.getFullYear() == now.getFullYear()
                && selectedDate.getMonth() == now.getMonth()
                && selectedDate.getDate() == now.getDate()) {
                timepickerFrom.timepicker('option', {
                    minTime: {
                        hour: now.getHours(),
                        minute: now.getMinutes()
                    }
                });
            } else {
                timepickerFrom.timepicker('option', {minTime: {hour: 0, minute: 0}});
            }
            checkEmergencySpeakingTaskPage();
        });

        modal.modal('show');
    });
}

$(document).on('click', '.submit-modal-form', function(e){
    e.preventDefault();
    var form = $(this).closest('.modal').find('form'),
        action = form.attr('action'),
        type = form.attr('method'),
        formData = new FormData(form[0]),
        actionType = $(this).attr('data-action-type');

    switch (actionType) {
        case 'followers':
            if (form.find('#follower_id').length && form.find('#follower_id').val().length === 0){
                formData.append('followers[]', []);
            }
            break;
        case 'assessor':
            formData.delete('project_type_id');
            formData.delete('language_id');
            if (form.find('#assessor_id').val() == null) {
                return;
            }
            break;
        case 'add-test':
            formData.delete('project_type_id');
            formData.delete('language_id');
            break;
        default: break;
    }

    $('body > .loading').addClass('active');
    $.ajax({
        url: action,
        type: type,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: formData,
        contentType: false,
        processData: false,
        //Ajax events
        success: function (res) {

            switch (actionType) {
                case 'followers':
                    $('#followers-list').html(res.data.followers.join(', '));
                    $('#followers-modal').modal('hide');
                    break;
                case 'assessor':
                    window.location.reload();
                    break;
                case 'add-test':
                    window.location.reload();
                    break;
                default:
                    if (res.data.reloadPage && res.data.reloadPage == true) {
                        window.location.reload();
                    }
                    break;
            }
        },
        complete: function(){
            $('body > .loading').removeClass('active');
        }
    });

});

if ($('.add-test-type-task').length) {
    $('.add-test-type-task').on('change', function(e){
        e.preventDefault();
        var testTypes = $(this).val().map(function (x) {
            return parseInt(x, 10);
        });
        if (testTypes.indexOf(TEST_READING) >= 0
            || testTypes.indexOf(TEST_LISTENING) >= 0
            || testTypes.indexOf(TEST_LANGUAGE_USE) >= 0
            || testTypes.indexOf(TEST_LANGUAGE_USE_NEW) >= 0
            || testTypes.indexOf(TEST_WRITING) >= 0
        ) {
            $(this).closest('.modal-body').find('.deadline-container').removeClass('hidden');
        } else {
            $(this).closest('.modal-body').find('.deadline-container').addClass('hidden');
        }

        var avbContainer =  $(this).closest('.modal-body').find('.availability-container'),
            assessorContainer = $(this).closest('.modal-body').find('.assessor-container');

        // availability
        if (testTypes.indexOf(TEST_SPEAKING) >= 0) {
            avbContainer.removeClass('hidden');
        } else {
            avbContainer.addClass('hidden');
        }

        // assessor
        $('#appended_row').remove();
        if (testTypes.indexOf(TEST_SPEAKING) >= 0 || testTypes.indexOf(TEST_WRITING) >= 0) {
            var assessorRow = $('<div id="appended_row" class="row"></div>');
            assessorRow.html($('#assessor-form').html());
            assessorContainer.append(assessorRow);
            assessorContainer.find('.select2, .select2-multiple').select2({ width: '100%' });
            assessorContainer.removeClass('hidden');
        } else {
            assessorContainer.addClass('hidden');
        }

    });
}

if ($('.task-delete-test').length) {
    $('.task-delete-test').on('click', function(e){
        e.preventDefault();
        var paperId = $(this).attr('data-paper-id');
        swal({
            title: '',
            type: 'warning',
            text: 'Are you sure you want to delete this test type?',
            customClass: 'swal2-overflow',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Delete',
            focusConfirm: false
        }).then(function (result) {
            if (result.value){
                $('body > .loading').addClass('active');
                $.ajax({
                    url: '/task/' + paperId + '/delete-test',
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    contentType: false,
                    processData: false,
                    //Ajax events
                    success: function (res) {
                        if (res.resType == 'success'){
                            window.location.reload();
                        } else {
                            swal({
                                type: 'error',
                                text: res.errMsg
                            });
                        }
                    },
                    complete: function(){
                        $('body > .loading').removeClass('active');
                    }
                });
            }
        });
    });
}

if ($('.task-delete-log').length) {
    $('.task-delete-log').on('click', function(e){
        e.preventDefault();
        var logId = $(this).attr('data-log-id');
        var _this = $(this);
        swal({
            title: '',
            type: 'warning',
            text: 'Are you sure you want to delete this entry?',
            customClass: 'swal2-overflow',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Delete',
            focusConfirm: false
        }).then(function (result) {
            if (result.value){
                $('body > .loading').addClass('active');
                $.ajax({
                    url: '/task/' + logId + '/delete-log',
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    contentType: false,
                    processData: false,
                    //Ajax events
                    success: function (res) {
                        if (res.resType == 'success'){
                            _this.closest('.row').remove();
                        } else {
                            swal({
                                type: 'error',
                                text: res.errMsg
                            });
                        }
                    },
                    complete: function(){
                        $('body > .loading').removeClass('active');
                    }
                });
            }
        });
    });
}


if ($('#edit-native').length) {
    $(document).on('change', '#edit-native', function(e){
        var form = $(this).parents('form:first'),
            project_type_id = form.find('input[name="project_type_id"]').val(),
            language_id = form.find('input[name="language_id"]').val(),
            native = $(this).val();
        $('body > .loading').addClass('active');
        $.ajax({
            url: '/project/language-assessors/' + project_type_id + '/' + language_id + '/native/' + native,
            type: 'GET',
            success: function (response) {

                if( form.find('select#assessor_id').length === 0 ){
                    return;
                }

                result = JSON.parse(response);

                var options = [];

                $.each(result.assessors, function (key, value) {
                    options.push({id: key, text: value})
                });

                var assessor = form.find('select#assessor_id')[0].getAttribute('data-id');

                form.find('select#assessor_id').empty().select2({
                    data: options,
                    placeholder: 'Any',
                    allowClear: true
                });

                form.find('select#assessor_id').val(assessor).trigger('change');
                form.find('#assessor_id')[0].setAttribute('data-id', assessor);
            },
            complete: function(){
                $('body > .loading').removeClass('active');
            }
        });
    });
}

if ($('.deadline-datepick').length){
    $('.deadline-datepick').datetimepicker({ minDate: new Date()});
}

var taskPage;

(function($) {
    var TaskPage = function() {
        var self = this;

        this.ready = function() {
            this.handleDOM();
            this.handleEvents();
        };

        this.handleDOM = function() {
            this.billClientSelect = $(".bill-client-select");
            this.billClientAmount = $(".bill-amount-container");
        };

        this.handleEvents = function () {
            this.billClientSelect.on("change", this.handleBillClientSelect.bind(this));
            $(document).on("change", ".has_custom_period", this.handleCustomPeriodToggle);
        };

        this.handleCustomPeriodToggle = function () {
            var parent = $(this).parents(".availability_parent");
            if ($(this).is(":checked")) {
                parent.find("label[for=availability]").text("Custom period for Speaking Test (day)");
                parent.find("label[for=availability_time]").text("Custom period for Speaking Test (time)");
            } else {
                parent.find("label[for=availability]").text("Availability for Speaking Test (day)");
                parent.find("label[for=availability_time]").text("Availability for Speaking Test (time)");
            }
        };
        this.handleBillClientSelect = function() {
            if (this.billClientSelect.val() == 1) {
                this.billClientAmount.removeClass("hidden");
            } else {
                this.billClientAmount.addClass("hidden");
            }
        };
    };

    taskPage = new TaskPage();
    $(document).ready(function () {
        taskPage.ready();
    });
})(jQuery);

