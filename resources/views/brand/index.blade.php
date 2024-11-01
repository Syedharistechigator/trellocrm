
@extends('layouts.app')

@section('cxmTitle', 'Brand')

@section('content')

<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Brand List</h2>

                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="zmdi zmdi-home"></i>{{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">Brands</li>
                        <li class="breadcrumb-item active"> List</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    @include('includes.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="">
                        <div class="table-responsive">
                            <table id="BrandTable" class="table table-striped table-hover js-basic-example theme-color">
                                <thead>
                                    <tr>
                                        <th>Brand Logo</th>
                                        <th>Brand Name</th>
                                        <!-- <th>Brand Key</th> -->
                                        <th data-breakpoints="sm xs">Brand URL</th>
                                        <th data-breakpoints="xs md" class="text-center">Status</th>
                                        <!-- <th data-breakpoints="sm xs md">Action</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                	@foreach($team_brand as $brand)
                                    <tr>
                                        <td class="align-middle">
                                        <object data="{!! $brand->brandLogo !!}"  width="100">
                                            <img src="{{asset('assets/images/no-results-found.png')}}" width="50px"
                                                 alt="{{$brand->brandName}}" loading="lazy">
                                        </object>
                                        </td>
                                        <td class="align-middle">{{$brand->brandName}}</td>
                                        <!-- <td><h5>{{$brand->brand_key}}</h5></td> -->
                                        <td class="align-middle">
                                            <a class="text-info" href="{{$brand->brandUrl}}" target="_blank"><span class="zmdi zmdi-open-in-new"></span> {{$brand->brandUrl}}</a>
                                        </td>
                                        <td class="text-center align-middle">
                                            {!! ($brand->status == 1)?'<span class="zmdi zmdi-check-circle text-success"></span>' :'<span class="zmdi zmdi-close-circle text-danger"></span>'; !!}
                                        </td>
                                        <!-- <td>
                                            <a title="Edit" href="{{route('brand.edit',[$brand->id],'/edit')}}" class="btn btn-primary btn-sm waves-effect waves-float waves-green"><i class="zmdi zmdi-edit"></i></a>
                                            <a title="Delete" data-id="{{$brand->id}}" data-type="confirm" href="javascript:void(0);" class=" btn btn-danger btn-sm  waves-effect waves-float waves-red delButton"><i class="zmdi zmdi-delete"></i></a>
                                        </td> -->
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
    @include('brand.script')
@endpush
