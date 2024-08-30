@extends('layouts.app')

@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Paper Types List</h3>
            <a href="#" class="editor_create">
                <button class="btn btn-success"><span class="fa fa-plus-circle"></span> Add Paper Type</button>
            </a>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table id="paper-types-table" class="table table-bordered table-striped" data-model="paper/type">
                <thead>
                <tr>
                    <th>Name</th>
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
            var element = $('#paper-types-table');
            var route = element.attr('data-model');
            var title = 'Paper Type';
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
                ajax: '/' + route + 's-data',
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
