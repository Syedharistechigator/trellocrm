@extends('layouts.app')

@section('cxmTitle', 'Project Details')

@section('content')
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>{{$project->project_title}}</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('project.index') }}">Project</a></li>
                        <li class="breadcrumb-item active">Details</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                @if(Auth::user()->type != 'client')
                    <button class="btn btn-success btn-icon rounded-circle cxm-btn-create" type="button" data-toggle="modal" data-target="#cxmDetailModal" title="Add Task"><i class="zmdi zmdi-plus" ></i></button>
                @else
                    <button class="btn btn-success btn-icon rounded-circle cxm-btn-create d-none" type="button" data-toggle="modal" data-target="#cxmDetailModal" title="Add Task"><i class="zmdi zmdi-plus" ></i></button>
                @endif
                    @include('includes.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-3 col-md-12">
                    <div class="card mcard_3">
                        <div class="body">
                            <h4 class="mt-0"><a class="text-info" href="{{route('client.show',$project->clientid)}}">{{$project->clientName}}</a></h4>
                            <div class="row">
                                <div class="col-6">
                                    <small>Sales Agent</small>
                                    <p>{{$project->projectAgent}}</p>
                                </div>
                                <div class="col-6">
                                    <small>Project Manager</small>
                                    <p>{{$project->projectManager}}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <small>Start Date</small>
                                    <p>{{$project->project_date_start}}</p>
                                </div>
                                <div class="col-6">
                                    <small>Due Date</small>
                                    <p>{{$project->project_date_due}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <small>Project Category</small>
                                    <p>{{$project->category}}</p>
                                </div>
                                <div class="col-6">
                                    <small>Project Status</small>
                                    <p><span class="badge badge-{{$project->projectStatusColor}} rounded-pill">{{$project->projectStatus}}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="body">
                            <ul class="list-unstyled">
                                <li>
                                    <div class="row align-items-center">
                                        <div class="col-5">Total Project Cost:</div>
                                        <div class="col-7">
                                            <div class="progress-container progress-primary">
                                                <span class="progress-badge font-weight-bold" style="font-size:15px;">${{$project->project_cost}}</span>
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
                                        <div class="col-5">Invoices</div>
                                        <div class="col-7">
                                            <div class="progress-container progress-success">
                                                <span class="progress-badge font-weight-bold" style="font-size:15px;">${{ $projectInvoicesAmount }}</span>
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
                                                <span class="progress-badge font-weight-bold" style="font-size:15px;">${{ $projectPaymentsAmount }}.00</span>
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
                                        <div class="col-5">Balance Due</div>
                                        <div class="col-7">
                                           <div class="progress-container progress-danger">
                                                <span class="progress-badge font-weight-bold" style="font-size:15px;">${{ $projectInvoicesAmount - $projectPaymentsAmount }}.00</span>
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
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="body">
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs p-0 mb-3 nav-tabs-info" role="tablist">
                                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#cxm_detail" data-cxm-modal="xDetail"><i class="zmdi zmdi-assignment"></i> Details </a></li>
                                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#cxm_invoice" data-cxm-modal="Invoice"><i class="zmdi zmdi-print"></i> Invoice</a></li>
                                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#cxm_payment" data-cxm-modal="xPayment"><i class="zmdi zmdi-money"></i> Payments</a></li>
                                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#project_files" data-cxm-modal="File"><i class="zmdi zmdi-file-text"></i> Files</a></li>
                                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#project_files" data-cxm-modal="File"><i class="zmdi zmdi-balance-wallet"></i> Expense</a></li>
                                        <li class="nav-item"><a id="comment_section" class="nav-link" data-toggle="tab" href="#cxm_comments" data-cxm-modal="Comment"><i class="zmdi zmdi-comment-text"></i> Comments</a></li>
                                    </ul>

                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane in active" id="cxm_detail">
                                            <p id="detail-box">{!! html_entity_decode($project->project_description) !!}</p>
                                            <input type="hidden" id="project_id" value="{{$project->id}}">
                                            <textarea rows="10" class="form-control no-resize" id="detail-text-edit" style="display:none;">{{$project->project_description}}</textarea>
                                            @if(Auth::user()->type != 'client')
                                            <button type="button" class="btn waves-effect waves-light btn-xs btn-info" id="project-description-button-edit">Edit Description</button>
                                            <button type="button" class="btn waves-effect waves-light btn-xs btn-success" id="project-description-button-update" style="display:none;">Update Description</button>
                                            @endif
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="cxm_invoice">
                                            @if(count($projectInvoices) == 0)
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
                                                            <th>Amount</th>
                                                            <th>Tax%</th>
                                                            <th>Net Amount</th>
                                                            <th class="text-center" data-breakpoints="xs md">Status</th>
                                                            <th data-breakpoints="sm xs md">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($projectInvoices as $invoice)
                                                        <tr>
                                                            <td>{{$invoice->id}}</td>
                                                            <td>{{$invoice->invoice_num}}</td>
                                                            <td>{{$invoice->created_at->format('j M, Y')}}</td>
                                                            <td>{{$invoice->cur_symbol}}{{$invoice->final_amount}}</td>
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
                                                            <td class="text-nowrap">
                                                            @if(Auth::user()->type != 'client')
                                                                @if($invoice->status == 'draft')
                                                                    <button data-id="{{$invoice->id}}" title="Publish" class="btn btn-info btn-sm btn-round publishInvoice" data-toggle="modal" data-target="modal">Publish</button>

                                                                    <button data-id="{{$invoice->id}}" title="Edit" class="btn btn-info btn-sm btn-round editInvoice" data-toggle="modal" data-target="#editInvoiceModal"><i class="zmdi zmdi-edit"></i></button>
                                                                @else
                                                                    @if(Auth::user()->type != 'client')
                                                                        <a title="Email To Client" data-id="{{$invoice->id}}" data-type="confirm" href="javascript:void(0);" class="btn bg-orange btn-sm btn-round sendEmail" data-toggle="modal"><i class="zmdi zmdi-email"></i></a>
                                                                        {{--<button Title="Copy Invoice URL"  id="{{route('payment.show', $invoice->invoice_key)}}" class="btn badge-success btn-sm btn-round copy-url"><i class="zmdi zmdi-copy"></i></button>--}}
<button Title="Copy Invoice URL"  id="{{$invoice->brandUrl}}checkout?invoicekey={{$invoice->invoice_key}}" class="btn badge-success btn-sm btn-round copy-url"><i class="zmdi zmdi-copy"></i></button>
                                                                    @endif
                                                                @endif
                                                            @endif
                                                            <a target="_blank" title="View Invoice" href="{{$invoice->brandUrl}}checkout?invoicekey={{$invoice->invoice_key}}" class="btn btn-primary btn-sm btn-round"><i class="zmdi zmdi-eye"></i></a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            @endif
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="cxm_payment">
                                            @if(count($projectPayments) == 0)
                                            <div class="text-center">
                                                <img src="{{ asset('assets/images/no-results-found.png') }}" class="img-fluid">
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
                                                            <th>Merchant</th>
                                                            <th>Amount</th>
                                                            <th class="text-center" data-breakpoints="xs md">Status</th>
                                                            <th class="text-center" data-breakpoints="xs md">Compliance<br>Varified</th>
                                                            <th class="text-center" data-breakpoints="xs md">Operation<br>Varified</th>
                                                            <th data-breakpoints="sm xs md">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($projectPayments as $payment)
                                                        <tr>
                                                            <td>{{$payment->id}}</td>
                                                            <td>{{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y')}}</td>
                                                            <td>{{$payment->invoice_id}}</td>
                                                            <td>{{$payment->payment_gateway}}</td>
                                                            <td>${{$payment->amount}}</td>
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
                                                            <td>
                                                                <a target="_blank" title="View Invoice" href="{{route('payment.show',$payment->invoice_id)}}" class="btn btn-info btn-sm btn-round"><i class="zmdi zmdi-eye"></i></a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            @endif
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="project_files">
                                            @if(count($projectFiles) == 0)
                                            <div class="text-center">
                                                <img src="{{ asset('assets/images/no-results-found.png') }}" class="img-fluid">
                                                <h5>Oops - No records were found</h5>
                                            </div>
                                            @else
                                            <div class="table-responsive">
                                                <table id="cxmFileTable" class="table table-striped table-hover js-basic-example theme-color">
                                                    <thead>
                                                        <tr>
                                                            <th>Preview</th>
                                                            <th>File Name</th>
                                                            <th>Size</th>
                                                            <th>Date</th>
                                                            @if(Auth::user()->type != 'client')<th class="text-center">Client Visibility</th>@endif
                                                            <th class="text-center" data-breakpoints="sm xs md">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($projectFiles as $file)
                                                        <tr>
                                                            <td class="align-middle">
                                                                @if($file->extension == 'png' || $file->extension == 'jpg')
                                                                    <img src="{{ asset('/uploads') }}/{{$file->filename}}" style="height:70px; width:70px; object-fit:contain; object-position:center;">
                                                                @else
                                                                    <a href="{{ asset('/uploads') }}/{{$file->filename}}">
                                                                        <div class="d-inline-block p-2 bg-grey text-uppercase font-weight-bold rounded">{{$file->extension}}</div>
                                                                    </a>
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-info">{{$file->thumbname}}</td>
                                                            <td class="align-middle"><?php echo  number_format($file->size / 1048576,2); ?> MB</td>
                                                             <!-- <td class="align-middle"><?php echo round($file->size / 1024, 2); ?> MB</td> old code  -->
                                                            <td class="align-middle">{{ \Carbon\Carbon::parse($file->created_at)->format('d/m/Y')}}</td>
                                                            @if(Auth::user()->type != 'client')
                                                            <td class="text-center align-middle">
                                                                <div class="custom-control custom-switch custom-switch-file">
                                                                    <input data-id="{{$file->id}}" type="checkbox"  class="custom-control-input toggle-class" id="customSwitch{{$file->id}}" {{ $file->visibility_client ? 'checked' : '' }}>
                                                                 <label class="custom-control-label" for="customSwitch{{$file->id}}"></label>

                                                                </div>
                                                            </td>
                                                            @endif
                                                            <td class="text-center align-middle">
                                                                <a target="_blank" title="{{$file->thumbname}}" href="{{ asset('/uploads') }}/{{$file->filename}}" class="btn btn-info btn-sm btn-round" download>
                                                                    <i class="zmdi zmdi-download"></i>
                                                                </a>
                                                                @if(Auth::user()->type != 'client')

                                                                <a data-id="{{$file->id}}" title="Delete File" href="#" class="btn btn-danger btn-sm btn-round deleteFile">
                                                                    <i class="zmdi zmdi-delete"></i>
                                                                </a>
                                                                @endif
                                                            </td>

                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            @endif
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="cxm_comments">
                                            <div class="p-2 border">
                                                <div class="chat-widget">
                                                    <ul class="cxm-chat-list list-unstyled">
                                                        <li>&nbsp;</li>
                                                        @foreach($comments as $comment)
                                                        <li class="{!! (Auth::user()->id == $comment->creatorid)?'right' :'left' !!}">
                                                            <img src="{{ asset('assets/images/xs/avatar3.jpg') }}" class="rounded-circle" alt="">
                                                            <ul class="list-unstyled chat_info">
                                                                <li><small>{{$comment->creatorName}} &nbsp; {{$comment->created_at->diffForHumans()}}</small></li>
                                                                <li><span class="message">{{$comment->comment_text}}</span></li>
                                                                {{-- comment->created_at->format('h:iA') --}}
                                                            </ul>
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                <form method="post" id="comment_form">
                                                    <input type="hidden" name="project_id" value="{{$project->id}}">
                                                    <input type="hidden" name="client_id" value="{{$project->clientId}}">
                                                    <div class="input-group mt-3">
                                                        <input name="comment" type="text" class="form-control" placeholder="Enter text here..." aria-label="Text input with dropdown button">
                                                        <div class="input-group-append">
                                                            <button type="submit" class="btn btn-info my-0"><i class="zmdi zmdi-mail-send"></i></button>
                                                        </div>
                                                    </div>
                                                </form>
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
    </div>
</section>

<!-- Cxm Detail Modal -->
<div class="modal fade" id="cxmDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Add Task</h4>
            </div>
            <form id="task_form" method="POST">
            @csrf
            <div class="modal-body">
                <div class="body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group form-float">
                                            <label for="Project">Project</label>
<?php
$projects = App\Models\Project::select('id','project_title')->get();
?>
                                            <select name="project_id" required class="form-control p-0" id="Project">
                                                <option value="">Select Project</option>
                                                <?php foreach ($projects as $key => $value): ?>
                                                <option value="{{$value->id}}" {{ Request::segment(3) == $value->id ? 'selected' : '' }}>{{$value->project_title}}</option>

                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <label for="email_address">Title</label>
                                            <input type="text" class="form-control" name="title" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <label for="email_address">Proiority</label>
                                            <select name="proiority" required class="form-control p-0">
                                                <option value="1">Low</option>
                                                <option value="2">Medium</option>
                                                <option value="3">High</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="form-group form-float">
                                            <label for="email_address">Description</label>
                                            <textarea class="form-control" name="description" required></textarea>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <label for="email_address">Assign To</label>
                                            <input type="text" class="form-control" name="assign_to" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <label for="email_address">Due Date</label>
                                            <input type="date" class="form-control" name="due_date" required>
                                        </div>
                                    </div>

                                </div>
                                <!-- <div class="row">
                                    <div class="col-md-6">
                                        <button class="btn btn-warning btn-round" type="submit">SUBMIT</button>
                                    </div>

                                </div> -->
                        </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success btn-round" type="submit">Create Task</button>
                <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
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
                <h4 class="title" id="defaultModalLabel">Create New Project Invoice</h4>
            </div>
            <form method="POST" id="create-project-invoice">
                <input type="hidden" name="client_id" value="{{$project->clientId}}">
                <input type="hidden" name="project_id" value="{{$project->id}}">

                <div class="modal-body">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <select id="brand_key" name="brand_key" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Brand" data-live-search="true" required>
                                @foreach($teamBrand as $brand)
                                    <option value="{{$brand->brandKey}}" data-cxm-team-key="{{ $brand->team_key }}">{{$brand->brandName}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <select id="agent_id" name="agent_id" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Agent" data-live-search="true" required>
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
                            <textarea id="invoice_description" class="form-control" placeholder="Description &amp; Details" name="description"></textarea>
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
                <h4 class="title" id="defaultModalLabel">Create New Payment</h4>
            </div>
            <form method="POST" id="create-project-payment">
                <input type="hidden" name="client_id" value="{{$project->clientId}}">
                <input type="hidden" name="project_id" value="{{$project->id}}">

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
                        <div class="form-group">
                            <textarea id="invoice_description" class="form-control" placeholder="Description &amp; Details" name="description"></textarea>
                        </div>
                        <div class="form-group">
                            <select id="type" name="sales_type" class="form-control show-tick ms select2" data-placeholder="Select Type" required>
                                <option>Select Sales Type</option>
                                <option value="Fresh">Fresh</option>
                                <option value="Upsale">Upsale</option>
                                <option value="Recurring">Recurring</option>
                            </select>
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
                                <input type="date" id="due_date" class="form-control" placeholder="Payment Date" name="due_date" required  />
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

<!-- Cxm File Modal -->
<link rel="stylesheet" href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}">
<div class="modal fade" id="cxmFileModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Upload Project File</h4>
            </div>
            <form id="upload-project-file" method="POST" enctype="multipart/form-data">

                <input type="hidden" name="client_id" value="{{$project->clientId}}">
                <input type="hidden" name="project_id" value="{{$project->id}}">
                <input type="hidden" name="brand_key" value="{{$project->brand_key}}">
                @if(Auth::user()->type == 'client')
                <input type="hidden" name="fileresource_type" value="client">
                @else
                <input type="hidden" name="fileresource_type" value="project">
                @endif

                <div class="modal-body">
                    <div class="form-group">
                        <input id="abc_file" type="file" name="upload_file" class="dropify" data-allowed-file-extensions="jpg png pdf xlsx pptx docx" data-max-file-size="5M">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="addStatusBtn" class="btn btn-success btn-round">Upload File</button>
                    <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cxm Comment Modal -->
<div class="modal fade" id="cxmCommentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">New Comment</h4>
            </div>
            <div class="modal-body">
                Lorem ipsum, dolor sit amet consectetur adipisicing elit. Fugiat esse vero enim harum alias itaque quod, soluta mollitia! Reiciendis aliquid quidem rem alias quibusdam? Maxime ipsa veniam beatae excepturi nostrum?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-round">Create Comment</button>
                <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
            </div>
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
                            <select id="sales_type" name="sales_type" class="form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Sale Type" required>
                                <option value="Fresh">Fresh</option>
                                <option value="Upsale">Upsale</option>
                                <option value="Recurring">Recurring</option>
                            </select>
                        </div>

                        <!--  -->
                        <div class="form-group">
                            <select name="edit_cur_symbol" class="form-control" id="edit_cur_symbol">
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
                                <input type="number" id="edit_amount" class="form-control" placeholder="Amount" max='2500' name="value" required />
                            </div>
                        </div>
                        <div class="custom-control custom-switch mb-2">
                            <input type="checkbox" class="custom-control-input toggle-class" id="edit_taxable" name="edit_taxable" value="1" checked>
                            <label class="custom-control-label" for="edit_taxable">Taxable?</label>
                        </div>
                        <div class="form-group" id="edit_taxField">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">%</i></span>
                                </div>
                                <input type="hidden" id="edit_tax_amount" class="form-control" name="edit_taxAmount" value="0">
                                <input type="number" name="edit_tax" id="edit_tax" class="form-control" placeholder="Tax" />
                            </div>
                        </div>
                        <div class="form-group" id="edit_totalAmount">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text cxm-currency-symbol-icon"><i class="zmdi zmdi-money"></i></span>
                                </div>
                                <input type="text" name="edit_total_amount" class="form-control" placeholder="Total Amount" id="edit_total_amount" value="0" disabled>
                            </div>
                        </div>
                        <!--  -->
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

@endsection

@push('cxmScripts')
    <script src="{{ asset('assets/plugins/dropify/js/dropify.min.js') }}"></script>
    @include('project.script')
    @include('includes.currency-change')
    <script>
        $('.nav-tabs .nav-item .nav-link').on('click', function(){
            let cxmModalActive = $(this).attr('data-cxm-modal');
            $('.cxm-btn-create').attr('data-target', '#cxm'+$.trim(cxmModalActive)+'Modal');

            @if(Auth::user()->type == 'client')
            if(cxmModalActive == 'File'){
                $('.cxm-btn-create').removeClass('d-none');

            }else{
                $('.cxm-btn-create').addClass('d-none');

            }
            @endif
        });

        $('.dropify').dropify();

        setInterval(function(){
            cxmRefreshChat();
            document.querySelector(".cxm-chat-list").scrollIntoView(false);
        }, 5000);
    $('#task_form').on('submit', function(e){
     e.preventDefault();
    $('.page-loader-wrapper').css({'display':'block', 'background':'rgba(238, 238, 238, 0.7)'});
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        //console.log($(this).serialize());
        $.ajax({
            url: "{{ route('task.store') }}",
            datatype: 'json',
            method:'POST',
            data: $(this).serialize(), // get all form field value in serialize form
            success: function(data){
                $("#task_form")[0].reset();
                $('.page-loader-wrapper').css('display', 'none');
                swal("Good job!", "User Info API successfully Created!", "success");
                $('#cxmDetailModal').modal('hide');
            },
            error: function(){
            $('.page-loader-wrapper').css('display', 'none');
            swal("Error!", "Request Fail!", "error");
            }
        });
});
    </script>
@endpush
