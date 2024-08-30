<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Email</th>
    </tr>
    </thead>
    <tbody>
    @if(empty($groupUsers->toArray()))
        <tr><td colspan="3" class="text-center">No user in this group.</td></tr>
    @else
        @foreach($groupUsers as $groupUser)
            <tr>
                <td>
                    {{ $groupUser->user()->first()->first_name }}
                </td>
                <td>
                    {{ $groupUser->user()->first()->last_name }}
                </td>
                <td>
                    {{ $groupUser->user()->first()->email }}
                </td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>