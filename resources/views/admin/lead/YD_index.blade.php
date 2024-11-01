@extends('admin.layouts.app')

@section('cxmTitle', 'Lead')

@section('content')
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Lead List</h2>

                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                            <li class="breadcrumb-item">Leads</li>
                            <li class="breadcrumb-item active"> List</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button"><i
                                class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        @include('includes.admin.cxm-top-right-toggle-btn')
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12">
                        <div class="row clearfix">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="card">
                                    <div class="body">
                                        <div class="row clearfix">
                                            <div class="col-lg-4 col-md-6">
                                                <label>Team</label>
                                                <select id="team" class="form-control cxm-live-search-fix"
                                                        data-icon-base="zmdi" data-tick-icon="zmdi-check"
                                                        data-show-tick="true" title="Please select team"
                                                        data-live-search="true">
                                                    <option value="" disabled>Please select team</option>
                                                    <option value="0">Select none</option>
                                                    @foreach($teams as $team)
                                                        <option value="{{$team->team_key}}"
                                                                data-team="{{$team->team_key}}">{{$team->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <label>Brands</label>
                                                <select id="brand" class="form-control cxm-live-search-fix"
                                                        data-icon-base="zmdi" data-tick-icon="zmdi-check"
                                                        data-show-tick="true" title="Please select brand"
                                                        data-live-search="true">
                                                    <option value="" disabled>Please select brand</option>
                                                    <option value="0">Select none</option>
                                                    @foreach($brands as $brand)
                                                        <option value="{{$brand->brand_key}}"
                                                                data-brand="{{$brand->brand_key}}">{{$brand->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <label>Select Month</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i
                                                                class="zmdi zmdi-calendar"></i></span>
                                                    </div>
                                                    {{--<input type="text" id="month-data" class="form-control">--}}
                                                    <input type="text" id="data-range"
                                                           class="form-control cxm-date-range-picker">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            {{--                            <div class="table-responsive">--}}
                            <div class="">
                                <!-- <table id="BrandTable" class="table table-hover product_item_list c_table theme-color mb-0"> -->
                                <form method="POST" action="/deleteLeads">
                                    @csrf
                                    <input type="submit" value="Delete Leads" class="btn btn-success">
                                    <table id="LeadTable" class="table table-striped table-hover theme-color "
                                           xdata-sorting="false">
                                        <thead>
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th>ID #</th>
                                            <th data-orderable="false">Title</th>
                                            <th data-orderable="false">Contact</th>
                                            <th data-orderable="false">Date</th>
                                            <th data-breakpoints="sm xs" data-orderable="false">Brand</th>
                                            <th data-breakpoints="sm xs" data-orderable="false">Value</th>
                                            <th class="text-center" data-breakpoints="xs md" data-orderable="false">
                                                Status
                                            </th>
                                            <th class="text-center" data-breakpoints="sm xs md" data-orderable="false">
                                                Action
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    {{--  --}}
    <!-- Comments Modal -->
    <style>
        .cxm-comments {
            max-height: 300px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .cxm-comments::-webkit-scrollbar {
            width: 5px;
        }

        .cxm-comments::-webkit-scrollbar-track {
            box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
        }

        .cxm-comments::-webkit-scrollbar-thumb {
            background-color: #ffc107;
            outline: 0px solid slategrey;
        }
    </style>

    <div class="modal fade" id="cxmCommentsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Comments</h4>
                    <a class="btn btn-warning btn-sm rounded-pill" data-toggle="collapse" href="#cxmCollapseAddComment"
                       role="button" aria-expanded="false" aria-controls="cxmCollapseAddComment"><i
                            class="zmdi zmdi-plus"></i> Add Comments</a>
                </div>
                <div class="modal-body">
                    <div class="collapse" id="cxmCollapseAddComment">
                        <div class="p-2 l-amber">
                            <form id="lead_comments_form">
                                <input type="hidden" name="lead_id" value="" id="leadId">
                                <input type="hidden" name="type" value="admin">
                                <textarea name="comment" class="form-control bg-light border-warning"
                                          placeholder="Type Comment."></textarea>
                                <div class="row justify-content-end">
                                    <div class="col-md-4">
                                        <button class="btn btn-warning btn-sm btn-block rounded-pill"><i
                                                class="zmdi zmdi-comment"></i> Save Comment
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <hr>
                    </div>
                    <div class="card border border-warning rounded-0">
                        <h6 class="header px-2 l-amber mb-2"><span class="text-dark">All Comments</span></h6>
                        <div class="body p-0">
                            <div id="lead_comments_data" class="cxm-comments"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="changeStatusModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Change Status</h4>
                    <input type="hidden" id="status_hdn" class="form-control" name="status_hdn" value="">
                </div>


                <div class="modal-body">

                    <select id="lead-status" name="status" class="form-control show-tick ms select2"
                            data-placeholder="Select" required>
                        @foreach($leadsStatus as $status)
                            <option value="{{ $status->id }}">{{ $status->status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" id="changeStatusBtn" class="btn btn-success btn-round">SAVE CHANGES</button>
                    <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('cxmScripts')
    <script src="{{ asset('assets/plugins/momentjs/moment.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        $(".cxm-date-range-picker").daterangepicker({
            // singleDatePicker: true,
            opens: "left",
            locale: {
                format: 'YYYY-MM-DD'
            },
            ranges: {
                'Last 245 Days': [moment().subtract(244, 'days'), moment()],
                'Last 3 Years': [moment().subtract(3, 'years').add(1, 'day'), moment()]
            }
        });
        $(function () {
            var table = $('#LeadTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.lead.YD_index') }}",
                },
                columns: [
                    {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false,},
                    {data: 'id', name: 'id', orderable: true, searchable: true,},
                    {data: 'lead_title', name: 'title', orderable: true, searchable: true,},
                    {data: 'lead_contact', name: 'Lead Contact', orderable: true, searchable: true,
                        render: function(data, type, row, meta) {
                            // Render the HTML for the cell with line breaks
                            return row.name + '<br>' + row.email + '<br>' + row.lead_ip;
                        }},
                    {data: 'created_at', name: 'created_at', orderable: true, searchable: true,},
                    {
                        data: 'get_brand_name',
                        name: 'get_brand_name',
                        orderable: true,
                        searchable: true,
                        render : function (data,type,row,meta) {
                            console.log(data)
                            console.log(type)
                            console.log(row)
                            console.log(meta)
                            return row.get_brand_name;
                        }
                    },
                    {data: 'lead_value', name: 'value', orderable: true, searchable: true,},
                    {data: 'lead_status', name: 'status', orderable: true, searchable: true,},
                    {data: 'action', name: 'action', orderable: true, searchable: true},
                ],
                dom: '<"row"<"col-2"l><"col-2"B><"col-5"><"col-3"f>>rt<"bottom"ip<"clear">>',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                createdRow: function (row, data, index) {
                    console.log(data)
                    $('td', row).eq(7).addClass('text-center');
                    $('td', row).eq(8).addClass('text-right');
                },
                initComplete: function () {
                    this.api().columns().every(function () {
                        var column = this;
                        var input  = document.createElement("input");
                        $(input).appendTo($(column.footer()).empty())
                            .on('change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            });
                    });
                }
            });
            var teamFilter = null;
            var brandFilter = null;
            var dateRangeFilter = null;

            $('#data-range').on('apply.daterangepicker', function () {
                var from_date = $('#data-range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var to_date = $('#data-range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                dateRangeFilter = "from_date=" + from_date + "&to_date=" + to_date;
                applyFilters();
            });
            // search team leads
            $('#team').on('change', function () {
                teamFilter = "team_key=" + $(this).val();
                applyFilters();
            });
            // search brand leads
            $('#brand').on('change', function () {
                brandFilter = "brand_key=" + $(this).val();
                applyFilters();
            });

            function applyFilters() {
                var filters = [];
                if (teamFilter) {
                    filters.push(teamFilter);
                }
                if (brandFilter) {
                    filters.push(brandFilter);
                }
                if (dateRangeFilter) {
                    filters.push(dateRangeFilter);
                }
                var filterString = filters.join('&');
                table.ajax.url("{{ route('admin.lead.YD_index') }}?" + filterString).load();
            }
        });

    </script>
@endpush
