<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <title>{{ $invoiceData->brandName }} Secure Payment Terminal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ $invoiceData->fav }}" type="image/webp">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Begin CSS -->
    <link href="{{ asset('assets/css/payment/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/payment/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/sweetalert/sweetalert.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/payment/helpers.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/payment/app.css') }}" rel="stylesheet">

    <!-- Begin JS -->
    <script type="text/javascript">
        var checkNotification = false;
    </script>
    <script src="{{ asset('assets/css/payment/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/css/payment/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/css/payment/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/css/payment/bootstrap-maxlength.js') }}"></script>
    <!-- <script src="{{ asset('assets/css/payment/sweet-alert.min.js') }}"></script> -->
    <script src="{{ asset('assets/plugins/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/css/payment/jquery.form.min.js') }}"></script>
    <script src="{{ asset('assets/css/payment/jquery.jGet.js') }}"></script>
    <script src="{{ asset('assets/css/payment/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/css/payment/jquery.validate.additional-methods.min.js') }}"></script>
    <script src="{{ asset('assets/css/payment/app.js') }}"></script>

    <!--[if lt IE 9]>
    <script src="js/html5shiv.min.js"></script>
    <![endif]-->

    <style>.card-type-image {
            background: transparent url("{{ asset('assets/images/credit-cards.jpg') }}") 0 0 no-repeat;
        }</style>
</head>

<body class="terminal-body">

<noscript>
    <div class="alert alert-danger mt20neg">
        <div class="container aligncenter">
            <strong>Oops!</strong> It looks like your browser doesn't have Javascript enabled. Please enable Javascript
            to use this website.
        </div>
    </div>
</noscript>

@if($invoiceData->countryName == 'Pakistan')
    {{--<style>.swal-text{text-align:center;}</style>
    <script>swal("Alert!", "You are using non-US IP, please use the correct IP for payment", "error", {buttons: false, closeOnClickOutside: false, closeOnEsc: false});</script>--}}
@endif

