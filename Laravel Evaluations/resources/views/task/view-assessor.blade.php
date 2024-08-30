@extends('layouts.app')

@section('content')
    <div id="view-task" class="content-view-task-page">
        <div class="header-content">
            <div class="tag-name project-name-task">
                <a href="/tasks?all">All Tasks</a> / <a href="{{ "/project/$task->project_id/tasks" }}">{{ $task->project->name }}</a> / {{$task->name}}
            </div>

            @hasRole(['administrator', 'master', 'client', 'css', 'recruiter', 'tds'])
            @if($showDownloadPdfButton)
                <a href="/task/{{$task->id}}/downloadAsPdf" class="btn btn-danger btn-download-pdf">Download as PDF</a>
            @endif
            @endHasRole

            @include('task.partials.buttons')

        </div>


        @include('task.partials.aside-eucom')

        <div class="row">
            <div class="col-xs-12 col-md-8 no-padding">
                <div class="col-xs-12 no-padding flex">
                    <div class="col-md-6" style="flex: 1">
                        @include('task.partials.task-left')
                    </div>
                    <div class="col-md-6" style="flex: 1">
                        @include('task.partials.task-details')
                    </div>
                </div>

                {{--todo--}}
                @hasRole(['assessor'])
                @if($task->remainingOnlineTests(true))
                    <div class="col-xs-12">
                        <button class="btn btn-danger btn-remind">! This test-taker also has an online test to take.
                            Please remind them !
                        </button>
                    </div>
                @endif
                @endHasRole

                @include('task.partials.task-tests-requested')

                @hasRole('tds')
                @if($task->completedTests()->count() && in_array(auth()->user()->id, $task->project->participants->pluck('user_id')->toArray()))
                    <div class="col-md-6">
                        <div class="panel" id="task-tests-details">
                            <div class="panel-heading">
                                Tests details
                            </div>
                            <div class="panel-body">
                                <canvas id="task-tests-chart"></canvas>
                            </div>
                        </div>
                    </div>
                @endif
                @endHasRole

                @include('task.partials.task-skill-assessments')

                @hasRole(['assessor', 'administrator', 'master'])

                    @include('task.partials.task-speaking-report')
                    @include('task.partials.task-writing-report')

                @endHasRole
            </div>
            <div class="col-xs-12 col-md-4">

                @include('task.partials.task-updates')
                @include('task.partials.task-history')


            </div>
        </div>

        @hasRole(['client'])
        <div id="view-task">
            @if(!empty($task->speakingTest(true)) && !empty($task->speakingTest(true)->report))
            <div class="panel col-sm-6 no-padding" id="task-speaking-skills">
                <div class="panel-body">
                    @include('tests.results.partials.report-speaking')
                </div>
            </div>
            @endif
            @if(!empty($task->writingTest(true)) && !empty($task->writingTest(true)->report))
            <div class="panel col-sm-6 no-padding" id="task-writing-skills">
                <div class="panel-body">
                    @include('tests.results.partials.report-writing')
                </div>
            </div>
                @endif
        </div>
        <div class="clearfix"></div>
        @endHasRole
    </div>
@endsection

@section('footer')
    @include('task.partials.task-scripts')
@endsection
