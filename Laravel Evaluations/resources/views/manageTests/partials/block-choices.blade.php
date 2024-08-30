@if($hasChoices)
    <label>Choices</label>

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>Answer</th>
            <th>Correct</th>
            <th class="text-center">Status</th>
            <th class="text-center">Delete</th>
        </tr>
        </thead>
        <tbody>
        @if ($question)
            @foreach($question->questionChoices as $choice)
                <tr>
                    <td>{{ Form::input('text', 'answer['.$choice->id.']', $choice->answer, ['class' => 'form-control']) }}</td>
                    <td>{{ Form::select('correct['.$choice->id.']', [ 0 => 'Incorrect', 1 => 'Correct' ], $choice->correct, ['class' => 'form-control']) }}</td>
                    <td class="text-center actions">
                        <label class="action-button choice-status">
                            <input type="checkbox" name="status[{{$choice->id}}]" value="1" @if(is_null($choice->deleted_at)) checked @endif />
                            <span class="slider round"></span>
                        </label>
                    </td>
                    <td class="text-center"><button type="button" class="btn btn-danger btn-xs btn-delete-choice">Delete</button></td>
                </tr>
            @endforeach
        @endif
        <tr class="end-choices">
            <td colspan="4">
                <label>Add choice</label>
            </td>
        </tr>
        <tr>
            <td>{{ Form::input('text', 'answer_template', '', ['class' => 'form-control answer_template ignore-validation', 'required']) }}</td>
            <td>{{ Form::select('correct_template', [ 0 => 'Incorrect', 1 => 'Correct' ], 0, ['class' => 'form-control']) }}</td>
            <td class="text-center actions"><label class="action-button choice-status"> <input type="checkbox" name="status_template" checked value="1"> <span class="slider round"></span> </label></td>
            <td class="text-center"><button type="button" class="btn btn-primary btn-xs btn-add-choice">Add</button></td>
        </tr>
        </tbody>
    </table>

@endif