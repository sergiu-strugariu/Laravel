@extends('layouts.app')

@section('content')
    <div class="invoices-page">
        <div class="header-content">
            <div class="tag-name">
                Invoices
            </div>
            <button id="show_filters" class="add-client">
                <img src="/assets/img/filter-button.svg">
                Filters
            </button>

            <span class="datatable-length form-inline">
                {!! Form::label('page-length', 'Show') !!}
                {!! Form::select('page-length', ['10' => '10 entries', '30' => '30 entries', '60' => '60 entries', '90' => '90 entries'], 10, ['class' => 'form-control', 'id' => 'invoice_dt_length']) !!}
            </span>
        </div>
        <div class="panel" id="filters">
            <div class="panel-heading">
                Filter invoices
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('client', 'Client') !!}
                            {!! Form::select('client', $clients->flip(), null, ['class' => 'form-control sel-status select2-tags column_filter', 'id' => 'client_select', 'placeholder' => 'Select Client']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('project', 'Project') !!}
                            {!! Form::select('project', $projects->flip(), null, ['class' => 'form-control sel-status select2-tags column_filter', 'id' => 'project_select', 'placeholder' => 'Select Project']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        {!! Form::label('billed_date', 'Billing date') !!}
                        <div class="input-group input-group date with-icon">
                            {!! Form::text('billed_date', null, ['class' => 'disabled form-control select-daterange column_filter', 'id' => 'billed_date', 'placeholder' => "Select billing date"]) !!}
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        {!! Form::label('billed_period', 'Billed period') !!}
                        <div class="input-group input-group date with-icon">
                            {!! Form::text('billed_period', null, ['class' => 'disabled form-control select-daterange column_filter', 'id' => 'billed_period', 'placeholder' => "Select billing period"]) !!}
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box invoices-box">
            <div class="box-header">
                <h3 class="box-title"></h3>
            </div>
            <div class="box-body">
                <div class="invoices-table">
                    <table class="table responsive nowrap ui celled">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Client</th>
                                <th>Project</th>
                                <th>Billed On</th>
                                <th>Billing Period</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('footer')
    <script>
        var invoicesPage;

        (function($) {
            var InvoicesPage = function () {
                var self = this;

                this.ready = function() {
                    if ($(".invoices-page").length > 0) {
                        this.handleDOM();
                        this.handleEvents();
                    }
                };

                this.handleDOM = function() {
                    this.page = $(".invoices-page");
                    this.filtersContainer = $("#filters");
                    this.pageLength = $("#invoice_dt_length");
                    this.invoicesTable = this.page.find(".invoices-table").first();
                    this.selectClient = $("#client_select");
                    this.selectProject = $("#project_select");
                    this.billedDate = $('#billed_date');
                    this.billedPeriod = $('#billed_period');
                };

                this.handleEvents = function() {
                    this.pageLength.select2({
                        minimumResultsForSearch: -1,
                        allowClear: false
                    });

                    this.selectClient.select2().on('change', function() {
                        self.dataTable.draw();
                    });

                    this.selectProject.select2().on('change', function() {
                        self.dataTable.draw();
                    });

                    this.billedDate.on("click focus change", function(e) {
                        e.preventDefault();
                        return false;
                    });
                    this.billedDate.daterangepicker({
                        autoUpdateInput: false,
                        opens: 'right',
                        locale: {
                            cancelLabel: 'Clear',
                            format: "DD-MM-YYYY"
                        }
                    }).on('apply.daterangepicker', function(ev, picker) {
                        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                        self.dataTable.draw();
                    });

                    this.billedPeriod.on("click focus change", function(e) {
                        e.preventDefault();
                        return false;
                    });
                    this.billedPeriod.daterangepicker({
                        autoUpdateInput: false,
                        opens: 'left',
                        locale: {
                            cancelLabel: 'Clear',
                            format: "DD-MM-YYYY"
                        }
                    }).on('apply.daterangepicker', function(ev, picker) {
                        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                        self.dataTable.draw();
                    });

                    this.drawDatatable();

                    this.pageLength.on("change", function(e) {
                        self.dataTable.page.len($(this).val()).draw();
                    });

                    this.page.on("click", ".export-annex", function(e) {
                        e.preventDefault();
                        self.exportAnnex($(this).attr('data-invoice-id'));
                    })
                };

                this.drawDatatable = function() {
                    this.dataTable = this.invoicesTable.find("table").first().DataTable({
                        destroy: true,
                        paging: this.pageLength.val() !== '-1',
                        lengthChange: false,
                        pageLength: parseInt(this.pageLength.val()),
                        searching: false,
                        ordering: false,
                        info: true,
                        autoWidth: true,
                        processing: true,
                        responsive: true,
                        serverSide: true,
                        ajax: {
                            url: '/invoices/all',
                            data: function(data) {
                                data.filter = {};
                                if (self.selectClient.val()) {
                                    data.filter['client_id'] = self.selectClient.val();
                                }

                                if (self.selectProject.val()){
                                    data.filter['project_id'] = self.selectProject.val();
                                }

                                if (self.billedDate.val()){
                                    data.filter['billed_date'] = self.billedDate.val();
                                }

                                if (self.billedPeriod.val()){
                                    data.filter['billed_period'] = self.billedPeriod.val();
                                }
                            }
                        },
                        columns: [
                            {
                                data: 'id',
                                name: 'id'
                            },
                            {
                                data: 'name',
                                name: 'name'
                            },
                            {
                                data: null,
                                name: 'client',
                                render: function (data, type, row) {
                                    return row.client.name
                                }
                            },
                            {
                                data: null,
                                name: 'project',
                                render: function (data, type, row) {
                                    return row.project.name
                                }
                            },
                            {
                                data: null,
                                name: 'date',
                                render: function (data, type, row) {
                                    return moment(row.created_at, "YYYY-MM-DD H:i:s").format("D MMM YYYY");
                                }
                            },
                            {
                                data: null,
                                name: 'period',
                                render: function (data, type, row) {
                                    var startDate = moment(row.date_from, "YYYY-MM-DD H:i:s").format("D MMM YYYY");
                                    var endDate = moment(row.date_to, "YYYY-MM-DD H:i:s").format("D MMM YYYY");
                                    return startDate + " - " + endDate;
                                }
                            },
                            {
                                data: null,
                                name: 'actions',
                                render: function (data, type, row) {
                                    return '<a href="/invoices/view-file/'+row.file+'" target="_blank"><i class="fa fa-file-pdf-o"></i></a>&nbsp;&nbsp;&nbsp;' +
                                    ' <a href="#" class="export-annex text-primary" data-invoice-id="' + row.id + '" target="_blank"><i class="fa fa-file-excel-o"></i></a>';
                                }
                            }
                        ]
                    });
                };

                this.exportAnnex = function(id) {
                    $('body .loading').addClass('active');

                    $.ajax({
                        url: '/invoices/export-annex/' + id,
                        type: "GET",
                        success: function (response) {
                            window.location.href = this.url;
                            $('body .loading').removeClass('active');
                        },
                        error: function (response) {

                        }
                    });
                };

            };

            invoicesPage = new InvoicesPage();

            $(document).ready(function () {
                invoicesPage.ready();
            });
        })(jQuery);
    </script>
@endsection