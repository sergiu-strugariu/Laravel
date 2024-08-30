@extends('layouts.app')

@section('content')
    <div class="header-content">
        <div class="tag-name">Language test types</div>

        @canAtLeast(['user.create'])
        <button onclick="location.href='{{url('/admin/settings')}}'" class="add-client">
            Go to instruction page
        </button>
        @endCanAtLeast
    </div>
    <div class="grid test-languages-grid">
        @foreach($languages as $lang)
            <div class="grid-item test-types-grid-item">
                <div class="client-name test-type-lang"
                     data-toggle="collapse" data-target="#toggle-{{$lang->id}}" data-id="{{$lang->id}}">
                    {{$lang->name}} <span class="pull-right"><i class="fa fa-chevron-down expand-list"></i></span>
                </div>
                <div id="toggle-{{$lang->id}}" class="box-body collapse lang-expand" aria-expanded="false">
                    @foreach($lang->language_paper_type as $langPaperType)

                        <div class="project-name" data-model="{{$langPaperType->paperTypes->id}}">
                            <?php $view_tasks_link = "#" ?>
                            @canAtLeast(['project.view_tasks'])
                            <?php $view_tasks_link = "/admin/tests/" . $langPaperType->id ?>
                            @endCanAtLeast

                            @hasRole(['master', 'administrator'])
                            <i class="fa fa-check test-types-check @if( $langPaperType->deleted_at != null) inactive @else active @endif"
                               data-id="{{$langPaperType->id}}" aria-hidden="true"></i>
                            @endHasRole

                            <a href="@if($langPaperType->paper_type_id != TEST_SPEAKING){{ $view_tasks_link }} @else # @endif">
                                {{$langPaperType->paperTypes->name}}
                            </a>
                        </div>

                    @endforeach

                        @if( count($lang->language_paper_type) < TOTAL_TEST_TYPES )
                            @canAtLeast(['project.create'])
                            <button class="add-project add-button add_lang_test_type" data-id="{{$lang->id}}">
                                <div class="ion-plus">
                                    Add test type
                                </div>
                            </button>
                            @endCanAtLeast
                        @endif
                </div>
            </div>
        @endforeach
    </div>

    <script id="language-template" type="text/x-custom-template">
        <div class="grid-item">
            <div class="language-name" data-id="">
            </div>

            @canAtLeast(['project.create'])
            <button class="add-project add-button add_lang_test_type" data-id="">
                <div class="ion-plus">
                    Add test type
                </div>
            </button>
            @endCanAtLeast
        </div>
    </script>

    <script id="test-type-template" type="text/x-custom-template">
        <div class="project-name" data-model="">
            @hasRole(['master', 'administrator'])
            <i class="fa fa-check test-types-check inactive" data-id="" aria-hidden="true"></i>
            @endHasRole
            <a href="#"></a>
        </div>
    </script>

    <aside id="add_new_test_type" class="control-sidebar control-sidebar-edit add-new-project-modal">
        <div class="panel">
            <div class="panel-heading">
                <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
                <h1>
                    Add test type
                </h1>
            </div>

            <div class="panel-body">

                <form class="form-horizontal" method="POST" id="add_new_test_type_form">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <div class="col-sm-12">
                            <label for="name">Test type</label>
                            {{ Form::select('paper_type_id', $paper_types, null, ['class' => 'form-control', 'id' => 'paper_type_id']) }}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-6">
                            <input type="hidden" name="language_id" id="language_id" value=""/>
                            <input type="submit" class="btn btn-primary add_new_test_type_button"
                                   value="Add test type"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </aside>

@endsection
@section('footer')
    <script>
        $(document).ready(function () {
            $('.grid').masonry({
                itemSelector: '.grid-item',
                columnWidth: 258
            });
        });
    </script>
@endsection