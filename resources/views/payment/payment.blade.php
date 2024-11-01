@extends('layouts.app')@section('cxmTitle', 'Payments')

@section('content')

	<section class="content">
		<div class="body_scroll">
			<div class="block-header">
				<div class="row">
					<div class="col-lg-7 col-md-6 col-sm-12">
						<h2>Payments</h2>
						<ul class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="zmdi zmdi-home"></i>
									{{ config('app.home_name') }}</a></li> <li class="breadcrumb-item">Sales</li>
							<li class="breadcrumb-item active"> Payments</li>
						</ul>
						<button class="btn btn-primary btn-icon mobile_menu" type="button">
							<i class="zmdi zmdi-sort-amount-desc"></i></button>
					</div>
					<div class="col-lg-5 col-md-6 col-sm-12 text-right">
						@if(Auth::user()->type == 'lead' && str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false)
							{{--                         @if(Auth::user()->type == 'lead' or Auth::user()->type == 'staff')--}}
							{{--                        @if(FALSE)--}}
							<button class="btn btn-success btn-icon rounded-circle" type="button" data-toggle="modal" data-target="#paymentModal">
								<i class="zmdi zmdi-plus"></i></button>
						@endif
						@include('includes.cxm-top-right-toggle-btn')
					</div>
				</div>
			</div>
			{{--        <div class="row clearfix">--}}
			{{--            <div class="col-lg-12 col-md-12 col-sm-12">--}}
			{{--                <div class="card">--}}
			{{--                    <div class="body" style="height: 80px;">--}}
			{{--                        <div class="row clearfix justify-content-between">--}}
			{{--                            <div class="col-lg-4 col-md-6">--}}

			{{--                            </div>--}}
			{{--                            <div class="col-lg-4 col-md-6">--}}

			{{--                            </div>--}}
			{{--                            <div class="col-lg-3 col-md-6">--}}
			{{--                                <div class="card">--}}
			{{--                                    <div class="search">--}}
			{{--                                            <form class="input-group mb-0" action="{{ route('searchPayment') }}" method="GET">--}}
			{{--                                            @csrf--}}
			{{--                                                <input type="text" class="form-control"  name="searchText" required placeholder="Search...">--}}
			{{--                                                <div class="input-group-append">--}}
			{{--                                                    <button type="submit" class="btn btn-raised btn-info waves-effect" id="basic-addon2">--}}
			{{--                                                        <i class="zmdi zmdi-search"></i>--}}
			{{--                                                    </button>--}}
			{{--                                                </div>--}}
			{{--                                            </form>--}}
			{{--                                        </div>--}}
			{{--                                </div>--}}
			{{--                            </div>--}}
			{{--                        </div>--}}
			{{--                    </div>--}}
			{{--                </div>--}}
			{{--            </div>--}}
			{{--        </div>--}}
			<div class="container-fluid">
				<div class="row clearfix">
					<div class="col-lg-12">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12">
								<div class="card">
									<div class="body">
										<form id="searchForm">
											@csrf
											<div class="form-row">
												<div class="col-md-6">
													<label>Brands</label>
													<select data-placeholder="Select" id="brand" name="brandKey" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" data-live-search="true">
														<option value='0' data-brand="0">All</option>
														@foreach($assign_brands as $assign_brand)
															<option value="{{$assign_brand->brand_key}}" {{$brandKey == $assign_brand->brand_key ? "selected" : "" }} data-brand="{{$assign_brand->brand_key}}">{{$assign_brand->getBrandNameWithTrashed->name}}</option>
														@endforeach
													</select>
												</div>
												<div class="col-lg-4 col-md-6">
													<label>Select Date Range</label>
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
						<div class="">
							<div class="table-responsive">
								<table id="PaymentTable" class="table table-striped table-hover xjs-basic-example theme-color">
									<thead>
									<tr>
										<th>ID#</th>
										<th class='text-nowrap'>Invoice#</th>
                                        @if(auth()->user()->type === 'ppc')
										<th class='text-nowrap'>Team</th>
                                        @endif
										<th class='text-nowrap'>Brand</th>
										<th class='text-nowrap'>Agent</th>
										<th class='text-nowrap'>Client</th>
										<th class='text-nowrap'>Sales Type</th>
										<th class='text-nowrap'>Amount</th>
										<th class='text-nowrap' data-breakpoints="xs md">Payment Date</th>
										<th class='text-nowrap'>Payment Gateway</th>
                                        @if(auth()->user()->type === 'ppc')
										<th class='text-nowrap'>Card Last 4</th>
                                        @endif
										<th class='text-nowrap'>Transction ID</th>
										<th class="text-nowrap text-center" data-breakpoints="xs md">Status</th>
										<th class="text-center" data-breakpoints="xs md">Compliance Verified</th>
										<th class="text-center" data-breakpoints="xs md">Operation Verified</th>
										<th class="text-nowrap text-center">Action</th>
									</tr>
									</thead>
									<tbody>
									@foreach($payments as $payment)
										<tr id="tr-{{$payment->id}}">
											<td class="align-middle">{{$payment->id}}</td>
											<td class="align-middle text-nowrap">
												<a class="text-warning" href="javascript:void(0);">{{isset($payment->getInvoice)?$payment->getInvoice->invoice_num : ""}}</a>
												<div class="">
													<span class="badge badge-info rounded-pill">{{ $payment->invoice_id}}</span>
												</div>
											</td>
                                            @if(auth()->user()->type === 'ppc')
                                            <td class="align-middle" class="text-info">{{optional($payment->getTeamName)->name}}</td>
                                            @endif
											<td class="align-middle" class="text-info">{{optional($payment->getBrandName)->name}}</td>
											<td class="align-middle">{{$payment->getAgentName->name}}</td>
											<td class="text-info align-middle text-nowrap">
												<a class="text-info text-nowrap" href="{{route('client.show',$payment->clientid)}}">
													{{$payment->name}}<br>{{$payment->email}}</a>
											</td>
											<td class="align-middle text-nowrap">{{$payment->sales_type}}</td>
											<td class="align-middle text-nowrap">${{$payment->amount}}</td>
											<td class="align-middle text-nowrap">{{$payment->created_at->format('j F, Y')}}
												<br>{{$payment->created_at->format('h:i:s A')}}
												<br>{{$payment->created_at->diffForHumans()}}
											</td>
											<td class="align-middle text-nowrap text-capitalize">{{$payment->payment_gateway}}
												@if($payment->payment_gateway === 'authorize' && isset($payment->getAuthorizeMerchant->merchant))
													<br>
													<p style="font-size:12px ">( {{ $payment->getAuthorizeMerchant->merchant}} )</p>
												@elseif($payment->payment_gateway === 'Expigate' && isset($payment->getExpigateMerchant->merchant))
													<br>
													<p style="font-size:12px ">( {{ $payment->getExpigateMerchant->merchant}} )</p>
												@endif
											</td>
                                            @if(auth()->user()->type === 'ppc')
											<td class="align-middle text-nowrap">{{$payment->card_number}}</td>
											@endif
											<td class="align-middle text-nowrap">{{$payment->authorizenet_transaction_id}}</td>
											<td class="text-center align-middle text-nowrap">
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
											<td class="text-center align-middle text-nowrap">
												@if(Auth::user()->type == 'qa')
													<button data-id="{{$payment->id}}" title="Refund" type="button" class="btn btn-warning btn-sm btn-round cxm-btn-refund">
														<i class="zmdi zmdi-replay"></i></button>
												@else
													<a title="View Payment Details" href="{{route('showPaymentDetail',$payment->id)}}" class="btn btn-info btn-sm btn-round"><i class="zmdi zmdi-eye"></i></a>
													@if(Auth::user()->type == 'lead' && str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false && $payment->actor_id > 0  && optional($payment->actor)->id == Auth::id())
														<a title="Delete Payment" data-id="{{$payment->id}}" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton">
															<i class="zmdi zmdi-delete"></i> </a>
													@endif

													{{--                                                    <a title="View Invoice"--}}
													{{--                                                       href="{{route('payment.show',$payment->invoice_id)}}"--}}
													{{--                                                       class="btn btn-warning btn-sm btn-round" target="_blank"><i--}}
													{{--                                                            class="zmdi zmdi-file-text"></i></a>--}}
												@endif
											</td>
										</tr>
									@endforeach
									</tbody>
								</table>
							</div>
							{{--            {{ $payments->links() }}--}}
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Cxm Refund Modal -->
	<div class="modal fade" id="cxmRefundModal" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="title">Refund Request</h4>
				</div>
				<form method="post" id="refund_form">
					<input type="hidden" id="team_key" name="team_key" value="">
					<input type="hidden" id="brand_key" name="brand_key" value="">
					<input type="hidden" id="agent_id" name="agent_id" value="">
					<input type="hidden" id="invoice_id" name="invoice_id" value="">
					<input type="hidden" id="client_id" name="client_id" value="">
					<input type="hidden" id="payment_id" name="payment_id" value="">
					<input type="hidden" id="auth_transaction_id" name="auth_transaction_id" value="">
					<input type="hidden" id="cxm_card" name="cxm_card" value="">
					<div class="modal-body">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="zmdi zmdi-money"></i></span>
								</div>
								<input id="amount" name="amount" type="number" class="form-control" placeholder="Amount" required/>
							</div>
						</div>
						<div class="form-group">
							<textarea id="reason" class="form-control" placeholder="Enter Reason" name="reason"></textarea>
						</div>
						<div class="form-group">
							<select id="type" name="type" class="form-control" data-placeholder="Select Type" required>
								<option>Select Type</option>
								<option value="refund">Refund</option>
								<option value="chargeback">Charge Back</option>
							</select>
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-success btn-round">Send</button>
						<button type="button" class="btn btn-danger btn-round" data-dismiss="modal">Close</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	@if(Auth::user()->type == 'lead' && str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false)
		<!-- Create Direct Payment -->
		<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="title" id="defaultModalLabel">Create New Payment</h4>
					</div>
					<form method="POST" id="direct-payment-Form">
						<div class="modal-body">
							<div class="col-sm-12">
								<div id="" class="form-group">
									<label for="payment_brand_key">Select Brand Name</label>
									<select id="brand_key" name="brand_key" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Brand" data-live-search="true" required>
										<option>Select Brand</option>
										@foreach($assign_brands as $assign_brand)
											<option value="{{$assign_brand->brand_key}}">{{$assign_brand->getBrandName->name}}</option>
										@endforeach
									</select>
								</div>
								<div id="" class="form-group">
									<label for="payment_agent_id">Select Agent Name</label>
									<select id="agent_id" name="agent_id" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Agent" data-live-search="true" required>
										<option>Select Sales Agent</option>
										@foreach($members as $member)
											<option value="{{$member->id}}">{{$member->name}}</option>
										@endforeach
									</select>
								</div>
								<div class="form-group">
									<label for="sales_type">Select Sale Type</label>
									<select id="type" name="sales_type" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Sales Type" data-live-search="true" required>
										<option>Select Sales Type</option>
										<option value="Fresh">Fresh</option>
										<option value="Upsale">Upsale</option>
										{{--<option value="Recurring">Recurring</option>--}}
									</select>
								</div>
								<div class="form-group">
									<label for="name">Enter Client Name</label>
									<input type="text" id="name" class="form-control" placeholder="Name" name="name" autocomplete="name">
								</div>
								<div class="form-group">
									<label for="email">Enter Client Email</label>
									<input type="email" id="email" class="form-control" placeholder="Email" name="email" autocomplete="email">
								</div>
								<div class="form-group">
									<label for="phone">Enter Client Phone Number</label>
									<input type="text" id="phone" class="form-control" placeholder="Phone" name="phone" autocomplete="phone">
								</div>
								<div class="form-group" id="projectTileBlock">
									<label for="projectTitle">Project Title</label>
									<input type="text" id="projectTitle" class="form-control" placeholder="Project Title" name="project_title"/>
								</div>
								<div class="form-group">
									<label for="invoice_description">Enter Invoice Description</label>
									<textarea id="invoice_description" class="form-control" placeholder="Description & Details" name="description"></textarea>
								</div>
								<div class="form-group">
									<label for="due_date">Enter Payment Date</label>
									<input type="date" id="due_date" class="form-control" placeholder="Due Date" name="due_date" required/>
								</div>
								<div class="form-group">
									<label for="amount">Enter Amount</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="zmdi zmdi-money"></i></span>
										</div>
										<input type="number" id="amount" class="form-control" placeholder="Amount" name="value" required autocomplete="value"/>
									</div>
								</div>
								<div class="form-group">
									<label for="merchant">Select Merchant</label>
									<select id="merchant" name="merchant" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Merchant Type" data-live-search="true" required>
										<option>Select Merchant</option>
										<option value="authorize">Authorize</option>
										<option value="Expigate">Expigate</option>
										<option value="PayArc">PayArc</option>
										<option value="Zelle Pay">Zelle Pay</option>
										<option value="PayPal">PayPal</option>
										<option value="Venmo">Venmo</option>
										<option value="Cash App">Cash App</option>
										<option value="Wire Transfer">Wire Transfer</option>
									</select>
								</div>
								<div class="form-group" id="projectTileBlock">
									<label for="trackId">Enter Payment Tracking Id</label>
									<input type="text" id="trackId" class="form-control" placeholder="Payment Tracking Id" name="track_id" required/>
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
	@endif
