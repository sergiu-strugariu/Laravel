function crudDataTable(table, element, route, title) {
    // New record
    $('a.editor_create').on('click', function (e) {
        e.preventDefault();

        $.ajax({
            url: '/' + route + '/create-form',
            type: 'GET',
            success: function (html) {
                swal({
                    title: 'Add ' + title,
                    type: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Add',
                    html: html,
                    animation: false,
                    customClass: 'animated zoomIn',
                    focusConfirm: false,
                    preConfirm: function () {
                        var form = $('#datatable-form').serializeArray();
                        return new Promise(function (resolve) {
                            resolve(form);
                        })
                    },
                    onOpen: function (e) {
                        $(e).find('#swal2-content select').select2({
                            allowClear: true,
                            minimumInputLength: 1
                        });


                        switch (element[0].id) {
                            case 'project-tasks-table':
                                $(e).find('#swal2-content input#deadline').datetimepicker({showClear: true});
                                $(e).find('#swal2-content input#availability_from').datetimepicker({showClear: true});
                                $(e).find('#swal2-content input#availability_to').datetimepicker({
                                    useCurrent: false,
                                    showClear: true
                                });
                                $(e).find('#swal2-content input#availability_from').on("dp.change", function (e) {
                                    $('#availability_to').data("DateTimePicker").minDate(e.date);
                                });
                                $("#availability_to").on("dp.change", function (e) {
                                    $('#availability_from').data("DateTimePicker").maxDate(e.date);
                                });
                                break;
                            case 'project':
                                $('#participants_id').select2({
                                    placeholder: 'Please select user',
                                    tags: true,
                                    minimumInputLength: 1
                                });
                                break;
                        }
                    }
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: '/' + route + '/create',
                            type: 'POST',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            data: result.value,
                            success: function (response) {
                                result = JSON.parse(response);
                                if (result.id !== undefined || result == true || result.type === 'success') {
                                    swal(
                                        'Success!',
                                        'Your ' + title + ' has been created.',
                                        'success'
                                    );
                                    table.ajax.reload();
                                } else {
                                    swal(
                                        'Error!',
                                        'Your ' + title + ' has not been created.',
                                        'error'
                                    )
                                }
                            },
                            error: function (response) {
                                console.log(response);
                                swal(
                                    'Error!',
                                    'Your ' + title + ' has not been created.',
                                    'error'
                                )
                            }
                        });
                    }
                });
            },
            error: function (response) {
                swal(
                    'Error!',
                    'Your ' + title + ' has not been created.',
                    'error'
                )
            }
        });
    });

    element.on('click', 'a.user_editor_edit', function (e) {
        e.preventDefault();
        var id = $(this).closest('tr').attr('data-id');
        $.ajax({
            url: '/user/update-form/' + id,
            type: 'GET',
            success: function (html) {
                swal({
                    title: 'Edit ' + title,
                    type: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Save',
                    html: html,
                    animation: false,
                    customClass: 'animated zoomIn',
                    focusConfirm: false,
                    preConfirm: function () {
                        var form = $('#datatable-form').serializeArray();
                        return new Promise(function (resolve) {
                            resolve(form)
                        })
                    }
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: '/user/update/' + id,
                            type: 'POST',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            data: result.value,
                            success: function (response) {
                                result = JSON.parse(response);
                                if (result === true) {
                                    swal(
                                        'Success!',
                                        'success'
                                    );
                                    table.ajax.reload();
                                } else {
                                    swal(
                                        'Error!',
                                        'error'
                                    )
                                }
                            },
                            error: function (response) {
                                swal(
                                    'Error!',
                                    'error'
                                )
                            }
                        });
                    }
                });
            },
            error: function (response) {
                swal(
                    'Error!',
                    'Your ' + title + ' has not been updated.',
                    'error'
                )
            }
        });
    });

    element.on('click', 'a.user_editor_remove', function (e) {
        e.preventDefault();
        var id = $(this).closest('tr').attr('data-id');

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
                    url: '/' + title + '/delete/' + id,
                    type: 'GET',
                    success: function (response) {
                        result = JSON.parse(response);
                        if (result === true) {
                            swal(
                                'Success!',
                                'Your ' + title + ' has been deleted.',
                                'success'
                            );
                            table.ajax.reload();
                        } else {
                            swal(
                                'Error!',
                                'Your ' + title + ' has not been deleted.',
                                'error'
                            )
                        }
                    },
                    error: function (response) {
                        swal(
                            'Error!',
                            'Your ' + title + ' has not been deleted.',
                            'error'
                        )
                    }
                });
            }
        });
    });

