@extends('layouts.app')
@section('sectionClass') tests-manager-section @endsection

@section('content')
    <div class="header-content">
        <div class="tag-name">
            Add answers for <b>{{ $question->body }}</b>
        </div>
        @canAtLeast(['user.create'])
        <button class="add-client add_question">
            <div class="ion-plus">
                Add new answer
            </div>
        </button>
        @endCanAtLeast
        <button class="add-client" id="show_filters"><i class="fa fa-reorder"></i>Filters</button>
    </div>

    <div class="panel" id="filters">
        <div class="panel-heading">
            Filter Answers
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-2 form-group">
                    {!! Form::input('text', 'search', null, ['class' => 'form-control sel-status column_filter', 'id' => 'search', 'placeholder' => 'Search']) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-body choices-page">

            <table id="table" class="table table-bordered table-striped" data-model="user">
                <thead>
                <tr>
                    <th>Answer</th>
                    <th>Correct</th>
                    <th>Actions</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

    <aside id="add_new_question" class="control-sidebar control-sidebar-edit add-new-project-modal">
        @include('manageTests.partials.modal-choice', ['choice' => null])
    </aside>

    <aside id="edit_question" class="control-sidebar control-sidebar-edit add-new-project-modal"></aside>

@endsection
@section('footer')
    <script>
        testsManagerTestID = '{{$question->id}}';
        tableGeneratorFunction = generateQuestionsChoicesTable;
    </script>
@endsection