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
                    {!! Form::select('status', [  '' => 'All', 1 => 'Active', 0 => 'Inactive' ], null, ['class' => 'form-control sel-status column_filter', 'id' => 'status_filter', 'data-placeholder' => 'Status']) !!}
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
            <table id="table" class="table table-bordered table-striped" data-model="user">
                <thead>
                <tr>
                    <th>Code</th>
                    <th>Title</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

    <aside id="add_new_question" class="control-sidebar control-sidebar-edit edit-template-modal add-new-project-modal">
        @include('manageTests.partials.modal-writing', ['question' => null])
    </aside>

    <aside id="edit_question" class="control-sidebar control-sidebar-edit  add-new-project-modal"></aside>

@endsection
@section('footer')
    <script>
        testsManagerTestID = '{{$testType->id}}';
        tableGeneratorFunction = generateWritingQuestionsTable;
    </script>
@endsection