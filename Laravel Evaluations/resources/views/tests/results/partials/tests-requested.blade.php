<div class="panel-body">
    <strong>Tests requested: </strong> {{ implode(', ', $task->papers->pluck('type.name')->toArray()) }}
</div>