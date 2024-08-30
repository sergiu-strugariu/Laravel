var billingPage;

(function($) {
    var BillingPage = function() {
        var self = this;
        var rowTpl = '<div class="form-group"><div class="col-sm-12"><label>{{label}}</label><p>{{data}}</p></div></div>';
        this.ready = function() {
            if ($(".billing-page").length > 0) {
                this.handleDOM();
                this.handleEvents();
            }
        };

        this.handleDOM = function() {
            this.filtersContainer = $("#filters");
            this.selectClient = $("#client_select");
            this.selectDateRange = $(".select-daterange");
            this.clientTableContainer = $(".client-table-container");
            this.clientNameContainer = this.clientTableContainer.find(".client-name");
            this.selectAllProjects = $(".billing-export-all");
            this.generateInvoiceButton = $(".generate-invoice");
            this.projectsTableContainer = $(".projects-table");
            this.controlSidebar = $(".control-sidebar");
            this.controlSidebarTitle = this.controlSidebar.find("h1");
            this.controlSidebarBody = this.controlSidebar.find(".panel-body");
        };

        this.handleEvents = function () {
            this.filtersContainer.show();
            if (this.selectClient.length > 0) {
                this.selectClient.select2({
                    allowClear: false
                });
            }

            if (this.selectClient.length > 0) {
                this.selectClient.on("change", function() {
                   self.getClientData();
                })
            }

            if (this.selectDateRange.length > 0) {
                this.selectDateRange.on("click focus change", function(e) {
                    e.preventDefault();
                    return false;
                });
                this.selectDateRange.daterangepicker({
                    autoUpdateInput: true,
                    opens: 'right',
                    startDate: moment(new Date()).subtract(1, "months"),
                    locale: {
                        cancelLabel: 'Clear',
                        format: "DD-MM-YYYY"
                    }
                });

                this.selectDateRange.on('apply.daterangepicker', function() {
                    self.getClientData();
                });
            }

            if (this.selectAllProjects.length > 0) {
                this.selectAllProjects.on("change", function () {
                    self.clientTableContainer.find("td input[type=checkbox]:enabled").prop('checked', self.selectAllProjects.is(":checked"));
                });
            }

            $(document).on("change", ".billing-export, .billing-export-all", function(e) {
                if ($(".billing-export:checked").length > 0) {
                    self.generateInvoiceButton.addClass('active');
                } else {
                    self.generateInvoiceButton.removeClass('active');
                }
            });

            if (this.projectsTableContainer.length > 0) {
                this.projectsTableContainer.on("click", '.action.view-project-details', function(e) {
                    e.preventDefault();
                    self.showProjectDetails($(this));
                });

                this.projectsTableContainer.on("click", '.action.view-client-details', function(e) {
                    e.preventDefault();
                    self.showClientDetails();
                });

                this.projectsTableContainer.on("click", '.action.preview-invoice', function(e) {
                    e.preventDefault();
                    self.getInvoicePreview($(this));
                });
            }

            if (this.generateInvoiceButton.length > 0) {
                this.generateInvoiceButton.on("click", function() {
                    if ($(this).hasClass('active')) {
                        self.generateInvoice();
                    } else {
                        swal(
                            'Error!',
                            'Please select some projects first!',
                            'error'
                        );
                    }
                })
            }

            this.drawDatatable();
        };

        this.drawDatatable = function() {
            this.dataTable = $('.projects-table table').DataTable({
                destroy: true,
                paging: false,
                lengthChange: false,
                pageLength: 10,
                searching: false,
                ordering: false,
                info: true,
                autoWidth: true,
                processing: true,
                responsive: true,
                columns: [
                    {
                        data: null,
                        name: 'cols',
                        orderable: false,
                        searchable: false,
                        defaultContent: '',
                        className: 'styled-checkbox',
                        render: function(data, type, row) {
                            var unbilledTests = 0;

                            row.tasks.forEach(function(task) {
                                task.papers.forEach(function(paper) {
                                    if (!paper.invoice_id) unbilledTests++;
                                });
                            });

                            return '<label><input type="checkbox" name="billing-export" class="billing-export" '+(unbilledTests ? "" : "disabled")+' value="' + row.id + '" /> <span class="label-text"></span></label>';
                        }
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: null,
                        name: 'tasks',
                        render: function (data, type, row) {
                            return row.tasks.length;
                        }
                    },
                    {
                        data: null,
                        name: 'billed',
                        render: function (data, type, row) {
                            var total = 0;

                            row.tasks.forEach(function(task) {
                                task.papers.forEach(function(paper) {
                                    if (paper.invoice_id) total++;
                                });
                            });

                            return total;
                        }
                    },
                    {
                        data: null,
                        name: 'unbilled',
                        render: function (data, type, row) {
                            var total = 0;

                            row.tasks.forEach(function(task) {
                                task.papers.forEach(function(paper) {
                                    if (!paper.invoice_id) total++;
                                });
                            });

                            return total;
                        }
                    },
                    {
                        data: null,
                        name: 'value',
                        render: function (data, type, row) {
                            var value = 0;
                            row.tasks.forEach(function(task) {
                                task.papers.forEach(function(paper) {
                                    if (!paper.invoice_id) {
                                        value += paper.cost;

                                        // Add custom period of the test has one
                                        if (paper.paper_type_id == 2) {
                                            value += task.custom_period_cost;
                                        }
                                    }
                                });
                            });
                            return value + " EUR";
                        }
                    },
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            var unbilledTests = 0;

                            row.tasks.forEach(function(task) {
                                task.papers.forEach(function(paper) {
                                    if (!paper.invoice_id) unbilledTests++;
                                });
                            });

                            return '<a href="#" class="action view-project-details" data-project-id="' + row.id + '" title="View project billing information"><span class="fa fa-eye"></span></a>'+
                                '<a href="#" class="action view-client-details" title="View client billing information"><span class="fa fa-eye"></span></a>' +
                                (unbilledTests ? '<a href="#" class="action preview-invoice" title="Invoice preview" data-project-id="' + row.id + '"><span class="fa fa-file-text"></a>' : "")
                            ;
                        }
                    }
                ]
            });
        };

        this.getClientData = function() {
            var url = "/billing/get-client-projects/" + self.selectClient.val();
            var date = this.selectDateRange.val();

            $.get(url, {'date': date}, function(response) {
                if (response && response.projects && response.client) {
                    var dateRange = self.selectDateRange.val().split(" - ");
                    var startDate = moment(dateRange[0], "DD-MM-YYYY").format("D MMM YYYY");
                    var endDate = moment(dateRange[1], "DD-MM-YYYY").format("D MMM YYYY");
                    var headerText = response.client.name + " <span>" + startDate + " - " + endDate + "</span>";
                    self.selectDateRange.removeClass('disabled');
                    self.clientNameContainer.html(headerText);
                    self.updateDataTable(response.projects);
                    self.clientTableContainer.removeClass("hidden");
                }
            })
        };

        this.updateDataTable = function (projects) {
            this.dataTable.clear();
            this.dataTable.rows.add(projects);
            this.dataTable.draw();
        };

        this.generateInvoice = function () {
            var ids = [];
            var clientId = this.selectClient.val();

            $(".billing-export:checked").each(function () {
                ids.push($(this).val());
            });

            var date = this.selectDateRange.val();
            this.generateInvoiceButton.removeClass("active");

            $.get("/billing/generate-invoice/" + clientId, {ids: ids, "date": date}, function (response) {
                if (response && response.invoices) {
                    var bills = "";
                    self.clientTableContainer.find("td input[type=checkbox]:checked").prop('disabled', true);
                    self.clientTableContainer.find("td input[type=checkbox]:checked").prop('checked', false);

                    response.invoices.forEach(function(item) {
                        bills += "<div><strong>" + item.series + item.number + "</strong></div>";
                    });
                    swal({
                        type: 'success',
                        title: 'Success!',
                        html: '<div><p>S-au emis urmatoarele facturi:</p>' + bills + "</div>",
                        showConfirmButton: false,
                        timer: 115500
                    });
                }
            })
        };

        this.showProjectDetails = function (elt) {
            // Show aside sidebar
            this.controlSidebar.addClass('control-sidebar-open');
            $('body').addClass('open');

            this.controlSidebarTitle.text("Project Billing Details");
            var id = elt.attr("data-project-id");
            this.getBillingInformation(id, "project");
        };

        this.showClientDetails = function() {

            // Show aside sidebar
            this.controlSidebar.addClass('control-sidebar-open');
            $('body').addClass('open');

            this.controlSidebarTitle.text("Client Billing Details");
            var id = this.selectClient.val();
            this.getBillingInformation(id, "client");
        };

        this.getBillingInformation = function(id, type) {
            this.controlSidebarBody.html("");
            $.get("/billing/get-billing-information", {id: id, type: type}, function(response) {
                if (response && typeof response === "object") {
                    if (response.billing) {
                        for (var key in response.billing) {
                            var item = response.billing[key];
                            var elt = $(rowTpl.replace("{{label}}", key).replace("{{data}}", item));
                            elt.appendTo(self.controlSidebarBody);
                        }
                    } else if (type !== "client") {
                        var elt = $(rowTpl.replace("{{label}}", "Billing").replace("{{data}}", "This project uses the companies billing information"));
                        elt.appendTo(self.controlSidebarBody);
                    }

                    if (response.annex) {
                        for (var key in response.annex) {
                            var item = response.annex[key];
                            var elt = $(rowTpl.replace("{{label}}", key).replace("{{data}}", item));
                            elt.appendTo(self.controlSidebarBody);
                        }
                    }
                }
            })
        };

        this.getInvoicePreview = function(elt) {
            var ids = [elt.attr("data-project-id")];
            var clientId = this.selectClient.val();
            var date = this.selectDateRange.val();

            window.location = "/billing/generate-invoice/" + clientId + "?" + $.param({ids: ids, "date": date, "draft" : true});
        }
    };

    billingPage = new BillingPage();
    $(document).ready(function () {
        billingPage.ready();
    });
})(jQuery);

