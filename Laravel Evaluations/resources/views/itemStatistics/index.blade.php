@extends('layouts.app')

@section('content')
    <div class="header-content">
        <div class="tag-name">
            Item Statistics
        </div>
    </div>
    <div class="box">
        <div class="box-body">

            <table id="item-statistics-table" class="table responsive nowrap ui celled">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Test Code</th>
                    <th>Test Type</th>
                    <th>Item Type</th>
                    <th>Item Level</th>
                    <th>Question</th>
                    <th>Mean Time Spent</th>
                    <th>Facility</th>
                    <th>Discrimination</th>
                    <th>Point Biser. Orient.</th>
                </tr>
                </thead>

                {{--<tbody>--}}
                {{--@foreach($data as $question)--}}
                    {{--<tr>--}}
                        {{--<td>{{$loop->index + 1}}</td>--}}
                        {{--<td>@if ($question['code']) {{$question['code']}} @else - @endif</td>--}}
                        {{--<td>{{$question['language_paper_types']['paper_types']['name']}}</td>--}}
                        {{--<td>@if ($question['language_use_type']) {{$languageUseTypes[$question['language_use_type']]}} @else--}}
                                {{--- @endif</td>--}}
                        {{--<td>@if ($question['level']) {{$question['level']['name']}} @else - @endif</td>--}}
                        {{--<td>@if ($question['q_type']) Q{{$question['q_type']}} @else - @endif</td>--}}
                        {{--<td>{{$question['mean']}}</td>--}}
                        {{--<td>{{$question['facility']}}</td>--}}
                        {{--<td>{{$question['discrimination']}}</td>--}}
                        {{--<td>@if($question['discrimination'] > 0.5) + @else - @endif </td>--}}
                    {{--</tr>--}}
                {{--@endforeach--}}
                {{--</tbody>--}}
            </table>

        </div>
    </div>
@endsection

@section('footer')
    <script>
        var languageUseTypes = JSON.parse('{!! json_encode($languageUseTypes) !!}');

        function generateStatisticsTable() {

            var dt = $('#item-statistics-table').DataTable({
                destroy: true,
                paging: true,
                lengthChange: false,
                pageLength: 10,
                searching: false,
                ordering: true,
                info: true,
                autoWidth: true,
                processing: true,
                serverSide: true,
                responsive: true,
                columns: [
                    {
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'code',
                        name: 'code',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'language_paper_types.paper_types.name',
                        name: 'language_paper_types.paper_types.name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: null,
                        name: 'language_use_type',
                        orderable: false,
                        searchable: false,
                        render: function(question) {
                            return (question['language_use_type']) ? "Q" + languageUseTypes[question['language_use_type']] : "-";
                        }
                    },
                    {
                        data: null,
                        name: 'level.name',
                        orderable: false,
                        searchable: false,
                        render: function(question) {
                            if (question.level && question.level.name) {
                                return question.level.name;
                            }

                            return "-";
                        }
                    },
                    {
                        data: null,
                        name: 'q_type',
                        orderable: false,
                        searchable: false,
                        render: function(question) {
                            return (question["q_type"]) ? "Q" + question['q_type'] : "-";
                        }
                    },
                    {
                        data: 'mean',
                        name: 'mean',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'facility',
                        name: 'facility',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'discrimination',
                        name: 'discrimination',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: null,
                        name: 'pbo',
                        orderable: false,
                        searchable: false,
                        render: function(question) {
                            return (question.discrimination && question.discrimination > 0.5) ? "+" : "-";
                        }
                    }
                ],
                ajax: {
                    url: '/item-statistics/get-statistics',
                    type: "GET",
                    data: {

                    }
                }, createdRow: function createdRow(row, data) {
                    $(row).attr('data-id', data.id);
                },
                "fnDrawCallback": function ()
                {
                    var paginateRow = $('.dataTables_paginate');
                    var pageCount = Math.ceil((this.fnSettings().fnRecordsDisplay()) / this.fnSettings()._iDisplayLength);
                    if (pageCount > 1) {
                        paginateRow.css("display", "block");
                    } else {
                        paginateRow.css("display", "none");
                    }


                }
            });
            return dt;

        }
        $(document).ready(function() {
            generateStatisticsTable();
        });
    </script>
@endsection