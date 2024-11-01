{{--@extends('admin.layouts.app')--}}

{{--@section('cxmTitle', 'Split Payments')--}}

{{--@section('content')--}}
{{--    <section class="content">--}}
{{--        <div class="body_scroll">--}}
{{--            <div class="block-header">--}}
{{--                <div class="row">--}}
{{--                    <div class="col-lg-7 col-md-6 col-sm-12">--}}
{{--                        <h2>Split Payments</h2>--}}
{{--                        <ul class="breadcrumb">--}}
{{--                            <li class="breadcrumb-item"><a href="#"><i class="zmdi zmdi-home"></i> TG</a></li>--}}
{{--                            <li class="breadcrumb-item">Sales</li>--}}
{{--                            <li class="breadcrumb-item active">Split Payments</li>--}}
{{--                        </ul>--}}
{{--                        <button class="btn btn-primary btn-icon mobile_menu" type="button"><i--}}
{{--                                class="zmdi zmdi-sort-amount-desc"></i></button>--}}
{{--                    </div>--}}
{{--                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">--}}
{{--                        @include('includes.admin.cxm-top-right-toggle-btn')--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="card">--}}
{{--                <div class="table-responsive mb-3">--}}
{{--                    @if(Auth::guard('admin')->user()->type == 'super')--}}
{{--                        <table id="SplitPaymentTable" class="table table-striped table-hover theme-color xjs-exportable"--}}
{{--                               xdata-sorting="false" xdata-page-size="5" xdata-paging="true" xdata-filtering="true">--}}
{{--                            <thead>--}}
{{--                            <tr>--}}
{{--                                <th>ID #</th>--}}
{{--                                <th>Invoice #</th>--}}
{{--                                <th data-breakpoints="sm xs">Date</th>--}}
{{--                                <th>Invoice Status</th>--}}
{{--                                <th>Transaction Id</th>--}}
{{--                                <th>Amount</th>--}}
{{--                                <th class="text-center" data-breakpoints="xs md">Status</th>--}}
{{--                                <th class="text-center" data-breakpoints="sm xs md">Action</th>--}}
{{--                            </tr>--}}
{{--                            </thead>--}}
{{--                            <tbody>--}}
{{--                            @foreach($splitPayments as $key => $splitPayment)--}}
{{--                                <tr>--}}
{{--                                    <td class="align-middle">{{$splitPayment->id}}</td>--}}
{{--                                    <td class="align-middle">--}}
{{--                                        <a class="text-warning" href="#" data-toggle="modal"--}}
{{--                                           data-target="#logModal{{ isset($splitPayment->getInvoice) && $splitPayment->getInvoice->count() > 0 ? $splitPayment->getInvoice->invoice_key : ""}}">{{ isset($splitPayment->getInvoice) && $splitPayment->getInvoice->count() > 0? $splitPayment->getInvoice->invoice_num : ""}}</a>--}}
{{--                                        <div class="mt-n2"><span--}}
{{--                                                class="badge badge-info rounded-pill">{{isset($splitPayment->getInvoice) && $splitPayment->getInvoice->count() > 0 ?  $splitPayment->getInvoice->invoice_key : ""}}</span>--}}
{{--                                        </div>--}}
{{--                                        <div class="modal fade"--}}
{{--                                             id="logModal{{isset($splitPayment->getInvoice) && $splitPayment->getInvoice->count() > 0 ?  $splitPayment->getInvoice->invoice_key : ""}}"--}}
{{--                                             tabindex="-1"--}}
{{--                                             role="dialog">--}}
{{--                                            <div class="modal-dialog modal-lg modal-centered" role="document">--}}
{{--                                                <div class="modal-content">--}}
{{--                                                    <div class="modal-header">--}}
{{--                                                        <h4 class="title" id="defaultModalLabel">Log</h4>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="modal-body">--}}
{{--                                                        @php $logs = \App\Models\PaymentTransactionsLog::where('invoiceid' ,$splitPayment->invoice_id)->get(); @endphp--}}
{{--                                                        @if(count($logs))--}}
{{--                                                            <div class="table-responsive">--}}
{{--                                                                <table id="InvoiceTable"--}}
{{--                                                                       class="table table-striped table-hover table-sm theme-color js-exportable"--}}
{{--                                                                       xdata-sorting="false">--}}
{{--                                                                    <thead>--}}
{{--                                                                    <tr>--}}
{{--                                                                        <th>ID #</th>--}}
{{--                                                                        <th>Invoice #</th>--}}
{{--                                                                        <th>Amount</th>--}}
{{--                                                                        <th>Response Reason</th>--}}
{{--                                                                        <th data-breakpoints="sm xs">Date</th>--}}
{{--                                                                    </tr>--}}
{{--                                                                    </thead>--}}
{{--                                                                    <tbody>--}}
{{--                                                                    @foreach ($logs as $log)--}}
{{--                                                                        <tr>--}}
{{--                                                                            <td class="align-middle">{{$log->id}}</td>--}}
{{--                                                                            <td class="align-middle">{{$log->invoiceid}}</td>--}}
{{--                                                                            <td class="align-middle">--}}
{{--                                                                                ${{$log->amount}}</td>--}}
{{--                                                                            <td class="align-middle">{{$log->response_reason}}</td>--}}
{{--                                                                            <td class="align-middle">{{$log->created_at->format('j F, Y')}}--}}
{{--                                                                                <br>{{$log->created_at->format('h:i:s A')}}--}}
{{--                                                                                <br>{{$log->created_at->diffForHumans()}}--}}
{{--                                                                            </td>--}}
{{--                                                                        </tr>--}}
{{--                                                                    @endforeach--}}
{{--                                                                    </tbody>--}}
{{--                                                                </table>--}}
{{--                                                            </div>--}}
{{--                                                        @else--}}
{{--                                                            <div class="alert alert-info">No Data Found</div>--}}
{{--                                                        @endif--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </td>--}}
{{--                                    <td class="align-middle">{{$splitPayment->created_at->format('j F, Y')}}--}}
{{--                                        <br>{{$splitPayment->created_at->format('h:i:s A')}}--}}
{{--                                        <br>{{$splitPayment->created_at->diffForHumans()}}</td>--}}
{{--                                    <td class="align-middle">--}}
{{--                                        @if(isset($splitPayment->getInvoiceStatus))--}}
{{--                                            @if($splitPayment->getInvoiceStatus->status == 'draft')--}}
{{--                                                <span class="badge bg-grey rounded-pill">Draft</span>--}}
{{--                                            @elseif($splitPayment->getInvoiceStatus->status == 'due')--}}
{{--                                                <span class="badge bg-amber rounded-pill">Due</span>--}}
{{--                                            @elseif($splitPayment->getInvoiceStatus->status == 'refund')--}}
{{--                                                <span class="badge bg-pink rounded-pill">Refund</span>--}}
{{--                                            @elseif($splitPayment->getInvoiceStatus->status == 'chargeback')--}}
{{--                                                <span class="badge bg-red rounded-pill">Charge Back</span>--}}
{{--                                            @else--}}
{{--                                                <span class="badge badge-success rounded-pill">Paid</span>--}}
{{--                                            @endif--}}
{{--                                        @endif--}}
{{--                                    </td>--}}
{{--                                    <td class="align-middle">{{$splitPayment->transaction_id ? $splitPayment->transaction_id : "---"}}</td>--}}
{{--                                    <td class="align-middle">{{isset($splitPayment->getInvoiceCurrencySymbol) ? $splitPayment->getInvoiceCurrencySymbol->cur_symbol : "$"}} {{$splitPayment->amount}}</td>--}}
{{--                                    --}}{{--                                    <td class="align-middle">{{ \Carbon\Carbon::parse($splitPayment->created_at)->format('d/m/Y')}}</td>--}}
{{--                                    <td class="text-center align-middle">--}}
{{--                                        @if($splitPayment->status == 1)--}}
{{--                                            <span class="badge badge-success rounded-pill">Paid</span>--}}
{{--                                        @else--}}
{{--                                            <span class="badge badge-danger rounded-pill">Pending</span>--}}
{{--                                        @endif--}}
{{--                                    </td>--}}
{{--                                    <td class="text-center align-middle">--}}
{{--                                        @if($splitPayment->status != 1)--}}
{{--                                            <button data-id="{{$splitPayment->id}}" title="Pay Now" type="button"--}}
{{--                                                    class="btn btn-info btn-sm btn-round split-payment-pay-now"><i--}}
{{--                                                    class="zmdi zmdi-money"></i> Pay Now--}}
{{--                                            </button>--}}
{{--                                        @else--}}
{{--                                            <button title="Paid" type="button"--}}
{{--                                                    class="btn btn-success btn-sm btn-round"><i--}}
{{--                                                    class="zmdi zmdi-money"></i> Already Paid--}}
{{--                                            </button>--}}
{{--                                        @endif--}}
{{--                                    </td>--}}
{{--                                </tr>--}}
{{--                            @endforeach--}}
{{--                            </tbody>--}}
{{--                        </table>--}}
{{--                    @else--}}
{{--                        <table id="SplitPaymentTable" class="table table-striped table-hover theme-color xjs-exportable"--}}
{{--                               xdata-sorting="false" xdata-page-size="5" xdata-paging="true" xdata-filtering="true">--}}
{{--                            <thead>--}}
{{--                            <tr>--}}
{{--                                <th>ID #</th>--}}
{{--                                <th>Invoice #</th>--}}
{{--                                <th data-breakpoints="sm xs">Date</th>--}}
{{--                                <th>Invoice Status</th>--}}
{{--                                <th>Transaction Id</th>--}}
{{--                                <th>Amount</th>--}}
{{--                                <th class="text-center" data-breakpoints="xs md">Status</th>--}}
{{--                                <th class="text-center" data-breakpoints="sm xs md">Action</th>--}}
{{--                            </tr>--}}
{{--                            </thead>--}}
{{--                            <tbody>--}}
{{--                            @foreach($splitPayments as $key => $splitPayment)--}}
{{--                                <tr>--}}
{{--                                    <td class="align-middle">{{$splitPayment->id}}</td>--}}

{{--                                    <td class="align-middle">--}}
{{--                                        <a class="text-warning" href="#" data-toggle="modal"--}}
{{--                                           data-target="#logModal{{ isset($splitPayment->getInvoice)  && $splitPayment->getInvoice->count() > 0 ? $splitPayment->getInvoice->invoice_key : ""}}">{{ isset($splitPayment->getInvoice) && $splitPayment->getInvoice->count() > 0? $splitPayment->getInvoice->invoice_num : ""}}</a>--}}
{{--                                        <div class="mt-n2"><span--}}
{{--                                                class="badge badge-info rounded-pill">{{ isset($splitPayment->getInvoice)  && $splitPayment->getInvoice->count() > 0 ? $splitPayment->getInvoice->invoice_key : ""}}</span>--}}
{{--                                        </div>--}}
{{--                                        <div class="modal fade"--}}
{{--                                             id="logModal{{ isset($splitPayment->getInvoice)  && $splitPayment->getInvoice->count() > 0 ? $splitPayment->getInvoice->invoice_key : ""}}"--}}
{{--                                             tabindex="-1"--}}
{{--                                             role="dialog">--}}
{{--                                            <div class="modal-dialog modal-lg modal-centered" role="document">--}}
{{--                                                <div class="modal-content">--}}
{{--                                                    <div class="modal-header">--}}
{{--                                                        <h4 class="title" id="defaultModalLabel">Log</h4>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="modal-body">--}}
{{--                                                        @php $logs = \App\Models\PaymentTransactionsLog::where('invoiceid' ,$splitPayment->invoice_id)->get(); @endphp--}}
{{--                                                        @if(count($logs))--}}
{{--                                                            <div class="table-responsive">--}}
{{--                                                                <table id="InvoiceTable"--}}
{{--                                                                       class="table table-striped table-hover table-sm theme-color js-exportable"--}}
{{--                                                                       xdata-sorting="false">--}}
{{--                                                                    <thead>--}}
{{--                                                                    <tr>--}}
{{--                                                                        <th>ID #</th>--}}
{{--                                                                        <th>Invoice #</th>--}}
{{--                                                                        <th>Date</th>--}}
{{--                                                                        <th>Amount</th>--}}
{{--                                                                        <th>Response Reason</th>--}}
{{--                                                                        <th data-breakpoints="sm xs">Date</th>--}}
{{--                                                                    </tr>--}}
{{--                                                                    </thead>--}}
{{--                                                                    <tbody>--}}
{{--                                                                    @foreach ($logs as $log)--}}
{{--                                                                        <tr>--}}
{{--                                                                            <td class="align-middle">{{$log->id}}</td>--}}
{{--                                                                            <td class="align-middle">{{$log->invoiceid}}</td>--}}
{{--                                                                            <td class="align-middle">--}}
{{--                                                                                ${{$log->amount}}</td>--}}
{{--                                                                            <td class="align-middle">{{$log->response_reason}}</td>--}}
{{--                                                                            <td class="align-middle">{{$log->created_at->format('j F, Y')}}--}}
{{--                                                                                <br>{{$log->created_at->format('h:i:s A')}}--}}
{{--                                                                                <br>{{$log->created_at->diffForHumans()}}--}}
{{--                                                                            </td>--}}
{{--                                                                        </tr>--}}
{{--                                                                    @endforeach--}}
{{--                                                                    </tbody>--}}
{{--                                                                </table>--}}
{{--                                                            </div>--}}
{{--                                                        @else--}}
{{--                                                            <div class="alert alert-info">No Data Found</div>--}}
{{--                                                        @endif--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </td>--}}
{{--                                    <td class="align-middle">{{$splitPayment->created_at->format('j F, Y')}}--}}
{{--                                        <br>{{$splitPayment->created_at->format('h:i:s A')}}--}}
{{--                                        <br>{{$splitPayment->created_at->diffForHumans()}}</td>--}}
{{--                                    <td class="align-middle">--}}
{{--                                        @if(isset($splitPayment->getInvoiceStatus))--}}
{{--                                            @if($splitPayment->getInvoiceStatus->status == 'draft')--}}
{{--                                                <span class="badge bg-grey rounded-pill">Draft</span>--}}
{{--                                            @elseif($splitPayment->getInvoiceStatus->status == 'due')--}}
{{--                                                <span class="badge bg-amber rounded-pill">Due</span>--}}
{{--                                            @elseif($splitPayment->getInvoiceStatus->status == 'refund')--}}
{{--                                                <span class="badge bg-pink rounded-pill">Refund</span>--}}
{{--                                            @elseif($splitPayment->getInvoiceStatus->status == 'chargeback')--}}
{{--                                                <span class="badge bg-red rounded-pill">Charge Back</span>--}}
{{--                                            @else--}}
{{--                                                <span class="badge badge-success rounded-pill">Paid</span>--}}
{{--                                            @endif--}}
{{--                                        @endif--}}
{{--                                    </td>--}}
{{--                                    <td class="align-middle">{{$splitPayment->transaction_id != null ? $splitPayment->transaction_id : "---"}}</td>--}}
{{--                                    <td class="align-middle">{{isset($splitPayment->getInvoiceCurrencySymbol) ? $splitPayment->getInvoiceCurrencySymbol->cur_symbol : "$"}} {{$splitPayment->amount}}</td>--}}
{{--                                    --}}{{--                                    <td class="align-middle">{{ \Carbon\Carbon::parse($splitPayment->created_at)->format('d/m/Y')}}</td>--}}
{{--                                    <td class="text-center align-middle">--}}
{{--                                        @if($splitPayment->status == 1)--}}
{{--                                            <span class="badge badge-success rounded-pill">Paid</span>--}}
{{--                                        @else--}}
{{--                                            <span class="badge badge-danger rounded-pill">Pending</span>--}}
{{--                                        @endif--}}
{{--                                    </td>--}}
{{--                                    <td class="text-center align-middle">--}}
{{--                                        @if($splitPayment->status != 1)--}}
{{--                                            <button data-id="{{$splitPayment->id}}" title="Pay Now" type="button"--}}
{{--                                                    class="btn btn-info btn-sm btn-round split-payment-pay-now"><i--}}
{{--                                                    class="zmdi zmdi-money"></i> Pay Now--}}
{{--                                            </button>--}}
{{--                                        @else--}}
{{--                                            <button title="Paid" type="button"--}}
{{--                                                    class="btn btn-success btn-sm btn-round"><i--}}
{{--                                                    class="zmdi zmdi-money"></i> Already Paid--}}
{{--                                            </button>--}}
{{--                                        @endif--}}
{{--                                    </td>--}}
{{--                                </tr>--}}
{{--                            @endforeach--}}
{{--                            </tbody>--}}
{{--                        </table>--}}
{{--                    @endif--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </section>--}}
{{--@endsection--}}

{{--@push('cxmScripts')--}}
{{--    <script>--}}
{{--        $(document).ready(function () {--}}
{{--            $('#SplitPaymentTable').DataTable({--}}
{{--                dom: 'lBfrtip',--}}
{{--                buttons: [--}}
{{--                    'copy', 'csv', 'excel', 'pdf', 'print'--}}
{{--                ]--}}
{{--            });--}}
{{--        });--}}
{{--        $(function () {--}}
{{--            $('#SplitPaymentTable').on('click', '.split-payment-pay-now', function () {--}}
{{--                swal({--}}
{{--                    title: "Are you want to Pay now?",--}}
{{--                    text: "Press Yes, if you want to pay now this payment!",--}}
{{--                    icon: "warning",--}}
{{--                    buttons: {--}}
{{--                        cancel: {--}}
{{--                            text: "Cancel",--}}
{{--                            value: null,--}}
{{--                            visible: true,--}}
{{--                            className: "btn-warning",--}}
{{--                            closeModal: true,--}}
{{--                        },--}}
{{--                        confirm: {--}}
{{--                            text: "Yes, Pay Now!"--}}
{{--                        }--}}
{{--                    },--}}
{{--                    dangerMode: true,--}}
{{--                })--}}
{{--                    .then((payNow) => {--}}
{{--                        if (payNow) {--}}
{{--                            $('.page-loader-wrapper').show();--}}
{{--                            var split_payment_id = $(this).data('id');--}}

{{--                            $.ajax({--}}
{{--                                type: "GET",--}}
{{--                                url: "{{route('admin_pay_now_split_payments')}}/" + split_payment_id,--}}
{{--                                success: function (data) {--}}
{{--                                    $('.page-loader-wrapper').hide();--}}
{{--                                    swal("Good job!", "Split payment completed successfully!", "success");--}}
{{--                                    setTimeout(function () {--}}
{{--                                        window.location = '{{url("admin/split-payments")}}';--}}
{{--                                    }, 2000);--}}
{{--                                },--}}
{{--                                error: function (data) {--}}
{{--                                    $('.page-loader-wrapper').hide();--}}
{{--                                    console.log('Error:', data);--}}
{{--                                    var errorMessage = data.responseJSON.error || data.responseJSON.message || 'An unknown error occurred.';--}}
{{--                                    swal('Error', errorMessage, 'error');--}}
{{--                                    setTimeout(function () {--}}
{{--                                        window.location = '{{url("admin/split-payments")}}';--}}
{{--                                    }, 2000);--}}
{{--                                }--}}
{{--                            });--}}
{{--                        } else {--}}
{{--                            swal('Thank You', 'Thank you for your patience!', 'success', {buttons: false, timer: 2000});--}}
{{--                        }--}}
{{--                    });--}}
{{--            });--}}
{{--        });--}}

{{--    </script>--}}
{{--@endpush--}}
@extends('admin.layouts.app')

@section('cxmTitle', 'Split Payments')

@section('content')
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Split Payments</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                            <li class="breadcrumb-item">Sales</li>
                            <li class="breadcrumb-item active">Split Payments</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button"><i
                                class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        @include('includes.admin.cxm-top-right-toggle-btn')
                    </div>
                </div>
            </div>

            <!-- Add tabs for Paid Invoices and Due Invoices -->
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#paid-invoices">Paid Invoices</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#due-invoices">Due Invoices</a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Tab for Paid Invoices -->
                <div class="tab-pane fade show active" id="paid-invoices">
                    <div class="card">
                        <!-- Paid Invoices Table for Super Admin -->
                        @if(Auth::guard('admin')->user()->type == 'super')
                            <table id="PaidInvoicesTable" class="table table-striped table-hover theme-color xjs-exportable"
                                   xdata-sorting="false" xdata-page-size="5" xdata-paging="true" xdata-filtering="true">
                                    <thead>
                                    <tr>
                                        <th>ID #</th>
                                        <th>Invoice #</th>
                                        <th data-breakpoints="sm xs">Date</th>
                                        <th>Invoice Status</th>
                                        <th>Transaction Id</th>
                                        <th>Amount</th>
                                        <th class="text-center" data-breakpoints="xs md">Status</th>
                                        <th class="text-center" data-breakpoints="sm xs md">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($splitPaymentsPaidInvoices as $key => $splitPayment)
                                        <tr>
                                            <td class="align-middle">{{$splitPayment->id}}</td>
                                            <td class="align-middle">
                                                <a class="text-warning" href="#" data-toggle="modal"
                                                   data-target="#logModal{{ isset($splitPayment->getInvoice) && $splitPayment->getInvoice->count() > 0 ? $splitPayment->getInvoice->invoice_key : ""}}">{{ isset($splitPayment->getInvoice) && $splitPayment->getInvoice->count() > 0? $splitPayment->getInvoice->invoice_num : ""}}</a>
                                                <div class="mt-n2"><span
                                                        class="badge badge-info rounded-pill">{{isset($splitPayment->getInvoice) && $splitPayment->getInvoice->count() > 0 ?  $splitPayment->getInvoice->invoice_key : ""}}</span>
                                                </div>
                                                <div class="modal fade"
                                                     id="logModal{{isset($splitPayment->getInvoice) && $splitPayment->getInvoice->count() > 0 ?  $splitPayment->getInvoice->invoice_key : ""}}"
                                                     tabindex="-1"
                                                     role="dialog">
                                                    <div class="modal-dialog modal-lg modal-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="title" id="defaultModalLabel">Log</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                @php $logs = \App\Models\PaymentTransactionsLog::where('invoiceid' ,$splitPayment->invoice_id)->get(); @endphp
                                                                @if(count($logs))
                                                                    <div class="table-responsive">
                                                                        <table id="InvoiceTable"
                                                                               class="table table-striped table-hover table-sm theme-color js-exportable"
                                                                               xdata-sorting="false">
                                                                            <thead>
                                                                            <tr>
                                                                                <th>ID #</th>
                                                                                <th>Invoice #</th>
                                                                                <th>Amount</th>
                                                                                <th>Response Reason</th>
                                                                                <th data-breakpoints="sm xs">Date</th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                            @foreach ($logs as $log)
                                                                                <tr>
                                                                                    <td class="align-middle">{{$log->id}}</td>
                                                                                    <td class="align-middle">{{$log->invoiceid}}</td>
                                                                                    <td class="align-middle">
                                                                                        ${{$log->amount}}</td>
                                                                                    <td class="align-middle">{{$log->response_reason}}</td>
                                                                                    <td class="align-middle">{{$log->created_at->format('j F, Y')}}
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
                                            <td class="align-middle">{{$splitPayment->created_at->format('j F, Y')}}
                                                <br>{{$splitPayment->created_at->format('h:i:s A')}}
                                                <br>{{$splitPayment->created_at->diffForHumans()}}</td>
                                            <td class="align-middle">
                                                @if(isset($splitPayment->getInvoiceStatus))
                                                    @if($splitPayment->getInvoiceStatus->status == 'draft')
                                                        <span class="badge bg-grey rounded-pill">Draft</span>
                                                    @elseif($splitPayment->getInvoiceStatus->status == 'due')
                                                        <span class="badge bg-amber rounded-pill">Due</span>
                                                    @elseif($splitPayment->getInvoiceStatus->status == 'refund')
                                                        <span class="badge bg-pink rounded-pill">Refund</span>
                                                    @elseif($splitPayment->getInvoiceStatus->status == 'chargeback')
                                                        <span class="badge bg-red rounded-pill">Charge Back</span>
                                                    @else
                                                        <span class="badge badge-success rounded-pill">Paid</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="align-middle">{{$splitPayment->transaction_id ? $splitPayment->transaction_id : "---"}}</td>
                                            <td class="align-middle">{{isset($splitPayment->getInvoiceCurrencySymbol) ? $splitPayment->getInvoiceCurrencySymbol->cur_symbol : "$"}} {{$splitPayment->amount}}</td>
                                            {{--                                    <td class="align-middle">{{ \Carbon\Carbon::parse($splitPayment->created_at)->format('d/m/Y')}}</td>--}}
                                            <td class="text-center align-middle">
                                                @if($splitPayment->status == 1)
                                                    <span class="badge badge-success rounded-pill">Paid</span>
                                                @else
                                                    <span class="badge badge-danger rounded-pill">Pending</span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">
                                                @if($splitPayment->status != 1)
                                                    <button data-id="{{$splitPayment->id}}" title="Pay Now" type="button"
                                                            class="btn btn-info btn-sm btn-round split-payment-pay-now"><i
                                                            class="zmdi zmdi-money"></i> Pay Now
                                                    </button>
                                                @else
                                                    <button title="Paid" type="button"
                                                            class="btn btn-success btn-sm btn-round"><i
                                                            class="zmdi zmdi-money"></i> Already Paid
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                        @endif

                        <!-- Paid Invoices Table for Regular Admin -->
                        @if(Auth::guard('admin')->user()->type == 'admin')
                            <table id="PaidInvoicesTable" class="table table-striped table-hover theme-color xjs-exportable"
                                   xdata-sorting="false" xdata-page-size="5" xdata-paging="true" xdata-filtering="true">
                                <thead>
                                <tr>
                                    <th>ID #</th>
                                    <th>Invoice #</th>
                                    <th data-breakpoints="sm xs">Date</th>
                                    <th>Invoice Status</th>
                                    <th>Transaction Id</th>
                                    <th>Amount</th>
                                    <th class="text-center" data-breakpoints="xs md">Status</th>
                                    <th class="text-center" data-breakpoints="sm xs md">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($splitPaymentsPaidInvoices as $key => $splitPayment)
                                    <tr>
                                        <td class="align-middle">{{$splitPayment->id}}</td>

                                        <td class="align-middle">
                                            <a class="text-warning" href="#" data-toggle="modal"
                                               data-target="#logModal{{ isset($splitPayment->getInvoice)  && $splitPayment->getInvoice->count() > 0 ? $splitPayment->getInvoice->invoice_key : ""}}">{{ isset($splitPayment->getInvoice) && $splitPayment->getInvoice->count() > 0? $splitPayment->getInvoice->invoice_num : ""}}</a>
                                            <div class="mt-n2"><span
                                                    class="badge badge-info rounded-pill">{{ isset($splitPayment->getInvoice)  && $splitPayment->getInvoice->count() > 0 ? $splitPayment->getInvoice->invoice_key : ""}}</span>
                                            </div>
                                            <div class="modal fade"
                                                 id="logModal{{ isset($splitPayment->getInvoice)  && $splitPayment->getInvoice->count() > 0 ? $splitPayment->getInvoice->invoice_key : ""}}"
                                                 tabindex="-1"
                                                 role="dialog">
                                                <div class="modal-dialog modal-lg modal-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="title" id="defaultModalLabel">Log</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            @php $logs = \App\Models\PaymentTransactionsLog::where('invoiceid' ,$splitPayment->invoice_id)->get(); @endphp
                                                            @if(count($logs))
                                                                <div class="table-responsive">
                                                                    <table id="InvoiceTable"
                                                                           class="table table-striped table-hover table-sm theme-color js-exportable"
                                                                           xdata-sorting="false">
                                                                        <thead>
                                                                        <tr>
                                                                            <th>ID #</th>
                                                                            <th>Invoice #</th>
                                                                            <th>Date</th>
                                                                            <th>Amount</th>
                                                                            <th>Response Reason</th>
                                                                            <th data-breakpoints="sm xs">Date</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        @foreach ($logs as $log)
                                                                            <tr>
                                                                                <td class="align-middle">{{$log->id}}</td>
                                                                                <td class="align-middle">{{$log->invoiceid}}</td>
                                                                                <td class="align-middle">
                                                                                    ${{$log->amount}}</td>
                                                                                <td class="align-middle">{{$log->response_reason}}</td>
                                                                                <td class="align-middle">{{$log->created_at->format('j F, Y')}}
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
                                        <td class="align-middle">{{$splitPayment->created_at->format('j F, Y')}}
                                            <br>{{$splitPayment->created_at->format('h:i:s A')}}
                                            <br>{{$splitPayment->created_at->diffForHumans()}}</td>
                                        <td class="align-middle">
                                            @if(isset($splitPayment->getInvoiceStatus))
                                                @if($splitPayment->getInvoiceStatus->status == 'draft')
                                                    <span class="badge bg-grey rounded-pill">Draft</span>
                                                @elseif($splitPayment->getInvoiceStatus->status == 'due')
                                                    <span class="badge bg-amber rounded-pill">Due</span>
                                                @elseif($splitPayment->getInvoiceStatus->status == 'refund')
                                                    <span class="badge bg-pink rounded-pill">Refund</span>
                                                @elseif($splitPayment->getInvoiceStatus->status == 'chargeback')
                                                    <span class="badge bg-red rounded-pill">Charge Back</span>
                                                @else
                                                    <span class="badge badge-success rounded-pill">Paid</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="align-middle">{{$splitPayment->transaction_id != null ? $splitPayment->transaction_id : "---"}}</td>
                                        <td class="align-middle">{{isset($splitPayment->getInvoiceCurrencySymbol) ? $splitPayment->getInvoiceCurrencySymbol->cur_symbol : "$"}} {{$splitPayment->amount}}</td>
                                        {{--                                    <td class="align-middle">{{ \Carbon\Carbon::parse($splitPayment->created_at)->format('d/m/Y')}}</td>--}}
                                        <td class="text-center align-middle">
                                            @if($splitPayment->status == 1)
                                                <span class="badge badge-success rounded-pill">Paid</span>
                                            @else
                                                <span class="badge badge-danger rounded-pill">Pending</span>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            @if($splitPayment->status != 1)
                                                <button data-id="{{$splitPayment->id}}" title="Pay Now" type="button"
                                                        class="btn btn-info btn-sm btn-round split-payment-pay-now"><i
                                                        class="zmdi zmdi-money"></i> Pay Now
                                                </button>
                                            @else
                                                <button title="Paid" type="button"
                                                        class="btn btn-success btn-sm btn-round"><i
                                                        class="zmdi zmdi-money"></i> Already Paid
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>

                <!-- Tab for Due Invoices -->
                <div class="tab-pane fade" id="due-invoices">
                    <div class="card">
                        <!-- Due Invoices Table for Super Admin -->
                        @if(Auth::guard('admin')->user()->type == 'super')
                            <table id="DueInvoicesTable" class="table table-striped table-hover theme-color xjs-exportable"
                                   xdata-sorting="false" xdata-page-size="5" xdata-paging="true" xdata-filtering="true">
                                <thead>
                                <tr>
                                    <th>ID #</th>
                                    <th>Invoice #</th>
                                    <th data-breakpoints="sm xs">Date</th>
                                    <th>Invoice Status</th>
                                    <th>Transaction Id</th>
                                    <th>Amount</th>
                                    <th class="text-center" data-breakpoints="xs md">Status</th>
                                    <th class="text-center" data-breakpoints="sm xs md">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($splitPaymentsDueInvoices as $key => $splitPayment)
                                    <tr>
                                        <td class="align-middle">{{$splitPayment->id}}</td>
                                        <td class="align-middle">
                                            <a class="text-warning" href="#" data-toggle="modal"
                                               data-target="#logModal{{ isset($splitPayment->getInvoice) && $splitPayment->getInvoice->count() > 0 ? $splitPayment->getInvoice->invoice_key : ""}}">{{ isset($splitPayment->getInvoice) && $splitPayment->getInvoice->count() > 0? $splitPayment->getInvoice->invoice_num : ""}}</a>
                                            <div class="mt-n2"><span
                                                    class="badge badge-info rounded-pill">{{isset($splitPayment->getInvoice) && $splitPayment->getInvoice->count() > 0 ?  $splitPayment->getInvoice->invoice_key : ""}}</span>
                                            </div>
                                            <div class="modal fade"
                                                 id="logModal{{isset($splitPayment->getInvoice) && $splitPayment->getInvoice->count() > 0 ?  $splitPayment->getInvoice->invoice_key : ""}}"
                                                 tabindex="-1"
                                                 role="dialog">
                                                <div class="modal-dialog modal-lg modal-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="title" id="defaultModalLabel">Log</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            @php $logs = \App\Models\PaymentTransactionsLog::where('invoiceid' ,$splitPayment->invoice_id)->get(); @endphp
                                                            @if(count($logs))
                                                                <div class="table-responsive">
                                                                    <table id="InvoiceTable"
                                                                           class="table table-striped table-hover table-sm theme-color js-exportable"
                                                                           xdata-sorting="false">
                                                                        <thead>
                                                                        <tr>
                                                                            <th>ID #</th>
                                                                            <th>Invoice #</th>
                                                                            <th>Amount</th>
                                                                            <th>Response Reason</th>
                                                                            <th data-breakpoints="sm xs">Date</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        @foreach ($logs as $log)
                                                                            <tr>
                                                                                <td class="align-middle">{{$log->id}}</td>
                                                                                <td class="align-middle">{{$log->invoiceid}}</td>
                                                                                <td class="align-middle">
                                                                                    ${{$log->amount}}</td>
                                                                                <td class="align-middle">{{$log->response_reason}}</td>
                                                                                <td class="align-middle">{{$log->created_at->format('j F, Y')}}
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
                                        <td class="align-middle">{{$splitPayment->created_at->format('j F, Y')}}
                                            <br>{{$splitPayment->created_at->format('h:i:s A')}}
                                            <br>{{$splitPayment->created_at->diffForHumans()}}</td>
                                        <td class="align-middle">
                                            @if(isset($splitPayment->getInvoiceStatus))
                                                @if($splitPayment->getInvoiceStatus->status == 'draft')
                                                    <span class="badge bg-grey rounded-pill">Draft</span>
                                                @elseif($splitPayment->getInvoiceStatus->status == 'due')
                                                    <span class="badge bg-amber rounded-pill">Due</span>
                                                @elseif($splitPayment->getInvoiceStatus->status == 'refund')
                                                    <span class="badge bg-pink rounded-pill">Refund</span>
                                                @elseif($splitPayment->getInvoiceStatus->status == 'chargeback')
                                                    <span class="badge bg-red rounded-pill">Charge Back</span>
                                                @else
                                                    <span class="badge badge-success rounded-pill">Paid</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="align-middle">{{$splitPayment->transaction_id ? $splitPayment->transaction_id : "---"}}</td>
                                        <td class="align-middle">{{isset($splitPayment->getInvoiceCurrencySymbol) ? $splitPayment->getInvoiceCurrencySymbol->cur_symbol : "$"}} {{$splitPayment->amount}}</td>
                                        {{--                                    <td class="align-middle">{{ \Carbon\Carbon::parse($splitPayment->created_at)->format('d/m/Y')}}</td>--}}
                                        <td class="text-center align-middle">
                                            @if($splitPayment->status == 1)
                                                <span class="badge badge-success rounded-pill">Paid</span>
                                            @else
                                                <span class="badge badge-danger rounded-pill">Pending</span>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            @if($splitPayment->status != 1)
                                                <button data-id="{{$splitPayment->id}}" title="Pay Now" type="button"
                                                        class="btn btn-info btn-sm btn-round split-payment-pay-now"><i
                                                        class="zmdi zmdi-money"></i> Pay Now
                                                </button>
                                            @else
                                                <button title="Paid" type="button"
                                                        class="btn btn-success btn-sm btn-round"><i
                                                        class="zmdi zmdi-money"></i> Already Paid
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif

                        <!-- Due Invoices Table for Regular Admin -->
                        @if(Auth::guard('admin')->user()->type == 'admin')
                            <table id="DueInvoicesTable" class="table table-striped table-hover theme-color xjs-exportable"
                                   xdata-sorting="false" xdata-page-size="5" xdata-paging="true" xdata-filtering="true">
                                <thead>
                                <tr>
                                    <th>ID #</th>
                                    <th>Invoice #</th>
                                    <th data-breakpoints="sm xs">Date</th>
                                    <th>Invoice Status</th>
                                    <th>Transaction Id</th>
                                    <th>Amount</th>
                                    <th class="text-center" data-breakpoints="xs md">Status</th>
                                    <th class="text-center" data-breakpoints="sm xs md">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($splitPaymentsDueInvoices as $key => $splitPayment)
                                    <tr>
                                        <td class="align-middle">{{$splitPayment->id}}</td>

                                        <td class="align-middle">
                                            <a class="text-warning" href="#" data-toggle="modal"
                                               data-target="#logModal{{ isset($splitPayment->getInvoice)  && $splitPayment->getInvoice->count() > 0 ? $splitPayment->getInvoice->invoice_key : ""}}">{{ isset($splitPayment->getInvoice) && $splitPayment->getInvoice->count() > 0? $splitPayment->getInvoice->invoice_num : ""}}</a>
                                            <div class="mt-n2"><span
                                                    class="badge badge-info rounded-pill">{{ isset($splitPayment->getInvoice)  && $splitPayment->getInvoice->count() > 0 ? $splitPayment->getInvoice->invoice_key : ""}}</span>
                                            </div>
                                            <div class="modal fade"
                                                 id="logModal{{ isset($splitPayment->getInvoice)  && $splitPayment->getInvoice->count() > 0 ? $splitPayment->getInvoice->invoice_key : ""}}"
                                                 tabindex="-1"
                                                 role="dialog">
                                                <div class="modal-dialog modal-lg modal-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="title" id="defaultModalLabel">Log</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            @php $logs = \App\Models\PaymentTransactionsLog::where('invoiceid' ,$splitPayment->invoice_id)->get(); @endphp
                                                            @if(count($logs))
                                                                <div class="table-responsive">
                                                                    <table id="InvoiceTable"
                                                                           class="table table-striped table-hover table-sm theme-color js-exportable"
                                                                           xdata-sorting="false">
                                                                        <thead>
                                                                        <tr>
                                                                            <th>ID #</th>
                                                                            <th>Invoice #</th>
                                                                            <th>Date</th>
                                                                            <th>Amount</th>
                                                                            <th>Response Reason</th>
                                                                            <th data-breakpoints="sm xs">Date</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        @foreach ($logs as $log)
                                                                            <tr>
                                                                                <td class="align-middle">{{$log->id}}</td>
                                                                                <td class="align-middle">{{$log->invoiceid}}</td>
                                                                                <td class="align-middle">
                                                                                    ${{$log->amount}}</td>
                                                                                <td class="align-middle">{{$log->response_reason}}</td>
                                                                                <td class="align-middle">{{$log->created_at->format('j F, Y')}}
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
                                        <td class="align-middle">{{$splitPayment->created_at->format('j F, Y')}}
                                            <br>{{$splitPayment->created_at->format('h:i:s A')}}
                                            <br>{{$splitPayment->created_at->diffForHumans()}}</td>
                                        <td class="align-middle">
                                            @if(isset($splitPayment->getInvoiceStatus))
                                                @if($splitPayment->getInvoiceStatus->status == 'draft')
                                                    <span class="badge bg-grey rounded-pill">Draft</span>
                                                @elseif($splitPayment->getInvoiceStatus->status == 'due')
                                                    <span class="badge bg-amber rounded-pill">Due</span>
                                                @elseif($splitPayment->getInvoiceStatus->status == 'refund')
                                                    <span class="badge bg-pink rounded-pill">Refund</span>
                                                @elseif($splitPayment->getInvoiceStatus->status == 'chargeback')
                                                    <span class="badge bg-red rounded-pill">Charge Back</span>
                                                @else
                                                    <span class="badge badge-success rounded-pill">Paid</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="align-middle">{{$splitPayment->transaction_id != null ? $splitPayment->transaction_id : "---"}}</td>
                                        <td class="align-middle">{{isset($splitPayment->getInvoiceCurrencySymbol) ? $splitPayment->getInvoiceCurrencySymbol->cur_symbol : "$"}} {{$splitPayment->amount}}</td>
                                        {{--                                    <td class="align-middle">{{ \Carbon\Carbon::parse($splitPayment->created_at)->format('d/m/Y')}}</td>--}}
                                        <td class="text-center align-middle">
                                            @if($splitPayment->status == 1)
                                                <span class="badge badge-success rounded-pill">Paid</span>
                                            @else
                                                <span class="badge badge-danger rounded-pill">Pending</span>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            @if($splitPayment->status != 1)
                                                <button data-id="{{$splitPayment->id}}" title="Pay Now" type="button"
                                                        class="btn btn-info btn-sm btn-round split-payment-pay-now"><i
                                                        class="zmdi zmdi-money"></i> Pay Now
                                                </button>
                                            @else
                                                <button title="Paid" type="button"
                                                        class="btn btn-success btn-sm btn-round"><i
                                                        class="zmdi zmdi-money"></i> Already Paid
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('cxmScripts')
    <script>
        $(document).ready(function () {
            $('#PaidInvoicesTable').DataTable({
                dom: 'lBfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[6, 'desc']]
            });

            $('#DueInvoicesTable').DataTable({
                dom: 'lBfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[6, 'desc']]
            });
            $('#invoiceTabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var targetTabId = $(e.target).attr("href");
                if (targetTabId === "#paid-invoices") {
                    $('#DueInvoicesTable').DataTable().destroy();
                    $('#PaidInvoicesTable').DataTable({
                        dom: 'lBfrtip',
                        buttons: [
                            'copy', 'csv', 'excel', 'pdf', 'print'
                        ],
                        order: [[6, 'desc']]
                    });
                } else if (targetTabId === "#due-invoices") {
                    $('#PaidInvoicesTable').DataTable().destroy();
                    $('#DueInvoicesTable').DataTable({
                        dom: 'lBfrtip',
                        buttons: [
                            'copy', 'csv', 'excel', 'pdf', 'print'
                        ],
                        order: [[6, 'desc']]
                    });
                }
            });
        });

        $(function () {
            $('#SplitPaymentTable').on('click', '.split-payment-pay-now', function () {
                swal({
                    title: "Are you want to Pay now?",
                    text: "Press Yes, if you want to pay now this payment!",
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
                            text: "Yes, Pay Now!"
                        }
                    },
                    dangerMode: true,
                })
                    .then((payNow) => {
                        if (payNow) {
                            $('.page-loader-wrapper').show();
                            var split_payment_id = $(this).data('id');

                            $.ajax({
                                type: "GET",
                                url: "{{route('admin_pay_now_split_payments')}}/" + split_payment_id,
                                success: function (data) {
                                    $('.page-loader-wrapper').hide();
                                    swal("Good job!", "Split payment completed successfully!", "success");
                                    setTimeout(function () {
                                        window.location = '{{url("admin/split-payments")}}';
                                    }, 2000);
                                },
                                error: function (data) {
                                    $('.page-loader-wrapper').hide();
                                    console.log('Error:', data);
                                    var errorMessage = data.responseJSON.error || data.responseJSON.message || 'An unknown error occurred.';
                                    swal('Error', errorMessage, 'error');
                                    setTimeout(function () {
                                        window.location = '{{url("admin/split-payments")}}';
                                    }, 2000);
                                }
                            });
                        } else {
                            swal('Thank You', 'Thank you for your patience!', 'success', {buttons: false, timer: 2000});
                        }
                    });
            });
        });

    </script>
@endpush
