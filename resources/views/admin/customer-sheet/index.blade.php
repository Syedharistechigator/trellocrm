@extends('admin.layouts.app')@section('cxmTitle', 'Customer Sheet')

@section('content')
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Customer Sheet</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                            <li class="breadcrumb-item"> Customer Sheet</li>
                            <li class="breadcrumb-item active"> List</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right customer-sheet">
                        <button class="btn btn-success btn-icon rounded-circle" type="button" data-toggle="modal" id="create-customer-sheet-modal" data-target="#customerSheetModal">
                            <i class="zmdi zmdi-plus"></i></button>
                        @include('includes.admin.cxm-top-right-toggle-btn')
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12">
                        <div class="">
                            <div class="table-responsive">
                                <table id="CustomerSheetTable" class="table table-striped table-hover theme-color xjs-exportable" data-sorting="false">
                                    <thead>
                                    <tr>
                                        <th class='text-nowrap'>ID #</th>
                                        <th class='text-nowrap'>Created By</th>
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
                                        <th data-breakpoints="sm xs md">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($customer_sheets as $customer_sheet)
                                        <tr id="tr-{{$customer_sheet->id}}">
                                            <td class="align-middle">{{$customer_sheet->id}}</td>
                                            <td class="align-middle">{{isset($customer_sheet->creator->name) ? $customer_sheet->creator->name : ""}}</td>
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
                                                    <span class="badge bg-danger rounded-pill">Refund</span>
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
                                            <td class="align-middle text-nowrap" id="action-btn-{{$customer_sheet->id}}">
                                                @if($customer_sheet->attachments() !== null && $customer_sheet->attachments()->count() > 0)
                                                    <button data-id="{{$customer_sheet->id}}" id="attachment-id-{{$customer_sheet->id}}" title="View Attachment" class="btn btn-warning btn-sm btn-round viewAttachments" data-toggle="modal" data-target="#attachmentsModal">
                                                        <i class="zmdi zmdi-attachment-alt"></i></button>
                                                @endif
                                                <button data-id="{{$customer_sheet->id}}" id="add-attachment-id-{{$customer_sheet->id}}" title="Add Attachment" class="btn btn-warning btn-sm btn-round addAttachments" data-toggle="modal" data-target="#addAttachmentsModal">
                                                    <i class="zmdi zmdi-plus-circle"></i></button>
                                                <button data-id="{{$customer_sheet->id}}" title="Edit" class="btn btn-warning btn-sm btn-round editCustomerSheet" data-toggle="modal" data-target="#editCustomerSheetModal">
                                                    <i class="zmdi zmdi-edit"></i></button>
                                                    <a title="Delete" data-id="{{$customer_sheet->id}}" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton"><i class="zmdi zmdi-delete"></i></a>
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

    <!-- Create Customer Sheet -->
    <div class="modal fade" id="customerSheetModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Create A New Customer Sheet</h4>
                </div>
                <form method="POST" id="create-customer-sheet-form" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="customer_name">Enter Customer Name</label>
                            <input type="text" id="customer_name" class="form-control" placeholder="Enter Customer Name" name="customer_name" required>
                        </div>
                        <div class="form-group">
                            <label for="customer_email">Enter Customer Email</label>
                            <input type="email" id="customer_email" class="form-control" placeholder="Enter Customer Email" name="customer_email" required>
                        </div>
                        <div class="form-group">
                            <label for="customer_phone">Enter Customer Phone Number</label>
                            <input type="text" id="customer_phone" class="form-control" placeholder="Enter Customer Phone Number" name="customer_phone" required>
                        </div>
                        <div class="form-group">
                            <label for="order_date">Enter Order Date</label>
                            <input type="date" id="order_date" class="form-control" placeholder="Enter Order Date" name="order_date" value="{{ date('Y-m-d') }}" min="{{Date::now()->subYears(10)->addDay()->format('Y-m-d')}}" required/>
                        </div>
                        <div class="form-group">
                            <label for="order_type">Select Order Type</label>
                            <select id="order_type" name="order_type" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Order Type" required>
                                <option disabled>Select Order Type</option>
                                <option value="1">Copyright</option>
                                <option value="2">Trademark</option>
                                <option value="3">Attestation</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filling">Select Filling</label>
                            <select id="filling" name="filling" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Filling" required>
                                <option disabled>Select Filling</option>
                                <option value="1">Logo</option>
                                <option value="2">Slogan</option>
                                <option value="3">Business Name</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="amount_charged">Enter Amount Charged</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text cxm-currency-symbol-icon"><i class="zmdi zmdi-money"></i></span>
                                </div>
                                <input type="number" id="amount_charged" class="form-control" placeholder="Amount Charged" name="amount_charged" value="0" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="order_status">Select Order Status</label>
                            <select id="order_status" name="order_status" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Order Status" required>
                                <option disabled>Select Order Status</option>
                                <option value="1">Requested</option>
                                <option value="2">Applied</option>
                                <option value="3">Received</option>
                                <option value="4">Rejected</option>
                                <option value="5">Objection</option>
                                <option value="6">Approved</option>
                                <option value="7">Delivered</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="communication">Select Communication</label>
                            <select id="communication" name="communication" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Communication" required>
                                <option disabled>Select Communication</option>
                                <option value="1">Out Of Reach</option>
                                <option value="2">Skeptic</option>
                                <option value="3">Satisfied</option>
                                <option value="4">Refunded</option>
                                <option value="5">Refund Requested</option>
                                <option value="6">Do Not Call</option>
                                <option value="7">Not Interested</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="project_assigned">Enter Project Assigned To</label>
                            <input type="text" id="project_assigned" class="form-control" placeholder="Enter Project Assigned To" name="project_assigned" required>
                        </div>
                        <div class="form-group">
                            <label for="attachments">Upload Attachments (Optional)</label>
                            <input type="file" id="attachments" class="form-control" name="attachments[]" multiple>
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

    <!-- Edit Customer Sheet -->
    <div class="modal fade" id="editCustomerSheetModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="editCustomerSheetModalLabel">Edit Customer Sheet</h4>
                </div>
                <form method="POST" id="update-customer-sheet-form">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_customer_name">Enter Customer Name</label>
                            <input type="text" id="edit_customer_name" class="form-control" placeholder="Enter Customer Name" name="customer_name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_customer_email">Enter Customer Email</label>
                            <input type="email" id="edit_customer_email" class="form-control" placeholder="Enter Customer Email" name="customer_email" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_customer_phone">Enter Customer Phone Number</label>
                            <input type="text" id="edit_customer_phone" class="form-control" placeholder="Enter Customer Phone Number" name="customer_phone" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_order_date">Enter Order Date</label>
                            <input type="date" id="edit_order_date" class="form-control" placeholder="Enter Order Date" name="edit_order_date" value="{{ date('Y-m-d') }}" min="{{Date::now()->subYears(10)->addDay()->format('Y-m-d')}}" required/>
                        </div>
                        <div class="form-group">
                            <label for="edit_order_type">Select Order Type</label>
                            <select id="edit_order_type" name="order_type" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Order Type" required>
                                <option disabled>Select Order Type</option>
                                <option value="1">Copyright</option>
                                <option value="2">Trademark</option>
                                <option value="3">Attestation</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_filling">Select Filling</label>
                            <select id="edit_filling" name="filling" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Filling" required>
                                <option disabled>Select Filling</option>
                                <option value="1">Logo</option>
                                <option value="2">Slogan</option>
                                <option value="3">Business Name</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_amount_charged">Enter Amount Charged</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text cxm-currency-symbol-icon"><i class="zmdi zmdi-money"></i></span>
                                </div>
                                <input type="number" id="edit_amount_charged" class="form-control" placeholder="Amount Charged" name="amount_charged" value="0" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="edit_order_status">Select Order Status</label>
                            <select id="edit_order_status" name="order_status" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Order Status" required>
                                <option disabled>Select Order Status</option>
                                <option value="1">Requested</option>
                                <option value="2">Applied</option>
                                <option value="3">Received</option>
                                <option value="4">Rejected</option>
                                <option value="5">Objection</option>
                                <option value="6">Approved</option>
                                <option value="7">Delivered</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_communication">Select Communication</label>
                            <select id="edit_communication" name="communication" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Communication" required>
                                <option disabled>Select Communication</option>
                                <option value="1">Out Of Reach</option>
                                <option value="2">Skeptic</option>
                                <option value="3">Satisfied</option>
                                <option value="4">Refunded</option>
                                <option value="5">Refund Requested</option>
                                <option value="6">Do Not Call</option>
                                <option value="7">Not Interested</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_project_assigned">Enter Project Assigned To</label>
                            <input type="text" id="edit_project_assigned" class="form-control" placeholder="Enter Project Assigned To" name="project_assigned" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="EditStatusBtn" class="btn btn-success btn-round">SAVE</button>
                        <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Attachments Modal -->
    <div class="modal fade" id="attachmentsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Attachments Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="attachmentsTable" class="table table-striped table-hover table-sm theme-color xjs-exportable" xdata-sorting="false">
                            <thead>
                            <tr>
                                <th>Id #</th>
                                <th>File Name</th>
                                <th>File Type</th>
                                <th>File Size</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody id="attachmentsList"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Attachments Modal -->
    <div class="modal fade" id="addAttachmentsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="addAttachmentModalLabel">Add Attachment</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" id="add-customer-sheet-attachment-form" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="add_more_attachments">Upload Attachments</label>
                            <input type="file" id="add_more_attachments" class="form-control" name="attachments[]" multiple>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="addMoreAttachmentSaveBtn" class="btn btn-success btn-round" style="display: none;">SAVE</button>
                        <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('cxmScripts')
    @include('admin.customer-sheet.script')

    <script>
        $(document).ready(function () {
            $('#CustomerSheetTable').DataTable().destroy();
            $('#CustomerSheetTable').DataTable({
                dom: 'lBfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[0, 'desc']],
                scrollX: true,
            });
            $('[type=search]').attr('id', "dt-search-box-" + randomNumber);
        });
        function getRandomInt(min, max) {
            min = Math.ceil(min);
            max = Math.floor(max);
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }
        var randomNumber = getRandomInt(1, 20);
        $('[type=search]').attr('id', "dt-search-box-" + randomNumber);
    </script>

@endpush
