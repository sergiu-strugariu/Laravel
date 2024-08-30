@extends('layouts.pdf')

@section('content')

    <style>
        .panel {
            border: none;
            box-shadow: none;
        }

        .container-chart {
            margin-top: 20px;
            margin-bottom: 0;
        }

        .reference_description {
            background-color: #f0f0f0;
            border-radius: 4px;
            margin: 0 0 20px;
            min-height: 42px;
            padding: 10px 0;
            width: 100%;
        }

        .bshadow.panel > .panel-body .panel {
            box-shadow: 0 0 20px 5px #f0f0f0;
            margin-bottom: 10px;;
        }

        /* Page Breaks */

        /***Always insert a page break before the element***/
        .pb_before {
            page-break-before: always !important;
        }

        /***Always insert a page break after the element***/
        .pb_after {
            page-break-after: always !important;
        }

        /***Avoid page break before the element (if possible)***/
        .pb_before_avoid {
            page-break-before: avoid !important;
        }

        /***Avoid page break after the element (if possible)***/
        .pb_after_avoid {
            page-break-after: avoid !important;
        }

        /* Avoid page break inside the element (if possible) */
        .pbi_avoid {
            page-break-inside: avoid !important;
        }

        .logo-pdf{
            text-align: center;
            margin-bottom: 25px;
        }



        /*#app {*/
            /*width: 1000px; margin: 0 auto; background: white;*/
        /*}*/
        /*body{*/
            /*background: grey;*/
        /*}*/
        /*.pb_before{*/
            /*background: grey none repeat scroll 0 0;*/
            /*height: 20px;*/
            /*padding: 20px 0;*/
        /*}*/
        /*todo: remove this !*/

    </style>

    <div id="view-task">
        <div class="row">

            <div class="col-sm-12 no-padding">

                <div class="col-sm-12 no-padding logo-pdf">
                    <img src="{{ asset('assets/img/logo-200x65.png') }}" alt="">
                </div>


                <div class="col-xs-6">
                    @include('tests.results.partials.candidate-details')
                </div>

                <div class="col-xs-6">
                    @include('tests.results.partials.chart')
                </div>

                <div class="row"></div>
                {{--<div class="row" style="padding: 0 15px;">--}}
                    {{--@hasRole(['administrator', 'master'])--}}
                    {{--<div class="col-xs-12">--}}
                        {{--@include('tests.results.partials.tests-requested')--}}
                    {{--</div>--}}
                    {{--@endHasRole--}}
                {{--</div>--}}


                @include('tests.results.partials.report-speaking')

                @include('tests.results.partials.report-writing')

                @hasRole(['administrator', 'master', 'client', 'css', 'recruiter'])

                <div class="col-sm-12 no-padding">
                    @include('tests.results.partials.test-grades')
                </div>

                @endHasRole

            </div>
        </div>
    </div>
@endsection
@section('footer')
    <script src="{{ url('js/chart.min.js') }}"></script>
    <script>
        var TEST_LANGUAGE_USE_NEW = {{TEST_LANGUAGE_USE_NEW}},
            TEST_LANGUAGE_USE = {{TEST_LANGUAGE_USE}},
            TEST_SPEAKING = {{TEST_SPEAKING}},
            TEST_WRITING = {{TEST_WRITING}},
            TEST_LISTENING = {{TEST_LISTENING}},
            TEST_READING = {{TEST_READING}};

        var pbChecks = document.getElementsByClassName('pb-check');
        var pageHeight = 4150;
        var pageBreaks = 1;

//        for (var i = 0; i < pbChecks.length; i++) {
//            var currentElem = pbChecks[i];
//            var currElemHeight = currentElem.clientHeight;
//            var offsetTop = currentElem.offsetTop;
//            var emptySpace = pageHeight * pageBreaks - offsetTop - currElemHeight;
//
//            if (offsetTop + currElemHeight - emptySpace > pageHeight * pageBreaks) {
//                var newElem = document.createElement('div');
//                newElem.className = 'pb_before';
//                currentElem.appendChild(newElem); // add it to the div
//                pageBreaks++;
//            }
//        }


        {!! file_get_contents(url('js/chart.min.js')) !!}

        var task = {!! $task !!};
        var taskStatus = {!! $task->status !!};
        var taskStatusesColor = {!! $taskStatusesColor !!};

        if (document.getElementById('task-tests-chart')) {
            var papers = {!! $task->completedTests() !!};
            var labels = [];
            var backgroundColors = [];
            var paperGrades = [];
            var label = [];
            var tooltips = {};

            papers.forEach(function (paper, key) {
                labels.push(paper.type.name);
                tooltips[paper.type.name] = paper.report.grade;

                switch (paper.type.id) {
                    case TEST_LANGUAGE_USE_NEW:
                    case TEST_LANGUAGE_USE:
                        backgroundColors.push('#07a8e4');
                        break;
                    case TEST_SPEAKING:
                        backgroundColors.push('#f01f1f');
                        break;
                    case TEST_WRITING:
                        backgroundColors.push('#00b497');
                        break;
                    case TEST_LISTENING:
                        backgroundColors.push('#ff8300');
                        break;
                    case TEST_READING:
                        backgroundColors.push('#782aa9');
                        break;
                    default:
                        backgroundColors.push('#782AA9');
                        break;
                }

                paperGrades.push(paper.report.ability);
            });
        }

        var grades = [
            'Pre-A1', 'A1', 'A2', 'B1', 'B2', 'C1', 'C2', 'N'
        ];

        var ctx = document.getElementById("task-tests-chart").getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: paperGrades,
                    backgroundColor: backgroundColors
                }]
            },
            options: {
                scales: {
                    xAxes: [{
                        gridLines: {
                            color: 'rgba(0, 0, 0, 0)'
                        },
                        ticks: {
                            autoSkip: false
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            min: 0,
                            max: 7,
                            stepSize: 1,
                            beginAtZero: true,
                            callback: function (label, index, labels) {
                                return grades[Math.abs(label)];
                            }
                        }
                    }]
                },

                legend: {
                    display: false
                },
                responsive: false,
                responsiveAnimationDuration: 0,
                tooltips: false,
                hover:false,
                animation: {
                    duration: 1,
                    onComplete: function () {
                        var chartInstance = this.chart,
                                ctx = chartInstance.ctx;
                        ctx.font = Chart.helpers.fontString(17, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'bottom';
                        ctx.fillStyle = 'white';
                        ctx.shadowBlur = 5;
                        ctx.shadowColor = "black";

                        this.data.datasets.forEach(function (dataset, i) {
                            var meta = chartInstance.controller.getDatasetMeta(i);
                            meta.data.forEach(function (bar, index) {
                                var data = tooltips[bar._model.label];
                                ctx.fillText(data, bar._model.x, bar._yScale.bottom - 1 );
                            });
                        });
                    }
                }
            }
        });

    </script>
@endsection