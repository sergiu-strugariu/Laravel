@extends('layouts.app')

@section('content')
    <div class="header-content">
        <div class="tag-name">Assessors</div>
        {{--<div class="candidate-assessors"><select id="candidate_assessors" name="candidate_assessors"--}}
        {{--class="form-control">--}}
        {{--<option value="0">Candidate assessors</option>--}}
        {{--<option value="1">Candidate 1</option>--}}
        {{--<option value="2">Candidate 2</option>--}}
        {{--<option value="3">Candidate 3</option>--}}
        {{--</select></div>--}}
    </div>
    <div class="grid table-assessors">
        @foreach($groups as $group)
            <div class="box">
                <div class="box client_box">
                    <div class="box-header">
                        <h3 class="box-title">{{$group->language->name}}</h3>
                        <button type="button" class="btn btn-danger group_add_new" data-id="{{$group->id}}"
                                data-group="{{$group->id}}">
                            <div class="ion-plus">
                                Add new
                            </div>
                        </button>
                    </div>
                    <!-- /.box-header -->


                    <div class="box-body">
                        {{--<form id="add_user_form_{{$group->id}}">--}}
                        <table id="group_datatable_{{$group->id}}" class="table group_table custom-table"
                               data-model="5">
                            <thead>
                            <tr>
                                <th>Assessor</th>
                                <th>Native</th>
                                <th>Status</th>
                                <th></th>
                            </tr>

                            </thead>
                        </table>
                        {{--</form>--}}
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        @endforeach
    </div>

    <script id="datetimepick-template" type="text/x-custom-template">
        <p style="margin-bottom: 10px; margin-top: 30px;">Add date range</p>
        <div class="col-sm-5">
            <div class="form-group has-feedback">
                <div class='input-group date'>
                    <label for="disabled_from">Disable From:</label>
                    <input type="text" id="disabled_from" required="required" name="disabled_from"
                           class="form-control sel-status datepicker_disable_user">
                    <span class="form-control-feedback"><i class="fa fa-calendar-o" aria-hidden="false"></i></span>
                </div>
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group has-feedback">
                <div class='input-group date'>
                    <label for="disabled_to">Disable To:</label>
                    <input type="text" id="disabled_to" required="required" name="disabled_to"
                           class="form-control sel-status datepicker_disable_user">
                    <span class="form-control-feedback"><i class="fa fa-calendar-o" aria-hidden="false"></i></span>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <button class="btn btn-primary btn-block btn-add-dates">Add</button>
        </div>
    </script>

@endsection

@section('footer')

    <script>

        $(document).ready(function () {
            $('select#candidate_assessors').select2({
                minimumResultsForSearch: -1,
                allowClear: false
            });

            var assessorGroups = {!! json_encode($assessorGroups) !!};

            var groupsArray = {!! $groups !!};
            $.each(groupsArray, function (index, group) {

                var element = $('#group_datatable_' + group.id);
                var route = element.attr('data-model');
                var title = 'Group';
                var table = element.DataTable({
                    paging: false,
                    lengthChange: false,
                    responsive: true,
                    pageLength: 30,
                    searching: false,
                    ordering: true,
                    info: true,
                    autoWidth: true,
                    processing: true,
                    serverSide: false,
                    data: assessorGroups[group.id],
                    createdRow: function createdRow(row, data) {
                        $(row).attr('data-id', data.user_id).attr('data-group', data.group_id);
                    },
                    "fnDrawCallback": function () {
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
                                if (data.user.first_name != null) {
                                    return data.user.first_name + ' ' + data.user.last_name;
                                } else {
                                    return data.user.email;
                                }
                            },
                            orderable: true
                        },
                        {
                            data: 'native',
                            name: 'native',
                            orderable: true
                        },
                        {
                            data: 'user.is_active_now',
                            name: 'status',
                            orderable: false,
                            width: '30%'
                        },
                        {
                            data: null,
                            className: "actions",
                            orderable: false,
                            searchable: false
                        }
                    ], "fnRowCallback": function fnRowCallback(nRow, aData) {

                        var user = aData.user;
                        $('td:eq(1)', nRow).html(
                            aData.native ? 'Yes' : 'No'
                        );

                        var inactiveInfo;
                        var disabled_from = new Date(user.disabled_from),
                            disabled_to = new Date(user.disabled_to);


                        if(user.deleted_at !== null){
                            inactiveInfo = '<div class="red-point"></div> Inactive';
                        } else {
                            if (!user.is_active_now) { // user is inactive now
                                inactiveInfo = '<div class="red-point"></div>Inactive <div class=until-inactive>until ' + user.inactivities[0].date_to + '</div>';
                            } else { // user is active now
                                if (user.inactivities.length) {
                                    inactiveInfo = '<div class="green-point"></div>Active (Becomes inactive from ' + user.inactivities[0].date_from + ' to ' + user.inactivities[0].date_to + ')';
                                } else {
                                    inactiveInfo = '<div class="green-point"></div>Active';
                                }
                            }
                        }

                        $('td:eq(2)', nRow).html(inactiveInfo);

                        var buttons = '';
                        // status button
                        if(user.deleted_at !== null){
                            buttons += '<label class="action-button switch_user_deleted_at disabled assessor_' + aData.user_id + '" data-group="' + aData.group_id + '" data-status="1" data-id="' + aData.user_id + '" title="Enable user from Cruds"> <input type="checkbox" checked disabled> <span class="slider round"></span> </label>';
                            $('.assessor_' + aData.user_id).removeClass('status_inactive').addClass('status_active').attr('data-status', 1);
                        } else {
                            if (!user.is_active_now) { // is inactive
                                buttons += '<label class="action-button switch_user_deleted_at status_inactive assessor_' + aData.user_id + '" data-group="' + aData.group_id + '" data-status="0" data-id="' + aData.user_id + '" title="Set assessor to active"> <input checked type="checkbox"> <span class="slider round"></span> </label>';
                                $('.assessor_' + aData.user_id).removeClass('status_active').addClass('status_inactive').attr('data-status', 0);
                            } else { // is active
                                buttons += '<label class="action-button switch_user_deleted_at status_active assessor_' + aData.user_id + '" data-group="' + aData.group_id + '" data-status="1" data-id="' + aData.user_id + '" title="Set assessor to inactive"> <input type="checkbox"> <span class="slider round"></span> </label>';
                                $('.assessor_' + aData.user_id).removeClass('status_inactive').addClass('status_active').attr('data-status', 1);
                            }
                        }



                        // remove button
                        buttons += '<a href="#" class="action-button remove_group_user" data-id="' + aData.user_id + '" data-group="' + aData.group_id + '" title="Remove from group"><span class="fa fa-times"></span></a>';

                        $('td:eq(-1)', nRow).html(buttons);

                        return nRow;
                    }

                });
                assessor(table, group.id, element);
                table.ajax.url('/admin/roles/' + group.id + '/datatable-groups');
            });

        });
    </script>
@endsection