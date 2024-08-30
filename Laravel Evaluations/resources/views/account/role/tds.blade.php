@extends('layouts.app')

@section('content')

    <div class="header-content">
        <div class="tag-name">Tds List</div>
        <div class="language-audit">
            <select class="form-control column_filter" id="active_tds">
                <option value="1">Active</option>
                <option value="-1">All</option>
                <option value="0">Inactive</option>
            </select>
        </div>
        @canAtLeast(['user.create'])
        <button class="add-client tds_add_new">
            <div class="ion-plus">
                Add new
            </div>
        </button>
        @endCanAtLeast
    </div>

    <div class="grid table-tds">
        <div class="box-header css-header">
            <!-- /.box-header -->
            <div class="box">
                <div class="box-body">
                    <table id="tds_datatable" class="table responsive nowrap ui celled" data-model="6">
                        <thead style="border-bottom: 1px solid red">
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
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
        </div>
    </div>
@endsection

@section('footer')
    <script>
        $(document).ready(function () {
            var element = $('#tds_datatable');
            var route = element.attr('data-model');
            var title = 'User';

            function createTds(element) {

                var filter = $('.column_filter').val();

                return element.DataTable({
                    paging: true,
                    destroy: true,
                    searching: false,
                    ordering: true,
                    info: true,
                    autoWidth: true,
                    processing: true,
                    serverSide: true,
                    "iDisplayLength": 30,
                    "lengthMenu": [[30, -1], [30, "All"]],
                    ajax: '/admin/getTdsDatatable/' + filter,
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
                            data: 'user.first_name',
                            name: 'user.first_name',
                            orderable: true,
                            searchable: false
                        },
                        {
                            data: 'user.last_name',
                            name: 'user.last_name',
                            orderable: true,
                            searchable: false
                        },
                        {
                            data: 'user.email',
                            name: 'user.email',
                            orderable: true,
                            searchable: false
                        },
                        {
                            data: 'role.name',
                            name: 'role.name',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'user.deleted_at',
                            render: function (data) {
                                data = data == null ? 'Active' : 'Inactive';
                                return data;
                            },
                            name: 'deleted_at',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: null,
                            render: function (data, type, row) {
                                var html = '';
                                if (row.user.deleted_at) {
                                    html = '<a href="#" class="action-button confirm_css_recruiter activate_tds" data-id="' + row.user_id + '" title="Activate"><span class="fa fa-check"></span></a>';
                                } else {
                                    html = '<a href="#" class="action-button demo-close remove_css_recruiter remove_tds" data-id="' + row.user_id + '" title="Remove"><i class="fa fa-times"></i></a>';
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

            var table = createTds(element, route);

            $('input.column_filter, select.column_filter').on('change', function () {
                table = createTds(element, route);
                tds(table, route);
            });

            tds(table, route);
        });
    </script>
@endsection