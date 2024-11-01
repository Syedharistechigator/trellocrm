@extends('layouts.app')

@section('cxmTitle', 'Payments Refund')

@section('content')

    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Payments Refunds</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="zmdi zmdi-home"></i>
                                    {{ config('app.home_name') }}</a></li>
                            <li class="breadcrumb-item">Sales</li>
                            <li class="breadcrumb-item active">Refunds</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button"><i
                                class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        @include('includes.cxm-top-right-toggle-btn')
                    </div>
                </div>
            </div>
            <div class="">
                <div class="table-responsive">
                    <table id="LeadTable"
                           class="table table-striped table-hover js-basic-example theme-color">
                        <thead>
                        <tr>
                            <th>ID #</th>
                            <th class='text-nowrap' data-breakpoints="sm xs">Invoice#</th>
                            <th class='text-nowrap'>Date</th>
                            <th class='text-nowrap'>Client</th>
                            <th class='text-nowrap'>payment Id</th>
                            <th class='text-nowrap'>Transaction ID</th>
                            <th class='text-nowrap'>Type</th>
                            <th class='text-nowrap'>Amount</th>
                            <th class="text-nowrap text-center" data-breakpoints="xs md">Status</th>
                            {{--<th>Action</th>--}}
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($refunds as $refund)
                            <tr>
                                <td class='align-middle'>{{$refund->id}}</td>
                                <td class="align-middle text-nowrap">
                                    <a class="text-warning"
                                       href="javascript:void(0);">{{isset($refund->getInvoice)?$refund->getInvoice->invoice_num : ""}}</a>
                                    <div class="">
                                                    <span
                                                        class="badge badge-info rounded-pill">{{ $refund->invoice_id}}</span>
                                    </div>
                                </td>
                                <td class="align-middle text-nowrap">{{$refund->created_at->format('j F, Y')}}
                                    <br>{{$refund->created_at->format('h:i:s A')}}
                                    <br>{{$refund->created_at->diffForHumans()}}
                                </td>
                                <td class="text-info align-middle text-nowrap">
                                    <a class="text-info text-nowrap"
                                       href="{{route('client.show',$refund->client_id)}}">
                                        {{$refund->getClientName->name}}<br>{{$refund->getClientEmail->email}}</a>
                                </td>
                                <td class='align-middle text-nowrap'><a title="View Payment Details" class="text-info"
                                                                        href="{{route('showPaymentDetail',$refund->payment_id)}}">{{$refund->payment_id}}</a>
                                </td>
                                <td class='align-middle text-nowrap'>{{$refund->authorizenet_transaction_id}}</td>
                                <td class="align-middle">
                                    @if($refund->type == 'refund')
                                        <span class="badge badge-warning rounded-pill">Refund</span>
                                    @else
                                        <span class="badge badge-danger rounded-pill">Charge Back</span>
                                    @endif
                                </td>
                                <td class='align-middle text-nowrap'>${{$refund->amount}}</td>
                                <td class="align-middle text-center">
                                    @if($refund->qa_approval == 1)
                                        <span class="badge badge-success">Approved</span>
                                    @else
                                        <span class="badge badge-danger rounded-pill">Not Approved</span>
                                    @endif
                                </td>
                                {{--<td>
                                 @if(Auth::user()->type == 'qa')
                                     <button data-id="{{$refund->id}}" title="QA Approved" type="button" class="btn btn-warning btn-sm btn-round cxm-approved-refund">
                                         <i class="zmdi zmdi-hc-fw"></i>
                                     </button>
                                 @endif
                                 </td>--}}
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('cxmScripts')
    <script>
        $(function () {
            $('#LeadTable').on('click', '.cxm-approved-refund', function () {
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
                            var id = $(this).data('id');
                            console.log(id);
                            $.ajax({
                                type: "GET",
                                dataType: "json",
                                url: "{{ route('refundStatusApproved') }}",
                                data: {'id': id},

                                success: function (data) {
                                    console.log(data);
                                    swal("Good job!", "Status change successfully!", "success");
                                    setInterval('location.reload()', 2000);        // Using .reload() method.
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
    </script>
@endpush
