<style>
    .btn-warning {
        background-color: #ff9948 !important; /* Add !important to override any conflicting styles */
    }

    .box.loading_div p {
        margin: 0px 0px 0px 20px;
        color: black;
        font-size: 17px;
    }
    .swal2-close {
        font-size: 24px;
        cursor: pointer;
        position: absolute;
        top: 10px;
        right: 10px;
        font-family: 'Font Awesome 5 Free';
        content: '\f00d'; /* fa-times icon */
        color: #aaa;
    }
</style>
<script>
    $(document).ready(function () {
        var chart = c3.generate({
            bindto: '#chart-area-spline-sracked', // id of chart wrapper
            data: {
                columns: [
                    // each columns data
                    //['data1', 21, 8, 32, 18, 19, 17, 23, 12, 25, 37],
                    [
                        'data1',
                        '<?php echo isset($data['m2']['January'][1]) ? $data['m2']['January'][1] : 0; ?>',
                        '<?php echo isset($data['m2']['February'][1]) ? $data['m2']['February'][1] : 0; ?>',
                        '<?php echo isset($data['m2']['March'][1]) ? $data['m2']['March'][1] : 0; ?>',
                        '<?php echo isset($data['m2']['April'][1]) ? $data['m2']['April'][1] : 0; ?>',
                        '<?php echo isset($data['m2']['May'][1]) ? $data['m2']['May'][1] : 0; ?>',
                        '<?php echo isset($data['m2']['June'][1]) ? $data['m2']['June'][1] : 0; ?>',
                        '<?php echo isset($data['m2']['July'][1]) ? $data['m2']['July'][1] : 0; ?>',
                        '<?php echo isset($data['m2']['August'][1]) ? $data['m2']['August'][1] : 0; ?>',
                        '<?php echo isset($data['m2']['September'][1]) ? $data['m2']['September'][1] : 0; ?>',
                        '<?php echo isset($data['m2']['October'][1]) ? $data['m2']['October'][1] : 0; ?>',
                        '<?php echo isset($data['m2']['November'][1]) ? $data['m2']['November'][1] : 0; ?>',
                        '<?php echo isset($data['m2']['December'][1]) ? $data['m2']['December'][1] : 0; ?>',
                    ],
                        @if(isset($data['expenseYearMonthWiseData']))
                    ['data2',
                        '<?php echo isset($data['expenseYearMonthWiseData']['January'][1]) ? $data['expenseYearMonthWiseData']['January'][1] : 0; ?>',
                        '<?php echo isset($data['expenseYearMonthWiseData']['February'][1]) ? $data['expenseYearMonthWiseData']['February'][1] : 0; ?>',
                        '<?php echo isset($data['expenseYearMonthWiseData']['March'][1]) ? $data['expenseYearMonthWiseData']['March'][1] : 0; ?>',
                        '<?php echo isset($data['expenseYearMonthWiseData']['April'][1]) ? $data['expenseYearMonthWiseData']['April'][1] : 0; ?>',
                        '<?php echo isset($data['expenseYearMonthWiseData']['May'][1]) ? $data['expenseYearMonthWiseData']['May'][1] : 0; ?>',
                        '<?php echo isset($data['expenseYearMonthWiseData']['June'][1]) ? $data['expenseYearMonthWiseData']['June'][1] : 0; ?>',
                        '<?php echo isset($data['expenseYearMonthWiseData']['July'][1]) ? $data['expenseYearMonthWiseData']['July'][1] : 0; ?>',
                        '<?php echo isset($data['expenseYearMonthWiseData']['August'][1]) ? $data['expenseYearMonthWiseData']['August'][1] : 0; ?>',
                        '<?php echo isset($data['expenseYearMonthWiseData']['September'][1]) ? $data['expenseYearMonthWiseData']['September'][1] : 0; ?>',
                        '<?php echo isset($data['expenseYearMonthWiseData']['October'][1]) ? $data['expenseYearMonthWiseData']['October'][1] : 0; ?>',
                        '<?php echo isset($data['expenseYearMonthWiseData']['November'][1]) ? $data['expenseYearMonthWiseData']['November'][1] : 0; ?>',
                        '<?php echo isset($data['expenseYearMonthWiseData']['December'][1]) ? $data['expenseYearMonthWiseData']['December'][1] : 0; ?>',
                    ]
                    @endif


                ],
                type: 'area-spline', // default type of chart
                groups: [
                    ['data1', 'data2']
                ],
                colors: {
                    'data1': Aero.colors["lime"],
                    'data2': Aero.colors["teal"],
                },
                names: {
                    // name of each serie
                    'data1': 'Revenue',
                    'data2': 'Expense'
                }
            },
            axis: {
                x: {
                    type: 'category',
                    // name of each category
                    categories: [
                        '<?php echo isset($data['m2']['January'][0]) ? $data['m2']['January'][0] : 'January'; ?>',
                        '<?php echo isset($data['m2']['February'][0]) ? $data['m2']['February'][0] : 'February'; ?>',
                        '<?php echo isset($data['m2']['March'][0]) ? $data['m2']['March'][0] : 'March'; ?>',
                        '<?php echo isset($data['m2']['April'][0]) ? $data['m2']['April'][0] : 'April'; ?>',
                        '<?php echo isset($data['m2']['May'][0]) ? $data['m2']['May'][0] : 'May'; ?>',
                        '<?php echo isset($data['m2']['June'][0]) ? $data['m2']['June'][0] : 'June'; ?>',
                        '<?php echo isset($data['m2']['July'][0]) ? $data['m2']['July'][0] : 'July'; ?>',
                        '<?php echo isset($data['m2']['August'][0]) ? $data['m2']['August'][0] : 'August'; ?>',
                        '<?php echo isset($data['m2']['September'][0]) ? $data['m2']['September'][0] : 'September'; ?>',
                        '<?php echo isset($data['m2']['October'][0]) ? $data['m2']['October'][0] : 'October'; ?>',
                        '<?php echo isset($data['m2']['November'][0]) ? $data['m2']['November'][0] : 'November'; ?>',
                        '<?php echo isset($data['m2']['December'][0]) ? $data['m2']['December'][0] : 'December'; ?>',
                    ]
                },
            },
            legend: {
                show: true, //hide legend
            },
            padding: {
                bottom: 0,
                top: 0
            },
        });
    });
    <?php if (isset($data['statusValue'])){ ?>
    $(document).ready(function () {
        var chart = c3.generate({
            bindto: '#chart-pie', // id of chart wrapper
            data: {
                columns: <?php echo isset($data['statusValue']) ? $data['statusValue'] : ''; ?>,
                type: 'pie', // default type of chart
                colors: {
                    'data1': Aero.colors["default"],
                    'data2': Aero.colors["success"],
                    'data3': Aero.colors["primary"],
                    'data4': Aero.colors["info"],
                    'data5': Aero.colors["dark"],
                    'data6': Aero.colors["warning"],
                    'data7': Aero.colors["danger"],
                },
                names: <?php echo isset($data['leadstatus']) ? $data['leadstatus'] : ''; ?>
            },
            axis: {},
            legend: {
                show: true, //hide legend
            },
            padding: {
                bottom: 0,
                top: 0
            },
        });

        var chart1 = c3.generate({
            bindto: '#chart-pie-1', // id of chart wrapper
            data: {
                columns: <?php echo isset($data['statusValue']) ? $data['statusValue'] : ''; ?>,
                type: 'pie', // default type of chart
                colors: {
                    'data1': Aero.colors["default"],
                    'data2': Aero.colors["success"],
                    'data3': Aero.colors["primary"],
                    'data4': Aero.colors["info"],
                    'data5': Aero.colors["dark"],
                    'data6': Aero.colors["warning"],
                    'data7': Aero.colors["danger"],
                },
                names: <?php echo isset($data['leadstatus']) ? $data['leadstatus'] : ''; ?>
            },
            axis: {},
            legend: {
                show: true, //hide legend
            },
            padding: {
                bottom: 0,
                top: 0
            },
        });
    });
    <?php } ?>

    $(document).ready(function () {
        var chart = c3.generate({
            bindto: '#chart-donut', // id of chart wrapper
            data: {
                columns: [
                    // each columns data
                    ['data1', 63],
                    ['data2', 37]
                ],
                type: 'donut', // default type of chart
                colors: {
                    'data1': Aero.colors["primary"],
                    'data2': Aero.colors["cyan"]
                },
                names: {
                    // name of each serie
                    'data1': 'Maximum',
                    'data2': 'Minimum'
                }
            },
            axis: {},
            legend: {
                show: true, //hide legend
            },
            padding: {
                bottom: 0,
                top: 0
            },
        });
    });

    // Refund Bar Graph
    $(document).ready(function () {
        var chart = c3.generate({
            bindto: '#chart-bar', // id of chart wrapper
            data: {
                columns: [
                    // each columns data
                    [
                        'data1',
                        '<?php echo isset($data['refundYearMonthWiseData']['January'][1]) ? $data['refundYearMonthWiseData']['January'][1] : 0; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['February'][1]) ? $data['refundYearMonthWiseData']['February'][1] : 0; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['March'][1]) ? $data['refundYearMonthWiseData']['March'][1] : 0; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['April'][1]) ? $data['refundYearMonthWiseData']['April'][1] : 0; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['May'][1]) ? $data['refundYearMonthWiseData']['May'][1] : 0; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['June'][1]) ? $data['refundYearMonthWiseData']['June'][1] : 0; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['July'][1]) ? $data['refundYearMonthWiseData']['July'][1] : 0; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['August'][1]) ? $data['refundYearMonthWiseData']['August'][1] : 0; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['September'][1]) ? $data['refundYearMonthWiseData']['September'][1] : 0; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['October'][1]) ? $data['refundYearMonthWiseData']['October'][1] : 0; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['November'][1]) ? $data['refundYearMonthWiseData']['November'][1] : 0; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['December'][1]) ? $data['refundYearMonthWiseData']['December'][1] : 0; ?>',
                    ],
                    ['data2',
                        '<?php echo isset($data['chargebackYearMonthWiseData']['January'][1]) ? $data['chargebackYearMonthWiseData']['January'][1] : 0; ?>',
                        '<?php echo isset($data['chargebackYearMonthWiseData']['February'][1]) ? $data['chargebackYearMonthWiseData']['February'][1] : 0; ?>',
                        '<?php echo isset($data['chargebackYearMonthWiseData']['March'][1]) ? $data['chargebackYearMonthWiseData']['March'][1] : 0; ?>',
                        '<?php echo isset($data['chargebackYearMonthWiseData']['April'][1]) ? $data['chargebackYearMonthWiseData']['April'][1] : 0; ?>',
                        '<?php echo isset($data['chargebackYearMonthWiseData']['May'][1]) ? $data['chargebackYearMonthWiseData']['May'][1] : 0; ?>',
                        '<?php echo isset($data['chargebackYearMonthWiseData']['June'][1]) ? $data['chargebackYearMonthWiseData']['June'][1] : 0; ?>',
                        '<?php echo isset($data['chargebackYearMonthWiseData']['July'][1]) ? $data['chargebackYearMonthWiseData']['July'][1] : 0; ?>',
                        '<?php echo isset($data['chargebackYearMonthWiseData']['August'][1]) ? $data['chargebackYearMonthWiseData']['August'][1] : 0; ?>',
                        '<?php echo isset($data['chargebackYearMonthWiseData']['September'][1]) ? $data['chargebackYearMonthWiseData']['September'][1] : 0; ?>',
                        '<?php echo isset($data['chargebackYearMonthWiseData']['October'][1]) ? $data['chargebackYearMonthWiseData']['October'][1] : 0; ?>',
                        '<?php echo isset($data['chargebackYearMonthWiseData']['November'][1]) ? $data['chargebackYearMonthWiseData']['November'][1] : 0; ?>',
                        '<?php echo isset($data['chargebackYearMonthWiseData']['December'][1]) ? $data['chargebackYearMonthWiseData']['December'][1] : 0; ?>',
                    ]
                ],
                type: 'bar', // default type of chart
                colors: {
                    'data1': Aero.colors["red"],
                    'data2': Aero.colors["orange"]
                },
                names: {
                    // name of each serie
                    'data1': 'Refund',
                    'data2': 'Charge Back'
                }
            },
            axis: {
                x: {
                    type: 'category',
                    // name of each category
                    categories: [
                        '<?php echo isset($data['refundYearMonthWiseData']['January'][0]) ? $data['refundYearMonthWiseData']['January'][0] : 'January'; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['February'][0]) ? $data['refundYearMonthWiseData']['February'][0] : 'February'; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['March'][0]) ? $data['refundYearMonthWiseData']['March'][0] : 'March'; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['April'][0]) ? $data['refundYearMonthWiseData']['April'][0] : 'April'; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['May'][0]) ? $data['refundYearMonthWiseData']['May'][0] : 'May'; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['June'][0]) ? $data['refundYearMonthWiseData']['June'][0] : 'June'; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['July'][0]) ? $data['refundYearMonthWiseData']['July'][0] : 'July'; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['August'][0]) ? $data['refundYearMonthWiseData']['August'][0] : 'August'; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['September'][0]) ? $data['refundYearMonthWiseData']['September'][0] : 'September'; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['October'][0]) ? $data['refundYearMonthWiseData']['October'][0] : 'October'; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['November'][0]) ? $data['refundYearMonthWiseData']['November'][0] : 'November'; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['December'][0]) ? $data['refundYearMonthWiseData']['December'][0] : 'December'; ?>',
                    ]
                },
            },
            bar: {
                width: 16
            },
            legend: {
                show: true, //hide legend
            },
            padding: {
                bottom: 0,
                top: 0
            },
        });
    });
</script>
