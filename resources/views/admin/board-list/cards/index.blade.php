@extends('admin.layouts.app')@section('cxmTitle', 'Board List Card')

@section('content')
    <style>
        .img-thumbnail {
            max-width: none !important;
            height: 60px !important;
            padding: 0px;
        }
    </style>
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Board List Card</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i
                                        class="zmdi zmdi-home"></i> {{ config('app.home_name') }}
                                </a></li>
                            <li class="breadcrumb-item">Board List Cards</li>
                            <li class="breadcrumb-item active"> List</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
{{--                        <a href="{{ route('admin.board.list.create') }}" class="btn btn-success btn-icon rounded-circle"--}}
{{--                           type="button"><i class="zmdi zmdi-plus"></i></a>--}}
                        @include('includes.admin.cxm-top-right-toggle-btn')
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="body">
                                <form id="searchForm">
                                    @csrf
                                    <div class="row clearfix">
                                        <div class="col-lg-4 col-md-6">
                                            <label for="team">Team</label>
                                            <select id="team" name="teamKey" class="form-control cxm-live-search-fix"
                                                    data-icon-base="zmdi" data-tick-icon="zmdi-check"
                                                    data-show-tick="true"
                                                    title="Select Team" data-live-search="true"
                                                    data-live-search-id="team-search">
                                                <option value="0" {{$teamKey == 0 ? "selected " : "" }}>All Teams</option>
                                                @foreach($teams as $team)
                                                    <option value="{{$team->team_key}}"
                                                            {{$teamKey == $team->team_key ? "selected " : "" }} data-team="{{$team->team_key}}">{{$team->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <label for="department">Department</label>
                                            <select id="department" name="departmentKey" class="form-control cxm-live-search-fix"
                                                    data-icon-base="zmdi" data-tick-icon="zmdi-check"
                                                    data-show-tick="true"
                                                    title="Select Department" data-live-search="true"
                                                    data-live-search-id="team-search">
                                                <option value="0" {{$departmentKey == 0 ? "selected " : "" }}>All Departments</option>
                                                @foreach($departments as $department)
                                                    <option value="{{$department->id}}"
                                                            {{$departmentKey == $department->id ? "selected " : "" }} data-team="{{$department->id}}">{{$department->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <label for="date-range">Select Date Range</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text" for="date-range"><i
                                                            class="zmdi zmdi-calendar"></i></label>
                                                </div>
                                                <input type="text" id="date-range" name="dateRange"
                                                       class="form-control cxm-date-range-picker">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="table-responsive">
                        <table id="BoardListCardTable"
                               class="table table-striped table-hover theme-color xjs-exportable"
                               xdata-sorting="false">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Trello ID</th>
                                <th class="text-nowrap">Cover Image</th>
                                <th>Department</th>
                                <th>Board List</th>
                                <th>Team</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Priority</th>
                                <th>Position</th>
                                <th class="text-nowrap">Date</th>
                                <th data-breakpoints="xs md">Status</th>
                                <th class="text-center" data-breakpoints="sm xs md">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($board_list_cards as $board_list_card)
                                <tr>
                                    <td class="align-middle text-nowrap">{{$board_list_card->id}}
                                        - {{$board_list_card->code}}</td>
                                    <td class="align-middle">{{$board_list_card->trello_id}}</td>
                                    <td class="align-middle" style="padding: 0px !important;">
                                        @if($board_list_card->cover_image)
                                            <object
                                                data="{{ $cover_image_url_trait($board_list_card, 'thumbnail') }}"
                                                type="image/jpeg"
                                                height="60px"
                                                width="120px"
                                                class="img-thumbnail">
                                                <img
                                                    src="{{ $cover_image_url_trait($board_list_card, 'thumbnail') }}"
                                                    alt="{{ $board_list_card->title }}"
                                                    style="height:60px; width:120px;"
                                                    loading="lazy">
                                            </object>
                                    @endif
                                    </td>
                                    <td class="align-middle text-nowrap">
                                        @if(isset($board_list_card->getBoardList->getDepartment))
                                            <a href="{{ route('admin.department.index') }}?search={{ $board_list_card->getBoardList->getDepartment->name }}"
                                               class="text-warning">{{ $board_list_card->getBoardList->getDepartment->name}}</a>
                                        @endif
                                    </td>
                                    <td class="align-middle text-nowrap">
                                        @if(isset($board_list_card->getBoardList))
                                            <a href="{{ route('admin.board.list.index') }}?search={{ $board_list_card->getBoardList->title }}"
                                               class="text-warning">{{ $board_list_card->getBoardList->title}}</a>
                                        @endif
                                    </td>
                                    <td class="align-middle text-nowrap">
                                        @if(isset($board_list_card->getTeam))
                                            <a href="{{ route('team.index') }}?search={{ $board_list_card->team_key }}"
                                               class="text-warning">{{ $board_list_card->getTeam->name}}</a>
                                        @endif
                                    </td>
                                    <td class="align-middle text-nowrap">{{$board_list_card->title}}</td>
                                    <td class="align-middle">{{Str::limit(strip_tags($board_list_card->description), 20, '...')}}</td>
                                    <td class="align-middle">
                                        @if($board_list_card->priority  == 1)
                                            <span class="badge badge-success rounded-pill">Low</span>
                                        @elseif($board_list_card->priority  == 2)
                                            <span class="badge badge-warning rounded-pill">Medium</span>
                                        @else
                                            <span class="badge badge-danger rounded-pill">High</span>
                                        @endif

                                    </td>
                                    <td class="align-middle">{{$board_list_card->position}}</td>
                                    <td class="align-middle text-nowrap">
                                        Start Date : {{$board_list_card->start_date }}
                                        - {{$board_list_card->is_checked_start_date ? "checked" : "" }}
                                        <br>
                                        Due Date : {{$board_list_card->due_date }}
                                        - {{$board_list_card->is_checked_due_date ? "checked" : "" }}
                                    </td>
                                    <td class="align-middle">
                                        <div class="custom-control custom-switch">
                                            <input data-id="{{$board_list_card->id}}" type="checkbox"
                                                   class="custom-control-input change-status"
                                                   id="customSwitch{{$board_list_card->id}}" {{ $board_list_card->status ? 'checked' : '' }}>
                                            <label class="custom-control-label"
                                                   for="customSwitch{{$board_list_card->id}}"></label>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <a title="Edit"
                                           href="{{route('admin.board.list.cards.edit',[$board_list_card->id],'/edit')}}"
                                           class="btn btn-warning btn-sm btn-round"><i
                                                class="zmdi zmdi-edit"></i></a>
                                        {{--                                                <a title="Delete" data-id="{{$board_list_card->id}}" data-type="confirm"--}}
                                        {{--                                                   href="javascript:void(0);"--}}
                                        {{--                                                   class="btn btn-danger btn-sm btn-round delButton"><i--}}
                                        {{--                                                        class="zmdi zmdi-delete"></i></a>--}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('cxmScripts')
    <script src="{{ asset('assets/plugins/momentjs/moment.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    @include('admin.board-list.cards.script')
    <script>
        function getParam() {
            var departmentKey = $('#department').val();
            var teamKey = $('#team').val();
            var dateRange = $('#date-range').val();
            var queryParams = [];
            if (departmentKey) {
                queryParams.push('departmentKey=' + encodeURIComponent(departmentKey));
            }
            if (teamKey) {
                queryParams.push('teamKey=' + encodeURIComponent(teamKey));
            }
            if (dateRange) {
                queryParams.push('dateRange=' + encodeURIComponent(dateRange));
            }
            window.location.href = "{{ route('admin.board.list.cards.index') }}" + (queryParams.length ? '?' + queryParams.join('&') : '');
        }


        $(function () {
            ['#team', '#department',].forEach(function (selector) {
                var parentId = $(selector).attr('id');
                $(selector).siblings('.dropdown-menu').find('input[type="text"]').attr('id', parentId + '-search');
            });

            $(document).ready(function () {
                $('#BoardListCardTable').DataTable({
                    dom: 'lBfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    order: [[0, 'desc']],
                    scrollX: true,
                    initComplete: function () {
                        $('#BoardListCardTable_filter input').attr('id', 'BoardListCardTable_searchInput');
                    }
                });

                var dateRangePicker = $(".cxm-date-range-picker");
                var initialStartDate = moment("{{ $fromDate }}", 'YYYY-MM-DD');
                var initialEndDate = moment("{{ $toDate }}", 'YYYY-MM-DD');
                var initialDateRange = initialStartDate.format('YYYY-MM-DD') + ' - ' + initialEndDate.format('YYYY-MM-DD');
                dateRangePicker.daterangepicker({
                    opens: "left",
                    locale: {
                        format: 'YYYY-MM-DD'
                    },
                    ranges: {
                        'Last 245 Days': [moment().subtract(244, 'days'), moment()],
                        'Last 3 Years': [moment().subtract(3, 'years').add(1, 'day'), moment()]
                    },
                    startDate: initialStartDate, // Set the initial start date
                    endDate: initialEndDate,     // Set the initial end date
                });
                dateRangePicker.on('apply.daterangepicker', getParam);
                dateRangePicker.val(initialDateRange);

                $('#team, #department').on('change', getParam);
            });
        });
    </script>

@endpush
