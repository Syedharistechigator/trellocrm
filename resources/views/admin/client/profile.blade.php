@extends('admin.layouts.app')
@section('cxmTitle', 'Client Profile')
@section('content')
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>{{$client->name}}</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('client.index') }}">Client</a></li>
                        <li class="breadcrumb-item active">Profile</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    <button class="btn btn-success btn-icon rounded-circle cxm-btn-create" type="button" data-toggle="modal" data-target="#cxmProjectModal"><i class="zmdi zmdi-plus"></i></button>
                    @include('includes.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-3 col-md-12">
                    <div class="card mcard_3">
                        <div class="body">
                            <h4 class="mt-0">{{$client->name}}</h4>
                            <p class="text-muted">{{$client->address}}</p>
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <small>Brand</small>
                                    <img src="{{$client->brandLogo}}">
                                </div>
                                <div class="col-6">
                                    <small>Account Status</small>
                                    <div><span class="badge badge-success">Acctive</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="body">
                            <small class="text-muted">Email Address:</small>
                            <p>{{$client->email}}</p>
                            <hr>
                            <small class="text-muted">Phone:</small>
                            <p>{{$client->phone}}</p>
                            @foreach($client_add_phones as $client_add_phone)
                            <div style="display: flow-root;"><p style="float: left;">{{$client_add_phone->phone}}</p>
                            <form action="{{ route('client_destroy_phone',$client_add_phone->id) }}" method="DELETE" class="form-delete float-right">
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-round teamDelButton delete_button" title="Delete Additional Phone #" onclick="return confirm('Are you sure you want to delete this phone number?');"><i class="zmdi zmdi-delete"></i></button>
                            </form>
                            </div>
                            @endforeach


  <div class="table-responsive">
    <table id="test-table" class="table table-condensed">
      <thead>
        <tr>
          <!-- <th>Phone</th> -->
        </tr>
      </thead>
      <tbody id="test-body">

      </tbody>
    </table>
    <input id='add-row' class='btn btn-primary' type='button' value='Add Phone' />
  </div>

                            <hr>
                            <ul class="list-unstyled">

                                <li>
                                    <div class="row align-items-center">
                                        <div class="col-5">Invoices</div>
                                        <div class="col-7">
                                            <div class="progress-container progress-primary">
                                                <span class="progress-badge font-weight-bold">${{round($invoiceAmount, 2)}}</span>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar progress-bar-warning" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                                                        <span class="progress-value">&nbsp;</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="row align-items-center">
                                        <div class="col-5">Payments</div>
                                        <div class="col-7">
                                           <div class="progress-container progress-info">
                                            <span class="progress-badge font-weight-bold">${{round($totalPayment, 2)}}</span>
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar progress-bar-warning" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                                                    <span class="progress-value">&nbsp;</span>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="row align-items-center">
                                        <div class="col-5">Completed Projects</div>
                                        <div class="col-7">
                                           <div class="progress-container progress-success">
                                            <span class="progress-badge font-weight-bold">{{$completeProject}}</span>
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar progress-bar-warning" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                                                    <span class="progress-value">&nbsp;</span>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="row align-items-center">
                                        <div class="col-5">Open Projects</div>
                                        <div class="col-7">
                                            <div class="progress-container progress-danger">
                                            <span class="progress-badge font-weight-bold">{{$openProject}}</span>
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar progress-bar-warning" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                                                    <span class="progress-value">&nbsp;</span>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-md-12">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="body">
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs p-0 mb-3 nav-tabs-info" role="tablist">
                                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#home_with_icon_title" data-cxm-modal="Project"> <i class="zmdi zmdi-assignment"></i> Projects </a></li>
                                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#profile_with_icon_title" data-cxm-modal="Invoice"><i class="zmdi zmdi-print"></i> Invoice </a></li>
                                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#messages_with_icon_title" data-cxm-modal="Payment"><i class="zmdi zmdi-money"></i> Payments </a></li>
                                        <li class="nav-item"><a class="nav-link nav-link_call_logs"  data-toggle="tab" href="#profile_with_call_logs" data-cxm-modal="call_logs"><i class="zmdi zmdi-phone-in-talk"></i> Call Logs </a></li>
                                        <li class="nav-item dropdown">
                                            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-expanded="false" data-cxm-modal="File"><i class="zmdi zmdi-collection-text"></i> Files <i class="zmdi zmdi-caret-down"></i></a>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" data-toggle="tab" href="#cxm_project_files"><i class="zmdi zmdi-file-text"></i> Project Files</a>
                                                <a class="dropdown-item" data-toggle="tab" href="#cxm_client_files"><i class="zmdi zmdi-file-text"></i> Client Files</a>
                                            </div>
                                        </li>
                                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#project_files" data-cxm-modal="File"><i class="zmdi zmdi-balance-wallet"></i> Expense</a></li>
                                    </ul>
                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane in active" id="home_with_icon_title">
                                            @if(count($clientProjects) == 0)
                                            <div class="text-center">
                                                <img style="width: 130px;" src="{{ asset('assets/images/no-results-found.png') }}">
                                                <br>
                                                <h5>Oops - No records were found</h5>
                                            </div>
                                            @else
                                            <div class="table-responsive">
                                                <table id="projectTable" class="table table-striped table-hover js-basic-example theme-color">
                                                    <thead>
                                                        <tr>
                                                            <th>ID #</th>
                                                            <th>Title</th>
                                                            <th>Due Date</th>
                                                            <th>Amount</th>
                                                            <th data-breakpoints="xs md">Status</th>
                                                            <th class="text-center" data-breakpoints="sm xs md">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($clientProjects as $project)
                                                        <tr>
                                                            <td class="align-middle">{{$project->id}}</td>
                                                            <td class="align-middle"><a class="text-warning align-middle" href="{{route('adminproject.show',$project->id)}}">{{$project->project_title}}</a></td>
                                                            <td class="align-middle">{{$project->project_date_due}}</td>
                                                            <td class="align-middle">${{$project->project_cost}}</td>
                                                            <td class="align-middle"><span class="badge badge-{{$project->statusColor}} rounded-pill">{{$project->status}}</span></td>
                                                            <td class="text-center align-middle">
                                                                <button data-id="{{$project->id}}" title="Edit" class="btn btn-info btn-sm btn-round editproject" data-toggle="modal" data-target="#EditProjecteModal"><i class="zmdi zmdi-edit"></i></button>
                                                                <a title="Change Status" data-id="{{$project->id}}" data-type="confirm" href="javascript:void(0);" class="btn btn-info btn-sm btn-round statusChange" data-toggle="modal" data-target="#changeStatusModal"><i class="zmdi zmdi-settings"></i></a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            @endif
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="profile_with_icon_title">
                                            @if(count($invoices) == 0)
                                            <div class="text-center">
                                                <img style="width: 130px;" src="{{ asset('assets/images/no-results-found.png') }}">
                                                <br>
                                                <h5>Oops - No records were found</h5>
                                            </div>
                                            @else
                                            <div class="table-responsive">
                                                <table id="InvoiceTable" class="table table-striped table-hover js-basic-example theme-color">
                                                    <thead>
                                                        <tr>
                                                            <th>ID #</th>
                                                            <th>Invoice #</th>
                                                            <th>Date</th>
                                                            <th>Project</th>
                                                            <th>Sales Type</th>
                                                            <th>Amount</th>
                                                            <th>Tax%</th>
                                                            <th>Net Amount</th>
                                                            <th class="text-center" data-breakpoints="xs md">Status</th>
                                                            <th data-breakpoints="sm xs md">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($invoices as $invoice)
                                                        <tr>
                                                            <td class="align-middle">{{$invoice->id}}</td>
                                                            <td class="align-middle"><a class="text-info" href="{{route('admin.invoices.index')}}#{{$invoice->invoice_num}}">{{$invoice->invoice_num}}</a> <br> {{$invoice->invoice_key}} </td>
                                                            <td class="align-middle">{{$invoice->created_at->format('j M, Y')}}</td>
                                                            <td class="text-warning align-middle">{{$invoice->ProjectName}}</td>
                                                            <td class="align-middle">{{$invoice->sales_type}}</td>
                                                            <td class="align-middle">${{$invoice->final_amount}}</td>
                                                            <td>{{$invoice->tax_percentage}}% : ${{$invoice->tax_amount}}</td>
                                                            <td>${{$invoice->total_amount}}</td>
                                                            <td class="text-center align-middle">
                                                                @if($invoice->status == 'draft')
                                                                <span class="badge bg-grey rounded-pill">Draft</span>
                                                                @elseif($invoice->status == 'due')
                                                                <span class="badge bg-amber rounded-pill">Due</span>
                                                                @else
                                                                <span class="badge badge-success rounded-pill">Paid</span>
                                                                @endif

                                                            </td>
                                                            <td class="align-middle text-nowrap">
                                                            @if($invoice->status == 'draft')
                                                                <button data-id="{{$invoice->id}}" title="Edit" class="btn btn-info btn-sm btn-round editInvoice" data-toggle="modal" data-target="#editInvoiceModal"><i class="zmdi zmdi-edit"></i></button>
                                                            @endif
                                                                <a title="Email To Client" data-id="{{$invoice->id}}" data-type="confirm" href="javascript:void(0);" class="btn bg-orange btn-sm btn-round sendEmail" data-toggle="modal"><i class="zmdi zmdi-email"></i></a>
                                                                <button Title="Copy Invoice URL"  id="{{$invoice->brandUrl}}checkout?invoicekey={{$invoice->invoice_key}}" class="btn badge-success btn-sm btn-round copy-url"><i class="zmdi zmdi-copy"></i></button>
                                                                <a target="_blank" title="View Invoice" href="{{$invoice->brandUrl}}checkout?invoicekey={{$invoice->invoice_key}}" class="btn btn-primary btn-sm btn-round"><i class="zmdi zmdi-eye"></i></a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            @endif
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="messages_with_icon_title">
                                            @if(count($payments) == 0)
                                            <div class="text-center">
                                                <img style="width: 130px;" src="{{ asset('assets/images/no-results-found.png') }}">
                                                <br>
                                                <h5>Oops - No records were found</h5>
                                            </div>
                                            @else
                                            <div class="table-responsive">
                                                <table id="InvoiceTable" class="table table-striped table-hover js-basic-example theme-color">
                                                    <thead>
                                                        <tr>
                                                            <th>ID #</th>
                                                            <th>Date</th>
                                                            <th>Invoice ID</th>
                                                            <th>Sales Type</th>
                                                            <th>Merchant</th>
                                                            <th>Amount</th>
                                                            <th>Project</th>
                                                            <th class="text-center" data-breakpoints="xs md">Status</th>
                                                            <th class="text-center" data-breakpoints="xs md">Compliance<br>Varified</th>
                                                            <th class="text-center" data-breakpoints="xs md">Operation<br>Varified</th>
                                                            <th class="text-center" data-breakpoints="sm xs md">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($payments as $payment)
                                                        <tr>
                                                            <td class="align-middle">{{$payment->id}}</td>
                                                            <td class="align-middle">{{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y')}}</td>
                                                            <td class="align-middle">{{$payment->invoice_id}}</td>
                                                            <td class="align-middle">{{$payment->sales_type}}</td>
                                                            <td class="align-middle text-nowrap text-capitalize">{{$payment->payment_gateway}}
                                                                @if($payment->payment_gateway === 'authorize' && isset($payment->getAuthorizeMerchant->merchant))
                                                                    <br>
                                                                    <p style="font-size:12px ">( {{ $payment->getAuthorizeMerchant->merchant}} )</p>
                                                                @elseif($payment->payment_gateway === 'Expigate' && isset($payment->getExpigateMerchant->merchant))
                                                                    <br>
                                                                    <p style="font-size:12px ">( {{ $payment->getExpigateMerchant->merchant}} )</p>
                                                                @endif
                                                            </td>
                                                            <td class="align-middle">${{$payment->amount}}</td>
                                                            <td class="align-middle"><a class="text-info" href="{{route('project.show',$payment->project_id)}}">{{$payment->ProjectName}}</a></td>
                                                            <td class="text-center align-middle">
                                @if($payment->payment_status == 1)
                                <span class="badge badge-success rounded-pill">Success</span>
                                @elseif($payment->payment_status == 2)
                                <span class="badge badge-warning rounded-pill">Refund</span>
                                @else
                                <span class="badge badge-danger rounded-pill">Charge Back</span>
                                @endif
                            </td>
                                                            <td class="text-center align-middle">
                                {!! ($payment->compliance_verified == 1)?'<i class="zmdi zmdi-check-circle text-success" title="Active"></i>' :'<i class="zmdi zmdi-close-circle text-danger" title="Inactive"></i>' !!}
                            </td>
                            <td class="text-center align-middle">
                                {!! ($payment->head_verified == 1)?'<i class="zmdi zmdi-check-circle text-success" title="Active"></i>' :'<i class="zmdi zmdi-close-circle text-danger" title="Inactive"></i>' !!}
                            </td>
                                                            <td class="text-center align-middle">
                                                                <a target="_blank" title="View Invoice" href="{{route('payment.show',$payment->invoice_id)}}" class="btn btn-info btn-sm btn-round"><i class="zmdi zmdi-eye"></i></a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            @endif
                                        </div>
                                        <div role="tabpanel" class="tab-pane tab-pane_call_logs" id="profile_with_call_logs">
                                            <div class="table-responsive">
                                                <table id="call_logs_Table" class="table table-striped table-hover js-basic-example theme-color">
                                                    <thead>
                                                        <tr>
                                                            <th>Direction</th>
                                                            <th>Caller</th>
                                                            <th>Caller #</th>
                                                            <th>Callee</th>
                                                            <th>Callee #</th>
                                                            <th>Duration</th>
                                                            <th>Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($client_call_logs as $client_calls)
                                                        @php
                                                           $client_call_log=unserialize($client_calls->response);
                                                            $caller_name = isset($client_call_log['caller_name']) ? $client_call_log['caller_name'] : 'N/A';
                                                            $caller_number = isset($client_call_log['caller_number']) ? $client_call_log['caller_number'] : 'N/A';
                                                            $callee_name = isset($client_call_log['callee_name']) ? $client_call_log['callee_name'] : 'N/A';
                                                            $callee_number = isset($client_call_log['callee_number']) ? $client_call_log['callee_number'] : 'N/A';
                                                            $direction = isset($client_call_log['direction']) ? $client_call_log['direction'] : 'N/A';
                                                            $date_time = isset($client_call_log['date_time']) ? date("y-m-d h:i A", strtotime($client_call_log['date_time'])) : 'N/A';
                                                        @endphp

                                                        @if(isset($client_call_log['duration']))
                                                            @php
                                                                $seconds = $client_call_log['duration'];
                                                                $minutes = floor($seconds / 60);
                                                                $remainingSeconds = $seconds % 60;
                                                                $duration = $minutes . ' : ' . $remainingSeconds . ' sec';
                                                            @endphp
                                                        @else
                                                            @php
                                                                $duration = 'N/A';
                                                            @endphp
                                                        @endif
                                                        <tr>
                                                            <td class="align-middle" style="text-transform: capitalize;">{{ $direction }}</td>
                                                            <td class="align-middle">{{ $caller_name }}</td>
                                                            <td class="align-middle">{{ $caller_number }}</td>
                                                            <td class="align-middle">{{ $callee_name }}</td>
                                                            <td class="align-middle">{{ $callee_number }}</td>
                                                            <td class="align-middle">{{ $duration }}</td>
                                                            <td class="align-middle">{{ $date_time }}</td>
                                                        </tr>
                                                    @endforeach
                                                   </tbody>
                                                </table>
                                            </div>

                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="cxm_project_files">
                                            <h3>Project Files</h3>
                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Exercitationem, quo officiis nam praesentium repellat dolores accusamus vitae voluptatem saepe ipsum natus aperiam eaque reiciendis animi? Deserunt accusamus vero ex? Excepturi?</p>
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="cxm_client_files">
                                            <h3>Client Files</h3>
                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Exercitationem, quo officiis nam praesentium repellat dolores accusamus vitae voluptatem saepe ipsum natus aperiam eaque reiciendis animi? Deserunt accusamus vero ex? Excepturi?</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Default Size -->
<div class="modal fade" id="changeStatusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Change Status</h4>
                <input type="hidden" id="status_hdn" class="form-control" name="status_hdn" value="">
            </div>

            <div class="modal-body">

                <select id="project-status" name="status" class="form-control show-tick ms select2" data-placeholder="Select" required>
                        @foreach($projectStatus as $status)
                            <option value="{{ $status->id }}">{{ $status->status }}</option>
                        @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" id="changeProjectStatus" class="btn btn-success btn-round">SAVE CHANGES</button>
                <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>

<!-- Cxm Project Modal -->
<div class="modal fade" id="cxmProjectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Create New Project</h4>
            </div>

            <form method="POST" id="create_client_project">
            <input type="hidden" name="client_id" value="{{$client->id}}">
                <div class="modal-body">
                    <div class="col-sm-12">
                        <div id="" class="form-group">
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
                        <div id="" class="form-group">
                            <select id="logo_category" name="category_id" class="form-control show-tick ms select2" data-placeholder="Select Project Category" required>
                                <option>Select Project Category</option>
                                @foreach($projectCategories as $category)
                                    <option value="{{$category->id}}">{{$category->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" id="title" class="form-control" placeholder="Project Title" name="title">
                        </div>
                        <div class="form-group">
                            <textarea id="description" class="form-control" placeholder="Description & Details" name="description"></textarea>
                        </div>
                        <div class="form-group">
                            <label style="font-size:10px;">Start Date*</label>
                            <input type="date" id="start_date" class="form-control" placeholder="Due Date" name="start_date" required />
                        </div>
                        <div class="form-group">
                        <label style="font-size: 12px;">Deadline</label>
                                <input type="date" id="due_date" class="form-control" placeholder="Due Date" name="due_date" required />
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="zmdi zmdi-money"></i></span>
                                </div>
                                <input type="number" id="" class="form-control" placeholder="Project Cost" name="project_cost" required />
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="addStatusBtn" class="btn btn-success btn-round waves-effect">Create Project</button>
                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">CLOSE</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cxm Invoice Modal -->
<div class="modal fade" id="cxmInvoiceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Create New Invoice</h4>
            </div>
            <form method="POST" id="create-client-invoice">
                <input type="hidden" name="client_id" value="{{$client->id}}">

                <div class="modal-body">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <select id="client_invoice_brand_key" name="brand_key" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Brand" data-live-search="true" required>
                                @foreach($teamBrand as $brand)
                                    <option value="{{$brand->brandKey}}" data-cxm-team-key="{{ $brand->team_key }}" <?php //if($client->brand_key == $brand->brandKey){echo "selected";} ?>>{{$brand->brandName}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <select id="client_payment_agent_id" name="agent_id" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Agent" data-live-search="true" required>
                                @foreach($members as $member)
                                    <option value="{{$member->id}}">{{$member->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="showProject" class="form-group">
                            <select id="projects" name="project_id" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Project" data-live-search="true" required>
                                @foreach($clientProjects as $clientPro)
                                <option value="{{$clientPro->id}}">{{$clientPro->project_title}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <select id="type" name="sales_type" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Sale Type" required>
                                <option value="Fresh">Fresh</option>
                                <option value="Upsale">Upsale</option>
                            </select>
                        </div>

                         <!--  -->
                         <div class="form-group">
                            <select name="cur_symbol" class="form-control" id="cur_symbol">
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                                <option value="GBP">GBP</option>
                                <option value="AUD">AUD</option>
                                <option value="CAD">CAD</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text cxm-currency-symbol-icon"><i class="zmdi zmdi-money"></i></span>
                                </div>
                                <input type="number" id="amount" class="form-control" placeholder="Amount" name="value" max="{{env('PAYMENT_LIMIT')}}" required />
                            </div>
                        </div>

                        <div class="custom-control custom-switch mb-2">
                            <input type="checkbox" class="custom-control-input toggle-class" id="taxable" name="taxable" value="1" checked>
                            <label class="custom-control-label" for="taxable">Taxable?</label>
                        </div>

                        <div class="form-group" id="taxField">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">%</i></span>
                                </div>
                                <input type="hidden" id="tax_amount" class="form-control" name="taxAmount" value="0">
                                <input type="number" name="tax" id="tax" class="form-control" placeholder="Tax" />
                            </div>
                        </div>
                        <div class="form-group" id="totalAmount">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text cxm-currency-symbol-icon"><i class="zmdi zmdi-money"></i></span>
                                </div>
                                <input type="text" name="total_amount" class="form-control" placeholder="Total Amount" id="total_amount" value="0" readonly>
                            </div>
                        </div>
                        <!--  -->



                        <div class="form-group">
                                <input type="date" id="due_date" class="form-control" placeholder="Due Date" name="due_date" required  />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="addStatusBtn" class="btn btn-success btn-round">Create Invoice</button>
                    <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cxm Payment Modal -->
<div class="modal fade" id="cxmPaymentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">New Payment</h4>
            </div>
            <form method="POST" id="admin_create-client-payment">
            <input type="hidden" name="client_id" value="{{$client->id}}">
            <input type="hidden" id="team_hnd" class="form-control" name="team_key" value="{{$client->team_key}}">

            <div class="modal-body">
                        <div class="col-sm-12">

                        <div id="" class="form-group">
                            <select id="client_payment_brand_key" name="brand_key" class="form-control show-tick ms select2" data-placeholder="Select Brand" required>
                                <option>Select Brand</option>
                                @foreach($teamBrand as $brand)
                                    <option value="{{$brand->brandKey}}" data-cxm-team-key="{{ $brand->team_key }}">{{$brand->brandName}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="" class="form-group">
                            <select id="client_payment_agent_id" name="agent_id" class="form-control show-tick ms select2" data-placeholder="Select Agent" required>
                                <option>Select Sales Agent</option>
                                @foreach($members as $member)
                                    <option value="{{$member->id}}">{{$member->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="showProject" class="form-group">
                            <select id="projects" name="project_id" class="form-control show-tick ms select2" data-placeholder="Select Type" required>
                                <option value="0">Select Project</option>
                                @foreach($clientProjects as $clientPro)
                                    <option value="{{$clientPro->id}}">{{$clientPro->project_title}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <textarea id="invoice_description" class="form-control" placeholder="Description & Details" name="description"></textarea>
                        </div>
                        <div class="form-group">
                            <select id="sales_type" name="sales_type" class="form-control show-tick ms " data-placeholder="Select Type" required>
                                <option>Select Sales Type</option>
                                <option value="Fresh">Fresh</option>
                                <option value="Upsale">Upsale</option>
                                <option value="Recurring">Recurring</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <select id="type" name="merchant" class="form-control show-tick ms select2" data-placeholder="Select Type" required>
                                <option>Select Merchant</option>
                                <option value="Authorize">Authorize</option>
                                <option value="Zelle Pay">Zelle Pay</option>
                                <option value="PayPal">PayPal</option>
                                <option value="Venmo">Venmo</option>
                                <option value="Cash App">Cash App</option>
                                <option value="Wire Transfer">Wire Transfer</option>
                            </select>
                        </div>
                        <div class="form-group" id="projectTileBlock">
                                <input type="text" id="trackId" class="form-control" placeholder="Payment Tracking Id" name="track_id" required />
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="zmdi zmdi-money"></i></span>
                                </div>
                                <input type="number" id="" class="form-control" placeholder="Amount" name="value" required />
                            </div>
                        </div>
                        <div class="form-group">
                                <input type="date" id="due_date" class="form-control" placeholder="Due Date" name="due_date" required  />
                        </div>
                    </div>
                </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success btn-round">Create Payment</button>
                <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Invoice -->
<div class="modal fade" id="editInvoiceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Edit Invoice</h4>
            </div>

            <form method="POST" id="invoice_update_form">
                @csrf
                @method('PUT')
                <input type="hidden" id="invoice_hdn" class="form-control" name="hdn" value="">
                <div class="modal-body">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <select id="edit_brand_key" name="brand_key" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Brand" required>
                                @foreach($teamBrand as $brand)
                                <option value="{{$brand->brandKey}}">{{$brand->brandName}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <select id="edit_agent_id" name="agent_id" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Sale Agent" required>
                                @foreach($members as $member)
                                    <option value="{{$member->id}}">{{$member->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <select id="edit_sales_type" name="sales_type" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Sale Type" required>
                                <option value="Fresh">Fresh</option>
                                <option value="Upsale">Upsale</option>
                                <option value="Recurring">Recurring</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <input type="number" id="edit_amount" class="form-control" placeholder="Amount" name="value" max='2500' required />
                        </div>
                        <div class="form-group">
                            <input type="date" id="edit_due_date" class="form-control" placeholder="Due Date" name="due_date" required  />
                        </div>
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

<!-- Update Project -->
<div class="modal fade" id="EditProjecteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Edit Project</h4>
            </div>

            <form method="POST" id="project_update_form">
                @csrf
                @method('PUT')
                <input type="hidden" id="project_hdn" class="form-control" name="hdn" value="">
                <div class="modal-body">
                    <div class="col-sm-12">
                        <div id="" class="form-group">
                            <select id="edit_project_brand_key" name="brand_key" class="form-control show-tick ms" data-placeholder="Select Brand" required>
                                <option>Select Brand</option>
                                @foreach($teamBrand as $brand)
                                    <option value="{{$brand->brand_key}}">{{$brand->brandName}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="" class="form-group">
                            <select id="edit_project_agent_id" name="agent_id" class="form-control show-tick ms" data-placeholder="Select Agent" required>
                                <option>Select Sales Agent</option>
                                @foreach($members as $member)
                                <option value="{{$member->id}}">{{$member->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="" class="form-group">
                            <select id="edit_project_logo_category" name="category_id" class="form-control show-tick ms" data-placeholder="Select Project Category" required>
                                <option>Select Project Category</option>
                                @foreach($projectCategories as $category)
                                    <option value="{{$category->id}}">{{$category->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" id="edit_project_title" class="form-control" placeholder="Project Title" name="title">
                        </div>
                        <div class="form-group">
                            <textarea id="edit_project_description" class="form-control" placeholder="Description & Details" name="description"></textarea>
                        </div>

                        <div class="form-group">
                            <label style="font-size:10px;">Start Date*</label>
                            <input type="date" name="start_date" id="edit_project_start_date" class="form-control" required />
                        </div>
                        <div class="form-group">
                        <label style="font-size: 12px;">Deadline</label>
                            <input type="date" id="edit_project_due_date" class="form-control" placeholder="Due Date" name="due_date" required />
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="zmdi zmdi-money"></i></span>
                                </div>
                                <input type="number" id="edit_project_cost" class="form-control" placeholder="Project Cost" name="project_cost" required />
                            </div>
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

@endsection

@push('cxmScripts')
    @include('client.script')
    @include('includes.currency-change')
    <script>
        $('.nav-tabs .nav-item .nav-link').on('click', function(){
            let cxmModalActive = $(this).attr('data-cxm-modal');
            $('.cxm-btn-create').attr('data-target', '#cxm'+$.trim(cxmModalActive)+'Modal');
        });


        //Create Client Direct payment
        $('#admin_create-client-payment').on('submit', function(e){
            e.preventDefault();
            console.log('test');
            $('.page-loader-wrapper').css({'display':'block', 'background':'rgba(238, 238, 238, 0.7)'});
            $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                url: "{{ route('adminCreateprojectPayment') }}",
                method:'POST',
                data: $(this).serialize(), // get all form field value in serialize form
                success: function(data){
                    $('.page-loader-wrapper').css('display', 'none');
                    $("#admin_create-client-payment")[0].reset();
                    console.log(data);
                    $("#cxmPaymentModal").modal('hide');

                    swal("Good job!", "Invoice successfully Created!", "success");
                    setInterval('location.reload()', 1000);
                },
                error: function(){
                swal("Errors!", "Request Fail!", "error");
                }
                });
        });


        $('#client_payment_brand_key').selectpicker('val', '{{ $client->brand_key }}');
        $('#client_invoice_brand_key').selectpicker('val', '{{ $client->brand_key }}');


        $('#client_payment_brand_key, #client_invoice_brand_key').on('change', function() {
        var brand = $(this).val();
        let cxmTeamKey = $(this).find(':selected').attr('data-cxm-team-key');
        console.log(brand + ' TK ' + cxmTeamKey);

        $('#team_hnd').val(cxmTeamKey);

        $.ajax({
            type: "GET",
            url: "/adminPaymentTeamMember/"+cxmTeamKey,
                success: function (data) {
                    console.log(data);
                    var len = data.length;
                    console.log(len);
                    $("#client_payment_agent_id").empty();

                    $("#client_payment_agent_id").append('<option class="bs-title-option" value="">Select Agent</option><option value="1">Default User</option>');
                    for( var i = 0; i<len; i++){
                        var id = data[i]['id'];
                        var name = data[i]['name'];
                        $("#client_payment_agent_id").append('<option value="'+id+'">'+name+'</option>');
                    }
                    //$('#client_payment_agent_id').selectpicker('refresh');
                }
            });
        });

          // Add row
  var row=1;
  var add_phone_url="{{ route('client_add_phone') }}";
  var client_id="{{$client->id}}";
$(document).on("click", "#add-row", function () {
    var new_row = '<tr id="row' + row + '"><td><form class="phone-form" action="' + add_phone_url + '" method="POST"><input name="client_id" value="'+ client_id +'" type="hidden"/><input name="phone" required type="text" class="form-control" /><input type="submit" class="submit-row btn btn-success" value="Submit" /></form></td><td><input class="delete-row btn btn-primary" type="button" value="Delete" /></td></tr>';
    $('#test-body').append(new_row);
    row++;
    return false;
  });

  // Remove criterion
  $(document).on("click", ".delete-row", function () {
  //  alert("deleting row#"+row);
    if(row>1) {
      $(this).closest('tr').remove();
      row--;
    }
  return false;
  });

  $(document).on("submit", ".phone-form", function (e) {
    e.preventDefault(); // Prevent the default form submission
    var formData = $(this).serialize();

    // Include the CSRF token
    // var csrfToken = $('meta[name="csrf-token"]').attr('content');
    // formData += '&_token=' + csrfToken;

    // AJAX submission
    $.ajax({
      type: 'POST',
      url: $(this).attr('action'),
      data: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
      success: function (response) {
        swal("Good job!","Phone added successfully","success");
          location.reload()
        // setInterval('location.reload()', 1000);
      },
      error: function (error) {
        swal("Errors!", "Phone required" , "error");
        // setInterval('location.reload()', 1000);
      }
    });
  });
</script>
@endpush
