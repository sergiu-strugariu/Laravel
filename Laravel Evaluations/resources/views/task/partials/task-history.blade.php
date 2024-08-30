@if(auth()->user()->hasRole('client'))
    @php return; @endphp
@endif

<div class="panel" id="task-comments">
    <div class="panel-heading">
        Task History
    </div>
    <div class="panel-body" id="task-history">
        <?php
            $canSeeCustom = Auth::user()->hasRole(['administrator', 'master', 'assessor']);
            $history = $task->taskHistory();
        ?>
        @if($history->count())
            {{-- Clear the custom messages--}}
            @foreach($history as $key => $log)
                @if (!$canSeeCustom && strpos($log->description, "__custom__") === 0)
                    <?php $history = $history->forget($key) ?>
                @endif
            @endforeach
            @foreach($history as $key => $log)
                {{--if current user is client and log entry is added by assessor--}}
                @if( $log->user && Auth::user()->hasRole(['client', 'tds']) && $log->user->hasRole('assessor') )
                    @continue
                @endif

                {{--hide assessor changes or refuses to current assessor--}}
                @if ( Auth::user()->hasRole('assessor') &&
                        (strstr($log->description, 'assessor was changed') !== false
                        || strstr($log->description, 'refused this task') !== false) )
                    @continue;
                @endif

                @if( Auth::user()->hasRole(['client', 'tds']) && (substr($log->description,0 , 13) != 'Task assessor' ))
                    <div class="row {{ $key >= 5 ? 'hidden' : '' }}">
                        <div class="col-xs-12 relative pr-25">
                            {{ str_replace("__custom__", "", $log->description) }}
                            <span>
                                            {{ date('d M Y', strtotime($log->created_at)) . ' at ' .  date('H:i', strtotime($log->created_at)) }}
                                by {{ $log->user == null ? 'System' : $log->user->full_name }}
                                            </span>
                            @hasRole(['master','administrator'])
                            <i class="fa fa-remove task-delete-log" title="Remove" data-log-id="{{$log->id}}"></i>
                            @endHasRole
                        </div>
                    </div>
                @elseif( !Auth::user()->hasRole(['client', 'tds']))
                    <div class="row {{ $key >= 5 ? 'hidden' : '' }}">
                        <div class="col-xs-12 relative pr-25">
                            {{ str_replace("__custom__", "", $log->description) }}
                            <span>
                                            {{ date('d M Y', strtotime($log->created_at)) . ' at ' .  date('H:i', strtotime($log->created_at)) }}
                                by {{ $log->user == null ? 'System' : $log->user->full_name }}
                                            </span>
                            @hasRole(['master','administrator'])
                            <i class="fa fa-remove task-delete-log" title="Remove" data-log-id="{{$log->id}}"></i>
                            @endHasRole
                        </div>
                    </div>
                @endif
            @endforeach
            @if($history->count() > 5)
                <div class="row text-center task-show-more">Show more...</div>
            @endif
        @else
            <div class='row'>
                <div class='col-xs-12 text-center'>No logs found.</div>
            </div>
        @endif
    </div>

</div>