@extends('layouts.app')

@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Groups List</h3>
            <a href="#" class="editor_create">
                <button class="btn btn-success"><span class="fa fa-plus-circle"></span> Add Group</button>
            </a>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table id="groups-table" class="table table-bordered table-striped" data-model="group">
                <thead>
                <tr>
                    <th>Language</th>
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
            var element = $('#groups-table');
            var route = element.attr('data-model');
            var title = 'Group';
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
                        data: 'language.name',
                        name: 'language',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: null,
                        className: "actions",
                        orderable: false,
                        searchable: false
                    }
                ],
                "fnRowCallback": function fnRowCallback(nRow, aData) {
                    $('td:eq(-1)', nRow).html(
                        '<a href="/group/' + aData.id + '/users" class="editor_view_users"><span class="fa fa-users"></span></a><a href="#" class="editor_edit"><span class="fa fa-pencil"></span></a><a href="#" class="editor_remove"><span class="fa fa-times"></span></a>'
                    );
                    return nRow;
                }
            });
            crudDataTable(table, element, route, title);

            // Display users in group
//            element.on('click', 'a.editor_view_users', function (e) {
//                e.preventDefault();
//                var id = $(this).closest('tr').attr('data-id');
//
//                $.ajax({
//                    url: '/' + route + '/' + id + '/users',
//                    type: 'GET',
//                    success: function (response) {
//                        swal({
//                            title: response.title,
//                            type: 'info',
//                            html: response.html
//                        })
//                    },
//                    error: function (response) {
//                        swal(
//                            'Error!',
//                            'Your ' + title + ' has not been updated.',
//                            'error'
//                        )
//                    }
//                });
//            });
        });
    </script>
@endsection
