@extends('layouts.app')

@section('content')
    <div class="box box-success">
        <div class="box">
            <div class="box-header css-header">
                <div class="row">
                    <div class="col-xs-6"><h3 class="box-page_heading">Project Types List</h3></div>
                    <div class="col-xs-6"><a href="#" class="editor_create">
                            <button class="btn btn-success"><span class="fa fa-plus-circle"></span> Add Project Type
                            </button>
                        </a></div>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table id="projectTypes" class="table responsive nowrap ui celled"
                       data-model="projectTypes">
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
    </div>
@endsection

@section('footer')
    <script>
        $(document).ready(function () {
            var table_element = $('#projectTypes');
            var datatable_model = table_element.attr('data-model');
            var title = 'Project Type';
            var table = table_element.DataTable({
                paging: true,
                lengthChange: false,
                pageLength: 30,
                searching: false,
                ordering: true,
                info: true,
                autoWidth: true,
                processing: true,
                serverSide: true,
                ajax: '/admin/projectTypes/datatables',
                createdRow: function createdRow(row, data) {
                    $(row).attr('data-id', data.id);
                },
                "fnDrawCallback": function () {
                    if (table_element.parent().find('span > .paginate_button').length > 1) {
                        table_element.parent().find('.dataTables_paginate')[0].style.display = "block";
                        table_element.parent().find('.dataTables_info')[0].style.display = "block";
                    } else if (table_element.parent().find('span > .paginate_button').length >= 0) {
                        table_element.parent().find('.dataTables_paginate')[0].style.display = "none";
                        table_element.parent().find('.dataTables_info')[0].style.display = "none";
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
                        className: "text-center",
                        defaultContent: '<a href="#" class="editor_edit"><span class="fa fa-pencil"></span></a><a href="#" class="editor_remove"><span class="fa fa-times"></span></a>',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
            crudDataTable(table, table_element, datatable_model, title);
        });
    </script>
@endsection