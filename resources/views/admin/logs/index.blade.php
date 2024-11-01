@extends('admin.layouts.app')
@section('cxmTitle', 'Logs')

@section('content')
@push('css')
    <style>
        .brand-icon object {
            display: inline-block;
            max-width: 120px;
            height: 30px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }
    </style>
@endpush
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Logs List</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">Logs</li>
                        <li class="breadcrumb-item active">List</li>
                    </ul>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    @include('includes.admin.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="row clearfix">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card">
                                <div class="body">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <form action="{{ route('admin.log') }}" method="POST" enctype="multipart/form-data" class="form-inline">
                                                @csrf
                                                <div class="row clearfix">
                                                    <div class="col-lg-12 col-md-12">
                                                        <label>Upload Log File</label>
                                                        <input type="file" name="log" class="form-control">
                                                        <button type="submit" class="btn btn-success">Submit</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="col-lg-4">
                                            <form action="{{ route('admin.log') }}" method="GET" class="form-inline">
                                                <div class="row clearfix">
                                                    <div class="col-lg-12 col-md-12 mt-3">
                                                        <label>Search Log</label>
                                                        <select name="type" id="" class="form-control">
                                                            <option value="All" selected>All</option>
                                                            <option value="Withlist" {{request('type') && request('type') == 'Withlist'?'selected':''}}>Withlist</option>
                                                            <option value="Blacklist" {{request('type') && request('type') == 'Blacklist'?'selected':''}}>Blacklist</option>
                                                        </select>
                                                        <button type="submit" class="btn btn-primary ml-2">Search</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="col-lg-4">
                                            <form action="{{ route('admin.log') }}" method="GET" class="form-inline">
                                                <div class="row clearfix">
                                                    <div class="col-lg-12 col-md-12 mt-3">
                                                    @if(request('type'))
                                                        <input type="hidden" name="type" value="{{ request('type') }}">
                                                    @endif
                                                    @if(request('page'))
                                                        <input type="hidden" name="page" value="{{ request('page') }}">
                                                    @endif
                                                        <label>Search Log</label>
                                                        <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                                                        <button type="submit" class="btn btn-primary ml-2">Search</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="table-responsive">
                            <table id="LeadTable" class="table table-striped table-hover theme-color js-exportable" xdata-sorting="false">
                                <thead>
                                    <tr>
                                        <th>&nbsp;</th>
                                        <th>ID #</th>
                                        <th data-orderable="false">IP</th>
                                        <th data-orderable="false">Datetime</th>
                                        <th data-orderable="false">Method</th>
                                        <th class="text-center" data-orderable="false">URL</th>
                                        <th class="text-center" data-orderable="false">Param</th>
                                        <th class="text-center" data-orderable="false">Size</th>
                                        <th class="text-center" data-orderable="false">Referrer</th>
                                        <th class="text-center" data-orderable="false">User Agent</th>
                                        <th class="text-center" data-orderable="false">Status</th>
                                        <th class="text-center" data-orderable="false">Created_at</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($logs_data as $key => $lead)
                                        <tr>
                                            <td class="align-middle">
                                            @if($lead->list_type == 1)
                                                <input type="checkbox" name="ids[{{$lead->id}}]" value="{{$lead->id}}" checked onchange="confirmChange(event, '{{'#formid-'.$key}}')">
                                            @else
                                                <input type="checkbox" name="ids[{{$lead->id}}]" value="{{$lead->id}}" onchange="confirmChange(event, '{{'#formid-'.$key}}')">
                                            @endif
                                            </td>
                                            <td class="align-middle">{{$lead->id}}</td>
                                            <td>
                                                @if($lead->list_type == 1)
                                                    <span class="badge bg-success text-white p-3 " style="font-size:16px">
                                                        {{ $lead->ip }}
                                                    </span>
                                                    <form method="POST" action="{{route('admin.logupdate')}}" id="formid-{{$key}}" class="d-none">
                                                        @csrf
                                                        <input type="text" value="{{$lead->ip}}" name="ip">
                                                        <input type="text" value="0" name="update_status">
                                                    </form>
                                                @else
                                                    <span class="badge bg-danger text-white p-3 " style="font-size:16px">
                                                        {{ $lead->ip }}
                                                    </span>
                                                    <form method="POST" action="{{route('admin.logupdate')}}" id="formid-{{$key}}" class="d-none">
                                                        @csrf
                                                        <input type="text" value="{{$lead->ip}}" name="ip">
                                                        <input type="text" value="1" name="update_status">
                                                    </form>
                                                    <!-- Optionally, you can put some other code here for when the IP is not whitelisted -->
                                                @endif
                                            </td>
                                            <!-- <td class="align-middle">
                                                <a class="text-warning" href="{{ route('lead.show', $lead->id) }}">{{ $lead->ip }}</a>
                                            </td> -->
                                            <td class="align-middle">{{ $lead->datetime }}</td>
                                            <td class="align-middle">{{ $lead->method }}</td>
                                            @if($lead->url)
                                                @php 
                                                    $parsed_url = parse_url($lead->url);
                                                    $path = $parsed_url['path'];
                                                    $query = isset($parsed_url['query']) ? $parsed_url['query'] : '';
                                                @endphp
                                                <td class="align-middle">{{ $path }}</td>
                                                <td class="align-middle">{{ $query }}</td>
                                            @else
                                                <td class="align-middle">{{ $lead->url }}</td>
                                                <td class="align-middle">-</td>
                                            @endif
                                            <td class="align-middle">{{ $lead->size }}</td>
                                            <td class="align-middle">{{ $lead->referrer }}</td>
                                            <td class="align-middle">{{ $lead->user_agent }}</td>
                                            <td class="align-middle">{{ $lead->status }}</td>
                                            <td class="align-middle">
                                                <span class="text-muted">{{ $lead->created_at->format('j F, Y') }}<br>{{ $lead->created_at->format('h:i:s A') }}<br>{{ $lead->created_at->diffForHumans() }}</span>
                                            </td>
                                        </tr>

                                    @endforeach
                                </tbody>
                            </table>
                            {{ $logs_data->appends(request()->except('page'))->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('cxmScripts')
    <!-- Include necessary scripts here -->
         <!-- https://www.daterangepicker.com/ -->
    <script src="{{ asset('assets/plugins/momentjs/moment.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    @include('admin.lead.script')

    <script>
        function getParam() {
            window.location.href = "{{ route('admin.leads.index') }}?brandKey=" + encodeURIComponent($('#brand').val()) + "&teamKey=" + encodeURIComponent($('#team').val()) + "&dateRange=" + encodeURIComponent($('#date-range').val());
        }

        $(function () {
            $(document).ready(function () {
                $('#LeadTable').DataTable().destroy();
                $('#LeadTable').DataTable({
                    paging: false,
                    dom: 'lBfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    order: [[0, 'desc']],
                    language: {
                        info: ""
                    }
                });

                $('#team, #brand').on('change', getParam);
            });
        });

        function confirmChange(event, formId) {
        const isChecked = event.target.checked;
        const confirmationMessage = "Are you sure you want to change this IP?";

        if (confirm(confirmationMessage)) {
            // User confirmed the change
            Update(formId);
        } else {
            // User canceled the change, revert the checkbox state
            event.target.checked = !isChecked;
        }
    }

    function Update(formId) {
        // Additional logic to update the form can be added here
        $(formId).submit();
    }
    </script>
@endpush
