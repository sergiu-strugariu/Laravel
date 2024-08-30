@extends('layouts.app')
@section('sectionClass') usersCrud @endsection

@section('content')
    <div class="box box-success">
        <div class="box">
            <div class="box-header css-header">
                <div class="row">
                    <div class="col-xs-6"><h3 class="box-page_heading">Users List</h3></div>
                    <div class="col-xs-6">
                        <button class="add-client" id="show_filters"><i class="fa fa-reorder"></i>Filters</button>
                    </div>
                </div>
            </div>

            <div class="panel" id="filters">
                <div class="panel-heading">
                    Filter Tests
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-10 no-padding">
                            <div class="col-md-2 form-group">
                                {!! Form::input('text', 'first_name', null, ['class' => 'form-control sel-status column_filter', 'id' => 'first_name_filter', 'placeholder' => 'First name']) !!}
                            </div>
                            <div class="col-md-2 form-group">
                                {!! Form::input('text', 'last_name', null, ['class' => 'form-control sel-status column_filter', 'id' => 'last_name_filter', 'placeholder' => 'Last name']) !!}
                            </div>
                            <div class="col-md-2 form-group">
                                {!! Form::input('text', 'email', null, ['class' => 'form-control sel-status column_filter', 'id' => 'email_filter', 'placeholder' => 'Email']) !!}
                            </div>
                            <div class="col-md-2 form-group">
                                {!! Form::select('role', $roles, null, ['class' => 'form-control sel-status column_filter', 'id' => 'role_filter', 'placeholder' => 'All Roles']) !!}
                            </div>
                            <div class="col-md-2 form-group">
                                {!! Form::select('status', [ 1 => 'Active', 0 => 'Inactive' ], null, ['class' => 'form-control sel-status column_filter', 'id' => 'status_filter', 'placeholder' => 'Status']) !!}
                            </div>
                            <div class="col-md-2 form-group">
                                <button class="btn btn-primary text-uppercase col-xs-12 pull-right" id="reset_filters">
                                    Show all
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2 form-group">
                            <button class="btn btn-success text-uppercase col-xs-12 pull-right" id="export_excel">
                                Export
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <!-- /.box-header -->
        <div class="box-body">

            <table id="users" class="table responsive xnowrap ui celled" data-model="user">
                <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Projects</th>
                    <th>Client</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->

    <aside id="edit-user" class="control-sidebar control-sidebar-edit add-new-project-modal">
        <div class="loading">
            <div class="loading-wheel fa-spin"></div>
        </div>
        <div class="panel">
            <div class="panel-heading">
                <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
                <h1>
                    Edit User
                </h1>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" method="POST" id="edit_user_form">
                    {{ csrf_field() }}

                    <div id="form-body"></div>
                </form>
            </div>
        </div>
    </aside>

@endsection

