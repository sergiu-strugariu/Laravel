
<aside id="add_new_language" class="control-sidebar control-sidebar-edit add-new-project-modal">
    <div class="panel">
        <div class="panel-heading">
            <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
            <h1>
                Add new language
            </h1>
        </div>

        <div class="panel-body">

            <form class="form-horizontal" method="POST" id="add_new_language_form">
                {{ csrf_field() }}

                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name='name'
                               id="name"
                               value="" placeholder="Name" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-6">
                        <input type="submit" class="btn btn-primary add_new_language_button" value="Add language"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
</aside>