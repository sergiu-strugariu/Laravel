//project page - tasks list
// test comm

$('.add-new-language').click(function () {
    $('#language_id').focus();
});

var defaultTimepickerConfig = {
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

moment.updateLocale('en', {
    week: { dow: 1 } // Monday is the first day of the week
});


function checkEmergencySpeaking(time) {

    var avbFrom = time !== undefined ? time : $('.timepick_from:visible').val();
    var speakingAvb = $('.timepick_from:visible').closest('.tab-pane').find('.speaking-availability').val();
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
        $('#add-submit-button').focus();
    }

}

$(document).on('change', '.language-use-new', function(e){

    if(!$(this).is(":checked")){
        $(this).prop('checked', false);
    }else{
        $(this).prop('checked', true);
        $(this).closest('.lang_paper_types').find('.language-use').prop('checked', false);
        $(this).closest('.edit-paper-types').find('.language-use').prop('checked', false);
    }
});

$(document).on('change', '.language-use', function(e){
    if(!$(this).is(":checked")){
        $(this).prop('checked', false);
    }else{
        $(this).prop('checked', true);
        $(this).closest('.lang_paper_types').find('.language-use-new').prop('checked', false);
        $(this).closest('.edit-paper-types').find('.language-use-new').prop('checked', false);
    }

});

