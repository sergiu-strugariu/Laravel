@extends('layouts.app')

@section('content')
    <div class="box box-success">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">User list</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table id="master_datatable" class="table table-bordered table-striped" data-model="1">
                    <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
@endsection

@section('footer')
    <script>
        $(document).ready(function () {
            var element = $('#master_datatable');
            var route = element.attr('data-model');
            var title = 'User';
            var table = element.DataTable({
                paging: true,
                lengthChange: false,
                pageLength: 30,
                searching: false,
                ordering: true,
                info: true,
                autoWidth: true,
                processing: true,
                serverSide: true,
                ajax: '/admin/roles/' + route + '/datatable',
                createdRow: function createdRow(row, data) {
                    $(row).attr('data-id', data.user_id);
                },
                "fnDrawCallback": function () {
                    if (element.parent().find('span > .paginate_button').length > 1) {
                        element.parent().find('.dataTables_paginate')[0].style.display = "block";
                        element.parent().find('.dataTables_info')[0].style.display = "block";
                    } else if(element.parent().find('span > .paginate_button').length >= 0) {
                        element.parent().find('.dataTables_paginate')[0].style.display = "none";
                        element.parent().find('.dataTables_info')[0].style.display = "none";
                    }
                },
                columns: [
                    {
                        data: 'user.first_name',
                        name: 'first_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user.last_name',
                        name: 'last_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user.email',
                        name: 'email',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'role.name',
                        name: 'role.name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: null,
                        className: "actions",
                        defaultContent: '<a href="#" class="user_editor_edit"><span class="fa fa-pencil"></span></a><a href="#" class="user_editor_remove"><span class="fa fa-times"></span></a>',
                        orderable: false,
                        searchable: false
                    }
                ]

            });
            crudDataTable(table, element, route, title);
        });
    </script>
@endsection