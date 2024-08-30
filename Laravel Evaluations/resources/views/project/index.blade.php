@extends('layouts.app')

@section('content')
    <div class="header-content">
        <div class="tag-name">Projects</div>
        <div class="language-audit">
            <select class="form-control" id="project_type_select"
                    name="project_type">
                <option value="0">All</option>
                @foreach($projectTypes as $projectType)
                    <option value="{{$projectType->id}}">{{$projectType->name}}</option>
                @endforeach

            </select>
        </div>
        @canAtLeast(['user.create', 'client.create'])
        <button class="add-client">
            <div class="ion-plus">
                Add new Client
            </div>
        </button>
        @endCanAtLeast
    </div>
    <div class="grid">
        @foreach($clients as $client)
            <div class="grid-item">
                <div class="client-name" data-id="{{$client->id}}">
                    <span class="span_client_name" id="span-client-name-{{$client->id}}">{{$client->name}}</span>
                    @canAtLeast(['project.create'])<span data-id="{{$client->id}}" class="ion-edit edit-client"></span>@endCanAtLeast
                </div>
                @foreach($client->projects as $project)
                    @if(
                            $project->user_id == Auth()->user()->id ||
                            Auth()->user()->hasRole(['master','administrator']) ||
                            in_array(Auth()->user()->id, $project->participants->pluck('user_id')->toArray()) ||
                            in_array(Auth()->user()->id, $project->tasks->pluck('assessor_id')->toArray())
                        )
                        <div class="project-name" data-model="{{$project->project_type_id}}">
                            <?php $view_tasks_link = "#" ?>
                            @canAtLeast(['project.view_tasks'])
                            <?php $view_tasks_link = "/project/" . $project->id . "/tasks" ?>
                            @endCanAtLeast
                            <a href="{{ $view_tasks_link }}">
                                {{$project->name}}
                            </a>
                            <a href="#">
                                @if($project->user_id == Auth()->user()->id || Auth()->user()->hasRole(['master','administrator']))
                                    <span class="ion-close-round delete-project" data-id="{{$project->id}}"></span>
                                    <span class="ion-edit edit-project" data-id="{{$project->id}}"
                                          data_client_id="{{$client->id}}"></span>
                                @endif
                            </a>
                        </div>
                    @endif
                @endforeach
                @canAtLeast(['project.create'])
                <button class="add-project add-button" data-id="{{$client->id}}">
                    <div class="ion-plus">
                        Add new project
                    </div>
                </button>
                @endCanAtLeast
            </div>
        @endforeach
    </div>

    <script id="client-template" type="text/x-custom-template">
        <div class="grid-item">
            <div class="client-name" data-id="">
            </div>

            @canAtLeast(['project.create'])
            <button class="add-project add-button" data-id="">
                <div class="ion-plus">
                    Add new project
                </div>
            </button>
            @endCanAtLeast
        </div>
    </script>

    <script id="project-template" type="text/x-custom-template">
        <div data-model="" class="project-name">
            <a href="" class="pt_a_first"></a>
            <a href="#" class="pt_a_second">
                <span data-id="" class="ion-close-round delete-project"></span>
                <span data-id="" data_client_id="" class="ion-edit edit-project"></span>
            </a>
        </div>
    </script>


    <aside id="add-modal" class="control-sidebar control-sidebar-edit add-new-project-modal">
        <div class="loading">
            <div class="loading-wheel fa-spin"></div>
        </div>
        <div class="panel">
            <div class="panel-heading">
                @canAtLeast(['project.create'])
                <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
                <h1>
                    Add new project
                </h1>
                @endCanAtLeast
            </div>
            <div class="panel-body">

                <form class="form-horizontal" method="POST" id="create_project_form">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <div class="col-sm-12">
                            <label for="project-name">Project name</label>
                            <input type="text" class="form-control" name="name" id="project-name"
                                   value="" placeholder="Add project name" required>
                        </div>
                    </div>

                    <input type="hidden" name="client_id" id="client_id" value="" />
                    <input type="hidden" name="user_id" id="user_id" value="{{Auth()->user()->id}}" />

                    <div class="form-group">
                        <div class="col-sm-12">
                            <label for="project-type">Project type</label>
                            {!! Form::select('project_type_id', $projectTypes->pluck('name', 'id')->toArray(), null, ['id' => 'project-type', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <label for="Participants-name">Project participants</label>
                            <select name="participants_id[]" id="participants_id" class="form-control" multiple required></select>
                        </div>
                    </div>

                    <div class="form-group">

                        <div class="col-sm-12">
                            <label for="bill-client">Bill client</label>
                            <select class="form-control" id="bill-client"
                                    name="default_bill_client">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">

                        <div class="col-sm-12">
                            <label for="pay-assessors">Pay assessors</label>
                            <select class="form-control" id="user_id"
                                    name="default_pay_assessor">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>

                    <div class="distinct-billing-fields hidden">
                        @include("project.partials.billing-form-fields", ['required' => false, 'type' => "project_edit"])
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <h3>Anexa</h3>
                        </div>
                        <div class="col-sm-6">
                            {!! Form::label('billing_contract_annex', 'Numar', array('class' => 'awesome')); !!}
                            {!! Form::text('billing_contract_annex', null, array('id' => 'project_billing_contract_annex', 'class' => 'form-control', "required")); !!}
                        </div>
                        <div class="col-sm-6">
                            {!! Form::label('billing_contract_annex_date', 'Data', array('class' => 'awesome')); !!}
                            {!! Form::text('billing_contract_annex_date', null, array('id' => 'project_billing_contract_annex_date', 'class' => 'has-datepicker form-control', "required")); !!}
                        </div>
                    </div>
                    {!! Form::hidden('billing_distinct', 0, array('id' => 'project_distinct_billing')); !!}
                    <div class="form-group">
                        <div class="col-sm-5">
                            <input type="submit" class="btn btn-primary add_new_project" value="Add new project"
                                   data-id="">
                            </input>
                        </div>
                        @hasRole(['administrator', 'master'])
                        <div class="col-sm-2 custom-price-button-container">
                            <button class="btn btn-primary btn-custom-price" data-prices-for="project" data-type="create">Prices</button>
                            <input type="hidden" name="custom-prices" value="">
                        </div>
                        @endHasRole
                        <div class="col-sm-5 distinct-billing-container">
                            <button class="btn btn-primary distinct-billing">Distinct billing</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </aside>
    <aside id="edit-modal" class="control-sidebar control-sidebar-edit add-new-project-modal">
        <div class="loading">
            <div class="loading-wheel fa-spin"></div>
        </div>
        <div class="panel">
            <div class="panel-heading">
                <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
                <h1>
                    Edit project
                </h1>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" method="POST" id="edit_project_form">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <div class="col-sm-12">
                            <label for="project-name">Project name</label>
                            <input type="text" class="form-control" name='name'
                                   id="project_name_edit"
                                   value="" placeholder="Add project name" required>
                        </div>
                    </div>

                    <input type="hidden" name="id" id="project_id_edit" value=""/>
                    <input type="hidden" name="client_id" id="project_client_id_edit" value=""/>

                    <div class="form-group">
                        <div class="col-sm-12">
                            <label for="project-type">Project type</label>
                            {!! Form::select('project_type_id', $projectTypes->pluck('name', 'id')->toArray(), null, ['id' => 'project_type_edit', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12">
                            <label for="Participants-name">Project participants</label>
                            <select name="participants_id[]" id="participants_id_edit"
                                    class="form-control" multiple
                                    required>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12">
                            <label for="bill-client">Bill client</label>
                            <select class="form-control" id="bill_client_edit"
                                    name="default_bill_client">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12">
                            <label for="pay-assessors">Pay assessors</label>
                            <select class="form-control" id="pay_assessors_id"
                                    name="default_pay_assessor">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="distinct-billing-fields hidden">
                        @include("project.partials.billing-form-fields", ['required' => false, 'type' => "project"])
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <h3>Anexa</h3>
                        </div>
                        <div class="col-sm-6">
                            {!! Form::label('billing_contract_annex', 'Numar', array('class' => 'awesome')); !!}
                            {!! Form::text('billing_contract_annex', null, array('id' => 'project_edit_billing_contract_annex', 'class' => 'form-control', "required")); !!}
                        </div>
                        <div class="col-sm-6">
                            {!! Form::label('billing_contract_annex_date', 'Data', array('class' => 'awesome')); !!}
                            {!! Form::text('billing_contract_annex_date', null, array('id' => 'project_edit_billing_contract_annex_date', 'class' => 'has-datepicker form-control', "required")); !!}
                        </div>
                    </div>
                    {!! Form::hidden('billing_distinct', 0, array('id' => 'project_edit_distinct_billing')); !!}
                    <div class="form-group">
                        <div class="col-sm-5">
                            <input type="submit" class="btn btn-primary edit-project-button"
                                   value="Save Project"
                                   data-id="">
                            </input>
                        </div>

                        <div class="col-sm-2 custom-price-button-container">
                            <button
                                    class="btn btn-primary btn-custom-price"
                                    data-prices-for="project"
                                    data-type="update"
                                    data-project-id="0"
                                    data-client-id="0"
                            >Prices</button>
                            <input type="hidden" name="custom-prices" value="">
                        </div>

                        <div class="col-sm-5 distinct-billing-container">
                            <button class="btn btn-primary distinct-billing">Distinct billing</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </aside>
    <aside id="add_new_client" class="control-sidebar control-sidebar-edit add-new-project-modal">
        <div class="panel">
            <div class="panel-heading">
                <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
                <h1>
                    Add new client
                </h1>
            </div>

            <div class="panel-body">

                <form class="form-horizontal" method="POST" id="add_new_client_form">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <div class="col-sm-12">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name='name'
                                   id="name"
                                   value="" placeholder="Name" required>
                        </div>
                    </div>
                    @include("project.partials.billing-form-fields", ['required' => true, 'type' => "client"])

                    <div class="form-group">
                        <div class="col-sm-6">
                            <input type="submit" class="btn btn-primary add_new_client_button" value="Add client"/>
                        </div>
                        @hasRole(['administrator', 'master'])
                        <div class="col-sm-6 custom-price-button-container">
                            <button class="btn btn-primary btn-custom-price" data-prices-for="client" data-type="create">Prices</button>
                            <input type="hidden" name="custom-prices" value="">
                        </div>
                        @endHasRole
                    </div>
                </form>
            </div>
        </div>
    </aside>
    <aside id="edit_client" class="control-sidebar control-sidebar-edit add-new-project-modal">
        <div class="panel">
            <div class="panel-heading">
                <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
                <h1>
                    Edit client
                </h1>
            </div>

            <div class="panel-body">

                <form class="form-horizontal" method="POST" id="edit_client_form">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <div class="col-sm-12">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name"
                                   id="client_name"
                                   value="" placeholder="Name" required>
                        </div>
                    </div>
                    @include("project.partials.billing-form-fields", ['required' => true, 'type' => "client_edit"])
                    <div class="form-group">
                        <div class="col-sm-6">
                            <input type="submit" class="btn btn-primary edit_client_button" value="Save client"/>
                        </div>

                        @hasRole(['administrator', 'master'])
                        <div class="col-sm-6 custom-price-button-container">
                            <button class="btn btn-primary btn-custom-price" data-prices-for="client">Prices</button>
                            <input type="hidden" name="custom-prices" value="">
                        </div>
                        @endHasRole
                    </div>
                </form>
            </div>
        </div>
    </aside>

    <div class="modal fade" tabindex="-1" role="dialog" id="project-prices-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Prices</h4>
                </div>
                <div class="modal-body">
                    <div class="legend">
                        Color codes:
                        <ul>
                            <li class="orange">Prices marked with <span>orange</span> are the default prices</li>
                            <li class="green">Prices marked with <span>green</span> are the client prices</li>
                            <li class="blue">Prices marked with <span>blue</span> are the project price</li>
                        </ul>
                    </div>

                    <div class="client-error modal-error hidden">
                        <p>Pentru acest client nu sunt specificate preturi speciale. Au fost preluate preturile implicite!</p>
                    </div>

                    <div class="project-error-client modal-error hidden">
                        <p>
                            Pentru acest proiect nu sunt specificate preturi speciale. Au fost preluate preturile de la client!
                        </p>
                    </div>

                    <div class="project-error-default modal-error hidden">
                        <p>
                            Pentru acest proiect nu sunt specificate preturi speciale. Au fost preluate preturile implicite!
                        </p>
                    </div>

                    <table class="table">
                        <thead>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-save">Save changes</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@endsection
@section('footer')
    <script>
        var projectsPage;

        (function($) {
            var ProjectsPage = function() {
                var self = this;

                this.ready = function() {
                    this.handleDOM();
                    this.handleEvents();
                };

                this.handleDOM = function() {
                    this.distinctBillingToggle = $(".distinct-billing");
                    this.billingFieldsContainer = $(".billing-form-fields");
                    this.hasDatepicker = $(".has-datepicker");
                    this.projectPricesModal = $("#project-prices-modal");
                    this.pricesModalToggle = $(".btn-custom-price");
                    this.projectPricesModalSaveButton = this.projectPricesModal.find(".btn-save");
                };

                this.handleEvents = function () {
                    if (this.distinctBillingToggle.length > 0) {
                        this.distinctBillingToggle.on("click", function(e) {
                            e.preventDefault();
                            var form = $(this).parents("form");
                            var distinctBillingFields = form.find(".distinct-billing-fields");

                            if (distinctBillingFields.hasClass('hidden')) {
                                distinctBillingFields.find("input").addClass("required");
                                distinctBillingFields.find("input").prop("required", "required");
                                form.find("input[name=billing_distinct]").val(1);
                                distinctBillingFields.removeClass('hidden');
                            } else {
                                distinctBillingFields.find("input").removeClass("required");
                                distinctBillingFields.find("input").removeProp("required");
                                form.find("input[name=billing_distinct]").val(0);
                                distinctBillingFields.addClass('hidden');
                            }

                            distinctBillingFields.find("input").each(function() {
                                $(this).removeClass("error");
                                var id = $(this).attr("id");
                                $("#" + id + "-error").remove();
                            })

                        });
                    }

                    if (this.hasDatepicker.length > 0) {
                        this.hasDatepicker.datetimepicker({
                            showClear: false,
                            showClose: true,
                            format: 'DD/MM/YYYY'
                        });
                    }

                    this.projectPricesModal.on("click", function(e) {
                        e.stopPropagation();
                    });

                    this.pricesModalToggle.on("click", function(e) {
                        e.preventDefault();

                        if (!$(this).parents("form").valid()) {
                            return true;
                        }

                        var requestData = {};

                        if ($(this).attr('data-prices-for') === "project") {

                            var clientId  = $(this).parents("form").find("input[name=client_id]").val();
                            var projectId = 0;
                            var projectIdInput = $(this).parents("form").find("#project_id_edit");

                            if (projectIdInput.length > 0) {
                                projectId = projectIdInput.val();
                            }

                            if (projectId) requestData.projectId = projectId;
                            if (clientId) requestData.clientId = clientId;

                        } else {

                            var clientIdButton  = $(this).parents("form").find(".edit_client_button");

                            if (clientIdButton.length > 0) {
                                clientId = clientIdButton.attr('data-id');
                            }

                            if (clientId) requestData.clientId = clientId;
                        }

                        $.get("/admin/prices/get-project-prices", requestData, function(response) {
                            self.drawPricesTable(response);
                            $("#project-prices-modal").modal("show");
                        });
                    });

                    this.projectPricesModalSaveButton.on("click", function(e) {
                        e.preventDefault();
                        self.savePriceData();
                        $("#project-prices-modal").modal("hide");
                    });
                };

                this.drawPricesTable = function(response) {
                    var table = this.projectPricesModal.find(".table");
                    var thead = table.find('thead');
                    var tbody = table.find('tbody');

                    var currentFormType = $(".control-sidebar-open").find("form").find(".btn-custom-price").attr("data-prices-for");
                    var formLevel = currentFormType == "client" ? 1 : 2;

                    thead.html('');
                    tbody.html('');

                    thead.append("<tr><th>Languages</th></tr>");
                    thead = thead.find("tr").first();

                    response.pricingTypes.forEach(function(type) {
                        var th = $('<th></th>');
                        th.text(type.name);
                        th.appendTo(thead);
                    });
                    var inheritsAllPrices = true;
                    var hasClientPricing = false;

                    response.languages.forEach(function(language) {
                        var tr = $("<tr></tr>");
                        var th = $("<th></th>");
                        th.text(language.name);
                        th.appendTo(tr);

                        response.pricingTypes.forEach(function(type, index) {
                            var td = $("<td></td>");
                            var input = $("<input type='text'>");
                            input.addClass('form-control project-price-input');
                            input.appendTo(td);
                            input.attr('data-language-id', language.id);
                            input.attr('data-type-id', type.id);

                            // Speaking tests
                            var paperType = (type['id'] == 3 || type['id'] == 9) ? 4 : type['id'];

                            // Writing tests
                            paperType = (paperType == 1) ? 2 : paperType;

                            var hasPricingType = false;

                            language['language_paper_type'].forEach(function(languagePaper) {
                                if (response.pricingTypesMap[languagePaper['paper_type_id']] == paperType) {
                                    hasPricingType = true;
                                    return true;
                                }
                            });

                            if (!hasPricingType) {
                                input.prop('disabled', true);
                                input.attr('disabled', true);
                            }

                            if (response.groupedPrices &&
                                response.groupedPrices[language.id] &&
                                response.groupedPrices[language.id][type.id]) {

                                var entry = response.groupedPrices[language.id][type.id];

                                input.val(entry.price);
                                input.attr('data-init-value', entry.price);

                                if (entry.level == formLevel) {
                                    input.attr('data-price-id', entry.id);
                                    inheritsAllPrices = false;
                                }

                                if (entry.level == 0) {
                                    input.addClass("border-default");
                                } else if (entry.level == 1) {
                                    hasClientPricing = true;
                                    input.addClass("border-client");
                                } else if (entry.level == 2) {
                                    input.addClass("border-project");
                                }
                            } else {
                                input.addClass("border-default");
                            }

                            td.appendTo(tr);
                        });
                        tr.appendTo(tbody);
                    });

                    $(".modal-error").addClass("hidden");

                    if (inheritsAllPrices && formLevel === 1) {
                        $(".client-error").removeClass("hidden");
                    } else if (inheritsAllPrices && !hasClientPricing && formLevel === 2) {
                        $(".project-error-default").removeClass("hidden");
                    } else if (hasClientPricing && formLevel === 2) {
                        $(".project-error-client").removeClass("hidden");
                    }

                    AutoNumeric.multiple(".project-price-input", {
                        alwaysAllowDecimalCharacter: true,
                        caretPositionOnFocus: "end",
                        decimalPlacesRawValue: 2,
                        decimalPlacesShownOnBlur: 2,
                        decimalPlacesShownOnFocus: 2,
                        digitGroupSeparator: "",
                        minimumValue: "0",
                        emptyInputBehavior: "zero"
                    });

                    $(".project-price-input").on("keyup blur", function(e) {
                        var input = $(this);
                        var oldValue = input.attr("data-init-value");
                        if (oldValue != input.val()) {
                            input.addClass("dirty");
                        } else {
                            input.removeClass("dirty");
                        }
                    });
                };

                this.savePriceData = function() {
                    var create = [];
                    var update = [];
                    var currentForm = $(".control-sidebar-open").find("form");
                    var clientId = currentForm.find("#project_client_id_edit").val();
                    var projectId = currentForm.find("#project_id_edit").val();
                    var currentFormType = currentForm.find(".btn-custom-price").attr("data-prices-for");
                    var level = currentFormType == "client" ? 1 : 2;

                    this.projectPricesModal.find(".table").find("input.dirty").each(function() {
                        var input = $(this);
                        if (input.attr('data-price-id')) {
                            update.push({
                                "price": input.val(),
                                "pricing_type_id": input.attr('data-type-id'),
                                "language_id": input.attr('data-language-id'),
                                "client_id": clientId,
                                "project_id": projectId,
                                "id": input.attr('data-price-id')
                            });
                        } else {
                            create.push({
                                "price": input.val(),
                                "pricing_type_id": input.attr('data-type-id'),
                                "client_id": clientId,
                                "project_id": projectId,
                                "level": level,
                                "language_id": input.attr('data-language-id')
                            });
                        }
                    });
                    var data = {create: create, update: update};
                    var parentForm = $(".control-sidebar-open").find("form");
                    parentForm.find("input[name=custom-prices]").val(JSON.stringify(data));

                    if(level == 2) {
                        var projectID = $('#project_id_edit').val();

                        AjaxCall({
                            url: '/project/update/' + projectID,
                            successMsg: 'Project has been updated.',
                            errorMsg: 'Project has not been updated.',
                            validate: false
                        }).makeCall(parentForm.find("input[type=submit]"), function () {
                            window.location.reload();
                        });
                    } else {
                        parentForm.find("input[type=submit]").trigger('click');
                    }
                };
            };

            projectsPage = new ProjectsPage();
            $(document).ready(function () {
                projectsPage.ready();
            });
        })(jQuery);

        var projectsMasonry = $('.grid');
        $(document).ready(function () {
            $('select#project_type_select').select2({
                minimumResultsForSearch: -1,
                allowClear: false
            });

            projectsMasonry.masonry({
                itemSelector: '.grid-item',
                columnWidth: 258
            });

            $(document).on('click', '.add-client', function (e) {
                e.preventDefault();
                $('#add_new_client').addClass('control-sidebar-open');
                $('body').addClass('open');
            });

            @canAtLeast(['project.create'])
            $(document).on('click', '.edit-client', function (e) {
                e.preventDefault();
                var id = $(this).attr("data-id");
                $.get("/admin/getClientDetails", {id: id}, function(response) {
                    if (response && response.client) {
                        for (var column in response.client) {
                            var cell = response.client[column];
                            if (column == "name") {
                                $("#client_name").val(cell);
                            } else {
                                console.log(cell);
                                if (column == "billing_hidden") {
                                    if (cell == 1) {
                                        $("#" + "client_edit_" + column).prop("checked", true);
                                        $("#" + "client_edit_" + column).attr("checked", "checked");
                                    } else {
                                        $("#" + "client_edit_" + column).prop("checked", false);
                                        $("#" + "client_edit_" + column).removeAttr("checked");
                                    }
                                } else {
                                    $("#" + "client_edit_" + column).val(cell);
                                }
                            }
                        }
                        $('#edit_client').addClass('control-sidebar-open');
                        $('.edit_client_button').attr('data-id', response.client.id);
                        $('body').addClass('open');
                    }

                })
            });
            $(document).on('click', '.edit_client_button', function (e) {
                e.preventDefault();
                var clientName = $('#client_name').val(),
                    clientId = $(this).attr('data-id');

                var data = {
                    'name': clientName
                };

                $("#edit_client_form").find(".billing-form-fields input, input[name=custom-prices]").each(function() {
                    if ($(this).attr("type") == "checkbox") {
                        data[$(this).attr('name')] = $(this).is(":checked") ? 1 : 0;
                    } else {
                        data[$(this).attr('name')] = $(this).val();
                    }

                });



                if ($(this).parents('form').valid()) {
                    $.ajax({
                        url: '/project/updateClient/' + clientId,
                        type: 'POST',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: data,
                        cache: false,
                        dataType: 'json',
                        success: function (res) {
                            if (res.resType == 'success') {
                                $('#span-client-name-' + clientId).text(clientName);
                                $('#edit_client').removeClass('control-sidebar-open');
                                $('body').removeClass('open');
                            } else {
                                var error_messages = '';

                                if ($.type(res.errMsg) === "string") {
                                    error_messages = res.errMsg;
                                } else {
                                    $.each(res.errMsg, function (element, error) {
                                        error_messages += error + '<br>';
                                    });
                                }
                                swal('Error', error_messages, 'error');
                            }
                        }
                    });
                }
            });
            @endCanAtLeast

            $(document).on('click', '.add-button', function (e) {
                e.preventDefault();

                var _this = $(this),
                    client_id = $(this).attr('data-id');
                $('#client_id').val(client_id);
                $('.add_new_project').attr('data-id', $(this).attr('data-id'));

                $('#participants_id').select2({
                    placeholder: 'Any',
                    tags: true,
                    allowClear: true,
                    tokenSeparators: [",", " "],
                    createTag: function (tag) {
                        return {
                            id: tag.term,
                            text: tag.term,
                            // add indicator:
                            isNew : true
                        };
                    }
                }).on("select2:select", function(e) {
                    var _this = $(this);
                    if(e.params.data.isNew){
                        // store the new tag:
                        $('.add-new-project-modal.control-sidebar-open').find('.loading').addClass('active');
                        $.ajax({
                            url: '/project/createClient',
                            type: 'POST',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            data: {
                                email: e.params.data.text,
                                client_id: client_id
                            },
                            cache: false,
                            dataType: 'json',
                            success: function (res) {
                                if (res.resType == 'success') {
                                    // append the new option element permanently:
                                    _this.find('[value="'+e.params.data.id+'"]').replaceWith('<option selected value="'+res.data.id+'">'+e.params.data.text+'</option>');
                                } else {
                                    var error_messages = '';

                                    if( $.type(  res.errMsg  ) === "string" ){
                                        error_messages = res.errMsg;
                                    } else {
                                        $.each(res.errMsg, function(element, error){
                                            error_messages += error + '<br>';
                                        });
                                    }

                                    swal('Error', error_messages, 'error');
                                    _this.find('[value="'+e.params.data.id+'"]').remove();
                                }
                            },
                            complete: function(){
                                $('.add-new-project-modal.control-sidebar-open').find('.loading').removeClass('active');
                            }
                        });
                    }
                });


                $.ajax({
                    type: "GET",
                    url: "/project/getParticipants/" + $(this).attr('data-id'),
                    dataType: 'json',
                    success: function (data) {
                        $('#participants_id').html(' ');
                        for (var i = 0; i < data.length; i++) {
                            var name = data[i].first_name == null && data[i].last_name == null ? data[i].email : data[i].first_name + ' ' + data[i].last_name;
                            $('#participants_id').append('<option value="' + data[i].id + '">' + name + '</option>');
                        }
                    }
                });

                $('.add-new-project-modal').removeClass('control-sidebar-open');
                $('#add-modal').addClass('control-sidebar-open');
                $('body').addClass('open');
            });

            $(document).on('click', '.edit-project', function (e) {
                e.preventDefault();
                $('#edit-modal .loading').addClass('active');


                var _this = $(this),
                    client_id = _this.attr('data_client_id'),
                    projectId = _this.attr('data-id');

                $('.add-new-project-modal').removeClass('control-sidebar-open');
                $('#edit-modal').addClass('control-sidebar-open');

                $('body').addClass('open');

                // Reset billing fields
                var form = $(".edit-project-button").parents('form');
                var distinctBillingFields = form.find(".distinct-billing-fields");

                distinctBillingFields.find("input").removeClass("required");
                distinctBillingFields.find("input").removeProp("required");
                form.find("input[name=billing_distinct]").val(0);
                distinctBillingFields.addClass('hidden');



                $('#participants_id_edit').select2({
                    placeholder: 'Any',
                    tags: true,
                    allowClear: true,
                    tokenSeparators: [",", " "],
                    createTag: function (tag) {
                        return {
                            id: tag.term,
                            text: tag.term,
                            // add indicator:
                            isNew : true
                        };
                    }
                }).on("select2:select", function(e) {
                    var _this = $(this);
                    if(e.params.data.isNew){
                        // store the new tag:
                        $('.add-new-project-modal.control-sidebar-open').find('.loading').addClass('active');
                        $.ajax({
                            url: '/project/createClient',
                            type: 'POST',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            data: {
                                email: e.params.data.text,
                                client_id: client_id
                            },
                            cache: false,
                            dataType: 'json',
                            success: function (res) {
                                if (res.resType == 'success') {
                                    // append the new option element permanently:
                                    _this.find('[value="'+e.params.data.id+'"]').replaceWith('<option selected value="'+res.data.id+'">'+e.params.data.text+'</option>');
                                } else {
                                    var error_messages = '';

                                    if( $.type(  res.errMsg  ) === "string" ){
                                        error_messages = res.errMsg;
                                    } else {
                                        $.each(res.errMsg, function(element, error){
                                            error_messages += error + '<br>';
                                        });
                                    }

                                    swal('Error', error_messages, 'error');
                                    _this.find('[value="'+e.params.data.id+'"]').remove();
                                }
                            },
                            complete: function(){
                                $('.add-new-project-modal.control-sidebar-open').find('.loading').removeClass('active');
                            }
                        });
                    }
                });

                $.ajax({
                    url: '/project/' + projectId,
                    type: 'get',
                    success: function (response) {
                        $('#edit-modal .loading').removeClass('active');
                        $('#edit-modal .btn-custom-price').attr('data-project-id', response.id);
                        $('#edit-modal .btn-custom-price').attr('data-client-id', response.client_id);
                        $('#project_id_edit').val(response.id);
                        $('#project_client_id_edit').val(response.client_id);
                        $('#project_name_edit').val(response.name);
                        $('#project_type_edit').val(response.project_type_id);
                        $('#bill_client_edit').val(response.default_bill_client);
                        $('#pay_assessors_id').val(response.default_pay_assessor);
                        $('#project_edit_billing_contract_annex').val(response.billing_contract_annex);
                        $('#project_edit_billing_contract_annex_date').val(response.billing_contract_annex_date);
                        //$('#project_edit_distinct_billing').val(response.billing_distinct);
                        var form = $(".edit-project-button").parents("form");
                        if (response.billing_distinct) {
                            form.find(".distinct-billing").trigger("click");
                        }
                        form.find(".distinct-billing-fields input").each(function() {
                            var name = $(this).attr('name');
                            if (response[name]) {
                                $(this).val(response[name]);
                            } else {
                                $(this).val("");
                            }
                        });
                        $.ajax({
                            type: "GET",
                            url: "/project/getParticipants/" + client_id,
                            data: {projectId: projectId},
                            dataType: 'json',
                            success: function (data) {
                                $('#participants_id_edit').val("");
                                $('#participants_id_edit option').remove();

                                projectId = parseInt(projectId);

                                for (var i = 0; i < data.length; i++) {
                                    var projectsParticipating = data[i].projects_participating.map(function(elem) {
                                        return elem.project_id;
                                    });
                                    var selectedTag = $.inArray(projectId, projectsParticipating) >= 0 ? 'selected' : '';
                                    var name = data[i].first_name == null && data[i].last_name == null ? data[i].email : data[i].first_name + ' ' + data[i].last_name;
                                    $('#participants_id_edit').append('<option ' + selectedTag + ' value="' + data[i].id + '">' + name + '</option>');
                                }

                            }
                        });
                    },
                    error:
                        function (response) {
                            $('#edit-modal .loading').removeClass('active');
                            swal({
                                type: 'error',
                                title: 'Project has not been updated.'
                            });
                        }
                });

                $('#client_id_edit').select2({
                    placeholder: 'Any',
                    allowClear: true
                });
            });
        });
    </script>
@endsection