$(document).ready(function(){
    
    $('.add-new-language').hide();

    $('select#task_status_filter').select2({
        placeholder: 'All Tasks',
        allowClear: true
    });
    $('select#name_filter').select2({
        placeholder: 'Any Name',
        allowClear: true,
        tags: true
    });
    $('select#id_filter').select2({
        placeholder: 'ID Filter',
        allowClear: true,
        tags: true
    });
    $('select#language_filter').select2({
        placeholder: 'Any Language',
        allowClear: true
    });
    $('select#assessor_filter').select2({
        placeholder: 'Any Assessor',
        allowClear: true
    });
    $('select#added_by_filter').select2({
        placeholder: 'Any User',
        allowClear: true
    });

    $('select#has_unbilled_tests').select2({
        placeholder: 'Has Unbilled Tests',
        allowClear: true
    });

    $('input#created_at_filter').datetimepicker({
        showClear: true,
        format: 'MM/DD/YYYY'
    });

    $("#edit-modal #native").bootstrapSwitch({
        onText: 'Yes',
        offText: 'No'
    });

    var modals = $('.control-sidebar');
    modals.find('select').select2();
    modals.find('input#deadline').datetimepicker({showClear: true, minDate: new Date()});

    modals.find('input#availability_from').datetimepicker({
        showClear: true,
        minDate: new Date(),
        format: 'MM/DD/YYYY'
    });

    // modals.find('input#availability_from').on("dp.change", function (e) {
    //     $('#deadline').data("DateTimePicker").minDate(e.date);
    // });
    //
    modals.find('input#availability_to').on("dp.change", function (e) {
        $('#availability_from').data("DateTimePicker").maxDate(e.date);
        // $('#deadline').data("DateTimePicker").minDate(e.date);
    });

    var edit_modal = $("#edit-modal");

    $(document).on('click', 'input.requires-availability', function (e) {

        if ($(this).is(':checked')) {
            $(this).closest('.tab-pane').find('.availability_parent').removeClass('hidden');
            $(this).closest('.tab-pane').find('.availability_parent input').removeClass('ignore-validation');
        } else {
            $(this).closest('.tab-pane').find('.availability_parent').addClass('hidden');
            $(this).closest('.tab-pane').find('.availability_parent input').addClass('ignore-validation');

        }
    });

    $(document).on('change', 'input.requires-assessor', function (e) {

        var writingCheckbox = $(this).closest('.lang_paper_types').find('.test-type-' + TEST_WRITING),
            speakingCheckbox = $(this).closest('.lang_paper_types').find('.test-type-' + TEST_SPEAKING);

        if (writingCheckbox.is(':checked') || speakingCheckbox.is(':checked')) {
            $(this).closest('.tab-pane').find('.assessor-parent').removeClass('hidden');
        } else {
            $(this).closest('.tab-pane').find('.assessor-parent').addClass('hidden');
        }

    });

    $(document).on('click', 'input.requires-deadline', function (e) {
        if ($(this).is(':checked')) {
            $(this).closest('.tab-content').find('.deadline_parent').removeClass('hidden');
        } else {
            if ($(this).closest('.tab-content').find('.requires-deadline:checked').length === 0) {
                $(this).closest('.tab-content').find('.deadline_parent').addClass('hidden');
            }
        }
    });

    $(document).on('blur', '.deadline-input', function (e) {
        var val = $(this).val();
        $.each($('.deadline-input'), function(){
            if (!$(this).val()) {
                $(this).val(val);
            }
        })
    });

    modals.find('select#language_id').on('change', function () {
        var projectId = $(this).parents("form").find("#project_id").val();
        if (this.hasAttribute('multiple')) { // on add task

            var selectedLanguages = $(this).select2('data');

            $.each($("#add-modal .nav.nav-tabs").children(), function (key, element) {
                var flag = false;

                $.each(selectedLanguages, function (k, language) {
                    if (element.id.split("-")[1] == language.id) {
                        flag = true;
                    }
                });

                if (flag == false && element.id) {
                    $("#add-modal .nav.nav-tabs li#" + element.id).remove();
                    $("#add-modal .tab-content #add-task-" + element.id).remove();
                }
            });

            $.each(selectedLanguages, function (key, language) {

                if ($("#add-modal .nav.nav-tabs li#language-" + language.id).length == 0) {
                    var name = language.text.substring(0, 2);

                    $("#add-modal .nav.nav-tabs").append(
                        "<li id='language-" + language.id + "'>" +
                        "<a data-toggle='tab' href='#add-task-language-" + language.id + "'>" + name +
                        //                                "<button type='button' class='close'>×</button>" +
                        "</a>" +
                        "</li>"
                    );

                    //check there are multiple languages to copy deadline from first lang
                    var deadlineVal = '';
                    if (selectedLanguages.length > 1){
                        deadlineVal = $('input[name="languagesExtra['+selectedLanguages[0].id+'][deadline]"]').val();
                    }

                    $("#add-modal .tab-content").append($("#add-modal #add-task-language").clone());
                    var content = $("#add-modal .tab-content #add-task-language");
                    content.removeClass('hidden').attr('id', "add-task-language-" + language.id);
                    content.find('#deadline').attr("name", "languagesExtra[" + language.id + "][deadline]").attr('id', 'deadline'+language.id).val(deadlineVal);
                    content.find('#availability_from').attr("name", "languagesExtra[" + language.id + "][availability_from]").attr('id', 'availability_from'+language.id);
                    content.find('.timepick_from').attr("name", "languagesExtra[" + language.id + "][from_date]").attr('id', 'timepick_from'+language.id);
                    content.find('.timepick_to').attr("name", "languagesExtra[" + language.id + "][to_date]").attr('id', 'timepick_to'+language.id);
                    content.find('.has_custom_period').attr("name", "languagesExtra[" + language.id + "][has_custom_period]").attr('id', 'has_custom_period'+language.id);
                    content.find('#paper-type-1').attr("name", "languagesExtra[" + language.id + "][PaperTypes][1]");
                    content.find('#paper-type-2').attr("name", "languagesExtra[" + language.id + "][PaperTypes][2]");
                    content.find('#paper-type-3').attr("name", "languagesExtra[" + language.id + "][PaperTypes][3]");
                    content.find('#paper-type-4').attr("name", "languagesExtra[" + language.id + "][PaperTypes][4]");
                    content.find('#paper-type-5').attr("name", "languagesExtra[" + language.id + "][PaperTypes][5]");

                    content.find('input#deadline'+language.id).datetimepicker({showClear: true, minDate: new Date()}).on('dp.change', function (ev) {
                        $(this).datetimepicker('hide');
                    });

                    content.find('input#availability_from'+language.id).datetimepicker({
                        showClear: true,
                        minDate: new Date(),
                        format: 'MM/DD/YYYY'
                    });

                    var timepickerFrom = content.find('input.timepick_from'),
                        timepickerTo = content.find('input.timepick_to');

                    var availabilityDataInput = $('#availability_from'+language.id);

                    var fromConfig = $.extend({}, defaultTimepickerConfig);
                    fromConfig.onClose = function (time, obj) {
                        if (time === '') {
                            availabilityDataInput.removeAttr('required');
                            return;
                        }
                        availabilityDataInput.attr('required', true);

                        checkEmergencySpeaking(time);

                        timepickerTo.timepicker('setTime', time);
                        timepickerTo.timepicker('option', {minTime: {hour: obj.hours, minute: obj.minutes}});
                        // if(!availabilityDataInput.val()) {
                        //     availabilityDataInput.data("DateTimePicker").date(new Date());
                        // }

                    };

                    var toConfig = $.extend({}, defaultTimepickerConfig);
                    toConfig.onClose = function (time, obj) {
                        if (time === '') {
                            availabilityDataInput.removeAttr('required');
                            return;
                        }

                        availabilityDataInput.attr('required', true);

                        if(!$('#timepick_from'+language.id).val()) {
                            timepickerFrom.timepicker('setTime', time);
                        }
                        // if(!availabilityDataInput.val()) {
                        //     availabilityDataInput.data("DateTimePicker").date(new Date());
                        // }
                    };

                    timepickerFrom.timepicker(fromConfig);
                    timepickerTo.timepicker(toConfig);

                    content.find('input#availability_from'+language.id).on("dp.change", function (e) {
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
                        // $('#deadline'+language.id).data("DateTimePicker").minDate(e.date);
                        checkEmergencySpeaking();
                    });


                    content.find('input.requires-availability').on('click', function (e) {
                        if ($(this).is(':checked')) {
                            content.find('div.availability_parent').removeClass('hidden');
                            content.find('div.availability_parent input').removeClass('ignore-validation');
                        } else {
                            content.find('div.availability_parent').addClass('hidden');
                            content.find('div.availability_parent input').addClass('ignore-validation');
                        }
                    });

                    content.find('input.requires-deadline').on('click', function (e) {
                        if ($(this).is(':checked')) {
                            content.find('div#deadline_parent').removeClass('hidden');
                        } else {
                            if (content.find('input.requires-deadline:checked').length === 0) {
                                content.find('div#deadline_parent').addClass('hidden');
                            }
                        }
                    });

                    content.find('#assessor_id').attr("name", "languagesExtra[" + language.id + "][assessor_id]");
                    var bsSwitch = content.find('#native').attr("name", "languagesExtra[" + language.id + "][native]").bootstrapSwitch({
                        onText: 'Yes',
                        offText: 'No'
                    }).on('switchChange.bootstrapSwitch', function (event, state) {
                        var checkedValue = Number(this.checked);
                        $.ajax({
                            url: '/project/language-assessors/' + projectType + '/' + language.id + '/native/' + checkedValue,
                            type: 'GET',
                            success: function (response) {

                                result = JSON.parse(response);

                                var options = [];

                                $.each(result.assessors, function (key, value) {
                                    options.push({id: key, text: value})
                                });

                                var assessor = content.find("select[name='languagesExtra[" + language.id + "][assessor_id]']")[0].getAttribute('data-id');

                                modals.find("select[name='languagesExtra[" + language.id + "][assessor_id]']").empty().select2({
                                    data: options,
                                    placeholder: 'Any',
                                    allowClear: true
                                });
                                bsSwitch.bootstrapSwitch('state', result.nativeState);
                                bsSwitch.bootstrapSwitch('readonly', result.nativeDisabled);

                                content.find("select[name='languagesExtra[" + language.id + "][assessor_id]']").val(assessor).trigger('change');
                                content.find("select[name='languagesExtra[" + language.id + "][assessor_id]']")[0].setAttribute('data-id', assessor);

                                if(assessor == null){
                                    content.find("select[name='languagesExtra[" + language.id + "][assessor_id]']").val(options[0].id).trigger('change');
                                }

                                var container = $('#add-task-language-' + language.id);

                                if (checkedValue == 1) {
                                    container.find("span.native").removeClass("hidden");
                                    container.find("span.non-native").addClass("hidden");
                                } else {
                                    container.find("span.native").addClass("hidden");
                                    container.find("span.non-native").removeClass("hidden");
                                }

                                $.each(result.languagePaperTypes, function (index, val) {
                                    //TODO
                                });

                            }
                        });
                    });

                    $.ajax({
                        url: '/project/language-assessors/' + projectType + '/' + language.id,
                        data: {
                            project_id: projectId
                        },
                        type: 'GET',
                        success: function (response) {
                            result = JSON.parse(response);

                            var options = [];

                            $.each(result.assessors, function (key, value) {
                                options.push({id: key, text: value})
                            });

                            if (result.nativeButton) {
                                content.find('#native_parent').removeClass('hidden');
                                content.find('select#assessor_id').parent().removeClass('col-sm-12').addClass('col-sm-8');
                            } else {
                                content.find('#native_parent').addClass('hidden');
                                content.find('select#assessor_id').parent().removeClass('col-sm-8').addClass('col-sm-12');
                            }


                            bsSwitch.bootstrapSwitch('state', result.nativeState);
                            bsSwitch.bootstrapSwitch('readonly', result.nativeDisabled);

                            content.find('select#assessor_id').empty().select2({
                                data: options,
                                placeholder: 'Any',
                                allowClear: true
                            });

                            content.find('select#assessor_id').trigger('change');
                            $('#add-modal .nav.nav-tabs').append($('.add-new-language'));
                            $('.add-new-language').show();


                            var container = $('#add-task-language-' + language.id).find('.lang_paper_types');
                            container.html('');
                            var hasLangUseNew = false;
                            var order = [6,1,2,3,4,5];
                            if (result.languagePaperTypes) {
                                result.languagePaperTypes.sort(function(a, b) {
                                   return order.indexOf(a.paper_types.id) - order.indexOf(b.paper_types.id)
                                });
                            }

                            $.each(result.languagePaperTypes, function (index, test_type) {

                                var classSpeaking = '';
                                if (test_type.paper_types.name == 'Speaking') {
                                    classSpeaking = 'requires-availability';
                                } else {
                                    classSpeaking = 'requires-deadline';
                                }

                                if(test_type.paper_types.id == TEST_SPEAKING || test_type.paper_types.id == TEST_WRITING){
                                    classSpeaking += ' requires-assessor';
                                }

                                var newCheckbox;
                                var currencyUsed = "EUR";
                                var paperTypeCost = 0;
                                var paperTypeCostString = "";
                                if (result.prices &&
                                    result.paperTypeToPricingType &&
                                    result.paperTypeToPricingType[test_type.paper_type_id] &&
                                    result.prices[test_type.language_id] &&
                                    result.prices[test_type.language_id][result.paperTypeToPricingType[test_type.paper_type_id]] &&
                                    result.prices[test_type.language_id][result.paperTypeToPricingType[test_type.paper_type_id]].price !== "0.00"
                                ) {
                                    paperTypeCostString = result.prices[test_type.language_id][result.paperTypeToPricingType[test_type.paper_type_id]].price + " " + currencyUsed;

                                    if (test_type.paper_type_id == TEST_SPEAKING) {
                                        var nativeCost =  result.prices[test_type.language_id][3].price + " " + currencyUsed;
                                        paperTypeCostString = "<span class='non-native'>"+paperTypeCostString+"</span><span class='native hidden'>"+nativeCost+"</span>";
                                    } else if (test_type.paper_type_id == TEST_WRITING) {
                                        var nativeCost =  result.prices[test_type.language_id][1].price + " " + currencyUsed;
                                        paperTypeCostString = "<span class='non-native'>"+paperTypeCostString+"</span><span class='native hidden'>"+nativeCost+"</span>";
                                    }
                                } else {
                                    return;
                                }

                                if(test_type.paper_type_id == 1){
                                    hasLangUseNew = true;
                                    newCheckbox = $('<div class="col-sm-12 text-left">' +
                                            '<input data-test-id="'+test_type.paper_type_id+'" data-test-name="'+test_type.paper_types.name+'" type="checkbox" id="paper-type-' + test_type.paper_type_id + '-' + language.id + '" name="languagesExtra[' + language.id + '][PaperTypes][' + test_type.paper_type_id + ']" value="1" class="test-type-' + test_type.paper_type_id + ' language-use-new ' + classSpeaking + '">' +
                                            '<label class="paper-type-label" for="paper-type-' + test_type.paper_type_id + '-' + language.id + '">' + test_type.paper_types.name + '</label>' +
                                        (paperTypeCostString ? '<span class="paper-cost">' + paperTypeCostString + '</span>' : "") +
                                        '</div>');
                                } else {
                                    newCheckbox = $('<div class="col-sm-12 text-left">' +
                                            '<input data-test-id="'+test_type.paper_type_id+'" data-test-name="'+test_type.paper_types.name+'" type="checkbox" id="paper-type-' + test_type.paper_type_id + '-' + language.id + '" name="languagesExtra[' + language.id + '][PaperTypes][' + test_type.paper_type_id + ']" value="1" class="test-type-' + test_type.paper_type_id + ' ' + classSpeaking + '">' +
                                            '<label class="paper-type-label" for="paper-type-' + test_type.paper_type_id + '-' + language.id +'">' + test_type.paper_types.name + '</label>' +
                                            (paperTypeCostString ? '<span class="paper-cost">' + paperTypeCostString  +'</span>' : "") +
                                        '</div>');
                                }

                                if(test_type.paper_type_id == 6 && hasLangUseNew){
                                    newCheckbox  =  $('<div class="col-sm-12 text-left">' +
                                            '<input data-test-id="'+test_type.paper_type_id+'" data-test-name="'+test_type.paper_types.name+'" type="checkbox" id="paper-type-' + test_type.paper_type_id + '-' + language.id + '" name="languagesExtra[' + language.id + '][PaperTypes][' + test_type.paper_type_id + ']" value="1" class="test-type-' + test_type.paper_type_id + ' language-use ' + classSpeaking + '"> ' +
                                            '<label class="paper-type-label" for="paper-type-' + test_type.paper_type_id + '-' + language.id + '">' + test_type.paper_types.name + '</label>' +
                                            (paperTypeCostString ? '<span class="paper-cost">' + paperTypeCostString + '</span>' : "") +
                                        '</div>');
                                }

                                if(test_type.paper_type_id == TEST_SPEAKING) {
                                    var tabPane = $('#add-task-language-' + language.id);
                                    var cpToggle = tabPane.find(".custom-period-toggle");

                                    if (result.prices &&
                                        result.prices[test_type.language_id] &&
                                        result.prices[test_type.language_id][9] &&
                                        result.prices[test_type.language_id][9].price !== "0.00"
                                ) {
                                        var cpString = "+"  + result.prices[test_type.language_id][9].price + " " + currencyUsed;
                                        cpToggle.find(".cost").text(cpString);
                                        cpToggle.show();
                                    } else {
                                        cpToggle.hide();
                                    }
                                }

                                    container.append(newCheckbox);
                            });


                            if (content.find('input.requires-availability:checked').length) {
                                content.find('div.availability_parent').removeClass('hidden');
                                content.find('div.availability_parent input').removeClass('ignore-validation');
                            }

                            if (content.find('input.requires-assessor').length) {
                                content.find('.assessor-parent').removeClass('hidden');
                            } else {
                                content.find('.assessor-parent').addClass('hidden');
                            }


                        }
                    });
                }
            });

            if ($("#add-modal .nav.nav-tabs").length != 0 && $("#add-modal .nav.nav-tabs .active").length == 0) {
                $("#add-modal .nav.nav-tabs li:not(:first-child)").addClass("active");
                $("#add-modal .tab-content div:first-child").addClass("active in");
                if ($('.add-new-language').hasClass('active')) {
                    $('.add-new-language').removeClass('active');
                    $("#add-modal .nav.nav-tabs li:not(:nth-child(2))").addClass("active");
                }
            }

            if ($("#add-modal .nav.nav-tabs li").length == 1) {
                $("#add-modal .nav.nav-tabs").addClass('hidden');
            } else {
                $("#add-modal .nav.nav-tabs").removeClass('hidden');
            }
        } else {
            if (this.value.length) {
                var content = $('#edit-modal');
                var language = {id: this.value};
                content.find('#native').bootstrapSwitch('destroy', true);
                content.find('#native').bootstrapSwitch({
                    onText: 'Yes',
                    offText: 'No'
                }).on('switchChange.bootstrapSwitch', function (event, state) {

                    $.ajax({
                        url: '/project/language-assessors/' + projectType + '/' + language.id + '/native/' + Number(this.checked),
                        type: 'GET',
                        success: function (response) {
                            result = JSON.parse(response);

                            var options = [];

                            $.each(result.assessors, function (key, value) {
                                options.push({id: key, text: value})
                            });


                            var assessor = content.find('select#assessor_id')[0].getAttribute('data-id');

                            modals.find('select#assessor_id').empty().select2({
                                data: options,
                                placeholder: 'Any',
                                allowClear: true
                            });

                            content.find('select#assessor_id').val(assessor).trigger('change');
                            content.find('#assessor_id')[0].setAttribute('data-id', assessor);
                        }
                    });
                });

                var taskNative = result.task.native;

                $.ajax({
                    url: '/project/language-assessors/' + projectType + '/' + this.value + '/' + taskNative,
                    type: 'GET',
                    success: function (response) {

                        result = JSON.parse(response);
                        var options = [];



                        $.each(result.assessors, function (key, value) {
                            options.push({id: key, text: value})
                        });

                        var assessor = $('#edit-modal').find('select#assessor_id')[0].getAttribute('data-id');
                        if (result.nativeButton) {
                            $('#edit-modal').find('#native_parent').removeClass('hidden');
                            content.find('select#assessor_id').parent().removeClass('col-sm-12').addClass('col-sm-8');
                        } else {
                            $('#edit-modal').find('#native_parent').addClass('hidden');
                            content.find('select#assessor_id').parent().removeClass('col-sm-8').addClass('col-sm-12');
                        }

                        modals.find('select#assessor_id').empty().select2({
                            data: options,
                            placeholder: 'Any',
                            allowClear: true
                        });
                        $('#edit-modal').find('select#assessor_id').val(assessor).trigger('change');
                        $('#edit-modal').find('#assessor_id')[0].setAttribute('data-id', assessor);

                        var container = $('.edit-paper-types');
                        container.html('');
                        $.each(result.languagePaperTypes, function (index, test_type) {
                            var classSpeaking = '';
                            if (test_type.paper_types.id == TEST_SPEAKING) {
                                classSpeaking = 'requires-availability';
                            }

                            var newCheckbox;

                            if(test_type.paper_type_id == 1){
                                newCheckbox = $('<div class="col-sm-6 text-left"><input type="checkbox" id="paper-type-' + test_type.paper_type_id + '-' + language.id + '" name="PaperTypes[' + test_type.paper_type_id + ']" value="1" class="language-use-new ' + classSpeaking + '"> <label for="paper-type-' + test_type.paper_type_id + '-' + language.id + '">' + test_type.paper_types.name + '</label></div>');
                            }else{
                                newCheckbox = $('<div class="col-sm-6 text-left"><input type="checkbox" id="paper-type-' + test_type.paper_type_id + '-' + language.id + '" name="PaperTypes[' + test_type.paper_type_id + ']" value="1" class="' + classSpeaking + '"> <label for="paper-type-' + test_type.paper_type_id + '-' + language.id +'">' + test_type.paper_types.name + '</label></div>');
                            }

                            if(test_type.paper_type_id == 6){
                                newCheckbox  =  $('<div class="col-sm-6 text-left"><input type="checkbox" id="paper-type-' + test_type.paper_type_id + '-' + language.id + '" name="PaperTypes[' + test_type.paper_type_id + ']" value="1" class="language-use ' + classSpeaking + '"> <label for="paper-type-' + test_type.paper_type_id + '-' + language.id + '">' + test_type.paper_types.name + '</label></div>');
                            }

                            container.append(newCheckbox);
                        });

                        $('#edit-modal .loading').addClass('active');

                        $.ajax({
                            url: '/' + route + '/update-form-data/' + $('#edit-modal').find('input#task_id')[0].value,
                            type: 'GET',
                            success: function (response) {
                                result = JSON.parse(response);
                                var modal = $('#edit-modal');
                                $.each(result.papers, function (key, value) {
                                    if (modal.find('#paper-type-' + key + '-' + language.id).length) {
                                        modal.find('#paper-type-' + key + '-' + language.id)[0].checked = true;
                                    }
                                });

                                if (modal.find('input.requires-deadline:checked').length === 0) {
                                    modal.find('div#deadline_parent').addClass('hidden');
                                } else {
                                    modal.find('div#deadline_parent').removeClass('hidden');
                                }

                                if (modal.find('input.requires-availability:checked').length === 0) {
                                    modal.find('.availability_parent').addClass('hidden');
                                    modal.find('.availability_parent input').addClass('ignore-validation');
                                } else {
                                    modal.find('.availability_parent').removeClass('hidden');
                                    modal.find('.availability_parent input').removeClass('ignore-validation');
                                }

                                modal.find('input.requires-availability').on('change', function (e) {
                                    if ($(this).is(':checked')) {
                                        modal.find('div.availability_parent').removeClass('hidden');
                                        modal.find('div.availability_parent input').removeClass('ignore-validation');
                                    } else {
                                        modal.find('div.availability_parent').addClass('hidden');
                                        modal.find('div.availability_parent input').addClass('ignore-validation');
                                    }
                                });

                                $('#edit-modal .loading').removeClass('active');
                            },
                            error: function (response) {
                                swal({
                                    type: 'error',
                                    title: 'Your ' + title + ' has not been updated.'
                                });
                            }
                        });
                    }
                });
            } else {
                modals.find('select#assessor_id').empty().select2({
                    data: [],
                    placeholder: 'Any',
                    allowClear: true
                });
            }
        }
    });

    var element = $('#project-tasks-table');

    element.on('page.dt', function() {
        $('html, body').animate({
            scrollTop: 0
        }, 'slow');
    });

    var route = element.attr('data-route');
    var title = 'Task';

    $('.add-button').on('click', function (e) {
        e.preventDefault();

        document.getElementById('add-modal').scrollTop = 0;

        $('#add-modal').addClass('control-sidebar-open');
        $('body').addClass('open');
    });

    element.on('click', 'a.edit-button', function (e) {
        e.preventDefault();
        var id;
        if ($(this).closest('tr')[0].hasAttribute('data-id')) {
            id = $(this).closest('tr').attr('data-id');
        } else {
            id = $($(this).closest('tr')[0].previousSibling).attr('data-id');
        }

        document.getElementById("edit-form").reset();
        document.getElementById('edit-modal').scrollTop = 0;
        $('#edit-modal .loading').addClass('active');
        $('#edit-modal').addClass('control-sidebar-open');
        $('body').addClass('open');

        var project_id = $(this).attr('data-id');

        if ($('#isTaskPage').length == 0) {
            route = 'project/' + project_id + '/task';
        }

        $.ajax({
            url: '/' + route + '/update-form-data/' + id,
            type: 'GET',
            success: function (response) {
                result = JSON.parse(response);
                var modal = $('#edit-modal');
                // if($.inArray(result.task.assessor_id, result.nativeAssessorsIds) >= 0 ) {
                //     $('#edit-modal').find('.bootstrap-switch').find('#native').trigger('click');
                // }

                $('#edit-modal').find('.bootstrap-switch').find('#native').bootstrapSwitch('state', result.task.native == 1);

                var bsSwitch = modal.find('#native');
                bsSwitch.bootstrapSwitch('state', result.nativeState);
                bsSwitch.bootstrapSwitch('readonly', result.nativeDisabled);

                if ($('#isTaskPage').length != 0) {
                    modal.find('#project_id')[0].value = result.task.project_id;
                }
                modal.find('#task_id')[0].value = result.task.id;
                modal.find('#project_type_id')[0].value = result.project.project_type_id;
                projectType = result.project.project_type_id;
                modal.find('#name')[0].value = result.task.name;
                modal.find('#email')[0].value = result.task.email;
                modal.find('#phone')[0].value = result.task.phone;
                modal.find('#skype')[0].value = result.task.skype;
                modal.find('#bill_client').val(result.task.bill_client).trigger('change');
                modal.find('#pay_assessor').val(result.task.pay_assessor).trigger('change');
                modal.find('#follower_id').val(result.followers).trigger('change');
                modal.find('#language_id').val(result.task.language_id).trigger('change');
                modal.find('#assessor_id').val(result.task.assessor_id).trigger('change');
                modal.find('#assessor_id')[0].setAttribute('data-id', result.task.assessor_id);
                if (modal.find('#mark').length) {
                    modal.find('#mark')[0].value = result.task.mark;
                    modal.find('#department')[0].value = result.task.department;
                }
                modal.find('#deadline')[0].value = result.task.deadline;
                modal.find('#availability_from')[0].value = result.task.availability_from;
                modal.find('#edit_timepick_from')[0].value = result.task.from_date;
                modal.find('#edit_timepick_to')[0].value = result.task.to_date;

                var timeFrom = modal.find('#edit_timepick_from'),
                    timeTo = modal.find('#edit_timepick_to');

                var fromConfig = $.extend({}, defaultTimepickerConfig);
                fromConfig.onClose = function (time, obj) {
                    if (time === '') {
                        return;
                    }
                    timeTo.timepicker('setTime', time);
                    timeTo.timepicker('option', {minTime: {hour: obj.hours, minute: obj.minutes}});
                };

                timeFrom.timepicker(fromConfig);
                timeTo.timepicker(defaultTimepickerConfig);

                if (result.task.from_date !== null) {
                    timeFrom.timepicker('setTime', result.task.from_date);
                    timeTo.timepicker('setTime', result.task.to_date);
                }

                var attachmentsHTML = '';
                $.each(result.attachments, function (key, value) {
                    attachmentsHTML = attachmentsHTML + "<div class='col-sm-4'><a href='" + value.url + "' target='_blank'><span class='fa fa-file-o fa-4x'></span>" + value.filename + "</a></div>";
                });

                modal.find('#view_attachments')[0].innerHTML = attachmentsHTML;

//                        $.each(result.papers, function (key, value) {
//                            modal.find('#paper-type-' + key)[0].checked = true;
//                        });

                if (modal.find('input.requires-deadline:checked').length === 0) {
                    modal.find('div#deadline_parent').addClass('hidden');
                } else {
                    modal.find('div#deadline_parent').removeClass('hidden');
                }

                if (modal.find('input.requires-availability:checked').length === 0) {
                    modal.find('.availability_parent').addClass('hidden');
                    modal.find('.availability_parent input').addClass('ignore-validation');
                } else {
                    modal.find('.availability_parent').removeClass('hidden');
                    modal.find('.availability_parent input').removeClass('ignore-validation');
                }

//                        $('#edit-modal .loading').removeClass('active');
            },
            error: function (response) {
                swal({
                    type: 'error',
                    title: 'Your ' + title + ' has not been updated.'
                });
            }
        });
    });

    function loop(items, myFunc, index) {
        index = index || 0;

        console.log('loop: index = '+ index);
        if (!items[index]) {
            $('body > .loading').removeClass('active');
            $('body').removeClass('open');
            $('#add-modal').removeClass('control-sidebar-open');
            generateDataTable(element, route);
            console.log(index, 'end');
            return;
        }

        function next(error) {
            if(error){ return; }
            loop(items, myFunc, ++index);
        }

        myFunc(items[index], next);
    }

    function clientTaskAdd(){


        $('body > .loading').addClass('active');
        var form = $('#add-form');
        var data = new FormData(form[0]);
        $.ajax({
            url: '/project/verify-test-taker',
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (res) {
                if (res.resType === 'success') {
                    
                    var totalOperations = Object.keys(res.data.tasks).length;
                    console.log('total: ', totalOperations);

                    loop(res.data.tasks, function(value, next){

                        var elem = value,
                            language_id = elem.language_id ;

                        var dataArray = form.serializeArray();
                        var newData = [];

                        $.each(dataArray, function(ind, input) {
                            if (input.name != 'languages[]' && !input.name.match(/languagesExtra/g)) { //skip languages[] and languagesExtra
                                newData.push(input);
                            } else if (input.name == 'languages[]' && input.value == language_id) { // add only languages[] == language_id
                                newData.push(input);
                            } else if (input.name.match(/languagesExtra/g) ) { // add only languagesExtra[] == language_id
                                var re = new RegExp('languagesExtra\\['+language_id+'\\]', 'g');
                                if (input.name.match(re) && input.name.match(re).length > 0) {
                                    newData.push(input);
                                }
                            }
                        });

                        $('body > .loading').addClass('active');
                        $('body').addClass('open');
                        $('#add-modal').addClass('control-sidebar-open');


                        // IS OK TO INSERT - ADD TASK FOR THIS LANG
                        if (elem.task === null){

                            console.log('case 0', 'lang_id: ' + language_id);

                            var obj = {};
                            $.each(newData, function(index, value) {
                                obj[value.name] = value.value;
                            });


                            $.ajax({
                                url: '/' + route + '/create',
                                type: 'POST',
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                data: obj,
                                dataType: 'json',
                                success: function(){
                                    next();
                                },
                                error: function (response) {
                                }
                            });




                        } else

                        // CASE 1 - IS IN THE SAME PROJECT
                        if (elem.other_project == false) {

                            console.log('case 1', 'lang_id: ' + language_id);

                            var html = 'For ' + elem.task.language.name + ' On ' +  dateFormat(new Date(elem.task.created_at.replace(/-/g, "/")), "dd mmm yyyy HH:MM") + ' <br>';
                            html += 'With ';
                            $.each(elem.task.papers, function(ind, elem){
                                if (elem.report) {
                                    html += elem.report.grade + ' (' + elem.type.name + ')<br>';
                                }
                            });

                            html += '<br><h2>Do you want to retest this candidate now?</h2><br>';
                            swal({
                                title: 'We have already assessed this candidate for you',
                                type: 'question',
                                html: html,
                                customClass: 'swal2-overflow',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes',
                                cancelButtonText: 'No',
                                focusConfirm: false,
                                allowOutsideClick: false
                            }).then(function (result) {
                                if (result.value) { //if Yes is clicked

                                    // normal add
                                    var obj = {};
                                    $.each(newData, function(index, value) {
                                        obj[value.name] = value.value;
                                    });

                                    //duplicate task and set half price
                                    obj.no_assessor = false;
                                    obj.send_mail_admin = true;
                                    obj.previous_task_id = elem.task.id;

                                    $.ajax({
                                        url: '/' + route + '/create',
                                        type: 'POST',
                                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                        data: obj,
                                        dataType: 'json',
                                        success: function(){
                                            next();
                                        },
                                        error: function (response) {
                                        }
                                    });

                                } else {
                                    next();
                                }
                            });

                        }

                        // CASE 2 - IS IN OTHER PROJECT
                        else if (elem.other_project == true) {

                            console.log('case 2', 'lang_id: ' + language_id);

                            var html = 'Language: ' + elem.task.language.name + '. Test date: ' +  dateFormat(new Date(elem.task.created_at.replace(/-/g, "/")), "dd mmm yyyy HH:MM") + ' <br>';
                            html += 'Tested for: ';

                            var tests = [];
                            $.each(elem.task.papers, function(ind, elem){
                                if (elem.report) {
                                    tests.push(elem.type.name);
                                }
                            });

                            html += tests.join(', ');
                            html += '<br><h2>Do you wish to view the results?</h2><br>';

                            swal({
                                title: 'This candidate has already been assessed for a different project',
                                type: 'question',
                                html: html,
                                customClass: 'swal2-overflow',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes',
                                cancelButtonText: 'No',
                                focusConfirm: false,
                                allowOutsideClick: false
                                //@TODO add more
                            }).then(function (result) {
                                if (result.value) { //if Yes is clicked

                                    var obj = {};
                                    $.each(newData, function(index, value) {
                                        obj[value.name] = value.value;
                                    });

                                    //duplicate task and set half price
                                    obj.half_price = true;
                                    obj.no_assessor = true;
                                    obj.task_to_duplicate = elem.task.id;
                                    obj.send_mail_admin = false;

                                    var alreadyTests = [], //old task
                                        alreadyNames = [],
                                        newTests = [],  //new task
                                        newNames = [];

                                    $.each(elem.task.papers, function(ind, elem){
                                        if (elem.report) {
                                            alreadyTests.push(elem.paper_type_id);
                                            alreadyNames.push(elem.type.name);
                                        }
                                    });

                                    var checkboxes = $('#add-task-language-'+language_id).find('.lang_paper_types input[type="checkbox"]:checked');
                                    $.each(checkboxes, function(ind, elem){
                                        newTests.push(parseInt($(elem).attr('data-test-id')));
                                        newNames.push($(elem).attr('data-test-name'));
                                    });

                                    var toAddNames = newNames.filter(function(i){
                                        return alreadyNames.indexOf(i) < 0;
                                    });

                                    var toAddTests = newTests.filter(function(i){
                                        return alreadyTests.indexOf(i) < 0;
                                    });

                                    //get results

                                    var results_html = 'For ' + elem.task.language.name + ' On ' +  dateFormat(new Date(elem.task.created_at.replace(/-/g, "/")), "dd mmm yyyy HH:MM") + ' <br>';
                                    results_html += 'With ';
                                    $.each(elem.task.papers, function(ind, elem){
                                        if (elem.report) {
                                            results_html += elem.report.grade + ' (' + elem.type.name + ')<br>';
                                        }
                                    });

                                    // if it has extra tests
                                    if (toAddTests.length){

                                        $.ajax({
                                            url: '/project/duplicate-task',
                                            type: 'POST',
                                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                            data: obj,
                                            dataType: 'json',
                                            success: function(){},
                                            error: function(){}
                                        });

                                        results_html += '<br>Candidate not tested for ' + toAddNames.join(', ');
                                        results_html += '<br><h2>Do you want to test this candidate for the rest of the tests?</h2><br>';

                                        swal({
                                            title: 'Test Results',
                                            type: 'question',
                                            html: results_html,
                                            customClass: 'swal2-overflow',
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Yes',
                                            cancelButtonText: 'No',
                                            focusConfirm: false,
                                            allowOutsideClick: false
                                        }).then(function (result) {
                                            if (result.value) { //if Yes is clicked

                                                // normal add
                                                var obj = {};
                                                $.each(newData, function(index, value) {
                                                    obj[value.name] = value.value;
                                                });

                                                // remove alreadyTested from obj
                                                $.each(alreadyTests, function(ind, elem){
                                                    delete obj['languagesExtra['+language_id+'][PaperTypes]['+elem+']'];
                                                });

                                                $.ajax({
                                                    url: '/' + route + '/create',
                                                    type: 'POST',
                                                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                                    data: obj,
                                                    dataType: 'json',
                                                    success: function(){
                                                        next();
                                                    },
                                                    error: function (response) {
                                                    }
                                                });

                                            } else {
                                                next();
                                            }
                                        });

                                    }
                                    else {


                                        results_html += '<br><h2>Do you want to retest this candidate now?</h2><br>';

                                        swal({
                                            title: 'Test Results',
                                            type: 'question',
                                            html: results_html,
                                            customClass: 'swal2-overflow',
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Yes',
                                            cancelButtonText: 'No',
                                            focusConfirm: false,
                                            allowOutsideClick: false
                                        }).then(function (result) {
                                            if (result.value) { //if Yes is clicked



                                                // normal add
                                                var obj = {};
                                                $.each(newData, function(index, value) {
                                                    obj[value.name] = value.value;
                                                });

                                                obj.send_mail_admin = false;
                                                obj.previous_task_id = elem.task.id;

                                                $.ajax({
                                                    url: '/' + route + '/create',
                                                    type: 'POST',
                                                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                                    data: obj,
                                                    dataType: 'json',
                                                    success: function(){
                                                        next();
                                                    },
                                                    error: function (response) {
                                                    }
                                                });

                                            } else {

                                                var obj = {};
                                                $.each(newData, function(index, value) {
                                                    obj[value.name] = value.value;
                                                });

                                                //duplicate task and set half price
                                                obj.half_price = true;
                                                obj.task_to_duplicate = elem.task.id;


                                                $.ajax({
                                                    url: '/project/duplicate-task',
                                                    type: 'POST',
                                                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                                    data: obj,
                                                    dataType: 'json',
                                                    success: function(){
                                                        next();
                                                    },
                                                    error: function(){}
                                                });
                                            }
                                        });

                                    }


                                } else { // if No is clicked

                                    // normal add
                                    var obj = {};
                                    $.each(newData, function(index, value) {
                                        obj[value.name] = value.value;
                                    });

                                    obj.no_assessor = false;
                                    obj.send_mail_admin = false;
                                    obj.previous_task_id = null;
                                    
                                    $.ajax({
                                        url: '/' + route + '/create',
                                        type: 'POST',
                                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                        data: obj,
                                        dataType: 'json',
                                        success: function(){
                                            next();
                                        },
                                        error: function (response) {
                                        }
                                    });


                                }
                            });



                        }
                    });

                } 
            },
            error: function (response) {
                $('body').removeClass('open');
                swal({
                    type: 'error',
                    title: 'Your ' + title + ' has not been created.'
                });
            }
        });



    }

    $(document).on('click', '#add-form #add-submit-button', function (e) {


        var avbDayInputs = $('.speaking-availability');
        var avbOk = true;
        $.each(avbDayInputs, function(){
            var day = $(this).val();

            if (!day) {
                return;
            }

            var hour = $(this).closest('.availability_parent').find('input.timepick_from').val();

            if (moment(day).isSame(moment(), 'day') && !hour){
                swal({
                    type: 'error',
                    text: 'Availability for speaking time must be greater than current time',
                });
                return;
            }

            if (!hour){
                return;
            }

            if ( new Date(day + ' ' + hour) < new Date() || !hour ){
                swal({
                    type: 'error',
                    text: 'Availability for speaking time must be greater than current time',
                });
                avbOk = false;
                return false;
            }

        });

        if (!avbOk) {
            return false;
        }

        var _this = $(this);

        var form = $(this).closest('form');

        $.validator.methods.email = function (value, element) {
            return this.optional(element) || /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i.test(value);
        };

        form.show();
        form.validate({
            highlight : function(label) {
                $(label).addClass('has-error-input');
                var tab_content= $(label).closest('.tab-content');
                if (tab_content.find(".tab-pane.active:has(.has-error-input)").length == 0) {
                    tab_content.find(".tab-pane:has(.has-error-input)").each(function (index, tab) {
                        var id = $(tab).attr("id");
                        $('a[href="#' + id + '"]').tab('show');
                    });
                }
            },
            ignore: '.ignore-validation',
            submitHandler: function (form, e, _this) {
                e.preventDefault();
                if (isClient == 1) {
                    clientTaskAdd();
                    return;
                }

                $('body > .loading').addClass('active');
                $.ajax({
                    url: '/' + route + '/create',
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: new FormData(document.getElementById('add-form')),
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (res) {
                        $('body > .loading').removeClass('active');
                        if (res.resType === 'Success') {
                            $('body > .loading').removeClass('active');
                            $('body').removeClass('open');
                            $('#add-modal').removeClass('control-sidebar-open');
                            swal({
                                type: 'success',
                                title: 'Your task has been created.',
                                showConfirmButton: false,
                                timer: 2500
                            });

                            document.getElementById('add-form').reset();
                            $("#add-form").find('#follower_id').val([]).trigger('change');
                            $("#add-form").find('#language_id').val([]).trigger('change');

                            if (element.find('.dataTables_empty').length) {
                                location.reload();
                            } else {
                                generateDataTable(element, route);
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
                    error: function (response) {
                        $('body').removeClass('open');
                        swal({
                            type: 'error',
                            title: 'Your ' + title + ' has not been created.'
                        });
                    }
                });


            }
        });
    });

    $('#edit-form #edit-submit-button').on('click', function (e) {
        var _this = $(this);
        var form = $(this).parents('form:first');
        form.show();
        form.validate({
            submitHandler: function (form, e, _this) {
                e.preventDefault();
                var id = $('#edit-form').find('#task_id')[0].value;

                $('body > .loading').addClass('active');

                $.ajax({
                    url: '/' + route + '/update/' + id,
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: new FormData(document.getElementById('edit-form')),
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (res) {
                        $('body > .loading').removeClass('active');
                        if (res.resType === 'Success') {
                            $('body').removeClass('open');
                            $('#edit-modal').removeClass('control-sidebar-open');
                            swal({
                                type: 'success',
                                title: 'Your ' + title + ' has been updated.',
                                showConfirmButton: false,
                                timer: 2500
                            });
                            generateDataTable(element, route);
//                                    table.ajax.reload();
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
                    error: function (response) {
                        $('body').removeClass('open');
                        swal({
                            type: 'error',
                            title: 'Your ' + title + ' has not been updated.'
                        });
                    }
                });
            }
        });
    });

    function generateDataTable(element, route) {
        var filters = {},
            permanent_filters = element.attr('data-permanent-filters');
        $('.column_filter').each(function () {
            filters[this.name] = this.value;
        });

        $('.task-batch-all').prop('checked', false);

        return element.DataTable({
            ajax: {
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '/' + route + 's-data' + (permanent_filters != false ? '?'+permanent_filters : ''),
                type: "POST",
                data: {
                    filters: filters
                }
            },
            destroy: true,
            paging: $("select#page_length").val() === '-1' ? false : true,
            lengthChange: false,
            pageLength: parseInt($("select#page_length").val()),
            searching: false,
            ordering: true,
            info: true,
            autoWidth: true,
            processing: true,
            language: {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
            },
            serverSide: true,
            responsive: true,
            //stateSave: true,
            deferRender: true,
            order: [
                [
                    1, 'desc'
                ]
            ],
            createdRow: function createdRow(row, data) {
                $(row).attr('data-id', data.id);
            },
            "initComplete": function () {
                if (element.parent().find('.dataTables_paginate').length) {
                    element.parent().find('.dataTables_info').parent().append(element.parent().find('.dataTables_info'));
                    if (element.parent().find('span > .paginate_button').length > 1) {
                        if ($('.export-buttons').length) {
                            element.parent().find('.dataTables_paginate')[0].style.visibility = "visible";
                            element.parent().find('.dataTables_info')[0].style.visibility = "visible";
                        } else {
                            element.parent().find('.dataTables_paginate')[0].style.display = "block";
                            element.parent().find('.dataTables_info')[0].style.display = "block";
                        }
                    } else if (element.parent().find('span > .paginate_button').length >= 0) {
                        if ($('.export-buttons').length) {
                            element.parent().find('.dataTables_paginate')[0].style.visibility = "hidden";
                            element.parent().find('.dataTables_info')[0].style.visibility = "hidden";
                        } else {
                            element.parent().find('.dataTables_paginate')[0].style.display = "none";
                            element.parent().find('.dataTables_info')[0].style.display = "none";
                        }
                    }
                }
            },
            "fnDrawCallback": function () {
                $.each($("table#project-tasks-table thead tr > th"), function (key, value) {
                    if ($(value).hasClass('hidden')) {
                        if ($(value).attr('aria-label') !== 'Actions') {
                            $('tr').find('td:eq(' + key + ')').addClass('hidden');
                        }
                    }
                });

                var paginateRow = $('.dataTables_paginate');
                var pageCount = Math.ceil((this.fnSettings().fnRecordsDisplay()) / this.fnSettings()._iDisplayLength);
                if (pageCount > 1) {
                    paginateRow.css("display", "block");
                } else {
                    paginateRow.css("display", "none");
                }
            },
            columns: [
                {
                    data: null,
                    name: null,
                    render: function (data, type, row) {
                        if (isAdmin == 0) {
                            return;
                        }
                        return '<label><input type="checkbox" name="task-batch" class="task-batch" value="' + row.id + '" data-language="' + row.language_id + '" /> <span class="label-text"></span></label>';
                    },
                    defaultContent: '',
                    orderable: false,
                    className: 'styled-checkbox'
                },
                {
                    data: 'id',
                    name: 'id',
                    render: function (data, type, row) {
                        if(isAssessor == 1 && row.task_status_id == 5){
                            return '<a href="#">#' + row.id + '</a>';
                        }
                        return '<a href="/task/' + row.id + '">#' + row.id + '</a>';
                    },
                    defaultContent: '',
                    orderable: true
                },
                {
                    data: 'project.name',
                    name: 'project_id',
                    render: function (data, type, row) {
                        return '<a href="/project/' + row.project.id + '/tasks">' + row.project.name + '</a>';
                    },
                    defaultContent: '',
                    className: 'small-td'
                },
                {
                    data: 'name',
                    name: 'name',
                    className: 'small-td',
                    render: function (data, type, row) {
                        var arr = row.created_at.split(/[- :]/);
                        var date = new Date(arr[0], arr[1] - 1, arr[2], arr[3], arr[4], arr[5]);
                        date = dateFormat(date, "dd mmm yyyy / HH:MM");
                        var spanWidth = 120 + row.email.length * 8;
                        var html = '<a href="/task/' + row.id + '">' + row.name + '</a><i class="fa fa-info-circle"><span class="info" style="min-width: '+spanWidth+'px;">';
                        if(isAssessor == 1 && row.task_status_id == 5){
                            html = '<a href="#">' + row.name + '</a><i class="fa fa-info-circle"><span class="info" style="min-width: '+spanWidth+'px;">';
                        }

                        html += '' +
                            '<p ><span class="col-left">Phone</span>' + row.phone + '</p>' +
                            '<p ><span class="col-left">Email</span>' + row.email + '</p>' +
                            '<p ><span class="col-left">Last updated</span>' + date + '</p>' +
                            '</span></i>';

                        return html;
                    },
                    defaultContent: ''
                },

                {
                    data: 'language.name',
                    name: 'language_id',
                    orderable: true
                },
                {
                    data: null,
                    width: isOnlyAssessor == 1 ? 100 : 253,
                    render: function (data, type, row) {
                        var paperTypes = {
                            'LU': {
                                name: 'U',
                                active: false,
                                grade: '',
                                date_taken: ''
                            },
                            'S': {
                                name: 'S',
                                active: false,
                                grade: '',
                                date_taken: ''
                            },
                            'W': {
                                name: 'W',
                                active: false,
                                grade: '',
                                date_taken: ''
                            },
                            'L': {
                                name: 'L',
                                active: false,
                                grade: '',
                                date_taken: ''
                            },
                            'R': {
                                name: 'R',
                                active: false,
                                grade: '',
                                date_taken: ''
                            },
                        };

                        if (isOnlyAssessor == 1) {
                            delete paperTypes['LU'];
                            delete paperTypes['L'];
                            delete paperTypes['R'];
                        }

                        var abilitySum = 0, reportsCount = 0, onlineTests = 0;
                        var paperNames = '<div class="grades">';
                        var canRefuse = false;
                        var native = false;
                        $.each(row.papers, function (index, value) {

                            var testId = parseInt(value.type.id),
                                name = value.type.name,
                                initials;

                            switch (testId) {
                                case 1:
                                case 6:
                                    initials = 'LU';
                                    break;
                                case 2:
                                    initials = 'S';
                                    break;
                                case 3:
                                    initials = 'W';
                                    break;
                                case 4:
                                    initials = 'L';
                                    break;
                                case 5:
                                    initials = 'R';
                                    break;
                                
                            }


                            if (!paperTypes[initials]){
                                return;
                            }

                            paperTypes[initials].active = true;
                            paperTypes[initials].fullName = name;
                            if (value.done !== 0) {
                                if(value.ended_at) {
                                    var date_ended = new Date(value.ended_at.replace(/-/g, "/"));
                                    paperTypes[initials].date_taken = dateFormat(date_ended, "dd mmm yyyy HH:mm");
                                }
                            }
                            if (value.report !== null) {
                                paperTypes[initials].grade = value.report.grade;
                                abilitySum = abilitySum + parseFloat(value.report.ability);
                                reportsCount++;
                                if (value.report.grade === 'N') {
                                    native = true;
                                }
                            } else {
                                if (['S', 'W'].indexOf(initials) !== -1) {
                                    canRefuse = true;
                                } else {
                                    onlineTests++;
                                }
                            }

                            //speaking date taken
                            if (value.report && value.report.created_at){
                                var date_ended_speaking = new Date(value.report.created_at.replace(/-/g, "/"));
                                paperTypes[initials].date_taken = dateFormat(date_ended_speaking, "mmm dd yyyy HH:MM");
                            }

                        });

                        if (row.language.groups[0].user_groups.length < 2) {
                            canRefuse = false;
                        }

                        var global_level_ability = abilitySum / reportsCount;
                        var global_level = '';
                        var training_needs = '';
                        if (global_level_ability < 0.7) {
                            global_level = 'Pre-A1';
                            training_needs = 'A1';
                        }
                        else if (global_level_ability < 1.7) {
                            global_level = 'A1';
                            training_needs = 'A2';
                        }
                        else if (global_level_ability < 2.7) {
                            global_level = 'A2';
                            training_needs = 'B1';
                        }
                        else if (global_level_ability < 3.7) {
                            global_level = 'B1';
                            training_needs = 'B2';
                        }
                        else if (global_level_ability < 4.7) {
                            global_level = 'B2';
                            training_needs = 'C1';
                        }
                        else if (global_level_ability < 5.7) {
                            global_level = 'C1';
                            training_needs = 'C2';
                        }
                        else if (global_level_ability < 6) {
                            global_level = 'C2';
                            training_needs = 'N';
                        }
                        else if (global_level_ability <= 7) {
                            global_level = 'N';
                            training_needs = 'N';
                        }

                        // if (row.papers.length === 5 && reportsCount === 5) {
                        //     paperTypes['G'] = {
                        //         name: 'G',
                        //         active: true,
                        //         grade: global_level,
                        //         date_taken: '',
                        //         fullName: 'Global Level'
                        //     };
                        //     paperTypes['T'] = {
                        //         name: 'T',
                        //         active: true,
                        //         grade: training_needs,
                        //         date_taken: '',
                        //         fullName: 'Training Needs'
                        //     };
                        // }
                        var papers = '<div class="grades">';
                        var gradeBtnClass = '';
                        $.each(paperTypes, function (index, value) {
                            if (value.grade.length) {
                                gradeBtnClass = '';
                            } else {
                                gradeBtnClass = ' partial';
                            }
                            if (value.active) {
                                if (value.name === 'G' || value.name === 'T') {
                                    // if (native === true) {
                                    // global level and training needs
                                    papers += '<button class="btn btn-grade btn-' + value.name + gradeBtnClass + '">' + value.grade +
                                        '<span class="info">' +
                                        '<div class="col-xs-12 no-padding"><div class="col-xs-4 no-padding">Test</div><div class="col-xs-8">' + value.fullName + '</div></div>' +
                                        '<div class="col-xs-12 no-padding"><div class="col-xs-4 no-padding">Grade</div><div class="col-xs-8">' + value.grade + '</div></div>' +
                                        '</span>' +
                                        '</button>';
                                    // }
                                } else {
                                    papers += '<button class="btn btn-grade btn-' + value.name + gradeBtnClass + ' ' + value.grade.replace(' ', '-').toLowerCase() + '">' + value.grade +
                                        '<span class="info">' +
                                        '<div class="col-xs-12 no-padding"><div class="col-xs-4 no-padding">Test</div><div class="col-xs-8">' + value.fullName + '</div></div>' +
                                        '<div class="col-xs-12 no-padding"><div class="col-xs-4 no-padding">Grade</div><div class="col-xs-8">' + value.grade + '</div></div>' +
                                        '<div class="col-xs-12 no-padding"><div class="col-xs-4 no-padding">Date Taken</div><div class="col-xs-8">' + value.date_taken + '</div></div>' +
                                        '</span>' +
                                        '</button>';
                                }
                            } else {
                                papers += '<button class="btn btn-grade btn-' + value.name + ' inactive"></button>';
                            }

                            paperNames += '<span class="grade-name">' + value.name + '</span>';
                        });
                        paperNames += '</div>';
                        papers += '</div>';

                        return paperNames + papers;
                    },
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'status.name',
                    name: 'task_status_id',
                    render: function (data, type, row) {
                        var statusFontColor = '';
                        if (['In Progress', 'Allocated'].indexOf(row.status.name) !== -1) {
                            statusFontColor = '#4A4A4A';
                        } else {
                            statusFontColor = '#fff';
                        }
                        var btn = '<button class="btn btn-primary btn-status" style="background-color: ' + row.status.color + '; border: none; font-weight: bold; color: ' + statusFontColor + '">' + row.status.name + '</button>';
                        if (row.task_status_id !== '3' && row.papers && row.papers.length && isAssessor == 1) {
                            var isDone = 1;
                            row.papers.forEach(function (item) {
                                if (item.paper_type_id == 3 || item.paper_type_id == 2) {
                                    isDone *= item.status.id != 3 ? 0 : 1;
                                }
                            });

                            if (isDone) {
                                btn = '<button class="btn btn-primary btn-status" style="background-color: #27AE60; border: none; font-weight: bold; color: #fff">Done</button>';
                            }
                        }
                        return btn;
                    },
                    defaultContent: '',
                    orderable: true
                },

                {
                    data: 'availability_from',
                    name: 'availability_from',
                    render: function(columnData, show, data) {
                        var text = "";
                        if (data.availability_from) {
                            try {
                                var arr = data.availability_from.split(/[- :]/);
                                var dateFrom = new Date(arr[0], arr[1] - 1, arr[2], arr[3], arr[4], arr[5]);
                                dateFrom = dateFormat(dateFrom, "dd mmm yyyy / HH:MM");
                                dateFrom = dateFrom.replace("/", " ");

                                var arr = data.availability_to.split(/[- :]/);
                                var dateTo = new Date(arr[0], arr[1] - 1, arr[2], arr[3], arr[4], arr[5]);
                                dateTo = dateFormat(dateTo, "dd mmm yyyy / HH:MM");
                                dateTo = dateTo.split("/");
                                text = "<div>" + dateFrom + "</div><div><span style='color: transparent'>" + dateTo[0] + "</span> " + dateTo[1] + "</div>";
                            } catch(err) {
                                console.log(data.availability_from);
                            }
                        }

                        return text;
                    }
                },
                {
                    data: 'added_by.email',
                    name: 'added_by_id',
                    render: function (data, type, row) {
                        return '<a href="#">' + row.added_by.first_name + ' ' + row.added_by.last_name + '</a>';
                    },
                    defaultContent: '',
                    orderable: true
                },

                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function (data, type, row) {
                        var arr = row.created_at.split(/[- :]/);
                        var date = new Date(arr[0], arr[1] - 1, arr[2], arr[3], arr[4], arr[5]);
                        date = dateFormat(date, "dd mmm yyyy / HH:MM");
                        date = date.split("/");
                        return "<span class='text-nowrap'>" + date[0] + "</br>" + date[1] + "</span>";
                    },
                    defaultContent: '',
                    orderable: true
                },
                {
                    data: 'assessor_id',
                    name: 'assessor_id',
                    render: function (data, type, row) {
                        var onlineTests = 0;
                        $.each(row.papers, function (index, value) {
                            var name = value.type.name;
                            var initials = name.match(/\b\w/g) || [];
                            initials = ((initials.shift() || '') + (initials.pop() || '')).toUpperCase();
                            if (value.report === null) {
                                if (['S', 'W'].indexOf(initials) !== -1) {
                                    canRefuse = true;
                                } else {
                                    onlineTests++;
                                }
                            }
                        });

                        if (row.assessor === null || onlineTests === row.papers.length) {
                            return '';
                        } else {
                            return '<a href="/task/' + row.id + '">' + row.assessor.first_name + ' ' + row.assessor.last_name + '</a>';
                        }
                    },
                    defaultContent: '',
                    orderable: true
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        var canRefuse = false;
                        $.each(row.papers, function (index, value) {
                            var name = value.type.name;
                            var initials = name.match(/\b\w/g) || [];

                            initials = ((initials.shift() || '') + (initials.pop() || '')).toUpperCase();
                            if (value.report === null) {
                                if (['S', 'W'].indexOf(initials) !== -1) {
                                    canRefuse = true;
                                }
                            }
                        });

                        var totalNative = row.language.groups[0].user_groups.filter(function(elem){
                            return elem.native == 1;
                        }).length;
                        var totalNonNative = row.language.groups[0].user_groups.length - totalNative;

                        if (row.language.groups[0].user_groups.length < 2) {
                            canRefuse = false;
                        }

                        if (parseInt(row.native) == 1 && totalNative <= 1){
                            canRefuse = false;
                        }

                        if (parseInt(row.native) == 0 && totalNonNative <= 1){
                            canRefuse = false;
                        }

                        // 5 = canceled
                        if (row.task_status_id == 5 || row.assessor === null || canRefuse === false || parseInt(row.project.project_type_id) != 1) {
                            return '';
                        } else {
                            if (isAssessor && row.assessor_id == currUID){
                                return '<a href="#" class="task-refuse" data-id="' + row.id + '"><span class="fa fa-times"></span> Refuse Task</a>';
                            } else {
                                return '';
                            }

                        }
                    },
                    defaultContent: '',
                    orderable: false,
                    searchable: false
                },
                {
                    data: null,
                    className: "actions",
                    render: function (data, type, row) {
                        if ($("table#project-tasks-table thead tr > th:last-of-type").hasClass('hidden')) {
                            $("table#project-tasks-table thead tr > th:last-of-type")[0].style = 'display: table-cell !important';

                            return '<a href="#" class="action-button updates-button" data-id="' + row.project_id + '" title="Quick View"><span class="fa fa-eye"></span></a>';
                        } else {
                            if (row.status.name === 'Canceled') {
                                return '';
                            } else {
                                return '<a href="#" class="action-button updates-button" data-id="' + row.project_id + '" title="Quick View"><span class="fa fa-eye"></span></a>' +
                                    // '<a href="#" class="action-button edit-button" data-id="' + row.project_id + '" title="Edit Task"><span class="fa fa-pencil"></span></a>' +
                                    '<a href="#" class="action-button demo-close editor_remove"  data-id="' + row.project_id + '" title="Cancel Task"><span class="fa fa-times"></span></a>';
                            }
                        } 
                    },
                    defaultContent: '',
                    orderable: false,
                    searchable: false
                }
            ],
            "preDrawCallback": function( settings ) {
                $('td.styled-checkbox').attr('colspan', 1);
            },
            "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {

                var filters = {};
                $('.column_filter').each(function () {
                    filters[this.name] = this.value;
                });
                if (filters['show_active'] == "true") {
                    $.each(aData, function (index, value) {
                        if (index == "task_status_id") {
                            if (value == "1" || value == "2") {
                                $('select#name_filter').val("");
                                $('select#name_filter option').remove();
                                $('select#name_filter').append('<option value="' + aData.name + '">' + aData.name + '</option>');
                                $('select#name_filter').val("");
                            }
                        }
                    });
                }
            }
        });
    }

    var table = generateDataTable(element, route);

    $(document).on('change dp.change', 'input.column_filter, select.column_filter', function (e) {
        generateDataTable(element, route).page('first').draw('page');
    });


    // date added tasks

    $('#date_range_filter').daterangepicker({
        autoUpdateInput: false,
        opens: 'left',
        locale: {
            cancelLabel: 'Clear'
        }
    });

    $('#date_range_filter').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        generateDataTable(element, route).page('first').draw('page');
    });

    $('#date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        generateDataTable(element, route).page('first').draw('page');
    });


    // date test taken

    $('#date_test_range_filter').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    }); 

    $('#date_test_range_filter').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        generateDataTable(element, route).page('first').draw('page');
    });

    $('#date_test_range_filter').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        generateDataTable(element, route).page('first').draw('page');
    });

    // date assessor test taken

    $('#date_assessor_tests_range_filter').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    $('#date_assessor_tests_range_filter').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        generateDataTable(element, route).page('first').draw('page');
    });

    $('#date_assessor_tests_range_filter').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        generateDataTable(element, route).page('first').draw('page');
    });


    crudDataTable(table, element, route, title);

    $('aside#projects_filter form#projects_filter_form').submit(function (event) {
        event.preventDefault();

        var project_id = $(this).find('#project_id')[0].value;
        var search_projects = $(this).find('#search_projects')[0].value;

        if (search_projects.length === 0) {
            search_projects = null;
        }

        $.ajax({
            url: '/project/' + project_id + '/filter-by-name/' + search_projects,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                var html = "";
                $.each(response.projects, function (index, value) {
                    html += "<li><a href='/project/" + index + "/tasks'>" + value + "</a></li>";
                });
                $('aside#projects_filter ul.list-unstyled').html(html);
            }
        });
    });

    $('#export_xls').on('click', function (e) {
        $('body .loading').addClass('active');
        var project_id = 0; //all projects

        if ($('#isTaskPage').length != 0) {
            project_id = $('#project_id')[0].value;
        }

        $.ajax({
            url: '/project/' + project_id + '/export-tasks-xls',
            type: "GET",
            data: {
                template: true
            },
            success: function (response) {
                window.location.href = this.url;
                $('body .loading').removeClass('active');
            },
            error: function (response) {

            }
        });
    });

    $('#export').on('click', function (e) {
        $('body .loading').addClass('active');
        var filters = {};
        $('.column_filter').each(function () {
            filters[this.name] = this.value;
        });
        var project_id = 0; //all projects

        if ($('#isTaskPage').length != 0) {
            project_id = $('#project_id')[0].value;
        }

        // get params
        var items = location.search.substr(1).split("&");
        for (var index = 0; index < items.length; index++) {
            var item =  items[index].split("=") ;
            filters[item[0]] = item[1];
        }


        $.ajax({
            url: '/project/' + project_id + '/export-tasks-xls',
            type: "GET",
            data: {
                filters: filters
            },
            success: function (response) {
                window.location.href = this.url;
                $('body .loading').removeClass('active');
            },
            error: function (response) {

            }
        });
    });

    $('#export-grades').on('click', function (e) {
        $('body .loading').addClass('active');
        var filters = {};
        $('.column_filter').each(function () {
            filters[this.name] = this.value;
        });
        var project_id = 0; //all projects

        if ($('#isTaskPage').length != 0) {
            project_id = $('#project_id')[0].value;
        }

        // get params
        var items = location.search.substr(1).split("&");
        for (var index = 0; index < items.length; index++) {
            var item =  items[index].split("=") ;
            filters[item[0]] = item[1];
        }

        $.ajax({
            url: '/project/' + project_id + '/export-grades-csv',
            type: "GET",
            data: {
                filters: filters
            },
            success: function (response) {
                window.location.href = this.url;
                $('body .loading').removeClass('active');
            },
            error: function (response) {

            }
        });
    });

    $('#import_xls').on('click', function () {
        var _this = $(this),
            status = _this.attr('data-status'),
            data = {},
            html,
            title = 'Import Tasks from excel file';

        html = $('#import-xls-template').html();


        swal({
            title: title,
            type: 'info',
            html: html,
            customClass: 'swal2-overflow',
            onOpen: function () {
                if (status === 0) {
                    return;
                }
            },
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Import',
            focusConfirm: false
        }).then(function (result) {
            data = new FormData(document.getElementById('import-form'));

            if (result.value) {
                $('body .loading').addClass('active');
                var project_id = $('#project_id')[0].value;
                $.ajax({
                    url: '/project/' + project_id + '/import-tasks-xls',
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (res) {
                        $('body .loading').removeClass('active');
                        if (res.resType === 'success') {
                            swal({
                                type: 'success',
                                title: 'Import completed successfully',
                                showConfirmButton: false,
                                timer: 2500
                            });
                            generateDataTable(element, route);
//                                    table.ajax.reload();
                        } else {
                            swal({
                                type: 'error',
                                title: 'Error!',
                                html: res.errMsg,
                                customClass: 'swal2-overflow swal-text-left'
                            });
                        }
                    },
                    error: function (response) {
                        swal({
                            type: 'error',
                            title: 'Error!'
                        });
                    }
                });
            }
        });
    });

    element.on('click', 'a.task-refuse', function (e) {
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
                $.ajax({
                    url: '/task/' + _this.attr('data-id') + '/refuse',
                    type: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        if (res.resType === 'success') {
                            swal({
                                type: 'success',
                                title: 'Task refused successfully',
                                showConfirmButton: false,
                                timer: 2500
                            });
                            generateDataTable(element, route);
//                                    table.ajax.reload();
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

    element.on('click', 'a.updates-button', function (e) {
        e.preventDefault();
        var id;
        if ($(this).closest('tr')[0].hasAttribute('data-id')) {
            id = $(this).closest('tr').attr('data-id');
        } else {
            id = $($(this).closest('tr')[0].previousSibling).attr('data-id');
        }

        document.getElementById('updates-modal').scrollTop = 0;
        $('#updates-modal .panel-body')[0].innerHTML = "";
        $('#updates-modal .loading').addClass('active');
        $('#updates-modal').addClass('control-sidebar-open');
        $('body').addClass('open');

        var project_id = $(this).attr('data-id');

        if ($('#isTaskPage').length == 0) {
            route = 'project/' + project_id + '/task';
        }

        $.ajax({
            url: '/' + route + '/update-form-data/' + id,
            type: 'GET',
            success: function (response) {
                result = JSON.parse(response);
                var modal = $('#updates-modal');

                modal.find('.panel-heading >  h1')[0].innerHTML = '#' + result.task.id + ' ' + result.task.name;

                var html = "";
                if (result.taskUpdates.length) {
                    $.each(result.taskUpdates, function (index, value) {
                        html += "<div class='row'>" +
                            "<div class='col-xs-12'>" + value.description +
                            "<span>" + dateFormat(new Date(value.created_at.replace(/-/g, "/")), "dd mmm yyyy") + " at " + dateFormat(new Date(value.created_at.replace(/-/g, "/")), "HH:MM") + " by " + result.taskUpdatesUsers[value.user_id] + "</span>" +
                            "</div>" +
                            "</div>";
                    });
                } else {
                    html = "<div class='row'><div class='col-xs-12 text-center'>No Updates found.</div></div>";
                }

                modal.find('.panel-body')[0].innerHTML = html;

                modal.find('.loading').removeClass('active');
            },
            error: function (response) {
                swal({
                    type: 'error',
                    title: 'Your ' + title + ' has not been updated.'
                });
            }
        });
    });


    $('select#page_length').select2({
        minimumResultsForSearch: -1,
        allowClear: false
    }).on('change', function () {
        clearDatatablesStorage();
        generateDataTable(element, route);
    });

    $('#view_project_filter').on('click', function () {
        var body = $("body");
        // if (!body.hasClass('sidebar-collapse')) {
        //     body.addClass('sidebar-collapse');
        // }
        body.toggleClass('pfilters-open');
        $('#project-tasks-table').dataTable().fnDraw();
    });

    $('#project-tasks-table_paginate').on('click', function () {
        $('.task-batch-all').prop('checked', false);
    });

});

Date.prototype.addHours = function(h) {
    this.setTime(this.getTime() + (h*60*60*1000));
    return this;
};
