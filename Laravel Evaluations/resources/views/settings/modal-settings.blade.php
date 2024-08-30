<div class="panel">
    <div class="panel-heading">
        <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
        <h1>Edit setting</h1>
    </div>

    <div class="panel-body">

        <form class="form-horizontal" enctype="multipart/form-data" id='setting_update_form' action="{{ url('/admin/setting/updateSetting/'.$setting->id) }}"
              method="POST">
            {{ csrf_field() }}

            <div class="form-group">
                <div class="col-sm-12">
                    <label for="name">Description</label>
                    {{ Form::input('text', 'description', @$setting->description, ['class' => 'form-control', 'required' => 'required']) }}
                </div>
            </div>

            @if( $setting->key == 'audio_file_path' )
                <div class="form-group @if(!$setting->value) hidden @endif">
                    <div class="col-sm-12">
                        <label for="name">Play file
                            <small>({{ucfirst ($setting->value) }})</small>
                        </label>

                        <audio id="plyr-audio">
                            <source src=" {{url('audio/'.$setting->value)}}"
                                    type="audio/{{$fileExtension}}">
                        </audio>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="name">Upload audio file</label>
                        {{ Form::file('value', []) }}
                    </div>
                </div>

            @else
                @if($setting->key == 'instructions' ||  $setting->key == 'audio_instruction')
                    <div class="form-group">
                        <div class="col-sm-12">
                            <div class="col-xs-12">
                                <label for="name">Variables</label>
                            </div>
                            <div class="col-sm-12">
                                <ul class="template-legend">

                                    <li><span>{tests}</span> - Tests list</li>
                                    <li><span>{name}</span> - Candidate name</li>

                                </ul></div>

                        </div>
                    </div>
                @endif

                @if($setting->key == 'welcome' ||  $setting->key == 'welcome_audio')
                    <div class="form-group">
                        <div class="col-sm-12">
                            <div class="col-xs-12">
                                <label for="name">Variables</label>
                            </div>
                            <div class="col-sm-12">
                                <ul class="template-legend">

                                    <li><span>{name}</span> - Candidate name</li>

                                </ul></div>

                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="name">{{ucfirst ($setting->key) }}</label>
                        {{ Form::textarea('value', @$setting->value, ['class' => 'form-control wysihtml5-editor', 'required' => 'required']) }}
                    </div>
                </div>

            @endif

            <div class="form-group">
                <div class="col-sm-6">
                    <input type="submit" class="btn btn-primary edit_setting_button_submit"
                           value="Save Setting"/>
                </div>
            </div>
        </form>
    </div>
</div>

@if( $setting->key == 'audio_file_path' )
    <script>
        plyr.setup({
            clickToPlay: true,
            showPosterOnEnd: true,
            controls: ['play', 'progress', 'volume']
        });
    </script>
@endif