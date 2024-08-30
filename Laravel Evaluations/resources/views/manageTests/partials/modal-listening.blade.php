<div class="panel">
    <div class="panel-heading">
        <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
        <h1>
            @if( $question ) Edit @else Add new @endif {{ $testType->paperTypes->name }} Question for {{ $language->name }}
        </h1>
    </div>

    <div class="panel-body">

        <form class="form-horizontal" enctype="multipart/form-data" action="{{ url('/admin/questions/createListeningQuestion') }}" method="POST" @if( $question ) id="edit_listening_question_form" @else id="add_listening_question_form" @endif>
            {{ csrf_field() }}

            @if( $question )
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="name">Play file <small>({{$question->audio_file_path}})</small></label>

                        <audio id="plyr-audio">
                            <source src=" {{url('audio/'.$question->id.'-'.$question->audio_file_path)}}"
                                    type="audio/{{$fileExtension}}">
                        </audio>
                    </div>
                </div>
            @endif

            <div class="form-group">
                <div class="col-sm-12">
                    <label for="name">Upload audio file</label>
                    @if( $question )
                        {{ Form::file('file', []) }}
                    @else
                        {{ Form::file('file', [ 'required' ]) }}
                    @endif

                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <label for="name">Body</label>
                    {{ Form::textarea('body', @$question->body, ['class' => 'form-control', 'required' => 'required']) }}
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <label for="name">Description</label>
                    {{ Form::input('text', 'description', @$question->description, ['class' => 'form-control']) }}
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-3">
                    <label for="name">Level</label>
                    {{ Form::select('question_level_id', $levels, ( $question ? $question->question_level_id : '' ), ['class' => 'form-control', 'required']) }}
                </div>
                <div class="col-sm-3">
                    <label for="name">Q Type</label>
                    {{ Form::select('q_type', $qTypes, ( $question ? $question->q_type : '' ), ['class' => 'form-control', 'required']) }}
                </div>
                <div class="col-sm-3">
                    <label for="name">Time limit
                        <small>(minutes)</small>
                    </label>
                    {{ Form::input('number', 'minutes', isset($question) ? $question->minutes : 0, ['class' => 'form-control', 'required', 'min' => 0]) }}
                </div>
                <div class="col-sm-3">
                    <label for="name">Time limit
                        <small>(seconds)</small>
                    </label>
                    {{ Form::input('number', 'seconds', isset($question) ? $question->seconds : 0, ['class' => 'form-control', 'required', 'min' => 0, 'max' => 60]) }}
                </div>
            </div>

            @include('manageTests.partials.block-choices')

            <div class="form-group">
                <div class="col-sm-6">
                    {{ Form::input('hidden', 'language_paper_type_id', $testType->id) }}
                    @if( $question )
                        <input type="submit" data-id="{{$question->id}}" class="btn btn-primary edit_listening_question_button" value="Save Question"/>
                    @else
                        <input type="submit" class="btn btn-primary add_listening_question_button" value="Add Question"/>
                    @endif

                </div>
            </div>
        </form>
    </div>
</div>

@if( $question )
<script>
    plyr.setup({
        clickToPlay: true,
        showPosterOnEnd: true,
        controls: ['play', 'progress']
    });
</script>
@endif