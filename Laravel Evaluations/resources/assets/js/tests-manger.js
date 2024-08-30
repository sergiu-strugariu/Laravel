var testsManagerTestID = null;
var tableGeneratorFunction = null;
var languageUseTaginput = $('.tagsinput');

$(document).ready(function () {
    $('.header-content #show_filters').on('click', function (e) {
        $('#filters').slideToggle('slow');
    });

    if ($("#reset_filters").length) {
        document.getElementById("reset_filters").onclick = function () {
            $('.column_filter').val('').trigger('change')
        };
    }

    $('.tests-manager-section select.column_filter').select2({
        allowClear: true
    });

    if (tableGeneratorFunction !== null) {
        tableGeneratorFunction();
    }

    $(document).on('click', '.add_lang_test_type', function (e) {
        e.preventDefault();

        $('#language_id').val($(this).attr('data-id'));
        $('#add_new_test_type').addClass('control-sidebar-open');
        $('body').addClass('open');
    });

    $(document).on('click', '.add_new_test_type_button', function (e) {
        AjaxCall({
            url: '/admin/createTestType',
            resetForm: false
        }).makeCall($(this), function (res) {

            var select = $('#paper_type_id');
            var optionVal = select.val();
            var optionText = select.find('option:selected').text();

            var clientTemplate = $('#test-type-template').html();
            var newItem = $(clientTemplate);
            newItem.attr('data-model', res.data.id);

            newItem.find('a').html(optionText);

            if (optionVal != 2) { //test speaking
                newItem.find('a').attr('href', '/admin/tests/' + res.data.id);
            }

            newItem.find('.test-types-check').attr('data-id', res.data.id);
            $('#toggle-' + res.data.language_id).find('.add_lang_test_type').before(newItem);
            $('.client-name[data-target="#toggle-' + res.data.language_id + '"]').addClass('test-type-lang').attr('data-toggle', 'collapse').bind("click", updateTestElements);
            $('.grid').masonry();
            $('.add-new-project-modal').removeClass('control-sidebar-open');
            $('body').removeClass('open');

        })
    });

    function updateTestElements() {
        var target = $($(this).attr('data-target'));
        if (target.hasClass('collapsing')) {
            return;
        }
        if (!$(this).hasClass('opened')) {
            $(this).addClass('opened');
            $(this).find('.expand-list').addClass('fa-chevron-up').removeClass('fa-chevron-down');
        } else {
            $(this).removeClass('opened');
            $(this).find('.expand-list').addClass('fa-chevron-down').removeClass('fa-chevron-up');
        }
        setTimeout(function () {
            $('.grid').masonry();
        }, 400);
    }

    $('.test-type-lang').click(updateTestElements);

    $('.add_new_question_button').on('click', function (e) {

        AjaxCall({
            ignoreValidation: '.ignore-validation, input:hidden',
            url: $(this).parents('form:first').attr('action')
        }).makeCall($(this), function(res){
            tableGeneratorFunction();
            $('.add-new-project-modal').removeClass('control-sidebar-open');
            $('body').removeClass('open');
        });

    });

    $(document).on('click', '.add_listening_question_button', function (e) {

        var form = $(this).parents('form:first');

        var submitHandler = function () {

            var formData = new FormData($('#add_listening_question_form')[0]);
            var action = form.attr('action');

            $.ajax({
                url: action,
                type: 'POST',
                data: formData,
                success: function (res) {
                    if (res.resType == 'success') {
                        swal('Success', 'Question added!', 'success').then(function () {
                            form[0].reset();
                            $(".control-sidebar").removeClass('control-sidebar-open');
                            $('body').removeClass('open');

                            if (res.data.redirectTo) {
                                window.location.href = res.data.redirectTo;
                            }

                        });
                    } else {

                        var error_messages = '';

                        if( $.type(  res.errMsg  ) === "string" ){
                            error_messages = res.errMsg;
                        } else {
                            $.each(res.errMsg, function(element, error){
                                error_messages += error + '<br>';
                            });
                        }


                        swal('Error', error_messages, 'error');
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
        };


        form.show();
        form.validate({
            submitHandler: submitHandler
        });

    });

    $(document).on('click', '.edit_listening_question_button', function (e) {

        var form = $(this).parents('form:first');
        var questionID = $(this).attr('data-id');

        var submitHandler = function () {

            var formData = new FormData($('#edit_listening_question_form')[0]);
            var action = form.attr('action');

            $.ajax({
                url: '/admin/questions/' + questionID,
                type: 'POST',
                data: formData,
                success: function (res) {
                    if (res.resType == 'success') {
                        swal('Success', 'Question updated!', 'success').then(function () {
                            form[0].reset();
                            $(".control-sidebar").removeClass('control-sidebar-open');
                            $('body').removeClass('open');
                        });
                    } else {

                        var errMsg = res.errMsg;

                        if (typeof errMsg === 'object') {
                            var errors = '';
                            $.each(errMsg, function (field, error) {
                                errors += error + '<br>';
                            });

                            errMsg = errors;
                        }

                        swal('Error', errMsg, 'error');
                    }
                },
                cache: false,
                contentType: false,
                processData: false,
                complete: tableGeneratorFunction
            });
        };


        form.show();
        form.validate({
            ignore: '.ignore-validation',
            submitHandler: submitHandler
        });

    });

    $(document).on('click', '.edit_question_button', function (e) {
        try {
            var form = $(this).parents("form");
            var type = form.find("select[name=language_use_type]").val();
            if (type != 1) {
                var gapValue = form.find("input[name=lu_gap_answer]").val();
                form.find(".tab_lu_1").find("input[name*=answer]").first().val(gapValue);
            }
        } catch (e) {
            console.error(e.message);
        }
        AjaxCall({
            ignoreValidation: '.ignore-validation, input:hidden',
            url: '/admin/questions/' + $(this).attr('data-id')
        }).makeCall($(this), function(res){
            $('#edit_question').removeClass('control-sidebar-open');
            $('body').removeClass('open');
            tableGeneratorFunction();
        });

    });

    $(document).on('click', '.edit_choice_button', function (e) {

        AjaxCall({
            url: '/admin/questions/choice/' + $(this).attr('data-id')
        }).makeCall($(this), tableGeneratorFunction);

    });

    $(document).on('click', '.add_question', function (e) {
        e.preventDefault();
        $('#add_new_question').addClass('control-sidebar-open');
        $('body').addClass('open');
    });

    var submitTimer = null;

    $(document).on('keyup', 'input.column_filter', function () {

        clearTimeout(submitTimer);
        submitTimer = setTimeout(function () {
            if( tableGeneratorFunction !== null )
            tableGeneratorFunction();
        }, 500);

    });

    $('select.column_filter').on('change', function () {
        if (tableGeneratorFunction !== null) {
            tableGeneratorFunction();
        }
    });

    $(document).on('click', 'a.questions-edit-button', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');

        $('#edit_question').addClass('control-sidebar-open');
        $('body').addClass('open');

        $.ajax({
            url: '/admin/questions/' + id,
            type: 'GET',
            success: function (response) {
                $('#edit_question').html(response);

            },
            error: function (response) {
                swal({
                    type: 'error',
                    title: 'The question cannot be updated.'
                });
            }
        });
    });

    $(document).on('click', 'a.question-choice-edit-button', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');

        $('#edit_question').addClass('control-sidebar-open');
        $('body').addClass('open');

        $.ajax({
            url: '/admin/questions/choice/' + id,
            type: 'GET',
            success: function (response) {
                $('#edit_question').html(response);

            },
            error: function (response) {
                swal({
                    type: 'error',
                    title: 'The question cannot be updated.'
                });
            }
        });
    });

    $(document).on('click', '.question-activate-item', function () {
        AjaxCall({
            'url': '/admin/questions/activate/' + $(this).attr('data-id'),
            'method': 'GET',
            'validate': false,
            'successMsg': 'Question has been activated!',
        }).makeCall($(this), tableGeneratorFunction);
    });

    $(document).on('click', '.question-deactivate-item', function () {
        AjaxCall({
            'url': '/admin/questions/deactivate/' + $(this).attr('data-id'),
            'method': 'GET',
            'validate': false,
            'successMsg': 'Question has been deactivated!',
            'errorMsg': 'Question was not deactivated'
        }).makeCall($(this), tableGeneratorFunction);
    });

    $(document).on('click', '.question-force-delete-item', function () {

        var _this = $(this);
        swal({
            title: "Permanent delete question?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            focusConfirm: false
        }).then(function (result) {
            if (result.value) {
                AjaxCall({
                    'url': '/admin/questions/force-delete/' + _this.attr('data-id'),
                    'method': 'GET',
                    'validate': false,
                    'successMsg': 'Question has been deleted!',
                }).makeCall(_this, tableGeneratorFunction);
            }
        });

    });

    $(document).on('click', '.choice-activate-item', function () {
        AjaxCall({
            'url': '/admin/questions/choice/activate/' + $(this).attr('data-id'),
            'method': 'GET',
            'validate': false,
            'successMsg': 'Answer has been activated!',
            'errorMsg': 'Answer was not activated'
        }).makeCall($(this), tableGeneratorFunction);
    });

    $(document).on('click', '.test-types-check.active', function () {
        var btn = $(this);
        AjaxCall({
            'url': '/admin/questions/testType/deactivate/' + $(this).attr('data-id'),
            'method': 'GET',
            'validate': false,
            'successMsg': 'Test type has been deactivated!',
            'errorMsg': 'Test type was not deactivated'
        }).makeCall($(this), function () {
            btn.removeClass('active').addClass('inactive');
        });
    });

    $(document).on('click', '.test-types-check.inactive', function () {
        var btn = $(this);
        AjaxCall({
            'url': '/admin/questions/testType/activate/' + $(this).attr('data-id'),
            'method': 'GET',
            'validate': false,
            'successMsg': 'Test type has been activated!',

        }).makeCall($(this), function () {
            btn.removeClass('inactive').addClass('active');
        });
    });

    $(document).on('click', '.choice-deactivate-item', function () {
        AjaxCall({
            'url': '/admin/questions/choice/deactivate/' + $(this).attr('data-id'),
            'method': 'GET',
            'validate': false,
            'successMsg': 'Answer has been deactivated!',
            'errorMsg': 'Answer was not deactivated'
        }).makeCall($(this), tableGeneratorFunction);
    });

    $('.lang_use_type_select').change(function () {
        $('.lu_tabs').addClass('hidden');
        if ($(this).val() == 1) {
            $('.lu_tabs').find("textarea[name=body_reading]")
                .prop("required", "required")
                .attr("required", "required");
        } else {
            $('.lu_tabs').find("textarea[name=body_reading]")
                .removeProp("required")
                .removeAttr("required");
        }
        $('.tab_lu_' + $(this).val()).removeClass('hidden');
    });


    languageUseTaginput.tagsinput({
        allowDuplicates: true
    });
    languageUseTaginput.on('itemAdded', updateLUArrangeQuestion);
    languageUseTaginput.on('itemRemoved', updateLUArrangeQuestion);

});

var generateWritingQuestionsTable = function () {
    var filters = {};
    $('.column_filter').each(function () {
        filters[this.name] = this.value;
    });

    return $('#table').DataTable({
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
            url: '/admin/questions/datatable/' + testsManagerTestID,
            type: "GET",
            data: {
                filters: filters
            }
        }, createdRow: function createdRow(row, data) {
            console.log(row);
            $(row).attr('data-id', data.id);
        },
        columns: [
            {
                data: 'id',
                name: 'id',
                orderable: true,
                searchable: true,
                render: function(data) {
                    console.log(data);
                    return "V" + data;
                }
            },
            {
                data: 'description',
                name: 'description',
                orderable: true,
                searchable: true,
                className: "question-body"
            },
            {
                data: null,
                name: 'time',
                orderable: true,
                searchable: true
            },
            {
                data: 'deleted_at',
                render: function (data) {
                    data = data == null ? 'Active' : 'Inactive';
                    return data;
                },
                name: 'deleted_at',
                orderable: false,
                searchable: false
            },
            {
                data: null,
                className: "actions",
                defaultContent: '',
                orderable: false,
                searchable: false
            }
        ], "fnRowCallback": function fnRowCallback(nRow, aData) {

            var buttons = '';

            //edit btn
            buttons += '<a class="action-button questions-edit-button" href="#" data-id="' + aData.id + '"><span class="fa fa-pencil"></span></a>';

            if (aData.deleted_at) {
                buttons += '<a href="#" class="action-button confirm_css_recruiter question-activate-item" data-id="' + aData.id + '"><span class="fa fa-check"></span></a>';
                buttons += '<a href="#" class="action-button question-force-delete-item" data-id="' + aData.id + '">DELETE</a>';
            } else {
                buttons += '<a href="#" class="action-button demo-close question-deactivate-item" data-id="' + aData.id + '"><i class="fa fa-times"></i></a>';
            }
            $('td:eq(2)', nRow).html(aData.minutes + 'm ' + aData.seconds + 's');
            $('td:eq(-1)', nRow).html(buttons);
            return nRow;
        },
        "fnDrawCallback": function () {
            var paginateRow = $('.dataTables_paginate');
            var pageCount = Math.ceil((this.fnSettings().fnRecordsDisplay()) / this.fnSettings()._iDisplayLength);
            if (pageCount > 1) {
                paginateRow.css("display", "block");
            } else {
                paginateRow.css("display", "none");
            }
        }
    });

};

var generateReadingQuestionsTable = function () {

    var filters = {};
    $('.column_filter').each(function () {
        filters[this.name] = this.value;
    });

    return $('#table').DataTable({
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
            url: '/admin/questions/datatable/' + testsManagerTestID,
            type: "GET",
            data: {
                filters: filters
            }
        }, createdRow: function createdRow(row, data) {
            $(row).attr('data-id', data.id);
        },
        columns: [
            {
                data: 'code',
                name: 'code',
                orderable: true,
                searchable: true,
                className: "question-body"
            },
            {
                data: 'description',
                name: 'description',
                orderable: false,
                searchable: true
            },
            {
                data: 'level.name',
                name: 'level',
                orderable: false,
                searchable: true
            },
            {
                data: 'q_type',
                name: 'q_type',
                orderable: true,
                searchable: true
            },
            {
                data: null,
                name: 'time',
                orderable: false,
                searchable: true
            },
            {
                data: 'deleted_at',
                render: function (data) {
                    data = data == null ? 'Active' : 'Inactive';
                    return data;
                },
                name: 'deleted_at',
                orderable: false,
                searchable: false
            },
            {
                data: null,
                className: "actions",
                defaultContent: '',
                orderable: false,
                searchable: false
            }
        ], "fnRowCallback": function fnRowCallback(nRow, aData) {

            var buttons = '';

            // choices btn
            //buttons += '<a class="action-button" href="/admin/questions/' + aData.id + '/choices" title="Add question answers"><span class="fa fa-plus"></span></a>';

            //edit btn
            buttons += '<a class="action-button questions-edit-button" href="#" data-id="' + aData.id + '"><span class="fa fa-pencil"></span></a>';

            if (aData.deleted_at) {
                buttons += '<a href="#" class="action-button confirm_css_recruiter question-activate-item" data-id="' + aData.id + '"><span class="fa fa-check"></span></a>';
                buttons += '<a href="#" class="action-button question-force-delete-item" data-id="' + aData.id + '">DELETE</a>';

            } else {
                buttons += '<a href="#" class="action-button demo-close question-deactivate-item" data-id="' + aData.id + '"><i class="fa fa-times"></i></a>';
            }
            $('td:eq(0)', nRow).html('<a href="/admin/questions/' + aData.id + '/choices">' + aData.code + ' </a>');
            $('td:eq(4)', nRow).html(aData.minutes + 'm ' + aData.seconds + 's');
            $('td:eq(-1)', nRow).html(buttons);
            return nRow;
        },
        "fnDrawCallback": function () {
            var paginateRow = $('.dataTables_paginate');
            var pageCount = Math.ceil((this.fnSettings().fnRecordsDisplay()) / this.fnSettings()._iDisplayLength);
            if (pageCount > 1) {
                paginateRow.css("display", "block");
            } else {
                paginateRow.css("display", "none");
            }
        }
    });

};

