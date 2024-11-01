@extends('admin.layouts.app')

@section('cxmTitle', 'Payments Refund')

@section('content')

<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Payments Refunds</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">Refunds</li>
                        <li class="breadcrumb-item active"> Payments</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    @include('includes.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>
        <div class="card">
            <div class="table-responsive">
                <table id="LeadTable" class="table table-striped table-hover js-basic-example theme-color table-sm cxm-font-sm">
                    <thead>
                        <tr>
                            <th>ID #</th>
                            <th data-orderable="false">Date</th>
                            <th data-orderable="false">Brand</th>
                            <th data-orderable="false">Agent</th>
                            <th data-orderable="false" data-breakpoints="sm xs">Invoice Id</th>
                            <th data-orderable="false">Payment Id</th>
                            <th data-orderable="false">Transaction ID</th>
                            <th data-orderable="false">Reason</th>
                            <th data-orderable="false">Type</th>
                            <th data-orderable="false">Amount</th>
                            <th data-orderable="false" class="text-center" data-breakpoints="xs md">Status</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($refunds as $refund)
                        <tr>
                            <td>{{$refund->id}}</td>
                            <td>{{ \Carbon\Carbon::parse($refund->created_at)->format('d/m/Y')}}</td>
                            <td>
                                @php $brandName = \App\Models\Brand::where('brand_key',$refund->brand_key)->value('name'); @endphp
                                {{$brandName}}
                            </td>
                            <td> @php $agentName = \App\Models\User::where('id',$refund->agent_id)->value('name'); @endphp
                                {{$agentName}}
                            </td>

                            <td>{{$refund->invoice_id}}</td>
                            <td>{{$refund->payment_id}}</td>
                            <td>{{$refund->authorizenet_transaction_id}}</td>
                            <td>{{$refund->reason}}</td>
                            <td>
                                @if($refund->type == 'refund')
                                <span class="badge badge-warning rounded-pill">Refund</span>
                                @else
                                <span class="badge badge-danger rounded-pill">Charge Back</span>
                                @endif
                            </td>
                            <td>${{$refund->amount}}</td>
                            <td class="text-center">
                                @if($refund->qa_approval == 1)
                                <span class="badge badge-success">Approved</span>
                                @else
                                <span class="badge badge-danger rounded-pill">Not Approved</span>
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
@endsection

@push('cxmScripts')
<script>
    $(function(){
        $('#LeadTable').on('click', '.cxm-approved-refund', function(){
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
