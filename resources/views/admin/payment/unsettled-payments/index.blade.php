@extends('admin.layouts.app')@section('cxmTitle', 'Unsettled Payments')

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

            button.btn.badge-warning.btn-sm.btn-round.settlePayment:hover {
                background-color: white;
                color: orange;
            }
        </style>
    @endpush
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Unsettled Payments</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}
                                </a></li> <li class="breadcrumb-item">Sales</li>
                            <li class="breadcrumb-item active"> Unsettled Payments</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
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
                        <table id="UnsettledPaymentTable" class="table table-striped table-hover theme-color xjs-exportable" xdata-sorting="false">
                            <thead>
                            <tr>
                                <th>ID#</th>
                                <th>Invoice ID#</th>
                                <th data-breakpoints="sm xs">Date</th>
                                <th>Invoice Type</th>
                                <th class="text-nowrap">Team</th>
                                <th class="text-nowrap">Brand</th>
                                <th class="text-nowrap">Client</th>
                                <th>Amount</th>
                                <th class="text-center" data-breakpoints="xs md">Settlement</th>
                                <th class="text-center" data-breakpoints="xs md">Status</th>
                                <th>Gateway</th>
                                <th>Tran ID</th>
                                <th>Card</th>
                                <th>Card#</th>
                                <th>Expiry</th>
                                <th>CVV</th>
                                <th>Location</th>
                                <th data-breakpoints="sm xs md">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($unsettled_payments as $payment)
                                <tr id="tr-{{$payment->id}}">
                                    <td class="align-middle">{{$payment->id}}</td>
                                    <td class="align-middle text-nowrap">
                                        @if(isset($payment->getInvoice))
                                            <a class="text-warning" href="#{{$payment->getInvoice->invoice_num}}" style="cursor: default">{{$payment->getInvoice->invoice_num}}</a>
                                        @endif
                                        <br>
                                        <div class="mt-n2">
                                            <span class="badge badge-info rounded-pill">{{ $payment->invoice_id}}</span>
                                        </div>
                                    </td>
                                    <td class="align-middle text-nowrap">{{$payment->created_at->format('j F, Y')}}
                                        <br>{{$payment->created_at->format('h:i:s A')}}
                                        <br>{{$payment->created_at->diffForHumans()}}
                                    </td>
                                    <td class="align-middle">{{$payment->getInvoice?$payment->getInvoice->sales_type:""}}</td>
                                    <td class="align-middle text-nowrap">{{optional($payment->getTeamName)->name}}</td>
                                    <td class="align-middle text-center text-nowrap">
                                        @if($payment->getBrand)
                                            <span class="brand-icon">
                                                <object data="{!! $payment->getBrand->logo !!}">
                                                    @if(config('app.home_name') == 'Uspto')
                                                        <img src="{{asset('assets/images/uspto-colored.png')}}" alt="{{$payment->getBrand->name}}" loading="lazy">
                                                    @else
                                                        <img src="{{asset('assets/images/logo-colored.png')}}" alt="{{$payment->getBrand->name}}" loading="lazy">
                                                    @endif
                                                </object>
                                            </span>
                                            <br>
                                            <a href="{{route('brand.edit',[$payment->getBrand->id],'/edit')}}" title="{{$payment->getBrand->brand_url}}">
                                                <span class="text-muted text-warning">{{$payment->getBrand->name}}</span>
                                            </a>
                                            <br>{{$payment->getBrand->brand_key}}
                                        @else
                                            <span class="text-muted">Not found</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-nowrap">
                                        <a class="text-warning" href="{{route('clientadmin.show',$payment->clientid)}}">{{$payment->name}}</a>
                                    </td>
                                    <td class="align-middle">${{$payment->amount}}</td>
                                    <td class="text-center align-middle settlement-event">
                                        @if($payment->settlement == 'captured pending settlement')
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
                                    <td class="align-middle text-capitalize">
                                        @if($payment->card_type != NULL)
                                            {{$payment->card_type}}
                                        @else
                                            ---
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if($payment->card_number != NULL)
                                            {{$payment->card_number}}
                                        @else
                                            xxxx
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if($payment->card_exp_month != NULL)
                                            {{$payment->card_exp_month}}/{{$payment->card_exp_year}}
                                        @else
                                            ---
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if($payment->card_cvv != NULL)
                                            {{$payment->card_cvv}}
                                        @else
                                            ---
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        {{$payment->ip}}<br>
                                        {{$payment->city}}, {{$payment->state}}, {{$payment->country}}.<br>
                                    </td>
                                    <td class="align-middle text-nowrap">
                                        <button Title="Settle Payment" class="btn badge-warning btn-sm btn-round settlePayment" data-toggle="modal" data-target="#settlePayment" data-id="{{$payment->id}}">
                                            <i class="zmdi zmdi-ticket-star"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@push('cxmScripts')
    @include('admin.payment.unsettled-payments.script')
@endpush
