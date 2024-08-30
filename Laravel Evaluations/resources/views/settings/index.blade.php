@extends('layouts.app')

@section('content')

    <div class="box box-success">

        <div class="box">
            <div class="box-header css-header">
                <div class="row">
                    <div class="col-xs-12"><h3 class="box-page_heading">Settings List</h3></div>
                </div>
            </div>

            <!-- /.box-header -->
            <div class="box-body">

                <table id="settings" class="table responsive nowrap ui celled"
                       data-model="setting">
                    <thead>
                    <tr>
                        <th>Key</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>

    <aside id="edit_setting" class="control-sidebar control-sidebar-edit add-new-project-modal"></aside>
@endsection
@section('footer')
    <script src="{{ url('js/bootstrap3-wysihtml5.all.min.js') }}"></script>
    <script>
        $(document).ready(function () {

            generateSettingsTable();
        })
        ;
    </script>
@endsection