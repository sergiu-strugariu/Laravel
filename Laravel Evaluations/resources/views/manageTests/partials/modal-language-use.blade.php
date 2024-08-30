<div class="panel">
    <div class="panel-heading">
        <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
        <h1>
            @if( $question ) Edit @else Add new @endif {{ $testType->paperTypes->name }} Question for {{ $language->name }}
        </h1>
    </div>

    <div class="panel-body">

        <form class="form-horizontal" action="{{ url('/admin/questions/createLanguageUseQuestion') }}" method="POST" @if( $question ) id="edit_question_form" @else id="add_new_question_form" @endif>
            {{ csrf_field() }}

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

            @include('manageTests.partials.block-choices')

            <div class="form-group">
                <div class="col-sm-6">
                    {{ Form::input('hidden', 'language_paper_type_id', $testType->id) }}
                    @if( $question )
                        <input type="submit" data-id="{{$question->id}}" class="btn btn-primary edit_question_button" value="Save Question"/>
                    @else
                        <input type="submit" class="btn btn-primary add_new_question_button" value="Add Question"/>
                    @endif

                </div>
            </div>
        </form>
    </div>
</div>