@endsection

@push('cxmScripts')
	<script src="{{ asset('assets/plugins/momentjs/moment.js') }}"></script>
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

	@include('payment.script')

	<script>
        @if($errors->any())
            createToast('error',`{{$errors->first()}}`);
        @endif
        function getParam() {
            window.location.href = "{{ route('user.payments.index') }}?brandKey=" + encodeURIComponent($('#brand').val()) + "&dateRange=" + encodeURIComponent($('#date-range').val());
        }

        $(function () {
            $(document).ready(function () {
                $('#PaymentTable').DataTable().destroy();
                $('#PaymentTable').DataTable({
                    dom: 'lBfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    order: [[0, 'desc']]
                });
                var dateRangePicker = $(".cxm-date-range-picker");
                var initialStartDate = moment("{{ $fromDate }}", 'YYYY-MM-DD');
                var initialEndDate = moment("{{ $toDate }}", 'YYYY-MM-DD');
                var initialDateRange = initialStartDate.format('YYYY-MM-DD') + ' - ' + initialEndDate.format('YYYY-MM-DD');
                dateRangePicker.daterangepicker({
                    opens: "left",
                    locale: {
                        format: 'YYYY-MM-DD'
                    },
                    ranges: {
                        'Last 245 Days': [moment().subtract(244, 'days'), moment()],
                        'Last 3 Years': [moment().subtract(3, 'years').add(1, 'day'), moment()]
                    },
                    startDate: initialStartDate, // Set the initial start date
                    endDate: initialEndDate,     // Set the initial end date
                });
                dateRangePicker.on('apply.daterangepicker', getParam);
                dateRangePicker.val(initialDateRange);

                $('#brand').on('change', getParam);
            });
        });
	</script>

@endpush
