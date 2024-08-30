@extends('layouts.app')

@section('content')
    <div class="grid table-clients">
        <div class="row">
            <div class="col-xs-6"><h3 class="page_heading">Clients</h3></div>
            <div class="col-xs-6 text-right">
            </div>
        </div>
    </div>

    @foreach($users as $userKey => $userValue)
        <div class="grid table-clients">
            <div class="box">
                <div class="box client_box" id="client-{{$userKey}}">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-xs-6">
                                <h3 class="box-title">
                                    {{$userValue}}
                                </h3>
                            </div>
                            <div class="col-xs-6">
                            <span class="pull-right-container"><i

                                        class="fa fa-angle-down pull-right client_button_class expand-clients expand-clients-down{{$userKey}}"
                                        data-toggle="collapse"
                                        data-target="#toggle-{{$userKey}}" data-id="{{$userKey}}"></i></span>
                                <span>
                            <i class="fa fa-angle-up pull-right client_button_class expand-clients expand-clients-up{{$userKey}} hidden"
                               data-toggle="collapse"
                               data-target="#toggle-{{$userKey}}" data-id="{{$userKey}}"></i></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3">
                                <div class="form-group">
                                    <select class="form-control column_filter hidden" id="client_filter_{{$userKey}}">
                                        <option value="1">Active</option>
                                        <option value="-1">All</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-9">
                                @canAtLeast(['user.create'])
                                <button type="button" data-id="{{$userKey}}"
                                        class="btn btn-danger client_add_new add_client_new{{$userKey}} hidden">+ Add
                                    new
                                </button>
                                @endCanAtLeast
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body collapse" id="toggle-{{$userKey}}">
                        <table id="client_datatable_{{$userKey}}" class="table responsive nowrap ui celled"
                               data-model="5"
                               data-id="{{$userKey}}" style="width: 100%">
                            <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Projects</th>
                                <th>Actions</th>
                            </tr>

                            </thead>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
    @endforeach

    <aside id="edit_client" class="control-sidebar control-sidebar-edit add-new-project-modal">
        <div class="panel">
            <div class="panel-heading">
                <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
                <h1>
                    Edit client
                </h1>
            </div>

            <div class="panel-body">

                <form class="form-horizontal" method="POST" id="edit_client_form">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <div class="col-sm-12">
                            <label for="projects">Projects</label>
                            <select name="projects[]" id="projects_edit"
                                    class="form-control" multiple
                            >
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-6">
                            <input type="submit" class="btn btn-primary edit_client_button" value="Save client"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </aside>
@endsection

@section('footer')
    <script>

        $(document).ready(function () {

            $(document).on('click', '.client_edit', function (e) {
                e.preventDefault();
                $('.edit_client_button').attr('data-model', $(this).attr('data-id'));
                $('.edit_client_button').attr('data-id', $(this).attr('data-model'));
                $('#projects_edit').val("");
                $('#projects_edit option').remove();
                $.ajax({
                    type: "GET",
                    url: "/project/getProjectsParticipatings/" + $(this).attr('data-id') + '/client/' + $(this).attr('data-model'),
                    dataType: 'json',
                    success: function (data) {

                        for (var index in data){
                                if (data[index].participating == true) {
                                    $('#projects_edit').append('<option selected value="' + data[index].id + '">' + data[index].name + '</option>');
                                } else {
                                    $('#projects_edit').append('<option value="' + data[index].id + '">' + data[index].name + '</option>');
                                }
                        }
                    }
                });

                $('#edit_client').addClass('control-sidebar-open');
                $('body').addClass('open');

            });

            $('#projects_edit').select2({
                placeholder: 'Any',
                tags: true,
                allowClear: true
            });


            $('.client_button_class').on('click', function () {
                var index = $(this).attr('data-id');
                var element = $('#client_datatable_' + $(this).attr('data-id'));
                var route = element.attr('data-model');
                var down = $('.expand-clients-down' + index);
                var up = $('.expand-clients-up' + index);
                if (!down.hasClass('hidden')) {
                    down.addClass('hidden');
                    up.removeClass('hidden');
                    $('.add_client_new' + index).removeClass('hidden');
                    $('#client_filter_' + index).removeClass('hidden');
                    $('#client_filter_' + index).select2({
                        minimumResultsForSearch: -1,
                        allowClear: false
                    });
                }
                else {
                    down.removeClass('hidden');
                    up.addClass('hidden');
                    $('.add_client_new' + index).addClass('hidden');
                    $('#client_filter_' + index).addClass('hidden');
                    $('#client_filter_' + index).select2('destroy');
                }


                function clients(element, route) {
                    var index = element.attr('data-id');
                    var filter = $('#client_filter_' + index).val();
                    return element.DataTable({
                        paging: true,
                        destroy: true,
                        searching: false,
                        ordering: true,
                        info: true,
                        autoWidth: true,
                        processing: true,
                        serverSide: true,
                        "iDisplayLength": 10,
                        "lengthMenu": [[10, -1], [10, "All"]],
                        ajax: '/admin/roles/' + route + '/datatable/' + index + '/' + filter,
                        createdRow: function createdRow(row, data) {
                            $(row).attr('data-id', data.user_id);
                        },
                        "fnDrawCallback": function () {
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
                                render: function (data, type, full) {
                                    return data;
                                },
                                name: 'first_name',
                                orderable: true,
                                searchable: false
                            },
                            {
                                data: 'last_name',
                                render: function (data, type, full) {
                                    return data;
                                },
                                name: 'last_name',
                                orderable: true,
                                searchable: false
                            },
                            {
                                data: 'email',
                                render: function (data, type, full) {
                                    return data;
                                },
                                name: 'email',
                                orderable: true,
                                searchable: false
                            },
                            {
                                data: null,
                                render: function (data, type, row) {
                                    var array = [];
                                    for (var i = 0; i < row.projects_participating.length; i++) {
                                        if(row.projects_participating[i].project != null){
                                        array.push(row.projects_participating[i].project.name);}

                                    }

                                    var html = '';
                                    for (var i = 0; i < array.length; i++) {
                                        html += '<span class="project_name_class">' + array[i] + '</span>';
                                    }

                                    return html;
                                },
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'deleted_at',
                                render: function (data, type, row) {
                                    var html = '';
                                    if (data) {
                                        html = '<a href="#" class="client_edit"  data-id="' + row.id + '" data-model="' + index + '" title="Edit"><span class="fa fa-pencil"></span><a href="#" class="action-button confirm_client activate_client" data-id="' + row.id + '"><span class="fa fa-check"></span></a>';
                                    } else {
                                        html = '<a href="#" class="client_edit"  data-id="' + row.id + '"  data-model="' + index + '" title="Edit"><span class="fa fa-pencil"></span><a href="#" class="action-button demo-close remove_client remove_client" data-id="' + row.id + '"><i class="fa fa-times"></i></a>';
                                    }
                                    return html;
                                },
                                className: "actions",
                                orderable: false,
                                searchable: false
                            }
                        ]
                    });



                }

                var table = clients(element, route);

                $('.column_filter').on('change', function () {
                    table = clients(element, route);
                    client(table, index, element);
                });

                client(table, index, element);

            });

        });

    </script>
@endsection