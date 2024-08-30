@extends('layouts.app')
@section('sectionClass') tests-manager-section @endsection

@section('content')
    <div class="header-content">
        <div class="tag-name">
            {{ $testType->paperTypes->name }} Questions
            <small>{{ $language->name }}</small>
        </div>

        @canAtLeast(['user.create'])
        <button class="add-client add_question">
            <div class="ion-plus">
                Add new question
            </div>
        </button>
        @endCanAtLeast

        <button class="add-client" id="show_filters"><i class="fa fa-reorder"></i>Filters</button>
    </div>

    <div class="panel" id="filters">
        <div class="panel-heading">
            Filter Tests
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-2 form-group">
                    {!! Form::input('text', 'search', null, ['class' => 'form-control sel-status column_filter', 'id' => 'search', 'placeholder' => 'Search']) !!}
                </div>
                <div class="col-md-2 form-group">
                    {!! Form::select('question_level_id', $levels, null, ['class' => 'form-control sel-status column_filter', 'id' => 'level_filter', 'data-placeholder' => 'Level']) !!}
                </div>
                <div class="col-md-2 form-group">
                    {!! Form::select('q_type', $qTypes, null, ['class' => 'form-control sel-status column_filter', 'id' => 'status_filter', 'data-placeholder' => 'Select Q Type']) !!}
                </div>
                <div class="col-md-2 form-group">
                    {!! Form::select('status', [  '' => 'All', 1 => 'Active', 0 => 'Inactive' ], null, ['class' => 'form-control sel-status column_filter', 'id' => 'status_filter', 'data-placeholder' => 'Status']) !!}
                </div>
                <div class="col-md-2 form-group">
                    <button class="btn btn-primary text-uppercase col-xs-12 btn-sm pull-right" id="reset_filters">
                        Show all
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>

    <div class="box">
        <div class="box-body">
            <table id="table" class="table responsive nowrap ui celled" data-model="user">
                <thead>
                <tr>
                    <th>Code</th>
                    <th>Question Body</th>
                    <th>Level</th>
                    <th>Q Type</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

    <aside id="add_new_question" class="control-sidebar control-sidebar-edit edit-template-modal add-new-project-modal unclosable">
        @include('manageTests.partials.modal-listening', ['question' => null])
    </aside>

    <aside id="edit_question" class="control-sidebar control-sidebar-edit edit-template-modal add-new-project-modal unclosable"></aside>

@endsection
@section('footer')
    <script>
        testsManagerTestID = '{{$testType->id}}';
        tableGeneratorFunction = generateReadingQuestionsTable;
    </script>
@endsection