var generateLanguageUseTable = function () {

    var filters = {};
    $('.column_filter').each(function () {
        filters[this.name] = this.value;
    });

    return $('#table').DataTable({
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
            url: '/admin/questions/datatable/' + testsManagerTestID,
            type: "GET",
            data: {
                filters: filters
            }
        }, createdRow: function createdRow(row, data) {
            $(row).attr('data-id', data.id);
        },
        columns: [
            {
                data: 'v_number',
                name: 'v_number',
                orderable: true,
                searchable: true,
                className: "question-body"
            },
            {
                data: 'body',
                name: 'body',
                orderable: true,
                searchable: true,
                className: "question-body"
            },
            {
                data: 'description',
                name: 'description',
                orderable: false,
                searchable: true
            },
            {
                data: 'deleted_at',
                render: function (data) {
                    data = data == null ? 'Active' : 'Inactive';
                    return data;
                },
                name: 'deleted_at',
                orderable: false,
                searchable: false
            },
            {
                data: null,
                className: "actions",
                defaultContent: '',
                orderable: false,
                searchable: false
            }
        ], "fnRowCallback": function fnRowCallback(nRow, aData) {

            var buttons = '';

            // choices btn
            // buttons += '<a class="action-button" href="/admin/questions/' + aData.id + '/choices" title="Add question answers"><span class="fa fa-plus"></span></a>';

            //edit btn
            buttons += '<a class="action-button questions-edit-button" href="#" data-id="' + aData.id + '"><span class="fa fa-pencil"></span></a>';

            if (aData.deleted_at) {
                buttons += '<a href="#" class="action-button confirm_css_recruiter question-activate-item" data-id="' + aData.id + '"><span class="fa fa-check"></span></a>';
                buttons += '<a href="#" class="action-button question-force-delete-item" data-id="' + aData.id + '">DELETE</a>';

            } else {
                buttons += '<a href="#" class="action-button demo-close question-deactivate-item" data-id="' + aData.id + '"><i class="fa fa-times"></i></a>';
            }
            $('td:eq(1)', nRow).html('<a href="/admin/questions/' + aData.id + '/choices"> ' + aData.body + ' </a>');
            $('td:eq(-1)', nRow).html(buttons);
            return nRow;
        },
        "fnDrawCallback": function () {
            var paginateRow = $('.dataTables_paginate');
            var pageCount = Math.ceil((this.fnSettings().fnRecordsDisplay()) / this.fnSettings()._iDisplayLength);
            if (pageCount > 1) {
                paginateRow.css("display", "block");
            } else {
                paginateRow.css("display", "none");
            }
        }
    });

};


