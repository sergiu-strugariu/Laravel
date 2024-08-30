var languagesPage;

(function($) {

    var LanguagesPage = function() {
        var self = this;

        this.ready = function() {
            if ($(".languages-page").length > 0) {
                this.handleDOM();
                this.handleEvents();
            }
        };

        this.handleDOM = function() {
            this.languagesContainer = $(".languages-box");
            this.languagesTable = this.languagesContainer.find(".table");
            this.addLanguageButton = $("button.add_language");
            this.addLanguageModal = $("#add_new_language");
            this.addLanguageSaveButton = $('.add_new_language_button');

            this.editLanguageModal = $("#edit_language");
            this.editLanguageSaveButton = $('.edit_language_button');
            this.editLanguageNameInput = this.editLanguageModal.find("input[name=name]");
            this.editLanguageIdInput = this.editLanguageModal.find("input[name=languageId]");

        };

        this.handleEvents = function() {
            this.addLanguageButton.on("click", function(e) {
                e.preventDefault();
                self.addLanguageModal.addClass('control-sidebar-open');
                $('body').addClass('open');
            });

            this.addLanguageSaveButton.on('click', function (e) {
                AjaxCall({
                    url: '/admin/languages/add'
                }).makeCall($(this), function (res) {
                    self.addLanguageModal.removeClass('control-sidebar-open');
                    self.dataTable.ajax.reload();
                    $('body').removeClass('open');
                    window.location.href = "/admin/prices";
                });
            });

            this.languagesTable.on("click", ".action.edit", function(e) {
                e.preventDefault();
                var languageId = $(this).attr("data-language-id");

                self.editLanguageIdInput.val(languageId);
                $.get("/admin/languages/get/" + languageId, {}, function(response) {
                    self.editLanguageNameInput.val(response.language.name);
                    self.editLanguageModal.addClass('control-sidebar-open');
                    $('body').addClass('open');
                });
            });

            this.editLanguageSaveButton.on('click', function (e) {
                e.preventDefault();
                AjaxCall({
                    url: '/admin/languages/edit',
                    validate: false
                }).makeCall($(this), function (res) {
                    self.editLanguageModal.removeClass('control-sidebar-open');
                    self.dataTable.ajax.reload();
                    $('body').removeClass('open');
                });
            });

            this.drawDatatable();
            this.updateDatatable();
        };

        this.drawDatatable = function() {
        this.dataTable = this.languagesTable.DataTable({
                destroy: true,
                paging: true,
                lengthChange: false,
                pageLength: 10,
                searching: false,
                ordering: false,
                info: true,
                autoWidth: true,
                processing: true,
                responsive: true,
                serverSide: true,
                ajax: '/admin/languages/all',
                columns: [
                    {
                        data: 'name',
                        name: 'name'
                    },

                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return '<a href="#" class="action edit" data-language-id="' + row.id + '" title="Edit language"><span class="fa fa-pencil"></span></a>';
                        }
                    },
                ],
            });
        };

        this.updateDatatable = function() {

        };
    };

    languagesPage = new LanguagesPage();
    $(document).ready(function () {
        languagesPage.ready();
    });
})(jQuery);
