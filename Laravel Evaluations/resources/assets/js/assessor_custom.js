function assessor(table, index, element) {
    $(document).on('click', '.submit_group_user', function (e) {
        if (index == $(this).attr('data-group')) {
            var _this = $(this);
            e.preventDefault();

            var assessor_id = $('#user_id_' + _this.attr('data-group') + ' ').val();
            var native = Number($('#native_' + _this.attr('data-group') + ' ')[0].checked);

            $.ajax({
                type: 'POST',
                url: '/group/' + _this.attr('data-group') + '/user/create',
                headers: {'X-CSRF-TOKEN': _token},
                data: {
                    user_id: assessor_id,
                    native: native
                },
                success: function (data) {
                    result = JSON.parse(data);
                    if (result.id !== undefined) {
                        swal({
                            type: 'success',
                            title: 'Assessor has been added to group.',
                            showConfirmButton: false,
                            timer: 2500
                        });
                        table.ajax.reload();
                    } else {
                        swal(
                            'Error!',
                            result.resMotive,
                            'error'
                        );
                    }
                },
                error: function (res) {
                }
            });
        }
    });

    $(document).on('click', '.remove_group_user', function () {
        if (index == $(this).attr('data-group')) {
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
                        url: '/group/' + _this.attr('data-group') + '/user/delete/' + _this.attr('data-id'),
                        headers: {'X-CSRF-TOKEN': _token},
                        dataType: 'json',
                        success: function (res) {
                            if (res === true) {
                                swal({
                                    type: 'success',
                                    title: 'Assessor has been removed from group.',
                                    showConfirmButton: false,
                                    timer: 2500
                                });
                                table.ajax.reload();
                            } else {
                                swal(
                                    'Error!',
                                    'Assessor has been added to group.',
                                    'error'
                                );
                            }
                        },
                        error: function (res) {
                        }
                    });
                }
            });
        }
    });

    moment.updateLocale('en', {
        week: { dow: 1 } // Monday is the first day of the week
    });
}

var dates = [];

$(document).on('click', '.btn-add-dates', function (e) {

    if (!$('#disabled_from').val() || !$('#disabled_to').val()) {
        return;
    }

    dates.push({
        date_from: moment($('#disabled_from').val()).format("YYYY-MM-DD HH:mm:ss"),
        date_to: moment($('#disabled_to').val()).format("YYYY-MM-DD HH:mm:ss")
    });

    var flags = [], output = [], l = dates.length, i;
    for( i=0; i<l; i++) {
        if( flags[dates[i].date_from]) continue;
        flags[dates[i].date_from] = true;
        output.push(dates[i]);
    }

    dates = output;

    redrawDatesAssessor(dates);

    $('#disabled_from').val('');
    $('#disabled_to').val('');

});


$(document).on('click', '.btn-remove-dates', function (e) {

    var date_from = $(this).attr('data-date');
    var tmp_dates = [];
    $.each(dates, function(){
        if(this.date_from != date_from){
            tmp_dates.push(this);
        }
    });
    dates = tmp_dates;
    redrawDatesAssessor(dates);

});




function redrawDatesAssessor(dates){
    var html = '';
    $.each(dates, function(){
        html += '<p class="inact-list">'+moment(this.date_from).format("D MMMM YYYY, HH:mm")+' ' +
            '- '+moment(this.date_to).format("D MMMM YYYY, HH:mm")+'<span style="margin-left: 15px;">' +
            '<button class="btn btn-danger btn-remove-dates" data-date="'+this.date_from+'">Delete</button> </span></p>';
    });

    $('.inact-list-ranges-container').html(html);
}

