@extends('layouts.app')
@section('content')

<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Payment Detail</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('userpayment.index') }}">Payment</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    @include('includes.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="body">
                            <div class="row">
                                <div class="col-xl-12 col-lg-12 col-md-12">
                                    <div class="product details">
                                        <h3 class="product-title mb-0">{{$payment->name}}</h3>
                                        <div class="price mt-0">Payment ID: <span class="col-amber">{{ '#'.$payment->id }}</span></div>
                                        <hr class="border-info">
                                        <div class="product-description border p-3 mb-4">{!! ($payment->payment_notes) ? $payment->payment_notes : "No Lead Description" !!}</div>
                                        <div class="row">
                                            <div class="col-xl-4 col-lg-4 col-md-4">
                                                <div class="border p-3 mb-3">
                                                    <div><span class="zmdi zmdi-account" title="Contact"></span> {{ $payment->name }}</div>
                                                    <hr class="border-info my-2">
                                                    <div><span class="zmdi zmdi-phone" title="Telephone"></span> {{ $payment->phone }}</div>
                                                    <hr class="border-info my-2">
                                                    <div><span class="zmdi zmdi-email" title="Email"></span> {{ $payment->email }}</div>
                                                    <hr class="border-info my-2">
                                                    <div><span class="zmdi zmdi-accounts" title="Team Name"></span> {{ $payment->teamName }}</div>
                                                    <hr class="border-info my-2">
                                                    <div><span class="zmdi zmdi-blogger" title="Brand"></span> {{ $payment->brandName }}</div>
                                                    <hr class="border-info my-2">
                                                    <div><span class="zmdi zmdi-account" title="Creator Name"></span> {{ $payment->creatorName }}</div>
                                                    <hr class="border-info my-2">
                                                    <div><span class="zmdi zmdi-account-circle" title="Agent Name"></span> {{ $payment->agentName }}</div>
                                                    <hr class="border-info my-2">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4">
                                                <div class="border p-3 mb-3">
                                                    <div><span class="zmdi zmdi-money" title="Value"></span> {{ $payment->amount }}</div>
                                                    <hr class="border-info my-2">
                                                    <div><span class="zmdi zmdi-file-text" title="Invoice Number"></span> {{ $payment->invoice_id }}</div>
                                                    <hr class="border-info my-2">
                                                    <div><span class="zmdi zmdi-balance" title="Keyword"></span> {{ $payment->payment_gateway }}</div>
                                                    <hr class="border-info my-2">
                                                    <div><span class="zmdi zmdi-windows" title="Tracking ID"></span> {{ $payment->authorizenet_transaction_id }}</div>
                                                    <hr class="border-info my-2">
                                                    <div><span class="zmdi zmdi-google" title="Merchant Auth ID"></span> {{ $payment->auth_id }}</div>
                                                    <hr class="border-info my-2">
                                                    <div><span title="Payment Status"></span>
                                                        @if($payment->payment_status == 1)
                                                            <span class="badge badge-success rounded-pill">Success</span>
                                                        @elseif($payment->payment_status == 2)
                                                            <span class="badge badge-warning rounded-pill">Refund</span>
                                                        @else
                                                        <span class="badge badge-danger rounded-pill">Charge Back</span>
                                                        @endif
                                                    </div>
                                                    <hr class="border-info my-2">

                                                    <div><span class="zmdi zmdi-calendar" title="Date Created"></span> {{ $payment->created_at->format('j F, Y') }}</div>
                                                    <hr class="border-info my-2">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4">
                                                <div class="border p-3 mb-3">
                                                    <div><span class="zmdi zmdi-cast-connected" title="Source"></span> {{ $payment->sales_type }}</div>
                                                    <hr class="border-info my-2">
                                                    <div><span class="zmdi zmdi-assignment" title="Project Title"></span> {{ $payment->projectTitle }}</div>
                                                    <hr class="border-info my-2">
                                                    @if($payment->audio != "")
                                                    <div>
                                                        <audio controls>
                                                            <source src="{{ asset('/uploads') }}/{{$payment->audio}}" type="audio/mpeg">
                                                            Your browser does not support the audio element.
                                                        </audio>
                                                    </div>
                                                    <hr class="border-info my-2">
                                                    @endif
                                                    <div><span title="Payment Status"></span>
                                                        @if($payment->compliance_verified == 1)
                                                            <span class="badge badge-success rounded-pill">Compliance Verified</span>
                                                        @else
                                                        <span class="badge badge-danger rounded-pill">Compliance Unverified</span>
                                                        @endif
                                                    </div>
                                                    <hr class="border-info my-2">
                                                    <div><span title="Payment Status"></span>
                                                        @if($payment->head_verified == 1)
                                                            <span class="badge badge-success rounded-pill">Head Verified</span>
                                                        @else
                                                        <span class="badge badge-warning rounded-pill">Head Unverified</span>
                                                        @endif
                                                    </div>
                                                    <hr class="border-info my-2">

                                                </div>
                                            </div>
                                        </div>
                                        @if($payment->compliance_varified_note != "")
                                        <div class="product-description border p-3 mb-4">
                                            {!! ($payment->compliance_varified_note) ? $payment->compliance_varified_note : "No Lead Description" !!}
                                        </div>
                                        @endif

                                        @php $refundData = \App\Models\Refund::where('payment_id' ,$payment->id)->first();@endphp
                                        @if($refundData)
                                        <div class="product-description border p-3 mb-4 ">
                                            <b><span style='text-info'>Refund / Charge Back Reason:</span></b><br> {!! ($refundData->reason) ? $refundData->reason : "No Reson Found" !!}
                                        </div>
                                        @endif


                                        <div class="action mt-3">
                                        @if(Auth::user()->type == 'qa')
                                        <button id="varifiedPayment" title="Create Team Member" class="btn btn-info btn-round" type="button" data-toggle="modal" data-target="#varifiedModal"><span class="zmdi zmdi-check"></span> Verified Payment</button>
                                        @endif
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Payment Varified -->
<link rel="stylesheet" href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}">
<div class="modal fade" id="varifiedModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Verified Payment</h4>
            </div>
            <form id="payment_varified_form" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="payment_id" class="form-control" name="payment_id" value="{{$payment->id}}">
                <div class="modal-body">
                    <div class="form-group">
                        <input id="abc_file" type="file" name="upload_file" class="dropify" data-allowed-file-extensions="mp3" data-max-file-size="5M">
                    </div>
                    <div class="form-group">
                        <textarea id="edit_project_description" class="form-control" placeholder="Description & Details" name="description"></textarea>
                    </div>
                    {{--<div class="form-group">
                        <select id="type" name="varified" class="form-control show-tick ms select2" data-placeholder="Select Type" required>
                            <option value="1">Verified</option>
                            <option value="0">Unverified</option>
                        </select>
                    </div>--}}
                    <div class="custom-control custom-switch">
                        <input name="varified" type="checkbox" class="custom-control-input toggle-class" id="cxmSwitch1" value="1" checked="">
                        <label class="custom-control-label" for="cxmSwitch1">Verified/Unverified</label>
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
    <script src="{{ asset('assets/plugins/dropify/js/dropify.min.js') }}"></script>
    @include('payment.script')

    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
