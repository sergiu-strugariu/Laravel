@hasRole('assessor')
<button class="add-client add-button" id="email_lang_manager">
    Email Eucom Manager
</button>
@endHasRole
@hasRole(['client', 'css', 'recruiter'])
<button class="add-client add-button" id="email_lang_manager">
    Email Eucom Manager
</button>
@endHasRole

{{--
@hasRole(['client', 'administrator', 'master'])
@if ($task->remainingOnlineTests()->count() && $task->status->name != 'Canceled')
    <button class="add-client add-button" id="task-test-here">
        Test candidate here
    </button>
@endif
@endHasRole
--}}

@hasRole(['administrator', 'master', 'css', 'recruiter'])
@if ($task->papers->count() == 1 && $task->papers[0]->paper_type_id == TEST_SPEAKING )
    {{--hide btn--}}
@else
    <button class="btn btn-task" id="task-reset">Reset online tests</button>
@endif
@endHasRole

@hasRole(['administrator', 'master'])
@if( strtotime($task->link_expires_at) < time() )
    <button class="btn btn-task" id="task-link-reset">Reset test link</button>
@endif
@endHasRole

@hasRole(['assessor', 'administrator', 'master', 'client', 'css', 'recruiter'])
@if($task->remainingOnlineTests()->count())
    <button class="btn btn-task" id="task-resend">Resend online test invitation</button>
    @endIf
    @endHasRole

    <a href="{{ "/project/$task->project_id/tasks" }}">
        <button class="btn btn-task">View all Project tasks</button>
    </a>