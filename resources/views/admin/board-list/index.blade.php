@extends('admin.layouts.app')@section('cxmTitle', 'Board List')

@section('content')
    @push('css')
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">

    @endpush
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Board List</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}
                                </a></li> <li class="breadcrumb-item">Board</li>
                            <li class="breadcrumb-item active"> List</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <a href="{{ route('admin.board.list.create') }}" class="btn btn-success btn-icon rounded-circle" type="button"><i class="zmdi zmdi-plus"></i></a>
                        @include('includes.admin.cxm-top-right-toggle-btn')
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-4 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="body">
                                <form id="searchForm">
                                    @csrf
                                    <div class="row clearfix">
                                        <div class="col-lg-12 col-md-6">
                                            <label for="department">Department</label>
                                            <select id="department" name="departmentKey" class="form-control cxm-live-search-fix"
                                                    data-icon-base="zmdi" data-tick-icon="zmdi-check"
                                                    data-show-tick="true"
                                                    title="Select Department" data-live-search="true"
                                                    data-live-search-id="department-search">
                                                <option value="0" {{$department_id == 0 ? "selected " : "" }}>All Departments</option>
                                                @foreach($departments as $department)
                                                    <option value="{{$department->id}}"
                                                            {{$department_id == $department->id ? "selected " : "" }} data-department="{{$department->id}}">{{$department->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="table-responsive">
                        <table id="BoardListTable" class="table table-striped table-hover theme-color xjs-exportable" xdata-sorting="false">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Department</th>
                                <th>Title</th>
                                {{--                                        <th>Team</th>--}}
                                {{--                                        <th>Team Count</th>--}}
                                <th>Position</th>
                                <th data-breakpoints="xs md">Status</th>
                                <th class="text-center" data-breakpoints="sm xs md">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($board_lists as $board_list)
                                <tr id="{{$board_list->id}}" data-id="{{$board_list->id}}">
                                    <td class="align-middle">{{$board_list->id}}</td>
                                    <td class="align-middle">{{optional($board_list->getDepartment)->name??""}}</td>
                                    <td class="align-middle">{{$board_list->title}}</td>
                                    {{--                                        <td class="align-middle">@foreach($board_list->getTeams as $team)--}}
                                    {{--                                                {{ $team->name }}--}}
                                    {{--                                                @if (!$loop->last)--}}
                                    {{--                                                    ,--}}
                                    {{--                                                @endif--}}
                                    {{--                                            @endforeach</td>--}}
                                    {{--                                        <td class="align-middle">{{$board_list->getTeams ? $board_list->getTeams->count():""}}</td>--}}
                                    <td class="align-middle">{{$board_list->position}}</td>
                                    <td class="align-middle">
                                        <div class="custom-control custom-switch">
                                            <input data-id="{{$board_list->id}}" type="checkbox" class="custom-control-input change-status" id="customSwitch{{$board_list->id}}" {{ $board_list->status ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="customSwitch{{$board_list->id}}"></label>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <a title="Edit" href="{{route('admin.board.list.edit',[$board_list->id],'/edit')}}" class="btn btn-warning btn-sm btn-round"><i class="zmdi zmdi-edit"></i></a>
                                        <a title="Delete" data-id="{{$board_list->id}}" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton"><i class="zmdi zmdi-delete"></i></a>
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
    @include('admin.board-list.script')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        function getParam() {
            var departmentKey = $('#department').val();
            var queryParams = [];
            if (departmentKey) {
                queryParams.push('departmentKey=' + encodeURIComponent(departmentKey));
            }
            window.location.href = "{{ route('admin.board.list.index') }}" + (queryParams.length ? '?' + queryParams.join('&') : '');
        }

        $(function () {
            ['#department'].forEach(function (selector) {
                var parentId = $(selector).attr('id');
                $(selector).siblings('.dropdown-menu').find('input[type="text"]').attr('id', parentId + '-search');
            });

            $(document).ready(function () {
                $('#BoardListTable').DataTable({
                    dom: 'lBfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    order: [[0, 'desc']],
                    scrollX: false,
                    initComplete: function () {
                        $('#BoardListTable_filter input').attr('id', 'BoardListCardTable_searchInput');
                    }
                });

                $('#BoardListTable tbody').sortable({
                    items: 'tr',
                    helper: function(e, tr) {
                        var $originals = tr.children();
                        var $helper = tr.clone();
                        $helper.children().each(function(index) {
                            $(this).width($originals.eq(index).width());
                        });
                        return $helper;
                    },
                    cursor: 'move',
                    opacity: 0.7,
                    update: function(event, ui) {
                        var rowOrder = $(this).sortable('toArray', { attribute: 'data-id' });
                        // console.log('Updated row order:', rowOrder);

                        // You can also send the new row order to your backend via AJAX if needed
                    }
                });

                $('#department').on('change', getParam);
            });

        });
    </script>

@endpush