/**
 * Language use new datatable
 * @returns {jQuery}
 */
var generateLanguageQuestionsTable = function () {
    var filters = {};
    $('.column_filter').each(function () {
        filters[this.name] = this.value;
    });

    var lu_types = {
        1: 'Multiple choices',
        2: 'Arrange words',
        3: 'Fill in gap'
    };

    return $('#table').DataTable({
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
            url: '/admin/questions/datatable/' + testsManagerTestID,
            type: "GET",
            data: {
                filters: filters
            }
        }, createdRow: function createdRow(row, data) {
            $(row).attr('data-id', data.id);
        },
        columns: [
            {
                data: 'code',
                name: 'code',
                orderable: true,
                searchable: true,
                className: "question-body"
            },
            {
                data: 'description',
                name: 'description',
                orderable: true,
                searchable: true
            },
            {
                data: 'level.name',
                name: 'level',
                orderable: false,
                searchable: true
            },
            {
                data: 'q_type',
                name: 'q_type',
                orderable: true,
                searchable: true
            },
            {
                data: null,
                name: 'type',
                orderable: true,
                searchable: true
            },
            {
                data: null,
                name: 'time',
                orderable: false,
                searchable: false
            },
            {
                data: 'deleted_at',
                render: function (data) {
                    data = data == null ? 'Active' : 'Inactive';
                    return data;
                },
                name: 'deleted_at',
                orderable: false,
                searchable: false
            },
            {
                data: null,
                className: "actions",
                defaultContent: '',
                orderable: false,
                searchable: false
            }
        ], "fnRowCallback": function fnRowCallback(nRow, aData) {

            var buttons = '';

            if (aData.language_use_type == 1) {
                // choices btn
               // buttons += '<a class="action-button" href="/admin/questions/' + aData.id + '/choices" title="Add question answers"><span class="fa fa-plus"></span></a>';
                $('td:eq(0)', nRow).html('<a href="/admin/questions/' + aData.id + '/choices">' + aData.code + '</a>');
            // } else if (aData.language_use_type == 2) {
                //$('td:eq(0)', nRow).html(JSON.parse(aData.body.replace(/&quot;/g, '"')).join(' '));
            } else {
                $('td:eq(0)', nRow).html(aData.code);
            }

            //edit btn
            buttons += '<a class="action-button questions-edit-button" href="#" data-id="' + aData.id + '"><span class="fa fa-pencil"></span></a>';

            if (aData.deleted_at) {
                buttons += '<a href="#" class="action-button confirm_css_recruiter question-activate-item" data-id="' + aData.id + '"><span class="fa fa-check"></span></a>';
                buttons += '<a href="#" class="action-button question-force-delete-item" data-id="' + aData.id + '">DELETE</a>';

            } else {
                buttons += '<a href="#" class="action-button demo-close question-deactivate-item" data-id="' + aData.id + '"><i class="fa fa-times"></i></a>';
            }

            $('td:eq(4)', nRow).html(lu_types[aData.language_use_type]);
            $('td:eq(5)', nRow).html(aData.minutes + 'm ' + aData.seconds + 's');
            $('td:eq(-1)', nRow).html(buttons);
            return nRow;
        },
        "fnDrawCallback": function () {
            var paginateRow = $('.dataTables_paginate');
            var pageCount = Math.ceil((this.fnSettings().fnRecordsDisplay()) / this.fnSettings()._iDisplayLength);
            if (pageCount > 1) {
                paginateRow.css("display", "block");
            } else {
                paginateRow.css("display", "none");
            }
        }
    });

};

