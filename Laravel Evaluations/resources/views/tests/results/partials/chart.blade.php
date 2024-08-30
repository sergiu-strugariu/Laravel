@if($task->completedTests()->count())
    <div class="col-sm-12 no-padding" style="min-height: 220px;">
        <div class="col-sm-12">
            <div class="panel container-chart" id="task-tests-details">
                {{--<div class="panel-heading">--}}
                    {{--Tests details--}}
                {{--</div>--}}
                <div class="panel-body">
                    <canvas id="task-tests-chart" width="400" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
@endif