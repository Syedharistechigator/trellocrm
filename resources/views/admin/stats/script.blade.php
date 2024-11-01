<script>
    /** => Developer Michael Update <= **/
    function getRandomInt(min, max) {
        min = Math.ceil(min);
        max = Math.floor(max);
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    let configMonths = {!! json_encode(config('app.months')) !!};

    $('[type=search],.bs-searchbox input[type=text]').each(function () {
        var randomNumber = getRandomInt(111111111, 999999999);
        $(this).attr('id', "dt-search-box-" + randomNumber);
    });

    function getParam() {
        let params = [];
        let
            teamKey = $('#search-team').val();
        if (teamKey && teamKey > 0) {
            params.push('teamKey=' + encodeURIComponent(teamKey));
        }

        let month = $('#search-month').val();
        if (month && configMonths.includes(month)) {
            params.push('month=' + encodeURIComponent(month));
        }
        let year = $('#search-year').val();
        if (year && year >= 2021 && year <= 2030 && !isNaN(year)) {
            params.push('year=' + encodeURIComponent(year));
        }
        window.location.href = params.length > 0 ? "{{ route('admin.stats') }}?" + params.join('&') : "{{ route('admin.stats') }}";
    }

    $(document).ready(function () {
        $('#search-team, #search-month, #search-year').on('change', getParam);

        if (jQuery("#apex-mixed-chart").length) {
            var data = {!! isset($chart_data) ? json_encode($chart_data) : null !!};
            var currentYear = new Date().getFullYear();

            options = {
                chart: {
                    height: 350,
                    type: "line",
                    stacked: !1,
                },
                stroke: {
                    width: [0, 2, 5, 4],
                    curve: "smooth"
                },
                plotOptions: {
                    bar: {
                        columnWidth: "50%"
                    }
                },
                colors: [],
                series: [],
                fill: {
                    opacity: [.85, .25, 1],
                    gradient: {
                        inverseColors: !1,
                        shade: "light",
                        type: "vertical",
                        opacityFrom: .85,
                        opacityTo: .55,
                        stops: [0, 100, 100, 100]
                    }
                },
                labels: [],
                markers: {
                    size: 0
                },
                xaxis: {
                    type: "datetime",
                    min: new Date(currentYear, 0).getTime(),
                    max: new Date(currentYear, 11, 31).getTime()
                },
                yaxis: {
                    min: 0
                },
                tooltip: {
                    shared: !0,
                    intersect: !1,
                    y: {
                        formatter: function (e) {
                            return void 0 !== e ? "$ " + e.toFixed(0) : e
                        }
                    }
                },
                legend: {
                    labels: {
                        useSeriesColors: !0
                    },
                    markers: {
                        customHTML: [function () {
                            return ""
                        }, function () {
                            return ""
                        }, function () {
                            return ""
                        }, function () {
                            return ""
                        }]
                    }
                }
            };
            if (data) {
                if (data.net_sales && data.net_sales.length > 0) {
                    options.series.push({
                        name: "Net Sales",
                        type: "column",
                        data: data.net_sales
                    });
                    options.colors.push("#0c7ce6");
                }
                if (data.gross_sales && data.gross_sales.length > 0) {
                    options.series.push({
                        name: "Gross Sales",
                        type: "area",
                        data: data.gross_sales
                    });
                    options.colors.push("#1cbfd0");
                }
                if (data.spending && data.spending.length > 0) {
                    options.series.push({
                        name: "Spending",
                        type: "line",
                        data: data.spending
                    });
                    options.colors.push("#ff9948");
                }
                if (data.charge_back_refund && data.charge_back_refund.length > 0) {
                    options.series.push({
                        name: "Charge Back / Refund",
                        type: "line",
                        data: data.charge_back_refund
                    });
                    options.colors.push("#ee2558");
                }
                if (data.labels && data.labels.length > 0) {
                    options.labels = data.labels;
                }
            }
            (chart = new ApexCharts(document.querySelector("#apex-mixed-chart"), options)).render()
        }
    });
</script>