@section('footer')
    <script>
        $(document).ready(function () {
            $('#show_filters').on('click', function (e) {
                $('#filters').slideToggle('slow');
            });

            document.getElementById("reset_filters").onclick = function () {
                $('.column_filter').val('').trigger('change')
            };

            function generateUsersTable() {

                var filters = {};
                $('.column_filter').each(function () {
                    filters[this.name] = this.value;
                });

                var dt = $('#users').DataTable({
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
                    responsive: true,
                    ajax: {
                        url: '/admin/users/datatable',
                        type: "GET",
                        data: {
                            filters: filters
                        }
                    }, createdRow: function createdRow(row, data) {
                        $(row).attr('data-id', data.id);
                    },
                    "fnDrawCallback": function () {
                        var element = $('#users');
                        if (element.parent().find('span > .paginate_button').length > 1) {
                            element.parent().find('.dataTables_paginate')[0].style.display = "block";
                            element.parent().find('.dataTables_info')[0].style.display = "block";
                        } else if (element.parent().find('span > .paginate_button').length >= 0) {
                            element.parent().find('.dataTables_paginate')[0].style.display = "none";
                            element.parent().find('.dataTables_info')[0].style.display = "none";
                        }
                    },
                    columns: [
                        {
                            data: 'first_name',
                            name: 'first_name',
                            orderable: true,
                            searchable: true
                        },
                        {
                            data: 'last_name',
                            name: 'last_name',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'email',
                            name: 'email',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: null,
                            name: 'projects',
                            orderable: false,
                            searchable: false,
                            render: function(user){
                                var projects = [];
                                $.each(user.projects_participating, function(index, val){
                                    if(val.project)
                                        projects.push(val.project.name);
                                });
                                return projects.join(', ');
                            }
                        },
                        {
                            data: 'clients',
                            name: 'client',
                            render: function (data) {
                                return data != null ? data.name : '-';
                            },
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'roles',
                            render: function (data) {
                                if (data.length){
                                    var slugs = [];
                                    for (var i = 0; i < data.length; i++) {
                                        slugs.push(data[i].slug);
                                    }
                                    return slugs.join(', ');
                                } else {
                                    return '-';
                                }
                            },
                            name: 'role',
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
                            data: 'deleted_at',
                            className: "actions",
                            render: function (data, type, row) {

                                var html = '<a href="#" class="action-button edit_user" data-id="' + row.id + '" title="Edit"><span class="fa fa-pencil"></span></a>';

                                if (data) {
                                    html += '<a href="#" class="action-button confirm_css_recruiter activate-user" data-id="' + row.id + '" title="Activate"><span class="fa fa-check"></span></a>';
                                } else {
                                    html += '<a href="#" class="action-button demo-close deactivate-user" data-id="' + row.id + '" title="Remove"><i class="fa fa-times"></i></a>';
                                }


                                return html;
                            },
                            defaultContent: '',
                            orderable: false,
                            searchable: false
                        }
                    ]     ,
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
                return dt;

            }

            generateUsersTable();

            var submitTimer = null;

            $(document).on('keyup', 'input.column_filter', function () {

                clearTimeout(submitTimer);
                submitTimer = setTimeout(function () {
                    generateUsersTable();
                }, 500);

            });

            $('select.column_filter').on('change', function () {
                generateUsersTable();
            });

            $(document).on('click', '.activate-user', function () {
                AjaxCall({
                    'url': '/admin/activateUser/' + $(this).attr('data-id'),
                    'method': 'GET',
                    'validate': false,
                    'successMsg': 'User has been activated!',
                    'errorMsg': 'User was not activated'
                }).makeCall($(this), generateUsersTable);
            });

            $(document).on('click', '.deactivate-user', function () {
                AjaxCall({
                    'url': '/admin/removeUser/' + $(this).attr('data-id'),
                    'method': 'GET',
                    'validate': false,
                    'successMsg': 'User has been deactivated!',
                    'errorMsg': 'User was not deactivated'
                }).makeCall($(this), generateUsersTable);
            });

            $(document).on('click', '#export_excel', function (e) {
                e.preventDefault();
                var filters = {};
                $('.column_filter').each(function () {
                    filters[this.name] = this.value;
                });
                $.ajax({
                    url: '/admin/exportUsers',
                    type: "GET",
                    data: {
                        filters: filters
                    },
                    success: function() {
                        window.location.href = this.url;
                    },
                    error: function() {

                    }
                });
            });

            $(document).on('click', '.edit_user', function (e) {
                e.preventDefault();
                $('#edit-user .loading').addClass('active');

                var _this = $(this);

                $('#edit-user').addClass('control-sidebar-open');

                var userId = $(this).attr('data-id');
                $('body').addClass('open');

                $.ajax({
                    method: "GET",
                    url: '/admin/getUserDetails/' + userId,
                    success: function(res){
                        $('#form-body').html(res);
                        $('.select2').select2();
                    },
                    complete: function(){
                        $('#edit-user .loading').removeClass('active');
                    }
                });

            });

            $(document).on('click', '.edit-user-button', function (e) {
                e.preventDefault();
                $('#edit-user .loading').addClass('active');
                $.ajax({
                    method: 'POST',
                    url: '/admin/updateUserDetails/' + $(this).attr('data-id'),
                    data: $('#edit_user_form').serialize(),
                    success: function(res){
                        if (res.resType == 'success') {
                            $(".control-sidebar").removeClass('control-sidebar-open');
                            $('body').removeClass('open');
                            generateUsersTable();
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
                    complete: function(){
                        $('#edit-user .loading').removeClass('active');
                    }
                });

            });
        });
    </script>
@endsection