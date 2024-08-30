@extends('layouts.app')

@section('content')
    <div class="header-content">
        <div class="tag-name">
            Tests Results List
        </div>

        <button class="add-client" id="show_filters"><i class="fa fa-reorder"></i>Filters</button>
    </div>

    <div class="clearfix"></div>
    <div class="grid table-test-results">
        <div class="panel" id="filters">
            <div class="panel-heading">
                Filter Tasks
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-2 form-group">
                        {!! Form::label('email', 'Email') !!}
                        {!! Form::input('text', 'email', null, ['class' => 'form-control sel-status result_column_filter', 'id' => 'email_filter', 'placeholder' => 'Email']) !!}
                    </div>
                    <div class="col-md-2 form-group">
                        {!! Form::label('language', 'Language') !!}
                        {!! Form::select('language', $languages, null, ['class' => 'form-control sel-status result_column_filter', 'id' => 'language_filter', 'placeholder' => 'All Languages']) !!}
                    </div>
                    <div class="col-md-2 form-group">
                        {!! Form::label('started_at', 'Date From') !!}
                        <div class="input-group date with-icon">
                            {!! Form::input('text', 'started_at', null, ['class' => 'form-control sel-status result_column_filter', 'id' => 'started_at_filter', 'placeholder' => 'Date From', ]) !!}
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </div>
                    <div class="col-md-2 form-group">
                        {!! Form::label('ended_at', 'Date To') !!}
                        <div class="input-group date with-icon">
                            {!! Form::input('text', 'ended_at', null, ['class' => 'form-control sel-status result_column_filter', 'id' => 'ended_at_filter', 'placeholder' => 'Date To']) !!}
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </div>
                    <div class="col-md-2 form-group">
                        <button class="btn btn-primary text-uppercase col-xs-12 pull-right" id="reset_report_filters">
                            Reset filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="box">
            <div class="box-body">
                <table id="users" class="table responsive nowrap ui celled" data-model="user">
                    <thead>
                    <tr>
                        <th>Test Type</th>
                        <th>Language</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade modal-center" id="test-table-modal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-body">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"></h4>
                    </div>

                    <div id="modal-content"></div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footer')
    <script>
        var tableTestTypes = [
            {{TEST_READING}}, {{TEST_LANGUAGE_USE_NEW}}, {{TEST_LISTENING}}
        ];
        var isAdmin = '{!! Auth::user()->hasRole(['master', 'administrator']) !!}';
        
        $(document).ready(function () {

            $(document).on('click', '.show-test-table',  function(e){
                e.preventDefault();
                var testId = $(this).attr('data-id'),
                        testName = $(this).attr('data-title');

                $.ajax({
                    url: '/task/' + testId + '/test-table',
                    type: 'GET',
                    success: function(res){
                        var modal = $('#test-table-modal');
                        modal.find('.modal-title').text(testName);
                        modal.find('#modal-content').html(res);
                        modal.modal('show');
                    }
                })
            });

            $("#reset_report_filters").on('click', function () {
                $('.result_column_filter').val('').trigger('change')
            });

            function generateUsersTable() {

                var filters = {};
                $('.result_column_filter').each(function () {
                    filters[this.name] = this.value;
                });

                return $('#users').DataTable({
                    destroy: true,
                    paging: true,
                    lengthChange: false,
                    pageLength: 10,
                    searching: false,
                    ordering: true,
                    order: [
                        [ 4, 'desc']
                    ],
                    info: true,
                    autoWidth: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '/results/datatable',
                        type: "GET",
                        data: {
                            filters: filters
                        }
                    }, createdRow: function createdRow(row, data) {
                        $(row).attr('data-id', data.id);
                    },
                    columns: [
                        {
                            data: 'paper.type.name',
                            name: 'paper.type.name',
                            orderable: true,
                            searchable: false
                        },
                        {
                            data: 'paper.task.language.name',
                            name: 'paper.task.language.name',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'paper.task.name',
                            name: 'paper.task.name',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'paper.task.email',
                            name: 'paper.task.email',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            orderable: true,
                            searchable: false
                        },
                        {
                            data: null,
                            name: null,
                            render: function(data, type, row){
                                var html = '<a href="/results/test/' + row.id + '/result" class="btn btn-danger go-to-test-button" data-id="' + row.id + '">See test</a><a href="/results/downloadPDF/' + row.id + '" class="btn btn-danger go-to-test-button text-right" data-id="' + row.id + '">Download as PDF</a>';

                                if (isAdmin && tableTestTypes.indexOf(parseInt(row.paper.paper_type_id)) >= 0){
                                    html += '<a href="#" class="btn btn-danger show-test-table" data-id="'+row.id+'" data-title="'+ row.paper.type.name+' Test Table">Test Table </a>';
                                }

                                return html;
                            },
                            orderable: false,
                            searchable: false
                        }
                    ] ,
                    "fnDrawCallback": function () {
                        var paginateRow = $('.dataTables_paginate');
                        var pageCount = Math.ceil((this.fnSettings().fnRecordsDisplay()) / this.fnSettings()._iDisplayLength);
                        if (pageCount > 1) {
                            paginateRow.css("display", "block");
                        } else {
                            paginateRow.css("display", "none");
                        }
                    }
                });

            }

            $('input#started_at_filter').datetimepicker({
                showClear: true,
                format: 'MM/DD/YYYY'
            });
            $('input#ended_at_filter').datetimepicker({
                showClear: true,
                format: 'MM/DD/YYYY'
            });

            $('input#started_at_filter').on("dp.change", function (e) {
                generateUsersTable();
                $("#ended_at_filter").datetimepicker('minDate', e.date);
            });

            $('input#ended_at_filter').on("dp.change", function (e) {
                generateUsersTable();
            });

            generateUsersTable();

            var submitTimer = null;

            $(document).on('keyup', 'input.result_column_filter', function () {

                clearTimeout(submitTimer);
                submitTimer = setTimeout(function () {
                    generateUsersTable();
                }, 500);

            });


            $('select.result_column_filter').on('change', function () {
                generateUsersTable();
            });

        });
    </script>
@endsection