// Edit record
    element.on('click', 'a.editor_edit', function (e) {
        e.preventDefault();
        var id = $(this).closest('tr').attr('data-id');

        $.ajax({
            url: '/' + route + '/update-form/' + id,
            type: 'GET',
            success: function (html) {
                swal({
                    position: 'right',
                    title: 'Edit ' + title,
                    showCancelButton: true,
                    showCloseButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Save',
                    html: html,
                    animation: false,
                    customClass: 'animated zoomIn modal-right',
                    focusConfirm: false,
                    preConfirm: function () {
                        var form = $('#datatable-form').serializeArray();
                        return new Promise(function (resolve) {
                            resolve(form)
                        })
                    },
                    onOpen: function (e) {
                        $(e).find('#swal2-content select').select2({
                            placeholder: 'Any',
                            allowClear: true
                        });


                        var parent = $("#parent_id").val();
                        if (parent !== undefined ) {
                            $.ajax({
                                type: 'GET',
                                url: '/project/getClientProjects/' + $("#parent_id").val(),
                                headers: {'X-CSRF-TOKEN': _token},
                                dataType: 'json',
                                success: function (res) {
                                    // result = JSON.parse(res);
                                    var options = [];

                                    $.each(res, function (key, value) {
                                        options.push({id: value.id, name: value.name})
                                    });


                                    $('select#project_participating_id').select2({
                                        tags: "true",
                                        placeholder: 'Any',
                                        allowClear: true
                                    });
                                    for (var i = 0; i < options.length; i++) {
                                        $('#project_participating_id').append('<option value="' + options[i].id + '">' + options[i].name + '</option>');
                                        if ($('#isParticipant').val() == 1) {
                                            $('#project_participating_id').children("option[value=" + options[i].id + "]").prop("selected", true);
                                        }
                                    }

                                },
                                error: function (res) {
                                }
                            });
                        }

                        switch (element[0].id) {
                            case 'project-tasks-table':
                                var deadline = $(e).find('#swal2-content input#deadline');
                                var deadlineTime = deadline.value;
                                deadline.datetimepicker({showClear: true});
                                deadline.value = deadlineTime;
                                $(e).find('#swal2-content input#availability_from').datetimepicker({showClear: true});
                                $(e).find('#swal2-content input#availability_to').datetimepicker({
                                    useCurrent: false,
                                    showClear: true
                                });
                                $(e).find('#swal2-content input#availability_from').on("dp.change", function (e) {
                                    $('#availability_to').data("DateTimePicker").minDate(e.date);
                                });
                                $("#availability_to").on("dp.change", function (e) {
                                    $('#availability_from').data("DateTimePicker").maxDate(e.date);
                                });
                                $(e).find('select#language_id').on('change', function () {
                                    if (this.value.length) {
                                        $.ajax({
                                            url: '/project/language-assessors/' + this.value,
                                            type: 'GET',
                                            success: function (response) {
                                                result = JSON.parse(response);

                                                var options = [];

                                                $.each(result, function (key, value) {
                                                    options.push({id: key, text: value})
                                                });

                                                $(e).find('select#assessor_id').empty().select2({
                                                    data: options,
                                                    placeholder: 'Any',
                                                    allowClear: true,
                                                    minimumInputLength: 1
                                                });
                                            }
                                        });
                                    } else {
                                        $(e).find('select#assessor_id').empty().select2({
                                            data: [],
                                            placeholder: 'Any',
                                            allowClear: true,
                                            minimumInputLength: 1
                                        });
                                    }
                                });
                                break;
                        }
                    }
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: '/' + route + '/update/' + id,
                            type: 'POST',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            data: result.value,
                            success: function (response) {
                                // result = JSON.parse(response);

                                if (response.type === "true" || response === "true") {
                                    swal(
                                        'Success!',
                                        'Your ' + title + ' has been updated.',
                                        'success'
                                    );
                                    table.ajax.reload();
                                } else {
                                    swal(
                                        'Error!',
                                        'Your ' + title + ' has not been updated.',
                                        'error'
                                    )
                                }
                            },
                            error: function (response) {
                                swal(
                                    'Error!',
                                    'Your ' + title + ' has not been updated.',
                                    'error'
                                )
                            }
                        });
                    }
                });
            },
            error: function (response) {
                swal(
                    'Error!',
                    'Your ' + title + ' has not been updated.',
                    'error'
                )
            }
        });
    });

// Delete a record
    element.on('click', 'a.editor_remove', function (e) {
        e.preventDefault();
        var id = $(this).closest('tr').attr('data-id');
        var project_id = $(this).attr('data-id');

        if ($('#isTaskPage').length == 0) {
            route = 'project/' + project_id + '/task';
        }

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
                    url: '/' + route + '/delete/' + id,
                    type: 'GET',
                    success: function (res) {

                        var action = "deleted";
                        if(title == "Task"){
                            action = 'canceled';
                        }
                        if (res.resType == 'success' || res.type === "true" || res === "true") {
                            swal('Success!', 'Your ' + title + ' has been ' + action + '.', 'success');
                            table.ajax.reload();
                        } else {
                            swal('Error!', res.errMsg, 'error')
                        }
                    },
                    error: function (response) {
                        var action = "deleted";
                        if(title == "Task"){
                            action = 'canceled';
                        }
                        swal('Error!', 'Your ' + title + ' has been ' + action + '.', 'error');
                    }
                });
            }
        });
    });
}