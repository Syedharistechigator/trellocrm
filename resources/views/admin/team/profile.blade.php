@extends('admin.layouts.app')@section('cxmTitle', 'Profile')

@section('content')
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Profile</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                            <li class="breadcrumb-item">Pages</li> <li class="breadcrumb-item active">Profile</li>
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
                    <div class="col-lg-4 col-md-12">
                        <div class="card mcard_3">
                            <div class="body">
                                <a href="javascript:void(0)">
                                    {{--                                    <img src="{{$member->image && file_exists(public_path('assets/images/profile_images/'). $member->image) ? asset('assets/images/profile_images/'.$member->image) : asset('assets/images/profile_av.jpg')}}" class="img-thumbnail rounded-circle shadow" id="profile-image" alt="{{$member->name}}" style="height:200px; width:200px; object-fit:cover;cursor: pointer;">--}}

                                    @if(filter_var($member->image, FILTER_VALIDATE_URL))
                                        <object data="{!! $member->image !!}" height="200px" width="200px" class="img-thumbnail rounded-circle shadow">
                                            <img class="img-thumbnail rounded-circle shadow" src="{{$member->image}}" alt="{{$member->name}}" style="height:70px; width:70px; object-fit:cover;" id="profile-image" loading="lazy">
                                        </object>
                                    @else
                                        @if($member->image && file_exists(public_path('assets/images/profile_images/') . $member->image))
                                            <img class="img-thumbnail rounded-circle shadow" src="{{ asset('assets/images/profile_images/' . $member->image) }}" alt="{{$member->name}}" style="height:200px; width:200px; object-fit:cover;cursor: pointer;" id="profile-image" loading="lazy">
                                        @else
                                            <img class="img-thumbnail rounded-circle shadow" src="{{ asset('assets/images/xs/avatar1.jpg') }}" alt="{{$member->name}}" style="height:200px; width:200px; object-fit:cover;cursor: pointer;" id="profile-image" loading="lazy">
                                        @endif
                                    @endif
                                </a>
                                <h4 class="m-t-10 text-warning mb-0">
                                <span class="position-relative">
                                    @if($member->type == 'lead')
                                        <img class="crown_profile" src="{{ asset('assets/images/crown.png') }}" style="top:-20px; left:-20px;">
                                    @endif
                                    {{$member->name}}
                                </span>
                                </h4>
                                <h5>{{$member->pseudo_name}}</h5>
                                <div class="row">
                                    <div class="col-6">
                                        <small>Designation</small>
                                        <p class="text-muted text-capitalize">{{$member->designation}}</p>
                                    </div>
                                    <div class="col-6">
                                        <small>Position</small>
                                        <p class="text-muted text-capitalize">{{$member->type}}</p>
                                    </div>
                                </div>
                                <hr class="mt-0">
                                <div class="row">
                                    <div class="col-6">
                                        <small>Target</small>
                                        <p class="text-muted text-capitalize">${{$member->target}}</p>
                                    </div>
                                    <div class="col-6">
                                        <small>Achived</small>
                                        <p class="text-muted text-capitalize">
                                            ${{$member->achived_amount}}
                                            {!! ($member->achived_amount > $member->target)?'<i class="zmdi zmdi-trending-up text-success"></i>' :'<i class="zmdi zmdi-trending-down text-warning"></i>' !!}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="body">
                                <small class="text-muted">Email Address: </small>
                                <p>{{$member->email}}</p>
                                <small class="text-muted">Pseudo Email Address: </small>
                                <p>{{$member->pseudo_email}}</p>
                                <hr>
                                <small class="text-muted">Phone: </small>
                                <p>{{$member->phone}}</p>
                                <hr>
                                <small class="text-muted">Status: </small>
                                <div class="custom-control custom-switch custom-switch-member">
                                    <input data-id="{{$member->id}}" type="checkbox" class="custom-control-input toggle-class" id="customSwitch{{$member->id}}" {{ $member->status ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="customSwitch{{$member->id}}"></label>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="body">
                                <div class="header px-3 pb-0">
                                    <h2><strong>Assigned Brand's &nbsp;</strong> Emails</h2>
                                </div>
                                <div class="body" style="max-height: 400px; overflow-y: auto; overflow-x: hidden;">
                                    <div class="row">
                                        @foreach($email_configurations as $brand_name => $emailconfigurations)
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <strong>{!! $brand_name !!}</strong>
                                                </div>
                                            </div>
                                            @foreach($emailconfigurations as $email_configuration)
                                                <div class="col-md-12" style=" margin: 0px 0px 0px 20px;">
                                                    <div class="form-group">
                                                        <div class="checkbox">
                                                            <input id="checkbox_{{$email_configuration->id}}" class="email-checkbox" type="checkbox" value="{{$email_configuration->id}}" name="email" {{$email_configuration->id}}  data-id="{{$member->id}}" @if(in_array($email_configuration->id, $email_configuration_ids)) checked @endif>
                                                            <label for="checkbox_{{$email_configuration->id}}">{{$email_configuration->email}}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="body mb-4">
                                    <div id="chart-bar" class="c3_chart"></div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="body">
                                <ul class="nav nav-tabs p-0 mb-3 nav-tabs-warning justify-content-center" role="tablist">
                                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#cxm_tb1"><i class="zmdi zmdi-group"></i> Projects</a></li>
                                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#cxm_tb2"><i class="zmdi zmdi-print"></i> Invoices</a></li>
                                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#cxm_tb3"><i class="zmdi zmdi-money"></i> Payments</a></li>
                                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#cxm_tb4"><i class="zmdi zmdi-refresh-sync-alert"></i> Charge Back</a></li>
                                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#cxm_tb5"><i class="zmdi zmdi-hc-fw"></i> Expense</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane in active" id="cxm_tb1">
                                        <div class="card mb-0">
                                            @if(count($projectsData) == 0)
                                                <div class="text-center">
                                                    <img src="{{ asset('assets/images/no-results-found.png') }}" class="img-fluid" style="width: 150px;">
                                                    <h5>Oops - No records were found</h5>
                                                </div>
                                            @else
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-hover theme-color mb-0" data-sorting="false">
                                                        <thead>
                                                        <tr>
                                                            <th>Title</th>
                                                            <th>Brand</th>
                                                            <th>Account Manager</th>
                                                            <th>Start Date</th>
                                                            <th>Due Date</th>
                                                            <th class="hidden-md-down">Status</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($projectsData as $project)
                                                            <tr>
                                                                <td class="align-middle">
                                                                    <a class="text-warning" href="{{route('adminproject.show', $project->id)}}"><i class="zmdi zmdi-open-in-new"></i>
                                                                        <strong>{{$project->project_title}}</strong></a>
                                                                    <div>
                                                                        <small>Cost: ${{$project->project_cost}}</small>
                                                                    </div>
                                                                </td>
                                                                <td class="align-middle">{{$project->brandName}}</td>
                                                                <td class="align-middle">
                                                                    <div class="form-row">
                                                                        <div class="col-auto">
                                                                            <img class="img-thumbnail rounded-circle" style="width:50px; height:50px;" src="{!! $project->pmImage?$project->pmImage :asset('assets/images/crown.png') !!}" alt="{{$project->pmName}}">
                                                                        </div>
                                                                        <div class="col">
                                                                            <div class="text-warning">{{$project->pmName}}</div>
                                                                            <small>{{$project->pmDesignation}}</small>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="align-middle">{{$project->project_date_start}}</td>
                                                                <td class="align-middle">{{$project->project_date_due}}</td>
                                                                <td class="align-middle">
                                                                    <span class="badge badge-{{$project->statusColor}} rounded-pill">{{$project->status}}</span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="cxm_tb2">
                                        <div class="card mb-0">
                                            @if(count($invoiceData) == 0)
                                                <div class="text-center">
                                                    <img src="{{ asset('assets/images/no-results-found.png') }}" class="img-fluid" style="width: 150px;">
                                                    <h5>Oops - No records were found</h5>
                                                </div>
                                            @else
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-hover theme-color mb-0" data-sorting="false">
                                                        <thead>
                                                        <tr>
                                                            <th>ID #</th>
                                                            <th>Date</th>
                                                            <th>Name</th>
                                                            <th>Amount</th>
                                                            <th>Sales Type</th>
                                                            <th data-breakpoints="sm xs">Due Date</th>
                                                            <th data-breakpoints="xs md">Status</th>
                                                            <th data-breakpoints="sm xs md">Action</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($invoiceData as $invoice)
                                                            <tr>
                                                                <td class="align-middle">{{$invoice->invoice_num}}</td>
                                                                <td class="align-middle">{{$invoice->created_at->format('j F, Y')}}</td>
                                                                <td class="align-middle">
                                                                    <a class="text-warning" href="#">{{$invoice->clientName}}</a>
                                                                </td>
                                                                <td class="align-middle">${{$invoice->final_amount}}</td>
                                                                <td class="align-middle">{{$invoice->sales_type}}</td>
                                                                <td class="align-middle">
                                                                        <?php
                                                                        $now = \Carbon\Carbon::now();

                                                                        if ($invoice->due_date >= $now) {
                                                                            $color = 'success';
                                                                        } else {
                                                                            $color = 'danger';
                                                                        }
                                                                        ?>
                                                                    <span class="badge badge-{{$color}} rounded-pill xtext-{{$color}}">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y')}}</span>
                                                                </td>
                                                                <td class="align-middle">
                                                                    @if($invoice->status == 'draft')
                                                                        <span class="badge bg-grey rounded-pill">Draft</span>
                                                                    @elseif($invoice->status == 'due')
                                                                        <span class="badge bg-amber rounded-pill">Due</span>
                                                                    @elseif($invoice->status == 'refund')
                                                                        <span class="badge bg-pink rounded-pill">Refund</span>
                                                                    @elseif($invoice->status == 'chargeback')
                                                                        <span class="badge bg-red rounded-pill">Charge Back</span>
                                                                    @else
                                                                        <span class="badge badge-success rounded-pill">Paid</span>
                                                                    @endif
                                                                </td>
                                                                <td class="align-middle">
                                                                    <a title="View Invoice" href="{{route('payment.show',$invoice->invoice_key)}}" class="btn btn-warning btn-sm btn-round"><i class="zmdi zmdi-open-in-new"></i></a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="cxm_tb3">
                                        <div class="card mb-0">
                                            @if(count($paymentData) == 0)
                                                <div class="text-center">
                                                    <img src="{{ asset('assets/images/no-results-found.png') }}" class="img-fluid" style="width: 150px;">
                                                    <h5>Oops - No records were found</h5>
                                                </div>
                                            @else
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-hover theme-color mb-0" data-sorting="false">
                                                        <thead>
                                                        <tr>
                                                            <th>ID #</th>
                                                            <th>Brand</th>
                                                            <th>Client</th>
                                                            <th>Amount</th>
                                                            <th>Transaction ID</th>
                                                            <th data-breakpoints="sm xs">Payment Date</th>
                                                            <th class="text-center" data-breakpoints="xs md">Status</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($paymentData as $payment)
                                                            <tr>
                                                                <td class="align-middle">{{$payment->id}}</td>
                                                                <td class="align-middle">{{$payment->brandName}}</td>
                                                                <td class="align-middle">
                                                                    <a class="text-warning" href="#">{{$payment->name}}</a>
                                                                </td>
                                                                <td class="align-middle">${{$payment->amount}}</td>
                                                                <td class="align-middle">{{$payment->authorizenet_transaction_id}}</td>
                                                                <td class="align-middle">{{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y')}}</td>
                                                                <td class="text-center align-middle">
                                                                    @if($payment->payment_status == 1)
                                                                        <span class="badge badge-success rounded-pill">Success</span>
                                                                    @elseif($payment->payment_status == 2)
                                                                        <span class="badge badge-warning rounded-pill">Refund</span>
                                                                    @else
                                                                        <span class="badge badge-danger rounded-pill">Charge Back</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="cxm_tb4">
                                        <div class="card mb-0">
                                            @if(count($refunds) == 0)
                                                <div class="text-center">
                                                    <img src="{{ asset('assets/images/no-results-found.png') }}" class="img-fluid" style="width: 150px;">
                                                    <h5>Oops - No records were found</h5>
                                                </div>
                                            @else
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-hover theme-color mb-0" data-sorting="false">
                                                        <thead>
                                                        <tr>
                                                            <th>ID #</th>
                                                            <th>Date</th>
                                                            <th data-breakpoints="sm xs">Invoice Id</th>
                                                            <th>payment Id</th>
                                                            <th>Transaction ID</th>
                                                            <th>Type</th>
                                                            <th>Amount</th>
                                                            <th class="text-center" data-breakpoints="xs md">Status</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($refunds as $refund)
                                                            <tr>
                                                                <td>{{$refund->id}}</td>
                                                                <td>{{ \Carbon\Carbon::parse($refund->created_at)->format('d/m/Y')}}</td>
                                                                <td>{{$refund->invoice_id}}</td>
                                                                <td>{{$refund->payment_id}}</td>
                                                                <td>{{$refund->authorizenet_transaction_id}}</td>
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
                                            @endif
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="cxm_tb5">
                                        <div class="card mb-0">
                                            @if(count($expenses) == 0)
                                                <div class="text-center">
                                                    <img src="{{ asset('assets/images/no-results-found.png') }}" class="img-fluid" style="width: 150px;">
                                                    <h5>Oops - No records were found</h5>
                                                </div>
                                            @else
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-hover theme-color mb-0" data-sorting="false">
                                                        <thead>
                                                        <tr>
                                                            <th>ID #</th>
                                                            <th>Date</th>
                                                            <th>Title & Description</th>
                                                            <th>Amount</th>
                                                            <th class="text-center" data-breakpoints="xs md">Status</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($expenses as $expense)
                                                            <tr>
                                                                <td class="align-middle">{{$expense->id}}</td>
                                                                <td class="align-middle">{{$expense->created_at->format('j F, Y')}}</td>
                                                                <td class="align-middle">{{$expense->title}}
                                                                    <br>{{$expense->description}}</td>
                                                                <td class="align-middle">${{$expense->amount}}</td>
                                                                <td class="text-center align-middle">
                                                                    {!! ($expense->status == 1)?'<span class="zmdi zmdi-check-circle text-success"></span>' :'<span class="zmdi zmdi-close-circle text-danger"></span>'; !!}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
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
@endsection

@push('cxmScripts')
    @include('admin.team.script')
@endpush
