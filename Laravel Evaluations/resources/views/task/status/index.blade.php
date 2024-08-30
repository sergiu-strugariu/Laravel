@extends('layouts.app')

@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Task Statuses List</h3>
            <a href="#" class="editor_create">
                <button class="btn btn-success"><span class="fa fa-plus-circle"></span> Add Task Status</button>
            </a>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table id="task-statuses-table" class="table table-bordered table-striped" data-model="task/status">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Color</th>
                    <th>Actions</th>
                </tr>
                </thead>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
@endsection


@section('footer')
    <script>
        $(document).ready(function () {
            var element = $('#task-statuses-table');
            var route = element.attr('data-model');
            var title = 'Task Status';
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
                ajax: '/' + route + 'es-data',
                createdRow: function createdRow(row, data) {
                    $(row).attr('data-id', data.id);
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
                        data: 'name',
                        name: 'name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'color',
                        name: 'color',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: null,
                        className: "actions",
                        defaultContent: '<a href="#" class="editor_edit"><span class="fa fa-pencil"></span></a><a href="#" class="editor_remove"><span class="fa fa-times"></span></a>',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
            crudDataTable(table, element, route, title);
        });
    </script>
@endsection
