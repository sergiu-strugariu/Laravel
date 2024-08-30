@extends('layouts.app')

@section('content')
    <div class="box box-success">
        <div class="box">
            <div class="box-header css-header">
                <div class="row">
                    <div class="col-xs-6"><h3 class="box-page_heading">Tests List</h3></div>

                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table id="projectTypes" class="table responsive nowrap ui celled"
                       data-model="tests">
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

    <aside id="edit_test" class="control-sidebar control-sidebar-edit add-new-project-modal">

        <div class="loading">
            <div class="loading-wheel fa-spin"></div>
        </div>
        <div class="panel">
            <div class="panel-heading">
                <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
                <h1>
                    Edit Test
                </h1>
            </div>
            <div class="panel-body">
                {!! Form::open(['id' => 'datatable-form']) !!}
                <div class="form-group">
                    {!! Form::label('name', 'Test Name') !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'id' => 'test-name']) !!}
                </div>
                <div class="form-group">
                    {!! Form::submit('Save', ['class' => 'btn btn-primary save-test']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>

    </aside>


@endsection

@section('footer')
    <script>
        $(document).ready(function () {
            var table_element = $('#projectTypes');
            var datatable_model = table_element.attr('data-model');
            var title = 'Tests';
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
                ajax: '/admin/tests/datatables',
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
                        className: 'test-name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: null,
                        className: "text-center",
                        defaultContent: '<a href="#" class="edit_test"><span class="fa fa-pencil"></span></a>',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
           // crudDataTable(table, table_element, datatable_model, title);

            $(document).on('click', '.edit_test', function(e){
                e.preventDefault();
                var id = $(this).closest('tr').attr('data-id');
                var name = $(this).closest('tr').find('.test-name').text();
                var cost = $(this).closest('tr').find('.test-cost').text();
                $('aside#edit_test').addClass('control-sidebar-open');
                $('#test-name').val(name);
                $('#test-cost').val(cost);
                $('.save-test').attr('data-id', id);
            });

            $('.save-test').on('click', function(e){
                e.preventDefault();
                AjaxCall({
                    'url': '/admin/tests/update/' + $(this).attr('data-id'),
                    'method': 'POST',
                    'validate': false,
                    'successMsg': 'Test has been updated!',
                }).makeCall($(this), function(){
                    table.ajax.reload();
                    $('aside#edit_test').removeClass('control-sidebar-open');
                });
            });
        });
    </script>
@endsection