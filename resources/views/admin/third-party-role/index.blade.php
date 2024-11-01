@extends('admin.layouts.app')@section('cxmTitle', 'Third Party Role')

@section('content')
    @push('css')
        @include('admin.third-party-role.style')
    @endpush
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Third Party Roles</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                            <li class="breadcrumb-item"> Third Party Roles </li>
                            <li class="breadcrumb-item active"><a href="{{route('admin.third.party.role.index')}}"> List</a></li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <button class="btn btn-success btn-icon rounded-circle" type="button" data-toggle="modal" id="create-third-party-role-modal-btn" data-target="#createThirdPartyRoleModal">
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
                                <table id="ThirdPartyRoleTable" class="table table-striped table-hover xjs-basic-example theme-color">
                                    <thead>
                                    <tr>
                                        <th class='text-nowrap'>ID #</th>
                                        <th class='text-nowrap'>Invoice #</th>
                                        <th class='text-nowrap'>Team</th>
                                        <th class='text-nowrap'>Client</th>
                                        <th class='text-nowrap'>Order Id</th>
                                        <th class='text-nowrap'>Order Status</th>
                                        <th>Description</th>
                                        <th class='text-nowrap'>Amount</th>
                                        <th class='text-nowrap'>Created Date</th>
                                        <th class='text-nowrap'>Merchant</th>
                                        <th class='text-nowrap'>Payment Status</th>
                                        <th data-breakpoints="sm xs md">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($third_party_roles as $third_party_role)
                                        <tr id="tr-{{$third_party_role->id}}">
                                            <td class="align-middle">{{$third_party_role->id}}</td>
                                            <td class="align-middle">
                                                @if($third_party_role->invoice_id)
                                                    <a class="text-warning invoice-trigger" data-invoice-num="{{ optional($third_party_role->getInvoice)->invoice_num}}" href="#{{optional($third_party_role->getInvoice)->invoice_num}}">{{optional($third_party_role->getInvoice)->invoice_num}}</a>
                                                    <div class="">
                                                        <span class="badge badge-info rounded-pill">{{ $third_party_role->invoice_id}}</span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="align-middle">{{optional($third_party_role->getTeam)->name}}</td>
                                            <td class="align-middle">{{optional($third_party_role->getClient)->name}}</td>
                                            <td class="align-middle">{{$third_party_role->order_id}}</td>
                                            <td class="align-middle">{{$third_party_role->order_status}}</td>
                                            <td class="align-middle td-make-desc-short" title="{{$third_party_role->description}}">{{$third_party_role->description}}</td>
                                            <td class="align-middle">${{$third_party_role->amount}}</td>
                                            <td class="align-middle text-nowrap">{{Carbon\Carbon::parse($third_party_role->created_at)->format('j F, Y')}}</td>
                                            <td class="align-middle">{{$third_party_role->merchant_type == 1 ? "Authorize" : ($third_party_role->merchant_type == 2 ? "Expigate" : ($third_party_role->merchant_type == 3 ? "PayArc" : ($third_party_role->merchant_type == 4 ? "Paypal" : ($third_party_role->merchant_type == 21 ? "Master Card 0079" : "Unknown Merchant")))) }}</td>
                                            <td class="align-middle">{{$third_party_role->payment_status == 0 ? "Pending" : ($third_party_role->payment_status == 1 ? "In Review" : ($third_party_role->payment_status == 2 ? "Completed" : null)) }}</td>
                                            <td class="align-middle text-nowrap">
                                                <button data-id="{{$third_party_role->id}}" title="Edit" class="btn btn-warning btn-sm btn-round editThirdPartyRole" data-toggle="modal" data-target="#editThirdPartyRoleModal">
                                                    <i class="zmdi zmdi-edit"></i></button>
                                                <a title="Delete" data-id="{{$third_party_role->id}}" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton"><i class="zmdi zmdi-delete"></i></a>
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
    <!-- Create Third Party Role -->
    <div class="modal fade" id="createThirdPartyRoleModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Create A New Third Party Role</h4>
                </div>
                <form method="POST" id="create_form">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="team_key">Select Team Name</label>
                            <select id="team_key" name="team_key" class="change form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Team" data-live-search="true" required>
                                <option disabled>Select Team</option>
                                @foreach($teams as $team)
                                    <option value="{{$team->team_key}}">{{$team->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="agent_id">Select Agent Name</label>
                            <select id="agent_id" name="agent_id" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Agent" data-live-search="true" required>
                                <option disabled>Select Team for Agent List</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="client_id">Select Client</label>
                            <select id="client_id" name="client_id" class="change form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Client" data-live-search="true" required>
                                <option disabled>Select Client</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="invoice_id">Select Invoice</label>
                            <select id="invoice_id" name="invoice_id" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Invoice" data-live-search="true">
                                <option disabled>Select Invoice</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="order_id">Enter Order Id</label>
                            <input id="order_id" name="order_id" type="text" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="order_status">Select Order Status</label>
                            <select id="order_status" name="order_status" class="change form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Order Status" data-live-search="true" required>
                                <option value="Order Placed" selected>Order Placed</option>
                                <option value="Shipped">Shipped</option>
                                <option value="Delivered">Delivered</option>
                                <option value="On Hold">On Hold</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="description">Enter Description</label>
                            <textarea id="description" name="description" class="form-control" placeholder="Description & Details"></textarea>
                            <div class="text-warning">
                                <small> <span class="zmdi zmdi-info"></span> Above description is optional.</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="amount">Enter Amount</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text cxm-currency-symbol-icon"><i class="zmdi zmdi-money"></i></span>
                                </div>
                                <input id="amount" type="number" name="amount" class="form-control" placeholder="Amount" value="0">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="merchant_type">Select Merchant</label>
                            <select id="merchant_type" name="merchant_type" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Merchant">
                                <option value="4" selected>Paypal</option>
                                <option value="21">Master Card 0079</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="payment_status">Select Payment Status</label>
                            <select id="payment_status" name="payment_status" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Payment Status">
                                <option value="0" selected>Pending</option>
                                <option value="1">In Review</option>
                                <option value="2">Completed</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="transaction_id">Enter Payment Transaction Id</label>
                            <input id="transaction_id" name="transaction_id" type="text" class="form-control">
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

    <!-- Edit Third Party Role -->
    <div class="modal fade" id="editThirdPartyRoleModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Edit Third Party Role</h4>
                </div>
                <form method="POST" id="update_form">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="team_key">Select Team Name</label>
                            <select id="edit_team_key" name="team_key" class="change form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Team" data-live-search="true" required>
                                <option disabled>Select Team</option>
                                @foreach($teams as $team)
                                    <option value="{{$team->team_key}}">{{$team->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_agent_id">Select Agent Name</label>
                            <select id="edit_agent_id" name="agent_id" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Agent" data-live-search="true" required>
                                <option disabled>Select Team for Agent List</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_client_id">Select Client</label>
                            <select id="edit_client_id" name="client_id" class="change form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Client" data-live-search="true" required>
                                <option disabled>Select Client</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_invoice_id">Select Invoice</label>
                            <select id="edit_invoice_id" name="invoice_id" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Invoice" data-live-search="true">
                                <option disabled>Select Invoice</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_order_id">Enter Order Id</label>
                            <input id="edit_order_id" name="order_id" type="text" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_order_status">Select Order Status</label>
                            <select id="edit_order_status" name="order_status" class="change form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Order Status" data-live-search="true" required>
                                <option value="Order Placed" selected>Order Placed</option>
                                <option value="Shipped">Shipped</option>
                                <option value="Delivered">Delivered</option>
                                <option value="On Hold">On Hold</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_description">Enter Description</label>
                            <textarea id="edit_description" name="description" class="form-control" placeholder="Description & Details"></textarea>
                            <div class="text-warning">
                                <small> <span class="zmdi zmdi-info"></span> Above description is optional.</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="edit_amount">Enter Amount</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text cxm-currency-symbol-icon"><i class="zmdi zmdi-money"></i></span>
                                </div>
                                <input id="edit_amount" type="number" name="amount" class="form-control" placeholder="Amount" value="0">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="edit_merchant_type">Select Merchant</label>
                            <select id="edit_merchant_type" name="merchant_type" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Merchant">
                                <option value="4" selected>Paypal</option>
                                <option value="21">Master Card 0079</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_payment_status">Select Payment Status</label>
                            <select id="edit_payment_status" name="payment_status" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Payment Status">
                                <option value="0" selected>Pending</option>
                                <option value="1">In Review</option>
                                <option value="2">Completed</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_transaction_id">Enter Payment Transaction Id</label>
                            <input id="edit_transaction_id" name="transaction_id" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-round">SAVE</button>
                        <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@push('cxmScripts')
    @include('admin.third-party-role.script')
@endpush