var generateQuestionsChoicesTable = function () {

    var filters = {};
    $('.column_filter').each(function () {
        filters[this.name] = this.value;
    });

    return $('#table').DataTable({
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
            url: '/admin/questions/datatable/' + testsManagerTestID + '/choices',
            type: "GET",
            data: {
                filters: filters
            }
        }, createdRow: function createdRow(row, data) {
            $(row).attr('data-id', data.id);
        },
        columns: [
            {
                data: 'answer',
                name: 'answer',
                orderable: false,
                searchable: false,
                className: "question-body"
            },
            {
                data: null,
                name: 'correct',
                orderable: false,
                searchable: false
            },
            {
                data: null,
                className: "actions",
                defaultContent: '',
                orderable: false,
                searchable: false
            }
        ], "fnRowCallback": function fnRowCallback(nRow, aData) {

            var buttons = '';

            //edit btn
            buttons += '<a class="action-button question-choice-edit-button" href="#" data-id="' + aData.id + '"><span class="fa fa-pencil"></span></a>';

            if (aData.deleted_at) {
                buttons += '<a href="#" class="action-button confirm_css_recruiter choice-activate-item" data-id="' + aData.id + '"><span class="fa fa-check"></span></a>';
                buttons += '<a href="#" class="action-button question-force-delete-item" data-id="' + aData.id + '">DELETE</a>';

            } else {
                buttons += '<a href="#" class="action-button demo-close choice-deactivate-item" data-id="' + aData.id + '"><i class="fa fa-times"></i></a>';
            }
            $('td:eq(1)', nRow).html(aData.correct == 1 ? 'Correct' : 'Incorrect');
            $('td:eq(-1)', nRow).html(buttons);
            return nRow;
        },
        "fnDrawCallback": function () {
            var paginateRow = $('.dataTables_paginate');
            var pageCount = Math.ceil((this.fnSettings().fnRecordsDisplay()) / this.fnSettings()._iDisplayLength);
            if (pageCount > 1) {
                paginateRow.css("display", "block");
            } else {
                paginateRow.css("display", "none");
            }
        }
    });

};

