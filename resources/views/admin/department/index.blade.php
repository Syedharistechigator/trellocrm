@extends('admin.layouts.app')@section('cxmTitle', 'Department')
@section('content')
    @push('css')
        @include('admin.department.style')
    @endpush
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Departments</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i
                                        class="zmdi zmdi-home"></i> {{ config('app.home_name') }}
                                </a></li>
                            <li class="breadcrumb-item"> Departments</li>
                            <li class="breadcrumb-item active"><a href="{{route('admin.department.index')}}"> List</a>
                            </li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <button class="btn btn-success btn-icon rounded-circle" type="button" data-toggle="modal"
                                id="create-department-modal-btn" data-target="#createDepartmentModal">
                            <i class="zmdi zmdi-plus"></i></button>
                        @include('includes.admin.cxm-top-right-toggle-btn')
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12">
                        <div class="">
                            <div class="table-responsive">
                                <table id="DepartmentTable"
                                       class="table table-striped table-hover xjs-basic-example theme-color">
                                    <thead>
                                    <tr>
                                        <th class='text-nowrap'>ID #</th>
                                        <th class='text-nowrap'>Name</th>
                                        <th class='text-nowrap'>Order</th>
                                        <th data-breakpoints="xs md">Status</th>
                                        <th data-breakpoints="sm xs md">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($departments as $department)
                                        <tr id="tr-{{$department->id}}">
                                            <td class="align-middle">{{$department->id}}</td>
                                            <td class="align-middle">{{$department->name}}</td>
                                            <td class="align-middle">{{$department->order}}</td>
                                            <td class="align-middle">
                                                <div class="custom-control custom-switch">
                                                    <input data-id="{{$department->id}}" type="checkbox"
                                                           class="custom-control-input change-status"
                                                           id="customSwitch{{$department->id}}" {{ $department->status ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                           for="customSwitch{{$department->id}}"></label>
                                                </div>
                                            </td>
                                            <td class="align-middle text-nowrap">
                                                <button data-id="{{$department->id}}" title="Edit"
                                                        class="btn btn-warning btn-sm btn-round editDepartment"
                                                        data-toggle="modal" data-target="#editDepartmentModal">
                                                    <i class="zmdi zmdi-edit"></i></button>
                                                <a title="Delete" data-id="{{$department->id}}" data-type="confirm"
                                                   href="javascript:void(0);"
                                                   class="btn btn-danger btn-sm btn-round delButton"><i
                                                        class="zmdi zmdi-delete"></i></a>
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
    <!-- Create Department -->
    <div class="modal fade" id="createDepartmentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Create A New Department</h4>
                </div>
                <form method="POST" id="create_form">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="order">Order</label>
                            <input type="number" id="order" name="order" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="change form-control" data-icon-base="zmdi"
                                    data-tick-icon="zmdi-check" data-show-tick="true" title="Select Status" required>
                                <option value="1" selected>Active</option>
                                <option value="0">InActive</option>
                            </select>
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

    <!-- Edit Department -->
    <div class="modal fade" id="editDepartmentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Edit Department</h4>
                </div>
                <form method="POST" id="update_form">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_name">Name</label>
                            <input type="text" id="edit_name" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_order">Order</label>
                            <input type="number" id="edit_order" name="order" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_status">Status</label>
                            <select id="edit_status" name="status" class="change form-control" data-icon-base="zmdi"
                                    data-tick-icon="zmdi-check" data-show-tick="true" title="Select Status" required>
                                <option value="1" selected>Active</option>
                                <option value="0">InActive</option>
                            </select>
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
    @include('admin.department.script')
@endpush
