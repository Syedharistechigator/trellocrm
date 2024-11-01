@extends('admin.layouts.app')

@section('cxmTitle', 'Trashed')

@section('content')
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Trashed Customer Sheet List</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item"> Customer Sheets</li>
                        <li class="breadcrumb-item active">Trashed</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    @include('includes.admin.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="card">
                        <div id="restoreAll" class="text-right">
                            <button type="button" class="btn btn-danger btn-round restoreAllButton">Restore All</button>
                        </div>
                        <div class="table-responsive">
                            <table id="RecordTrashedTable" class="table table-striped table-hover theme-color xjs-exportable" data-sorting="false">
                                <thead>
                                    <tr>
                                        <th class='text-nowrap'>ID #</th>
                                        <th class='text-nowrap'>Customer Id</th>
                                        <th class='text-nowrap'>Customer Name</th>
                                        <th class='text-nowrap'>Customer Email</th>
                                        <th class='text-nowrap'>Customer phone</th>
                                        <th class='text-nowrap'>Order Date</th>
                                        <th class='text-nowrap'>Order Type</th>
                                        <th class='text-nowrap'>Filling</th>
                                        <th class='text-nowrap'>Amount Charged</th>
                                        <th class='text-nowrap'>Order Status</th>
                                        <th class='text-nowrap'>Communication</th>
                                        <th class='text-nowrap'>Project Assigned</th>
                                        <th class="text-center" data-breakpoints="xs md">Status</th>
                                        <th data-breakpoints="xs md">Delete Date</th>
                                        <th class="text-center" data-breakpoints="sm xs md">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($customer_sheets as $customer_sheet)
                                    <tr id="tr-{{$customer_sheet->id}}">
                                        <td class="align-middle">{{$customer_sheet->id}}</td>
                                        <td class="align-middle">{{$customer_sheet->customer_id}}</td>
                                        <td class="align-middle">{{$customer_sheet->customer_name}}</td>
                                        <td class="align-middle">{{$customer_sheet->customer_email}}</td>
                                        <td class="align-middle">{{$customer_sheet->customer_phone}}</td>
                                        <td class="align-middle text-nowrap">{{Carbon\Carbon::parse($customer_sheet->order_date)->format('j F, Y')}}</td>
                                        <td class="align-middle">
                                            {{ $customer_sheet->order_type === 0 ? 'none' :
                                               ($customer_sheet->order_type === 1 ? 'copyright' :
                                               ($customer_sheet->order_type === 2 ? 'trademark' :
                                               ($customer_sheet->order_type === 3 ? 'attestation' : ''))) }}
                                        </td>
                                        <td class="align-middle">
                                            {{ $customer_sheet->filling === 0 ? 'none' :
                                               ($customer_sheet->filling === 1 ? 'logo' :
                                               ($customer_sheet->filling === 2 ? 'slogan' :
                                               ($customer_sheet->filling === 3 ? 'business-name' : ''))) }}
                                        </td>
                                        <td class="align-middle text-center">$ {{$customer_sheet->amount_charged}}</td>
                                        <td class="text-center align-middle">
                                            @if($customer_sheet->order_status === 1)
                                                <span class="badge bg-grey rounded-pill">requested</span>
                                            @elseif($customer_sheet->order_status === 2)
                                                <span class="badge bg-amber rounded-pill">applied</span>
                                            @elseif($customer_sheet->order_status === 3)
                                                <span class="badge bg-amber rounded-pill">received</span>
                                            @elseif($customer_sheet->order_status === 4)
                                                <span class="badge bg-pink rounded-pill">Refund</span>
                                            @elseif($customer_sheet->order_status === 5)
                                                <span class="badge bg-red rounded-pill">objection</span>
                                            @elseif($customer_sheet->order_status === 6)
                                                <span class="badge bg-red rounded-pill">delivered</span>
                                            @elseif($customer_sheet->order_status === 7)
                                                <span class="badge badge-success rounded-pill">approved</span>
                                            @else
                                                <span class="badge badge-success rounded-pill">none</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            {{ $customer_sheet->communication === 0 ? 'none' :
                                               ($customer_sheet->communication === 1 ? 'out-of-reached' :
                                               ($customer_sheet->communication === 2 ? 'skeptic' :
                                               ($customer_sheet->communication === 3 ? 'satisfied' :
                                               ($customer_sheet->communication === 4 ? 'refunded' :
                                               ($customer_sheet->communication === 5 ? 'refund-requested' :
                                               ($customer_sheet->communication === 6 ? 'do-not-call' :
                                               ($customer_sheet->communication === 7 ? 'not-interested' : ''))))))) }}
                                        </td>
                                        <td class="align-middle">{{$customer_sheet->project_assigned}}</td>
                                        <td class="text-center">{!! ($customer_sheet->status == 1)?'<i class="zmdi zmdi-check-circle text-success" title="Publish"></i>' :'<i class="zmdi zmdi-close-circle text-danger" title="Unpublish"></i>' !!}</td>
                                        <td>{{$customer_sheet->deleted_at->format('j F, Y')}}
                                            <br>{{$customer_sheet->deleted_at->format('h:i:s A')}}
                                            <br>{{$customer_sheet->created_at->diffForHumans()}}
                                        </td>
                                            <td class="text-center">
                                                <a title="Restore" data-id="{{$customer_sheet->id}}" class="btn btn-warning btn-sm btn-round restoreButton"><i class="zmdi zmdi-refresh"></i></a>
                                                <a title="Force Delete" data-id="{{$customer_sheet->id}}" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round force-del"><i class="zmdi zmdi-delete"></i></a>
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
    @include('admin.customer-sheet.script')
    <script>
        $(document).ready(function () {
            $('#RecordTrashedTable').DataTable().destroy();
            $('#RecordTrashedTable').DataTable({
                dom: 'lBfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[0, 'desc']],
                scrollX: true
            });
            $('[type=search]').attr('id', "dt-search-box");
        });
    </script>
@endpush
