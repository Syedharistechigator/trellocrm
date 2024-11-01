@extends('admin.layouts.app')@section('cxmTitle', 'Payments')

@section('content')
    @push('css')
        <style>
            #admin_direct-payment-Form label {
                margin-bottom: 0px;
            }

            #admin_direct-payment-Form .form-group {
                margin-bottom: 5px;
            }

            #admin_direct-payment-Form .form-group .control-label, .form-group > label {
                font-weight: 600;
                color: #34395e;
                font-size: 13px;
                letter-spacing: 0.5px;
            }

            #admin_direct-payment-Form button.btn.dropdown-toggle.bs-placeholder.btn-simple {
                height: 35px;
            }

            #admin_direct-payment-Form span.filter-option.pull-left {
                font-size: 12px;
            }

            #admin_direct-payment-Form .form-control {
                font-size: 13px;
            }
        </style>
    @endpush
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Payments</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}
                                </a></li> <li class="breadcrumb-item">Sales</li>
                            <li class="breadcrumb-item active"> Payments</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <button class="btn btn-success btn-icon rounded-circle" type="button" data-toggle="modal" data-target="#paymentModal">
                            <i class="zmdi zmdi-plus"></i></button>
                        @include('includes.admin.cxm-top-right-toggle-btn')
                    </div>
                </div>
            </div>
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="body">
                            <form id="searchForm">
                                @csrf
                                <div class="row clearfix">
                                    <div class="col-lg-4 col-md-6">
                                        <label for="team">Team</label>
                                        <select id="team" name="teamKey" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Team" data-live-search="true" data-live-search-id="team-search">
                                            <option value="0">All Teams</option>
                                            @foreach($teams as $team)
                                                <option value="{{$team->team_key}}" {{$teamKey == $team->team_key ? "selected " : "" }}data-team="{{$team->team_key}}">{{$team->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <label for="brand">Brands</label>
                                        <select id="brand" name="brandKey" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Brand" data-live-search="true">
                                            <option value="0">All Brands</option>
                                            @foreach($brands as $brand)
                                                <option value="{{$brand->brand_key}}" {{$brandKey == $brand->brand_key ? "selected " : "" }}data-brand="{{$brand->brand_key}}">{{$brand->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <label for="date-range">Select Date Range</label>
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
                <div class="table-responsive mb-3">
                    @if(Auth::guard('admin')->user()->type == 'super')
                        <table id="LeadTable" class="table table-striped table-hover theme-color xjs-exportable" xdata-sorting="false" xdata-page-size="5" xdata-paging="true" xdata-filtering="false">
                            <thead>
                            <tr>
                                <th>ID#</th>
                                <th>Invoice ID#</th>
                                <th>Invoice Type</th>
                                <th>Brand</th>
                                <th>Client</th>
                                {{--<th>Description</th>--}}
                                <th>Amount</th>
                                <th>Card</th>
                                <th>Card#</th>
                                <th>Expiry</th>
                                <th>CVV</th>
                                <th>Location</th>
                                <th>Gateway</th>
                                <th>Tran ID</th>
                                <th data-breakpoints="sm xs">Date</th>
                                <th class="text-center" data-breakpoints="xs md">Settlement</th>
                                <th class="text-center" data-breakpoints="xs md">Status</th>
                                <th class="text-center" data-breakpoints="sm xs md">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td class="align-middle">{{$payment->id}}</td>
                                    <td class="align-middle">{{$payment->invoice_id}}</td>
                                    <td class="align-middle">{{$payment->getInvoice?$payment->getInvoice->sales_type:""}}</td>
                                    <td class="align-middle">{{$payment->brandName}}</td>
                                    <td class="align-middle">
                                        <a class="text-warning" href="{{route('clientadmin.show',$payment->clientid)}}">{{$payment->name}}</a>
                                    </td>
                                    {{--<td class="align-middle">{{$payment->payment_notes}}</td>--}}
                                    <td class="align-middle">${{$payment->amount}}</td>
                                    <td class="align-middle text-capitalize">@if($payment->card_type != NULL)
                                            {{$payment->card_type}}
                                        @else
                                            ---
                                        @endif</td>
                                    <td class="align-middle">
                                        @if($payment->card_number != NULL)
                                            {{$payment->card_number}}
                                        @else
                                            xxxx
                                        @endif
                                    </td>
                                    <td class="align-middle">@if($payment->card_exp_month != NULL)
                                            {{$payment->card_exp_month}}/{{$payment->card_exp_year}}@else---
                                        @endif</td>
                                    <td class="align-middle">@if($payment->card_cvv != NULL)
                                            {{$payment->card_cvv}}@else---
                                        @endif</td>
                                    <td class="align-middle">
                                        {{$payment->ip}}<br>
                                        {{$payment->city}}, {{$payment->state}}, {{$payment->country}}.<br>
                                    </td>
                                    <td class="align-middle text-nowrap text-capitalize">{{$payment->payment_gateway }}
                                        @if($payment->payment_gateway === 'authorize' && isset($payment->getAuthorizeMerchant->merchant))
                                            <br>
                                            <p style="font-size:12px ">( {{ $payment->getAuthorizeMerchant->merchant}} )</p>
                                        @elseif($payment->payment_gateway === 'Expigate' && isset($payment->getExpigateMerchant->merchant))
                                            <br>
                                            <p style="font-size:12px ">( {{ $payment->getExpigateMerchant->merchant}} )</p>
                                        @endif
                                    </td>
                                    <td class="align-middle">{{$payment->authorizenet_transaction_id}}</td>
                                    <td class="align-middle text-nowrap">{{$payment->created_at->format('j F, Y')}}
                                        <br>{{$payment->created_at->format('h:i:s A')}}
                                        <br>{{$payment->created_at->diffForHumans()}}
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($payment->settlement == 'settled successfully')
                                            <span class="badge badge-success rounded-pill">{{ucfirst($payment->settlement)}}</span>
                                        @elseif($payment->settlement == 'captured pending settlement')
                                            <span class="badge badge-warning rounded-pill">{{ucfirst($payment->settlement)}}</span>
                                        @elseif($payment->settlement == 'voided')
                                            <span class="badge badge-danger rounded-pill">{{ucfirst($payment->settlement)}}</span>
                                        @else
                                            <span class="badge badge-primary rounded-pill">{{ucfirst($payment->settlement)}}</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($payment->payment_status == 1)
                                            <span class="badge badge-success rounded-pill">Success</span>
                                        @elseif($payment->payment_status == 2)
                                            <span class="badge badge-warning rounded-pill">Refund</span>
                                        @else
                                            <span class="badge badge-danger rounded-pill">Charge Back</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <button data-id="{{$payment->id}}" title="Refund" type="button" class="btn btn-warning btn-sm btn-round cxm-btn-refund">
                                            <i class="zmdi zmdi-replay"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <table id="LeadTable" class="table table-striped table-hover theme-color xjs-exportable" xdata-sorting="false" xdata-page-size="5" xdata-paging="true" xdata-filtering="false">
                            <thead>
                            <tr>
                                <th>ID #</th>
                                <th>Invoice ID#</th>
                                <th>Team</th>
                                <th>Brand</th>
                                <th>Client</th>
                                <th>Amount</th>
                                <th>Payment Gateway</th>
                                <th>Transaction ID</th>
                                <th data-breakpoints="sm xs">Payment Date</th>
                                <th class="text-center" data-breakpoints="xs md">Status</th>
                                <th class="text-center" data-breakpoints="xs md">Compliance<br>Varified</th>
                                <th class="text-center" data-breakpoints="xs md">Operation<br>Varified</th>
                                <th class="text-center" data-breakpoints="sm xs md">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td class="align-middle">{{$payment->id}}</td>
                                    <td class="align-middle">{{$payment->invoice_id}}</td>
                                    <td class="align-middle">{{optional($payment->getTeam)->name}}</td>
                                    <td class="align-middle">{{optional($payment->getBrand)->name}}</td>
                                    <td class="align-middle">
                                        <a class="text-warning" href="{{route('clientadmin.show',$payment->clientid)}}">{{$payment->name}}</a>
                                    </td>
                                    <td class="align-middle">${{$payment->amount}}</td>
                                    <td class="align-middle">{{$payment->payment_gateway}}</td>
                                    <td class="align-middle">{{$payment->authorizenet_transaction_id}}</td>
                                    <td class="align-middle">{{$payment->created_at->format('j F, Y')}}
                                        <br>{{$payment->created_at->format('h:i:s A')}}
                                        <br>{{$payment->created_at->diffForHumans()}}
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($payment->payment_status == 1)
                                            <span class="badge badge-success rounded-pill">Success</span>
                                        @elseif($payment->payment_status == 2)
                                            <span class="badge badge-warning rounded-pill">Refund</span>
                                        @else
                                            <span class="badge badge-danger rounded-pill">Charge Back</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        {!! ($payment->compliance_verified == 1)?'<i class="zmdi zmdi-check-circle text-success" title="Active"></i>' :'<i class="zmdi zmdi-close-circle text-danger" title="Inactive"></i>' !!}
                                    </td>
                                    <td class="text-center align-middle">
                                        {!! ($payment->head_verified == 1)?'<i class="zmdi zmdi-check-circle text-success" title="Active"></i>' :'<i class="zmdi zmdi-close-circle text-danger" title="Inactive"></i>' !!}
                                    </td>
                                    <td class="text-center align-middle">
                                        <button data-id="{{$payment->id}}" title="Refund" type="button" class="btn btn-warning btn-sm btn-round cxm-btn-refund">
                                            <i class="zmdi zmdi-replay"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
                {{--                {{ $payments->links() }}--}}
            </div>
        </div>
    </section>

    <!-- Cxm Refund Modal -->
    <div class="modal fade" id="cxmRefundModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Refund Request</h4>
                </div>
                <form method="post" id="refund_form">
                    <input type="hidden" id="team_key" name="team_key" value="">
                    <input type="hidden" id="brand_key" name="brand_key" value="">
                    <input type="hidden" id="agent_id" name="agent_id" value="">
                    <input type="hidden" id="invoice_id" name="invoice_id" value="">
                    <input type="hidden" id="client_id" name="client_id" value="">
                    <input type="hidden" id="payment_id" name="payment_id" value="">
                    <input type="hidden" id="auth_transaction_id" name="auth_transaction_id" value="">
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="zmdi zmdi-money"></i></span>
                                </div>
                                <input id="amount" name="amount" type="number" class="form-control" placeholder="Amount" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <textarea id="reason" class="form-control" placeholder="Enter Reason" name="reason" required></textarea>
                        </div>
                        <div class="form-group">
                            <select id="refund_type" name="type" class="form-control" data-placeholder="Select Type" required>
                                <option>Select Type</option>
                                <option value="refund">Refund</option>
                                <option value="chargeback">Charge Back</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-round">Send</button>
                        <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Direct Payment -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Create New Payment</h4>
                </div>
                <form method="POST" id="admin_direct-payment-Form">
                    <input type="hidden" id="team_hnd" class="form-control" name="team_key" value="">
                    <div class="modal-body">
                        <div class="col-sm-12">
                            <div id="" class="form-group">
                                <label for="payment_brand_key">Select Brand Name</label>
                                <select id="payment_brand_key" name="brand_key" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Brand" data-live-search="true" required>
                                    <option>Select Brand</option>
                                    @foreach($assign_brands as $assign_brand)
                                        <option value="{{$assign_brand->brand_key}}" data-cxm-team-key="{{ $assign_brand->team_key }}">{{optional($assign_brand->getBrandName)->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="" class="form-group">
                                <label for="payment_agent_id">Select Agent Name</label>
                                <select id="payment_agent_id" name="agent_id" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Agent" data-live-search="true" required>
                                    <option>Select Sales Agent</option>
                                    @foreach($members as $member)
                                        <option value="{{$member->id}}">{{$member->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="sales_type">Select Sale Type</label>
                                <select id="sales_type" name="sales_type" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Sales Type" data-live-search="true" required>
                                    <option>Select Sales Type</option>
                                    <option value="Fresh">Fresh</option>
                                    <option value="Upsale">Upsale</option>
                                    {{--<option value="Recurring">Recurring</option>--}}
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="name">Enter Client Name</label>
                                <input type="text" id="name" class="form-control" placeholder="Name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Enter Client Email</label>
                                <input type="email" id="email" class="form-control" placeholder="Email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Enter Client Phone Number</label>
                                <input type="text" id="phone" class="form-control" placeholder="Phone" name="phone" required>
                            </div>
                            <div class="form-group" id="projectTileBlock">
                                <label for="projectTitle">Project Title</label>
                                <input type="text" id="projectTitle" class="form-control" placeholder="Project Title" name="project_title" required/>
                            </div>
                            <div class="form-group">
                                <label for="invoice_description">Enter Invoice Description</label>
                                <textarea id="invoice_description" class="form-control" placeholder="Description & Details" name="description"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="due_date">Enter Payment Date</label>
                                <input type="date" id="due_date" class="form-control" placeholder="Due Date" name="due_date" required/>
                            </div>
                            <div class="form-group">
                                <label for="amount">Enter Amount</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="zmdi zmdi-money"></i></span>
                                    </div>
                                    <input type="number" id="amount" class="form-control" placeholder="Amount" name="value" required/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="merchant">Select Merchant</label>
                                <select id="merchant" name="merchant" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Merchant Type" data-live-search="true" required>
                                    <option>Select Merchant</option>
                                    <option value="authorize">Authorize</option>
                                    <option value="Expigate">Expigate</option>
                                    <option value="PayArc">PayArc</option>
                                    <option value="Zelle Pay">Zelle Pay</option>
                                    <option value="PayPal">PayPal</option>
                                    <option value="Venmo">Venmo</option>
                                    <option value="Cash App">Cash App</option>
                                    <option value="Wire Transfer">Wire Transfer</option>
                                </select>
                            </div>
                            <div class="form-group" id="projectTileBlock">
                                <label for="trackId">Enter Payment Tracking Id</label>
                                <input type="text" id="trackId" class="form-control" placeholder="Payment Tracking Id" name="track_id" required/>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="addStatusBtn" class="btn btn-success btn-round">SAVE</button>
                        <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('cxmScripts')
    <script src="{{ asset('assets/plugins/momentjs/moment.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#team, #brand').on('change', getParam);
        });

        function getParam() {
            window.location.href = "{{ route('paymentadmin.index') }}?brandKey=" + encodeURIComponent($('#brand').val()) + "&teamKey=" + encodeURIComponent($('#team').val()) + "&dateRange=" + encodeURIComponent($('#date-range').val());
        }

        $(function () {
            ['#team', '#brand'].forEach(function (selector) {
                var parentId = $(selector).attr('id');
                $(selector).siblings('.dropdown-menu').find('input[type="text"]').attr('id', parentId + '-search');
            });

            $(document).ready(function () {
                $('#LeadTable').DataTable({
                    dom: 'lBfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    order: [[0, 'desc']],
                    scrollX: true,
                    initComplete: function () {
                        $('#LeadTable_filter input').attr('id', 'LeadTable_searchInput');
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

                $('#team, #brand').on('change', getParam);
            });

            $('#LeadTable').on('click', '.cxm-btn-refund', function () {
                swal({
                    title: "Are you want to refund?",
                    text: "Press Yes, if you want to refund your payment!",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: "Cancel",
                            value: null,
                            visible: true,
                            className: "btn-warning",
                            closeModal: true,
                        },
                        confirm: {
                            text: "Yes, refund!"
                        }
                    },
                    dangerMode: true,
                })
                    .then((cxmRefund) => {
                        if (cxmRefund) {
                            console.log('Test Refund');
                            var payment_id = $(this).data('id');

                            $.ajax({
                                type: "GET",
                                url: "{{url('admin/paymentadmin/')}}/" + payment_id,
                                success: function (data) {
                                    console.log(data);
                                    $('#payment_id').val(payment_id);
                                    $('#team_key').val(data.team_key);
                                    $('#brand_key').val(data.brand_key);
                                    $('#invoice_id').val(data.invoice_id);
                                    $('#client_id').val(data.clientid);
                                    $('#agent_id').val(data.agent_id);
                                    $('#auth_transaction_id').val(data.authorizenet_transaction_id);
                                    $('#amount').val(data.amount);
                                    $('#cxmRefundModal').modal('show');
                                },
                                error: function (data) {
                                    console.log('Error:', data);
                                }
                            });
                        } else {
                            swal('Thank You', 'Thank you for your patience!', 'success', {buttons: false, timer: 2000});
                        }
                    });
            });
        });


        /** Maintain Refund Record*/
        $('#refund_form').on('submit', function (e) {
            e.preventDefault();
            console.log('tet');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('refundPayment') }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function (data) {
                    $("#refund_form")[0].reset();
                    console.log(data);
                    $("#cxmRefundModal").modal('hide');

                    swal("Good job!", "Refund Added successfully!", "success");
                    setTimeout('location.reload()', 1000);
                },
                error: function () {
                    swal("Errors!", "Request Fail!", "error");
                }
            });
        });

        $('#team').on('change', function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var teamKey = $(this).val();

            console.log(teamKey);

            $.ajax({
                url: "{{ route('teamPayment') }}",
                method: 'POST',
                data: {search: teamKey},
                success: function (result) {
                    // console.log(result);
                    $("#LeadTable").html(result);
                    $('#LeadTable').DataTable({
                        "destroy": true, //use for reinitialize datatable
                    });
                }
            });

        });

        $('#brand').on('change', function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var brandKey = $(this).val();

            console.log(brandKey);

            $.ajax({
                url: "{{ route('brandPayment') }}",
                method: 'POST',
                data: {search: brandKey},
                success: function (result) {
                    // console.log(result);
                    $("#LeadTable").html(result);
                    $('#LeadTable').DataTable({
                        "destroy": true, //use for reinitialize datatable
                    });
                }
            });

        });


        $('#payment_brand_key').on('change', function () {
            var brand = $(this).val();
            let cxmTeamKey = $(this).find(':selected').attr('data-cxm-team-key');
            console.log(brand + ' TK ' + cxmTeamKey);

            $('#team_hnd').val(cxmTeamKey);

            $.ajax({
                type: "GET",
                url: "/adminPaymentTeamMember/" + cxmTeamKey,
                success: function (data) {
                    console.log(data);
                    var len = data.length;
                    console.log(len);
                    let payment_agent_id = $("#payment_agent_id");
                    payment_agent_id.empty();

                    payment_agent_id.selectpicker('refresh');

                    payment_agent_id.append('<option class="bs-title-option" value="">Select Agent</option><option value="1">Default User</option>');
                    for (var i = 0; i < len; i++) {
                        var id = data[i]['id'];
                        var name = data[i]['name'];
                        payment_agent_id.append('<option value="' + id + '">' + name + '</option>');
                    }
                    payment_agent_id.selectpicker('refresh');
                }
            });
        });

        //Create Direct Payment
        $('#admin_direct-payment-Form').on('submit', function (e) {
            e.preventDefault();
            $('.page-loader-wrapper').css({'display': 'block', 'background': 'rgba(238, 238, 238, 0.7)'});
            console.log('Direct Payment Form');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: `{{route('admin_add_Payment')}}`,
                method: 'POST',
                data: $(this).serialize(), // get all form field value in serialize form
                success: function (data) {
                    $('.page-loader-wrapper').css('display', 'none');
                    $("#admin_direct-payment-Form")[0].reset();
                    console.log(data);
                    $("#paymentModal").modal('hide');

                    swal("Good job!", "Payment successfully Created!", "success");
                    setTimeout('location.reload()', 1000);
                },
                error: function (err) {
                    console.log(err);
                    $('.page-loader-wrapper').css('display', 'none');
                    swal("Errors!", "Request Fail!", "error");
                }
            });
        });
    </script>
@endpush
