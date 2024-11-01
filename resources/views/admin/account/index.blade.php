@extends('admin.layouts.app')

@section('cxmTitle', 'Admin Account')

@section('content')
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Account List</h2>

                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">Account</li>
                        <li class="breadcrumb-item active"> List</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                <button class="btn btn-success btn-icon rounded-circle" type="button" data-toggle="modal" id="create-account-modal-btn"  data-target="#accountModal"><i class="zmdi zmdi-plus"></i></button>
                    @include('includes.admin.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="table-responsive">
                                <table id="AccountTable" class="table table-striped table-hover xjs-basic-example theme-color">

                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Created At</th>
                                        <th data-breakpoints="xs md">Status</th>
                                        <th class="text-center" data-breakpoints="sm xs md">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                	@foreach($accounts as $account)
                                    <tr>

                                        <td class="align-middle">{{$account->name}}</td>
                                        <td class="align-middle">{{$account->email}}</td>
                                        <td class="align-middle">{{$account->created_at}}</td>
                                        <td class="align-middle text-center">
                                            {!! ($account->status == 1)?'<span class="zmdi zmdi-check-circle text-success" title="Active"></span>' :'<span class="zmdi zmdi-close-circle text-danger" title="Inactive"></span>' !!}
                                        </td>
                                        <td class="text-center align-middle">
                                            <button data-id="{{$account->id}}" title="Edit" class="btn btn-warning btn-sm btn-round editAccount" data-toggle="modal" data-target="#EditModal"><i class="zmdi zmdi-edit"></i></button>



                                            <a data-id="{{$account->id}}" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round adminDelButton"><i class="zmdi zmdi-delete"></i></a>


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

<!-- Create Account -->
<div class="modal fade" id="accountModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Create A New Admin  Account</h4>
            </div>
            <form id="create-account">
                <div class="modal-body">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <input type="text" id="name" class="form-control" placeholder="Name" name="name">
                        </div>
                        <div class="form-group">
                            <input type="email" id="email" class="form-control" placeholder="Email" name="email">
                        </div>
                        <div class="form-group">
                            <input type="email" id="pseudo_email" class="form-control" placeholder="Pseudo Email" name="pseudo_email">
                        </div>
                        <div class="form-group">
                            <input type="number" id="phone" class="form-control" placeholder="Phone" name="phone">
                        </div>
                        <div class="form-group">
                            <input type="password" id="password" class="form-control" placeholder="Password" name="password">
                        </div>
                        <div class="form-group">
                            <input type="text" id="designation" class="form-control" placeholder="Designation" name="designation">
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

<!-- Edit Account -->
<div class="modal fade" id="EditModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Edit Admin Account</h4>
            </div>
            <form id="account_update_form">
                <div class="modal-body">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <input type="text" id="edit_name" class="form-control" placeholder="Name" name="name">
                        </div>
                        <div class="form-group">
                            <input type="email" id="edit_email" class="form-control" placeholder="Email" name="email">
                        </div>
                        <div class="form-group">
                            <input type="email" id="edit_pseudo_email" class="form-control" placeholder="Pseudo Email" name="pseudo_email">
                        </div>
                        <div class="form-group">
                            <input type="number" id="edit_phone" class="form-control" placeholder="Phone" name="phone">
                        </div>
                        <div class="form-group">
                            <input type="password" id="edit_password" class="form-control" placeholder="Password" name="password">
                        </div>
                        <div class="form-group">
                            <input type="text" id="edit_designation" class="form-control" placeholder="Designation" name="designation">
                        </div>

                          <div class="form-group ">
                                            <label for="status_address">Status</label>
                                            <select class="form-control show-tick ms" data-placeholder="Select" id='status'name='status' required>
                                                <option value="1" >Active</option>
                                                <option value="0" >Inactive</option>


                                            </select>
                                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="updateAccoutBtn" class="btn btn-success btn-round">SAVE</button>
                    <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                </div>
            </form>


        </div>
    </div>
</div>

@endsection

@push('cxmScripts')
    @include('admin.account.script')
@endpush
