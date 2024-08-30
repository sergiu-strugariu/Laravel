@extends('layouts.app')

@section('content')
    <div class="billing-page">
        <div class="header-content">
            <div class="tag-name">
                Billing
            </div>
            <a href="#" class="add-client status_all generate-invoice">Generate Invoice</a>
            <button id="show_filters" class="add-client">
                <img src="/assets/img/filter-button.svg">
                Filters
            </button>
        </div>
        <div class="panel" id="filters">
            <div class="panel-heading">
                Select client and date
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::select('client', $clients, null, ['class' => 'form-control sel-status select2-tags column_filter', 'id' => 'client_select', 'placeholder' => 'Select Client']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group date with-icon">
                            {!! Form::text('date', null, ['class' => 'disabled form-control select-daterange']) !!}
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box client-table-container client_box hidden">
            <div class="box-header">
                <h3 class="client-name box-title"></h3>
                <div class="client-actions">
                </div>
            </div>
            <div class="box-body">
                <div class="projects-table">
                    <table class="table responsive nowrap ui celled">
                        <thead>
                        <tr>
                            <th class="styled-checkbox"><label><input type="checkbox" name="billing-export-all" class="billing-export-all" /> <span class="label-text"></span></label></th>
                            <th>Project Name</th>
                            <th>Tasks</th>
                            <th>Billed Tests</th>
                            <th>Unbilled Tests</th>
                            <th>Value</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <aside class="control-sidebar">
            <div class="loading">
                <div class="loading-wheel"></div>
            </div>
            <div class="panel">
                <div class="panel-heading">
                    <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
                    <h1></h1>
                </div>
                <div class="panel-body">
                </div>
            </div>
        </aside>
    </div>
@endsection

@section('footer')
@endsection