<div class="panel">
    <div class="panel-heading">
        <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
        <h1>
            @if( $question ) Edit @else Add new @endif {{ $testType->paperTypes->name }} Question for {{ $language->name }}
        </h1>
    </div>

    <div class="panel-body">

        <form class="form-horizontal" action="{{ url('/admin/questions/createLanguageQuestion') }}" method="POST" @if( $question ) id="edit_question_form" @else id="add_new_question_form" @endif>
            {{ csrf_field() }}

            <div class="form-group">
                <div class="col-sm-12">
                    <label for="name">Type</label>
                    {{ Form::select('language_use_type', [ TEST_LU_READING => 'Multiple choice', TEST_LU_ARRANGE => 'Arrange words', TEST_LU_FILLGAPS => 'Fill in the gap' ], @$question->language_use_type, ['class' => 'form-control lang_use_type_select', 'required' => 'required', 'placeholder' => 'Type']) }}
                </div>
            </div>

            <div class="lu_tabs tab_lu_1 @if( $question && $question->language_use_type == TEST_LU_READING ) @else hidden @endif ">

                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="name">Body</label>
                        <p><small>Please enter the question's body placing the _ (underscore) where you want the texts input to be. Eg. What is your ___ ?</small></p>
                        {{ Form::textarea('body_reading', @$question->body, ['class' => 'form-control', 'required' => 'required']) }}
                    </div>
                </div>

            </div>

            <div class="lu_tabs tab_lu_2 @if( $question && $question->language_use_type == TEST_LU_ARRANGE ) @else hidden @endif">

                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="name">Correct answer</label>
                        <p><small>Please enter the words separated by commas + punctuation. Eg. Many, work, office, people, an, in,.</small></p>
                        @if( $question && $question->language_use_type == TEST_LU_ARRANGE )
                            {{ Form::input('text', 'body_arrange', implode(',', json_decode($question->body, true)), ['class' => 'form-control tagsinput-edit', 'required' => 'required']) }}
                        @else
                            {{ Form::input('text', 'body_arrange', '', ['class' => 'form-control tagsinput', 'required' => 'required']) }}
                        @endif
                        {{ Form::input('hidden', 'body_arrange_json', @$question->body) }}
                    </div>
                </div>
                <div class="form-group incorrect-pattern-container @if(!@$question->body_incorrect)hidden @endif">
                    <div class="col-sm-12">
                        <label>Incorrect Pattern</label>
                        <p><small>The incorrect field is used to specify the specific pattern that the user sees</small></p>
                        <ul class="incorrect-pattern">
                            @if(@$question->body_incorrect)
                                <?php $incorrect = json_decode($question->body_incorrect)?>
                                @foreach($incorrect as $element)
                                    <li draggable="true">{{$element}}</li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                    {{-- The incorrect field for this arrange question --}}
                    {{ Form::input('hidden', 'body_incorrect', @$question->body_incorrect) }}
                </div>
            </div>

            <div class="lu_tabs tab_lu_3 @if( $question && $question->language_use_type == TEST_LU_FILLGAPS ) @else hidden @endif">

                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="name">Body</label>
                        <p><small>Please enter the question's body placing the <b>_</b> (underscore) where you want the texts input to be. Eg. <i>What is your ___ ?</i></small></p>
                        {{ Form::input('text', 'body_gaps', @$question->body, ['class' => 'form-control', 'required' => 'required']) }}
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="name">Correct answer</label>
                        {{ Form::input('text', 'lu_gap_answer', @$question->lu_gap_answer, ['class' => 'form-control', 'required' => 'required']) }}
                    </div>
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
                    <label for="name">Time limit <small>(minutes)</small></label>
                    {{ Form::input('number', 'minutes', isset($question) ? $question->minutes : 0, ['class' => 'form-control', 'required', 'min' => 0]) }}
                </div>
                <div class="col-sm-3">
                    <label for="name">Time limit <small>(seconds)</small></label>
                    {{ Form::input('number', 'seconds', isset($question) ? $question->seconds : 0, ['class' => 'form-control', 'required', 'min' => 0, 'max' => 60]) }}
                </div>
            </div>

            <div class="lu_tabs tab_lu_1 @if( $question && $question->language_use_type == TEST_LU_READING ) @else hidden @endif">
                @include('manageTests.partials.block-choices')
            </div>

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

@if( $question && $question->language_use_type == TEST_LU_ARRANGE )
    <script>
        var tagsinput = $('.tagsinput-edit');
        tagsinput.tagsinput({
            allowDuplicates: true
        });
        tagsinput.on('itemAdded', updateLUArrangeQuestionOnEdit);
        tagsinput.on('itemRemoved', updateLUArrangeQuestionOnEdit);

        function updateLUArrangeQuestionOnEdit(doNotRedraw) {
            var words = tagsinput.tagsinput('items');
            $('input[name="body_arrange_json"]').val(JSON.stringify(words));

            // Handle the incorrect pattern field
            var incorrectPatternContainer = $("#edit_question_form .incorrect-pattern-container");
            var patternBlocks = incorrectPatternContainer.find(".incorrect-pattern");
            var incorrectPatternInput = $("#edit_question_form input[name=body_incorrect]");


            if (words && words.length > 0) {
                var shuffledWords = jsArrayShuffle([].concat(words));

                incorrectPatternContainer.removeClass("hidden");
                patternBlocks.html("");
                shuffledWords.forEach(function(item) {
                    var elt = $("<li draggable='true'>"+item+"</li>");
                    elt.appendTo(patternBlocks);
                });
                incorrectPatternInput.val(JSON.stringify(shuffledWords));
            } else {
                incorrectPatternContainer.addClass("hidden");
            }

            sortableLUArrangeQuestionOnEdit(patternBlocks[0]);
        }

        function sortableLUArrangeQuestionOnEdit(elt) {
            var sortable = Sortable.create(elt, {
                sort: true,
                group: {
                    name: 'advanced',
                    pull: true,
                    put: true
                },
                onEnd: function (evt, originalEvent) {
                    var data = [];
                    $(elt).find("li").each(function() {
                        data.push($(this).text());
                    });

                    $("#edit_question_form input[name=body_incorrect]").val(JSON.stringify(data));
                }
            });
        }

        sortableLUArrangeQuestionOnEdit($("#edit_question_form").find(".incorrect-pattern")[0]);
    </script>
@endif