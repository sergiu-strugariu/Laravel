@extends('layouts.app')

@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Users in Language Group: {{ $group->language()->first()->name }}</h3>
            <a href="#" class="editor_create">
                <button class="btn btn-success"><span class="fa fa-plus-circle"></span> Add User</button>
            </a>
            <a href="{{ URL::previous() }}">
                <button class="btn btn-default"><span class="fa fa-undo"></span> Go Back</button>
            </a>
        </div>

        <!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <div class="col-sm-2 form-group">
                    {!! Form::select('user_id', $assessors, null, ['class' => 'form-control sel-status column_filter select2-single', 'id' => 'assessor_filter', 'placeholder' => 'Any Assessor']) !!}
                </div>
                <div class="col-sm-2 form-group  pull-right">
                    <button class="btn btn-primary text-uppercase col-xs-12" id="reset_filters">Reset Filters</button>
                </div>
            </div>

            <div class="clearfix"></div>

            <table id="group-users-table" class="table table-bordered table-striped"
                   data-route="group/{{ $group->id }}/user">
                <thead>
                <tr>
                    <th>User</th>
                    <th>Native</th>
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
            $('select#assessor_filter').select2({
                placeholder: 'All Assessors',
                allowClear: true,
//                minimumInputLength: 1
            });

            document.getElementById("reset_filters").onclick = function () {
                $('.column_filter').val('').trigger('change')
            };

            var element = $('#group-users-table');
            var route = element.attr('data-route');
            var title = 'User in Language Group';

            function generateDataTable(element, route) {
                var filters = {};
                $('.column_filter').each(function () {
                    filters[this.name] = this.value;
                });

                return element.DataTable({
                    destroy: true,
                    paging: true,
                    lengthChange: false,
                    pageLength: 30,
                    searching: false,
                    ordering: true,
                    info: true,
                    autoWidth: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: '/' + route + 's-data',
                        type: "POST",
                        data: {
                            filters: filters
                        }
                    },
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
                            data: 'user.email',
                            name: 'user_id',
                            orderable: true
                        },
                        {
                            data: 'native',
                            name: 'native',
                            orderable: true
                        },
                        {
                            data: null,
                            className: "actions",
                            defaultContent: '<a href="#" class="editor_edit"><span class="fa fa-pencil"></span></a><a href="#" class="editor_remove"><span class="fa fa-times"></span></a>',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    "fnRowCallback": function fnRowCallback(nRow, aData) {
                        $('td:eq(0)', nRow).html(
                            '<a href="#">' + aData.user.first_name + ' ' + aData.user.last_name + '</a>'
                        );
                        $('td:eq(1)', nRow).html(
                            aData.native ? 'Yes' : 'No'
                        );
                        return nRow;
                    }
                });
            }

            var table = generateDataTable(element, route);

            $('input.column_filter, select.column_filter').on('change', function () {
                generateDataTable(element, route);
            });

            crudDataTable(table, element, route, title);
        });
    </script>
@endsection
