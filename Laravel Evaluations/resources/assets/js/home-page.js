var homePage;

(function($) {
    var HomePage = function() {
        var self = this;
        var filterTpl = '<label><input type="checkbox" class="filter-checkbox" checked data-index="{{index}}"> {{filterName}}</label>';
        this.revenuePerLanguageData = {};
        this.revenuePerLanguageHidden = [];
        this.revenuePerDayDaterangeFilterDrawn = false;
        this.ready = function() {
            this.handleDOM();
            this.handleEvents();
        };

        this.handleDOM = function() {
            this.revenuePerLanguage = $("#revenue-per-language");
            this.revenuePerLanguageFilters = $('#revenue-per-language-filters');
            this.revenuePerDay = $("#revenue-per-day");
            this.revenuePerDayDaterangeFilter = $("#revenue-per-day-daterange");
        };

        this.handleEvents = function () {
            if (this.revenuePerLanguage.length > 0) {
                this.revenuePerLanguageCtx = this.revenuePerLanguage[0].getContext('2d');
                this.handleRevenuePerLanguage();
                this.revenuePerLanguageFilters.on("change", '.filter-checkbox', function() {
                    self.revenuePerLanguageHidden = [];
                    self.revenuePerLanguageFilters.find('.filter-checkbox:not(:checked)').each(function() {
                        var index = $(this).attr('data-index');
                        self.revenuePerLanguageHidden.push(parseInt(index));
                    });
                    self.updateRevenuePerLanguage();
                })
            }

            if (this.revenuePerDay.length > 0) {
                this.revenuePerDayCtx = this.revenuePerDay[0].getContext('2d');
                this.handleRevenuePerDay();
            }

        };

        this.handleRevenuePerLanguage = function() {
            this.revenuePerLanguageChart = new Chart(this.revenuePerLanguageCtx, {
                type: 'bar',
                data: {},
                options: {
                    "responsive": true,
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });

            $.get('/home/get-revenue-per-language', {}, function(response) {
                if (typeof response == 'object' && response.revenue) {
                    self.revenuePerLanguageData = response.revenue;
                    self.updateRevenuePerLanguage(true);
                }
            });

        };

        this.updateRevenuePerLanguage = function(drawFilters) {
            var data = {
                "labels": [],
                "datasets": [{
                    "label": "Revenue in EUR",
                    "data": [],
                    "backgroundColor": []
                }]
            };
            var x = 0;
            for (var entry in this.revenuePerLanguageData) {
                if (this.revenuePerLanguageHidden.indexOf(x) < 0) {
                    var item = this.revenuePerLanguageData[entry];
                    var itemHash = self.hashCode((entry+51) + item.label);
                    var itemColor = self.intToRGB(itemHash);
                    data.labels.push(item.label);
                    data.datasets[0].data.push(parseFloat(item.revenue));
                    data.datasets[0].backgroundColor.push("#" + itemColor);
                    if (drawFilters) {
                        var elt = $(filterTpl.replace('{{index}}', x).replace('{{filterName}}', item.label));
                        elt.css({
                            "background": "#" + itemColor
                        });
                        elt.appendTo(self.revenuePerLanguageFilters);
                    }
                }
                x++;
            }

            self.revenuePerLanguageChart.data = data;
            self.revenuePerLanguageChart.update();
        };

        this.handleRevenuePerDay = function() {
            this.revenuePerDayChart = new Chart(this.revenuePerDayCtx, {
                type: 'line',
                data: {},
                options: {
                    "responsive": true,
                    legend: {
                        display: false
                    }
                }
            });

            this.getRevenuePerDayData();
        };

        this.getRevenuePerDayData = function(data) {
            data = data || {};
            $.get('/home/get-revenue-per-day', data, function(response) {
                var data = {
                    "labels": [],
                    "datasets": [{
                        "label": null,
                        "data": [],
                        "backgroundColor": []
                    }]
                };

                if (typeof response == 'object' && response.revenue) {
                    for (var entry in response.revenue) {
                        var item = response.revenue[entry];
                        data.labels.push(moment(item.label).format("DD MMM 'YY"));
                        data.datasets[0].data.push(parseFloat(item.revenue));
                    }

                    if (!self.revenuePerDayDaterangeFilterDrawn) {
                        self.revenuePerDayDaterangeFilter.daterangepicker({
                            autoUpdateInput: true,
                            opens: 'right',
                            minDate: moment(response.minDate),
                            startDate: moment(response.minDate),
                            locale: {
                                cancelLabel: 'Clear',
                                format: "DD-MM-YYYY"
                            }
                        });
                        self.revenuePerDayDaterangeFilter.on('apply.daterangepicker', function(ev, picker) {
                            var dates = self.revenuePerDayDaterangeFilter.val().split(" - ");
                            self.getRevenuePerDayData({
                                "dateStart": dates[0] + " 00:00:00",
                                "dateEnd": dates[1] + " 00:00:00"
                            });
                        });
                        self.revenuePerDayDaterangeFilterDrawn = true;
                    }
                }

                self.revenuePerDayChart.data = data;
                self.revenuePerDayChart.update();
            });
        };

        this.hashCode = function (str) {
            var hash = 0;
            for (var i = 0; i < str.length; i++) {
                hash = str.charCodeAt(i) + ((hash << 5) - hash);
            }
            return hash;
        };

        this.intToRGB = function (i){
            var c = (i & 0x00FFFFFF)
                .toString(16)
                .toUpperCase();

            return "00000".substring(0, 6 - c.length) + c;
        }
    };

    homePage = new HomePage();
    $(document).ready(function () {
        homePage.ready();
    });
})(jQuery);