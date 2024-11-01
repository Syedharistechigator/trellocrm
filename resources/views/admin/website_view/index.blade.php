@extends('admin.layouts.app')
@section('cxmTitle', 'User Tracking')
@section('content')
	<section class="content">
		<div class="body_scroll">
			<div class="block-header">
				<div class="row">
					<div class="col-lg-7 col-md-6 col-sm-12">
						<h2>User Tracking List</h2>
						<ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
							<li class="breadcrumb-item">User Tracking</li>
							<li class="breadcrumb-item active">List</li>
						</ul>
						<button class="btn btn-primary btn-icon mobile_menu" type="button"><i
									class="zmdi zmdi-sort-amount-desc"></i></button>
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
							<div class="table-responsive">
								<table id="website_viewTable"
								       class="table table-striped table-hover theme-color js-exportable"
								       xdata-sorting="false">
									<thead>
									<tr>
										<th>Id</th>
										<th>Name</th>
										<th>Date</th>
										<th>IP Address</th>
										<th>Page URL</th>
										<th class="text-center" data-breakpoints="xs md">Secure</th>
										<th class="text-center" data-breakpoints="sm xs md">Action</th>
									</tr>
									</thead>
									<tbody>
									@foreach($website_views as $key=>$website_view)
                                            <?php
                                            $userData = App\Models\User::where("id", $website_view->user_id)->first(); ?>
										<tr>
											<td class="align-middle">{{$website_view->id}}</td>
											<td class="align-middle">{{$userData->name}}</td>
											<td class="align-middle">{{$website_view->created_at->format('j F, Y')}}
												<br>{{$website_view->created_at->format('h:i:s A')}}
												<br>{{$website_view->created_at->diffForHumans()}}</td>
											<td class="align-middle">{{$website_view->ip_address}}</td>
											<td class="align-middle"><a href="{{$website_view->page_url}}" target="_blank">{{ Str::limit($website_view->page_url, $limit = 45, $end = '...')}}</a></td>
											<td class="text-center">{!! (in_array($website_view->ip_address, $ip_addresses_keys) &&
                                            $ip_addresses->where('ip_address', $website_view->ip_address)->first()->list_type == 1 ) ?
                                             '<i class="zmdi zmdi-check-circle text-success" title="Publish"></i>' :
                                             '<i class="zmdi zmdi-close-circle text-danger" title="Unpublish"></i>' !!}</td>

											<td class="text-center align-middle">
												<a title="View"
												   href="{{route('website_view.show',[$website_view->id],'/show')}}"
												   class="btn btn-warning btn-sm btn-round"><i
															class="zmdi zmdi-eye"></i></a>
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
	<script src="{{ asset('assets/plugins/momentjs/moment.js') }}"></script>
@endpush
