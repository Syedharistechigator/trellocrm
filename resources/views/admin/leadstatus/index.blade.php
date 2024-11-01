@extends('admin.layouts.app')@section('cxmTitle', 'Lead Status')

@section('content')
    @push('css')
{{--        <style>--}}
{{--            .form-control:invalid {--}}
{{--                border-color: #dc3545;--}}
{{--            }--}}

{{--            .form-control:invalid:focus {--}}
{{--                border-color: #dc3545;--}}
{{--                box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);--}}
{{--            }--}}

{{--            .form-control:valid {--}}
{{--                border-color: #28a745;--}}
{{--            }--}}
{{--        </style>--}}
    @endpush
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Lead Status</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}
                                </a></li> <li class="breadcrumb-item">Lead Status</li>
                            <li class="breadcrumb-item active"> List</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <button class="btn btn-success btn-icon rounded-circle" type="button" data-toggle="modal" id="create-lead-status-modal-btn" data-target="#statusModal">
                            <i class="zmdi zmdi-plus"></i></button>
                        @include('includes.admin.cxm-top-right-toggle-btn')
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="table-responsive">
                                <table id="StatusTable" class="table table-striped table-hover xjs-basic-example theme-color">
                                    <thead>
                                    <tr>
                                        <th>ID #</th>
                                        <th class="text-center">Status</th>
                                        <th data-breakpoints="sm xs">Created Date</th>
                                        <th class="text-center" data-breakpoints="sm xs md">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($statuses as $status)
                                        <tr>
                                            <td class="align-middle">{{ $status->id }}</td>
                                            <td class="text-center align-middle">
                                                <span class="badge badge-{{$status->leadstatus_color}} rounded-pill">{{$status->status}}</span>
                                            </td>
                                            <td class="align-middle">{{$status->created_at->format('j F, Y')}}</td>
                                            <td class="text-center align-middle">
                                                <button data-id="{{$status->id}}" title="Edit" class="btn btn-warning btn-sm btn-round editStatus" data-toggle="modal" data-target="#updateStatusModal">
                                                    <i class="zmdi zmdi-edit"></i>
                                                </button>
                                                <a title="Delete" data-id="{{$status->id}}" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round statusDelButton"><i class="zmdi zmdi-delete"></i></a>
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

    <!-- Create Status -->
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Create Status</h4>
                </div>
                <form id="lead-status-Form">
                    <div class="modal-body">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="create_status">Status</label>
                                <input type="text" id="create_status" class="form-control" placeholder="New Status" name="status" required>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="create_status_color">Status Color</label>
                                <select id="create_status_color" name="status_color" class="change form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Status Color" required>
                                    <option disabled>Select Status Color</option>
                                    <option value="default">Default</option>
                                    <option value="primary">Primary</option>
                                    <option value="success">Success</option>
                                    <option value="info">Info</option>
                                    <option value="warning">Warning</option>
                                    <option value="danger">Danger</option>
                                    <option value="dark">Dark</option>
                                </select>
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

    <!-- Edit Status -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Update Status</h4>
                </div>
                <form id="Update-lead-status-Form">
                    <input type="hidden" id="status_hdn" class="form-control" name="hdn" value="">
                    <div class="modal-body">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="edit_status">Status</label>
                                <input type="text" id="edit_status" class="form-control" placeholder="New Status" name="status" required>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="edit_status_color">Status Color</label>
                                <select id="edit_status_color" name="status_color" class="change form-control" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Status Color" required>
                                    <option disabled>Select Status Color</option>
                                    <option value="default">Default</option>
                                    <option value="primary">Primary</option>
                                    <option value="success">Success</option>
                                    <option value="info">Info</option>
                                    <option value="warning">Warning</option>
                                    <option value="danger">Danger</option>
                                    <option value="dark">Dark</option>
                                </select>
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
    @include('admin.leadstatus.script')
@endpush
