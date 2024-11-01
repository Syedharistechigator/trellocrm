@extends('layouts.app')@section('cxmTitle', 'Leads')

@section('content')
	<section class="content">
		<div class="body_scroll">
			<div class="block-header">
				<div class="row">
					<div class="col-lg-7 col-md-6 col-sm-12">
						<h2>{{$leadType == 1 ? 'My':'All'}} Lead List</h2>
						<ul class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="zmdi zmdi-home"></i>
									{{ config('app.home_name') }}</a></li>
							<li class="breadcrumb-item">{{$leadType == 1 ? 'My':'All'}} Leads</li>
							<li class="breadcrumb-item active"> List</li>
						</ul>
						<button class="btn btn-primary btn-icon mobile_menu" type="button">
							<i class="zmdi zmdi-sort-amount-desc"></i></button>
					</div>
					<div class="col-lg-5 col-md-6 col-sm-12 text-right">
						@if(Auth::user()->type != 'ppc' && Auth::user()->type != 'qa')
							<button class="btn btn-success btn-icon rounded-circle" type="button" id="mylead" data-id="my-leads" style="background-color:{{$leadType == 'my-leads' ? '#2bab41':''}}" title="My Leads">
								<i class="zmdi zmdi-account-o"></i></button>
							<button class="btn btn-success btn-icon rounded-circle" type="button" id="alllead" data-id="all-leads" style="background-color:{{$leadType == 'all-leads' ? '#2bab41':''}}" title="All Leads">
								<i class="zmdi zmdi-border-all"></i></button>
						@endif
						<button class="btn btn-success btn-icon rounded-circle" type="button" data-toggle="modal" id="create-lead-show-modal" data-target="#create-lead-modal">
							<i class="zmdi zmdi-plus"></i></button>
						@include('includes.cxm-top-right-toggle-btn')
					</div>
				</div>
			</div>
			<div class="container-fluid">
				<div class="row clearfix">
					<div class="col-lg-12">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12">
								<div class="card">
									<div class="body">
										<form id="searchForm">
											<input type="hidden" name="lead_button_id" id="lead_type" value="{{$leadType}}">
											<div class="form-row">
												<div class="col-md-6">
													<label>Brands</label>
                                                    <select id="brand" name="brandKey" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Brand" data-live-search="true" required>
                                                        <option value='0' data-brand="0" {{$brandKey == 0 ? "selected" : "" }} >All</option>
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
								<table id="LeadTable" class="table table-striped table-hover xjs-basic-example theme-color">
									<thead>
									<tr>
										<th>ID#</th>
										<th>Title</th>
										<th>Contact</th>
										<th>Assigned</th>
										<th>Date</th>
										<th>Brand</th>
										<th>Value</th>
										<th class="text-center">Status</th>
										<th>Action</th>
									</tr>
									</thead>
									<tbody>
									@foreach($leads as $lead)
										<tr>
											<td class="align-middle">{{$lead->id}}</td>
											<td class="align-middle">
												<a class="text-info" href="{{route('leadshow',$lead->id)}}"><span class="zmdi zmdi-open-in-new"></span> {{$lead->title}}
												</a></td>
											<td class="align-middle">
												{{$lead->name}}<br>
												{{$lead->email}}<br>
												{{$lead->phone}}<br>
												{{$lead->lead_ip}}<br>
											</td>
											<td class="align-middle">{{ $lead->getAgentNames->implode('name', ', ') ?? '---' }}</td>
											<td class="align-middle">
												<span class="text-muted">{{$lead->created_at->format('j F, Y h:s a')}}</span>
											</td>
											<td class="align-middle">
												<span class="text-muted">{{$lead->getBrandName->name}}</span>
											</td>
											<td class="align-middle">
												<span class="text-muted">{{($lead->value)?'$'.$lead->value.'.00' :'---'}}</span>
											</td>
											<td class="text-center align-middle">
												<span class="badge badge-{{$lead->getStatusColor->leadstatus_color}} rounded-pill">{{$lead->getStatus->status}}</span>
											</td>
											<td class="align-middle text-nowrap">
												<!-- only Team Lead Assing Lead to Sales Agent -->
												@if(Auth::user()->type == 'lead')
													<a title="Assing to Agent" data-id="{{$lead->id}}" data-type="confirm" href="assign-agent" class="btn btn-info btn-sm btn-round cxm-assing assign-lead-show-modal" data-toggle="modal" data-target="#assignLead"><span class="zmdi zmdi-account-add"></span></a>
												@endif
												@if($lead->view == '0')
													<a href="javascript:void(0);" class="btn btn-danger btn-sm btn-round" title="Viewed"><i class="zmdi zmdi-close"></i></a>
												@else
													<a href="javascript:void(0);" class="btn btn-success btn-sm btn-round" title="Not View"><i class="zmdi zmdi-check"></i></a>
												@endif
												<a title="View" href="{{route('leadshow',$lead->id)}}" class="btn btn-primary btn-sm btn-round"><i class="zmdi zmdi-eye"></i></a>
												<a title="Change Status" data-id="{{$lead->id}}" data-type="confirm" href="javascript:void(0);" class="btn bg-orange btn-sm btn-round statusChange" data-toggle="modal" data-target="#changeStatusModal"><span class="zmdi zmdi-settings"></span></a>
												<a title="Comments" data-id="{{$lead->id}}" href="#" class="btn btn-neutral btn-sm btn-round LeadComments" data-toggle="modal" data-target="#cxmCommentsModal">
													<i class="zmdi zmdi-comments text-info"></i> </a>
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

	<!-- Create Lead -->
	<div class="modal fade" id="create-lead-modal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="title" id="defaultModalLabel">Create Lead</h4>
				</div>
				<form method="POST" id="create-lead-form">
					<div class="modal-body">
						<div class="col-sm-12">
							@if(Auth::user()->type == 'ppc')
								<div class="form-group">
                                    <select id="team_hnd" name="team_key" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Team" data-live-search="true" required>
										<option disabled>Select Team</option>
										@foreach($teams as $team)
											<option value="{{$team->team_key}}">{{$team->name}}</option>
										@endforeach
									</select>
								</div>
							@else
								<input type="hidden" id="team_hnd" class="form-control" name="team_key" value="{{Auth::user()->team_key}}">
							@endif
							<div class="form-group">
								@if(Auth::user()->type == 'ppc')
                                    <select id="brand_key" name="brand_key" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Brand" data-live-search="true" required>
										<option disabled>Select Brand</option>
									</select>
								@else
                                    <select id="brand_key" name="brand_key" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Brand" data-live-search="true" required>
										<option disabled>Select Brand</option>
										@foreach($assign_brands as $assign_brand)
											<option value="{{$assign_brand->brand_key}}">{{$assign_brand->getBrandNameWithTrashed->name}}</option>
										@endforeach
									</select>
								@endif
							</div>
							<div class="form-group">
								<input type="text" id="title" class="form-control" placeholder="Lead Title" name="title" required>
							</div>
							<div class="form-group">
								<input type="text" id="name" class="form-control" placeholder="Name" name="name" required>
							</div>
							<div class="form-group">
								<input type="email" id="email" class="form-control" placeholder="Email" name="email" required/>
							</div>
							<div class="form-group">
								<input type="text" id="phone" class="form-control" placeholder="Phone" name="phone" required/>
							</div>
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text"><i class="zmdi zmdi-money"></i></span>
									</div>
									<input type="number" id="value" class="form-control" placeholder="Lead Value" name="value"/>
								</div>
							</div>
							<div class="form-group">
                                    <select id="source" name="source" class="form-control show-tick" data-placeholder="Select Lead Source" required>
									<option value="facebook">Facebook</option>
									<option value="brack">Bark</option>
									<option value="google">Google</option>
									<option value="bing">Bing</option>
								</select>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" id="addLeadBtn" class="btn btn-success btn-round">SAVE</button>
						<button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Comments Modal -->
	<style>
        .cxm-comments {
            max-height: 300px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .cxm-comments::-webkit-scrollbar {
            width: 5px;
        }

        .cxm-comments::-webkit-scrollbar-track {
            box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
        }

        .cxm-comments::-webkit-scrollbar-thumb {
            background-color: #17a2b8;
            outline: 0px solid slategrey;
        }
	</style>
	<div class="modal fade" id="cxmCommentsModal" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="title" id="defaultModalLabel">Comments</h4>
					<a class="btn btn-info btn-sm rounded-pill" data-toggle="collapse" href="#cxmCollapseAddComment" role="button" aria-expanded="false" aria-controls="cxmCollapseAddComment"><i class="zmdi zmdi-plus"></i> Add Comments</a>
				</div>
				<div class="modal-body">
					<div class="collapse" id="cxmCollapseAddComment">
						<div class="p-2 bg-info">
							<form id="lead_comments_form">
								<input type="hidden" name="lead_id" value="" id="leadId">
								<input type="hidden" name="type" value="{{Auth::user()->type}}">
								<textarea name="comment" class="form-control bg-light border-info" placeholder="Type Comment."></textarea>
								<div class="row justify-content-end">
									<div class="col-md-4">
										<button class="btn btn-info btn-sm btn-block rounded-pill">
											<i class="zmdi zmdi-comment"></i> Save Comment
										</button>
									</div>
								</div>
							</form>
						</div>
						<hr>
					</div>
					<div class="card border border-info rounded-0">
						<h6 class="header px-2 l-cyan mb-2"><span class="text-dark">All Comments</span></h6>
						<div class="body p-0">
							<div id="lead_comments_data" class="cxm-comments"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Dialogs ====== -->
	<!-- Default Size -->
	<div class="modal fade" id="changeStatusModal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="title" id="defaultModalLabel">Change Status</h4>
					<input type="hidden" id="status_hdn" class="form-control" name="status_hdn" value="">
				</div>
				<div class="modal-body">
					<select id="lead-status" name="status" class="form-control show-tick ms select2" data-placeholder="Select" required>
						@foreach($leadsStatus as $status)
							<option value="{{ $status->id }}">{{ $status->status }}</option>
						@endforeach
					</select>
				</div>
				<div class="modal-footer">
					<button type="button" id="changeStatusBtn" class="btn btn-success btn-round">SAVE CHANGES</button>
					<button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Default Size -->
	<div class="modal fade" id="assignLead" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="title" id="defaultModalLabel">Assign to Agent</h4>
					<input type="hidden" id="lead_hdn" class="form-control" name="lead_hdn" value="">
				</div>
				<div class="modal-body">
					<select id="agent_key" name="assingAgent" class="form-control show-tick ms select2" data-placeholder="Select" required>
						<option>Select Agent</option>
						@foreach($agentSales as $agent)
							<option value="{{$agent->id}}">{{$agent->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="modal-footer">
					<button type="button" id="assingAgentBtn" class="btn btn-success btn-round">SAVE CHANGES</button>
					<button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
				</div>
			</div>
		</div>
	</div>

@endsection

@push('cxmScripts')
	<script src="{{ asset('assets/plugins/momentjs/moment.js') }}"></script>
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

	@include('lead.script')
	<script>
        function getParam() {
            window.location.href = "{{ route('user.leads.index') }}?brandKey=" + encodeURIComponent($('#brand').val()) + "&dateRange=" + encodeURIComponent($('#date-range').val()) + "&listType=" + encodeURIComponent($("#lead_type").val());
        }

        $(function () {
            $(document).ready(function () {
                $('#LeadTable').DataTable().destroy();
                $('#LeadTable').DataTable({
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



{{--@extends('layouts.app')--}}{{--@section('cxmTitle', 'Leads')--}}{{--@section('content')--}}{{--<section class="content">--}}{{--    <div class="body_scroll">--}}{{--        <div class="block-header">--}}{{--            <div class="row">--}}{{--                <div class="col-lg-7 col-md-6 col-sm-12">--}}{{--                    <h2>Lead List</h2>--}}{{--                    <ul class="breadcrumb">--}}{{--                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}</a></li>--}}{{--                        <li class="breadcrumb-item">Leads</li>--}}{{--                        <li class="breadcrumb-item active"> List</li>--}}{{--                    </ul>--}}{{--                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>--}}{{--                </div>--}}{{--                <div class="col-lg-5 col-md-6 col-sm-12 text-right">--}}{{--                @if(Auth::user()->type != 'ppc' && Auth::user()->type != 'qa')--}}{{--                    <button class="btn btn-success btn-icon rounded-circle" type="button" id="mylead" title="My Leads"><i class="zmdi zmdi-account-o"></i></button>--}}{{--                @endif--}}{{--                    <button class="btn btn-success btn-icon rounded-circle" type="button" data-toggle="modal" data-target="#leadModal"><i class="zmdi zmdi-plus"></i></button>--}}{{--                    @include('includes.cxm-top-right-toggle-btn')--}}{{--                </div>--}}{{--            </div>--}}{{--        </div>--}}{{--        <div class="container-fluid">--}}{{--            <div class="row clearfix">--}}{{--                <div class="col-lg-12">--}}{{--                    <div class="row">--}}{{--                        <div class="col-lg-12 col-md-12 col-sm-12">--}}{{--                            <div class="card">--}}{{--                                <div class="body">--}}{{--                                    <div class="form-row">--}}{{--                                        <div class="col-md-6">--}}{{--                                            <label>Brands</label>--}}{{--                                            <select class="form-control show-tick ms select2" data-placeholder="Select" id="brand">--}}{{--                                                <option value='0' data-brand="0">All</option>--}}{{--                                                @foreach($team_brand as $brand)--}}{{--                                                <option value="{{$brand->brand_key}}" data-brand="{{$brand->brand_key}}">{{$brand->brand_name}}</option>--}}{{--                                                @endforeach--}}{{--                                            </select>--}}{{--                                        </div>--}}{{--                                        <div class="col-md-6">--}}{{--                                            <label>Select Month</label>--}}{{--                                            <div class="input-group">--}}{{--                                                <div class="input-group-prepend">--}}{{--                                                    <span class="input-group-text"><i class="zmdi zmdi-calendar"></i></span>--}}{{--                                                </div>--}}{{--                                                <input type="month" id="month-input" class="form-control" value="">--}}{{--                                            </div>--}}{{--                                        </div>--}}{{--                                    </div>--}}{{--                                </div>--}}{{--                            </div>--}}{{--                        </div>--}}{{--                    </div>--}}{{--                    <div class="card">--}}{{--                        <div class="table-responsive">--}}{{--                            <table id="LeadTable" class="table table-striped table-hover xjs-basic-example theme-color">--}}{{--                                <thead>--}}{{--                                    <tr>--}}{{--                                        <th>ID#</th>--}}{{--                                        <th>Title</th>--}}{{--                                        <th>Contact</th>--}}{{--                                        <th>Assigned</th>--}}{{--                                        <th>Date</th>--}}{{--                                        <th>Brand</th>--}}{{--                                        <th>Value</th>--}}{{--                                        <th class="text-center">Status</th>--}}{{--                                        <th>Action</th>--}}{{--                                    </tr>--}}{{--                                </thead>--}}{{--                                <tbody>--}}{{--                                    @foreach($leadsdata as $lead)--}}{{--                                    <tr>--}}{{--                                        <td class="align-middle">{{$lead->id}}</td>--}}{{--                                        <td class="align-middle"><a class="text-info" href="{{route('leadshow',$lead->id)}}"><span class="zmdi zmdi-open-in-new"></span> {{$lead->title}}</a></td>--}}{{--                                        <td class="align-middle">--}}{{--                                            {{$lead->name}}<br>--}}{{--                                            {{$lead->email}}<br>--}}{{--                                            {{$lead->phone}}<br>--}}{{--                                            {{$lead->lead_ip}}<br>--}}{{--                                        </td>--}}{{--                                        <td class="align-middle">{{($lead->assignAgent)?$lead->assignAgent :'---' }}</td>--}}{{--                                        <td class="align-middle"><span class="text-muted">{{$lead->created_at->format('j F, Y h:s a')}}</span></td>--}}{{--                                        <td class="align-middle"><span class="text-muted">{{$lead->brandName}}</span></td>--}}{{--                                        <td class="align-middle"><span class="text-muted">{{($lead->value)?'$'.$lead->value.'.00' :'---'}}</span></td>--}}{{--                                        <td class="text-center align-middle"><span class="badge badge-{{$lead->statusColor}} rounded-pill">{{$lead->status}}</span></td>--}}{{--                                        <td class="align-middle text-nowrap">--}}{{--                                        <!-- only Team Lead Assing Lead to Sales Agent -->--}}{{--                                        @if(Auth::user()->type == 'lead')--}}{{--                                            <a title="Assing to Agent" data-id="{{$lead->id}}" data-type="confirm" href="assign-agent" class="btn btn-info btn-sm btn-round cxm-assing" data-toggle="modal" data-target="#assignLead"><span class="zmdi zmdi-account-add"></span></a>--}}{{--                                        @endif--}}{{--                                            @if($lead->view == '0')--}}{{--                                            <a href="javascript:void(0);" class="btn btn-danger btn-sm btn-round" title="Viewed"><i class="zmdi zmdi-close"></i></a>--}}{{--                                            @else--}}{{--                                            <a href="javascript:void(0);" class="btn btn-success btn-sm btn-round" title="Not View"><i class="zmdi zmdi-check"></i></a>--}}{{--                                            @endif--}}{{--                                            <a title="View" href="{{route('leadshow',$lead->id)}}" class="btn btn-primary btn-sm btn-round"><i class="zmdi zmdi-eye"></i></a>--}}{{--                                            <a title="Change Status" data-id="{{$lead->id}}" data-type="confirm" href="javascript:void(0);" class="btn bg-orange btn-sm btn-round statusChange" data-toggle="modal" data-target="#changeStatusModal"><span class="zmdi zmdi-settings"></span></a>--}}{{--                                            <a title="Comments" data-id="{{$lead->id}}" href="#" class="btn btn-neutral btn-sm btn-round LeadComments" data-toggle="modal" data-target="#cxmCommentsModal">--}}{{--                                                <i class="zmdi zmdi-comments text-info"></i>--}}{{--                                            </a>--}}{{--                                        </td>--}}{{--                                    </tr>--}}{{--                                    @endforeach--}}{{--                                </tbody>--}}{{--                            </table>--}}{{--                        </div>--}}{{--                        {{ $leads->links() }}--}}{{--                    </div>--}}{{--                </div>--}}{{--            </div>--}}{{--        </div>--}}{{--    </div>--}}{{--</section>--}}{{--  --}}{{--<!-- Comments Modal -->--}}{{--<style>--}}{{--    .cxm-comments {max-height:300px; overflow-y:auto; overflow-x:hidden;}--}}{{--    .cxm-comments::-webkit-scrollbar {width:5px;}--}}{{--    .cxm-comments::-webkit-scrollbar-track {box-shadow:inset 0 0 6px rgba(0,0,0,0.3);}--}}{{--    .cxm-comments::-webkit-scrollbar-thumb {background-color:#17a2b8; outline:0px solid slategrey;}--}}{{--</style>--}}{{--<div class="modal fade" id="cxmCommentsModal" tabindex="-1" role="dialog">--}}{{--    <div class="modal-dialog modal-lg" role="document">--}}{{--        <div class="modal-content">--}}{{--            <div class="modal-header">--}}{{--                <h4 class="title" id="defaultModalLabel">Comments</h4>--}}{{--                <a class="btn btn-info btn-sm rounded-pill" data-toggle="collapse" href="#cxmCollapseAddComment" role="button" aria-expanded="false" aria-controls="cxmCollapseAddComment"><i class="zmdi zmdi-plus"></i> Add Comments</a>--}}{{--            </div>--}}{{--            <div class="modal-body">--}}{{--                <div class="collapse" id="cxmCollapseAddComment">--}}{{--                    <div class="p-2 bg-info">--}}{{--                        <form id="lead_comments_form">--}}{{--                            <input type="hidden" name="lead_id" value="" id="leadId">--}}{{--                            <input type="hidden" name="type" value="{{Auth::user()->type}}">--}}{{--                            <textarea name="comment" class="form-control bg-light border-info" placeholder="Type Comment."></textarea>--}}{{--                            <div class="row justify-content-end">--}}{{--                                <div class="col-md-4">--}}{{--                                    <button class="btn btn-info btn-sm btn-block rounded-pill"><i class="zmdi zmdi-comment"></i> Save Comment</button>--}}{{--                                </div>--}}{{--                            </div>--}}{{--                        </form>--}}{{--                    </div>--}}{{--                    <hr>--}}{{--                </div>--}}{{--                <div class="card border border-info rounded-0">--}}{{--                    <h6 class="header px-2 l-cyan mb-2"><span class="text-dark">All Comments</span></h6>--}}{{--                    <div class="body p-0">--}}{{--                        <div id="lead_comments_data" class="cxm-comments"></div>--}}{{--                    </div>--}}{{--                </div>--}}{{--            </div>--}}{{--        </div>--}}{{--    </div>--}}{{--</div>--}}{{--  --}}{{--<!-- Modal Dialogs ====== -->--}}{{--<!-- Default Size -->--}}{{--<div class="modal fade" id="changeStatusModal" tabindex="-1" role="dialog">--}}{{--    <div class="modal-dialog" role="document">--}}{{--        <div class="modal-content">--}}{{--            <div class="modal-header">--}}{{--                <h4 class="title" id="defaultModalLabel">Change Status</h4>--}}{{--                <input type="hidden" id="status_hdn" class="form-control" name="status_hdn" value="">--}}{{--            </div>--}}{{--            <div class="modal-body">--}}{{--                <select id="lead-status" name="status" class="form-control show-tick ms select2" data-placeholder="Select" required>--}}{{--                    @foreach($leadsStatus as $status)--}}{{--                        <option value="{{ $status->id }}">{{ $status->status }}</option>--}}{{--                    @endforeach--}}{{--                </select>--}}{{--            </div>--}}{{--            <div class="modal-footer">--}}{{--                <button type="button" id="changeStatusBtn" class="btn btn-success btn-round">SAVE CHANGES</button>--}}{{--                <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>--}}{{--            </div>--}}{{--        </div>--}}{{--    </div>--}}{{--</div>--}}{{--<!-- Create Lead -->--}}{{--<div class="modal fade" id="leadModal" tabindex="-1" role="dialog">--}}{{--    <div class="modal-dialog" role="document">--}}{{--        <div class="modal-content">--}}{{--            <div class="modal-header">--}}{{--                <h4 class="title" id="defaultModalLabel">Create Lead</h4>--}}{{--            </div>--}}{{--            <!-- <form method="POST" id="team-member-Form"> -->--}}{{--            <div class="modal-body">--}}{{--                    <div class="col-sm-12">--}}{{--                    @if(Auth::user()->type == 'ppc')--}}{{--                        <div class="form-group">--}}{{--                            <select id="team_hnd" name="team_key" class="form-control " data-placeholder="Select" required>--}}{{--                            <option>Select Team</option>--}}{{--                            @foreach($teams as $team)--}}{{--                                <option value="{{$team->team_key}}">{{$team->name}}</option>--}}{{--                            @endforeach--}}{{--                            </select>--}}{{--                        </div>--}}{{--                    @else--}}{{--                        <input type="hidden" id="team_hnd" class="form-control" name="team_key" value="{{Auth::user()->team_key}}">--}}{{--                    @endif--}}{{--                        <div class="form-group">--}}{{--                        @if(Auth::user()->type == 'ppc')--}}{{--                        <select id="brand_key" name="brand_key" class="form-control show-tick ms" data-placeholder="Select" required>--}}{{--                            <option>Select Brand</option>--}}{{--                        </select>--}}{{--                        @else--}}{{--                        <select id="brand_key" name="brand_key" class="form-control show-tick ms" data-placeholder="Select" required>--}}{{--                            <option>Select Brand</option>--}}{{--                            @foreach($team_brand as $brand)--}}{{--                                <option value="{{$brand->brand_key}}">{{$brand->brand_name}}</option>--}}{{--                            @endforeach--}}{{--                        </select>--}}{{--                        @endif--}}{{--                        </div>--}}{{--                        <div class="form-group">--}}{{--                            <input type="text" id="title" class="form-control" placeholder="Lead Title" name="title" required>--}}{{--                        </div>--}}{{--                        <div class="form-group">--}}{{--                            <input type="text" id="name" class="form-control" placeholder="Name" name="name" required>--}}{{--                        </div>--}}{{--                        <div class="form-group">--}}{{--                            <input type="email" id="email" class="form-control" placeholder="Email" name="email" required />--}}{{--                        </div>--}}{{--                        <div class="form-group">--}}{{--                            <input type="text" id="phone" class="form-control" placeholder="Phone" name="phone" required />--}}{{--                        </div>--}}{{--                        <div class="form-group">--}}{{--                            <div class="input-group">--}}{{--                                <div class="input-group-prepend">--}}{{--                                    <span class="input-group-text"><i class="zmdi zmdi-money"></i></span>--}}{{--                                </div>--}}{{--                                <input type="text" id="value" class="form-control" placeholder="Lead Value" name="value" />--}}{{--                            </div>--}}{{--                        </div>--}}{{--                        <div class="form-group">--}}{{--                        <select id="source" name="source" class="form-control show-tick ms select2" data-placeholder="Select Lead Source" required>--}}{{--                            <option value="facebook">Facebook</option>--}}{{--                            <option value="brack">Brack</option>--}}{{--                            <option value="google">Google</option>--}}{{--                            <option value="bing">Bing</option>--}}{{--                        </select>--}}{{--                        </div>--}}{{--                    </div>--}}{{--            </div>--}}{{--            <div class="modal-footer">--}}{{--                <button type="button" id="addLeadBtn" class="btn btn-success btn-round">SAVE</button>--}}{{--                <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>--}}{{--            </div>--}}{{--        </div>--}}{{--    </div>--}}{{--</div>--}}{{--<!-- Default Size -->--}}{{--<div class="modal fade" id="assignLead" tabindex="-1" role="dialog">--}}{{--    <div class="modal-dialog" role="document">--}}{{--        <div class="modal-content">--}}{{--            <div class="modal-header">--}}{{--                <h4 class="title" id="defaultModalLabel">Assign to Agent</h4>--}}{{--                <input type="hidden" id="lead_hdn" class="form-control" name="lead_hdn" value="">--}}{{--            </div>--}}{{--            <div class="modal-body">--}}{{--                <select id="agent_key" name="assingAgent" class="form-control show-tick ms select2" data-placeholder="Select" required>--}}{{--                <option>Select Agent</option>--}}{{--                    @foreach($agentSales as $agent)--}}{{--                    <option value="{{$agent->id}}">{{$agent->name}}</option>--}}{{--                    @endforeach--}}{{--                </select>--}}{{--            </div>--}}{{--            <div class="modal-footer">--}}{{--                <button type="button" id="assingAgentBtn" class="btn btn-success btn-round">SAVE CHANGES</button>--}}{{--                <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>--}}{{--            </div>--}}{{--        </div>--}}{{--    </div>--}}{{--</div>--}}{{--@endsection--}}{{--@push('cxmScripts')--}}{{--    @include('lead.script')--}}{{--@endpush--}}
