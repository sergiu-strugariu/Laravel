function createCssRecruiter(table) {

    $(document).off('click', '.submit_css_recruiter').on('click', '.submit_css_recruiter', function (e) {

        var _this = $(this);
        e.preventDefault();

        var first_name = $("input[name*='first_name']").val();
        var last_name = $("input[name*='last_name']").val();
        var email = $("input[name*='email']").val();
        var role = $('#role').val();

        var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
        if (email == '' || email == undefined || pattern.test(email) == false) {
            swal(
                'Error!',
                'Email is required!',
                'error'
            );
        } else {

            $.ajax({
                type: 'POST',
                url: '/admin/createUserByRoleId/' + role,
                headers: {'X-CSRF-TOKEN': _token},
                data: {
                    first_name: first_name,
                    last_name: last_name,
                    email: email,
                    role: role
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
    });

    $(document).off('click', '.remove_css_recruiter').on('click', '.remove_css_recruiter', function (e) {

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

    $(document).off('click', '.activate_css_recruiter').on('click', '.activate_css_recruiter', function (e) {
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

}