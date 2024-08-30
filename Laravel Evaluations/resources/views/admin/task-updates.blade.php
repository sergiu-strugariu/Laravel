@extends('layouts.app')

@section('content')
    <div class="box box-success">
        <div class="box">
            <div class="box-header css-header">
                <div class="row">
                    <div class="col-xs-6"><h3 class="box-page_heading">Task Updates</h3></div>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table id="projectTypes" class="table responsive xnowrap ui celled"
                       data-model="admin/task-updates">
                    <thead>
                    <tr>
                        <th>Slug</th>
                        <th>Text</th>
                        <th>List Text</th>
                        <th>Roles</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>


    <aside id="edit-task-updates" class="control-sidebar control-sidebar-edit add-new-project-modal">
        <div class="panel">
            <div class="panel-heading">
                <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
                <h1>
                    Edit
                </h1>
            </div>

            <div class="panel-body" id="etu-content">

            </div>
        </div>
    </aside>

@endsection

@section('footer')
    <script>
        $(document).ready(function () {
            var table_element = $('#projectTypes');
            var datatable_model = table_element.attr('data-model');
            var title = 'Task Updates';
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
                ajax: '/admin/task-updates/datatables',
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
                        data: 'slug',
                        name: 'slug',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'display_name',
                        name: 'display_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: null,
                        name: 'roles',
                        orderable: false,
                        searchable: false,
                        render: function(item){
                            var roles = [];
                            $.each(item.roles, function(index, role){
                                roles.push(role.name);
                            });
                            return roles.join(', ');
                        }
                    },
                    {
                        data: null,
                        className: "text-center",
                        defaultContent: '<a href="#" class="task_updates_edit"><span class="fa fa-pencil"></span></a>',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
            crudDataTable(table, table_element, datatable_model, title);

            $(document).on('click', '.task_updates_edit', function (e) {

                e.preventDefault();
                $('#edit-task-updates .loading').addClass('active');

                var _this = $(this),
                    id = _this.closest('tr').attr('data-id');

                $('.add-new-project-modal').removeClass('control-sidebar-open');
                $('#edit-task-updates').addClass('control-sidebar-open');

                $('body').addClass('open');

                $.ajax({
                    url: '/admin/task-updates/update-form/' + id,
                    type: 'get',
                    success: function (response) {
                        $('#etu-content').html(response);
                        $('#etu-content').find('.select2').select2();
                    }
                });
            });

            $(document).on('click', '.btn-save-task-update', function (e) {
                var action = $(this).closest('form').attr('action');

                AjaxCall({
                    url: action,
                }).makeCall($(this), function(){
                    $('.add-new-project-modal').addClass('control-sidebar-open');
                    $('#edit-task-updates').removeClass('control-sidebar-open');
                    $('body').removeClass('open');
                    table.ajax.reload();
                });
            });

        });

    </script>
@endsection