@extends('admin.layouts.app')

@section('content')
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Categories</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item"><a href="#">Category</a></li>
                        <li class="breadcrumb-item active">Show</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12">
                    <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
                </div>
            </div>
        </div>

        <div class="container-fluid">

            <!-- Exportable Table -->
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="header">
                            <h2 ><strong>Project Name</strong> Category
                                <span style="float: right;padding-right: 5px; color: #e47297;">
                                    <strong><a href="{{ route('category.create') }}" style="color: #e47297;">Add New Category</a></strong></span>
                            </h2>
                            <div id="alerts"></div>
                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                <table id="testTable" class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <!-- <table class="table  product_item_list table-hover dataTable js-exportable c_table theme-color mb-0"> -->
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Create Date</th>
                                            <th>Last Update</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                         @foreach($categories as $category)
                                        <tr>
                                            <td>{{$category->id}}</td>
                                            <td>{{$category->name}}</td>

                                            <td>{{$category->created_at}}</td>
                                            <td>{{$category->updated_at}}</td>
                                            <td>
                                                <a href="{{route('category.edit',[$category->id],'/edit')}}"  class="btn btn-default waves-effect waves-float btn-sm waves-green">
                                                    <i class="zmdi zmdi-edit"></i>
                                                </a>
                                                <a  href="#"  id="total-earning" data-id="{{$category->id}}" class="btn btn-default waves-effect waves-float btn-sm waves-red delButton"><i class="zmdi zmdi-delete"></i></a>



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
    </div>
</section>
@endsection
