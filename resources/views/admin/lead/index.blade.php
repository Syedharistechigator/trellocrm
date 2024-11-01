@extends('admin.layouts.app')@section('cxmTitle', 'Lead')

@section('content')
    @push('css')
        <style>
            .brand-icon object {
                display: inline-block;
                max-width: 120px;
                height: 30px;
                background-size: contain;
                background-repeat: no-repeat;
                background-position: center;
            }
        </style>
    @endpush
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Lead List</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                            <li class="breadcrumb-item">Leads</li> <li class="breadcrumb-item active"> List</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
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
                                        <form id="searchForm">
                                            @csrf
                                            <div class="row clearfix">
                                                <div class="col-lg-4 col-md-6">
                                                    <label>Team</label>
                                                    <select id="team" name="teamKey" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Team" data-live-search="true">
                                                        <option value="0">All Team Leads</option>
                                                        @foreach($teams as $team)
                                                            <option value="{{$team->team_key}}" {{$teamKey == $team->team_key ? "selected" : "" }} data-team="{{$team->team_key}}">{{$team->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-lg-4 col-md-6">
                                                    <label>Brands</label>
                                                    <select id="brand" name="brandKey" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Brand" data-live-search="true">
                                                        <option value="0">All Brand Leads</option>
                                                        @foreach($brands as $brand)
                                                            <option value="{{$brand->brand_key}}" {{$brandKey == $brand->brand_key ? "selected" : "" }} data-brand="{{$brand->brand_key}}">{{$brand->name . ' - ' . $brand->brand_key}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-lg-4 col-md-6">
                                                    <label>Select Date Range</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <label class="input-group-text" for="date-range"><i class="zmdi zmdi-calendar"></i></label>
                                                        </div>
                                                        <input type="text" id="date-range" name="dateRange" class="form-control cxm-date-range-picker">
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
                                <!-- <table id="BrandTable" class="table table-hover product_item_list c_table theme-color mb-0"> -->
                                <form method="POST" action="/deleteLeads">
                                    @csrf
                                    <input type="submit" value="Delete Leads" class="btn btn-success">
                                    <table id="LeadTable" class="table table-striped table-hover theme-color js-exportable" xdata-sorting="false">
                                        <thead>
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th>ID #</th>
                                            <th data-orderable="false">Title</th>
                                            <th data-orderable="false">Contact</th>
                                            <th data-orderable="false">Date</th>
                                            <th data-breakpoints="sm xs" data-orderable="false" class="text-center">Brand</th>
                                            <th data-breakpoints="sm xs" data-orderable="false" class="text-center">Value</th>
                                            <th class="text-center" data-breakpoints="xs md" data-orderable="false">
                                                Status
                                            </th>
                                            <th class="text-center" data-breakpoints="sm xs md" data-orderable="false">
                                                Action
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($leads as $lead)
                                            <tr>
                                                <td class="align-middle">
                                                    <input type="checkbox" name="ids[{{$lead->id}}]" value="{{$lead->id}}">
                                                </td>
                                                <td class="align-middle">{{$lead->id}}</td>
                                                <td class="align-middle">
                                                    <a class="text-warning" href="{{route('lead.show',$lead->id)}}"> {{$lead->title}}</a>
                                                </td>
                                                <td class="align-middle">{{$lead->name}}<br>{{$lead->email}}
                                                    <br>{{$lead->lead_ip}}</td>
                                                <td class="align-middle"><span class="text-muted">{{$lead->created_at->format('j F, Y')}}
                                            <br>{{$lead->created_at->format('h:i:s A')}}
                                            <br>{{$lead->created_at->diffForHumans()}}</span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    @if($lead->getBrand && $lead->getBrandName)
                                                        <span class="brand-icon">
                                                            <object data="{!! $lead->getBrand->logo !!}">
                                                                <img src="{{asset('assets/images/logo-colored.png')}}" alt="{{$lead->getBrandName->name}}" loading="lazy">
                                                            </object>
                                                        </span>
                                                        <br>
                                                        <a href="{{route('brand.edit',[$lead->getBrand->id],'/edit')}}" title="{{$lead->getBrand->brand_url}}">
                                                            <span class="text-muted text-warning">{{$lead->getBrandName->name}}</span>
                                                        </a>
                                                        <br>{{$lead->getBrand->brand_key}}
                                                    @elseif($lead->getBrandName)
                                                        <span class="text-muted">{{$lead->getBrandName->name}}</span>
                                                    @else
                                                        <span class="text-muted">Not found</span>
                                                    @endif
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="text-muted">{{($lead->value) ? '$'.$lead->value.'.00' : '---'}}</span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="badge badge-{{$lead->getStatusColor->leadstatus_color}} rounded-pill">{{$lead->getStatus->status}}</span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    @if($lead->view == '0')
                                                        <a href="javascript:void(0);" class="btn btn-info btn-sm btn-round"><i class="zmdi zmdi-eye-off"></i></a>
                                                    @else
                                                        <a href="javascript:void(0);" class="btn btn-info btn-sm btn-round"><i class="zmdi zmdi-eye"></i></a>
                                                    @endif
                                                    <a title="View" href="{{route('lead.show',$lead->id)}}" class="btn btn-warning btn-sm btn-round"><i class="zmdi zmdi-open-in-new"></i></a>
                                                    <a title="Change Status" data-id="{{$lead->id}}" data-type="confirm" href="javascript:void(0);" class=" btn btn-info btn-sm btn-round statusChange" data-toggle="modal" data-target="#changeStatusModal">
                                                        <i class="zmdi zmdi-settings"></i> </a>
                                                    {{--  --}}
                                                    <a title="Comments" data-id="{{$lead->id}}" href="#" class=" btn btn-neutral btn-sm btn-round LeadComments" data-toggle="modal" data-target="#cxmCommentsModal">
                                                        <i class="zmdi zmdi-comments text-warning"></i> </a>
                                                    {{--  --}}

                                                    @if(Auth::guard('admin')->user()->type == 'super')
                                                        <a title="Delete" data-id="{{$lead->id}}" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton"><i class="zmdi zmdi-delete"></i></a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
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
                    <a class="btn btn-warning btn-sm rounded-pill" data-toggle="collapse" href="#cxmCollapseAddComment" role="button" aria-expanded="false" aria-controls="cxmCollapseAddComment"><i class="zmdi zmdi-plus"></i> Add Comments</a>
                </div>
                <div class="modal-body">
                    <div class="collapse" id="cxmCollapseAddComment">
                        <div class="p-2 l-amber">
                            <form id="lead_comments_form">
                                <input type="hidden" name="lead_id" value="" id="leadId">
                                <input type="hidden" name="type" value="admin">
                                <textarea name="comment" class="form-control bg-light border-warning" placeholder="Type Comment."></textarea>
                                <div class="row justify-content-end">
                                    <div class="col-md-4">
                                        <button class="btn btn-warning btn-sm btn-block rounded-pill">
                                            <i class="zmdi zmdi-comment"></i> Save Comment
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
    {{--  --}}


    <!-- Modal Dialogs ====== -->
    <!-- Default Size -->
    <div class="modal fade" id="changeStatusModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Change Status</h4>
                    <input type="hidden" id="status_hdn" class="form-control" name="status_hdn" value="">
                </div>
                <div class="modal-body">
                    <select id="lead-status" name="status" class="form-control show-tick ms select2" data-placeholder="Select" required>
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

    <!-- https://www.daterangepicker.com/ -->
    <script src="{{ asset('assets/plugins/momentjs/moment.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    @include('admin.lead.script')

    <script>
        function getParam() {
            window.location.href = "{{ route('admin.leads.index') }}?brandKey=" + encodeURIComponent($('#brand').val()) + "&teamKey=" + encodeURIComponent($('#team').val()) + "&dateRange=" + encodeURIComponent($('#date-range').val());
        }

        $(function () {
            $(document).ready(function () {
                $('#LeadTable').DataTable().destroy();
                $('#LeadTable').DataTable({
                    dom: 'lBfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    order: [[0, 'desc']]
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

                $('#team, #brand').on('change', getParam);
            });
        });
    </script>
@endpush
