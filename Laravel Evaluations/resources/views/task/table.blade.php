<table class="table table-condensed">
    <thead>
    <tr>
        <th class="text-center">No.</th>
        <th class="text-center">Question Level</th>
        <th class="text-center">Code</th>
        <th class="text-center">Difficulty</th>
        <th class="text-center">Answer</th>
        <th class="text-center">Ability</th>
        <th class="text-center">Level</th>
    </tr>
    </thead>
    <tbody>

        @foreach($questions as $question)

            <tr>
                <td class="text-center">{{$loop->index + 1}}</td>
                <td class="text-center">{{$question->level->name}}</td>
                <td class="text-center">{{$question->code}}</td>
                <td class="text-center">{{$algorithm['all_difficulties'][$loop->index + 1] ?? ''}}</td>
                <td class="text-center">{{$question->isCorrect ? '1' : '0' }}</td>
                <td class="text-center">{{$algorithm['all_abilities'][$loop->index + 1] ?? ''}}</td>
                <td class="text-center">{{$abilities[$algorithm['all_levels'][$loop->index + 1]] ?? ''}}</td>

            </tr>

        @endforeach

    </tbody>
</table>