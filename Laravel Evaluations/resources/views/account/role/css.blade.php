@extends('layouts.app')

@section('content')

    <div class="header-content">
        <div class="tag-name">CSS/Recruiters</div>
        <div class="language-audit">
            <select class="form-control column_filter" id="active_client">
                <option value="1">Active</option>
                <option value="-1">All</option>
                <option value="0">Inactive</option>
            </select>
        </div>
        @canAtLeast(['user.create'])
        <button class="add-client css_recruiter_add_new">
            <div class="ion-plus">
                Add new
            </div>
        </button>
        @endCanAtLeast
    </div>

    <div class="grid table-css">
        <!-- /.box-header -->
        <div class="box">
            <div class="box-body">
                <table id="css_datatable" class="table responsive nowrap ui celled"
                       data-model="4">
                    <thead>
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
@endsection

@section('footer')
    <script>
        $(document).ready(function () {
            var element = $('#css_datatable');
            var route = element.attr('data-model');
            var title = 'User';

            function cssRecruiter(element) {

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
                    ajax: '/admin/getCssRecruitersDatatable/' + filter,
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
                            orderable: true,
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
                            data: 'user.deleted_at',
                            render: function (data, type, row) {
                                var html = '';
                                if (row.user.deleted_at != null) {
                                    html = '<a href="#" class="action-button confirm_css_recruiter activate_css_recruiter" data-id="' + row.user_id + '" title="Activate"><span class="fa fa-check"></span></a>';
                                } else {
                                    html = '<a href="#" class="action-button demo-close remove_css_recruiter" data-id="' + row.user_id + '" title="Remove"><i class="fa fa-times"></i></a>';
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

            var table = cssRecruiter(element, route);

            $('.column_filter').on('change', function () {
                table = cssRecruiter(element, route);
                createCssRecruiter(table);
            });

            createCssRecruiter(table);

            $('.submit_css_recruiter').css({

                'outline': 'none',
                'width': '105px',
                'border': 'none',
                'background': 'unset',
                'position': 'absolute',
                'height': '30px',
                'z-index': '9999'

            });
        });

    </script>
@endsection