@extends('admin.layouts.app')@section('cxmTitle', 'Wire Payments')

@section('content')
    @push('css')
        @include('admin.payment.wire-payment.style')
    @endpush
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Wire Payments</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}
                                </a></li> <li class="breadcrumb-item">Sales</li>
                            <li class="breadcrumb-item active"> Wire Payments</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <button class="btn btn-success btn-icon rounded-circle" type="button" data-toggle="modal" id="create-wire-payment-modal-btn" data-target="#createWirePaymentModal">
                            <i class="zmdi zmdi-plus"></i></button>
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
                    <table id="WirePaymentTable" class="table table-striped table-hover theme-color xjs-exportable" xdata-sorting="false">
                        <thead>
                        <tr>
                            <th class='text-center'>ID#</th>
                            <th class='text-nowrap text-center'>Brand</th>
                            <th class='text-nowrap'>Agent</th>
                            <th class='text-nowrap'>Client</th>
                            <th class='text-nowrap'>Sales Type</th>
                            <th class='text-nowrap'>Amount</th>
                            <th>Description</th>
                            <th class='text-nowrap' data-breakpoints="xs md">Payment Date</th>
                            <th class='text-nowrap'>Transaction ID</th>
                            <th class="text-nowrap text-center" data-breakpoints="xs md">Status</th>
                            <th class='text-nowrap'>Created By</th>
                            <th class='text-nowrap'>Status Changed By</th>
                            <th data-breakpoints="sm xs md">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($wire_payments as $wire_payment)
                            <tr id="tr-{{$wire_payment->id}}">
                                <td class="align-middle text-center">{{$wire_payment->id}}</td>
                                <td class="align-middle text-center text-nowrap">
                                    @if($wire_payment->getBrand)
                                        <span class="brand-icon">
                                                <object data="{!! $wire_payment->getBrand->logo !!}">
                                                    @if(config('app.home_name') == 'Uspto')
                                                        <img src="{{asset('assets/images/uspto-colored.png')}}" alt="{{$wire_payment->getBrand->name}}" loading="lazy">
                                                    @else
                                                        <img src="{{asset('assets/images/logo-colored.png')}}" alt="{{$wire_payment->getBrand->name}}" loading="lazy">
                                                    @endif
                                                </object>
                                            </span>
                                        <br>
                                        <a href="{{route('brand.edit',[$wire_payment->getBrand->id],'/edit')}}" title="{{$wire_payment->getBrand->brand_url}}">
                                            <span class="text-muted text-warning">{{$wire_payment->getBrand->name}}</span>
                                        </a>
                                        <br>{{$wire_payment->getBrand->brand_key}}
                                    @else
                                        <span class="text-muted">Not found</span>
                                    @endif
                                </td>
                                <td class="align-middle">{{optional($wire_payment->getAgent)->name}}</td>
                                <td class="align-middle text-nowrap">
                                    {{$wire_payment->client_name}}<br>{{$wire_payment->client_email}}
                                </td>
                                <td class="align-middle text-nowrap">{{$wire_payment->sales_type}}</td>
                                <td class="align-middle text-nowrap">${{$wire_payment->amount}}</td>
                                <td class="align-middle td-make-desc-short" title="{{$wire_payment->description}}">{{$wire_payment->description}}</td>
                                <td class="align-middle text-nowrap">{{Carbon\Carbon::parse($wire_payment->due_date)->format('j F, Y')}}</td>
                                <td class="align-middle text-nowrap">{{$wire_payment->transaction_id}}</td>
                                <td class="text-center align-middle td-payment-approval" id="payment-approval-{{$wire_payment->id}}">
                                    @if($wire_payment->payment_approval == 'Approved')
                                        <span class="badge badge-success rounded-pill">Approved</span>
                                    @elseif($wire_payment->payment_approval == 'Not Approved')
                                        <span class="badge badge-danger rounded-pill">Not Approved</span>
                                    @else
                                        <span class="badge badge-warning rounded-pill">Pending</span>
                                    @endif
                                </td>
                                <td class="align-middle text-nowrap text-center">{{optional($wire_payment->actor)->name}}</td>
                                <td class="align-middle text-nowrap text-center td-status-change" id="status-change-{{$wire_payment->id}}">{{optional($wire_payment->approval_actor)->name}}</td>
                                <td class="text-center align-middle" id="action-btn-{{$wire_payment->id}}">
                                    @if(is_array(json_decode($wire_payment->screenshot,true)) && count(json_decode($wire_payment->screenshot,true)) > 0)
                                        <button data-id="{{$wire_payment->id}}" id="attachment-id-{{$wire_payment->id}}" title="View Attachment" class="btn btn-warning btn-sm btn-round viewAttachments" data-toggle="modal" data-target="#attachmentsModal">
                                            <i class="zmdi zmdi-attachment-alt"></i></button>
                                    @endif
                                    @if($wire_payment->payment_approval == 'Pending')
                                        <button data-id="{{$wire_payment->id}}" id="wire-payment-id-{{$wire_payment->id}}" title="Change Payment Approval Status" class="btn btn-warning btn-sm btn-round paymentApproval" data-toggle="modal" data-target="#PaymentApprovalModal">
                                            <i class="zmdi zmdi-refresh-sync"></i></button>
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

    <!-- Create Wire Payment -->
    <div class="modal fade" id="createWirePaymentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Create A New Wire Payment</h4>
                </div>
                <form method="POST" id="create_form" enctype="multipart/form-data">
                    <input type="hidden" id="team_hnd" class="form-control" name="team_key" value="">
                    <div class="modal-body">
                        <div class="col-sm-12">
                            <div id="" class="form-group">
                                <label for="brand_key">Select Brand Name</label>
                                <select id="brand_key" name="brand_key" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Brand" data-live-search="true" required>
                                    @foreach($assign_brands as $assign_brand)
                                        <option value="{{$assign_brand->brand_key}}" data-team-key="{{ $assign_brand->team_key }}">{{optional($assign_brand->getBrand)->name . " - ".$assign_brand->brand_key  }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="agent_id">Select Agent</label>
                                <select id="agent_id" name="agent_id" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Agent" data-live-search="true" required>
                                    <option disabled>Select Sales Agent</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="sales_type">Select Sale Type</label>
                                <select id="sales_type" name="sales_type" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Sales Type" data-live-search="true" required>
                                    <option disabled>Select Sales Type</option>
                                    <option value="Fresh">Fresh</option>
                                    <option value="Upsale">Upsale</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="name">Client Name</label>
                                <input type="text" id="name" class="form-control" placeholder="Name" name="name" autocomplete="name">
                            </div>
                            <div class="form-group">
                                <label for="email">Client Email</label>
                                <input type="email" id="email" class="form-control" placeholder="Email" name="email" autocomplete="email">
                            </div>
                            <div class="form-group">
                                <label for="phone">Client Phone Number</label>
                                <input type="text" id="phone" class="form-control" placeholder="Phone" name="phone" autocomplete="phone">
                            </div>
                            <div class="form-group" id="projectTileBlock">
                                <label for="projectTitle">Project Title</label>
                                <input type="text" id="projectTitle" class="form-control" placeholder="Project Title" name="project_title"/>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" class="form-control" placeholder="Description & Details" name="description"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="due_date">Payment Date</label>
                                <input type="date" id="due_date" class="form-control" placeholder="Due Date" name="due_date" value="{{ date('Y-m-d') }}" min="{{Date::now()->subYears(2)->addDay()->format('Y-m-d')}}" max="{{Date::now()->addDay()->format('Y-m-d')}}" required/>
                            </div>
                            <div class="form-group">
                                <label for="amount">Amount</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="zmdi zmdi-money"></i></span>
                                    </div>
                                    <input type="number" id="amount" class="form-control" placeholder="Amount" name="amount" required autocomplete="value"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="transaction_id">Payment Transaction Id</label>
                                <input type="text" id="transaction_id" class="form-control" placeholder="Payment Tracking Id" name="transaction_id"/>
                            </div>
                            <div class="form-group">
                                <label for="screenshots">Upload Attachments (Optional)</label>
                                <input type="file" id="screenshots" class="form-control" accept="image/*" multiple>
                            </div>
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

    <!-- Approval Status Modal -->
    <div class="modal fade" id="PaymentApprovalModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="PaymentApprovalModalLabel">Payment Approval</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" id="update-payment-approval-form">
                    <div class="modal-body">
                        <div id="" class="form-group">
                            <label for="payment_approval">Payment Approval</label>
                            <select id="payment_approval" name="payment_approval" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Payment Status" data-live-search="true" required>
                                <option disabled>Select Payment Status</option>
                                <option value="Approved">Approved</option>
                                <option value="Not Approved">Not Approved</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="paymentApprovalSaveBtn" class="btn btn-success btn-round" style="display: block;">SAVE</button>
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
                                <th class="width-100px">Image</th>
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
@endsection

@push('cxmScripts')
    @include('admin.payment.wire-payment.script')
@endpush
