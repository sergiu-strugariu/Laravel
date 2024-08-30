
<aside id="edit_language" class="control-sidebar control-sidebar-edit add-new-project-modal">
    <div class="panel">
        <div class="panel-heading">
            <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
            <h1>
                Edit language
            </h1>
        </div>

        <div class="panel-body">

            <form class="form-horizontal" method="POST" id="edit_language_form">
                {{ csrf_field() }}

                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="edit_name">Name</label>
                        <input type="text" class="form-control" name='name'
                               id="edit_name"
                               value="" placeholder="Name" required>
                    </div>
                </div>
                <input type="hidden" name="languageId">
                <div class="form-group">
                    <div class="col-sm-6">
                        <input type="submit" class="btn btn-primary edit_language_button" value="Edit language"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
</aside>