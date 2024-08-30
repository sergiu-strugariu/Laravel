@extends('layouts.app')

@section('content')
    <div class="home-page">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">Dashboard</div>

                        <div class="panel-body">
                            @if (session('status'))
                                <div class="alert alert-success">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <h4 style="text-align: center">You are logged in!</h4>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">Revenue Per Language (total in EUR)</div>
                        <div class="panel-body">
                            <label>Select Languages</label>
                            <p id="revenue-per-language-filters"></p>
                            <canvas id="revenue-per-language" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">Daily Revenue (in EUR)</div>
                        <div class="panel-body">
                            <div id="daily-revenue-filters">
                                <label>Filter by Date</label>
                                <div class="form-group">
                                    <div class="input-group date with-icon">
                                            <input type="text" id="revenue-per-day-daterange" class="form-control">
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                    </div>
                                </div>
                            </div>
                            <canvas id="revenue-per-day" height="300"></canvas>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
