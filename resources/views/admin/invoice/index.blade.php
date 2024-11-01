@extends('admin.layouts.app')@section('cxmTitle', 'Invoice')

@section('content')
    @push('css')
        <style>
            .badge-money {
                background: #008000;
                color: #fff;
            }

            .badge-money:hover {
                box-shadow: 0 3px 8px 0 rgba(41, 42, 51, 0.17);
            }

            .badge-money:hover {
                color: #fff;
                background-color: #27a127;
                border-color: #27a127;
            }

            .badge-money:focus {
                background: #fff;
                color: #008000;
            }

            .badge-money:focus > i.zmdi.zmdi-money {
                font-size: 18px;
            }

            .badge-paypal {
                background: #145ebd;
            }

            .badge-paypal:hover {
                box-shadow: 0 3px 8px 0 rgba(41, 42, 51, 0.17);
            }

            .badge-paypal:hover {
                color: #145ebd;
                background-color: #fff;
                border-color: #fff;
            }
        </style>
    @endpush
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Invoices</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i
                                        class="zmdi zmdi-home"></i> {{ config('app.home_name') }}
                                </a></li>
                            <li class="breadcrumb-item">Sales</li>
                            <li class="breadcrumb-item active"> Invoices</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <button class="btn btn-success btn-icon rounded-circle" type="button" data-toggle="modal"
                                id="create-invoice-show-modal" data-target="#invoiceModal">
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
                                        <select id="team" name="teamKey" class="form-control cxm-live-search-fix"
                                                data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true"
                                                title="Select Team" data-live-search="true"
                                                data-live-search-id="team-search">
                                            <option value="0">All Teams</option>
                                            @foreach($teams as $team)
                                                <option value="{{$team->team_key}}"
                                                        {{$teamKey == $team->team_key ? "selected " : "" }}data-team="{{$team->team_key}}">{{$team->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <label for="brand">Brands</label>
                                        <select id="brand" name="brandKey" class="form-control cxm-live-search-fix"
                                                data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true"
                                                title="Select Brand" data-live-search="true">
                                            <option value="0">All Brands</option>
                                            @foreach($brands as $brand)
                                                <option value="{{$brand->brand_key}}"
                                                        {{$brandKey == $brand->brand_key ? "selected " : "" }}data-brand="{{$brand->brand_key}}">{{$brand->name . ' - ' . $brand->brand_key}}</option>
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
                    <table id="InvoiceTable" class="table table-striped table-hover theme-color xjs-exportable"
                           xdata-sorting="false">
                        <thead>
                        <tr>
                            <th>ID #</th>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Brand</th>
                            <th>Agent</th>
                            <th>Name</th>
                            <th>Amount</th>
                            <th>Received Amount</th>
                            <th>Sales Type</th>
                            <th data-breakpoints="sm xs">Due Date</th>
                            <th data-breakpoints="xs md">Status</th>
                            <th data-breakpoints="sm xs md">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($invoices as $invoice)
                            <tr id="invoice-tr-{{$invoice->id}}">
                                <td class="align-middle text-nowrap">{{$invoice->id}}</td>
                                <td class="align-middle">
                                    {{-- <a class="text-warning" href="{{route('transLog',$invoice->invoice_key)}}">{{$invoice->invoice_num}}</a> --}}
                                    <a class="text-warning" href="#{{$invoice->invoice_num}}" data-toggle="modal"
                                       data-target="#logModal{{ $invoice->invoice_key}}">{{$invoice->invoice_num}}</a>
                                    <div class="mt-n2">
                                        <span class="badge badge-info rounded-pill">{{ $invoice->invoice_key}}</span>
                                    </div>
                                    {{--  --}}
                                    <div class="modal fade" id="logModal{{ $invoice->invoice_key}}" tabindex="-1"
                                         role="dialog">
                                        <div class="modal-dialog modal-lg modal-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="title" id="defaultModalLabel">Log</h4>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    @php $logs = \App\Models\PaymentTransactionsLog::where('invoiceid' ,$invoice->invoice_key)->get(); @endphp
                                                    @if(count($logs))
                                                        <div class="table-responsive">
                                                            <table id="LogTable-{{ $invoice->invoice_key}}"
                                                                   class="LogTable table table-striped table-hover table-sm theme-color js-exportable"
                                                                   xdata-sorting="false">
                                                                <thead>
                                                                <tr>
                                                                    <th>ID #</th>
                                                                    <th>Invoice #</th>
                                                                    <th>Amount</th>
                                                                    <th>Payment Gateway</th>
                                                                    <th>Response</th>
                                                                    <th data-breakpoints="sm xs">Date</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @foreach ($logs as $log)
                                                                    <tr>
                                                                        <td class="align-middle">{{$log->id}}</td>
                                                                        <td class="align-middle">{{$log->invoiceid}}</td>
                                                                        <td class="align-middle">${{$log->amount}}</td>
                                                                        <td class="align-middle text-nowrap">{{$log->payment_gateway === 1 ? "Authorize " : ($log->payment_gateway === 2 ? "Expigate" : ($log->payment_gateway === 3 ? "Payarc" : ($log->payment_gateway === 4 ? "Paypal" : "")))}}
                                                                            @if($log->payment_gateway === 1 && $log->merchant_id && isset($log->getAuthorizeMerchant) )
                                                                                <br>
                                                                                <p style="font-size:11px ">
                                                                                    ( {{$log->getAuthorizeMerchant->merchant}}
                                                                                    )</p>
                                                                            @endif
                                                                            @if($log->payment_gateway === 2 && $log->merchant_id && isset($log->getExpigateMerchant) )
                                                                                <br>
                                                                                <p style="font-size:11px ">
                                                                                    ( {{$log->getExpigateMerchant->merchant}}
                                                                                    )</p>
                                                                            @endif
                                                                        </td>
                                                                        <td class="align-middle text-nowrap">{{ Str::contains($log->response_reason, 'REFID') ? trim(Str::before($log->response_reason, 'REFID:')) : ($log->response_reason == "The 'AnetApi\/xml\/v1\/schema\/AnetApiSchema.xsd:cardCode' element is invalid - The value XXXXXX is invalid according to its datatype 'AnetApi\/xml\/v1\/schema\/AnetApiSchema.xsd:cardCode' - The Pattern constraint failed." ? "Card Cvv Is Invalid" : $log->response_reason ) }}</td>
                                                                        <td class="align-middle text-nowrap">{{$log->created_at->format('j F, Y')}}
                                                                            <br>{{$log->created_at->format('h:i:s A')}}
                                                                            <br>{{$log->created_at->diffForHumans()}}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @else
                                                        <div class="alert alert-info">No Data Found</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle text-nowrap">{{$invoice->created_at->format('j F, Y')}}
                                    <br>{{$invoice->created_at->format('h:i:s A')}}
                                    <br>{{$invoice->created_at->diffForHumans()}}</td>
                                <td class="align-middle">
                                    <a href="{{route('brand.edit',[$invoice->getBrand->id],'/edit')}}">{{$invoice->getBrandName->name}}</a><br>{{$invoice->getBrand->brand_key}}
                                </td>
                                <td class="align-middle">{{$invoice->getAgentName->name}}</td>
                                <td class="align-middle">
                                    <a class="text-warning"
                                       href="{{route('clientadmin.show',$invoice->clientid)}}">{{$invoice->getClientName->name}}</a>
                                </td>
                                <td class="align-middle" style="text-wrap: nowrap;">
                                    <b>Amount:</b> {{$invoice->currency_symbol}}{{$invoice->final_amount}}
                                    <br>
                                    @if($invoice->is_merchant_handling_fee == 1)
                                        <b>Mcht.
                                            Fee:</b> {{$invoice->currency_symbol}}{{ $invoice->merchant_handling_fee}}
                                        <br>
                                    @endif
                                    <b>Tax {{$invoice->tax_percentage}}%
                                        :</b> {{$invoice->currency_symbol}}{{$invoice->tax_amount}}
                                    <br>
                                    <b>Net
                                        Amount:</b> {{$invoice->currency_symbol}}{{$invoice->total_amount > 0? $invoice->total_amount : $invoice->final_amount}}
                                </td>
                                <td class="align-middle  text-nowrap">
                                    {{--                                    @if($invoice->payment_gateway === 'authorize')--}}
                                    {{--                                        @if($invoice->received_amount == $invoice->total_amount)--}}
                                    {{--                                            {{$invoice->cur_symbol." ".($invoice->total_amount) . ($invoice->status == 'paid' ? " Paid" : ' Unpaid')}}--}}
                                    {{--                                        @else--}}
                                    {{--                                        @if($invoice->is_split == 1 && isset($invoice->splitPayments) && $invoice->splitPayments->count() > 0)--}}
                                    {{--                                            {{$invoice->cur_symbol." ".($invoice->total_amount > 3 ? ($invoice->total_amount - 3) : $invoice->total_amount  ) . ($invoice->status == 'paid' ? " Paid" : ' Unpaid')}}--}}
                                    {{--                                            <br>--}}
                                    {{--                                            @foreach($invoice->splitPayments as $spKey => $skVal)--}}
                                    {{--                                                {{$invoice->cur_symbol}} {{$skVal->amount}} {{$skVal->status == 1 ? 'Paid' : 'Unpaid'}}--}}
                                    {{--                                                <br>--}}
                                    {{--                                            @endforeach--}}
                                    {{--                                        @else--}}
                                    {{--                                            {{$invoice->cur_symbol." ".($invoice->total_amount) . ($invoice->status == 'paid' ? " Paid" : ' Unpaid')}}--}}
                                    {{--                                        @endif--}}
                                    {{--                                        @endif--}}
                                    {{--                                        Todo need to make condtion for paypal--}}
                                    {{--                                    @else--}}
                                    {{--                                        {{$invoice->cur_symbol." ".($invoice->total_amount > 0? $invoice->total_amount : $invoice->final_amount) . ($invoice->status == 'paid' ? " Paid" : ' Unpaid')}}--}}
                                    {{--                                    @endif--}}
                                    @php
                                        $amount = $invoice->total_amount;

                                        if ($invoice->is_merchant_handling_fee == 1) {
                                            $amount -= $invoice->merchant_handling_fee;
                                        }
                                        if ($invoice->is_merchant_handling_fee == 1 && $invoice->is_tax == 1) {
                                            $amount -= $invoice->tax_amount;
                                        }
                                        $displayAmount = $invoice->total_amount > 0 ? $amount : $invoice->final_amount;
                                    @endphp
                                    {{$invoice->cur_symbol." ".($invoice->is_merchant_handling_fee == 1 ? $invoice->final_amount : $invoice->total_amount) . ($invoice->status == 'paid' ? " Paid" : ' Unpaid')}}
                                    @if($invoice->is_merchant_handling_fee == 1 && $invoice->is_tax == 1 && $invoice->tax_amount > 0)
                                        {{--                                        @if($invoice->split_tax == 1)--}}
                                        <br> (
                                        Tax {{$invoice->cur_symbol." ".($invoice->tax_amount) . ($invoice->tax_paid == 1 ? " Paid" : ' Unpaid')}}
                                        )
                                        {{--                                        @else--}}
                                        {{--                                            <br>--}}
                                        {{--                                            <p style="font-size: 11px;">--}}
                                        {{--                                                ( Incl. Tax--}}
                                        {{--                                                {{$invoice->cur_symbol ." ". $invoice->tax_amount}} )--}}
                                        {{--                                            </p>--}}
                                        {{--                                        @endif--}}
                                    @endif
                                    @if($invoice->is_merchant_handling_fee == 1 && $invoice->merchant_handling_fee > 0)
                                        {{--                                        @if($invoice->split_merchant_handling_fee == 1)--}}
                                        <br> ( Mcht.
                                        Fee {{$invoice->cur_symbol." ".($invoice->merchant_handling_fee) . ($invoice->merchant_handling_fee_paid == 1 ? " Paid" : ' Unpaid')}}
                                        )
                                        {{--                                        @else--}}
                                        {{--                                            <br>--}}
                                        {{--                                            <p style="font-size: 11px;">--}}
                                        {{--                                                ( Incl. Mcht. Fee--}}
                                        {{--                                                {{$invoice->cur_symbol ." ". $invoice->merchant_handling_fee}} )--}}
                                        {{--                                            </p>--}}
                                        {{--                                        @endif--}}
                                    @endif

                                    <br>

                                    @if($invoice->status == 'paid' && $invoice->is_merchant_handling_fee == 1 && ($invoice->merchant_handling_fee_paid == 0 || ($invoice->is_tax == 1 && $invoice->tax_paid == 0)))
                                        <button Title="Merchant Payment" style="font-size: 10px"
                                                class="btn badge-danger btn-sm btn-round MerchantPaymentBtn"
                                                data-id="{{$invoice->invoice_key}}">
                                            <i class="zmdi zmdi-money"></i> Remaining payment
                                        </button>
                                    @endif
                                </td>
                                <td class="align-middle">{{$invoice->sales_type}}</td>
                                <td class="align-middle td-due-date">
                                        <?php
                                        $now = \Carbon\Carbon::now();

                                        if ($invoice->due_date >= $now or $invoice->status == 'paid') {
                                            $color = 'success';
                                        } else {
                                            $color = 'danger';
                                        }
                                        ?>
                                    <span
                                        class="badge badge-{{$color}} rounded-pill xtext-{{$color}}">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y')}}</span>
                                </td>
                                <td class="align-middle status-span">
                                    @if($invoice->status == 'draft')
                                        <span class="badge bg-grey rounded-pill">Draft</span>
                                    @elseif($invoice->status == 'due')
                                        <span class="badge bg-amber rounded-pill">Due</span>
                                    @elseif($invoice->status == 'refund')
                                        <span class="badge bg-pink rounded-pill">Refund</span>
                                    @elseif($invoice->status == 'chargeback')
                                        <span class="badge bg-red rounded-pill">Charge Back</span>
                                    @elseif($invoice->status == 'paid')
                                        <span class="badge badge-success rounded-pill">Paid</span>
                                    @elseif($invoice->status == 'authorized')
                                        <span class="badge badge-warning rounded-pill">Authorized</span>
                                    @else
                                        <span class="badge badge-warning rounded-pill">UnKnown</span>
                                    @endif
                                </td>
                                <td class="align-middle text-nowrap">
                                    {{-- <a title="View Invoice" href="{{route('payment.show',$invoice->invoice_key)}}" class="btn btn-warning btn-sm btn-round"><i class="zmdi zmdi-open-in-new"></i></a> --}}
                                    <button Title="Copy Invoice URL"
                                            {{--                                            id="{{$invoice->getBrandUrl->brand_url}}checkout/{{isset($invoice->getBrand) && $invoice->getBrand->default_merchant_id == 2 ? 'expigate':'index'}}.php?invoicekey={{$invoice->invoice_key}}"--}}id="{{$invoice->getBrandUrl->brand_url}}checkout?invoicekey={{$invoice->invoice_key}}"
                                            class="btn badge-success btn-sm btn-round copy-url">
                                        <i class="zmdi zmdi-copy"></i></button>
                                    {{--                                    @if(isset($invoice->getBrand) && $invoice->getBrand->is_paypal == 1)--}}
                                    {{--                                        <button Title="Copy Paypal Invoice URL"--}}
                                    {{--                                                id="{{$invoice->getBrandUrl->brand_url}}checkout/paypal.php?invoicekey={{$invoice->invoice_key}}"--}}
                                    {{--                                                class="btn badge-paypal btn-sm btn-round copy-url"><i--}}
                                    {{--                                                class="zmdi zmdi-copy"></i></button>--}}
                                    {{--                                    @endif--}}
                                    @if($invoice->status != 'paid')
                                        <button data-id="{{$invoice->id}}" title="Edit"
                                                class="btn btn-info btn-sm btn-round editInvoice" data-toggle="modal"
                                                data-target="#editInvoiceModal">
                                            <i class="zmdi zmdi-edit"></i></button>
                                    @endif
                                    <a title="Change Status" data-id="" data-type="confirm" href="javascript:void(0);"
                                       class="btn btn-info btn-sm btn-round statusChange" data-toggle="modal"
                                       data-target="#changeStatusModal"><i class="zmdi zmdi-settings"></i></a>
                                    @if($invoice->status == 'paid' && (Auth::guard('admin')->user()->type == 'super' || Auth::guard('admin')->user()->type == 'admin'))
                                        <button data-id="{{$invoice->id}}" title="payments"
                                                class="btn badge-money btn-sm btn-round viewPaymentInvoice"
                                                data-toggle="modal" data-target="#viewPaymentInvoiceModal">
                                            <i class="zmdi zmdi-money"></i>
                                        </button>

                                    @endif
                                    @if(Auth::guard('admin')->user()->type == 'super')
                                        <a title="Delete" data-id="{{$invoice->id}}" data-type="confirm"
                                           href="javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton"><i
                                                class="zmdi zmdi-delete"></i></a>
                                    @endif

                                    {{-- Upsale Payment  --}}

                                    @php
                                        $ccInfo = \App\Models\CcInfo::where('client_id' ,$invoice->clientid)->where('status',1)->get();
                                    @endphp
                                    @if(count($ccInfo) && $invoice->status != 'paid')
                                        <button Title="Upsale Payment"
                                                class="btn badge-success btn-sm btn-round upSalePayment"
                                                data-toggle="modal" data-target="#upsalePaymentModal"
                                                data-id="{{$invoice->invoice_key}}"
                                                data-cxm-client-id="{{ $invoice->clientid }}">
                                            <i class="zmdi zmdi-balance"></i>
                                        </button>
                                    @endif
                                    @if($invoice->status != 'paid' && $invoice->status == 'authorized')
                                        <a title="Authorize Payment" data-id="{{$invoice->id}}"
                                           data-key="{{$invoice->invoice_key}}" data-type="confirm"
                                           href="javascript:void(0);"
                                           class="btn btn-warning btn-sm btn-round AuthorizePaymentButton"><i
                                                class="zmdi zmdi-balance-wallet"></i></a>
                                    @endif
                                    @php
                                        $failed_cards = \App\Models\CcInfo::where('client_id' ,$invoice->clientid)->get();
                                    @endphp
                                    @if(count($failed_cards) && $invoice->status != 'paid' && auth()->guard('admin')->user()->type === 'super')
                                        <button Title="Failed Card Upsale Payment"
                                                class="btn badge-success btn-sm btn-round failedCardUpSalePayment"
                                                data-toggle="modal" data-target="#failedCardUpSalePaymentModal"
                                                data-id="{{$invoice->invoice_key}}"
                                                data-cxm-client-id="{{ $invoice->clientid }}">
                                            <i class="zmdi zmdi-refresh-sync"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- ****** --><!-- Modals --><!-- ****** -->
    <!-- Upsale Payment -->
    <div class="modal fade" id="upsalePaymentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Upsale Payment</h4>
                </div>
                <form method="POST" id="admin-upsale-payment-Form">
                    <input type="hidden" id="invoice_key" class="form-control" name="invoice_key" value="">
                    <div class="modal-body">
                        <div class="form-group">
                            <select id="client_card" name="client_card" class="form-control show-tick ms xselect2"
                                    data-placeholder="Select" required></select>
                        </div>
                        <div class="form-group">
                            <textarea id="invoice_description" class="form-control" placeholder="Description & Details"
                                      name="description"></textarea>
                            <div class="text-warning">
                                <small><span class="zmdi zmdi-info"></span> Above description is optional.</small></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-round">Pay Now</button>
                        <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @if(auth()->guard('admin')->user()->type === 'super')
        <!-- Failed Card Upsale Payment -->
        <div class="modal fade" id="failedCardUpSalePaymentModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="title" id="defaultModalLabel">Failed Card Upsale Payment</h4>
                    </div>
                    <form method="POST" id="admin-failed-card-upsale-payment-Form">
                        <input type="hidden" id="failed_card_invoice_key" class="form-control" name="invoice_key"
                               value="">
                        <div class="modal-body">
                            <div class="form-group">
                                <select id="failed_card_client_card" name="client_card"
                                        class="form-control show-tick ms xselect2" data-placeholder="Select"
                                        required></select>
                            </div>
                            <div class="form-group">
                                <label>Select Merchant Type:</label><br>
                                <input type="radio" id="authorize_radio" name="merchant_type" value="authorize"
                                       style="display: inline-block;">
                                <label for="authorize_radio" style="display: inline-block; margin-right: 10px;">Authorize</label>
                                <input type="radio" id="expigate_radio" name="merchant_type" value="expigate"
                                       style="display: inline-block;">
                                <label for="expigate_radio" style="display: inline-block;">Expigate</label>
                            </div>
                            <div class="form-group" id="merchant_list_dropdown" style="display: none;">
                                <select id="failed_card_merchant_list" name="merchant"
                                        class="form-control show-tick ms xselect2"
                                        data-placeholder="Select Merchant"></select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success btn-round">Pay Now</button>
                            <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <!-- Create Invoice -->
    <div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Create A New Invoice</h4>
                </div>
                <form method="POST" id="admin_direct-invoice-Form">
                    <input type="hidden" id="team_hnd" class="form-control" name="team_key" value="">
                    <div class="modal-body">
                        <div class="form-group">
                            <select id="brand_key" name="brand_key" class="form-control cxm-live-search-fix"
                                    data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true"
                                    title="Select Brand" data-live-search="true" required>
                                <option value="">Select Brand</option>
                                @foreach($teamBrand as $brand)
                                    <option value="{{$brand->brand_key}}"
                                            data-cxm-team-key="{{ $brand->team_key }}">{{$brand->getBrandName->name . (isset($brand->getBrand->brand_key) ? " - ".$brand->getBrand->brand_key : ""  )}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select id="agent_id" name="agent_id" class="form-control cxm-live-search-fix"
                                    data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true"
                                    title="Select Agent" data-live-search="true" required>
                                @foreach($members as $member)
                                    <option value="{{$member->id}}">{{$member->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select id="type" name="sales_type" class="form-control" data-icon-base="zmdi"
                                    data-tick-icon="zmdi-check" data-show-tick="true" title="Select Sale Type" required>
                                <option value="Fresh" selected>Fresh</option>
                                <option value="Upsale">Upsale</option>
                                <option value="Recurring">Recurring</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" id="name" class="form-control" placeholder="Name" name="name"
                                   autocomplete="name" required>
                        </div>
                        <div class="form-group">
                            <input type="email" id="email" class="form-control" placeholder="Email" name="email"
                                   autocomplete="email" required>
                        </div>
                        <div class="form-group">
                            <input type="text" id="phone" class="form-control" placeholder="Phone" name="phone"
                                   autocomplete="phone" required>
                        </div>
                        <div class="form-group" id="projectTileBlock">
                            <label for="projectTitle">Project Title</label>
                            <input type="text" id="projectTitle" class="form-control" placeholder="Project Title"
                                   name="project_title" required/>
                        </div>
                        <div class="form-group">
                            <textarea id="invoice_description" class="form-control" placeholder="Description & Details"
                                      name="description"></textarea>
                            <div class="text-warning">
                                <small><span class="zmdi zmdi-info"></span> Above description is optional.</small></div>
                        </div>
                        <div class="form-group">
                            <select name="cur_symbol" class="form-control" id="cur_symbol" data-icon-base="zmdi"
                                    data-tick-icon="zmdi-check" data-show-tick="true">
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                                <option value="GBP">GBP</option>
                                <option value="AUD">AUD</option>
                                <option value="CAD">CAD</option>
                            </select>
                        </div>
                        <div class="form-group d-none">
                            <label for="payment_gateway">Select Payment Method</label>
                            <select id="payment_gateway" name="payment_gateway" class="form-control"
                                    data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true"
                                    title="Select Payment Merchant" disabled>
                                <option value="authorize" selected>Authorize.Net</option>
                                {{--<option value="paypal">PayPal</option>--}}
                            </select>
                        </div>
                        {{--                        <div class="form-group" style="display:none">--}}
                        {{--                            <label for="is_split">Select Payment Split</label>--}}
                        {{--                            <select id="is_split" name="is_split" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" required>--}}
                        {{--                                <option value="1">Yes</option>--}}
                        {{--                                <option value="0" selected>No</option>--}}
                        {{--                            </select>--}}
                        {{--                        </div>--}}
                        <div class="form-group">
                            <label>Amount</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text cxm-currency-symbol-icon"><i
                                            class="zmdi zmdi-money"></i></span>
                                </div>
                                <input type="number" id="amount" class="form-control" placeholder="Amount" name="value"
                                       max="{{env('PAYMENT_LIMIT')}}" required/>
                            </div>
                        </div>
                        <div class="">
                            <div class="input-group">
                                {{--                                <label class="mr-2">Merchant split Handling</label>--}}
{{--                                <div class="custom-control custom-switch mb-2 mr-2">--}}
{{--                                    <input type="checkbox" class="custom-control-input toggle-class val-class"--}}
{{--                                           id="is_merchant_handling_fee" name="is_merchant_handling_fee" value="0">--}}
{{--                                    --}}{{--                                    <label class="custom-control-label" for="is_merchant_handling_fee">Enable?</label>--}}
{{--                                    <label class="custom-control-label" for="is_merchant_handling_fee">Merchant split--}}
{{--                                        Handling?</label>--}}
{{--                                </div>--}}
                                {{--                                <div class="custom-control custom-switch mb-2">--}}
                                {{--                                    <input type="checkbox" class="custom-control-input toggle-class val-class"--}}
                                {{--                                           id="split_merchant_handling_fee" name="split_merchant_handling_fee"--}}
                                {{--                                           value="0">--}}
                                {{--                                    <label class="custom-control-label" for="split_merchant_handling_fee">Split?</label>--}}
                                {{--                                </div>--}}
                            </div>
                        </div>
{{--                        <div class="form-group merchant-handling-fee-div" style="display:none">--}}
{{--                            <div class="input-group">--}}
{{--                                <div class="input-group-prepend">--}}
{{--                                    <span class="input-group-text cxm-currency-symbol-icon"><i--}}
{{--                                            class="zmdi zmdi-money"></i></span>--}}
{{--                                </div>--}}
{{--                                <input type="number" id="merchant_handling_fee" class="form-control"--}}
{{--                                       placeholder="Merchant split Handling" name="merchant_handling_fee" value="20.00"--}}
{{--                                       readonly/>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                        <div class="">
                            <div class="input-group">
                                {{--                                <label class="mr-2">Taxable</label>--}}
                                <div class="custom-control custom-switch mb-2 mr-2">
                                    <input type="checkbox" class="custom-control-input toggle-class val-class"
                                           id="taxable" name="taxable" value="1" checked>
                                    {{--                                    <label class="custom-control-label" for="taxable">Enable?</label>--}}
                                    <label class="custom-control-label" for="taxable">Taxable?</label>
                                </div>
                                {{--                                <div class="custom-control custom-switch mb-2">--}}
                                {{--                                    <input type="checkbox" class="custom-control-input toggle-class val-class"--}}
                                {{--                                           id="split_tax" name="split_tax" value="0">--}}
                                {{--                                    <label class="custom-control-label" for="split_tax">Split?</label>--}}
                                {{--                                </div>--}}
                            </div>
                        </div>
                        <div class="form-group" id="taxField">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">%</span>
                                </div>
                                <input type="hidden" id="tax_amount" class="form-control" name="taxAmount" value="0">
                                <input type="number" name="tax" id="tax" class="form-control" placeholder="Tax"
                                       value="0"/>
                            </div>
                        </div>
                        <div class="form-group" id="totalAmount">
                            <label for="totalAmount">Total Amount</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text cxm-currency-symbol-icon"><i
                                            class="zmdi zmdi-money"></i></span>
                                </div>
                                <input type="text" name="total_amount" class="form-control" placeholder="Total Amount"
                                       id="total_amount" value="0" readonly>
                            </div>
                        </div>
                        <div class="xform-group">
                            <input type="date" id="due_date" class="form-control" placeholder="Due Date" name="due_date"
                                   value="{{ date('Y-m-d') }}" required/>
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
    <!-- Edit Invoice -->
    <div class="modal fade" id="editInvoiceModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Edit Invoice</h4>
                </div>
                <form method="POST" id="invoice_update_form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="invoice_hdn" class="form-control" name="hdn" value="">
                    <div class="modal-body">
                        <div class="form-group">
                            <select id="edit_brand_key" name="brand_key" class="form-control" data-icon-base="zmdi"
                                    data-tick-icon="zmdi-check" data-show-tick="true" title="Select Brand" required>
                                @foreach($teamBrand as $brand)
                                    <option value="{{$brand->brand_key}}"
                                            data-cxm-team-key="{{ $brand->team_key }}">{{$brand->getBrandName->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select id="edit_agent_id" name="agent_id" class="form-control" data-icon-base="zmdi"
                                    data-tick-icon="zmdi-check" data-show-tick="true" title="Select Sale Agent"
                                    required>
                                @foreach($members as $member)
                                    <option value="{{$member->id}}">{{$member->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select id="sales_type" name="sales_type" class="form-control" data-icon-base="zmdi"
                                    data-tick-icon="zmdi-check" data-show-tick="true" title="Select Sale Type" required>
                                <option value="Fresh">Fresh</option>
                                <option value="Upsale">Upsale</option>
                                <option value="Recurring">Recurring</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <textarea id="edit_invoice_description" class="form-control"
                                      placeholder="Description & Details" name="description"></textarea>
                        </div>
                        <div class="form-group">
                            <select name="edit_cur_symbol" class="form-control" id="edit_cur_symbol"
                                    data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true">
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                                <option value="GBP">GBP</option>
                                <option value="AUD">AUD</option>
                                <option value="CAD">CAD</option>
                            </select>
                        </div>
                        <div class="form-group d-none">
                            <label for="edit_payment_gateway">Select Payment Method</label>
                            <select id="edit_payment_gateway" name="edit_payment_gateway" class="form-control"
                                    data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true"
                                    title="Select Payment Merchant" disabled>
                                <option value="authorize" selected>Authorize.Net</option>
                                {{--<option value="paypal">PayPal</option>--}}
                            </select>
                        </div>
                        {{--                        <div class="form-group">--}}
                        {{--                            <label for="edit_is_split">Select Payment Split</label>--}}
                        {{--                            <select id="edit_is_split" name="edit_is_split" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" required>--}}
                        {{--                                <option value="1">Yes</option>--}}
                        {{--                                <option value="0" selected>No</option>--}}
                        {{--                            </select>--}}
                        {{--                        </div>--}}
                        {{--                        <div class="form-group form-float">--}}
                        {{--                            <label for="edit_is_split">Select Payment Split</label>--}}
                        {{--                            <select class="form-control show-tick ms select2" data-placeholder="Select"--}}
                        {{--                                    id='edit_is_split' name='is_split' required>--}}
                        {{--                                <option value="1">Yes</option>--}}
                        {{--                                <option value="0" selected>No</option>--}}
                        {{--                            </select>--}}
                        {{--                        </div>--}}
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text cxm-currency-symbol-icon"><i
                                            class="zmdi zmdi-money"></i></span>
                                </div>
                                <input type="number" id="edit_amount" class="form-control" placeholder="Amount"
                                       max='5999' name="value" required/>
                            </div>
                        </div>
{{--                        <div class="form-group edit-merchant-handling-fee-div" style="display:none">--}}
{{--                            <label for="edit_merchant_handling_fee">Merchant split Handling</label>--}}
{{--                            <input type="number" id="edit_merchant_handling_fee" class="form-control"--}}
{{--                                   placeholder="Merchant split Handling" name="merchant_handling_fee" value="20.00"/>--}}
{{--                        </div>--}}
                        {{--
                        <div class="form-group">
                            <label class="text-muted">
                                <input type="checkbox" name="edit_taxable" id="edit_taxable" value="1" checked> Taxable?
                            </label>
                        </div>
                        --}}
                        <div class="custom-control custom-switch mb-2">
                            <input type="checkbox" class="custom-control-input toggle-class" id="edit_taxable"
                                   name="edit_taxable" value="1" checked>
                            <label class="custom-control-label" for="edit_taxable">Taxable?</label>
                        </div>
                        <div class="form-group" id="edit_taxField">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">%</i></span>
                                </div>
                                <input type="hidden" id="edit_tax_amount" class="form-control" name="edit_taxAmount"
                                       value="0">
                                <input type="number" name="edit_tax" id="edit_tax" class="form-control"
                                       placeholder="Tax"/>
                            </div>
                        </div>
                        <div class="form-group" id="edit_totalAmount">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text cxm-currency-symbol-icon"><i
                                            class="zmdi zmdi-money"></i></span>
                                </div>
                                <input type="text" name="edit_total_amount" class="form-control"
                                       placeholder="Total Amount" id="edit_total_amount" value="0" readonly>
                            </div>
                        </div>
                        <div class="xform-group">
                            <input type="date" id="edit_due_date" class="form-control" placeholder="Due Date"
                                   value="{{ date('Y-m-d') }}" name="due_date" required/>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-round">SAVE</button>
                        <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- View Payment Invoice -->
    <div class="modal fade" id="viewPaymentInvoiceModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">View Payment Invoice</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Paid Amount</strong><br>
                        <div><b>1st Payment : </b><span id="first-payment"></span></div>
                        <div><b>2nd Payment : </b> <span id="second-payment"></span></div>
                        <div><b>3rd Payment : </b> <span id="third-payment"></span></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success btn-round" data-dismiss="modal">Okay</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('cxmScripts')
    <script src="{{ asset('assets/plugins/momentjs/moment.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    @include('includes.currency-change')
    @include('admin.invoice.script')
    <script>
        function getParam() {
            window.location.href = "{{ route('admin.invoices.index') }}?brandKey=" + encodeURIComponent($('#brand').val()) + "&teamKey=" + encodeURIComponent($('#team').val()) + "&dateRange=" + encodeURIComponent($('#date-range').val());
        }

        $(function () {
            $('.LogTable').each(function () {
                var tableId = $(this).attr('id');
                $('#' + tableId + '_filter input').attr('id', tableId + '_searchInput');
            });

            ['#team', '#brand', '#brand_key', '#agent_id'].forEach(function (selector) {
                var parentId = $(selector).attr('id');
                $(selector).siblings('.dropdown-menu').find('input[type="text"]').attr('id', parentId + '-search');
            });

            $(document).ready(function () {
                $('#InvoiceTable').DataTable({
                    dom: 'lBfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    order: [[0, 'desc']],
                    scrollX: true,
                    initComplete: function () {
                        $('#InvoiceTable_filter input').attr('id', 'InvoiceTable_searchInput');
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
        });
    </script>
@endpush
