@extends('emails.layout')

@section('content')


    <div class="mail-text">

        {!! $body_en !!}

    </div>
    @include('emails.partials.button', ['text' => 'Task page', 'href' => $link, 'style' => 'background: green;'])
    @include('emails.partials.button', ['text' => 'Refuse task', 'href' => $linkRefuse])
    @if(isset($calendar_links) && count($calendar_links))
        @include('emails.partials.calendar-links', ['calendar_links' => $calendar_links, 'prefix' => "Add to", 'suffix' => "calendar"])
    @endif
    <hr style="border-color: rgb(233, 233, 233); opacity: 0.3;">

    <div class="mail-text">
        {!! $body_ro !!}
    </div>
    @include('emails.partials.button', ['text' => 'Pagina task', 'href' => $link, 'style' => 'background: green;'])
    @include('emails.partials.button', ['text' => 'Refuza task', 'href' => $linkRefuse])
    @if(isset($calendar_links) && count($calendar_links))
        @include('emails.partials.calendar-links', ['calendar_links' => $calendar_links, 'prefix' => "Adauga in", 'suffix' => "calendar"])
    @endif
@endsection