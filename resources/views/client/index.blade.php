@extends('layouts.app')@section('cxmTitle', 'Clients/Customers')

@section('content')
    <section class="content uspto-page">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Clients List</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}
                                </a></li> <li class="breadcrumb-item">Clients</li>
                            <li class="breadcrumb-item active"> List</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <button class="btn btn-success btn-icon rounded-circle" type="button" data-toggle="modal" data-target="#clientModal">
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
                                <table id="ClientTable" class="table table-striped table-hover xjs-basic-example theme-color">
                                    <thead>
                                    <tr>
                                        <th>ID #</th>
                                        <th>Brand</th>
                                        <th>Name</th>
                                        <th class="text-center">Email</th>
                                        <th data-breakpoints="sm xs">Phone</th>
                                        @if(auth()->user()->type == 'third-party-user')
                                            <th>Total Spending</th>
                                        @endif
                                        <th>Date</th>
                                        <th class="text-center" data-breakpoints="xs md">Status</th>
                                        <th class="text-center" data-breakpoints="sm xs md">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($clients as $client)
                                        <tr>
                                            <td class="align-middle">{{$client->id}}</td>
                                            <td class="align-middle">{{optional($client->getBrandName)->name }}</td>
                                            <td class="align-middle">
                                                <div class="position-relative">
                                                    <a class="text-info" href="{{route('client.show',$client->id)}}"><span class="zmdi zmdi-open-in-new"></span> {{$client->name}}
                                                    </a>
                                                    @if(Cache::has('user-is-online-' . $client->userId))
                                                        <span class="cxm-online pulse"><i class="zmdi zmdi-circle text-success" title="Online"></i></span>
                                                    @else
                                                        <span class="cxm-offline pulse"><i class="zmdi zmdi-circle-o text-danger" title="Offline"></i></span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                <a class="text-info" href="mailto:{{$client->email}}" title="{{$client->email}}"><span class="zmdi zmdi-email"></span></a>
                                            </td>
                                            <td class="align-middle">{{$client->phone}}</td>
                                            @if(auth()->user()->type == 'third-party-user')
                                                <td class="align-middle">{{optional($client->getThirdPartyRole)->sum('amount')}}</td>
                                            @endif
                                            <td class="align-middle">{{$client->created_at->format('j F, Y')}}</td>
                                            <td class="text-center align-middle">
                                                @php echo ($client->status == 1)?'<span class="zmdi zmdi-check-circle text-success"></span>' :'<span class="zmdi zmdi-close-circle text-danger"></span>';  @endphp
                                            </td>
                                            <td class="text-center align-middle">
                                                <a title="View" href="{{route('client.show', $client->id)}}" class="btn btn-info btn-sm btn-round statusChange" style="color:#fff!important"><span class="zmdi zmdi-eye"></span></a>
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

    <!-- Create Client   -->
    <div class="modal fade" id="clientModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Create New Client</h4>
                </div>
                <form method="POST" id="client-Form">
                    <input type="hidden" id="team_hnd" class="form-control" name="team_key" value="{{ Auth::user()->team_key }}">
                    <div class="modal-body">
                        <div class="form-group">
                            <select id="brand_key" name="brand_key" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Brand" data-live-search="true" required>
                                <option disabled>Select Brand</option>
                                @foreach($assign_brands as $assign_brand)
                                    <option value="{{$assign_brand->brand_key}}" data-cxm-team-key="{{ $assign_brand->team_key }}">{{$assign_brand->getBrandNameWithTrashed->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" id="name" class="form-control" placeholder="Name" name="name" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <input type="email" id="email" class="form-control" placeholder="Email" name="email" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <input type="text" id="phone" class="form-control" placeholder="Phone" name="phone" autocomplete="off" required>
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

    <script src="{{ asset('assets/plugins/momentjs/moment.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    @include('client.script')
    <script>
        function getParam() {
            window.location.href = "{{ route('user.clients.index') }}?brandKey=" + encodeURIComponent($('#brand').val()) + "&dateRange=" + encodeURIComponent($('#date-range').val());
        }

        $(function () {
            $(document).ready(function () {
                $('#ClientTable').DataTable().destroy();
                $('#ClientTable').DataTable({
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
