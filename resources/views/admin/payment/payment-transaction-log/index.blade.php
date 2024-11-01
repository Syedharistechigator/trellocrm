@extends('admin.layouts.app')@section('cxmTitle', 'Payment Transaction Log')

@section('content')
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Payment Transaction Logs</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                            <li class="breadcrumb-item active"> List</li>
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
                                                    <option value="{{$team->team_key}}" {{$teamKey == $team->team_key ? "selected " : "" }} data-team="{{$team->team_key}}">{{$team->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <label for="brand">Brands</label>
                                            <select id="brand" name="brandKey" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Brand" data-live-search="true">
                                                <option value="0">All Brands</option>
                                                @foreach($brands as $brand)
                                                    <option value="{{$brand->brand_key}}" {{$brandKey == $brand->brand_key ? "selected " : "" }} data-brand="{{$brand->brand_key}}">{{$brand->name}}</option>
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
                <div class="row clearfix">
                    <div class="col-lg-12">
                        <div class="">
                            <div class="table-responsive">
                                <table id="PaymentTransactionLogTable" class="table table-striped table-hover theme-color xjs-exportable" data-sorting="false">
                                    <thead>
                                    <tr>
                                        <th>ID#</th>
                                        <th>Invoice#</th>
                                        <th></th>
                                        <th>Payment Gateway</th>
                                        <th data-breakpoints="sm xs">Date</th>
                                        <th>Team</th>
                                        <th>Brand</th>
                                        <th>Client</th>
                                        <th>Amount</th>
                                        <th>Response</th>
                                        <th>Billing Details</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($payment_transaction_logs as $payment_transaction_log)
                                        <tr>
                                            <td class="align-middle">{{$payment_transaction_log->id}}</td>
                                            <td class="align-middle">
                                                @if(isset($payment_transaction_log->getInvoice))
                                                    <a class="text-warning" href="#{{$payment_transaction_log->getInvoice->invoice_num}}">{{$payment_transaction_log->getInvoice->invoice_num}}</a>
                                                @endif
                                                <br>
                                                <div class="mt-n2">
                                                    <span class="badge badge-info rounded-pill">{{ $payment_transaction_log->invoiceid}}</span>
                                                </div>
                                            </td>
                                            <td class="align-middle" data-sort="{{
                                                $payment_transaction_log->payment_gateway == 1
                                                    ? ($payment_transaction_log->response_code == 1 || $payment_transaction_log->response_reason == 'This transaction has been approved.' ? 1 : ($payment_transaction_log->response_code == 4 ? 2 : 3))
                                                    : (
                                                        $payment_transaction_log->payment_gateway == 2
                                                            ? ($payment_transaction_log->response_code == 100 || $payment_transaction_log->response_reason == 'This transaction has been approved.' ? 1 : 2)
                                                            : (
                                                                $payment_transaction_log->payment_gateway == 3
                                                                    ? ($payment_transaction_log->response_code == 00 || $payment_transaction_log->response_reason == 'This transaction has been approved.' ? 1 : 2)
                                                                    : ($payment_transaction_log->response_code == 1 || $payment_transaction_log->response_reason == 'This transaction has been approved.' ? 1 : 2)
                                                            )
                                                    )
                                            }}">
                                                @if($payment_transaction_log->payment_gateway == 1)
                                                    @if($payment_transaction_log->response_code ==1 || $payment_transaction_log->response_reason == 'This transaction has been approved.')
                                                        <i class="zmdi zmdi-check-circle text-success" title="success"></i>
                                                    @elseif($payment_transaction_log->response_code ==4 )
                                                        <i class="zmdi zmdi-check-circle text-warning" title="in process (held)"></i>
                                                    @else
                                                        <i class="zmdi zmdi-close-circle text-danger" title="unsuccessful"></i>
                                                    @endif
                                                @elseif($payment_transaction_log->payment_gateway == 2)
                                                    @if($payment_transaction_log->response_code ==100  || $payment_transaction_log->response_reason == 'This transaction has been approved.')
                                                        <i class="zmdi zmdi-check-circle text-success" title="success"></i>
                                                    @else
                                                        <i class="zmdi zmdi-close-circle text-danger" title="unsuccessful"></i>
                                                    @endif
                                                @elseif($payment_transaction_log->payment_gateway == 3)
                                                    @if($payment_transaction_log->response_code ==00  || $payment_transaction_log->response_reason == 'This transaction has been approved.')
                                                        <i class="zmdi zmdi-check-circle text-success" title="success"></i>
                                                    @else
                                                        <i class="zmdi zmdi-close-circle text-danger" title="unsuccessful"></i>
                                                    @endif
                                                @elseif($payment_transaction_log->payment_gateway == 4)
                                                    @if($payment_transaction_log->response_code ==1  || $payment_transaction_log->response_reason == 'This transaction has been approved.')
                                                        <i class="zmdi zmdi-check-circle text-success" title="success"></i>
                                                    @else
                                                        <i class="zmdi zmdi-close-circle text-danger" title="unsuccessful"></i>
                                                    @endif
                                                @elseif($payment_transaction_log->response_code == 1  || $payment_transaction_log->response_reason == 'This transaction has been approved.')
                                                    <i class="zmdi zmdi-check-circle text-success" title="success"></i>
                                                @else
                                                    <i class="zmdi zmdi-close-circle text-danger" title="unsuccessful"></i>
                                                @endif
                                            </td>
                                            <td class="align-middle text-nowrap">
                                                {{$payment_transaction_log->payment_gateway === 1 ? "Authorize " : ($payment_transaction_log->payment_gateway === 2 ? "Expigate" : ($payment_transaction_log->payment_gateway === 3 ? "Payarc" : ($payment_transaction_log->payment_gateway === 4 ? "Paypal" : "---")))}}
                                                @if($payment_transaction_log->payment_gateway === 1 && $payment_transaction_log->merchant_id && isset($payment_transaction_log->getAuthorizeMerchant) )
                                                    <br>
                                                    <p style="font-size:11px ">( {{$payment_transaction_log->getAuthorizeMerchant->merchant}} )</p>
                                                @endif
                                                @if($payment_transaction_log->payment_gateway === 2 && $payment_transaction_log->merchant_id && isset($payment_transaction_log->getExpigateMerchant) )
                                                    <br>
                                                    <p style="font-size:11px ">( {{$payment_transaction_log->getExpigateMerchant->merchant}} )</p>
                                                @endif
                                                @if($payment_transaction_log->payment_gateway === 3 && $payment_transaction_log->merchant_id && isset($payment_transaction_log->getPayArcMerchant) )
                                                    <br>
                                                    <p style="font-size:11px ">( {{$payment_transaction_log->getPayArcMerchant->merchant}} )</p>
                                                @endif
                                            </td>
                                            <td class="align-middle text-nowrap">{{$payment_transaction_log->created_at->format('j F, Y')}}
                                                <br>{{$payment_transaction_log->created_at->format('h:i:s A')}}
                                                <br>{{$payment_transaction_log->created_at->diffForHumans()}}
                                            </td>
                                            <td class="align-middle">
                                                @if(isset($payment_transaction_log->getTeam))
                                                    <a href="{{route('team.edit',[$payment_transaction_log->getTeam->id],'/edit')}}">{{$payment_transaction_log->getTeam->name}}</a>
                                                @endif
                                                <br>{{$payment_transaction_log->team_key}}
                                            </td>
                                            <td class="align-middle">
                                                @if(isset($payment_transaction_log->getBrand))
                                                    <a href="{{route('brand.edit',[$payment_transaction_log->getBrand->id],'/edit')}}">{{$payment_transaction_log->getBrand->name}}</a>
                                                @endif
                                                <br>{{$payment_transaction_log->brand_key}}
                                            </td>
                                            <td class="align-middle">
                                                <a class="text-warning" href="{{route('clientadmin.show',$payment_transaction_log->clientid)}}" title="
                                                   @if(isset($payment_transaction_log->getClient))
                                                        {{ "Name : ".$payment_transaction_log->getClient->name."\nEmail : ".$payment_transaction_log->getClient->email."\nPhone : ".$payment_transaction_log->getClient->phone }}
                                                   @endif">{{isset($payment_transaction_log->getClient) ? $payment_transaction_log->getClient->name : ""}}</a>
                                            </td>
                                            <td class="align-middle">$ {{$payment_transaction_log->amount}}</td>
                                            <td class="align-middle">{{ Str::contains($payment_transaction_log->response_reason, 'REFID') ? trim(Str::before($payment_transaction_log->response_reason, 'REFID:')) : $payment_transaction_log->response_reason }}</td>
                                            <td class="align-middle text-nowrap">
                                                @if($payment_transaction_log->address)
                                                    Address : {{$payment_transaction_log->address}}
                                                @elseif(!$payment_transaction_log->address)
                                                    Address : {{"---"}}
                                                @endif
                                                @if($payment_transaction_log->city)
                                                    <br>City : {{$payment_transaction_log->city}}
                                                @elseif(!$payment_transaction_log->city)
                                                    <br>City : {{"---"}}
                                                @endif
                                                @if($payment_transaction_log->state)
                                                    <br>State : {{$payment_transaction_log->state}}
                                                @elseif(!$payment_transaction_log->state)
                                                    <br>State : {{"---"}}
                                                @endif
                                                @if($payment_transaction_log->country)
                                                    <br>Country : {{$payment_transaction_log->country}}
                                                @elseif(!$payment_transaction_log->country)
                                                    <br>Country : {{"---"}}
                                                @endif
                                                @if($payment_transaction_log->zipcode)
                                                    <br>Zipcode : {{$payment_transaction_log->zipcode}}
                                                @elseif(!$payment_transaction_log->zipcode)
                                                    <br>Zipcode : {{"---"}}
                                                @endif
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

@endsection
@push('cxmScripts')
    <script src="{{ asset('assets/plugins/momentjs/moment.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        /** => Developer Michael Update <= **/
        function getParam() {
            window.location.href = "{{ route('admin.payment.transaction.log.index') }}?brandKey=" + encodeURIComponent($('#brand').val()) + "&teamKey=" + encodeURIComponent($('#team').val()) + "&dateRange=" + encodeURIComponent($('#date-range').val());
        }

        $(document).ready(function () {
            ['#team', '#brand'].forEach(function (selector) {
                var parentId = $(selector).attr('id');
                $(selector).siblings('.dropdown-menu').find('input[type="text"]').attr('id', parentId + '-search');
            });
            $('#PaymentTransactionLogTable').DataTable().destroy();

            $('#PaymentTransactionLogTable').DataTable({
                dom: 'lBfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[0, 'desc']],
                scrollX: true,
                initComplete: function () {
                    $('#PaymentTransactionLogTable_filter input').attr('id', 'PaymentTransactionLogTable_searchInput');
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
    </script>

@endpush