$(document).on('click', '.switch_user_deleted_at', function (e) {

    e.stopPropagation();
    e.preventDefault();

    if($(this).hasClass('disabled')){
        return;
    }

    var _this = $(this),
        userId = _this.attr('data-id'),
        status = _this.attr('data-status'),
        endpoint,
        data = {},
        successMsg,
        html = '',
        title = '';

    var inactivities;

    $.get('/assessor/'+userId+'/inactivity', function(res){

        dates = [];

        if (res.resType == 'success') {

            inactivities = res.data;

            $.each(inactivities, function(){
                dates.push({
                    date_from: this.date_from,
                    date_to: this.date_to
                });
                html += '<p class="inact-list">'+moment(this.date_from).format("D MMMM YYYY, HH:mm")+' ' +
                    '- '+moment(this.date_to).format("D MMMM YYYY, HH:mm")+'<span style="margin-left: 15px;">' +
                    '<button class="btn btn-danger btn-remove-dates" data-date="'+this.date_from+'">Delete</button> </span></p>';
            });

            html = '<div class="inact-list-ranges-container">'+html+'</div>';

            html += $('#datetimepick-template').html();

            swal({
                title: title,
                // type: 'warning',
                html: html,
                onOpen: function () {

                    var datepicker_from = $('#disabled_from'),
                        datepicker_to = $('#disabled_to');

                    datepicker_from.datetimepicker({});
                    datepicker_to.datetimepicker({});

                    datepicker_from.on('dp.change', function (e) {
                        datepicker_to.data("DateTimePicker").minDate(e.date);
                    });

                    datepicker_from.click(function () {
                        $(this).focus();
                    });
                },
                customClass: 'swal2-overflow assessor_inctivity',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Save',
                focusConfirm: false
            }).then(function (result) {

                if (result.value) {

                    $.ajax({
                        type: 'POST',
                        url: '/assessor/' + userId + '/update-inactivity',
                        headers: {'X-CSRF-TOKEN': _token},
                        processData: true,
                        data: {
                            dates: dates
                        },
                        success: function (res) {
                            console.log(res);
                            _this.closest('table').DataTable().ajax.reload();
                        },
                        error: function (res) {
                        }
                    });

                } else {
                    swal(
                        '',
                        'Assessor has not been changed!',
                        'error'
                    );
                }
            });
        } else {
            swal(
                'Error!',
                res.errMsg,
                'error'
            );
        }
    });


    return;

    if (status == 1) {
        endpoint = 'addUserTemporaryDisabled';
        successMsg = 'deactivated';
        html = $('#datetimepick-template').html();
    } else {
        endpoint = 'removeUserTemporaryDisabled';
        successMsg = 'activated';
        title = 'Are you sure you want to activate?';
    }


    swal({
        title: title,
        type: 'warning',
        html: html,
        customClass: 'swal2-overflow',
        onOpen: function () {

            if (status == 0) {
                return;
            }

            var datepicker_from = $('#disabled_from'),
                datepicker_to = $('#disabled_to');

            datepicker_from.datetimepicker({});
            datepicker_to.datetimepicker({});

            datepicker_from.on('dp.change', function (e) {
                datepicker_to.data("DateTimePicker").minDate(e.date);
            });

            datepicker_from.click(function () {
                $(this).focus();
            });
        },
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
        focusConfirm: false
    }).then(function (result) {

        data = {
            disabled_from: $('#disabled_from').val(),
            disabled_to: $('#disabled_to').val()
        };

        if (($('#disabled_from').val() !== '') && ($('#disabled_to').val() !== '')) {
            if (result.value) {
                $.ajax({
                    type: 'POST',
                    data: data,
                    url: '/admin/' + endpoint + '/' + userId,
                    headers: {'X-CSRF-TOKEN': _token},
                    dataType: 'json',
                    success: function (res) {
                        if (res.resType === 'success') {
                            swal(
                                'Success!',
                                'User has been ' + successMsg + '!',
                                'success'
                            );
                            table.ajax.reload();

                        } else {

                            swal(
                                'Error!',
                                'User has not been changed!',
                                'error'
                            );
                        }
                    },
                    error: function (res) {
                    }
                });
            }else {
                if (status === "1") {
                    if ($('.swal2-confirm').click() === $('.swal2-confirm')) {
                        $('.assessor_' + userId + ' input[type="checkbox"]').prop('checked', true);
                    } else {
                        $('.assessor_' + userId + ' input[type="checkbox"]').prop('checked', false);
                    }
                } else if (status === "0") {
                    if ($('.swal2-confirm').click() === $('.swal2-confirm')) {
                        $('.assessor_' + userId + ' input[type="checkbox"]').prop('checked', false);
                    } else {
                        $('.assessor_' + userId + ' input[type="checkbox"]').prop('checked', true);
                    }
                }
            }
        } else {
            swal(
                'Error!',
                'User has not been changed!',
                'error'
            );
            $('.assessor_' + userId + ' input[type="checkbox"]').prop('checked', false);
        }
    });


});