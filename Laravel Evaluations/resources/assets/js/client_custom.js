function client(table, index, element) {

    $(document).off('click', '.submit_project_participant').on('click', '.submit_project_participant', function (e) {
        if (index == $(this).attr('data-id')) {
            var _this = $(this);
            e.preventDefault();

            var first_name = $("input[name*='first_name']").val();
            var last_name = $("input[name*='last_name']").val();
            var email = $("input[name*='email']").val();
            var projectsId = $('#projects_id_' + _this.attr("data-id") + ' ').val();

            var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
            var valid = true;

            if (email == '' || email == undefined) {
                swal(
                    'Error!',
                    'Email is required!',
                    'error'
                );
                valid = false;
            }

            if (projectsId == '' || projectsId == undefined) {
                swal(
                    'Error!',
                    'Projects are required!',
                    'error'
                );
                valid = false;
            }
            if (pattern.test(email) == false) {
                swal(
                    'Error!',
                    'Please enter a correct email address!',
                    'error'
                );
                valid = false;
            }

            if ((email == '' || email == undefined) && (projectsId == '' || projectsId == undefined)) {
                swal(
                    'Error!',
                    'Projects and email are required!',
                    'error'
                );
                valid = false;
            }

            if (valid == true) {

                $.ajax({
                    type: 'POST',
                    url: '/admin/createClientParticipant',
                    headers: {'X-CSRF-TOKEN': _token},
                    data: {
                        first_name: first_name,
                        last_name: last_name,
                        email: email,
                        projectsId: projectsId,
                        client_id: _this.attr("data-id")
                    },
                    dataType: 'json',
                    success: function (res) {
                        if (res.resType == 'Success') {
                            swal(
                                'Success!',
                                'User has been created.',
                                'success'
                            );
                            table.ajax.reload();
                        } else {
                            swal(
                                'Error!',
                                res.resMotive,
                                'error'
                            );
                        }
                    },
                    error: function (res) {
                    }
                });
            }
        }
    });


    $(document).off('click', '.remove_client').on('click', '.remove_client', function () {

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
                    url: '/admin/removeUser/' + _this.attr('data-id'),
                    headers: {'X-CSRF-TOKEN': _token},
                    dataType: 'json',
                    success: function (res) {
                        if (res.resType == 'success') {
                            swal(
                                'Success!',
                                'User has been deleted!',
                                'success'
                            );
                            table.ajax.reload();
                        } else {
                            swal(
                                'Error!',
                                'User has not been deleted!',
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

    $(document).off('click', '.activate_client').on('click', '.activate_client', function () {
        var _this = $(this);

        $.ajax({
            type: 'GET',
            url: '/admin/activateUser/' + _this.attr('data-id'),
            headers: {'X-CSRF-TOKEN': _token},
            dataType: 'json',
            success: function (res) {
                if (res.resType == 'success') {
                    swal(
                        'Success!',
                        'User has been activated!',
                        'success'
                    );
                    table.ajax.reload();
                } else {
                    swal(
                        'Error!',
                        'User has not been activated!',
                        'error'
                    );
                }
            },
            error: function (res) {
            }
        });
    });

    $(document).off('click', '.edit_client_button').on('click', '.edit_client_button', function (e) {

        var _this = $(this);

        var form = _this.parents('form:first');
        form.show();
        form.validate({
            submitHandler: function (form, e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: '/project/updateParticipants/' + _this.attr('data-model') + '/project/' + _this.attr('data-id'),
                    data: $(form).serialize(),
                    headers: {'X-CSRF-TOKEN': _token},
                    dataType: 'json',
                    success: function (res, form) {
                        if (res.resType == 'Success') {
                            swal(
                                'Success!',
                                'Client projects participating have been update!',
                                'success'
                            );
                            table.ajax.reload();
                        } else {
                            swal(
                                'Error!',
                                'Client projects participating have not been updated!',
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


}