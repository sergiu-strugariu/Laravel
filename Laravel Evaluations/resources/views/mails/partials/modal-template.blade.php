<div class="panel mail-template-modal">
    <div class="panel-heading">
        <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
        <h1>Edit template</h1>
    </div>

    <div class="panel-body">

        <form class="form-horizontal" action="{{ url('/admin/mails/'.$mailTemplate->id) }}" method="POST">
            {{ csrf_field() }}

            <div class="form-group">
                <div class="col-sm-12">
                    <label for="name">Name</label>
                    {{ Form::input('text', 'name', $mailTemplate->name, ['class' => 'form-control', 'required']) }}
                </div>
            </div>

            <div class="form-group @if($mailTemplate->slug == SMS_TEST_REMIND) hidden @endif">
                <div class="col-sm-12">
                    <label for="name">Subject</label>
                    {{ Form::input('text', 'subject', $mailTemplate->subject, ['class' => 'form-control', 'required']) }}
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <div class="col-xs-12">
                        <label for="name">Variables</label>
                    </div>
                    <div class="col-sm-6">
                        <ul class="template-legend">
                            @foreach( config('mail.vars_by_type.'.$mailTemplate->slug) as $variable => $description )

                                @if( $loop->index > 1 && $loop->index % 4 == 0)
                                    </div>
                                    <div class="col-sm-6">
                                        <ul class="template-legend">
                                @endif

                                <li><span>{{ '{'.$variable.'}' }}</span> - {{ $description }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            @if( $mailTemplate->slug == MAIL_TEST_TAKE)
            <div class="form-group">
                <div class="col-sm-12">
                    For speaking you must add <b>#speaking</b> at the beginning or text and <b>speaking#</b> at the end. E.g.: <br><br> <span>#speaking<br>You have a speaking test on {schedule}<br>speaking#</span>
                    <hr />
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-12">
                    For online tests you must add <b>#online</b> at the beginning or text and <b>online#</b> at the end. E.g.: <br><br> <span>#online<br>You have online test<br>online#</span>
                    <hr />
                </div>
            </div>
            @endif

            @if( $mailTemplate->slug == MAIL_TEST_TAKE_MULTIPLE)
                <div class="form-group">
                    <div class="col-sm-12">
                        For every type of test there is a <b>#</b> marker, 
                        <hr />
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        For online tests you must add <b>#online</b> at the beginning or text and <b>online#</b> at the end. E.g.: <br><br> <span>#online<br>You have online test<br>online#</span>
                        <hr />
                    </div>
                </div>
            @endif

            <div class="form-group">
                <div class="col-sm-12">
                    <label for="name">Body @if($mailTemplate->slug != SMS_TEST_REMIND) En @endif</label>
                    @if($mailTemplate->slug == SMS_TEST_REMIND)
                        {{ Form::textarea('body_en', $mailTemplate->body_en, ['class' => 'form-control', 'required']) }}
                    @else
                        {{ Form::textarea('body_en', $mailTemplate->body_en, ['class' => 'form-control wysihtml5-editor', 'required']) }}
                    @endif
                </div>
            </div>

            <div class="form-group @if($mailTemplate->slug == SMS_TEST_REMIND) hidden @endif">
                <div class="col-sm-12">
                    <label for="name">Body RO</label>
                    {{ Form::textarea('body_ro', $mailTemplate->body_ro, ['class' => 'form-control wysihtml5-editor', 'required']) }}
                </div>
            </div>


            <div class="form-group">
                <div class="col-sm-6">
                    <input type="submit" data-id="{{ $mailTemplate->id }}" class="btn btn-primary edit_template_button" value="Save Template"/>
                </div>
            </div>
        </form>
    </div>
</div>