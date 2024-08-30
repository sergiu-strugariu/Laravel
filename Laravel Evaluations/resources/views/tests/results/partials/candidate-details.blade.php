<div class="panel" id="task-details">
    <div class="col-xs-12" style="padding: 20px;">
        <img src="{{asset('assets/img/user-logo.jpg')}}" alt="">
    </div>
    <div class="col-xs-12" style="padding-right: 0!important">
        <div class="panel-body" style="padding-right: 0">

            <div class="form-group">
                <h2 style="margin-top: 0;">{{$task->name}}</h2>
            </div>

            <div class="form-group">
                <div class="show-edit">
                    <div class="edit-html">
                        <strong>Language:</strong> <span id="replace">{{ $task->language->name }}</span>
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="form-group">
                <div class="show-edit">
                    <div class="edit-html">
                        <strong>Phone:</strong> <span id="replace">{{ $task->phone }}</span>
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="form-group show-edit">
                <div class="edit-html">
                    <strong>Email:</strong> <span id="replace">{{ $task->email }}</span>
                </div>
            </div>

            <div class="clearfix"></div>

            {{--<div class="form-group">--}}
                {{--@if(!empty($task->assessor) && !Auth::user()->hasRole('client') && $showAssessorInPDF === true )--}}
                    {{--<strong>Assessor:</strong> {{$task->assessor->full_name }}--}}

                {{--@endif--}}
            {{--</div>--}}

        </div>
    </div>

</div>