function jsArrayShuffle(a) {
    var j, x, i;
    for (i = a.length - 1; i > 0; i--) {
        j = Math.floor(Math.random() * (i + 1));
        x = a[i];
        a[i] = a[j];
        a[j] = x;
    }
    return a;
}

function updateLUArrangeQuestion(input) {
    var words = languageUseTaginput.tagsinput('items');
    $('input[name="body_arrange_json"]').val(JSON.stringify(words));

    // Handle the incorrect pattern field
    var incorrectPatternContainer = $(".incorrect-pattern-container");
    var patternBlocks = incorrectPatternContainer.find(".incorrect-pattern");
    var incorrectPatternInput = $("input[name=body_incorrect]");

    if (words && words.length > 0) {
        var shuffledWords = jsArrayShuffle([].concat(words));
        incorrectPatternContainer.removeClass("hidden");
        patternBlocks.html("");
        shuffledWords.forEach(function(item) {
            var elt = $("<li draggable='true'>"+item+"</li>");
            elt.appendTo(patternBlocks);
        });
        incorrectPatternInput.val(JSON.stringify(shuffledWords));
    } else {
        incorrectPatternContainer.addClass("hidden");
    }


    Sortable.create(patternBlocks[0], {
        sort: true,
        group: {
            name: 'advanced',
            pull: true,
            put: true
        },
        onEnd: function (evt, originalEvent) {
            var data = [];
            patternBlocks.find("li").each(function() {
                data.push($(this).text());
            });

            incorrectPatternInput.val(JSON.stringify(data));
        }
    });
}

