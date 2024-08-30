@extends('layouts.app')

@section('content')

    <aside id="projects_filter" class="hidden-xs">
        <div class="panel panel-default">
            <div class="panel-body">
                Projects
                @if(isset($project))
                    <form action="#" class="sidebar-form" id="projects_filter_form">
                        {!! Form::hidden('project_id', $project->id, ['id' => 'project_id']) !!}
                        <div class="input-group">
                        <span class="input-group-btn">
                            <button class="btn btn-flat">
                              <i class="fa fa-search"></i>
                            </button>
                         </span>
                            <input type="text" name="search_projects" class="search-bar" placeholder="Search Projects"
                                   id="search_projects">
                        </div>
                    </form>
                @endif
                <ul class="list-unstyled">
                    @php $projects = []; @endphp
                    @foreach($allProjects as $projectModal)
                        @if($projectModal->user_id == Auth()->user()->id || Auth()->user()->hasRole('master','administrator') || in_array(Auth()->user()->id, $projectModal->participants->pluck('user_id')->toArray()) || in_array(Auth()->user()->id, $projectModal->assessors()->pluck('id')->toArray()))
                            @php 
                            $projects[$projectModal->id] = $projectModal->name;
                            @endphp
                            <li>
                                <a href="{{ route('project-tasks', ['project' => $projectModal->id]) }}">{{ $projectModal->name }}</a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </aside>
    <div id="project_contents">
        <div class="header-content">
            <div class="tag-name">
                <a href="#" role="button" class="sidebar-togglex hidden-xs"
                   id="view_project_filter">
                    <i class="fa fa-angle-right pull-left"></i>
                </a>

                @if(!empty($global_search))
                    Search results for: {{ $global_search }}
                @else
                    @if(isset($project))
                        {{ $project->name }} Tasks
                    @else
                        All  @if($projectTypeName) {{$projectTypeName}} @endif tasks
                    @endif
                @endif
            </div>
            @canAtLeast(['project.create_tasks'])
            @if(empty($all_tasks) && isset($project))
                <button class="add-client add-button">
                    <div class="ion-plus">
                        Add Task
                    </div>
                </button>
            @endif
            @endCanAtLeast
            @if($all_tasks == 'active')
                <a class="add-client status_all"
                   @if(isset($request_all['project_type']))
                        href="?all=true&project_type={{$request_all['project_type']}}"
                    @else
                        href="?all=true"
                    @endif>Show all tasks</a>
            @else
                <a class="add-client status_all"
                   @if(isset($request_all['project_type']))
                        href="?all=active&project_type={{$request_all['project_type']}}"
                   @else
                        href="?all=active"
                    @endif
                >Show all active tasks</a>
            @endif
            @if($all_tasks == 'archived')
            <a class="add-client status_all"
                   @if(isset($request_all['project_type']))
                        href="?all=true&project_type={{$request_all['project_type']}}"
                   @else
                        href="?all=true"
                    @endif
                >Show all tasks</a>
            @else
                <a class="add-client status_all"
                   @if(isset($request_all['project_type']))
                        href="?all=archived&project_type={{$request_all['project_type']}}"
                   @else
                        href="?all=archived"
                    @endif
                >Show all archived tasks</a>
            @endif

            <button class="add-client" id="show_filters">
                <img src="{{ asset('assets/img/filter-button.svg') }}">
                Filters
            </button>
            <span class="datatable-length form-inline">
                {!! Form::label('page-length', 'Show') !!}
                {!! Form::select('page-length', ['10' => '10 entries', '30' => '30 entries', '60' => '60 entries', '90' => '90 entries'], 10, ['class' => 'form-control', 'id' => 'page_length']) !!}
            </span>
        </div>

        <div class="panel" id="filters">
            <div class="panel-heading">
                Filter Tasks
            </div>
            <div class="panel-body">
                <div class="row">
                    {!! Form::hidden('global_search', $global_search, ['id' => 'global_search', 'class' => 'column_filter']) !!}
                    <div class="col-md-2 form-group">
                        {!! Form::label('task_status_id', 'Status') !!}
                        @php
                            $taskStatusesArray = [];
                            $taskStatusesArray[''] = 'Select';
                            foreach ($taskStatuses as $id => $status) {
                                if ($id != ARCHIVED) {
                                    $taskStatusesArray[$id] = $status;
                                }
                            }
                        @endphp
                        {!! Form::select('task_status_id', $taskStatusesArray, null, ['class' => 'form-control sel-status column_filter select2-tags', 'id' => 'task_status_filter', 'placeholder' => 'All Tasks']) !!}
                    </div>
                    @if(!isset($project))
                     <div class="col-md-2 form-group">
                        {!! Form::label('name', 'Project') !!}
                        {!! Form::select('project_id', $projects, null, ['class' => 'form-control sel-status column_filter select2-tags', 'id' => 'project_filter_input', 'placeholder' => 'Any Project']) !!}
                    </div>
                    @endif
                    <div class="col-md-2 form-group">
                        {!! Form::label('id_filter', 'ID') !!}
                        {!! Form::select('id', [], null, ['class' => 'form-control sel-status column_filter select2-tags', 'id' => 'id_filter', 'placeholder' => 'Any ID']) !!}
                    </div>
                    <div class="col-md-2 form-group">
                        {!! Form::label('name', 'Name') !!}
                        {!! Form::input('text', 'name', null, ['class' => 'form-control sel-status column_filter', 'id' => 'name_filter_input', 'placeholder' => 'Any Name']) !!}
                    </div>
                    @if($viewAssesorPermission)
                        <div class="col-md-2 form-group">
                            {!! Form::label('assessor_id', 'Assessor') !!}
                            {!! Form::select('assessor_id', $assessors, null, ['class' => 'form-control sel-status column_filter select2-single', 'id' => 'assessor_filter', 'placeholder' => 'Any Assessor']) !!}
                        </div>
                    @endif
                    <div class="col-md-2 form-group">
                        {!! Form::label('language_id', 'Language') !!}
                        {!! Form::select('language_id', $languages, null, ['class' => 'form-control sel-status column_filter select2-single', 'id' => 'language_filter', 'placeholder' => 'Any Language']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 form-group">
                        {!! Form::label('added_by_id', 'Added by') !!}
                        {!! Form::select('added_by_id', $addedByUsers, null, ['class' => 'form-control sel-status column_filter select2-single', 'id' => 'added_by_filter', 'placeholder' => 'Any User']) !!}
                    </div>

                    <div class="col-md-3 form-group">
                        {!! Form::label('date_test_range_filter', 'Date Test taken') !!}
                        <div class="input-group date with-icon">
                            {!! Form::input('text', 'date_range_tests', null, ['class' => 'form-control sel-status column_filter', 'id' => 'date_test_range_filter', 'placeholder' => 'Date Interval', 'required' => true]) !!}
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </div>

                    <div class="col-md-3 form-group">
                        {!! Form::label('date_assessor_tests_range_filter', 'Date Assessor Tests') !!}
                        <div class="input-group date with-icon">
                            {!! Form::input('text', 'date_assessor_tests_range', null, ['class' => 'form-control sel-status column_filter', 'id' => 'date_assessor_tests_range_filter', 'placeholder' => 'Date Interval', 'required' => true]) !!}
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </div>

                    <div class="col-md-3 form-group">
                        {!! Form::label('date_range_filter', 'Date task added') !!}
                        <div class="input-group date with-icon">
                            {!! Form::input('text', 'date_range', '', ['class' => 'form-control sel-status column_filter', 'id' => 'date_range_filter', 'placeholder' => 'Date Interval', 'required' => true]) !!}
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </div>
                </div>
                @if(Auth()->user()->hasRole(['master', 'administrator']))
                    <div class="row">
                        <div class="col-md-3 form-group">
                            {!! Form::label('has_unbilled_tests', 'Has unbilled tests') !!}
                            {!! Form::select('has_unbilled_tests', ["yes" => "Yes", "no" => "No"], null, ['class' => 'form-control sel-status column_filter select2-single', 'id' => 'has_unbilled_tests', 'placeholder' => 'Has unbilled tests']) !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="box">
            <!-- /.box-header -->
            <div class="box-body">
                <table id="project-tasks-table" class="table responsive nowrap ui celled"
                       data-route="project/{{$route}}"
                       data-permanent-filters="{{(isset($permanentFilters) && !(empty($permanentFilters))) ? $permanentFilters : false}}"
                >
                    <thead>
                    <tr>

                        <th class="styled-checkbox" colspan="1">
                            @if(Auth()->user()->hasRole(['master', 'administrator']))
                                <label>
                                    <input type="checkbox" name="task-batch-all" class="task-batch-all"/> <span
                                            class="label-text"></span>
                                </label>
                            @endif
                        </th>

                        <th>Task ID</th>
                        <th class="{{ (empty($global_search) && empty($all_tasks)) ? 'hidden' : '' }}">Project</th>
                        <th>Name</th>
                        <th>Language</th>
                        <th>Grades</th>
                        <th>Status</th>
                        <th>Availability</th>
                        <th>Added by</th>
                        <th>Date Added</th>
                        <th class="{{ $viewAssesorPermission ? '' : 'hidden' }}">Assessor</th>
                        <th class="{{ Auth::user()->canAtLeast(['task.refuse']) ? '' : 'hidden' }}">Refuse Task</th>
                        <th class="{{ Auth::user()->canAtLeast(['task.update']) ? '' : 'hidden' }}">Actions</th>
                    </tr>
                    </thead>
                </table>

                <div class="export-buttons">
                    @canAtLeast(['project.create_tasks'])
                        <button class="btn-export btn-batch">Batch Update</button>
                    @endCanAtLeast
                    @if(isset($project))
                        @canAtLeast(['project.create_tasks'])
                        <button class="btn-export" onclick="window.open('/Project-Tasks-Template.xlsx')">Export XLS Template</button>
                        @if( $all_tasks != true )
                            <button class="btn-export" id="import_xls">Import from XLS</button>
                        @endif
                        @endCanAtLeast
                    @endif
                    <button class="btn-export" id="export">Export Tasks</button>
                    <button class="btn-export" id="export-grades">Export Grades</button>
                </div>

            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>

    <script id="import-xls-template" type="text/x-custom-template">
        {!! Form::open(['id' => 'import-form', 'files' => true]) !!}
        <div class="col-xs-12">
            {!! Form::file('import-file', null, ['class' => 'form-control sel-status', 'id' => 'import-file', 'placeholder' => 'Tasks XLS', 'required' => true]) !!}
        </div>
        {!! Form::close() !!}
    </script>


    <aside id="batch-modal" class="control-sidebar control-sidebar-add">
        <div class="loading">
            <div class="loading-wheel"></div>
        </div>
        <div class="panel">
            <div class="panel-heading">
                <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
                <h1>
                    Edit batch
                </h1>
            </div>
            <div class="panel-body">
                {!! Form::open([ 'action' => ['TaskController@updateBatch'], 'method' => 'POST', 'id' => 'edit-form-batch']) !!}
                {!! Form::hidden('task_ids', '', ['class' => 'hidden_task_ids']) !!}

                <div class="form-group">
                    <div class="row" id="batch-assessor">
                        @if( !Auth::user()->hasRole(['client']))
                            <div class="col-sm-9">
                                {!! Form::label('batch_assessor_id', 'Assessor') !!}
                                {!! Form::select('assessor_id', [], null, ['class' => 'form-control sel-status select2-single', 'id' => 'batch_assessor_id']) !!}
                            </div>
                            <div class="col-sm-3" id="batch_native_parent">
                                {!! Form::label('native', 'Native assessor') !!}
                                <div class="col-xs-12 no-padding">
                                    {!! Form::checkbox('native', true, false, ['id' => 'batch-native']) !!}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row hidden" id="no-assessor-change">
                        <div class="col-sm-12">
                            <label>Assessor</label>
                            <p>You can't update assessor because you have selected tasks with different languages!</p>
                        </div>
                    </div>
                </div>
                @if( !Auth::user()->hasRole(['client']))
                    <div class="form-group">
                        {!! Form::label('bill_client', 'Bill client', ['class' => 'awesome']) !!}
                        {!! Form::select('bill_client', [ '' => 'Select', 0 => 'No', 1 => 'Yes', 2 => 'Half Price' ], null, ['class' => 'form-control', 'id' => 'batch_default_bill_client']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('pay_assessor', 'Pay assessors', ['class' => 'awesome']) !!}
                        {!! Form::select('pay_assessor', [ '' => 'Select', 0 => 'No', 1 => 'Yes' ], null, ['class' => 'form-control', 'id' => 'batch_default_pay_assessor']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('task_status_id', 'Status', ['class' => 'awesome']) !!}
                        @php
                            $taskStatusesArray = [];
                            $taskStatusesArray[''] = 'Select';
                            foreach ($taskStatuses as $id => $status) {
                                $taskStatusesArray[$id] = $status;
                            }
                        @endphp
                        {!! Form::select('task_status_id', $taskStatusesArray, null, ['class' => 'form-control', 'id' => 'batch_default_task_status_id']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('language_id', 'Language', ['class' => 'awesome']) !!}
                        @php
                            $languagesArray = [];
                            $languagesArray[''] = 'Select';
                            foreach ($languages as $id => $lang) {
                                $languagesArray[$id] = $lang;
                            }
                        @endphp
                        {!! Form::select('language_id', $languagesArray, null, ['class' => 'form-control', 'id' => 'batch_default_language_id']) !!}
                    </div>

                    <div class="form-group">
                        <div class="row" id="batch-tests">
                            <div class="col-sm-12">
                                {!! Form::label('batch_tests', 'Add Tests') !!}
                                {!! Form::select('tests', [], null, ['class' => 'form-control sel-status select2-single', 'id' => 'batch_tests']) !!}
                            </div>
                        </div>
                        <div class="row hidden" id="no-test-type-change">
                            <div class="col-sm-12">
                                <label>Tests</label>
                                <p>You can't add tests because you have selected tasks with different languages!</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    {!! Form::submit('Save Tasks', ['class' => 'btn btn-danger edit-batch-submit-button']) !!}
                </div>

                {!! Form::close() !!}

            </div>

        </div>
        <div class="panel">
            <div class="panel-body">
                {!! Form::open(['action' => ['TaskController@deleteBatch'], 'method' => 'DELETE', 'id' => 'delete-tasks-form-batch']) !!}
                {!! Form::hidden('task_ids', '', ['class' => 'hidden_task_ids']) !!}
                <div class="form-group">
                    {!! Form::button('Delete All', ['class' => 'btn btn-danger', 'id' => 'delete-batch-submit-button']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </aside>
@endsection
@if(isset($project))

@section('aside-right')
    <input type="hidden" id="isTaskPage"/>
    <aside id="add-modal" class="control-sidebar control-sidebar-edit">
        <div class="loading">
            <div class="loading-wheel"></div>
        </div>
        <div class="panel">
            <div class="panel-heading">
                <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
                <h1>
                    Add task
                </h1>
            </div>
            <div class="panel-body">
                {!! Form::open(['id' => 'add-form', 'files' => true]) !!}
                {!! Form::hidden('project_id', $project->id, ['id' => 'project_id', 'files' => true]) !!}
                {!! Form::hidden('task_id', null, ['id' => 'task_id']) !!}
                <div class="form-group">
                    {!! Form::label('name', 'First & Last Name') !!} <span class="required-input">*</span>
                    {!! Form::input('text', 'name', null, ['class' => 'form-control sel-status', 'id' => 'name', 'placeholder' => 'First & Last Name', 'required' => true]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('email', 'E-mail') !!} <span class="required-input">*</span>
                    {!! Form::input('email', 'email', null, ['class' => 'form-control sel-status', 'id' => 'email', 'placeholder' => 'E-mail', 'required' => true]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('phone', 'Phone') !!} <span class="required-input">*</span>
                    {!! Form::input('text', 'phone', null, ["maxlength" => "20", 'class' => 'form-control sel-status', 'id' => 'phone', 'placeholder' => 'Phone', 'required' => true]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('phone', 'Skype') !!}
                    {!! Form::input('text', 'skype', null, ['class' => 'form-control sel-status', 'id' => 'skype', 'placeholder' => 'Skype']) !!}
                </div>
                @if($project->type->name == 'Courses Initial Tests')
                    <div class="form-group">
                        {!! Form::label('mark', 'Mark') !!}
                        {!! Form::input('text', 'mark', null, ['class' => 'form-control sel-status', 'id' => 'mark', 'placeholder' => 'Mark']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('department', 'Department') !!}
                        {!! Form::input('text', 'department', null, ['class' => 'form-control sel-status', 'id' => 'department', 'placeholder' => 'Department']) !!}
                    </div>
                @endif
                <div class="form-group">
                    {!! Form::label('followers', 'Followers') !!}
                    {!! Form::select('followers[]', $projectParticipants, null, ['class' => 'form-control sel-status select2-multiple', 'id' => 'follower_id', 'multiple' => true]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('extra_info', 'Extra Information') !!}
                    {!! Form::textarea('extra_info', null, [
                        'class' => 'form-control sel-status',
                        'id' => 'extra_info',
                        'placeholder' => 'Extra Information that you want to add',
                        'maxlength' => 200,
                        'rows' => 4
                    ]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('attachment', 'Attachment') !!}
                    <div class="input-group date">
                        {!! Form::file('attachment', null, ['class' => 'form-control sel-status', 'id' => 'attachment', 'placeholder' => 'Attachment']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('languages', 'Language') !!} <span class="required-input">*</span>
                    {!! Form::select('languages[]', $languages, null, ['class' => 'form-control sel-status select2-multiple', 'id' => 'language_id', 'multiple' => true, 'required' => true]) !!}
                </div>

                <ul class="nav nav-tabs">
                    <li class="add-new-language"><a style=""><span class="ion-plus"></span>Add new</a></li>
                </ul>

                <div class="tab-content"></div>
                <div class="form-group">
                    {!! Form::submit('Add task', ['class' => 'btn btn-danger', 'id' => 'add-submit-button']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div id="add-task-language" class="tab-pane fade in hidden">
            <div class="form-group">
                <div class="row lang_paper_types"></div>
                <div class="row">
                </div>
            </div>
            <div class="form-group assessor-parent {{ $viewAssesorPermission || Auth()->user()->hasRole('client') ? '' : 'hidden-important' }}">
                <div class="row">
                    @if($viewAssesorPermission)
                        <div class="col-sm-8">
                            {!! Form::label('assessor_id', 'Assessor') !!}
                            {!! Form::select('assessor_id', $assessors, null, ['class' => 'form-control sel-status not-select2 select2-single', 'id' => 'assessor_id']) !!}
                        </div>
                    @endif
                    @if($viewAssesorPermission || Auth()->user()->hasRole('client'))
                        <div class="col-sm-4" id="native_parent">
                            {!! Form::label('native', 'Native assessor') !!}
                            <div class="col-xs-12 no-padding">
                                {!! Form::checkbox('native', true, false, ['id' => 'native']) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group deadline_parent">
                {!! Form::label('deadline', 'Deadline for online tests') !!}
                <div class="input-group date with-icon">
                    {!! Form::input('text', 'deadline', null, ['class' => 'form-control sel-status deadline-input', 'id' => 'deadline', 'placeholder' => 'Deadline']) !!}
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
            </div>
            <div class="availability_parent hidden">
                {!! Form::label('availability', 'Availability for Speaking Test (day)') !!}
                <div class="row">
                    <div class='col-md-12'>
                        <div class="form-group">
                            <div class='input-group date with-icon'>
                                {!! Form::input('text', 'availability_from', null, ['class' => 'form-control sel-status speaking-availability', 'id' => 'availability_from', 'placeholder' => 'Choose day']) !!}
                                <span class="input-group-addon"><span
                                            class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <label for="availability_time">Availability for Speaking Test (time)</label>
                    </div>
                    <div class='col-md-6'>
                        <label for="timepick_from">From</label>
                        <div class="form-group">
                            <div class='input-group'>
                                {!! Form::input('text', 'from_date', null, ['class' => 'form-control sel-status timepick_from', 'readonly' => 'readonly', 'id' => 'timepick_from', 'placeholder' => 'Hour (Romanian time)']) !!}
                            </div>
                        </div>
                    </div>
                    <div class='col-md-6'>
                        <label for="timepick_to">To</label>
                        <div class="form-group">
                            <div class='input-group'>
                                {!! Form::input('text', 'to_date', null, ['class' => 'form-control sel-status timepick_to', 'readonly' => 'readonly', 'id' => 'timepick_to', 'placeholder' => 'Hour (Romanian time)']) !!}
                            </div>
                        </div>
                    </div>
                    <div class='col-md-12'>
                        <div class="form-group">
                            <label class="custom-period-toggle btn btn-blue">
                                {!! Form::checkbox('has_custom_period',null,null, ['class' => 'has_custom_period', 'id' => 'has_custom_period']) !!}
                                <span>Add Custom Period for Speaking Test</span> <span class="cost">+0 EUR</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </aside>
    <aside id="edit-modal" class="control-sidebar control-sidebar-add">
        <div class="loading">
            <div class="loading-wheel"></div>
        </div>
        <div class="panel">
            <div class="panel-heading">
                <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
                <h1>
                    Edit task
                </h1>
            </div>
            <div class="panel-body">
                {!! Form::open(['id' => 'edit-form', 'files' => true]) !!}
                {!! Form::hidden('project_id', $project->id, ['id' => 'project_id']) !!}
                {!! Form::hidden('project_type_id', $project->project_type_id, ['id' => 'project_type_id']) !!}
                {!! Form::hidden('task_id', null, ['id' => 'task_id']) !!}
                <div class="form-group">
                    {!! Form::label('name', 'Name') !!}
                    {!! Form::input('text', 'name', null, ['class' => 'form-control sel-status', 'id' => 'name', 'placeholder' => 'Name', 'required' => true]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('email', 'E-mail') !!}
                    {!! Form::input('email', 'email', null, ['class' => 'form-control sel-status', 'id' => 'email', 'placeholder' => 'E-mail', 'required' => true]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('phone', 'Phone') !!}
                    {!! Form::input('text', 'phone', null, ['class' => 'form-control sel-status', 'id' => 'phone', 'placeholder' => 'Phone', 'required' => true]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('phone', 'Skype') !!}
                    {!! Form::input('text', 'skype', null, ['class' => 'form-control sel-status', 'id' => 'skype', 'placeholder' => 'Skype']) !!}
                </div>
                @if( !auth()->user()->hasRole('client'))
                <div class="form-group">
                    {!! Form::label('bill_client', 'Bill Client') !!}
                    {!! Form::select('bill_client',  [ 0 => 'No', 1 => 'Yes' ], ['class' => 'form-control sel-status', 'id' => 'bill_client', 'placeholder' => 'Bill Client']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('pay_assessor', 'Pay assessor') !!}
                    {!! Form::select('pay_assessor',  [ 0 => 'No', 1 => 'Yes' ], ['class' => 'form-control sel-status', 'id' => 'pay_assessor', 'placeholder' => 'Pay assessor']) !!}
                </div>
                @endif
                <div class="form-group {{ !Auth::user()->hasRole(['master','administrator']) ? 'client-edit-permissions' : '' }} ">
                    <div class="row">
                        <div class="col-sm-9 {{ $viewAssesorPermission ? '' : 'hidden' }}">
                            {!! Form::label('assessor_id', 'Assessor') !!}
                            {!! Form::select('assessor_id', $assessors, null, ['class' => 'form-control sel-status select2-single', 'id' => 'assessor_id']) !!}
                        </div>
                        <div class="col-sm-3 " id="native_parent">
                            {!! Form::label('native', 'Native assessor') !!}
                            <div class="col-xs-12 no-padding">
                                {!! Form::checkbox('native', true, false, ['id' => 'native']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                @if($project->type->name == 'Courses Initial Tests')
                    <div class="form-group">
                        {!! Form::label('mark', 'Mark') !!}
                        {!! Form::input('text', 'mark', null, ['class' => 'form-control sel-status', 'id' => 'mark', 'placeholder' => 'Mark']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('department', 'Department') !!}
                        {!! Form::input('text', 'department', null, ['class' => 'form-control sel-status', 'id' => 'department', 'placeholder' => 'Department']) !!}
                    </div>
                @endif
                <div class="form-group">
                    {!! Form::label('followers', 'Followers') !!}
                    {!! Form::select('followers[]', $projectParticipants, null, ['class' => 'form-control sel-status select2-multiple', 'id' => 'follower_id', 'multiple' => true]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('attachment', 'Attachment') !!}
                    <div class="input-group date">
                        {!! Form::file('attachment', null, ['class' => 'form-control sel-status', 'id' => 'attachment', 'placeholder' => 'Attachment']) !!}
                    </div>
                </div>
                <div class="form-group row" id="view_attachments"></div>
                <div class="form-group">
                    {!! Form::label('languages', 'Language') !!}
                    {!! Form::select('languages[]', $languages, null, ['class' => 'form-control sel-status select2-multiple', 'id' => 'language_id', 'required' => true]) !!}
                </div>

                <div class="form-group deadline_parent">
                    {!! Form::label('deadline', 'Deadline for online tests') !!}
                    <div class="input-group date with-icon">
                        {!! Form::input('text', 'deadline', null, ['class' => 'form-control sel-status', 'id' => 'deadline', 'placeholder' => 'Deadline']) !!}
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                    </div>
                </div>

                <div class="availability_parent">
                    {!! Form::label('availability', 'Availability for Speaking Test (day)') !!}
                    <div class="row">
                        <div class='col-md-12'>
                            <div class="form-group">
                                <div class='input-group date with-icon'>
                                    {!! Form::input('text', 'availability_from', null, ['class' => 'form-control sel-status speaking-availability', 'id' => 'availability_from', 'placeholder' => 'Choose day']) !!}
                                    <span class="input-group-addon"><span
                                                class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label for="">Availability for Speaking Test (time)</label>
                        </div>
                        <div class='col-md-6'>
                            {!! Form::label('edit_timepick_from', 'From') !!}
                            <div class="form-group">
                                <div class='input-group'>
                                    {!! Form::input('text', 'from_date', null, ['class' => 'form-control sel-status timepick_from', 'id' => 'edit_timepick_from', 'placeholder' => 'Hour']) !!}
                                </div>
                            </div>
                        </div>
                        <div class='col-md-6'>
                            {!! Form::label('edit_timepick_to', 'To') !!}
                            <div class="form-group">
                                <div class='input-group'>
                                    {!! Form::input('text', 'to_date', null, ['class' => 'form-control sel-statusx timepick_to', 'id' => 'edit_timepick_to', 'placeholder' => 'Hour']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row lang_paper_types"></div>
                    <div class="row edit-paper-types">
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::submit('Save Task', ['class' => 'btn btn-danger', 'id' => 'edit-submit-button']) !!}
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </aside>

    <aside id="updates-modal" class="control-sidebar control-sidebar-add">
        <div class="loading">
            <div class="loading-wheel"></div>
        </div>
        <div class="panel">
            <div class="panel-heading">
                <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
                <h1>#</h1>
            </div>
            <div class="panel-body"></div>
        </div>
    </aside>
@endsection
@else
@section('aside-right')
    <aside id="updates-modal" class="control-sidebar control-sidebar-add">
        <div class="loading">
            <div class="loading-wheel"></div>
        </div>
        <div class="panel">
            <div class="panel-heading">
                <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
                <h1>#</h1>
            </div>
            <div class="panel-body"></div>
        </div>
    </aside>

    <aside id="edit-modal" class="control-sidebar control-sidebar-add">
        <div class="loading">
            <div class="loading-wheel"></div>
        </div>
        <div class="panel">
            <div class="panel-heading">
                <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
                <h1>
                    Edit task
                </h1>
            </div>
            <div class="panel-body">
                {!! Form::open(['id' => 'edit-form', 'files' => true]) !!}
                {{--{!! Form::hidden('project_id', $project->id, ['id' => 'project_id']) !!}--}}
                {!! Form::hidden('task_id', null, ['id' => 'task_id']) !!}
                {!! Form::hidden('project_type_id', null, ['id' => 'project_type_id']) !!}
                <div class="form-group">
                    {!! Form::label('name', 'Name') !!}
                    {!! Form::input('text', 'name', null, ['class' => 'form-control sel-status', 'id' => 'name', 'placeholder' => 'Name', 'required' => true]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('email', 'E-mail') !!}
                    {!! Form::input('email', 'email', null, ['class' => 'form-control sel-status', 'id' => 'email', 'placeholder' => 'E-mail', 'required' => true]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('phone', 'Phone') !!}
                    {!! Form::input('text', 'phone', null, ['class' => 'form-control sel-status', 'id' => 'phone', 'placeholder' => 'Phone', 'required' => true]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('phone', 'Skype') !!}
                    {!! Form::input('text', 'skype', null, ['class' => 'form-control sel-status', 'id' => 'skype', 'placeholder' => 'Skype']) !!}
                </div>
                @if( !auth()->user()->hasRole('client'))
                <div class="form-group">
                    {!! Form::label('bill_client', 'Bill Client') !!}
                    {!! Form::select('bill_client',  [ 0 => 'No', 1 => 'Yes' ], ['class' => 'form-control sel-status', 'id' => 'bill_client', 'placeholder' => 'Bill Client']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('pay_assessor', 'Pay assessor') !!}
                    {!! Form::select('pay_assessor',  [ 0 => 'No', 1 => 'Yes' ], ['class' => 'form-control sel-status', 'id' => 'pay_assessor', 'placeholder' => 'Pay assessor']) !!}
                </div>
                @endif
                <div class="form-group {{ $viewAssesorPermission ? '' : 'hidden' }}">
                    <div class="row">
                        <div class="col-sm-9">
                            {!! Form::label('assessor_id', 'Assessor') !!}
                            {!! Form::select('assessor_id', $assessors, null, ['class' => 'form-control sel-status select2-single', 'id' => 'assessor_id']) !!}
                        </div>
                        <div class="col-sm-3" id="native_parent">
                            {!! Form::label('native', 'Native assessor') !!}
                            <div class="col-xs-12 no-padding">
                                {!! Form::checkbox('native', true, false, ['id' => 'native']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                {{--@if($project->type->name == 'Courses Initial Tests')--}}
                {{--<div class="form-group">--}}
                {{--{!! Form::label('mark', 'Mark') !!}--}}
                {{--{!! Form::input('text', 'mark', null, ['class' => 'form-control sel-status', 'id' => 'mark', 'placeholder' => 'Mark']) !!}--}}
                {{--</div>--}}
                {{--<div class="form-group">--}}
                {{--{!! Form::label('department', 'Department') !!}--}}
                {{--{!! Form::input('text', 'department', null, ['class' => 'form-control sel-status', 'id' => 'department', 'placeholder' => 'Department']) !!}--}}
                {{--</div>--}}
                {{--@endif--}}
                <div class="form-group">
                    {!! Form::label('followers', 'Followers') !!}
                    {!! Form::select('followers[]', $projectParticipants, null, ['class' => 'form-control sel-status select2-multiple', 'id' => 'follower_id', 'multiple' => true]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('attachment', 'Attachment') !!}
                    <div class="input-group date">
                        {!! Form::file('attachment', null, ['class' => 'form-control sel-status', 'id' => 'attachment', 'placeholder' => 'Attachment']) !!}
                    </div>
                </div>
                <div class="form-group row" id="view_attachments"></div>
                <div class="form-group">
                    {!! Form::label('languages', 'Language') !!}
                    {!! Form::select('languages[]', $languages, null, ['class' => 'form-control sel-status select2-multiple', 'id' => 'language_id', 'required' => true]) !!}
                </div>

                <div class="form-group deadline_parent">
                    {!! Form::label('deadline', 'Deadline for online tests') !!}
                    <div class="input-group date with-icon">
                        {!! Form::input('text', 'deadline', null, ['class' => 'form-control sel-status', 'id' => 'deadline', 'placeholder' => 'Deadline']) !!}
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                    </div>
                </div>

                <div class="availability_parent">
                    {!! Form::label('availability', 'Availability for Speaking Test (day)') !!}
                    <div class="row">
                        <div class='col-md-12'>
                            <div class="form-group">
                                <div class='input-group date with-icon'>
                                    {!! Form::input('text', 'availability_from', null, ['class' => 'form-control sel-status speaking-availability', 'id' => 'availability_from', 'placeholder' => 'Choose day']) !!}
                                    <span class="input-group-addon"><span
                                                class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label for="">Availability for Speaking Test (time)</label>
                        </div>
                        <div class='col-md-6'>
                            {!! Form::label('edit_timepick_from', 'From') !!}
                            <div class="form-group">
                                <div class='input-group'>
                                    {!! Form::input('text', 'from_date', null, ['class' => 'form-control sel-status timepick_from', 'id' => 'edit_timepick_from', 'placeholder' => 'Hour']) !!}
                                </div>
                            </div>
                        </div>
                        <div class='col-md-6'>
                            {!! Form::label('edit_timepick_to', 'To') !!}
                            <div class="form-group">
                                <div class='input-group'>
                                    {!! Form::input('text', 'to_date', null, ['class' => 'form-control sel-statusx timepick_to', 'id' => 'edit_timepick_to', 'placeholder' => 'Hour']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row lang_paper_types"></div>
                    <div class="row edit-paper-types">
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::submit('Save Task', ['class' => 'btn btn-danger', 'id' => 'edit-submit-button']) !!}
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </aside>
@endsection
@endif


@section('footer')
    <script>

        @if( session()->has('flash_success') )
                swal('success', '{{session()->get('flash_success')}}');
        @endif

        @if( session()->has('flash_info') )
            swal('warning', '{{session()->get('flash_info')}}');
        @endif

        var isAdmin = '{{ Auth()->user()->hasRole(['master', 'administrator']) ? 1 : 0 }}';
        var projectType = parseInt({{ (isset($project) ? $project->project_type_id : null) }});
        var isClient = '{{ Auth()->user()->hasRole('client') ? 1 : 0 }}';
        var isAssessor = '{{ Auth::user()->hasRole('assessor') ? 1 : 0 }}';
        var isOnlyAssessor = '{{ Auth::user()->hasOnlyRole('assessor') ? 1 : 0 }}';
        var currUID = {{Auth()->user()->id}};
        var TEST_LANGUAGE_USE_NEW = {{TEST_LANGUAGE_USE_NEW}},
                TEST_SPEAKING = {{TEST_SPEAKING}},
                TEST_WRITING = {{TEST_WRITING}},
                TEST_LISTENING = {{TEST_LISTENING}},
                TEST_READING = {{TEST_READING}};
    </script>
    <script src="{{ asset('js/scripts-task-page.min.js') }}"></script>


    @if ($hidePrices && !Auth()->user()->hasRole('master','administrator'))
        <style>
            .paper-cost {
                display: none !important;
            }
        </style>
    @endif
@endsection
