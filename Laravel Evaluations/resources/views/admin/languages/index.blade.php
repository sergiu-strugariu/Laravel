@extends('layouts.app')

@section('content')
    <div class="languages-page">
        <div class="header-content">
            <div class="tag-name">
                Languages
            </div>
            <button class="add-client add_language">
                <div class="ion-plus">
                    Add new language
                </div>
            </button>
        </div>
        <div class="box languages-box">
            <div class="box-header">
                <h3 class="box-title"></h3>
            </div>
            <div class="box-body">
                <div class="languages-table">
                    <table class="table responsive nowrap ui celled">
                        <thead>
                        <tr>
                            <th>Language Name</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @include("admin.languages.partials.add-language-form")
        @include("admin.languages.partials.edit-language-form")
    </div>


@endsection

@section('footer')
@endsection