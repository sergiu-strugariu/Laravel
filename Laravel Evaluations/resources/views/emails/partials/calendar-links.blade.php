<p>
    {{ $prefix }} {{ $suffix }}
    <br>
    @foreach($calendar_links as $event)
        @if ($event['serviceName'] === "webOutlook")
            <a clicktracking=off href="{{$event['serviceLink']}}">Outlook</a>
        @else
            <a clicktracking=off href="{{$event['serviceLink']}}">{{ucfirst($event['serviceName'])}}</a> -
        @endif
    @endforeach
</p>