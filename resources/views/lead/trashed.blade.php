@extends('admin.layouts.app')

@section('cxmTitle', 'Lead')

@section('content')
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Trashed Lead List</h2>

                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">Trashed Leads</li>
                        <li class="breadcrumb-item active"> List</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    @include('includes.admin.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-12">


                    <div class="card">
                        <div class="table-responsive">
                            <!-- <table id="BrandTable" class="table table-hover product_item_list c_table theme-color mb-0"> -->
                            <table id="LeadTable" class="table table-striped table-hover theme-color js-exportable" xdata-sorting="false">
                                <thead>
                                    <tr>
                                        <th>ID #</th>
                                        <th>Title</th>
                                        <th>Contact</th>
                                        <th>Date</th>
                                        <th data-breakpoints="sm xs">Brand</th>
                                        <th data-breakpoints="sm xs">Value</th>
                                        <th class="text-center" data-breakpoints="xs md">Status</th>
                                        <th class="text-center" data-breakpoints="sm xs md">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leadsdata as $lead)
                                    <tr>
                                        <td class="align-middle">{{$lead->id}}</td>
                                        <td class="align-middle"><a class="text-warning" href="javascript:void(0);"> {{$lead->title}}</a></td>
                                        <td class="align-middle">{{$lead->name}}<br>{{$lead->email}}<br>{{$lead->lead_ip}}</td>
                                        <td class="align-middle"><span class="text-muted">{{$lead->created_at->format('j F, Y')}}</span></td>
                                        <td class="align-middle"><span class="text-muted">{{$lead->brandName}}</span></td>
                                        <td class="align-middle"><span class="text-muted">{{($lead->value) ? '$'.$lead->value.'.00' : '---'}}</span></td>
                                        <td class="text-center align-middle"><span class="badge badge-{{$lead->statusColor}} rounded-pill">{{$lead->status}}</span></td>
                                        <td class="text-center align-middle">




                                            <a title="Restore" data-id="{{$lead->id}}" data-type="confirm" href="javascript:void(0);" class="btn btn-warning btn-sm btn-round leadRestoreButton"><i class="zmdi zmdi-refresh"></i></a>

                                            <a title="Delete" data-id="{{$lead->id}}" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round leadDelButton"><i class="zmdi zmdi-delete"></i></a>


                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

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
    @include('admin.lead.script')

    {{--
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}" />
    <script src="{{ asset('assets/plugins/momentjs/moment.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}"></script>
    <script>
    $('#month-data').bootstrapMaterialDatePicker({
        time: false,
        clearButton: true,
        format : 'YYYY-MM-DD',
        okText: 'Select',
    });
    </script>
    --}}

<!-- https://www.daterangepicker.com/ -->
<script src="{{ asset('assets/plugins/momentjs/moment.js') }}"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
    //date range
    $(".cxm-date-range-picker").daterangepicker({
        // singleDatePicker: true,
        opens: "left",
        locale: {
            format: 'YYYY-MM-DD'
        },
        // forceUpdate: true,
        // orientation: 'right',
        ranges: {
            'Last 245 Days': [moment().subtract(244, 'days'), moment()],
            'Last 3 Years': [moment().subtract(3, 'years').add(1, 'day'), moment()]
        }
    });
</script>

@endpush
