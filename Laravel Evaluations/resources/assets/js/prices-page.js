var pricesPage;

(function($) {

    var PricesPage = function() {
        var self = this;

        this.ready = function() {
            if ($(".prices-page").length > 0) {
                this.handleDOM();
                this.handleEvents();
            }
        };

        this.handleDOM = function() {
            this.pricesPage = $(".prices-page");
            this.priceInputs = $(".prices-box").find(".price-input")
            this.saveButton = this.pricesPage.find('.save_prices');
        };

        this.handleEvents = function() {
            AutoNumeric.multiple(".price-input", {
                alwaysAllowDecimalCharacter: true,
                caretPositionOnFocus: "end",
                decimalPlacesRawValue: 2,
                decimalPlacesShownOnBlur: 2,
                decimalPlacesShownOnFocus: 2,
                digitGroupSeparator: "",
                minimumValue: "0",
                emptyInputBehavior: "zero"
            });

            this.priceInputs.on("keyup blur", function(e) {
                var input = $(this);
                var oldValue = input.attr("data-init-value");
                if (oldValue != input.val()) {
                    input.addClass("dirty");
                } else {
                    input.removeClass("dirty");
                }

                if ($(".prices-box").find(".price-input.dirty").length > 0) {
                    self.saveButton.removeClass("hidden");
                } else {
                    self.saveButton.addClass("hidden");
                }
            });

            this.saveButton.on("click", function(e) {
                e.preventDefault();
                if (!$(this).hasClass('disabled')) {
                    self.savePrices();
                }
            });
        };

        this.savePrices = function() {
            var create = [];
            var update = [];
            this.saveButton.addClass("disabled");

            $(".prices-box").find(".price-input.dirty").each(function() {
                var pricingType = $(this).attr("data-pricing-type");
                var languageId = $(this).attr("data-language-id");
                var typeId = $(this).attr("data-type-id");
                var val = $(this).val();

                if (typeId) {
                    update.push({
                        "pricing_type_id": pricingType,
                        "language_id": languageId,
                        "price": val,
                        "id": typeId
                    });
                } else {
                    create.push({
                        "pricing_type_id": pricingType,
                        "language_id": languageId,
                        "level": 0,
                        "price": val
                    });
                }
            });

            $.ajax({
                url: '/admin/prices/save-default',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                data: {
                    create: create,
                    update: update
                },
                success: function(response) {
                    if (response && response.resType == 'success') {
                        window.location.reload();
                    }
                }
            });
        };
    };

    pricesPage = new PricesPage();
    $(document).ready(function () {
        pricesPage.ready();
    });
})(jQuery);
