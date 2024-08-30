@extends('layouts.app')

@section('content')
    <div class="header-content">
        <div class="tag-name">Email Notifications</div>

    </div>
    <div class="grid">
        @foreach($emailTemplates as $template)
            <div class="grid-item">
                <div>
                    <a class="edit_mail_template_button" data-id="{{$template->id}}" href="/admin/mails/{{$template->id}}">{{ $loop->index + 1  }}. {{$template->name}}</a>
                </div>
            </div>
        @endforeach
    </div>

    <aside id="edit_template" class="control-sidebar control-sidebar-edit edit-template-modal"></aside>

@endsection
@section('footer')
    <script src="{{ url('js/bootstrap3-wysihtml5.all.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.grid').masonry({
                itemSelector: '.grid-item',
                columnWidth: 258
            });
        });
    </script>
@endsection