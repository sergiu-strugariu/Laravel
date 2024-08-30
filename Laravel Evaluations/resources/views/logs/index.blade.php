@extends('layouts.app')

@section('content')
    <div class="header-content">
        <div class="tag-name">
            Logs
        </div>
        <button class="add-client" id="show_filters"><i class="fa fa-reorder"></i>Filters</button>
    </div>

    <div class="panel" id="filters">
        <div class="panel-heading">
            Filter Tasks
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-2 form-group">
                    {!! Form::select('type_id', $logTypes, null, ['class' => 'form-control sel-status column_filter select2-tags', 'id' => 'type_id', 'placeholder' => 'All Types']) !!}
                </div>
                <div class="col-md-2 form-group">
                    <button class="btn btn-primary text-uppercase col-xs-12 pull-right" id="reset_filters">
                        Show all
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-body">
            <table id="logs-table" class="table table-bordered table-striped" data-model="log">
                <thead>
                <tr>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Task Id</th>
                    <th>Created at</th>
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

            function generateLogTable() {
                var filter = $('#type_id :selected').text();
                if (filter == "All Types") {
                    filter = "All";
                }
                return $('#logs-table').DataTable({
                    destroy: true,
                    paging: true,
                    lengthChange: false,
                    pageLength: 10,
                    searching: false,
                    ordering: true,
                    order: [[ 3, 'desc' ]] ,
                    info: true,
                    autoWidth: true,
                    processing: true,
                    serverSide: true,
                    ajax: '/admin/logs/getData/' + filter,
                    createdRow: function createdRow(row, data) {
                        $(row).attr('data-id', data.id);
                    },
                    "fnDrawCallback": function () {
                        var element = $('#logs-table');
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
                            data: 'type',
                            name: 'type',
                            orderable: true,
                            searchable: false
                        },
                        {
                            data: 'description',
                            name: 'description',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'task_id',
                            name: 'task_id',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            orderable: true,
                            searchable: false
                        }
                    ]
                });
            }

            $('#type_id').on('change', function () {
                generateLogTable();
            });

            generateLogTable();
        });
    </script>
@endsection
