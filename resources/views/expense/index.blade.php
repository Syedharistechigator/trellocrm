@extends('layouts.app')

@section('cxmTitle', 'Expense')

@section('content')
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Expense</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">Sales</li>
                        <li class="breadcrumb-item active"> Expense</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    <button class="btn btn-success btn-icon rounded-circle" type="button" data-toggle="modal" data-target="#ExpenseModal"><i class="zmdi zmdi-plus"></i></button>
                    @include('includes.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>


        <div class="card">
            <div class="table-responsive">
                <table id="InvoiceTable" class="table table-striped table-hover js-basic-example theme-color">
                    <thead>
                        <tr>
                            <th>ID #</th>
                            <th>Date</th>
                            <th>Brand</th>
                            <th>Project</th>
                            <th>Title & Description</th>
                            <th>Amount</th>
                            <th class="text-center" data-breakpoints="xs md">Status</th>
                            <th data-breakpoints="sm xs md">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenses as $expense)
                        <tr>
                            <td class="align-middle">{{$expense->id}}</td>
                            <td class="align-middle">{{$expense->created_at->format('j F, Y')}}</td>
                            <td class="align-middle">{{$expense->brand_key}}</td>
                            <td class="text-info align-middle"><a class="text-info" href="">{{$expense->project_id}}</a></td>
                            <td class="align-middle">{{$expense->title}}<br>{{$expense->description}}</td>
                            <td class="align-middle">${{$expense->amount}}</td>
                            <td class="text-center align-middle">
                            {!! ($expense->status == 1)?'<span class="zmdi zmdi-check-circle text-success"></span>' :'<span class="zmdi zmdi-close-circle text-danger"></span>'; !!}
                            </td>
                            <td class="align-middle">

                                <a title="View Invoice" href="" class="btn btn-info btn-sm btn-round"><i class="zmdi zmdi-eye"></i></a>

                                <a title="Email To Client" data-id="{{$expense->id}}" data-type="confirm" href="javascript:void(0);" class="btn bg-orange btn-sm btn-round sendEmail" data-toggle="modal"><i class="zmdi zmdi-email"></i></a>

                                <button Title="Copy Invoice URL"  id="" class="btn badge-success btn-sm btn-round copy-url"><i class="zmdi zmdi-copy"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>
<!-- Create Invoice -->
<div class="modal fade" id="ExpenseModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Create New Expense</h4>
            </div>

        <form method="POST" id="expense-Form">
            <input type="hidden" id="clientId" class="form-control" name="client_id" value="">
                <div class="modal-body">
                        <div class="col-sm-12">

                        <div id="" class="form-group">
                            <select id="team_key" name="team_key" class="form-control ms select2x" data-placeholder="Select Team" required>
                                <option>Select Team</option>
                                @foreach($teams as $team)
                                    <option value="{{$team->team_key}}">{{$team->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="teamBrand" class="form-group">
                            <select id="brand" name="brand_key" class="form-control show-tick ms select2x" data-placeholder="Select Brand" required>
                                <option value="">Select Brand</option>
                            </select>
                        </div>

                        <div id="showProject" class="form-group">
                            <select id="projects" name="project_id" class="form-control  ms select2x" data-placeholder="Select Type" required>
                                <option value="">Select Project</option>
                            </select>
                        </div>

                        <div id="" class="form-group">
                            <select id="agent_id" name="agent_id" class="form-control ms select2x" data-placeholder="Select Agent" required>
                                <option>Select Sales Agent</option>
                                @foreach($members as $member)
                                    <option value="{{$member->id}}">{{$member->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" id="title" class="form-control" placeholder="Expense Title" name="title">
                        </div>
                        <div class="form-group">
                            <textarea id="invoice_description" class="form-control" placeholder="Description & Details" name="description"></textarea>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="zmdi zmdi-money"></i></span>
                                </div>
                                <input type="number" id="amount" class="form-control" placeholder="Amount" name="value" required />
                            </div>
                        </div>




                        {{--<div id="" class="form-group">
                            <select id="brand_key" name="brand_key" class="form-control show-tick ms select2" data-placeholder="Select Brand" required>
                                <option>Select Brand</option>
                                @foreach($teamBrand as $brand)
                                    <option value="{{$brand->brandKey}}">{{$brand->brandName}}</option>
                                @endforeach

                            </select>
                        </div>

                        <div id="" class="form-group">
                            <select id="agent_id" name="agent_id" class="form-control show-tick ms select2" data-placeholder="Select Agent" required>
                                <option>Select Sales Agent</option>
                                @foreach($members as $member)
                                    <option value="{{$member->id}}">{{$member->name}}</option>
                                @endforeach

                            </select>
                        </div>

                        <div class="form-group">
                            <select id="type" name="sales_type" class="form-control show-tick ms select2" data-placeholder="Select Type" required>
                                <option>Select Sales Type</option>
                                <option value="New">New</option>
                                <option value="Upsale">Upsale</option>
                            </select>
                        </div>

                        <div id="showClient" class="form-group" style="display: none;">
                            <select id="client" name="client_id" class="form-control show-tick ms select2" data-placeholder="Select Type" required>
                                <option value="0">Select Client</option>
                                @foreach($teamClients as $client)
                                    <option value="{{$client->id}}">{{$client->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="showProject" class="form-group" style="display: none;">
                            <select id="projects" name="project_id" class="form-control show-tick ms select2" data-placeholder="Select Type" required>
                                <option value="0">Select Project</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <input type="text" id="name" class="form-control" placeholder="Name" name="name">
                        </div>
                        <div class="form-group">
                            <input type="email" id="email" class="form-control" placeholder="Email" name="email">
                        </div>
                        <div class="form-group">
                            <input type="text" id="phone" class="form-control" placeholder="Phone" name="phone">
                        </div>
                        <div class="form-group" id="projectTileBlock">
                                <label>Project Title</label>
                                <input type="text" id="projectTitle" class="form-control" placeholder="Project Title" name="project_title" />
                        </div>
                        <div class="form-group">
                            <textarea id="invoice_description" class="form-control" placeholder="Description & Details" name="description"></textarea>
                        </div>

                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="zmdi zmdi-money"></i></span>
                                </div>
                                <input type="number" id="email" class="form-control" placeholder="Amount" name="value" required />
                            </div>
                        </div>
                        <div class="form-group">
                                <input type="date" id="due_date" class="form-control" placeholder="Due Date" name="due_date" required  />
                        </div>
                    </div>
                </div>--}}
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
    @include('expense.script')
@endpush
