@extends('admin.layouts.app')

@section('cxmTitle', 'Edit')

@section('content')
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Brand</h2>

                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item"><a href="#">Brand</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12">
                    <button class="btn btn-primary btn-icon float-right right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <!-- Basic Validation -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Create New</strong> Brand</h2>
                        </div>


                        <div class="body">

                            <form id="brand_update_form">

                                @csrf
                                @method('PUT')
                                <input type="hidden" id="hdn" class="form-control" name="hdn" value="{{$brand->id}}">

                                <div class="form-group form-float">
                                <label for="email_address">Brand Name</label>
                    <input type="text" class="form-control" id="brandName" name="name" value="{{$brand->name}}" minlength="3" required>
                                </div>
                                <div class="form-group form-float">
                                    <label for="email_address">Brand Logo (URL)</label>
                                    <input id="brandLogo" type="url" class="form-control" value="{{$brand->logo}}" name="logo" required>
                                </div>
                                <div class="form-group form-float">
                                    <label for="email_address">Brand URL</label>
                                    <input id="brandUrl" type="url" class="form-control" value="{{$brand->brand_url}}" name="brand_url" required>
                                </div>
                                <div class="form-group form-float">
                                    <label for="email_address">Publish</label>
                                    <select class="form-control show-tick ms select2" data-placeholder="Select" name='status' required>
                                        <option></option>
                                        <option value="1" <?php if($brand->status == 1) {echo "selected";}?>>Yes</option>
                                        <option value="0" <?php if($brand->status == 0) {echo "selected";}?>>No</option>
                                    </select>
                                </div>

                                <input id="update_data" type="submit" value="Submit" class="btn btn-raised btn-primary waves-effect">



                            </form>




                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('cxmScripts')
    @include('admin.lead.script')
@endpush
