<div class="panel">
    <div class="panel-heading">
        <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
        <h1>

            @if(isset($choice))
                 Edit answer
            @else
                Add answer
            @endif
        </h1>
    </div>

    <div class="panel-body">

        <form class="form-horizontal" action="{{ url('/admin/questions/createQuestionChoice') }}" method="POST" @if( $choice ) id="edit_question_form" @else id="add_new_question_form" @endif>
            {{ csrf_field() }}

            <div class="form-group">
                <div class="col-sm-12">
                    <label for="name">Answer</label>
                    {{ Form::input('text', 'answer', @$choice->answer, ['class' => 'form-control']) }}
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <label for="name">Correct</label>
                    {{ Form::select('correct', [ 0 => 'Incorrect', 1 => 'Correct' ], @$choice->correct, ['class' => 'form-control']) }}
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-6">
                    {{ Form::input('hidden', 'question_id', isset($question) ? $question->id : $choice->question_id ) }}
                    @if( $choice )
                        <input type="submit" data-id="{{ $choice->id }}" class="btn btn-primary edit_choice_button" value="Save Answer"/>
                    @else
                        <input type="submit" class="btn btn-primary add_new_question_button" value="Add Answer"/>
                    @endif

                </div>
            </div>
        </form>
    </div>
</div>