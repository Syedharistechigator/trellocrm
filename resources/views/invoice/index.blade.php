@extends('layouts.app')@section('cxmTitle', 'Invoices')

@section('content')
    @push('css')
        <style>
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

            .bootstrap-select > .dropdown-toggle {
                padding: 0.7rem 0.75rem !important;
                line-height: 16px !important;
            }

            .upSalePayment:focus {
                color: #04be5b;
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
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}
                                </a></li> <li class="breadcrumb-item">Sales</li>
                            <li class="breadcrumb-item active"> Invoices</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <button class="btn btn-success btn-icon rounded-circle" type="button" data-toggle="modal" id="create-invoice-show-modal" data-target="#invoiceModal">
                            <i class="zmdi zmdi-plus"></i></button>
                        @include('includes.cxm-top-right-toggle-btn')
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="card">
                                    <div class="body">
                                        <form id="searchForm">
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <label>Brands</label>
                                                    <select id="brand" name="brandKey" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Brand" data-live-search="true" required>
                                                        <option value='0' data-brand="0" {{$brandKey == 0 ? "selected" : "" }} >All</option>
                                                        @foreach($assign_brands as $assign_brand)
                                                            <option value="{{$assign_brand->brand_key}}" {{$brandKey == $assign_brand->brand_key ? "selected" : "" }} data-brand="{{$assign_brand->brand_key}}">{{$assign_brand->getBrandNameWithTrashed->name}}</option>
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
                        <div class="">
                            <div class="table-responsive">
                                <table id="InvoiceTable" class="table table-striped table-hover xjs-basic-example theme-color">
                                    <thead>
                                    <tr>
                                        <th class='text-nowrap'>ID #</th>
                                        <th class='text-nowrap'>Invoice #</th>
                                        <th>Date</th>
                                        <th>Brand</th>
                                        <th>Agent</th>
                                        <th>Name</th>
                                        <th>Amount</th>
                                        <th>Sales Type</th>
                                        <th data-breakpoints="sm xs">Signature</th>
                                        <th data-breakpoints="sm xs">Due Date</th>
                                        <th class="text-center" data-breakpoints="xs md">Status</th>
                                        <th data-breakpoints="sm xs md">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($invoices as $invoice)
                                        <tr>
                                            <td class="align-middle">{{$invoice->id}}</td>
                                            <td class="align-middle">
                                                <a class="text-warning" href="#{{$invoice->invoice_num}}" data-toggle="modal" data-target="#logModal{{ $invoice->invoice_key}}">{{$invoice->invoice_num}}</a>
                                                <div class="">
                                                    <span class="badge badge-info rounded-pill">{{ $invoice->invoice_key}}</span>
                                                </div>
                                                <div class="modal fade" id="logModal{{ $invoice->invoice_key}}" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog modal-lg modal-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="title" id="defaultModalLabel">Transactions Log</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                @if(isset($invoice->getPaymentsTransactionLogs) && count($invoice->getPaymentsTransactionLogs))
                                                                    <div class="table-responsive">
                                                                        <table id="InvoiceTable" class="table table-striped table-hover table-sm theme-color js-exportable" xdata-sorting="false">
                                                                            <thead>
                                                                            <tr>
                                                                                <th>ID #</th>
                                                                                <th>Invoice #</th>
                                                                                <th>Amount</th>
                                                                                <th>Payment Gateway</th>
                                                                                <th>Response Reason</th>
                                                                                <th data-breakpoints="sm xs">Date</th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                            @foreach ($invoice->getPaymentsTransactionLogs as $log)
                                                                                <tr>
                                                                                    <td class="align-middle">{{$log->id}}</td>
                                                                                    <td class="align-middle">{{$log->invoiceid}}</td>
                                                                                    <td class="align-middle">${{$log->amount}}</td>
                                                                                    <td class="align-middle text-nowrap">{{$log->payment_gateway === 1 ? "Authorize " : ($log->payment_gateway === 2 ? "Expigate" : ($log->payment_gateway === 3 ? "Payarc" : ($log->payment_gateway === 4 ? "Paypal" : "")))}}
                                                                                        @if($log->payment_gateway === 1 && $log->merchant_id && isset($log->getAuthorizeMerchant) )
                                                                                            <br>
                                                                                            <p style="font-size:12px">( {{$log->getAuthorizeMerchant->merchant}} )</p>
                                                                                        @endif
                                                                                        @if($log->payment_gateway === 2 && $log->merchant_id && isset($log->getExpigateMerchant) )
                                                                                            <br>
                                                                                            <p style="font-size:12px">( {{$log->getExpigateMerchant->merchant}} )</p>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td class="align-middle text-nowrap">{{$log->response_reason}}</td>
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
                                            <td class="align-middle">{{$invoice->getBrandName->name}}</td>
                                            <td class="align-middle">{{$invoice->getAgentName->name}}</td>
                                            <td class="text-info align-middle">
                                                <a class="text-info" href="{{route('client.show',$invoice->clientid)}}">{{$invoice->getClientName->name}}</a>
                                            </td>
                                            <td class="align-middle">
                                                <b>Amount:</b> {{$currencySymbols[$invoice->currency_symbol] ?? '$'}} {{$invoice->final_amount}}
                                                <br>
                                                <b>Tax {{$invoice->tax_percentage}}% :</b> {{$currencySymbols[$invoice->currency_symbol] ?? '$'}} {{$invoice->tax_amount}}
                                                <br>
                                                <b>Net Amount:</b> {{$currencySymbols[$invoice->currency_symbol] ?? '$'}} {{$invoice->total_amount > 0 ? $invoice->total_amount:$invoice->final_amount}}
                                            </td>
                                            <td class="align-middle">{{$invoice->sales_type}}</td>
                                            <td class="align-middle">
                                                    <?php
                                                    $invoices = App\Models\InvoiceSignature::where('invoice_id', $invoice->invoice_key)->get();

                                                foreach ($invoices as $key => $value) {


                                                    if ($key % 2 == 0 && $key != 0)
                                                        echo '<br>';


                                                    ?>
                                                <img class="signature_img" src="<?=$value['signature']?>" width="50" style="border: 1px solid"/>
                                                    <?php


                                                }

                                                    if (count($invoices) == 0)
                                                        echo 'No Signature';

                                                    ?>
                                            </td>
                                            <td class="align-middle">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y')}}</td>
                                            <td class="text-center align-middle">
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
                                                @if($invoice->status == 'draft')
                                                    <button data-id="{{$invoice->id}}" title="Publish" class="btn btn-info btn-sm btn-round publishInvoice" data-toggle="modal" data-target="modal">Publish
                                                    </button>
                                                @else
                                                    <a title="Email To Client" data-id="{{$invoice->id}}" data-type="confirm" href="javascript:void(0);" class="btn bg-orange btn-sm btn-round sendEmail" data-toggle="modal"><i class="zmdi zmdi-email"></i></a>
                                                    {{--<button Title="Copy Invoice URL"  id="{{route('payment.show', $invoice->invoice_key)}}" class="btn badge-success btn-sm btn-round copy-url"><i class="zmdi zmdi-copy"></i></button>--}}
                                                    <button Title="Copy Invoice URL"
                                                            {{--                                                            id="{{$invoice->getBrandUrl->brand_url}}checkout/{{isset($invoice->getBrand) && $invoice->getBrand->default_merchant_id == 2 ? 'expigate':'index'}}.php?invoicekey={{$invoice->invoice_key}}"--}}
                                                            id="{{$invoice->getBrandUrl->brand_url}}checkout?invoicekey={{$invoice->invoice_key}}" class="btn badge-success btn-sm btn-round copy-url">
                                                        <i class="zmdi zmdi-copy"></i></button>
                                                    {{--                                                    @if(auth()->user()->type === 'ppc' && isset($invoice->getBrand) && $invoice->getBrand->is_paypal == 1)--}}
                                                    {{--                                                        <button Title="Copy Paypal Invoice URL"--}}
                                                    {{--                                                                id="{{$invoice->getBrandUrl->brand_url}}checkout/paypal.php?invoicekey={{$invoice->invoice_key}}"--}}
                                                    {{--                                                                class="btn badge-paypal btn-sm btn-round copy-url"><i--}}
                                                    {{--                                                                class="zmdi zmdi-copy"></i></button>--}}
                                                    {{--                                                    @endif--}}

                                                @endif
                                                @if($invoice->status != 'paid')
                                                    <button data-id="{{$invoice->id}}" title="Edit" class="btn btn-info btn-sm btn-round editInvoice" data-toggle="modal" data-target="#editInvoiceModal">
                                                        <i class="zmdi zmdi-edit"></i></button>
                                                @endif
                                                <a title="View Invoice"
{{--                                                   href="{{$invoice->getBrandUrl->brand_url}}checkout/{{isset($invoice->getBrand) && $invoice->getBrand->default_merchant_id == 2 ? 'expigate':'index'}}.php?invoicekey={{$invoice->invoice_key}}"--}}
                                                   href="{{$invoice->getBrandUrl->brand_url}}checkout?invoicekey={{$invoice->invoice_key}}"
                                                   class="btn btn-info btn-sm btn-round"><i class="zmdi zmdi-eye"></i></a>
                                                {{-- Upsale Payment  --}}
                                                @php
                                                    $ccInfo = \App\Models\CcInfo::where('client_id' ,$invoice->clientid)
                                                    ->where('status',1)->get();
                                                @endphp
                                                @if(count($ccInfo) && $invoice->status != 'paid')
                                                    <button Title="Upsale Payment" class="btn badge-success btn-sm btn-round upSalePayment" data-toggle="modal" data-target="#upsalePaymentModal" data-id="{{$invoice->invoice_key}}" data-cxm-client-id="{{ $invoice->clientid }}">
                                                        <i class="zmdi zmdi-balance"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{--                {{ $invoices->links() }}--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                            <select id="client_card" name="client_card" class="form-control show-tick ms xselect2" data-placeholder="Select" required></select>
                        </div>
                        <div class="form-group">
                            <textarea id="invoice_description" class="form-control" placeholder="Description & Details" name="description"></textarea>
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

    <!-- Create Invoice -->
    <div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Create A New Invoice</h4>
                </div>
                <form method="POST" id="direct-invoice-Form">
                    <div class="modal-body">
                        @if(Auth::user()->type == 'ppc')
                            <div class="form-group">
                                <label for="team_hnd">Select Team Name</label>
                                <select id="team_hnd" name="team_key" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Team" data-live-search="true" required>
                                    <option disabled>Select Team</option>
                                    @foreach($teams as $team)
                                        <option value="{{$team->team_key}}">{{$team->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <input type="hidden" id="team_hnd" class="form-control" name="team_key" value="{{Auth::user()->team_key}}">
                        @endif
                        <div class="form-group">
                            <label for="brand_key">Select Brand Name</label>
                            @if(Auth::user()->type == 'ppc')
                                <select id="brand_key" name="brand_key" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Brand" data-live-search="true" required>
                                    <option disabled>Select Brand</option>
                                </select>
                            @else
                                <select id="brand_key" name="brand_key" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Brand" data-live-search="true" required>
                                    <option disabled>Select Brand</option>
                                    @foreach($assign_brands as $assign_brand)
                                        <option value="{{$assign_brand->brand_key}}">{{$assign_brand->getBrandNameWithTrashed->name}}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="agent_id">Select Agent Name</label>
                            <select id="agent_id" name="agent_id" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Agent" data-live-search="true" required>
                                <option disabled>Select Agent</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="type">Select Sale Type</label>
                            <select id="type" name="sales_type" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Sale Type">
                                <option value="Fresh" selected>Fresh</option>
                                <option value="Upsale">Upsale</option>
                                <option value="Recurring">Recurring</option>
                            </select>
                        </div>
                        <div id="showClient" class="form-group" style="display: none;">
                            <label for="client">Select Client Name</label>
                            <select id="client" name="client_id" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Client" data-live-search="true" required>
                                <option value="new">Create New Client</option>
                                @foreach($teamClients as $client)
                                    <option value="{{$client->id}}">{{$client->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" id="showName">
                            <label for="name">Enter Client Name</label>
                            <input type="text" id="name" class="form-control" placeholder="Name" name="name" required>
                        </div>
                        <div class="form-group" id="showEmail">
                            <label for="email">Enter Client Email</label>
                            <input type="email" id="email" class="form-control" placeholder="Email" name="email" required>
                        </div>
                        <div class="form-group" id="showPhone">
                            <label for="phone">Enter Client Phone Number</label>
                            <input type="text" id="phone" class="form-control" placeholder="Phone" name="phone" required>
                        </div>
                        <div id="showProject" class="form-group" style="display: none;">
                            <label for="projects">Select Project</label>
                            <select id="projects" name="project_id" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Project" data-live-search="true" required>
                                <option disabled>Select Client for project</option>
                            </select>
                        </div>
                        <div class="form-group" id="projectTileBlock">
                            <label for="projectTitle">Project Title</label>
                            <input type="text" id="projectTitle" class="form-control" placeholder="Project Title" name="project_title" required/>
                        </div>
                        <div class="form-group">
                            <label for="invoice_description">Enter Invoice Description</label>
                            <textarea id="invoice_description" class="form-control" placeholder="Description & Details" name="description"></textarea>
                            <div class="text-warning">
                                <small><span class="zmdi zmdi-info"></span> Above description is optional.</small></div>
                        </div>
                        <div class="form-group">
                            <label for="cur_symbol">Select Currency</label>
                            <select name="cur_symbol" class="form-control" id="cur_symbol" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true">
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                                <option value="GBP">GBP</option>
                                <option value="AUD">AUD</option>
                                <option value="CAD">CAD</option>
                            </select>
                        </div>
                        <div class="form-group" style="display:none">
                            <label>Select Payment Method</label>
                            <select id="payment_gateway" name="payment_gateway" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Payment Merchant" required>
                                <option value="authorize" selected>Authorize.Net</option>
                                {{--<option value="paypal">PayPal</option>--}}
                            </select>
                        </div>
                        <div class="form-group " style="display:none">
                            <label>Select Payment Split</label>
                            <select id="is_split" name="is_split" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" required>
                                <option value="1">Yes</option>
                                <option value="0" selected>No</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="amount">Enter Amount</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text cxm-currency-symbol-icon"><i class="zmdi zmdi-money"></i></span>
                                </div>
                                <input type="number" id="amount" class="form-control" placeholder="Amount" name="value" max="{{env('PAYMENT_LIMIT')}}" required/>
                            </div>
                        </div>
                        {{--
                        <div class="xform-group">
                            <label class="text-muted">
                                <input type="checkbox" name="taxable" id="taxable" value="1" checked="">
                                Taxable?
                            </label>
                        </div>
                        --}}
                        <div class="custom-control custom-switch mb-2">
                            <input type="checkbox" class="custom-control-input toggle-class" id="taxable" name="taxable" value="1" checked>
                            <label class="custom-control-label" for="taxable">Taxable?</label>
                        </div>
                        <div class="form-group" id="taxField">
                            <label for="tax">Enter Tax Percentage</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">%</i></span>
                                </div>
                                <input type="hidden" id="tax_amount" class="form-control" name="taxAmount" value="0">
                                <input type="number" name="tax" id="tax" class="form-control" placeholder="Tax" value="0"/>
                            </div>
                        </div>
                        <div class="form-group" id="totalAmount">
                            <label for="total_amount">Total Amount</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text cxm-currency-symbol-icon"><i class="zmdi zmdi-money"></i></span>
                                </div>
                                <input type="text" name="total_amount" class="form-control" placeholder="Total Amount" id="total_amount" value="0" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="due_date">Enter Due Date</label>
                            <input type="date" id="due_date" class="form-control" placeholder="Due Date" name="due_date" value="{{ date('Y-m-d') }}" required/>
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
                            <select id="edit_brand_key" name="brand_key" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Brand" required>
                                @foreach($assign_brands as $assign_brand)
                                    <option value="{{$assign_brand->brand_key}}">{{$assign_brand->getBrandName->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select id="edit_agent_id" name="agent_id" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Sale Agent" required>
                                @foreach($members as $member)
                                    <option value="{{$member->id}}">{{$member->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select id="sales_type" name="sales_type" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Sale Type" required>
                                <option value="">Please Select</option>
                                <option value="Fresh">Fresh</option>
                                <option value="Upsale">Upsale</option>
                                <option value="Recurring">Recurring</option>
                            </select>
                        </div>
                        {{-- <div class="form-group">
                             <select id="sales_status" name="status" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Status" required>
                                 <option value="draft">Draft</option>
                                 <option value="due">Due</option>
                             </select>
                         </div>--}}
                        <div class="form-group">
                            <textarea id="edit_invoice_description" class="form-control" placeholder="Description & Details" name="description"></textarea>
                        </div>
                        <div class="form-group">
                            <select name="edit_cur_symbol" class="form-control" id="edit_cur_symbol" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true">
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                                <option value="GBP">GBP</option>
                                <option value="AUD">AUD</option>
                                <option value="CAD">CAD</option>
                            </select>
                        </div>
                        <div class="form-group d-none">
                            <label>Select Payment Method</label>
                            <select id="edit_payment_gateway" name="edit_payment_gateway" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Payment Merchant" disabled>
                                <option value="authorize" selected>Authorize.Net</option>
                                {{--<option value="paypal">PayPal</option>--}}
                            </select>
                        </div>
                        <div class="form-group " style="display:none">
                            <label>Select Payment Split</label>
                            <select id="edit_is_split" name="edit_is_split" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" required>
                                <option value="1">Yes</option>
                                <option value="0" selected>No</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text cxm-currency-symbol-icon"><i class="zmdi zmdi-money"></i></span>
                                </div>
                                <input type="number" id="edit_amount" class="form-control" placeholder="Amount" max='5999' name="value" required/>
                            </div>
                        </div>
                        {{--
                        <div class="form-group">
                            <label class="text-muted">
                                <input type="checkbox" name="edit_taxable" id="edit_taxable" value="1" checked> Taxable?
                            </label>
                        </div>
                        --}}
                        <div class="custom-control custom-switch mb-2">
                            <input type="checkbox" class="custom-control-input toggle-class" id="edit_taxable" name="edit_taxable" value="1" checked>
                            <label class="custom-control-label" for="edit_taxable">Taxable?</label>
                        </div>
                        <div class="form-group" id="edit_taxField">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">%</i></span>
                                </div>
                                <input type="hidden" id="edit_tax_amount" class="form-control" name="edit_taxAmount" value="0">
                                <input type="number" name="edit_tax" id="edit_tax" class="form-control" placeholder="Tax"/>
                            </div>
                        </div>
                        <div class="form-group" id="edit_totalAmount">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text cxm-currency-symbol-icon"><i class="zmdi zmdi-money"></i></span>
                                </div>
                                <input type="text" name="edit_total_amount" class="form-control" placeholder="Total Amount" id="edit_total_amount" value="0" readonly>
                            </div>
                        </div>
                        <div class="xform-group">
                            <input type="date" id="edit_due_date" class="form-control" placeholder="Due Date" value="{{ date('Y-m-d') }}" name="due_date" required/>
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

@endsection

@push('cxmScripts')

    <script src="{{ asset('assets/plugins/momentjs/moment.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    @include('invoice.script')
    @include('includes.currency-change')

    <script>
        function getParam() {
            window.location.href = "{{ route('user.invoices.index') }}?brandKey=" + encodeURIComponent($('#brand').val()) + "&dateRange=" + encodeURIComponent($('#date-range').val());
        }

        $(function () {
            $(document).ready(function () {
                $('#InvoiceTable').DataTable().destroy();
                $('#InvoiceTable').DataTable({
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

                $('#brand').on('change', getParam);
            });
        });
    </script>

@endpush