<div class="container terminal-wrapper">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="colorprimary">
                    <img src="{{ $invoiceData->brandlogo }}" style="width: 250px;"><br/>
                    <small style="font-size: 13px; padding: 0 0 0 33px;">{{ $invoiceData->brandName }} Secure Payment
                        Terminal</small>
                </h2>
            </div>
            <div class="col-md-4">
                <h1 class="text-right text-uppercase {!! ($invoiceData->status == 'paid')?'text-success' :'text-danger' !!}">{{ $invoiceData->status }}</h1>
            </div>
        </div>
    </div>

    @if($invoiceData->status == 'paid')
        <div class="alert alert-success">
            <strong><i class="fa fa-check"></i> This invoice has already been paid!</strong><br>Payment for this invoice
            was received on <b>{{$invoiceData->updated_at->format('j F, Y')}}</b>.
        </div>
    @endif

    <form method="POST" class="validate form-horizontal " id="order_form">
        <input type="hidden" id="team_key" name="team_key" value="{{ $invoiceData->team_key }}">
        <input type="hidden" id="brand_key" name="brand_key" class="enable-subscriptions"
               value="{{ $invoiceData->brand_key }}">
        <input type="hidden" id="creatorid" name="creatorid" class="enable-subscriptions"
               value="{{ $invoiceData->creatorid }}">
        <input type="hidden" id="agentid" name="agentid" class="enable-subscriptions"
               value="{{ $invoiceData->agent_id }}">
        <input type="hidden" id="clientid" name="clientid" class="enable-subscriptions"
               value="{{ $invoiceData->clientid }}">
        <input type="hidden" id="invoiceid" name="invoiceid" class="enable-subscriptions"
               value="{{ $invoiceData->invoice_key }}">
        <input type="hidden" id="projectid" name="projectid" class="enable-subscriptions"
               value="{{ $invoiceData->project_id }}">
        <input type="hidden" id="salesType" name="salestype" class="enable-subscriptions"
               value="{{ $invoiceData->sales_type }}">
        <input type="hidden" id="payment_gateway" name="payment_gateway" class="enable-subscriptions" value="authorize">

        <input type="hidden" name="tkn" id="tkn" value="{{ $invoiceData->invoice_key }}">
        <input type="hidden" name="ip" value="{{ $invoiceData->clientip }}">
        <input type="hidden" name="city" value="{{ $invoiceData->cityName }}">
        <input type="hidden" name="state" value="{{ $invoiceData->stateName }}">
        <input type="hidden" name="country" value="{{ $invoiceData->countryName }}">
        <input type="hidden" name="brand_url" value="{{ $invoiceData->brandurl }}">
        <input type="hidden" name="date_stamp" value="<?php echo date("Y-m-d h:i:s A"); ?>">
        <input type="hidden" name="brand_name" value="{{ $invoiceData->brandName }}">
        <input type="hidden" name="source" value="New CRM">
        <input type="hidden" name="merchant" value="{{ $invoiceData->merchant }}">
        <input type="hidden" id="card_type" name="card_type" value="">
        <input type="hidden" name="amount"
               value="{{ ($invoiceData->total_amount != 0)?$invoiceData->total_amount : $invoiceData->final_amount }}">

        <div class="row">
            <div class="col-md-6">
                <h3 class="colorgray mb30">Payment Details</h3>
                <div class="form-group">
                    <label class="col-md-3 control-label"><span class="colordanger">*</span>Amount</label>
                    <div class="col-md-9">
                        <div class="input-group">
                            <span class="input-group-addon">{{ $invoiceData->currency_symbol }}</span>
                            <input type="text" id="final_amount" name="final_amount" class="form-control"
                                   placeholder="0.00" value="{{ $invoiceData->final_amount }}" readonly>
                        </div>
                    </div>
                </div>
                @if($invoiceData->tax_amount != 0)
                    <div class="form-group">
                        <label class="col-md-3 control-label"><span
                                class="colordanger">*</span>{{$invoiceData->tax_percentage}}% Tax</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-addon">{{ $invoiceData->currency_symbol }}</span>
                                <input type="text" id="tax" name="tax" class="form-control" placeholder="0.00"
                                       value="{{ $invoiceData->tax_amount }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"><span class="colordanger">*</span>Net Amount</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-addon">{{ $invoiceData->currency_symbol }}</span>
                                <input type="text" id="amount" name="amount" class="form-control" placeholder="0.00"
                                       data-rule-required="true" data-rule-number="true"
                                       value="{{ $invoiceData->total_amount?$invoiceData->total_amount : $invoiceData->final_amount }}"
                                       readonly>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="form-group">
                    <label class="col-md-3 control-label"><span class="colordanger">*</span>Description</label>
                    <div class="col-md-9">
                        <textarea id="description" name="description" class="form-control h55 maxlength" maxlength="120"
                                  placeholder="Description"
                                  data-rule-required="true" disabled>{{ $invoiceData->invoice_descriptione }}</textarea>
                    </div>
                </div>

                <hr class="visible-xs visible-sm">

                <h3 class="colorgray mt40 mb30">Your Information</h3>
                <div class="form-group">
                    <label class="control-label col-md-3"><span class="colordanger">*</span>Name</label>
                    <div class="col-md-9">
                        <input type="text" id="name" name="name" class="form-control" placeholder="Name"
                               value="{{ $invoiceData->clientname }}" data-rule-required="true" disabled >
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3"><span class="colordanger">*</span>Email</label>
                    <div class="col-md-9">
                        <input type="text" id="email" name="email" class="form-control" placeholder="Email"
                               value="{{ $invoiceData->clientemail }}" data-rule-required="true" data-rule-email="true" disabled >
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3"><span class="colordanger">*</span>Phone</label>
                    <div class="col-md-9">
                        <input type="text" id="email" name="phone" class="form-control" placeholder="Phone"
                               value="{{ $invoiceData->clientphone }}" data-rule-required="true" disabled >
                    </div>
                </div>

            </div>
            <div class="col-md-6">
                <hr class="visible-xs visible-sm">
                <h3 class="colorgray mb30">
                    Payment Method
                    <div class="floatright">
                        <img src="{{ asset('assets/images/credit-cards.jpg') }}" class="">
                    </div>
                </h3>

                <div class="creditcard-content">
                    <div class="form-group">
                        <label class="control-label col-md-3"><span class="colordanger">*</span>Name on Card</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <input type="text" id="card_name" name="card_name" class="form-control"
                                       placeholder="Name on Card" value="" data-rule-required="true" disabled>
                                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3"><span class="colordanger">*</span>Card Number</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <input maxlength="16" type="text" id="card_number" name="card_number"
                                       class="form-control card-number" placeholder="Card Number" value=""
                                       data-rule-required="true" data-rule-creditcard="true" disabled>
                                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                            </div>
                            <div class="card-type-image none"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3"><span class="colordanger">*</span>Expiration/CVC</label>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-4 col-xs-4 pr5">
                                    <select id="card_exp_month" name="card_exp_month" class="form-control"
                                            data-rule-required="true" disabled>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ $i == Carbon\Carbon::now()->month ? 'selected' : '' }}>
                                                {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-4 col-xs-4 pl5 pr5">
                                    <select id="card_exp_year" name="card_exp_year" class="form-control"
                                            data-rule-required="true" disabled>
                                        @for ($year = Carbon\Carbon::now()->year; $year <= Carbon\Carbon::now()->addYears(10)->year; $year++)
                                            <option value="{{ $year }}" {{ $year == Carbon\Carbon::now()->year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-4 col-xs-4 pl5">
                                    <div class="input-group">
                                        <input type="text" id="card_cvv" name="card_cvv" name="cvc" class="form-control"
                                               placeholder="CVV" value="" data-rule-required="true" disabled>
                                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt50">
                    <div class="col-md-12 alignright">
                        <div class="creditcard-content">
                            @if($invoiceData->status == 'due')
                                <button id="pay_button" type="submit" class="btn btn-lg btn-primary submit-button mb20">
											<span
                                                class="total show">Total: {{ $invoiceData->currency_symbol }}<span>{{ ($invoiceData->total_amount != 0)?$invoiceData->total_amount : $invoiceData->final_amount }}</span>
											<small></small></span>
                                    <i class="fa fa-check"></i> Submit Payment
                                </button>
                            @else
                                <button class="btn btn-lg btn-primary submit-button mb20" disabled="">
                                    <i class="fa fa-check"></i> Submit Payment
                                </button>
                            @endif
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        </div>
    </form>
    <br>
    <div class="alert alert-warning text-center" role="alert">On your bank statement the descriptor should be
        <b>{{ $invoiceData->merchant }}</b> as Merchant Name.
    </div>
</div>
</body>

<script>
    // $('#order_form').on('submit', function(e){
    $('#pay_button').on('click', function (e) {
        e.preventDefault();

        $(this).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');
        $(this).attr('disabled', true);

        let cxmFrmData = $(this).parents('form').serializeArray();
        let cxmFrmDataJson = {};
        $.map(cxmFrmData, function (n, i) {
            cxmFrmDataJson[n['name']] = n['value'];
        });
        // console.log(cxmFrmDataJson);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "GET",
            dataType: "json",
            url: "{{ route('pay') }}",
            data: cxmFrmDataJson,
            success: function (data) {

                $("#order_form")[0].reset();
                console.log(data);
                var a = data.type.split('_');
                var Msg_head = '';
                if (a[0] == 'error') {
                    Msg_head = 'Error!';
                } else {
                    Msg_head = 'Good job!';
                }
                swal(Msg_head, data.message, a[0])
                    .then(() => {
                        location.reload();
                    });
            },
            error: function (data) {
                console.log(data);
                let message = "Request Fail!";
                if(data && data.responseJSON && data.responseJSON.error){
                    message = data.responseJSON.error;
                }

                swal("Error!", message, "error");
            }
        });
    });
</script>
<script>
    $(function () {
        $('.card-number').on('keyup', function () {
            let cxmCardNumber = $(this).val();
            let cxmCardType = app.getCardType(cxmCardNumber);
            $('#card_type').val(cxmCardType);
        });
    });
</script>
</html>
