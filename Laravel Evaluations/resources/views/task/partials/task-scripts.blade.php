<script src="{{ url('js/chart.min.js') }}"></script>
<script>
    var task = {!! $task !!};
    var taskStatus = {!! $task->status !!};
    var taskStatusesColor = {!! $taskStatusesColor !!};
    var isClient = '{!! Auth::user()->hasRole('client') !!}';
    var isAssessor = '{!! Auth::user()->hasRole('assessor') !!}';
    var isOnlyAssessor = '{{ Auth::user()->hasOnlyRole('assessor') ? 1 : 0 }}';
    var papers = {!! $task->completedTests() !!};
    var speakingReport = @if($speakingReport) {!! json_encode($speakingReport) !!} @else null @endif ;
    var writingReport = @if($writingReport) {!! json_encode($writingReport) !!} @else null @endif ;
    var TEST_LANGUAGE_USE_NEW = {{TEST_LANGUAGE_USE_NEW}},
            TEST_LANGUAGE_USE = {{TEST_LANGUAGE_USE}},
            TEST_SPEAKING = {{TEST_SPEAKING}},
            TEST_WRITING = {{TEST_WRITING}},
            TEST_LISTENING = {{TEST_LISTENING}},
            TEST_READING = {{TEST_READING}};
</script>
<script src="{{ asset('js/scripts-task-page.min.js') }}"></script>
<script>
    $(document).ready(function(){
        @if($speakingReport && isset($speakingReport['assessments']['general_descriptors']))
            $('a[href="#speaking-general_descriptors-{{$speakingReport['assessments']['general_descriptors']}}"]').trigger('click');
        @endif
         @if($writingReport && isset($writingReport['assessments']['general_descriptors']))
            $('a[href="#writing-general_descriptors-{{$writingReport['assessments']['general_descriptors']}}"]').trigger('click');
        @endif
        if (speakingReport) {
            sliderChanged = true;
            if(speaking_slider_ability_aquired)
                speaking_slider_ability_aquired.setValue(speakingReport.assessments.speaking['ability-aquired']);
            if (speakingReport.assessments.speaking['ability-aquired'] == 100) {
                $('.speaking-slider.slider-2').removeClass('hidden');
            }
            if(speaking_slider_ability_next)
                speaking_slider_ability_next.setValue(speakingReport.assessments.speaking['ability-next']);
        }
        if (writingReport) {
            sliderChanged = true;
            if(writing_slider_ability_aquired)
                writing_slider_ability_aquired.setValue(writingReport.assessments.writing['ability-aquired']);
            if (writingReport.assessments.writing['ability-aquired'] == 100) {
                $('.writing-slider.slider-2').removeClass('hidden');
            }
            if(writing_slider_ability_next)
                writing_slider_ability_next.setValue(writingReport.assessments.writing['ability-next']);
        }
    });
</script>