var tmp_id_choice = 0;

$(document).ready(function(){
    $(document).on('click', '.btn-add-choice', function(e){
        tmp_id_choice++;
        var tr = $(this).closest('tr'),
            trChoices = $(this).closest('table').find('tr.end-choices');

        if (!tr.find('[name="answer_template"]').val()) {
            tr.find('[name="answer_template"]').focus();
            return false;
        }

        var newTr = tr.clone();
        newTr.find('.btn-add-choice')
            .removeClass('btn-add-choice')
            .removeClass('btn-primary')
            .addClass('btn-delete-choice')
            .addClass('btn-danger')
            .text('Delete');
        newTr.find('[name="correct_template"]').attr('name', 'correct[new-'+tmp_id_choice+']');
        newTr.find('[name="answer_template"]').attr('name', 'answer[new-'+tmp_id_choice+']');
        newTr.find('[name="status_template"]').attr('name', 'status[new-'+tmp_id_choice+']');

        tr.find('[name="answer_template"]').val('');

        trChoices.before(newTr);
    });

    $(document).on('click', '.btn-delete-choice', function(e){
        $(this).closest('tr').remove();
    });

    // Reset form on trigger
    var addQuestionForm = $("#add_new_question_form");
    if (addQuestionForm.length > 0) {
        addQuestionForm.on("custom-ajax-success", function() {
            // Clear tags and randomized word box
            var tagsInput = $('.tagsinput');
            tagsInput.tagsinput('removeAll');
            tagsInput.trigger('itemRemoved');

            // Reset the form view
            $("select[name=language_use_type]").trigger("change")
        })
    }
});