@extends('admin.layouts.app')@section('cxmTitle', 'Payment Multiple Response')

@section('content')
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Payment Multiple Responses</h2>
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
                        <div class="">
                            <div class="body">
                                <form id="searchForm">
                                    @csrf
                                    <div class="row clearfix">
                                        <div class="col-lg-4 col-md-6"></div>
                                        <div class="col-lg-4 col-md-6"></div>
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

                @php
                    function displayJsonData($data) {
                        echo '<ul>';
                        foreach ($data as $key => $value) {
                            echo '<li>';
                            echo '<strong>' . htmlspecialchars($key) . '</strong>: ';
                            if (is_array($value) || is_object($value)) {
                                displayJsonData((array)$value);
                            } else {
                                echo is_string($value) ? htmlspecialchars($value) : json_encode($value);
                                if (strpos($key, 'ResultCode') !== false) {
                                    echo  getResultCodeDescription($key, $value);
                                }
                            }
                            echo '</li>';
                        }
                        echo '</ul>';
                    }
                    function getResultCodeDescription($key, $code) {
                        switch ($key) {
                            case 'avsResultCode':
                                return getAvsStatusCodeDescription($code);
                            case 'cvvResultCode':
                                return getCvvStatusCodeDescription($code);
                            case 'cavvResultCode':
                                return getCavvStatusCodeDescription($code);
                            default:
                                return '';
                        }
                    }
                    function getAvsStatusCodeDescription($code) {
                        switch ($code) {
                            case 'A':
                                return ' ( Address (Street) matches, ZIP does not ) ';
                            case 'B':
                                return ' ( Address information not provided for AVS check ) ';
                            case 'E':
                                return ' ( AVS error ) ';
                            case 'G':
                                return ' ( Non-U.S. Card Issuing Bank ) ';
                            case 'N':
                                return ' ( No Match on Address (Street) or ZIP ) ';
                            case 'P':
                                return ' ( AVS not applicable for this transaction ) ';
                            case 'R':
                                return ' ( Retry – System unavailable or timed out ) ';
                            case 'S':
                                return ' ( Service not supported by issuer ! This means the card issuing bank does not support AVS. ) ';
                            case 'U':
                                return ' ( Address information is unavailable ) ';
                            case 'W':
                                return ' ( Nine digit ZIP matches, Address (Street) does not ) ';
                            case 'X':
                                return ' ( Address (Street) and nine digit ZIP match ) ';
                            case 'Y':
                                return ' ( Address (Street) and five digit ZIP match ) ';
                            case 'Z':
                                return ' ( Five digit ZIP matches, Address (Street) does not ) ';
                            default:
                                return '';
                        }
                    }

                    function getCvvStatusCodeDescription($code) {
                        switch ($code) {
                            case 'M':
                                return ' ( Match ! The CVV2 code entered matched that of the credit card.) ';
                            case 'N':
                                return ' ( Does Not Match ! The code entered is incorrect. ) ';
                            case 'P':
                                return ' ( Is Not Processed ! The code was not validated. ) ';
                            case 'S':
                                return ' ( Should be on card but not so indicated ! The customer left that cvv field blank. ) ';
                            case 'U':
                                return ' ( Issuer not certified or has not provided encryption key ! The card issuing bank does not participate in the CVV2 program or hasn\'t provided the key so that the code can be validated. ) ';
                            default:
                                return '';
                        }
                    }
                    function getCavvStatusCodeDescription($code) {
                        switch ($code) {
                            case '0':
                                return ' ( Cavv not validated because erroneous data was submitted ) ';
                            case '1':
                                return ' ( Cavv failed validation ) ';
                            case '2':
                                return ' ( Cavv passed validation ) ';
                            case '3':
                                return ' ( Cavv validation could not be performed; issuer attempt incomplete ) ';
                            case '4':
                                return ' ( Cavv validation could not be performed; issuer system error ) ';
                            case '5': case '6':
                                return ' ( Reserved for future use ) ';
                            case '7':
                                return ' ( Cavv attempt – failed – issuer unavailable ) ';
                            case '8':
                                return ' ( Cavv attempt – passed ) ';
                            case '9':
                                return ' ( Cavv validation could not be performed; issuer rejected authentication ) ';
                            default:
                                return '';
                        }
                    }

                @endphp
                <div class="row clearfix">
                    <div class="col-lg-12">
                        <div class="">
                            <div class="table-responsive">
                                <table id="MultiPaymentResponseTable" class="table table-striped table-hover theme-color xjs-exportable" data-sorting="false">
                                    <thead>
                                    <tr>
                                        <th>ID#</th>
                                        <th>Invoice#</th>
                                        <th>Agent Team</th>
                                        <th>Payment Gateway</th>
                                        <th data-breakpoints="sm xs">Date</th>
                                        <th>Response</th>
                                        <th>Payment Process From</th>
                                        <th>Form Inputs</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($multi_payment_responses as $multi_payment_response)
                                        <tr>
                                            <td class="align-middle">{{$multi_payment_response->id}}</td>
                                            <td class="align-middle">
                                                @if(isset($multi_payment_response->getInvoice))
                                                    <a class="text-warning" href="#{{$multi_payment_response->getInvoice->invoice_num}}" style="cursor: default">{{$multi_payment_response->getInvoice->invoice_num}}</a>
                                                @endif
                                                <br>
                                                <div class="mt-n2">
                                                    <span class="badge badge-info rounded-pill">{{ $multi_payment_response->invoice_id}}</span>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <a href="{{isset($multi_payment_response->getInvoice->getAgent->getTeam->id) ? route('team.edit',[$multi_payment_response->getInvoice->getAgent->getTeam->id],'/edit') : "#"}}">{{optional($multi_payment_response->getInvoice->getAgent->getTeam)->name}}</a><br>{{optional($multi_payment_response->getInvoice->getAgent)->team_key}}
                                            </td>
                                            <td class="align-middle">{{$multi_payment_response->payment_gateway}}</td>
                                            <td class="align-middle text-nowrap">{{$multi_payment_response->created_at->format('j F, Y')}}
                                                <br>{{$multi_payment_response->created_at->format('h:i:s A')}}
                                                <br>{{$multi_payment_response->created_at->diffForHumans()}}
                                            </td>
                                            <td class="align-middle">
                                                <button class="toggle-response btn btn-link" type="button" data-toggle="modal" data-target="#responseModal{{$multi_payment_response->id}}">View Response</button>
                                                <div class="modal fade" id="responseModal{{$multi_payment_response->id}}" tabindex="-1" role="dialog" aria-labelledby="responseModalLabel{{$multi_payment_response->id}}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content" style="width: 650px;">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="responseModalLabel{{$multi_payment_response->id}}">Response Details : {{$multi_payment_response->invoice_id ?? "########"}}</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body" style="overflow: auto;">
                                                                @php
                                                                    $responseData = json_decode($multi_payment_response->response, true);
                                                                @endphp
                                                                @if(is_array($responseData))
                                                                    @php displayJsonData($responseData); @endphp
                                                                @else
                                                                    {{ htmlspecialchars($multi_payment_response->response) }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>


                                            <td class="align-middle">
                                                <button class="toggle-payment-process btn btn-link" type="button" data-toggle="modal" data-target="#paymentProcessFromModal{{$multi_payment_response->id}}">View Payment Process</button>
                                                <div class="modal fade" id="paymentProcessFromModal{{$multi_payment_response->id}}" tabindex="-1" role="dialog" aria-labelledby="paymentProcessFromModalLabel{{$multi_payment_response->id}}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content" style="width: 650px;">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="paymentProcessFromModalLabel{{$multi_payment_response->id}}">Payment Process Details : {{$multi_payment_response->invoice_id ?? "########"}}</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body" style="overflow: auto;">
                                                                @php
                                                                    $responseData = json_decode($multi_payment_response->payment_process_from, true);
                                                                @endphp
                                                                @if(is_array($responseData))
                                                                    @php displayJsonData($responseData); @endphp
                                                                @else
                                                                    {{ htmlspecialchars($multi_payment_response->payment_process_from) }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="align-middle">
                                                <button class="toggle-form-inputs btn btn-link" type="button" data-toggle="modal" data-target="#formInputsModal{{$multi_payment_response->id}}">View Form Inputs</button>
                                                <div class="modal fade" id="formInputsModal{{$multi_payment_response->id}}" tabindex="-1" role="dialog" aria-labelledby="formInputsModalLabel{{$multi_payment_response->id}}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content" style="width: 650px;">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="formInputsModalLabel{{$multi_payment_response->id}}">Form Inputs Details : {{$multi_payment_response->invoice_id ?? "########"}}</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body" style="overflow: auto;">
                                                                @php
                                                                    $responseData = json_decode($multi_payment_response->form_inputs, true);
                                                                @endphp
                                                                @if(is_array($responseData))
                                                                    @php displayJsonData($responseData); @endphp
                                                                @else
                                                                    {{ htmlspecialchars($multi_payment_response->form_inputs) }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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
            window.location.href = "{{ route('admin.payment.multiple.response.index') }}?dateRange=" + encodeURIComponent($('#date-range').val());
        }

        $(document).ready(function () {
            $('#MultiPaymentResponseTable').DataTable().destroy();

            $('#MultiPaymentResponseTable').DataTable({
                dom: 'lBfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[0, 'desc']],
                scrollX: false,
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

            $('.toggle-response').click(function () {
                $(this).next('.response-table').toggle();
            });
            $('.toggle-payment-process').click(function () {
                $(this).next('.payment-process-table').toggle();
            });
            $('.toggle-form-inputs').click(function () {
                $(this).next('.form-inputs-table').toggle();
            });
        });
    </script>

@endpush
